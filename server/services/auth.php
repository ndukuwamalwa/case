<?php
$loader=require("../vendor/autoload.php");
$loader->addPsr4("flogert\\helpers\\","../Framework/Helpers");
$loader->addPsr4("flogert\\model\\","../Framework/Model");
$loader->addPsr4("flogert\\utils\\","../Framework/Utils");
$loader->addPsr4("functions\\","../functions");
use flogert\utils\Session;
use flogert\helpers\Security;
use functions\Auth;
$Auth=new Auth();
if (isset($_POST['username']) && isset($_POST['password'])) {
	echo $Auth->login($_POST['username'],$_POST['password']);
}else{
	echo json_encode(['status' => 'failed','message' => 'username and password are required']);
}
if (isset($_POST['service']) && $_POST['service']=="logout") {
	$Session=new Session();
	$Session->destroy();
	echo json_encode(["status" => "success", "message" => ""]);
}
sleep(1);