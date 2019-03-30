<?php
namespace functions;

use flogert\model\CRUD;

class Client extends CRUD
{
	private $clientID;
	function __construct($clientID)
	{
		$this->clientID=$clientID;
	}
	function stats()
	{
		$result=[];
		$result['appliedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM appliedCases WHERE clientID={$this->clientID}"))[0]->total;
		$result['rejectedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM appliedCases WHERE clientID={$this->clientID} AND status='REJECTED'"))[0]->total;
		$result['wonCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WON' AND clientID={$this->clientID}"))[0]->total;
		$result['lostCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='LOST' AND clientID={$this->clientID}"))[0]->total;
		$result['appealedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='APPEALED' AND clientID={$this->clientID}"))[0]->total;
		$result['withdrawnCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WITHDRAWN' AND clientID={$this->clientID}"))[0]->total;
		$result['awaitingMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE status='AWAITING' AND clientID={$this->clientID}"))[0]->total;
		return json_encode($result);
	}
	function register(array $data) {
		$dbh=$this->connect();
		try{
			$dbh->beginTransaction();
			$dbh->exec("INSERT INTO client(fname, mname, lname, dob, email, address, contact, verified) VALUES('{$data['fname']}', mname, '{$data['lname']}', '{$data['dob']}', '{$data['email']}', '{$data['address']}', '{$data['contact']}', 0)");
			$dbh->exec("INSERT INTO user (username, password, type) VALUES('{$data['email']}','{$data['password']}','CLIENT')");
			$dbh->commit();
			return json_encode(["status" =>"success","message"=>"User registered successfully."]);
		}catch(Exception $e) {
			$dbh->rollBack();
			return json_encode(["status" => "failed", "message" => "Registration failed."]);
		}
	}
	function addCase(array $data)
	{
		return $this->insert("appliedCases",['caseTitle', 'caseType', 'courtName', 'advocateID', 'firstHearing', 'status', 'clientID', 'description'],[$data['caseTitle'],$data['caseType'],$data['courtName'],$data['advocateID'],$data['firstHearing'],"PENDING",$this->clientID, $data['description']],['success' => "Case application successful. Please wait while we review your application. We will notify you when we are done.",'failed' => "Application process failed. Please try again."]);
	}
	function searchAdvocate($keyword)
	{
		return $this->select("SELECT empID AS mainKey,CONCAT(fname,' ',CONCAT(lname,' ',mname)) AS key1, email AS key2, address AS key3 FROM employee WHERE CONCAT(fname,lname,CONCAT(email,address,'')) LIKE '%{$keyword}%' LIMIT 10");
	}
	function notifications()
	{
		return $this->select("SELECT clientNotification.id,cases.caseTitle, clientNotification.message, clientNotification.dateAdded, clientNotification.seen FROM clientNotification INNER JOIN cases ON clientNotification.clientID=cases.clientID WHERE clientNotification.clientID={$this->clientID} AND clientNotification.caseID=cases.caseID ORDER BY clientNotification.dateAdded DESC LIMIT 50");
	}
	function newNotifications()
	{
		return $this->select("SELECT COUNT(*) AS total FROM clientNotification WHERE clientID={$this->clientID} AND seen=0");
	}
	function markNotification($id)
	{
		return $this->modify("clientNotification",["seen"],[1],["id" => $id],["success" => "", "failed"=> ""]);
	}
	function viewProfile()
	{
		return $this->select("SELECT clientID,CONCAT(fname,' ',lname) AS name,DATE_FORMAT(dob,'%D of %M %Y') AS dob, email, address, contact FROM client WHERE clientID={$this->clientID}");
	}
	function appliedCases()
	{
		return $this->select("SELECT appliedCases.id AS id,appliedCases.caseTitle AS title,CONCAT(client.fname,' ',client.lname) AS client,client.clientID AS clientID, appliedCases.caseType AS caseType, appliedCases.courtName AS courtName,employee.empID AS advocateID,CONCAT(employee.fname,' ',employee.lname) AS advocate, DATE_FORMAT(appliedCases.firstHearing, '%D of %M, %Y') AS firstHearing, appliedCases.firstHearing AS fHearing, DATE_FORMAT(appliedCases.dateAdded,'%D of %M, %Y') AS dateApplied,appliedCases.description AS description FROM appliedCases INNER JOIN client ON appliedCases.clientID=client.clientID INNER JOIN employee ON appliedCases.advocateID=employee.empID WHERE appliedCases.clientID={$this->clientID}");
	}
	function cancelCase($caseID)
	{
		$accepted=json_decode($this->select("SELECT COUNT(*) AS total FROM cases WHERE caseID={$caseID}"))[0]->total;
		if ($accepted>0) {
			return json_encode(["status" =>"failed", "message" => "You cannot cancel the case since it has already been accepted/rejected. Please contact administrator for help"]);
		}
		return $this->modify("appliedCases",["status"],["REJECTED"],["id" => $caseID],["success" => "Case cancelled successfully.", "failed"=> "Unable to update case information."]);
	}
	function addWitness(array $data)
	{
		return $this->insert("caseWitness",["caseID","witnessName","address","contact","email"],[$data["caseID"],$data["witnessName"],$data["address"],$data["contact"],$data["email"]],["success" => "Witness added successfully.","failed" => "Unable to add witness."]);
	}
	function viewCase($id) {
		$result=[];
		$result['mainDetails']=$this->select("SELECT * FROM cases WHERE caseID={$id}");
		$result['clientDetails']=$this->select("SELECT client.clientID,CONCAT(client.fname,' ',client.lname) AS name,client.email AS email,client.contact AS contact FROM client INNER JOIN cases ON client.clientID=cases.clientID WHERE cases.caseID={$id}");
		$result['assignmentDetails']=$this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS name,employee.email AS contact FROM assignedCases INNER JOIN employee ON assignedCases.advocateID=employee.empID WHERE assignedCases.caseID={$id}");
		$result['proceedings']=$this->select("SELECT DATE_FORMAT(hearingDate,'%D of %M, %Y') AS hearingDate,proceedings FROM caseDetails WHERE caseID={$id} ORDER BY hearingDate DESC");
		$result['witnesses']=$this->select("SELECT * FROM caseWitness WHERE caseID={$id}");
		$result['meetings']=$this->select("SELECT meetingDate,venue FROM meetingsArranged WHERE caseID={$id}");
		$result['payments']=$this->select("SELECT paymentID,datePayed,transID,status, FORMAT(amount,2) AS amount FROM payment WHERE caseID={$id}");
		return json_encode($result);
	}
	function searchCase($keyword)
	{
		return $this->select("SELECT cases.caseID AS mainKey,cases.caseTitle AS key1,CONCAT(employee.fname,' ',employee.lname) AS key2, CONCAT(client.fname,' ',client.lname) AS key3 FROM cases INNER JOIN employee ON cases.advocateID=employee.empID INNER JOIN client ON cases.clientID=client.clientID WHERE cases.clientID={$this->clientID} AND CONCAT(cases.caseTitle,'',CONCAT(cases.caseType,'',CONCAT(cases.courtName,'',CONCAT(cases.advocateID,'',CONCAT(cases.outcome,'',CONCAT(employee.fname,'',CONCAT(employee.lname,'',CONCAT(client.fname,'',CONCAT(client.lname,'',''))))))))) LIKE '%{$keyword}%'");
	}
	function witnesses($caseID)
	{
		return $this->select("SELECT caseWitness.id,cases.caseTitle,caseWitness.witnessName,caseWitness.contact,caseWitness.email,caseWitness.address FROM caseWitness INNER JOIN cases ON caseWitness.caseID=cases.caseID WHERE caseWitness.caseID={$caseID}");
	}
	function deleteWitness($id)
	{
		return $this->del("caseWitness",["id" => $id],["success" => "Witness deleted successfully.", "failed" => "Unable to delete witness."]);
	}
	function finalizeCase($caseID,$outcome)
	{
		return $this->modify("cases",["outcome","status"],[$outcome,"CLOSED"],["caseID" => $caseID,"clientID" => $this->clientID],["success" => "Case conclusion was successful.", "failed"=> "Unable to conclude case."]);
	}
	function myActiveCases()
	{
		return $this->select("SELECT cases.caseID AS caseID,cases.caseTitle AS caseTitle,cases.caseType,cases.courtName,CONCAT(client.fname,' ',client.lname) AS client,DATE_FORMAT(cases.lastHearing,'%D of %M,%Y') AS lastHearing,client.clientID AS clientID,cases.dateAccepted AS dateAccepted FROM cases INNER JOIN client ON cases.clientID=client.clientID WHERE cases.clientID={$this->clientID} AND status='IN PROGRESS'");
	}
	function withdrawCase($caseID)
	{
		return $this->modify("cases",["outcome","status"],["WITHDRAWN","CLOSED"],["caseID" => $caseID],["success" => "Case conclusion was successful.", "failed"=> "Unable to conclude case."]);
	}
	function proceedings($caseID)
	{
		return $this->select("SELECT DATE_FORMAT(hearingDate,'%D of %M, %Y') AS hearingDay,proceedings FROM caseDetails WHERE caseID={$caseID} ORDER BY hearingDate DESC");
	}
	function viewCosts()
	{
		return $this->select("SELECT cases.caseID,client.clientID,cases.caseTitle,cases.caseType,cases.courtName,CONCAT(client.fname,' ',client.lname) AS client,DATE_FORMAT(cases.lastHearing,'%D of %M,%Y') AS lastHearing,DATE_FORMAT(cases.nextHearing,'%D of %M,%Y') AS nextHearing,cases.status AS status,cases.outcome AS outcome,cases.dateAccepted AS dateAccepted,FORMAT(caseCost.cost,2) AS cost,FORMAT(SUM(payment.amount),2) AS paid,FORMAT((caseCost.cost-SUM(payment.amount)),2) AS balance FROM cases INNER JOIN client ON cases.clientID=client.clientID INNER JOIN caseCost ON cases.caseID=caseCost.caseID INNER JOIN payment ON caseCost.caseID=payment.caseID WHERE cases.clientID={$this->clientID} GROUP BY cases.caseID,client.fname,client.lname");
	}
	function pay(array $data)
	{
		return $this->insert("payment",["caseID","transID","status","amount"],[$data["caseID"],$data["transID"],"PENDING",$data["amount"]],["success" => "Payment successful.","failed" => "Unable to receive payment."]);
	}
	function casePayments($caseID)
	{
		return $this->select("SELECT paymentID,caseID,datePayed,transID,status,FORMAT(amount,2) AS amount FROM payment WHERE caseID={$caseID}");
	}
	function meetings()
	{
		return $this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS advocate,CONCAT(client.fname,' ',client.lname) AS client, cases.caseTitle AS caseTitle,meetingsArranged.venue AS venue,meetingsArranged.status AS status,meetingsArranged.meetingDate AS meetingDate FROM employee INNER JOIN meetingsArranged ON employee.empID=meetingsArranged.advocateID INNER JOIN client ON client.clientID=meetingsArranged.clientID INNER JOIN cases ON meetingsArranged.caseID=cases.caseID WHERE cases.clientID={$this->clientID} ORDER BY meetingsArranged.meetingDate DESC");
	}
}