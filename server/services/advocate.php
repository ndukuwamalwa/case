<?php
$loader=require("../vendor/autoload.php");
$loader->addPsr4("flogert\\helpers\\","../Framework/Helpers");
$loader->addPsr4("flogert\\model\\","../Framework/Model");
$loader->addPsr4("flogert\\utils\\","../Framework/Utils");
$loader->addPsr4("functions\\","../functions");
use flogert\utils\Session;
use flogert\helpers\Security;
use functions\Advocate;

$Session=new Session();
if ($Session->get('username')==null || $Session->get('password')==null) {
	echo json_encode(['status' => 'login','message' => 'Please login first']);
	exit();
}
if ($_POST['token']!=$Session->get('token')) {
	echo json_encode(['status' => 'login','message' => 'Unauthorized.']);
	exit();
}
if ($Session->get('type')!='ADVOCATE') {
	echo json_encode(['status'=> 'login', 'message' => 'Unauthorized.']);
	exit();
}
if (!isset($_POST['service'])) {
	echo json_encode(["status" => "failed", "message" => "Unknown service request. Please contact administrator over this problem."]);
	exit();
}
$Advocate = new Advocate($Session->get('empID'));
if (!isset($_POST['service'])) {
	echo json_encode(["status" => "failed", "message" => "Unknown service request. Please contact administrator over this problem."]);
	exit();
}
$service=$_POST['service'];
if ($service=="stats") {
	echo $Advocate->stats();
}
if ($service=="viewProfile") {
	echo $Advocate->viewProfile();
}
if ($service=="addContact") {
	echo $Advocate->addContact($_POST['contact']);
}
if ($service=="viewContact") {
	echo $Advocate->viewContact();
}
if ($service=="deleteContact") {
	echo $Advocate->deleteContact($_POST['contact']);
}
if ($service=="addEducation") {
	echo $Advocate->addEducation(["specialization" => $_POST['specialization'],"institution" => $_POST['institution'],"achievement" => $_POST['achievement'],"year" => $_POST['year'],"description" => $_POST['description']]);
}
if ($service=="viewEducation") {
	echo $Advocate->viewEducation();
}
if ($service=="addProceeding") {
	echo $Advocate->addProceeding(["caseID" => $_POST['caseID'],"hearingDate" => $_POST['hearingDate'],"proceedings" => $_POST['proceedings']]);
}
if ($service=="appliedCases") {
	echo $Advocate->appliedCases();
}
if ($service=="searchCase") {
	echo $Advocate->searchCase($_POST['keyword']);
}
if ($service=="viewCase") {
	echo $Advocate->viewCase($_POST['id']);
}
if ($service=="viewWitnesses") {
	echo $Advocate->viewWitnesses($_POST['id']);
}
if ($service=="myCases") {
	echo $Advocate->myCases();
}
if ($service=="requestPayCases") {
	echo $Advocate->requestPayCases();
}
if ($service=="myActiveCases") {
	echo $Advocate->myActiveCases();
}
if ($service=="finalizeCase") {
	echo $Advocate->finalizeCase($_POST['caseID'],$_POST['clientID'],$_POST['outcome']);
}
if ($service=="setCost") {
	echo $Advocate->setCost($_POST['caseID'],$_POST['cost']);
}
if ($service=="viewCasePayment") {
	echo $Advocate->viewCasePayment($_POST['id']);
}
if ($service=="awaitingMeetings") {
	echo $Advocate->awaitingMeetings();
}
if ($service=="requestPayment") {
	echo $Advocate->requestPayment($_POST['caseID'],$_POST['clientID']);
}
if ($service=="casePayments") {
	echo $Advocate->casePayments($_POST['id']);
}
if ($service=="arrangeMeeting") {
	echo $Advocate->arrangeMeeting(["clientID" => $_POST['clientID'],"caseID" => $_POST['caseID'],"meetingDate" => $_POST['meetingDate'],"venue" => $_POST['venue']]);
}
if ($service=="arrangedMeetings") {
	echo $Advocate->arrangedMeetings();
}
if ($service=="markDone") {
	echo $Advocate->markDone($_POST['id'],$_POST['clientID'],$_POST['caseID']);
}
if ($service=="mySalaries") {
	echo $Advocate->mySalaries();
}
sleep(1);