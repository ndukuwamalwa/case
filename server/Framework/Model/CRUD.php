<?php
namespace flogert\model;

use flogert\utils\MySQL;
use \PDOStatement;

class CRUD extends MySQL
{
	protected function insert($table, $columns, $values, array $response)
	{
		$colString=implode(",", $columns);
		$colPrep=":".implode(",:", $columns);
		$query="INSERT INTO {$table}({$colString}) VALUES ({$colPrep})";
		$statement=$this->connect()->prepare($query);
		for ($i=0; $i < count($columns); $i++) { 
			$statement->bindValue(":".$columns[$i],$values[$i]);
		}
		$statement->execute();
		return $this->complete($statement,$response);
	}
	protected function del($table, array $conditions, array $response)
	{
		$columns=array_keys($conditions);
		$values=array_values($conditions);
		$query='';
		$lastCondition='';
		$prepCols=[];
		for ($i=0;$i<count($columns);$i++) {
			array_push($prepCols, $columns[$i]."=:".$columns[$i]);
		}
		$query="DELETE FROM {$table} WHERE ".implode(" AND ", $prepCols);
		$statement=$this->connect()->prepare($query);
		for ($i=0; $i < count($columns); $i++) { 
			$statement->bindValue(":".$columns[$i],$values[$i]);
		}
		$statement->execute();
		return $this->complete($statement,$response);
	}
	protected function modify($table, array $columns, array $values, array $conditions,array $response)
	{
		$setCols=[];
		for ($i=0; $i<count($columns); $i++) {
			array_push($setCols, $columns[$i]."=:".$columns[$i]);
		}
		$setString=implode(",", $setCols);
		$condColumns=array_keys($conditions);
		$condValues=array_values($conditions);
		$condsArray=[];
		for ($i=0;$i<count($condColumns);$i++) {
			array_push($condsArray, $condColumns[$i]."=:".$condColumns[$i]);
		}
		$condString=" WHERE ".implode(" AND ", $condsArray);
		$query="UPDATE {$table} SET {$setString}{$condString}";
		$statement=$this->connect()->prepare($query);
		for ($i=0;$i<count($columns);$i++) {
			$statement->bindValue(":".$columns[$i],$values[$i]);
		}
		for ($i=0;$i<count($condColumns);$i++) {
			$statement->bindValue(":".$condColumns[$i],$condValues[$i]);
		}
		$statement->execute();
		return $this->complete($statement,$response);
	}
	protected function select($query)
	{
		$query=$this->connect()->query($query);
		$results=$query->fetchAll(\PDO::FETCH_ASSOC);
		return json_encode($results);
	}
	protected function getFile($query)
	{
		$statement=$this->connect()->query($query);
		$result=$statement->fetch(\PDO::FETCH_ASSOC);
		return $result;
	}
	protected function call($procedure, array $args,$response=[])
	{
		$pdo=$this->connect();
		if (count($args)>0){
			$toks=[];
			for ($i=0; $i < count($args); $i++) { 
				array_push($toks, ":"."toks".$i);
			}
			$queryString=implode(",", $toks);
			$query="CALL {$procedure}({$queryString})";
			$statement=$pdo->prepare($query);
			for ($i=0; $i<count($args);$i++) {
				$statement->bindValue(":toks".$i,$args[$i]);
			}
			$statement->execute();
			if (array_key_exists("success", $response) && array_key_exists("failed", $response)) {
				if ($statement->errorCode()=="00000") {
					return json_encode(["status" => "success", "message" => $response['success']]);
				}else{
					return json_encode(["status" => "failed", "message" => $response['failed']]);
				}
			}
			$results=$statement->fetchAll(\PDO::FETCH_ASSOC);
			$statement->closeCursor();
			return json_encode($results);
		}else{
			$statement=$pdo->query("CALL {$procedure}()");
			$results=$statement->fetchAll(\PDO::FETCH_ASSOC);
			$statement->closeCursor();
			return json_encode($results);
		}
	}
	private function complete(PDOStatement $statement, array $response)
	{
		$errorCode=$statement->errorCode();
		$statement->closeCursor();
		if ($errorCode=="00000") {
			return json_encode([
				'status' => 'success',
				'message' => $response['success']
			]);
		}else{
			return json_encode([
				'status' => 'failed',
				'message' => $response['failed']
			]);
		}
	}
}