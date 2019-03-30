<?php
namespace flogert\helpers;

/**
*Handles frequent validation problems.
*/
Class Validation
{
	/**
	*Checks whether a given string is an email address.
	*@param string $string
	*@return boolean
	*/
	static function email($string)
	{
		if (is_null($string)){
			return false;
		}
		$is_email=(preg_match("/^[a-zA-Z][a-zA-Z0-9]*[@]{1}[a-zA-Z0-9]+/i", $string)) ? true : false;
		return $is_email;
	}
	/**
	*@param string $string
	*@return boolean
	*/
	static function text($string)
	{
		$is_text=(is_string($string)) ? true : false;
		return $is_text;
	}
	/**
	*@param string $string
	*@return boolean
	*/
	static function number($string)
	{
		$is_number=(is_int($string) || is_integer($string) || is_numeric($string)) ? true : false;
		return $is_number;
	}
	/**
	*@param string $string
	*@return boolean
	*/
	static function file($path)
	{
		$is_file=(is_file($path)) ? true : false;
		return $is_file;
	}
	/**
	*@param string $string
	*@return boolean
	*/
	static function password($string)
	{
		if (strlen($string)<8){
			return false;
		}
		if (!preg_match("/[,\.\?\/:;!@#$%^&*\(\-\)\+]/", $string)){
			return false;
		}
		if (!preg_match("/[a-z]{1,}/", $string)){
			return false;
		}
		if (!preg_match("/[A-Z]{1,}/", $string)){
			return false;
		}
		if (!preg_match("/[0-9]{1,}/", $string)){
			return false;
		}
		return true;
	}
}
