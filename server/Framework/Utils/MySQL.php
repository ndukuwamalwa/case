<?php
namespace flogert\utils;

use \PDO;
use flogert\utils\Config;
/**
*Handles database connectivity and driver.
*/
Class MySQL
{
	/**
	*Connects to the database.
	*@param array $options;
	*@return PDO
	*/
	function connect()
	{
		try{
			$host=Config::DB_HOST;
			$dbname=Config::DB_NAME;
			$pdo=new PDO("mysql:host={$host};dbname={$dbname}",Config::DB_USER,Config::DB_PASSWORD);
			$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_SILENT);
			return $pdo;
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}
}