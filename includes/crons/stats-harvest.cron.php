<?php
/****************************************************************\
## FileName: toplist-harvest.cron.php									 
## Author: Brad Riemann										 
## Usage: Exports the data from the topseries cron
## and puts it in the stats database
## Copyright 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

require_once("/home/mainaftw/public_html/includes/classes/config.class.php");

class StatsHarvest extends Config {
	
	private $CronID, $ScriptRuntime, $StatsDatabase, $MainDatabase;

	public function __construct()
	{
		// variable declarations
		$this->TodaysDate 		= date("mdY"); 		// Todays date in MMDDYYYY format
		$this->StatsDatabase 	= 'mainaftw_stats'; // Stats database
		$this->MainDatabase		= 'mainaftw_anime'; // Production database
		$this->CronID			= 7;				// ID of the cron in use.
		
		// SCript init
		$this->initializeScript();
		$this->updateCronEntry();
		
	}
	
	private function initializeScript()
	{
		// The object of this script is to update all of the stats on the site, we want to make sure its not resource intensive so we are making it
		
		$this->usersOnlineLast24Hours();	// amount of users online in the past 24 hours
		//$this->totalComments();				// total comments to this point
		$this->totalSeries(); 				// total series present on the site
		$this->episodeStats(); 				// put total minutes, hours and video size in here
		$this->totalStatusUpdates();		// total status updates
		$this->totalMyWatchListEntries();	// tottal MyWatchlist Entries
		$this->totalMaleUsers();			// Total users marked as Male
		$this->totalFemaleUsers();			// Total users marked as Female
		$this->totalAvatarsSet();			// Total Avatars Active
		$this->totalAvatarsGif();			// Total Avatars that are Gif images
		$this->totalAvatarsJpg();			// Total Avatars that are JPegs
		$this->totalAvatarsPNG();			// Total Avatars that are PNG images
		$this->totalUsers();				// Total users on the site (Active)
		//$this->totalProfileComments();		// Total Profile Comments
		$this->totalTrackerRows();			// Total tracker rows
	}
	
	private function usersOnlineLast24Hours()
	{
		$twentyfourhorusago = time()-86400;
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `lastActivity` >= " . $twentyfourhorusago . ") WHERE `id` = 1");
		if(!$results)
		{
			echo 'An error was found when trying to run the update last online user count: ' . mysql_error();
			exit;
		}
	}
	
	private function totalComments()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(id) AS NumRows FROM `" . $this->MainDatabase . "`.`page_comments` WHERE `page_id` = 0) WHERE `id` = 2");
		if(!$results)
		{
			echo 'An error was found when trying to run the total comments update: ' . mysql_error();
			exit;
		}
	}
	
	private function totalSeries()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(id) FROM `" . $this->MainDatabase . "`.`series`) WHERE `id` = 3");
		if(!$results)
		{
			echo 'An error was found when trying to run the total series update: ' . mysql_error();
			exit;
		}
	}
	
	private function episodeStats()
	{
		// first thing we need to do, is get the count of total episodes on the site.
		$query = "SELECT COUNT(id) AS total FROM `" . $this->MainDatabase . "`.`episode`";
		$results = mysql_query($query);
		if(!$results)
		{
			echo 'An error was found when trying to get the total episodes: ' . mysql_error();
			exit;
		}
		
		$row = mysql_fetch_array($results);		
		
		// first thing first, update the total number of episodes.
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = " . $row['total'] . " WHERE `id` = 4");
		if(!$results)
		{
			echo 'An error was found when trying to run the episode stats, videos: ' . mysql_error();
			exit;
		}
		
		// second, how many minutes of video is this.
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = " . ($row['total']*30) . " WHERE `id` = 5");
		if(!$results)
		{
			echo 'An error was found when trying to run the total minutes of videos update: ' . mysql_error();
			exit;
		}
		
		// third, how many hours of video are these.
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = " . (($row['total']*30)/60) . " WHERE `id` = 6");
		if(!$results)
		{
			echo 'An error was found when trying to run the total hours of videos update: ' . mysql_error();
			exit;
		}
		
		// fourth, how BIG is the sum total of the videos
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = " . ($row['total']*130) . " WHERE `id` = 7");
		if(!$results)
		{
			echo 'An error was found when trying to run the total size of videos update: ' . mysql_error();
			exit;
		}
		
	}
	
	private function totalStatusUpdates()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(id) FROM `" . $this->MainDatabase . "`.`status`) WHERE `id` = 8");
		if(!$results)
		{
			echo 'An error was found when trying to run the total status updates: ' . mysql_error();
			exit;
		}
	}
	
	private function totalMyWatchListEntries()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(id) FROM `" . $this->MainDatabase . "`.`watchlist`) WHERE `id` = 9");
		if(!$results)
		{
			echo 'An error was found when trying to run the total mywatchlist entries: ' . mysql_error();
			exit;
		}
	}
	
	private function totalMaleUsers()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `gender` = 'male') WHERE `id` = 10");
		if(!$results)
		{
			echo 'An error was found when trying to run the total male users: ' . mysql_error();
			exit;
		}
	}
	
	private function totalFemaleUsers()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `gender` = 'female') WHERE `id` = 11");
		if(!$results)
		{
			echo 'An error was found when trying to run the total female users: ' . mysql_error();
			exit;
		}
	}
	
	private function totalAvatarsSet()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `avatarActivate` = 'yes') WHERE `id` = 12");
		if(!$results)
		{
			echo 'An error was found when trying to run the total avatars set: ' . mysql_error();
			exit;
		}
	}
	
	private function totalAvatarsGif()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `avatarActivate` = 'yes' AND `avatarExtension` = 'gif') WHERE `id` = 13");
		if(!$results)
		{
			echo 'An error was found when trying to run the total avatars gif: ' . mysql_error();
			exit;
		}
	}
	
	private function totalAvatarsJpg()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `avatarActivate` = 'yes' AND `avatarExtension` = 'jpg') WHERE `id` = 14");
		if(!$results)
		{
			echo 'An error was found when trying to run the total avatars jpg: ' . mysql_error();
			exit;
		}
	}
	
	private function totalAvatarsPNG()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `avatarActivate` = 'yes' AND `avatarExtension` = 'png') WHERE `id` = 15");
		if(!$results)
		{
			echo 'An error was found when trying to run the total avatars png: ' . mysql_error();
			exit;
		}
	}
	
	private function totalUsers()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(ID) FROM `" . $this->MainDatabase . "`.`users` WHERE `active` = 1) WHERE `id` = 16");
		if(!$results)
		{
			echo 'An error was found when trying to run the total users: ' . mysql_error();
			exit;
		}
	}
	
	private function totalProfileComments()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(id) AS NumRows FROM `" . $this->MainDatabase . "`.`page_comments` WHERE `page_id` != 0) WHERE `id` = 17");
		if(!$results)
		{
			echo 'An error was found when trying to run the total profile comments: ' . mysql_error();
			exit;
		}
	}
	
	private function totalTrackerRows()
	{
		$results = mysql_query("UPDATE `" . $this->MainDatabase . "`.`stats` SET `content` = (SELECT COUNT(id) FROM `" . $this->MainDatabase . "`.`episode_tracker`) WHERE `id` = 18");
		if(!$results)
		{
			echo 'An error was found when trying to run the total tracker rows: ' . mysql_error();
			exit;
		}
	}
	
	private function updateCronEntry()
	{
		mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->CronID . "', '" . time() . "', '" . time() . "');");
		mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
	}
}

$SH = new StatsHarvest(); // declare the class and GO!