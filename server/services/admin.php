<?php
$loader=require("../vendor/autoload.php");
$loader->addPsr4("flogert\\helpers\\","../Framework/Helpers");
$loader->addPsr4("flogert\\model\\","../Framework/Model");
$loader->addPsr4("flogert\\utils\\","../Framework/Utils");
$loader->addPsr4("functions\\","../functions");
use flogert\utils\Session;
use flogert\helpers\Security;
use functions\Admin;

$Admin = new Admin();
$Session=new Session();
if ($Session->get('username')==null || $Session->get('password')==null) {
	echo json_encode(['status' => 'login','message' => 'Please login first']);
	exit();
}
if ($_POST['token']!=$Session->get('token')) {
	echo json_encode(['status' => 'login','message' => 'Unauthorized.']);
	exit();
}
if ($Session->get('type')!='ADMIN') {
	echo json_encode(['status'=> 'login', 'message' => 'Unauthorized.']);
	exit();
}
if (!isset($_POST['service'])) {
	echo json_encode(["status" => "failed", "message" => "Unknown service request. Please contact administrator over this problem."]);
	exit();
}
$service=$_POST['service'];
if ($service=="updateProfile") {
	$file=file_get_contents($_FILES['photo']['tmp_name']);
	$type=explode("/",$_FILES['photo']['type']);
	$mime=".".end($type);
	echo $Admin->updateProfile(["fname" => $_POST['fname'],"mname" => $_POST['mname'],"lname" => $_POST['lname'],"email" => $_POST['email'],"address" => $_POST['address'],"dob" => $_POST['dob'],"photo" => $file,"mime" => $mime]);
}
if ($service=="stats") {
	echo $Admin->stats();
}
if ($service=="viewProfile") {
	echo $Admin->viewProfile();
}
if ($service=="getPhoto") {
	echo $Admin->getPhoto();
}
if ($service=="addContact") {
	echo $Admin->addContact($_POST['contact']);
}
if ($service=="viewContacts") {
	echo $Admin->viewContact();
}
if ($service=="deleteContact") {
	echo $Admin->deleteContact($_POST['contact']);
}
if ($service=="addEducation") {
	echo $Admin->addEducation(["specialization" => $_POST["specialization"],"institution" => $_POST["institution"],"achievement" => $_POST["achievement"],"year" => $_POST["year"],"description" => $_POST["description"]]);
}
if ($service=="viewEducation") {
	echo $Admin->viewEducation();
}
if ($service=="addEmployee") {
	$file=file_get_contents($_FILES['photo']['tmp_name']);
	$type=explode("/",$_FILES['photo']['type']);
	$mime=".".end($type);
	echo $Admin->addEmployee(["fname" => $_POST['fname'],"mname" => $_POST['mname'],"lname" => $_POST['lname'],"email" => $_POST['email'],"address" => $_POST['address'],"empType" => $_POST['empType'],"salary" => $_POST['salary'],"photo" => $file,"mime" => $mime]);
}
if ($service=="searchEmployee") {
	echo $Admin->searchEmployee($_POST['keyword']);
}
if ($service=="viewEmployee") {
	echo $Admin->viewEmployee($_POST['id']);
}
if ($service=="deleteEmployee") {
	echo $Admin->deleteEmployee($_POST['id']);
}
if ($service=="updateEmployee") {
	echo $Admin->updateEmployee(["empID" => $_POST["empID"],"fname" => $_POST['fname'],"mname" => $_POST['mname'],"lname" => $_POST['lname'],"email" => $_POST['email'],"address" => $_POST['address'],"empType" => $_POST['empType'],"salary" => $_POST['salary']]);
}
if ($service=="addEmployeeEdu") {
	echo $Admin->addEmployeeEdu(["empID" => $_POST['empID'],"specialization" => $_POST["specialization"],"institution" => $_POST["institution"],"achievement" => $_POST["achievement"],"year" => $_POST["year"],"description" => $_POST["description"]]);
}
if ($service=="viewEmployeeEdu") {
	echo $Admin->viewEmployeeEdu($_POST['id']);
}
if ($service=="unVerifiedClients") {
	echo $Admin->unVerifiedClients();
}
if ($service=="verifyClient") {
	echo $Admin->verifyClient($_POST['id']);
}
if ($service=="clients") {
	echo $Admin->clients();
}
if ($service=="deleteClient") {
	echo $Admin->deleteClient($_POST['id']);
}
if ($service=="appliedCases") {
	echo $Admin->appliedCases();
}
if ($service=="rejectCase") {
	echo $Admin->rejectCase($_POST['id']);
}
if ($service=="acceptCase") {
	echo $Admin->acceptCase(["caseID" =>$_POST['caseID'],"caseTitle" =>$_POST['caseTitle'],"caseType" =>$_POST['caseType'],"courtName" =>$_POST["courtName"],"advocateID" =>$_POST['advocateID'],"clientID" =>$_POST['clientID'],"lastHearing" =>$_POST['lastHearing'],"nextHearing" =>$_POST['nextHearing'],"status" =>$_POST['status'],"outcome" =>$_POST['outcome']]);
}
if ($service=="searchCase") {
	echo $Admin->searchCase($_POST['keyword']);
}
if ($service=="viewCase") {
	echo $Admin->viewCase($_POST['id']);
}
if ($service=="viewWitnesses") {
	echo $Admin->viewWitnesses($_POST['id']);
}
if ($service=="viewPayments") {
	echo $Admin->viewPayments();
}
if ($service=="viewPayment") {
	echo $Admin->viewPayment($_POST['id']);
}
if ($service=="searchPayments") {
	echo $Admin->searchPayments($_POST['keyword']);
}
if ($service=="unconfirmedPayments") {
	echo $Admin->unconfirmedPayments();
}
if ($service=="confirmPayment") {
	echo $Admin->confirmPayment($_POST['id']);
}
if ($service=="viewEmployees") {
	echo $Admin->viewEmployees();
}
if ($service=="paySalary") {
	echo $Admin->paySalary($_POST['id']);
}
if ($service=="paymentHistory") {
	echo $Admin->paymentHistory();
}
if ($service=="arrangedMeetings") {
	echo $Admin->arrangedMeetings();
}
if ($service=="cancellableMeetings") {
	echo $Admin->cancellableMeetings();
}
if ($service=="cancelArrangement") {
	echo $Admin->cancelArrangement($_POST['id'],$_POST['advocateID'],$_POST['clientID'],$_POST['caseID']);
}
sleep(1);