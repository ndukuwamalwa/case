<?php
namespace flogert\utils;

Class Session
{
	/**
	*Starts a session if not started.
	*/
	function __construct()
	{
		if ( !isset($_SESSION) )
		{
			session_start();
		}
	}
	/**
	*Sets a session variable with the given value.
	*@param string $key
	*@param mixed $value
	*/
	function set($key,$value)
	{
		if ( isset($_SESSION[$key]) )
		{
			unset($_SESSION[$key]);
		}
		$_SESSION[$key]=$value;
	}
	/**
	*Checks whether a given key is available in the $_SESSION.
	*@param string $key
	*@return boolean
	*/
	function has($key)
	{
		if (! isset($_SESSION[$key])){
			return false;
		}
		return true;
	}
	/**
	*Gets a session value based on the key.
	*@param string $key
	*@return mixed $value
	*/
	function get($key)
	{
		if ( isset($_SESSION[$key]) )
		{
			return $_SESSION[$key];
		}
	}
	/**
	*deletes a session value based on the key and unsets the key
	*@param string $key
	*@return boolean $state
	*/
	function del($key)
	{
		if ( !isset($_SESSION[$key]) )
		{
			return true;
		}
		unset($_SESSION[$key]);
		//since unset returns void, we device other means of knowing whether the item is really deleted
		if ( isset($_SESSION[$key]) )
		{
			return false;
		}
		return true;
	}
	/**
	*Destroys the whole session.
	*/
	function destroy()
	{
		if (isset($_SESSION))
		{
			$_SESSION=null;
		}
		session_destroy();
	}
}
