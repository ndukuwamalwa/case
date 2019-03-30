<?php
namespace flogert\helpers;

/**
*String and other formating operations.
*/
Class Format
{
	/**
	*Formats a string into comma-separated number.
	*@param string $string
	*@return string $formatted
	*/
	function number($string)
	{
		if (!is_numeric($string) || is_nan($string)){
			throw new Exception("Not a number given for formating.");
		}

		return number_format($string);
	}
	/**
	*formats an array into json.
	*@param array $arr
	*@return string $json
	*/
	function json(array $arr)
	{
		return json_encode($arr);
	}
}