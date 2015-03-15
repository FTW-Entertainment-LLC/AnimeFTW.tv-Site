<?php
/****************************************************************\
## FileName: videoimages.cron.php									 
## Author: Brad Riemann										 
## Usage: Checks for episodes added in the last 15 minutes and
## runs through and initiates the image creation script.
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

#include('/home/mainaftw/public_html/includes/siteroot.php');
	require_once("/home/mainaftw/public_html/includes/classes/config.class.php");
	require_once("/home/mainaftw/public_html/includes/classes/email.class.php");


	// The object of this script is to check the last 15 minutes, 
	// see what episodes have been added and initiate the generation script.

	// vars
	$currenttime = time();
	$timepast = $currenttime-900; // 15 minutes, one second 
	$CronID = 1;

	// build the query that will tell us how many there were.
	$query = "SELECT COUNT(id) AS id FROM episode WHERE date >= '".$timepast."' AND date <= '".$currenttime."' AND image = 0";
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$reportback = '';
	if($row['id'] > 0){
		$query = "SELECT episode.id, series.seriesname, episode.epprefix, episode.epnumber, episode.vidwidth, episode.vidheight, episode.Movie, episode.videotype, series.id, series.videoServer, series.stillRelease FROM episode, series WHERE series.id=episode.sid AND (episode.date >= '".$timepast."' AND episode.date <= '".$currenttime."') AND episode.image = 0";
		$query = mysql_query($query);
		while(list($epid,$seriesname,$epprefix,$epnumber,$vidwidth,$vidheight,$Movie,$videotype,$sid,$videoServer,$stillRelease) = mysql_fetch_array($query)){
			$url = 'http://' . $videoServer . '.animeftw.tv/fetch-pictures-v2.php?node=add&remote=true&seriesName=' . $seriesname . '&epprefix=' . $epprefix . '&epnumber=' . $epnumber . '&durration=120&vidwidth=' . $vidwidth . '&vidheight=' . $vidheight . '&videotype=' . $videotype . '&movie=' . $Movie;
			$contents = file_get_contents($url);
			if(strpos($contents, 'Success') !== FALSE)
			{
			}
			else
			{
				$reportback .= $seriesname . ', episode #' . $epnumber . ' did not create with error: ' . $contents . "\n";
			}
			mysql_query("UPDATE episode SET image = 1, html5 = 1 WHERE id = $epid");
		}
	}
	else {}
	// Update the logs, and then make sure the cron is reset.
	mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $CronID . "', '" . $currenttime . "', '" . time() . "');");
	mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $CronID);
	
	//require_once("../classes/config.class.php")
	//require_once("../classes/email.class.php");
	if($reportback != '')
	{
		$reports = "Video Image Creation Email Errors.\n\n" . $reportback;
		$Email = new Email('support@animeftw.tv');
		$Email->Send('2',$reports);
	}
?>