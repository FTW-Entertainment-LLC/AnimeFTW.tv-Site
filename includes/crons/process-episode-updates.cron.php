<?php
/****************************************************************\
## FileName: process-episode-updates.cron.php									 
## Author: Brad Riemann										 
## Usage: Interfaces with the episode_update table to take episode
## updates and do them autonomously, this removes manual functions
## that would have previously cause workflow issues
## Copyright 2014 FTW Entertainment LLC, All Rights Reserved
## Author: robotman321
## Version: 1.0
## Updated: 27/09/2014
\****************************************************************/

require_once("/home/mainaftw/public_html/includes/classes/config.v2.class.php");
require_once("/home/mainaftw/public_html/includes/classes/email.class.php");

/*
* The objective of this script is to allow for management to free up certain functions, like
* creating episode images, creating mp4 variants of videos, and doing mass series updates.
* 
* This script is 100% cron controlled to get these functions out of the hands of staff,
* this is not for their safety but their sanity so that they can click and go.
* 
* These are the types used by the script/matched to the DB
* 0 - Create normal mp4 (SD version)
* 1 - Create Episode images
* 2 - Create 720p mp4
* 3 - Create 1080p mp4
*/

class UpdateVideos extends Config {

	var $CronID, $IssueList;
	
	public function __construct()
	{
		// Include the parent class so we can use it's functions
		parent::__construct();
		
		// variables..
		$this->CronID			= 15;
		
		// Script init
		$this->initializeScript();
		$this->updateCronEntry();
	}
	
	private function initializeScript()
	{
		$query = "SELECT `id`, `type`, `eid` FROM `" . $this->MainDB . "`.`episode_updates` WHERE `status` = 0";
		$result = $this->mysqli->query($query);
		
		while($row = $result->fetch_assoc())
		{
			// we need to create a mux of the SD version, type 0
			if($row['type'] == 0)
			{
				$this->createSDMP4($row['eid']);
				$this->updateAutoEntry($row['id']);
			}
			// this entry needs to recreate the episode image, type 1
			else if($row['type'] == 1)
			{
				$this->createEpisodeImage($row['eid']);
				$this->updateAutoEntry($row['id']);
			}
			// this will create a 720p mux, type 2
			else if($row['type'] == 2)
			{
				$this->create720pMP4($row['eid']);
				$this->updateAutoEntry($row['id']);
			}
			// creating a 1080p muxh, type 3
			else if($row['type'] == 3)
			{
				$this->create1080oMP4($row['eid']);
				$this->updateAutoEntry($row['id']);
			}
			else
			{
				// Whatever was left didn't fall under anyone else's list..
			}
		}
	}
	
	private function createSDMP4($epid)
	{
		$query = "SELECT `episode`.`id`, `series`.`seriesname`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id`=$epid";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'https://videos.animeftw.tv/process-episode.php?node=add-sd-mp4&seriesName=' . $row['seriesName'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=0&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		$contents = file_get_contents($url);
		if(stristr($contents, 'SuccessSuccess') === FALSE || stristr($contents, 'Success') === FALSE)
		{
			$this->IssueList .= $row['seriesname'] . ', episode #' . $row['epnumber'] . '`s SD MP4 did not create, error: ' . $contents . "\n";
		}
		else
		{
			// Success was reported, please let us continue.
			$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`episode` SET `html5` = 1 WHERE `id` = $epid");
		}
	}
	
	private function createEpisodeImage($epid)
	{
		$query = "SELECT `episode`.`id`, `series`.`seriesname`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id`=$epid";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'https://videos.animeftw.tv/process-episode.php?node=add-ep-image&seriesName=' . $row['seriesName'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=120&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		$contents = file_get_contents($url);
		if(stristr($contents, 'SuccessSuccess') === FALSE || stristr($contents, 'Success') === FALSE)
		{
			$this->IssueList .= $row['seriesname'] . ', episode #' . $row['epnumber'] . '`s Image did not create, error: ' . $contents . "\n";
		}
		else
		{
			// Success was reported, please let us continue.
			$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`episode` SET `image` = 1 WHERE `id` = $epid");
		}
	}
	
	private function create720pMP4($epid)
	{
		$query = "SELECT `episode`.`id`, `series`.`seriesname`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id`=$epid";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'https://videos.animeftw.tv/process-episode.php?node=add-720p-mp4&seriesName=' . $row['seriesName'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=0&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		$contents = file_get_contents($url);
		if(stristr($contents, 'SuccessSuccess') === FALSE || stristr($contents, 'Success') === FALSE)
		{
			$this->IssueList .= $row['seriesname'] . ', episode #' . $row['epnumber'] . '`s 720p MP4 did not create, error: ' . $contents . "\n";
		}
		else
		{
			// Success was reported, please let us continue.
			$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`episode` SET `hd` = 1 WHERE `id` = $epid");
		}
	}
	
	private function create1080oMP4($epid)
	{
		$query = "SELECT `episode`.`id`, `series`.`seriesname`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id`=$epid";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'https://videos.animeftw.tv/process-episode.php?node=add-1080p-mp4&seriesName=' . $row['seriesName'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=0&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		$contents = file_get_contents($url);
		if(stristr($contents, 'SuccessSuccess') === FALSE || stristr($contents, 'Success') === FALSE)
		{
			$this->IssueList .= $row['seriesname'] . ', episode #' . $row['epnumber'] . '`s 1080p MP4 did not create, error: ' . $contents . "\n";
		}
		else
		{
			// Success was reported, please let us continue.
			$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`episode` SET `hd` = 2 WHERE `id` = $epid");
		}
	}
	
	private function updateAutoEntry($entry_id)
	{
		$query = "UPDATE `" . $this->MainDB . "`.`episode_updates` SET `status` = 1, `date_completed` = " . time() . " WHERE `id` = " . $entry_id;
		$this->mysqli->query($query);
	}
	
	private function updateCronEntry()
	{
		$this->mysqli->query("INSERT INTO `" . $this->MainDB . "`.`crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->CronID . "', '" . time() . "', '" . time() . "');");
		$this->mysqli->query("UPDATE `" . $this->MainDB . "`.`crons` SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
	}
}