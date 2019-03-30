<?php
namespace functions;

use flogert\model\CRUD;

class Advocate extends CRUD
{
	private $advocateID;
	function __construct($advocateID)
	{
		$this->advocateID=$advocateID;
	}
	function systemStats()
	{
		$stats=[];
		$stats['caseApplications']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM appliedCases"))[0]->total;
		$stats['casesHandled']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases"))[0]->total;
		$stats['casesWon']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WON'"))[0]->total;
		$stats['casesWithdrawn']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WITHDRAWN'"))[0]->total;
		$done=json_decode($this->select("SELECT COUNT(*) AS total FROM cases WHERE status='CLOSED'"))[0]->total;
		$won=(int) str_replace(",", '', $stats['casesWon']);
		$rate=($won/$done)*100;
		$stats['winningRate']=number_format($rate)."%";
		$stats['advocates']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM employee WHERE empType='ADVOCATE'"))[0]->total;
		$stats['clients']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM client"))[0]->total;
		$stats['caseTypes']=json_decode($this->select("SELECT cases.caseType,FORMAT(COUNT(cases.caseType),0) AS total,FORMAT(AVG(caseCost.cost),2) AS average FROM cases INNER JOIN caseCost ON cases.caseID=caseCost.caseID GROUP BY caseType"));
		return json_encode($stats);
	}
	function stats()
	{
		$result=[];
		$result['handledCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM assignedCases WHERE advocateID={$this->advocateID}"))[0]->total;
		$result['wonCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WON' AND advocateID={$this->advocateID}"))[0]->total;
		$result['lostCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='LOST' AND advocateID={$this->advocateID}"))[0]->total;
		$result['appealedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='APPEALED' AND advocateID={$this->advocateID}"))[0]->total;
		$result['withdrawnCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WITHDRAWN' AND advocateID={$this->advocateID}"))[0]->total;
		$result['arrangedMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE advocateID={$this->advocateID}"))[0]->total;
		$result['cancelledMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE status='CANCELLED' AND advocateID={$this->advocateID}"))[0]->total;
		$result['awaitingMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE status='AWAITING' AND advocateID={$this->advocateID}"))[0]->total;
		$result['doneMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE status='DONE' AND advocateID={$this->advocateID}"))[0]->total;
		return json_encode($result);
	}
	function viewProfile()
	{
		return $this->select("SELECT empID,CONCAT(fname,' ',CONCAT(lname,' ',mname)) AS name,email,address,empType,FORMAT(salary,2) AS salary FROM employee WHERE empID={$this->advocateID}");
	}
	function addContact($contact)
	{
		return $this->insert("employeeContact",["empID","contact"],[$this->advocateID,$contact],["success" => "Contact added successfully.","failed" => "Unable to add contact."]);
	}
	function viewContact()
	{
		return $this->select("SELECT contact FROM employeeContact WHERE empID={$this->advocateID}");
	}
	function deleteContact($id)
	{
		return $this->del("employeeContact",["contact" => $id],["success" => "Contact deleted successfully.", "failed" => "Unable to delete contact."]);
	}
	function addEducation(array $data)
	{
		return $this->insert("employeeEducation", ["empID","specialization","institution","achievement","year","description"], [$this->advocateID,$data["specialization"],$data["institution"],$data["achievement"],$data["year"],$data["description"]],["success"=>"Academic achievement added successfully.", "failed"=>"Unable to add details. Please try again."]);
	}
	function viewEducation()
	{
		return $this->select("SELECT * FROM employeeEducation WHERE empID={$this->advocateID}");
	}
	function appliedCases()
	{
		return $this->select("SELECT appliedCases.id AS id,appliedCases.caseTitle AS title,CONCAT(client.fname,' ',client.lname) AS client,client.clientID AS clientID, appliedCases.caseType AS caseType, appliedCases.courtName AS courtName,employee.empID AS advocateID,CONCAT(employee.fname,' ',employee.lname) AS advocate, DATE_FORMAT(appliedCases.firstHearing, '%D of %M, %Y') AS firstHearing, appliedCases.firstHearing AS fHearing, DATE_FORMAT(appliedCases.dateAdded,'%D of %M, %Y') AS dateApplied,appliedCases.description AS description FROM appliedCases INNER JOIN client ON appliedCases.clientID=client.clientID INNER JOIN employee ON appliedCases.advocateID=employee.empID WHERE appliedCases.status='PENDING' AND appliedCases.advocateID={$this->advocateID}");
	}
	function addProceeding(array $data)
	{
		return $this->insert("caseDetails",["caseID","hearingDate","proceedings"],[$data["caseID"],$data["hearingDate"],$data["proceedings"]],['success' => "Proceedings added successfully.","failed" => "Operation failed"]);
	}
	function searchCase($keyword)
	{
		return $this->select("SELECT cases.caseID AS mainKey,cases.caseTitle AS key1,CONCAT(employee.fname,' ',employee.lname) AS key2, CONCAT(client.fname,' ',client.lname) AS key3 FROM cases INNER JOIN employee ON cases.advocateID=employee.empID INNER JOIN client ON cases.clientID=client.clientID WHERE cases.advocateID={$this->advocateID} AND employee.empID={$this->advocateID} AND CONCAT(cases.caseTitle,'',CONCAT(cases.caseType,'',CONCAT(cases.courtName,'',CONCAT(cases.advocateID,'',CONCAT(cases.outcome,'',CONCAT(employee.fname,'',CONCAT(employee.lname,'',CONCAT(client.fname,'',CONCAT(client.lname,'',''))))))))) LIKE '%{$keyword}%'");
	}
	function viewCase($id)
	{
		$result=[];
		$result['mainDetails']=$this->select("SELECT * FROM cases WHERE caseID={$id}");
		$result['clientDetails']=$this->select("SELECT client.clientID,CONCAT(client.fname,' ',client.lname) AS name,client.email AS email,client.contact AS contact FROM client INNER JOIN cases ON client.clientID=cases.clientID WHERE cases.caseID={$id}");
		$result['assignmentDetails']=$this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS name,employee.email AS contact FROM assignedCases INNER JOIN employee ON assignedCases.advocateID=employee.empID WHERE assignedCases.caseID={$id}");
		$result['proceedings']=$this->select("SELECT DATE_FORMAT(hearingDate,'%D of %M, %Y') AS hDate,proceedings FROM caseDetails WHERE caseID={$id} ORDER BY hearingDate DESC");
		$result['witnesses']=$this->select("SELECT * FROM caseWitness WHERE caseID={$id}");
		$result['meetings']=$this->select("SELECT meetingDate,venue FROM meetingsArranged WHERE caseID={$id}");
		$result['payments']=$this->select("SELECT paymentID,datePayed,transID,status, FORMAT(amount,2) AS amount FROM payment WHERE caseID={$id}");
		return json_encode($result);
	}
	function viewWitnesses($caseID)
	{
		return $this->select("SELECT * FROM caseWitness WHERE caseID={$caseID}");
	}
	function myCases()
	{
		return $this->select("SELECT cases.caseID,cases.caseTitle,cases.caseType,cases.courtName,CONCAT(client.fname,' ',client.lname) AS client,DATE_FORMAT(cases.lastHearing,'%D of %M,%Y') AS lastHearing,DATE_FORMAT(cases.nextHearing,'%D of %M,%Y') AS nextHearing,cases.status AS status,cases.outcome AS outcome,cases.dateAccepted AS dateAccepted FROM cases INNER JOIN client ON cases.clientID=client.clientID WHERE cases.advocateID={$this->advocateID}");
	}
	function requestPayCases()
	{
		return $this->select("SELECT cases.caseID,client.clientID,cases.caseTitle,cases.caseType,cases.courtName,CONCAT(client.fname,' ',client.lname) AS client,DATE_FORMAT(cases.lastHearing,'%D of %M,%Y') AS lastHearing,DATE_FORMAT(cases.nextHearing,'%D of %M,%Y') AS nextHearing,cases.status AS status,cases.outcome AS outcome,cases.dateAccepted AS dateAccepted,FORMAT(caseCost.cost,2) AS cost,FORMAT(SUM(payment.amount),2) AS paid,FORMAT((caseCost.cost-SUM(payment.amount)),2) AS balance FROM cases INNER JOIN client ON cases.clientID=client.clientID INNER JOIN caseCost ON cases.caseID=caseCost.caseID INNER JOIN payment ON caseCost.caseID=payment.caseID WHERE cases.advocateID={$this->advocateID} GROUP BY cases.caseID,client.fname,client.lname");
	}
	function myActiveCases()
	{
		return $this->select("SELECT cases.caseID AS caseID,cases.caseTitle AS caseTitle,cases.caseType,cases.courtName,CONCAT(client.fname,' ',client.lname) AS client,DATE_FORMAT(cases.lastHearing,'%D of %M,%Y') AS lastHearing,client.clientID AS clientID,cases.dateAccepted AS dateAccepted FROM cases INNER JOIN client ON cases.clientID=client.clientID WHERE cases.advocateID={$this->advocateID} AND status='IN PROGRESS'");
	}
	function finalizeCase($caseID,$clientID,$outcome)
	{
		return $this->modify("cases",["outcome","status"],[$outcome,"CLOSED"],["caseID" => $caseID,"clientID" => $clientID, "advocateID" => $this->advocateID],["success" => "Case conclusion was successful.", "failed"=> "Unable to conclude case."]);
	}
	function setCost($caseID,$cost)
	{
		$check=json_decode($this->select("SELECT COUNT(*) AS total FROM caseCost WHERE caseID={$caseID}"))[0]->total;
		if ($check==0) {
			return $this->insert("caseCost",["caseID","cost"],[$caseID,$cost],["success" => "Case cost set successfully.","failed" => "Unable to set cost. Check to make sure that the administrator has accepted the case first."]);
		}else{
			return $this->modify("caseCost",["cost"],[$cost],["caseID" => $caseID],["success" => "Case cost updated successfully.", "failed"=> "Unable to update case cost. Check to make sure that the administrator has accepted the case first."]);
		}
	}
	function viewCasePayment($caseID)
	{
		return $this->select("SELECT paymentID,datePayed,transID,status, FORMAT(amount,2) AS amount FROM payment WHERE caseID={$caseID}");
	}
	function requestPayment($caseID,$clientID)
	{
		$clientName=json_decode($this->select("SELECT CONCAT(fname,' ',lname) AS name FROM client WHERE clientID={$clientID}"))[0]->name;
		$balance=json_decode($this->select("SELECT FORMAT((caseCost.cost-SUM(payment.amount)),2) AS balance FROM caseCost INNER JOIN payment ON caseCost.caseID=payment.caseID WHERE caseCost.caseID={$caseID} AND payment.caseID={$caseID} AND caseCost.caseID IN (SELECT caseID FROM cases WHERE clientID={$clientID})"))[0]->balance;
		$message="Dear {$clientName}, you are hereby reminded to pay your representation costs balance of KES. {$balance} by your attorney.";
		return $this->insert("clientNotification",["caseID","clientID","message"],[$caseID,$clientID,$message],["success" => "Request send successfully.","failed" => "Unable to send request."]);
	}
	function casePayments($caseID)
	{
		return $this->select("SELECT paymentID,caseID,datePayed,transID,status,FORMAT(amount,2) AS amount FROM payment WHERE caseID={$caseID}");
	}
	function arrangeMeeting(array $data)
	{
		return $this->insert("meetingsArranged",["advocateID","clientID","caseID","meetingDate","venue","status"],[$this->advocateID,$data['clientID'],$data['caseID'],$data['meetingDate'],$data['venue'],"AWAITING"],["success" => "Meeting arrangement was successful.","failed" => "Unable to arrange meeting."]);
	}
	function arrangedMeetings()
	{
		return $this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS advocate,CONCAT(client.fname,' ',client.lname) AS client, cases.caseTitle AS caseTitle,meetingsArranged.venue AS venue,meetingsArranged.status AS status,meetingsArranged.meetingDate AS meetingDate FROM employee INNER JOIN meetingsArranged ON employee.empID=meetingsArranged.advocateID INNER JOIN client ON client.clientID=meetingsArranged.clientID INNER JOIN cases ON meetingsArranged.caseID=cases.caseID WHERE cases.advocateID={$this->advocateID} ORDER BY meetingsArranged.meetingDate DESC");
	}
	function awaitingMeetings()
	{
		return $this->select("SELECT meetingsArranged.id AS id,CONCAT(employee.fname,' ',employee.lname) AS advocate,CONCAT(client.fname,' ',client.lname) AS client, cases.caseTitle AS caseTitle,meetingsArranged.venue AS venue,meetingsArranged.status AS status,meetingsArranged.meetingDate AS meetingDate,meetingsArranged.advocateID AS advocateID,meetingsArranged.clientID AS clientID, meetingsArranged.caseID AS caseID FROM employee INNER JOIN meetingsArranged ON employee.empID=meetingsArranged.advocateID INNER JOIN client ON client.clientID=meetingsArranged.clientID INNER JOIN cases ON meetingsArranged.caseID=cases.caseID WHERE meetingsArranged.status='AWAITING' AND meetingsArranged.advocateID={$this->advocateID} ORDER BY meetingsArranged.meetingDate DESC");
	}
	function markDone($id,$clientID,$caseID)
	{
		return $this->modify("meetingsArranged",["status"],["DONE"],["advocateID" => $this->advocateID,"clientID" => $clientID, "caseID" => $caseID, 'id' => $id],["success" => "Meeting state changed successfully.", "failed"=> "Unable to modify meeting. Try again."]);
	}
	function mySalaries()
	{
		return $this->select("SELECT payedSalary.empID AS empID,CONCAT(employee.fname,' ',employee.lname) AS name,payedSalary.year AS year, DATE_FORMAT(CONCAT(payedSalary.year,'-',CONCAT(payedSalary.month,'-','01')),'%M') AS month,DATE_FORMAT(payedSalary.datePayed,'%D of %M, %Y') AS datePayed,FORMAT(payedSalary.salary,2) AS salary FROM payedSalary INNER JOIN employee ON payedSalary.empID=employee.empID WHERE payedSalary.empID={$this->advocateID}");
	}
}