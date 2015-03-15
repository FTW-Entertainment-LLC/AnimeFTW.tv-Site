<?php
/****************************************************************\
## FileName: episode-count-harvest.cron.php									 
## Author: Brad Riemann										 
## Usage: Runs every two hours, grabs episode data for 
## Analytics and stats.
## Copyright 2014 FTW Entertainment LLC, All Rights Reserved
## Version: 1.0.0
## Updated: 17/09/2014 @ 3:30pm CST by Robotman321
\****************************************************************/

require_once("/home/mainaftw/public_html/includes/classes/config.v2.class.php");
	
class EpisodeStats extends Config {
	private $CronID, $ScriptDay, $CollectionTime;
	
	public function __construct()
	{
		parent::__construct();
		// variable declarations
		$this->CronID			= 14;				// ID of this cron
		$this->ScriptDay		= strtotime(date("j F Y")); 			// Used for matching any records created at this time.
		$this->CollectionTime 	= 60*60*2;			// the period of time we want to take from, default is 2 hours
		
		// SCript init
		$this->initializeScript();
		$this->updateCronEntry();
	}
	
	private function initializeScript()
	{
		/*
		* The object of the script is to go through the records of the topseries list (before they are processed)
		* and record for each episode via a +1. 
		* Steps:
		* 1) Cycle through data from the last two hours, group and then record the data/
		* We will be updateing the `views` column for episodes as well as adding/updating rows for the day
		* in the stats table.
		*/
		
		$query = "SELECT COUNT(id) as `numrows`, eid FROM `" . $this->MainDB . "`.`episodestats` WHERE `date` >= " . (time()-$this->CollectionTime) . " GROUP BY eid";
		$result = $this->mysqli->query($query);
		
		while($row = $result->fetch_assoc())
		{
			// First Update the views of the episode on the episode..
			$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`episode` SET `views` = `views` + " . $row['numrows'] . " WHERE `id` = " . $row['eid']);
			// check to see if the entry is already in the system..
			$available = $this->checkIfEntryExists($eid);
			if($available == 0)
			{
				// it's not, add an entry.
				$query = "INSERT INTO `" . $this->StatsDB . "`.`episode_stats` (`id`, `date`, `type`, `episode_id`, `value`) VALUES (NULL, '" . $this->ScriptDay . "', 0, '" . $row['eid'] . "', '" . $row['numrows'] . "')";
			}
			else
			{
				// we have something there, let's update it.
				$query = "UPDATE `" . $this->StatsDB . "`.`episode_stats` SET `value` = `value` + " . $row['numrows'] . " WHERE `episode_id` = " . $row['eid'] . " AND `type` = 0 AND `date` = '" . $this->ScriptDay . "'";
			}
			$this->mysqli->query($query);
		}
	}
	
	private function checkIfEntryExists($eid)
	{
		$query = "SELECT `value` FROM `" . $this->StatsDB . "`.`episode_stats` WHERE `type` = 0 AND `date` = '" . $this->ScriptDay . "' AND `episode_id` = '" . $eid . "'";
		$result = $this->mysqli->query($query);
		$count = mysqli_num_rows($result);
		if($count > 0)
		{
			// The count is larger than 0.. so something is there..
			$row = $result->fetch_assoc();
			return $row['value'];
		}
		else
		{
			return 0;
		}
	}
	
	private function updateCronEntry()
	{
		$this->mysqli->query("INSERT INTO `" . $this->MainDB . "`.`crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->CronID . "', '" . time() . "', '" . time() . "');");
		$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`crons` SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
	}
}

$EpisodeStats = new EpisodeStats();