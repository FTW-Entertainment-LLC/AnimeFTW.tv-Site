<?php
/****************************************************************\
## FileName: profile.class.php									 
## Author: Brad Riemann										 
## Usage: Profile Constructor class for the mobile website.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Profile extends Config{
	var $uid;
	public function __construct()
	{
		parent::__construct();
		$this->uid = $_COOKIE['cookie_id'];
	}	
	
	public function Init()
	{
		$this->DisplayUser();
	}
	private function DisplayUser(){
		if (!isset($this->uid))
		{
			echo "Not logged in";
		}
		else
		{
		$query = "SELECT * FROM users WHERE ID = '" . $this->mysqli->real_escape_string($this->uid) . "'";
		$results = $this->mysqli->query($query);
		$row = $results->fetch_assoc();
		echo $row['Username']."<br />";
		echo $row['PersonalMsg']."<br />";
		echo $row['ID']."<br />";
		}
	}
}

?>