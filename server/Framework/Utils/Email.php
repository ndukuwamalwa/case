<?php
namespace flogert\utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use flogert\helpers\Validation;
/**
*Sends e-mails
*/
Class Email
{
	/**
	*PHPMailer object
	*@private
	*/
	private $mail;
	/**
	*Mail server.
	*@private
	*/
	private $host;
	/**
	*Authentication username.
	*@private
	*/
	private $username;
	/**
	*Authentication password.
	*@private
	*/
	private $password;
	/**
	*Enable authentication?
	*@private
	*/
	private $smtp_auth;
	/**
	*Use TLS?
	*@private
	*/
	private $smtp_secure;
	/**
	*Communication port number.
	*@private
	*/
	private $port;
	/**
	*Mail receivers.
	*@private
	*/
	function __construct($host=null, $port, $username, $password)
	{
		$this->mail=new PHPMailer(true);
		if (is_null($host)){
			throw new Exception("The mail host cannot be null.");
		}
		if (is_null($username) || is_null($password)){
			throw new Exception("Please specify a username and password for the mail server");
		}
		$this->host=$host;
		$this->port=$port;
		$this->username=$username;
		$this->password=$password;
		$this->smtp_auth=true;
		$this->smtp_secure='tls';
		try{
			//Server settings
			 $this->mail->SMTPDebug = 1;
		     $this->mail->isSMTP();
		     $this->mail->Host = $this->host;
		     $this->mail->SMTPAuth = $this->smtp_auth;
		     $this->mail->Username = $this->username;
		     $this->mail->Password = $this->password;
		     $this->mail->SMTPSecure = $this->smtp_secure;
		     $this->mail->Port = $this->port;   
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	/**
	*Sets the mail sender.
	*@param string $email
	*@param string $name
	*@return $this
	*/
	function sender($email, $name=null)
	{
		if (!Validation::email($email)){
			throw new Exception("Please specify a valide email address for the sender.");
		}
		$this->mail->setFrom($email,$name);
		return $this;
	}
	/**
	*Adds recipient addresses.Pass an associative array with keys 'email' and 'name'.
	*@param array $address
	*@return $this
	*/
	function addAddress(array $addresses)
	{
		if (count($addresses)===0){
			throw new Exception("You must specify at least one address.");
		}
		foreach ($addresses as $address) {
			if (array_key_exists('email', $address) && array_key_exists('name', $address))
			{
				if (Validation::email($address['email']) && Validation::text($address['name'])){
					$this->mail->addAddress($address['email'],$address['name']);
				}
			}
		}
		return $this;
	}
	/**
	*Adds a reply email incase of any replys.
	*@param string $email
	*@param string $name
	*/
	function replyTo($email, $name)
	{
		if (!Validation::email($email)){
			throw new Exception("'Reply to' must be a valid email address.");
		}
		$this->mail->addReplyTo($email,$name);
		return $this;
	}
	/**
	*Adds attachments to the mail.
	*@param array $files
	*/
	function addAttachments(array $files)
	{
		if (count($files)===0){
			throw new Exception("Please specify a file to add.");
		}
		foreach ($files as $file) {
			if (!Validation::file($file)){
				throw new Exception("{$file} is not a valid file.");
			}
		}
		try{
			foreach ($files as $file) {
				$this->mail->addAttachment($file);
			}
		}catch(Exception $e){
			echo $e->getMessage();
		}
		return $this;
	}
	/**
	*Finalizes and sends the email.
	*@param string $subject.
	*@param string $body
	*/
	function send($subject, $body)
	{
		try{
			$this->mail->isHTML(true);
			$this->mail->Subject=$subject;
			$this->mail->Body=$body;
			$this->mail->send();
			return true;
		}catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
	}
}