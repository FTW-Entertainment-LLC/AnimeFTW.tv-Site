<?php
/****************************************************************\
## FileName: cron-daemon.php									 
## Author: Brad Riemann										 
## Usage: Cron Daemon for AnimeFTW.tv, details below.
## Copywrite 2008-2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

/*
# This script is designed to be the Daemon for all Cron activities on the site.
# It will query the database, and return based on multiple variables. The first being,
# that we will take the current time, in minutes and hours, and will process rows of a 
# SQL database, based on the current time. It will then loop through everything and spawn
# PHP processes in the background, that will hopefully do their job without issue.
*/

include($_SERVER['DOCUMENT_ROOT'] . "/includes/config_site.php");
include($_SERVER['DOCUMENT_ROOT'] . "/includes/newsOpenDb.php");

class cron {

	var $Hour, $Minute, $MinuteArray, $HourArray;

	public function __construct()
	{
		$this->Minute = date("i"); 					// Establish the current minute on runtime. Format is two digits.
		$this->Hour = date("G"); 					// Establish the current hour on runtime. using 24 hour format.
		$this->MinuteArray = array(0,5,10,15,30); 	//Predefined list of intervals (Minutes)..
		$this->HourArray = array(2,3,4,6,12); 		//Predefined list of intervals (Hours)..
		
		$this->CronDaemon(); //Execute the whole script
	}
	
