<?php
/****************************************************************\
## FileName: tracker.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Tracker Class and subfunctions
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
## Updated: 05/10/2014 by Robotman321
## Version: 1.0.0
\****************************************************************/

class Tracker extends Config {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function checkAutoRecord($epid)
	{
		// This will automatically check the users settings to ensure that we auto record to the tracker if we are requested.
		// this will only happen if they get past 60% of the video.
		if(isset($this->SettingsArray[9]) && $this->SettingsArray[9]['disabled'] != 1)
		{
			if($this->SettingsArray[9]['value'] == 17)
			{
				// we record the episode for them
				$query = "INSERT INTO episode_tracker (`uid`, `eid`, `seriesName`, `dateViewed`) VALUES ('" . $this->UserArray['ID'] . "', '" . $this->mysqli->real_escape_string($epid) . "', (SELECT `sid` FROM `episode` WHERE `id` = " . $this->mysqli->real_escape_string($epid) . "), '" . time() . "')";
				$result = $this->mysqli->query($query);
			}
			else
			{
				// this shouldnt be here, its the default and they dont get to auto record.
			}
		}
	}
}