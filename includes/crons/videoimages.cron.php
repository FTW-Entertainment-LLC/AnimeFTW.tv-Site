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

	$newTimepast = date("Y-m-d h:i:s", $timepast);
	// build the query that will tell us how many there were.
	$newSql = "SELECT count(id) as id FROM episode WHERE updated IS NOT NULL AND updated >= '{$newTimepast}' AND image = 0";
	$results = mysql_query($newSql);
	$row = mysql_fetch_array($results);
	$reportback = '';
	if($row['id'] > 0){
		$newSql = "SELECT episode.id, series.seriesname, episode.epprefix, episode.epnumber, episode.vidwidth, episode.vidheight, episode.Movie, episode.videotype, series.id, series.videoServer, series.fullSeriesName FROM episode, series WHERE series.id=episode.sid AND episode.updated IS NOT NULL AND episode.updated >= '{$newTimepast}' AND episode.image = 0";
		$query = mysql_query($newSql);
		while(list($epid,$seriesname,$epprefix,$epnumber,$vidwidth,$vidheight,$Movie,$videotype,$sid,$videoServer,$fullSeriesName) = mysql_fetch_array($query)){
			$newUrl = "http://{$videoServer}.animeftw.tv/scripts/fetch-pictures.php?seriesName={$seriesname}&seriesId={$sid}&epprefix={$epprefix}&epnumber={$epnumber}&epid={$epid}&duration=360&vidwidth={$vidwidth}&vidheight={$vidheight}&videotype={$videotype}&movie={$Movie}";

			$contents = file_get_contents($newUrl);

			$response = json_decode($contents);
			if ($response->error) {
				$reportback .= "Failed to generate images for \"{$fullSeriesName}\" ep {$epnumber}, with prefix \"{$epprefix}\". Reason: \"{$response->reason}\"";
			} else {
				// TODO: Store Sprite sheet information from $response->sprite

				mysql_query("UPDATE episode SET image = 1, html5 = 1 WHERE id = {$epid}");
			}
		}
	}

	$reportback = trim($reportback);
	if (!empty($reportback)) {
		$reports	= "Video Image Creation Errors.\n\n{$reportback}";
		$email		= new Email("support@animeftw.tv");
		$email->send("2", $reports);
	}

	// Update the logs, and then make sure the cron is reset.
	mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $CronID . "', '" . $currenttime . "', '" . time() . "');");
	mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $CronID);
	
	//require_once("../classes/config.class.php")
	//require_once("../classes/email.class.php");
//	if($reportback != '')
//	{
//		$reports = "Video Image Creation Email Errors.\n\n" . $reportback;
//		$Email = new Email('support@animeftw.tv');
//		$Email->Send('2',$reports);
//	}
?>