<?php
namespace flogert\utils;

use \Exception;
use flogert\helpers\Validation;
/**
*Handles task scheduling through crontab and anacrom
*/
Class Cron
{
	/**
	*Temporary file that holds the operations.
	*@private
	*/
	private $cron_file;
	/**
	*The directory for dealing with scheduling tasks.
	*@private
	*/
	private $dir;
	/**
	*The minute to for the cron job.
	*@private
	*/
	private $min;
	/**
	*The hour to for the cron job.
	*@private
	*/
	private $hour;
	/**
	*The day to for the cron job.
	*@private
	*/
	private $day;
	/**
	*The month to for the cron job.
	*@private
	*/
	private $month;
	/**
	*The week day to for the cron job.
	*@private
	*/
	private $weekDay;
	/**
	*The program to execute the job.
	*@private
	*/
	private $program;
	/**
	*The file to execute;
	*@private
	*/
	private $file;
	/**
	*@param string $dir
	*/
	function __construct($dir)
	{
		if (! is_dir($dir)){
			throw new Exception("The provided path is not a valid directory.");
		}
		if (! is_writable($dir)){
			throw new Exception("The provided directory is not writable.");
		}
		$this->dir=$dir.DIRECTORY_SEPARATOR;
		$file_name="cron_file.sh";
		if (! file_exists($this->dir.$file_name)){
			//Create the file
			$create=file_put_contents($this->dir.$file_name,"");
		}
		$this->cron_file=$this->dir.$file_name;

	}
	/**
	*Checks the validity of the given values.
	*@param int $val
	*@param int $min
	*@param int $max
	*/
	function valid($val, $min=null, $max=null)
	{
		if (!is_integer($val) && $val!="*"){
			throw new Exception("The given value must be an integer or an asteric");
		}
		if (is_null($min) && is_null($max)){
			return true;
		}
		if ($val<$min || $val>$max){
			throw new Exception("The value mus be within the range [{$min}-{$max}]");
		}
		return true;
	}
	/**
	*The minute the task is to be executed. Ranges from 0-59.
	*Use an asteric(*) to mean all.
	*@param int $min
	*@return $this
	*/
	function min($min)
	{
		$this->valid($min,0,59);
		$this->min=$min;
		return $this;
	}
	/**
	*The hour the task is to be executed. Ranges from 0-23, where 0 is midnight.
	*Use an asteric(*) to mean all.
	*@param int $min
	*@return $this
	*/
	function hour($hour)
	{
		$this->valid($hour,0,23);
		$this->hour=$hour;
		return $this;
	}
	/**
	*The day of the month the task is to be executed. Depends on the selected month and can range from 0-28, 0-29, 0-30 or 0-31.
	*Use an asteric(*) to mean all.
	*@param int $min
	*@return $this
	*/
	function day($day)
	{
		$this->valid($day,1,31);
		$this->day=$day;
		return $this;
	}
	/**
	*The month the task is to be executed. Ranges from 1-12, where 1 is January.
	*Use an asteric(*) to mean all.
	*@param int $min
	*@return $this
	*/
	function month($month)
	{
		$this->valid($month,1,12);
		$this->month=$month;
		return $this;
	}
	/**
	*The day of the week the task is to be executed. Ranges from 0-6, where 0 is Sunday.
	*Use an asteric(*) to mean all.
	*@param int $min
	*@return $this
	*/
	function weekDay($weekDay)
	{
		$this->valid($weekDay,0,6);
		$this->weekDay=$weekDay;
		return $this;
	}
	/**
	*The path of the program that will execute the job.
	*@param string $path
	*@return $this
	*/
	function program($path)
	{
		$this->program=$path;
		return $this;
	}
	/**
	*The file to execute.
	*@param string $file
	*/
	function file($file)
	{
		if (! Validation::file($file)){
			throw new Exception("The provided path is not a valid file.");
		}
		$this->file=$file;
		return $this;
	}
	/**
	*Adds a job to the cron file.
	*@return $this
	*/
	function add()
	{
		if (! isset($this->min) || ! isset($this->hour) || ! isset($this->day) || ! isset($this->month) || ! isset($this->weekDay)){
			throw new Exception("Make sure to provide the minute, hour, day, month and day of the week to add a new job.");
		}
		$jobs= $this->list();
		$new=$this->min." ".$this->hour." ".$this->day." ".$this->month." ".$this->weekDay." \"".$this->program."\" \"".$this->file."\"\n";
		array_push($jobs, $new);
		$jobs=implode("\n", $jobs);
		file_put_contents($this->cron_file, $jobs);
		return $this;
	}
	/**
	*Removes a job from the cron file.
	*Like add(), this method requires the whole procedure of creating a job but instead of adding the job, it removes it.
	*You have to specify the min, hour, day, month, day of the week, program and file of the job you want to remove.
	*@return $this
	*/
	function remove()
	{
		
	}
	/**
	*Executes the cron file.
	*@return boolean
	*/
	function execute()
	{
		$commands=file_get_contents($this->cron_file);
		if ($commands == ""){
			throw new Exception("No commands to execute.");
		}
		$output=array();
		exec("crontab ./{$this->cron_file}");
		
		return true;
	}
	/**
	*Lists all the cron jobs.
	*@return array $jobs
	*/
	function list()
	{
		$jobs=array();
		exec("crontab -l",$jobs);
		return json_encode($jobs);
	}
	/**
	*Deletes the whole crontab.
	*@return void
	*/
	function destroy()
	{
		exec("crontab -r");
	}

}