	private function CronDaemon()
	{
		$query = "SELECT * FROM crons";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result))
		{
			$this->BuildCMD($row['id'],$row['cron_name'],$row['script_location'],$row['last_run'],$row['status'],$row['interval']);
		}
	}
	
	private function BuildCMD($id,$cron_name,$script_location,$last_run,$status,$interval)
	{	
		// We have to check the status first, if it is not zero, it could be disabled or stuck.. so we will need to alert ourselves later.
		if($status == 0)
		{
			$interval = explode(",", $interval); // We want to explode the cron interval into a array, to use later
			// Later..
			$int_minutes = $interval[0]; 	// Minutes interval
			$int_hours = $interval[1];		// Hours Interval
			$int_day = $interval[2];		// Days Interval
			$int_month = $interval[3];		// Month Interval
			$int_weekday = $interval[4];	// Weekday Interval
			
			// The first thing we check, is hour schedule.
			if($int_hours == '*' || $int_hours == $this->Hour || stristr($int_hours,"/") == TRUE)
			{
				if($int_hours == '*')
				{
					// This happens every hour, so nothing to do here.
					$HourCheck = TRUE;
				}
				else if($int_hours == $this->Hour)
				{
					// THIS is the hour that this is supposed to happen!!!
					$HourCheck = TRUE;
				}
				else if(stristr($int_hours,"/") == TRUE)
				{
					// This is a string, that indicates every X, every other hour, every 3 hours, every 6 hours, so we need to set this up as such.
					$EveryXH = substr($int_hours, 2); // Change it so that we only see the last one or two digits, remove the bad stuff.
					if($EveryXH == 2)
					{
						// Every other hour
						if(($this->Hour % $EveryXH) == 0) 
						{
							// We compare the current hour against the every 2, if it is an even hour, it will return a valid time to run the job
							$HourCheck = TRUE;
						}
						else
						{
							// No, this hour the job is not supposed to run
							$HourCheck = FALSE;
						}
					}
					else if($EveryXH == 3)
					{
						// Every 3 hours
						if(($this->Hour % $EveryXH) == 0) 
						{
							// We compare the current hour against the every 3, if it is an even hour, it will return a valid time to run the job
							$HourCheck = TRUE;
						}
						else
						{
							// No, this hour the job is not supposed to run
							$HourCheck = FALSE;
						}
					}
					else if($EveryXH == 4)
					{
						// Every 4 Hours
						if(($this->Hour % $EveryXH) == 0) 
						{
							// We compare the current hour against the every 4, if it is an even hour, it will return a valid time to run the job
							$HourCheck = TRUE;
						}
						else
						{
							// No, this hour the job is not supposed to run
							$HourCheck = FALSE;
						}
					}
					else if($EveryXH == 6)
					{
						// Every 6 Hours
						if(($this->Hour % $EveryXH) == 0) 
						{
							// We compare the current hour against the every 6, if it is an even hour, it will return a valid time to run the job
							$HourCheck = TRUE;
						}
						else
						{
							// No, this hour the job is not supposed to run
							$HourCheck = FALSE;
						}
					}
					else
					{
						// We will default to every 12 hours
						if(($this->Hour % $EveryXH) == 0) 
						{
							// We compare the current hour against the every 12, it will return a valid time to run the job
							$HourCheck = TRUE;
						}
						else
						{
							// No, this hour the job is not supposed to run
							$HourCheck = FALSE;
						}
					}
				}
				else
				{
					$HourCheck = FALSE;
				}
			}
			else
			{
				$HourCheck = FALSE;
			}
			
			#-----
			# Now we check the Minutes
			#-----
					
			if($int_minutes == '*' || $int_minutes == $this->Minute || stristr($int_minutes,"/") == TRUE)
			{	
				if($int_minutes == '*')
				{
					// This is set for every minute, we shouldn't be running this script that much, but here goes..
					$MinuteCheck = TRUE;
				}
				else if($int_minutes == $this->Minute)
				{
					// We have chosen for this script to run for a single minute in an hour window.. THIS IS THAT MINUTE!!!
					$MinuteCheck = TRUE;
				}
				else if(stristr($int_minutes,"/") == TRUE)
				{
					// This is a string, that indicates every X, every other hour, every 3 hours, every 6 hours, so we need to set this up as such.
					$EveryXM = substr($int_minutes, 2); // Change it so that we only see the last one or two digits, remove the bad stuff.
					if($EveryXM == 2)
					{
						// Every 2 Minutes
						if(($this->Minute % $EveryXM) == 0) 
						{
							// If the current minute is divisable by 2, it shouldn't have any remainders and thus time to be used!
							$MinuteCheck = TRUE;
						}
						else
						{
							// This is not the minute you were destined to run...
							$MinuteCheck = FALSE;
						}
					}
					else if($EveryXM == 5)
					{
						// Every 5 Minutes
						if(($this->Minute % $EveryXM) == 0) 
						{
							// If the current minute is divisable by 5, it shouldn't have any remainders and thus time to be used!
							$MinuteCheck = TRUE;
						}
						else
						{
							// This is not the minute you were destined to run...
							$MinuteCheck = FALSE;
						}
					}
					else if($EveryXM == 10)
					{
						// Every 10 Minutes
						if(($this->Minute % $EveryXM) == 0) 
						{
							// If the current minute is divisable by 10, it shouldn't have any remainders and thus time to be used!
							$MinuteCheck = TRUE;
						}
						else
						{
							// This is not the minute you were destined to run...
							$MinuteCheck = FALSE;
						}
					}
					else if($EveryXM == 15)
					{
						// Every 15 Minutes
						if(($this->Minute % $EveryXM) == 0) 
						{
							// If the current minute is divisable by 15, it shouldn't have any remainders and thus time to be used!
							$MinuteCheck = TRUE;
						}
						else
						{
							// This is not the minute you were destined to run...
							$MinuteCheck = FALSE;
						}
					}
					else
					{
						// Default will be every 30 minutes
						if(($this->Minute % $EveryXM) == 0) 
						{
							// If the current minute is divisable by 30, it shouldn't have any remainders and thus time to be used!
							$MinuteCheck = TRUE;
						}
						else
						{
							// This is not the minute you were destined to run...
							$MinuteCheck = FALSE;
						}
					}
				}
				else
				{
					// We dont know how but it got past one of my checks.. And then failed..
					$MinuteCheck = FALSE;
				}
			}
			else
			{
				// Someone failed at setting this Cron up.. there should always be a minute!
				$MinuteCheck = FALSE;
			}
			
			if($HourCheck != FALSE && $MinuteCheck != FALSE)
			{
				// We pass all our initial checks.. we will build more later, but as it is, you may proceed with the script!
					
				// The first thing we do, is tell the database, that we have initiated this script, so down the road, if it is still running, we cannot start it again.
				$result = mysql_query("UPDATE crons SET status = '1', last_run = '" . time() . "' WHERE id = " . $id);
				if(!result)
				{
					// there was an issue with the update, let them know
					return "While attempting to update cron id " . $id . " there was an issue: " . mysql_error();
					unset($result);
				}
				else
				{
					// we've updated the origin row, but we need to add logs for keeping track of things.
					$result = mysql_query("INSERT INTO crons_log (id, cron_id, start_time, end_time) VALUES (NULL, '" . $id . "', '" . time() . "', '0')");
					if(!$result)
					{
						return "Failure to write to the Crons_log table for cron id " . $id . " there was an issue: " . mysql_error();						
						unset($result);
					}
					else
					{
						$ShellCMD = exec("php5 -f " . $script_location);
						return 'Job-' . $cron_name . ', Run Successfully.';
					}
				}
				
			}
			else
			{
				// There is nothing to do with this job, so we won't even tell anyone it happened..
			}
		}
		else
		{
			// if the status is 1, then it was still running and never shut down correctly, make sure to have it as part of the email. 
			if($status == 1)
			{
				return 'Job-' . $cron_name . ' with id of ' . $id . ', was supposed to run, but was set as still being running, please check.';
			}
			// Job is disabled, so we let them know.
			else if($status == 2)
			{
				return 'Job-' . $cron_name . ' with id of ' . $id . ', was disabled, and did not run.';				
			}
			
		}
	}
}

$Cron = new cron();
?>