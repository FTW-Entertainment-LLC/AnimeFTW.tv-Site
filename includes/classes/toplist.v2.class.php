<?php
/****************************************************************\
## FileName: toplist.v2.class.php								 
## Author: Brad Riemann									 
## Usage: TopList scripts
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
## Modified: 09/19/2014
## Version: 1.0.0
\****************************************************************/

class toplist extends Config {

	public function __construct()
	{
		parent::__construct();
	}
	
	// Parses through script variables sent via the scripts.php file
	
	public function scriptsFunctions()
	{
		if(isset($_GET['action']) && $_GET['action'] == 'record')
		{
			$this->recordEpisodeView();
		}
		else
		{
			echo 'That was not the function you were looking for..';
		}
	}
	
	// Will take an episode id and record it in the toplist records.
	private function recordEpisodeView()
	{
		// Check if the epid is set.. if it is not throw the book at em!
		if(!isset($_GET['epid']) && !is_numeric($_GET['epid']))
		{
			echo 'Error: The episode ID was invalid or wrong. Please try again.';
		}
		else
		{			
			$query = "SELECT `sid`, `epnumber` FROM `episode` WHERE `id` = '" . $this->mysqli->real_escape_string($_GET['epid']) . "'";
			$result = $this->mysqli->query($query);
			$row = $result->fetch_assoc();
	
			//Get the Date for today, all 24 hours
			$currentDay = date('d-m-Y',time());
			$midnight = strtotime($currentDay);
			$elevenfiftynine = $midnight+86399;
		
			//check for any rows that were done today...
			// we will want to switch out the seriesid and the epnumber for epid later.. just makes it easier..
			$query  = $this->mysqli->query("SELECT `id` FROM `episodestats` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `epSeriesId` = '" . $row['sid'] . "' AND `epNumber` = '" . $row['epnumber'] . "' AND `date` >= '" . $midnight . "'");
			$count = mysqli_num_rows($query);
		
			if($count == 0)
			{
				$query = "INSERT INTO `episodestats` (`eid`, `epSeriesId`, `ip`, `date`, `epnumber`)  VALUES ('" . $this->mysqli->real_escape_string($_GET['epid']) . "', '" . $row['sid'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '".time()."', '" . $row['epnumber'] . "')";
				$result = $this->mysqli->query($query);
			}
		}
	}
}