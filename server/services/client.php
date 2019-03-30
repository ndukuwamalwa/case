<?php
$loader=require("../vendor/autoload.php");
$loader->addPsr4("flogert\\helpers\\","../Framework/Helpers");
$loader->addPsr4("flogert\\model\\","../Framework/Model");
$loader->addPsr4("flogert\\utils\\","../Framework/Utils");
$loader->addPsr4("functions\\","../functions");
use flogert\utils\Session;
use flogert\helpers\Security;
use functions\Client;

$Session=new Session();
if ($Session->get('username')==null || $Session->get('password')==null) {
	echo json_encode(['status' => 'login','message' => 'Please login first']);
	exit();
}
if ($_POST['token']!=$Session->get('token')) {
	echo json_encode(['status' => 'login','message' => 'Unauthorized.']);
	exit();
}
if ($Session->get('type')!='CLIENT') {
	echo json_encode(['status'=> 'login', 'message' => 'Unauthorized.']);
	exit();
}
if (!isset($_POST['service'])) {
	echo json_encode(["status" => "failed", "message" => "Unknown service request. Please contact administrator over this problem."]);
	exit();
}
$Client = new Client($Session->get("clientID"));
if (!isset($_POST['service'])) {
	echo json_encode(["status" => "failed", "message" => "Unknown service request. Please contact administrator over this problem."]);
	exit();
}
$service=$_POST['service'];
if ($service=="stats") {
	echo $Client->stats();
}
if ($service=="notifications") {
	echo $Client->notifications();
}
if ($service=="markNotification") {
	echo $Client->markNotification($_POST['id']);
}
if ($service=="addCase") {
	echo $Client->addCase(['caseTitle' => $_POST['caseTitle'], 'caseType' => $_POST['caseType'], 'courtName' => $_POST['courtName'], 'advocateID' => $_POST['advocateID'], 'firstHearing' => $_POST['firstHearing'], 'description' => $_POST['description']]);
}
if ($service=="newNotifications") {
	echo $Client->newNotifications();
}
if ($service=="searchAdvocate") {
	echo $Client->searchAdvocate($_POST['keyword']);
}
if ($service=="viewProfile") {
	echo $Client->viewProfile();
}
if ($service=="appliedCases") {
	echo $Client->appliedCases();
}
if ($service=="cancelCase") {
	echo $Client->cancelCase($_POST['caseID']);
}
if ($service=="addWitness") {
	echo $Client->addWitness(["caseID" => $_POST["caseID"],"witnessName" => $_POST["witnessName"],"address" => $_POST["address"],"contact" => $_POST["contact"],"email" => $_POST["email"]]);
}
if ($service=="searchCase") {
	echo $Client->searchCase($_POST['keyword']);
}
if ($service=="witnesses") {
	echo $Client->witnesses($_POST['caseID']);
}
if ($service=="deleteWitness") {
	echo $Client->deleteWitness($_POST['id']);
}
if ($service=="viewCase") {
	echo $Client->viewCase($_POST['id']);
}
if ($service=="finalizeCase") {
	echo $Client->finalizeCase($_POST['caseID'],$_POST['outcome']);
}
if ($service=="myActiveCases") {
	echo $Client->myActiveCases();
}
if ($service=="proceedings") {
	echo $Client->proceedings($_POST['caseID']);
}
if ($service=="viewCosts") {
	echo $Client->viewCosts();
}
if ($service=="pay") {
	echo $Client->pay(["caseID" => $_POST['caseID'],"transID" => $_POST['transID'],"amount" => $_POST['amount']]);
}
if ($service=="casePayments") {
	echo $Client->casePayments($_POST['caseID']);
}
if ($service=="meetings") {
	echo $Client->meetings();
}
sleep(1);