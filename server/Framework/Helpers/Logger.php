<?php
namespace flogert\helpers;

use \Exception;
/**
*Handles system logging process;
*/
Class Logger
{
	/**
	*Does the actual logging.
	*@param string $message
	*@param string $file_name
	*@return boolean
	*/
	function log($message,$file_name)
	{
		if (!file_exists($file_name)){
			//throw new Exception("The specified logging file could not be found");	
		}
		if (!is_writable($file_name)){
			//throw new Exception("The specified file cannot be written into.");
		}
		if (file_put_contents($file_name, $message.PHP_EOL,FILE_APPEND)){
			return true;
		}
		return false;
	}
}