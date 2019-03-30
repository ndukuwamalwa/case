<?php
namespace functions;

use flogert\model\CRUD;
use flogert\helpers\Security;
use flogert\utils\Session;

class Auth extends CRUD
{
	function login($username, $password)
	{
		$exist=json_decode($this->select("SELECT username,password,type FROM user WHERE username='{$username}'"));
		if (count($exist)>0) {
			if (Security::verify($password, $exist[0]->password)) {
				$Session=new Session();
				$Session->set("username", $username);
				$Session->set("password", $password);
				$token=Security::hash(session_id());
				$Session->set("token", $token);
				if ($exist[0]->type=="ADMIN") {
					$Session->set("type", "ADMIN");
					return json_encode(['status' => 'success','message' => 'ADMIN','token' => $token]);
				}elseif ($exist[0]->type=="ADVOCATE") {
					$empID=json_decode($this->select("SELECT empID FROM employee WHERE email='{$username}'"))[0]->empID;
					$Session->set("type", "ADVOCATE");
					$Session->set("empID", $empID);
					return json_encode(['status' => 'success','message' => 'ADVOCATE','token' => $token]);
				}elseif ($exist[0]->type=="CLIENT") {
					$clientID=json_decode($this->select("SELECT clientID FROM client WHERE email='{$username}'"))[0]->clientID;
					$Session->set("type", "CLIENT");
					$Session->set("clientID", $clientID);
					return json_encode(['status' => 'success','message' => 'CLIENT','token' => $token]);
				}else{
					return json_encode(['status' => 'failed','message' => 'Technical error occurred.']);
				}
			}else {
				return json_encode(['status' => 'failed','message' => 'Invalid username or password.']);
			}
		}else{
			return json_encode(['status' => 'failed','message' => 'Invalid username or password.']);
		}
	}
}