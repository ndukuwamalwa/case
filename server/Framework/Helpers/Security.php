<?php
namespace flogert\helpers;

/**
*Handles security like encrption
*/
Class Security
{
	/**
	*hashes a given string and returns it.
	*@param string $string
	*@return string $cipher
	*/
	static function hash($string)
	{
		return password_hash($string, PASSWORD_DEFAULT);
	}
	/**
	*verifies whether the first argument is the plain text of the second argument
	*/
	static function verify($string,$cipher)
	{
		return (password_verify($string,$cipher)) ? true : false;
	}
}