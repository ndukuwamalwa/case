<?php
namespace functions;

use flogert\model\CRUD;
use flogert\helpers\Security;

class Admin extends CRUD
{
	function stats()
	{
		$result=[];
		$result['users']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM user"))[0]->total;
		$result['appliedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM appliedCases"))[0]->total;
		$result['acceptedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases"))[0]->total;
		$result['wonCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WON'"))[0]->total;
		$result['lostCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='LOST'"))[0]->total;
		$result['appealedCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='APPEALED'"))[0]->total;
		$result['withdrawnCases']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM cases WHERE outcome='WITHDRAWN'"))[0]->total;
		$result['clients']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM client"))[0]->total;
		$result['verifiedClients']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM client WHERE verified=1"))[0]->total;
		$result['employees']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM employee"))[0]->total;
		$result['advocates']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM employee WHERE empType='ADVOCATE'"))[0]->total;
		$result['meetingsArranged']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged"))[0]->total;
		$result['doneMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE status='DONE'"))[0]->total;
		$result['awaitingMeetings']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM meetingsArranged WHERE status='AWAITING'"))[0]->total;
		$result['payments']=json_decode($this->select("SELECT FORMAT(COUNT(*),0) AS total FROM payment"))[0]->total;
		$result['paidAmount']=json_decode($this->select("SELECT FORMAT(SUM(amount),2) AS total FROM payment"))[0]->total;
		return json_encode($result);
	}
	function updateProfile(array $data)
	{
		$count=json_decode($this->select("SELECT COUNT(*) AS total FROM admin"))[0]->total;
		if ($count==0) {
			return $this->insert("admin", ["fname","mname","lname","email","address","dob","photo","mime"], [$data["fname"],$data["mname"],$data["lname"],$data["email"],$data["address"],$data["dob"],$data["photo"], $data['mime']],["success"=>"Profile created successfully", "failed"=>"Unable to create profile. Please try again."]);
		}else{
			return $this->modify("admin",["fname","mname","lname","email","address","dob","photo","mime"],[$data["fname"],$data["mname"],$data["lname"],$data["email"],$data["address"],$data["dob"],$data["photo"],$data['mime']],["advocateID" => 1],["success"=>"Profile updated successfully.", "failed"=>"Unable to update profile. Please try again later."]);
		}
	}
	function getPhoto()
	{
		$file=$this->getFile("SELECT photo FROM admin LIMIT 1");
		print_r($file['photo']);
	}
	function viewProfile()
	{
		return $this->select("SELECT fname,mname,lname,email,address,DATE_FORMAT(dob,'%D of %M, %Y') AS dob,dob AS dobRaw,mime FROM admin LIMIT 1");
	}
	function addContact($contact)
	{
		return $this->insert("adminContact",["advocateID","contact"],[1000000,$contact],["success" => "Contact added successfully.","failed" => "Unable to add contact."]);
	}
	function deleteContact($id)
	{
		return $this->del("adminContact",["contact" => $id],["success" => "Contact deleted successfully.", "failed" => "Unable to delete contact."]);
	}
	function viewContact()
	{
		return $this->select("SELECT contact FROM adminContact");
	}
	function addEducation(array $data)
	{
		return $this->insert("adminEducation", ["advocateID","specialization","institution","achievement","year","description"], [1000000,$data["specialization"],$data["institution"],$data["achievement"],$data["year"],$data["description"]],["success"=>"Academic achievement added successfully.", "failed"=>"Unable to add details. Please try again."]);
	}
	function viewEducation()
	{
		return $this->select("SELECT * FROM adminEducation");
	}
	function addEmployee(array $data)
	{
		$new=$this->insert("employee",["fname","mname","lname","email","address","empType","salary","photo","mime"],[$data["fname"],$data["mname"],$data["lname"],$data["email"],$data["address"],$data["empType"],$data["salary"],$data["photo"],$data["mime"]],["success" =>"Employee added successfully.", "failed" => "Unable to add employee. Please try again."]);
		if (json_decode($new)->status=='success' && $data['empType']=='ADVOCATE') {
			$password=Security::hash("12345678");
			$user=$this->insert("user",['username','password','type'],[$data['email'],$password,'ADVOCATE'],['success' => 'User added.', 'failed' => 'Failed to add user.']);
			if (json_decode($user)->status=='success') {
				return json_encode(["status" =>"success", "message" => "Employee added successfully."]);
			}else{
				return json_encode(['status' => 'failed', 'message' => 'Unable to add employee.']);
			}
		}elseif ($data['empType']!=='ADVOCATE') {
			return json_encode(["status" =>"success", "message" => "Employee added successfully."]);
		}else{
			return json_encode(['status' => 'failed', 'message' => 'Unable to add employee.']);
		}
	}
	function searchEmployee($keyword)
	{
		return $this->select("SELECT empID AS mainKey,CONCAT(fname,' ',CONCAT(lname,' ',mname)) AS key1, email AS key2, address AS key3,CONCAT('KES. ',' ',salary) AS key4  FROM employee WHERE empID LIKE '%{$keyword}%' OR fname LIKE '%{$keyword}%' OR lname LIKE '%{$keyword}%' OR email LIKE '%{$keyword}%' OR address LIKE '%{$keyword}%' OR empType LIKE '%{keyword}%' OR CONCAT(fname,' ',lname) LIKE '{$keyword}' LIMIT 5");
	}
	function viewEmployee($id)
	{
		return $this->select("SELECT empID,fname,mname,lname,email,address,empType,CONCAT('KES.',' ',FORMAT(salary,2)) AS salary FROM employee WHERE empID={$id}");
	}
	function deleteEmployee($id)
	{
		return $this->del("employee",["empID" => $id],['success' => "Employee deleted successfully.", "failed" => "Unable to delete employee. This may be due to data integrity concerns."]);
	}
	function updateEmployee(array $data)
	{
		return $this->modify("employee",["fname","mname","lname","email","address","empType","salary"],[$data["fname"],$data["mname"],$data["lname"],$data["email"],$data["address"],$data["empType"],$data["salary"]],["empID" => $data['empID']],["success" => "Updated employee details successfully.", "failed"=> "Unable to update employee details."]);
	}
	function addEmployeeEdu(array $data)
	{
		return $this->insert("employeeEducation", ["empID","specialization","institution","achievement","year","description"], [$data['empID'],$data["specialization"],$data["institution"],$data["achievement"],$data["year"],$data["description"]],["success"=>"Academic achievement added successfully.", "failed"=>"Unable to add details. Please try again."]);
	}
	function viewEmployeeEdu($id)
	{
		return $this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS name,employeeEducation.empID AS empID, employeeEducation.specialization AS specialization, employeeEducation.institution AS institution, employeeEducation.achievement AS achievement, employeeEducation.year AS year, employeeEducation.description AS description FROM employee INNER JOIN employeeEducation ON employee.empID=employeeEducation.empID  WHERE employee.empID={$id}");
	}
	function unVerifiedClients()
	{
		return $this->select("SELECT clientID, CONCAT(fname,' ',lname) AS name, DATE_FORMAT(dob,'%D of %M, %Y') AS dob,contact,email FROM client WHERE verified=0");
	}
	function verifyClient($id)
	{
		return $this->modify("client",["verified"],[1],["clientID" => $id],["success" => "Client verified successfully..", "failed"=> "Unable to verify client. A technical detail occured."]);
	}
	function clients()
	{
		return $this->select("SELECT clientID, CONCAT(fname,' ',lname) AS name, DATE_FORMAT(dob,'%D of %M, %Y') AS dob,contact,email, address, verified FROM client");
	}
	function deleteClient($id)
	{
		return $this->del("client",["clientID" => $id],['success' => "Client deleted successfully.", "failed" => "Unable to delete client. This may be due to data integrity concerns."]);
	}
	function appliedCases()
	{
		return $this->select("SELECT appliedCases.id AS id,appliedCases.caseTitle AS title,CONCAT(client.fname,' ',client.lname) AS client,client.clientID AS clientID, appliedCases.caseType AS caseType, appliedCases.courtName AS courtName,employee.empID AS advocateID,CONCAT(employee.fname,' ',employee.lname) AS advocate, DATE_FORMAT(appliedCases.firstHearing, '%D of %M, %Y') AS firstHearing, appliedCases.firstHearing AS fHearing, DATE_FORMAT(appliedCases.dateAdded,'%D of %M, %Y') AS dateApplied,appliedCases.description AS description FROM appliedCases INNER JOIN client ON appliedCases.clientID=client.clientID INNER JOIN employee ON appliedCases.advocateID=employee.empID WHERE appliedCases.status='PENDING'");
	}
	function viewCase($id)
	{
		$result=[];
		$result['mainDetails']=$this->select("SELECT * FROM cases WHERE caseID={$id}");
		$result['clientDetails']=$this->select("SELECT CONCAT(client.fname,' ',client.lname) AS name,client.email AS email,client.contact AS contact FROM client INNER JOIN cases ON client.clientID=cases.clientID WHERE cases.caseID={$id}");
		$result['assignmentDetails']=$this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS name,employee.email AS contact FROM assignedCases INNER JOIN employee ON assignedCases.advocateID=employee.empID WHERE assignedCases.caseID={$id}");
		$result['proceedings']=$this->select("SELECT DATE_FORMAT(hearingDate,'%D of %M, %Y') AS hearingDate,proceedings FROM caseDetails WHERE caseID={$id} ORDER BY hearingDate DESC");
		$result['witnesses']=$this->select("SELECT * FROM caseWitness WHERE caseID={$id}");
		$result['meetings']=$this->select("SELECT meetingDate,venue FROM meetingsArranged WHERE caseID={$id}");
		$result['payments']=$this->select("SELECT paymentID,datePayed,transID,status, FORMAT(amount,2) AS amount FROM payment WHERE caseID={$id}");
		return json_encode($result);
	}
	function viewWitnesses($caseID)
	{
		return $this->select("SELECT * FROM caseWitness WHERE caseID={$caseID}");
	}
	function rejectCase($id)
	{
		return $this->modify("appliedCases",["status"],["REJECTED"],["id" => $id],["success" => "Case has been rejected successfully.", "failed"=> "Failed to reject case. Please try again later."]);
	}
	function acceptCase(array $data)
	{
		$dbh=$this->connect();
		try{
			$dbh->beginTransaction();
			$dbh->exec("UPDATE appliedCases SET status='ACCEPTED' WHERE id={$data['caseID']}");
			$dbh->exec("INSERT INTO cases VALUES({$data['caseID']},'{$data['caseTitle']}','{$data['caseType']}','{$data['courtName']}',{$data['advocateID']},{$data['clientID']},'{$data['lastHearing']}','{$data['nextHearing']}','{$data['status']}','{$data['outcome']}',null)");
			$dbh->exec("INSERT INTO assignedCases VALUES ({$data['caseID']},{$data['advocateID']})");
			$dbh->commit();
			return json_encode(["status" =>"success","message"=>"Case acceptance successful. The case has been assigned to the suggested advocate."]);
		}catch(Exception $e) {
			$dbh->rollBack();
			return json_encode(["status" => "failed", "message" => "Case acceptance process failed. Please try again."]);
		}
	}
	function searchCase($keyword)
	{
		return $this->select("SELECT cases.caseID AS mainKey,cases.caseTitle AS key1,CONCAT(employee.fname,' ',employee.lname) AS key2, CONCAT(client.fname,' ',client.lname) AS key3 FROM cases INNER JOIN employee ON cases.advocateID=employee.empID INNER JOIN client ON cases.clientID=client.clientID WHERE cases.caseTitle LIKE '%{keyword}%' OR cases.caseType LIKE '%{$keyword}%' OR cases.courtName LIKE '%{$keyword}%' OR cases.advocateID LIKE '%{$keyword}%' OR cases.outcome LIKE '%{$keyword}%' OR employee.fname LIKE '{$keyword}' OR employee.lname LIKE '%{$keyword}%' OR client.fname LIKE '%{$keyword}%' OR client.lname LIKE '%{$keyword}%' LIMIT 8");
	}
	function viewPayments()
	{
		return $this->select("SELECT paymentID,caseID,datePayed,transID,status,FORMAT(amount,2) AS amount FROM payment");
	}
	function viewPayment($id)
	{
		return $this->select("SELECT paymentID,caseID,datePayed,transID,status,FORMAT(amount,2) AS amount FROM payment WHERE paymentID={$id}");
	}
	function searchPayments($keyword)
	{
		return $this->select("SELECT paymentID AS mainKey,datePayed AS key1,transID AS key2,status AS key3, CONCAT('KES.',' ',FORMAT(amount,2)) AS key4 FROM payment WHERE paymentID LIKE '%{$keyword}%' OR caseID LIKE '%{$keyword}%' OR transID LIKE '%{$keyword}%' OR status LIKE '%{$keyword}%' OR amount LIKE '%{$keyword}%' LIMIT 20");
	}
	function unconfirmedPayments()
	{
		return $this->select("SELECT paymentID,caseID,datePayed,transID,status,FORMAT(amount,2) AS amount FROM payment WHERE status='PENDING'");
	}
	function confirmPayment($id)
	{
		return $this->modify("payment",["status"],["CONFIRMED"],["paymentID" => $id],["success" => "Payment confirmed successfully.", "failed"=> "Unable to confirm payment."]);
	}
	function viewEmployees()
	{
		$year=date('Y');
		$month=date('m');
		return $this->select("SELECT empID, CONCAT(fname,' ',CONCAT(lname,' ',mname)) AS name,email,empType,FORMAT(salary,2) AS salary FROM employee WHERE empID NOT IN(SELECT empID FROM payedSalary WHERE year>={$year} AND month>={$month})");
	}
	function paySalary($id)
	{
		$amount=json_decode($this->select("SELECT salary FROM employee WHERE empID={$id}"))[0]->salary;
		$year=date('Y');
		$month=date('m');
		return $this->insert("payedSalary",["empID","year","month","salary"],[$id,$year,$month,$amount],["success" => "Salary payment successful. Employee has been added to the current month payroll.","failed" => "Could not complete request. Try again later."]);
	}
	function paymentHistory()
	{
		$year=date('Y');
		return $this->select("SELECT payedSalary.empID AS empID,CONCAT(employee.fname,' ',employee.lname) AS name,payedSalary.year AS year, DATE_FORMAT(CONCAT(payedSalary.year,'-',CONCAT(payedSalary.month,'-','01')),'%M') AS month,DATE_FORMAT(payedSalary.datePayed,'%D of %M, %Y') AS datePayed,FORMAT(payedSalary.salary,2) AS salary FROM payedSalary INNER JOIN employee ON payedSalary.empID=employee.empID WHERE payedSalary.year={$year}");
	}
	function arrangedMeetings()
	{
		return $this->select("SELECT CONCAT(employee.fname,' ',employee.lname) AS advocate,CONCAT(client.fname,' ',client.lname) AS client, cases.caseTitle AS caseTitle,meetingsArranged.venue AS venue,meetingsArranged.status AS status,meetingsArranged.meetingDate AS meetingDate FROM employee INNER JOIN meetingsArranged ON employee.empID=meetingsArranged.advocateID INNER JOIN client ON client.clientID=meetingsArranged.clientID INNER JOIN cases ON meetingsArranged.caseID=cases.caseID ORDER BY meetingsArranged.meetingDate DESC");
	}
	function cancellableMeetings()
	{
		return $this->select("SELECT meetingsArranged.id AS id,CONCAT(employee.fname,' ',employee.lname) AS advocate,CONCAT(client.fname,' ',client.lname) AS client, cases.caseTitle AS caseTitle,meetingsArranged.venue AS venue,meetingsArranged.status AS status,meetingsArranged.meetingDate AS meetingDate,meetingsArranged.advocateID AS advocateID,meetingsArranged.clientID AS clientID, meetingsArranged.caseID AS caseID FROM employee INNER JOIN meetingsArranged ON employee.empID=meetingsArranged.advocateID INNER JOIN client ON client.clientID=meetingsArranged.clientID INNER JOIN cases ON meetingsArranged.caseID=cases.caseID WHERE meetingsArranged.status<>'CANCELLED' AND meetingsArranged.status<>'DONE' ORDER BY meetingsArranged.meetingDate DESC");
	}
	function cancelArrangement($id,$advocateID,$clientID,$caseID)
	{
		return $this->modify("meetingsArranged",["status"],["CANCELLED"],["advocateID" => $advocateID,"clientID" => $clientID, "caseID" => $caseID, 'id' => $id],["success" => "Meeting cancelled successfully.", "failed"=> "Unable to cancel meeting. Try again."]);
	}
}