<?php
namespace flogert\utils;

use AfricasTalking\SDK\AfricasTalking;
use GuzzleHttp\Exception\GuzzleException;
use \Exception;
/**
*Short Message Service handler
*/
Class SMS
{
	/**
	*The username of the API service.
	*@private
	*/
	private $api_username;
	/**
	*The API key
	*@private
	*/
	private $api_key;
	/**
	*The authenticated telco object.
	*/
	private $client;
	/**
	*Takes the username and the key for initialization and Authorization.
	*/
	function __construct($api_username=null, $api_key=null)
	{
		if (is_null($api_username) || is_null($api_key)){
			throw new Exception("API username/key cannot be null");
		}
		$this->api_username=$api_username;
		$this->api_key=$api_key;
		$at=new AfricasTalking($this->api_username, $this->api_key);
		$this->client=$at->sms();
		$this->tokenClient=$at->token();
	}
	/**
	*Sends the message.
	*@param string $message
	*@param array $recipients
	*@return boolean
	*/
	function send($message, array $recipients)
	{
		if (count($recipients)===0){
			throw new Exception("You must specify atleast one recipient of the message.");
		}
		if (strlen($message)>160){
			throw new Exception("Message cannot be longer than 160 characters");
		}
		if (!is_string($message)){
			throw new Exception("The message must be a string");
		}
		foreach ($recipients as $recipient) {
			$this->client->send([
				'to' => $recipient,
				'message' => $message
			]);
		}
		return true;
	}
}