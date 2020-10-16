<?php
/****************************************************************\
## FileName: toplist-harvest.cron.php									 
## Author: Brad Riemann										 
## Usage: Exports the data from the topseries cron
## and puts it in the stats database
## Copyright 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

require_once("/home/mainaftw/public_html/includes/classes/config.class.php");

Class ToplistHarvest extends Config {
	
	private $TodaysDate, $StatsDatabase, $MainDatabase, $CronID;
	
	public function __construct()
	{
		// variable declarations
		$this->TodaysDate 		= strtotime(date("d F Y",time())); 		// Todays date in MMDDYYYY format
		$this->StatsDatabase 	= 'mainaftw_stats'; // Stats database
		$this->MainDatabase		= 'mainaftw_anime'; // Production database
		$this->CronID			= 6;				// ID of this cron
		
		// SCript init
		$this->initializeScript();
		$this->updateCronEntry();
	}
	
	private function initializeScript()
	{
		// First thing we do, this will be running after the nightly cron job, so we need to cycle through the topseriescalc after it has run.
		$query = "SELECT `seriesId`, `countedPages` FROM `" . $this->MainDatabase . "`.`topseriescalc` ORDER BY `seriesId`";
		$results = mysqli_query($conn, $query);
		
		if(!$results)
		{
			echo 'There was an issue with the query, please try again.';
			exit;
		}
		else
		{
			// We need to loop through all of the data that is available, and push it into the stats database, please!
			$i = 0; // we want to know how many rows were inserted.
			$totalviews = 0; // Total amount of episodes viewed.
			while($row = mysqli_fetch_assoc($results))
			{
				mysqli_query($conn, "INSERT INTO `" . $this->StatsDatabase . "`.`series_stats` (`id`, `date`, `series_id`, `views`, `type`) VALUES (NULL, '" . $this->TodaysDate . "', '" . $row['seriesId'] . "', '" . $row['countedPages'] . "', 0);");
				$totalviews+$row['countedPages'];
				$i++;
			}
			echo $i . ' rows added to the Series Stats table.';
		}
		
		// we record the total episodes viewed for the day, of ALL series.
		mysqli_query($conn, "INSERT INTO `" . $this->StatsDatabase . "`.`series_stats` (`id`, `date`, `series_id`, `views`, `type`) VALUES (NULL, '" . $this->TodaysDate . "', '0', '" . $totalviews . "',1);");
	}
	
	private function updateCronEntry()
	{
		mysqli_query($conn, "INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->CronID . "', '" . time() . "', '" . time() . "');");
		mysqli_query($conn, "UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
	}
}

$TH = new ToplistHarvest(); // declare the class and GO!