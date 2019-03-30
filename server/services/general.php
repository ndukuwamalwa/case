<?php
$loader=require("../vendor/autoload.php");
header("Access-Control-Allow-Origin: *");
$loader->addPsr4("flogert\\helpers\\","../Framework/Helpers");
$loader->addPsr4("flogert\\model\\","../Framework/Model");
$loader->addPsr4("flogert\\utils\\","../Framework/Utils");
$loader->addPsr4("functions\\","../functions");
use flogert\utils\Session;
use flogert\helpers\Security;
use functions\Client;
use functions\Advocate;

$Client=new Client(0);
$Advocate=new Advocate(0);

if (!isset($_POST['service'])) {
	echo json_encode(['status' => "failed", "message" => "Please specify a service"]);
	exit();
}
$service=$_POST['service'];
if ($service=="register") {
	$password=Security::hash($_POST['password']);
	echo $Client->register(['fname' => $_POST['fname'], 'lname' => $_POST['lname'], 'mname' => $_POST['mname'], 'dob' => $_POST['dob'], 'contact' => $_POST['contact'], 'email' => $_POST['email'], 'address' => $_POST['address'], 'password' => $password]);
}
if ($service=="systemStats") {
	echo $Advocate->systemStats();
}
sleep(1);