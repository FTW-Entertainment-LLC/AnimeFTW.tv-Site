<?php
/****************************************************************\
## FileName: cron.php
## Author: Brad Riemann
## Usage: Parses Database objects for execuable scripts.
## Copywrite 2013 Techpro, Inc, All Rights Reserved
\****************************************************************/

include("/home/mainaftw/public_html/includes/classes/config.class.php"); 

class cron extends Config {

	var $Hour, $Minute, $Day, $Month, $Weekday, $FailedJobs = array();

	public function __construct()
	{

		$this->Minute = date("i"); 					// Establish the current minute on runtime. Format is two digits.
		$this->Hour = date("G"); 					// Establish the current hour on runtime. using 24 hour format.
		$this->Day = date("d");						// Establish the current day in 2 digit format.
		$this->Month = date("m");					// Establish the current month in 2 digit format.
		$this->Weekday = date("w");					// Establish the current Weekday in single digit format. 0-6.
		$this->AllCrons = TRUE;						// True for all Crons, even ones disabled or running. False for only Enbled Crons
		$this->CronCount = 0;						// we want to globally show how many crons run, so we need to use this.
		
		$this->CronDaemon(); 						//Execute the whole script
	}
	
	private function CronDaemon()
	{
		$query = "SELECT * FROM crons";
		// check to see if we need to limit things..
		if($this->AllCrons == FALSE)
		{
			$query .= " WHERE status = 0";
		}
		$result = mysql_query($query);
		$numrows = mysql_num_rows($result);
		if($numrows < 1)
		{
			echo "No Crons to execute.\n";
			exit;
		}
		else
		{
			while($row = mysql_fetch_array($result))
			{
				echo $this->BuildCMD($row['id'],$row['cron_name'],$row['script_location'],$row['last_run'],$row['status'],$row['interval'],$row['notified']);
			}
			$this->processFailedJobs(); // Will process failed Jobs, and send them to the administrator of the server
			echo $this->CronCount . " crons processed.\n";
		}
	}
	
	private function BuildCMD($id,$cron_name,$script_location,$last_run,$status,$interval,$notified)
	{	
		// We have to check the status first, if it is not zero, it could be disabled or stuck.. so we will need to alert ourselves later.
		if($status == 0)
		{
			$interval = explode(" ", $interval); // We want to explode the cron interval into a array, to use later, we will use a space to check things
			// Later..
			$int_minutes = $interval[0]; 	// Minutes interval
			$int_hours = $interval[1];		// Hours Interval
			$int_day = $interval[2];		// Days Interval
			$int_month = $interval[3];		// Month Interval
			$int_weekday = $interval[4];	// Weekday Interval
			
			// The first thing we check, is hour schedule.
			if($int_hours == '*' || $int_hours == $this->Hour || (stristr($int_hours,"/") == TRUE || stristr($int_hours,",") == TRUE ))
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
					$TwoHours = explode(",",$int_hours); // Put them in an array so we can use later.
					if(in_array($this->Hour,$TwoHours)) // Later...
					{
						$HourCheck = TRUE; //The array found the date.. so it needs to run TODAY.
					}
					else
					{
						$HourCheck = FALSE;
					}
				}
			}
			else
			{
				$HourCheck = FALSE;
			}
			
			#-----
			# Now we check the Minutes
			#-----
					
			if($int_minutes == '*' || $int_minutes == $this->Minute || (stristr($int_minutes,"/") == TRUE || stristr($int_minutes,",") == TRUE))
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
			
			#-----
			# Now we check the Days
			#-----
					
