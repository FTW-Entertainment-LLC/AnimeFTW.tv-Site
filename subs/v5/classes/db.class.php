<?php
/****************************************************************\
## FileName: db.class.php									 
## Author: Brad Riemann
## Version: 5.0.0
## Usage: MYSQLI Database Connector/Contructor Class
## Copyright 2013 FTW Entertainment LLC, All Rights Reserved.
\****************************************************************/
/*
		// Then simply connect to your DB this way:
 		$db = new DB($config);

		// Run a Query:
 		$db->query('SELECT * FROM someplace');

 		// Get an array of items:
 		$result = $db->get();
 		print_r($result);
 		
 		// Optional fetch modes (1 and 2)
 		$db->setFetchMode(1);
 		
 		// Get a single item:
 		$result = $db->get('field');
 		print_r($result);
 */
class db
{
	/**
	* @var <str> The mode to return results, defualt is MYSQLI_BOTH, use setFetchMode() to change.
	*/
	private $fetchMode = MYSQLI_BOTH;
	
	/**
	* @desc		Creates the MySQLi object for usage.
	*
	* @param	<arr> $db Required connection params.
	*/
	public function  __construct($db) {
		$this->mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['table']);

		if (mysqli_connect_errno()) 
		{
			printf("<b>Connection failed:</b> %s\n", mysqli_connect_error());
			exit;
		}
	}
	
	/** 
	* @desc		Optionally set the return mode.
	*
	* @param	<int> $type The mode: 1 for MYSQLI_NUM, 2 for MYSQLI_ASSOC, default is MYSQLI_BOTH
	*/
	public function setFetchMode($type)
	{
		switch($type)
		{			
			case 1:
			$this->fetchMode = MYSQLI_NUM;
			break;
			
			case 2:
			$this->fetchMode = MYSQLI_ASSOC;
			break;
			
			default:
			$this->fetchMode = MYSQLI_BOTH;
			break;

		}

	}

	/**
	 * @desc	Simple preparation to clean the SQL/Setup result Object.
	 *
	 * @param	<str> SQL statement
	 * @return	<bln|null>
	 */
	public function query($SQL)
	{
		$this->SQL = $this->mysqli->real_escape_string($SQL);
		$this->result = $this->mysqli->query($SQL);

		if ($this->result == true)
		{
			return true;
		}
		else
		{
			printf("<b>Problem with SQL:</b> %s\n", $this->SQL);
			exit;
		}
	}

	/**
	 * @desc	Get the results
	 *
	 * @param	<str|int> $field Select a single field, or leave blank to select all.
	 * @return	<mixed>
	 */
	public function get($field = NULL)
	{
		if ($field == NULL)
		{
			/** Grab all the data */
			$data = array();

			while ($row = $this->result->fetch_array($this->fetchMode))
			{
				$data[] = $row;
			}
		}
		else
		{
			/** Select the specific row */
			$row = $this->result->fetch_array($this->fetchMode);
			$data = $row[$field];
		}

		/** Make sure to close the result Set */
		$this->result->close();

		return $data;

	}
	
	/**
	* @desc		Returns the automatically generated insert ID
	* 			This MUST come after an insert Query.
	*/
	public function id()
	{
		return $this->mysqli->insert_id;
	}

	/**
	 * @desc	Automatically close the connection when finished with this object.
	 */
	public function __destruct()
	{
		$this->mysqli->close();
	}

}


?>