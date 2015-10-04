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
	$sql = "SELECT count(id) as id FROM `episode` WHERE `updated` IS NOT NULL AND `updated` >= '{$timepast}' AND (`episode`.`image` = 0 OR `episode`.`spriteId` IS NULL)";
	$results = mysql_query($sql);
	$row = mysql_fetch_array($results);
	$reportback = '';
	if($row['id'] > 0){
		$sql = "SELECT `episode`.`id` as `epid`, `episode`.`spriteId`, `series`.`seriesName`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`sid`, `series`.`fullSeriesName` FROM `episode`, `series` WHERE `episode`.`sid`=`series`.`id` AND ((`episode`.`date` != 0 AND `episode`.`date` >= '{$timepast}') OR (`episode`.`updated` IS NOT NULL AND `episode`.`updated` >= '{$timepast}')) AND (`episode`.`image` = 0 OR `episode`.`spriteId` IS NULL)";
		$query = mysql_query($sql);
		while(list($epid,$spriteId,$seriesname,$epprefix,$epnumber,$vidwidth,$vidheight,$Movie,$videotype,$image,$sid,$fullSeriesName) = mysql_fetch_array($query)){
			$newUrl = "http://videos.animeftw.tv/scripts/fetch-pictures.php?seriesName={$seriesname}&seriesId={$sid}&epprefix={$epprefix}&epnumber={$epnumber}&epid={$epid}&duration=360&vidwidth={$vidwidth}&vidheight={$vidheight}&videotype={$videotype}&movie={$Movie}";

			if ($image !== 0 && $spriteId !== null) {
				if ($image === 0) {
					$newUrl .= "&mode=thumbnail";
				}
				if ($spriteId === null) {
					$newUrl .= "&mode=sprite";
				}
			}

			$contents = file_get_contents($newUrl);
			$response = json_decode($contents);
			
			if ($response->error) {
				$reportback .= "Failed to generate image(s) for \"{$fullSeriesName}\" for \"{$seriesname}/{$epprefix}_{$epnumber}_ns.mp4\". Reason: \"{$response->reason}\"";
			} else {
				// Construct query based on what's required, as to not added bad data.
				$updateQuery = "UPDATE `episode` SET `updated` '" . time() . "'";

				if ($spriteId === null) {
					$spriteQuery = "INSERT INTO `sprites` (`width`, `height`, `totalWidth`, `rate`, `count`, `created`) VALUES ({$response->sprite->width}, {$response->sprite->height}, {$response->sprite->totalWidth}, {$response->sprite->rate}, {$response->sprite->count}, " . time() . ")";

					mysql_query($spriteQuery);
					$spriteId = mysql_insert_id();

					if ($spriteId) {
						$updateQuery .= ", spriteId = '{$spriteId}";
					} // else { // assume failure to add sprite, do niothing. }
				}

				if ($image === 0) {
					$updateQuery .= ", image = 1";
				}

				mysql_query("{$updateQuery} WHERE id = {$epid}");
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
	mysql_query("INSERT INTO `crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $CronID . "', '" . $currenttime . "', '" . time() . "');");
	mysql_query("UPDATE `crons` SET `last_run` = '" . time() . "', `status` = 0 WHERE `id` = " . $CronID);
	
	//require_once("../classes/config.class.php")
	//require_once("../classes/email.class.php");
//	if($reportback != '')
//	{
//		$reports = "Video Image Creation Email Errors.\n\n" . $reportback;
//		$Email = new Email('support@animeftw.tv');
//		$Email->Send('2',$reports);
//	}
?>