			if($int_day == '*' || $int_day == $this->Day || (stristr($int_day,"/") == TRUE || stristr($int_day,",") == TRUE))
			{
				if($int_day == '*')
				{
					// This is set for every Day, let them pass.
					$DaysCheck = TRUE;
				}
				else if($int_day == $this->Day)
				{
					// We have chosen for this script to run for a single day in a month, this is that Day!
					$DaysCheck = TRUE;
				}
				else if(stristr($int_day,"/") == TRUE)
				{
					// This is a string, that indicates every X, every other day, every third day.
					$EveryXD = substr($int_day, 2); // Change it so that we only see the last one or two digits, remove the bad stuff.
					$LastDay = date("d",$last_run); // We need to know the last day for the every X clause.
					// Every other day
					if($EveryXD == 2 && $LastDay <= ($this->Day-2))
					{
						// If the current day is every other, then it will be greater than one the last time it was run
						$DaysCheck = TRUE;
					}
					else if($EveryXD == 3 && $LastDay <= ($this->Day-3))
					{
						// If the current day is every three days, then this will be active.
						$DaysCheck = TRUE;
					}
					else //We really dont know the request, so we are going to have to make it fail
					{
						$DaysCheck = FALSE;
					}
				}
				else
				{
					// the last possible Day event, is for a day combination. The format is two days separated by a comma.
					$TwoDays = explode(",",$int_day); // Put them in an array so we can use later.
					if(in_array($this->Day,$TwoDays)) // Later...
					{
						$DaysCheck = TRUE; //The array found the date.. so it needs to run TODAY.
					}
					else
					{
						$DaysCheck = FALSE;
					}
				}
			}
			else
			{
				// Someone failed at setting this Cron up.. there should always be a Day!
				$DaysCheck = FALSE;
			}
			
			#-----
			# Now we check the Month
			#-----
					
			if($int_month == '*' || $int_month == $this->Month || (stristr($int_month,"/") == TRUE || stristr($int_month,",") == TRUE))
			{
				if($int_month == '*')
				{
					// This is set for every Month, they can pass.
					$MonthCheck = TRUE;
				}
				else if($int_month == $this->Month)
				{
					// We have chosen for this script to run for a single month of the year.. this is that month..
					$MonthCheck = TRUE;
				}
				else if(stristr($int_month,"/") == TRUE)
				{
					// This is a string, that indicates every X, every other month, every third month (Quarter)
					$EveryXM = substr($int_month, 2); // Change it so that we only see the last one or two digits, remove the bad stuff.
					$LastMonth = date("m",$last_run); // We need to know the last day for the every X clause.
					// Every other Month
					if($EveryXM == 2 && $LastDay <= ($this->Month-2))
					{
						// If the current day is every other, then it will be greater than one the last time it was run
						$MonthCheck = TRUE;
					}
					else if($EveryXM == 4 && $LastMonth <= ($this->Month-3))
					{
						// If the current month is the every quarter
						$MonthCheck = TRUE;
					}
					else //We really dont know the request, so we are going to have to make it fail
					{
						$MonthCheck = FALSE;
					}
				}
				else
				{
					// the last possible Day event, is for a day combination. The format is two days separated by a comma.
					$TwoMonths = explode(",",$int_day); // Put them in an array so we can use later.
					if(in_array($this->Month,$TwoMonths)) // Later...
					{
						$MonthCheck = TRUE; //The array found the date.. so it needs to run TODAY.
					}
					else
					{
						$MonthCheck = FALSE;
					}
				}
			}
			else
			{
				// Someone failed at setting this Cron up.. there should always be a Month!
				$MonthCheck = FALSE;
			}
			
			#-----
			# Now we check the Weekday
			#-----
					
			if($int_weekday == '*' || $int_weekday == $this->Weekday || (stristr($int_weekday,"-") == TRUE || stristr($int_weekday,",") == TRUE))
			{
				if($int_weekday == '*')
				{
					// This is set for every Month, they can pass.
					$WeekdayCheck = TRUE;
				}
				else if($int_weekday == $this->Weekday)
				{
					// We have chosen for this script to run for a single month of the year.. this is that month..
					$WeekdayCheck = TRUE;
				}
				else if(stristr($int_weekday,"-") == TRUE)
				{
					// This check will see if a day is inbetween something.. so like 1-4, is it inbetween
					$Between = explode("-",$int_weekday);
					if($this->Weekday >= $Between[0] && $this->Weekday <= $Between[1])
					{
						// We checked if the current weekday is in the request.
						$WeekdayCheck = TRUE;
					}
					else
					{
						$WeekdayCheck = FALSE;
					}
				}
				else
				{
					// The last possible solution is having multiple days checked, the will be in comma dillimited format..
					$ListedWeekday = explode(",",$int_weekday);
					if(in_array($this->Weekday,$ListedWeekday))
					{
						// The current weekday is in the array we got, so we can let them pass.
						$WeekdayCheck = TRUE;
					}
					else
					{
						$WeekdayCheck = FALSE;
					}
				}
			}
			else
			{
				// Someone failed at setting this Cron up.. there should always be a minute!
				$WeekdayCheck = FALSE;
			}
			
