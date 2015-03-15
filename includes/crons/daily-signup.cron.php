<?php
/****************************************************************\
## FileName: daily-signup.cron.php									 
## Author: Brad Riemann										 
## Usage: Harvests data pertaining to daily signups for
## analytics purposes.
## Copyright 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

require_once("/home/mainaftw/public_html/includes/classes/config.class.php");

class DailySignups extends Config {
	
	private $CronID, $ScriptRuntime, $StatsDatabase, $MainDatabase;

	public function __construct()
	{
		// variable declarations
		$this->TodaysDate 		= date("mdY"); 		// Todays date in MMDDYYYY format
		$this->StatsDatabase 	= 'mainaftw_stats'; // Stats database
		$this->MainDatabase		= 'mainaftw_anime'; // Production database
		$this->CronID			= 9;				// ID of the cron in use.
		
		// Script init
		$this->initializeScript();
		$this->updateCronEntry();
		
	}
	
	private function initializeScript()
	{
		for($i=1;$i>0;$i--)
		{
			$xdaysago = $i*86400; // gives us the value for how many days ago, we subtract that against the time() value
			$today = strtotime(date("d F Y"));
			$startofday = $today-$xdaysago;
			$endofday = $startofday+86399;
			
			$query = "SELECT COUNT(ID) AS NumSignups FROM `" . $this->MainDatabase . "`.`users` WHERE `registrationDate` >= " . $startofday . " AND `registrationDate` <= " . $endofday . " ";
			$result = mysql_query($query);
			$row = mysql_fetch_assoc($result);
			mysql_query("INSERT INTO `" . $this->StatsDatabase . "`.`user_stats` (`id`, `type`, `var1`, `var2`) VALUES (NULL, '1', '" . $startofday . "', '" . $row['NumSignups'] . "')");
		}
	}
	
	private function updateCronEntry()
	{
		mysql_query("INSERT INTO `" . $this->MainDatabase . "`.`crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->CronID . "', '" . time() . "', '" . time() . "');");
		mysql_query("UPDATE `" . $this->MainDatabase . "`.`crons` SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
	}
}

$DS = new DailySignups(); // declare the class and GO!