			if($HourCheck != FALSE && $MinuteCheck != FALSE && $DaysCheck != FALSE && $MonthCheck != FALSE && $WeekdayCheck != FALSE)
			{
				// We pass all our initial checks.. we will build more later, but as it is, you may proceed with the script!
					
				// The first thing we do, is tell the database, that we have initiated this script, so down the road, if it is still running, we cannot start it again.
				$result = mysql_query("UPDATE crons SET status = '1', last_run = '" . time() . "' WHERE id = " . $id);
				if(!$result)
				{
					// there was an issue with the update, let them know
					return "While attempting to update cron id " . $id . " there was an issue: " . mysql_error() . "<br />\n";
					unset($result);
				}
				else
				{
					// we've updated the origin row, but we need to add logs for keeping track of things.
					//$result = mysql_query("INSERT INTO crons_log (id, cron_id, start_time, end_time) VALUES (NULL, '" . $id . "', '" . time() . "', '0')");
					if(!$result)
					{
						return "Failure to write to the Crons_log table for cron id " . $id . " there was an issue: " . mysql_error() . "<br />\n";						
						unset($result);
					}
					else
					{
						$ShellCMD = exec("php5 -f " . $script_location);
						return 'Job- ' . $cron_name . ", Run Successfully.<br />\n";
					}
					$this->CronCount++;
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
				// Notification bit. This will enable the email to administrators when a script has failed.
				// ADDED: 11/7 - Robotman321
				if($notified == 0)
				{
					$this->FailedJobs[] = $id . ',' . $cron_name . ',' . $last_run;
				}
				else
				{
					// The job has already been sent to the admin letting them know that it was failed.
					return 'Job-' . $cron_name . ' with id of ' . $id . ", was supposed to run, but was set as still being running, please check.\n";
				}
			}
			// Job is disabled, so we let them know.
			else if($status == 2)
			{
				return 'Job-' . $cron_name . ' with id of ' . $id . ", was disabled, and did not run.<br />\n";				
			}
			
		}
	}
	
	// name: processFailedJobs
	// purpose: used to notify the admin that there was a failed job for the cron.
	// ADDED: 11/7 - Robotman321
	
	private function processFailedJobs()
	{
		// first thing first, check to see if there are any failed crons
		if(!empty($this->FailedJobs))
		{
			$body = '';
			
			// we need to format the failedjobs listing first.
			foreach($this->FailedJobs AS $jobs)
			{
				// we explode the Job data, to see what it gives us.
				$Jobs = explode(',',$jobs);
				$body .= 'The Cron with the id of ' . $Jobs[0] . ' named ' . $Jobs[1] . ' failed to complete in a timely manner, on ' . date('l jS \of F Y h:i:s A',$Jobs[2]) . '.
				';
				// Let's make sure we record the update so that we don't spam the admin for stupid things.
				$results = mysql_query("UPDATE `crons` SET `notified` = 1 WHERE `id` = " . $Jobs[0]);
			}
			
			// now loop through the admins.. and make sure to send emails to them.
			foreach($AdminEmails as $Emails)
			{
				ini_set('sendmail_from', 'no-reply@animeftw.tv');
				$headers = 'From: AnimeFTW.TV Cron Manager <support@animeftw.tv>' . "\r\n" .
					'Reply-To: AnimeFTW.TV Cron Manager <support@animeftw.tv>' . "\r\n" .
					'Content-type: text/html; charset="iso-8859-1"' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
				mail('support@animeftw.tv', 'Failed Cron Jobs ', $body, $headers);
			}
		}
		else
		{
			// There are no failed Crons, we can let the system carry on it's merry way.
		}
	}
}

// initialize the cron (does everything for you)
$cron = new cron(); // the system defaults to 
