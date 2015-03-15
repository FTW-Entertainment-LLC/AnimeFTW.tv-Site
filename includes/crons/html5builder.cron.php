<?php
/****************************************************************\
## FileName: html5builder.cron.php									 
## Author: Brad Riemann										 
## Usage: A cron script that is called every 15 minutes, will 
## cycle through 100 mkv based videos and convert them to mp4
## Copyright 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

//videos.animeftw.tv/convert-to-html5.php?remote=md5(date("dmY"))&seriesName=trinityblood&epprefix=trinityblood&epnumber=1

require_once("/home/mainaftw/public_html/includes/classes/config.class.php");
require_once("/home/mainaftw/public_html/includes/classes/email.class.php");

$CronID = 10;
$currenttime = time();
$today = md5(date("dmY"));

$query = "SELECT `id`, `seriesname`, `epprefix`, `epnumber` FROM `episode` WHERE `html5` = 0 AND `videotype` = 'mkv' LIMIT 0, 50";
$result = mysql_query($query);

$count = mysql_num_rows($result);

if($count == 0)
{
	// if we receive zero rows, this job is done, should send an email as well, but who cares...
	mysql_query("UPDATE `crons` SET `status` = 2 WHERE `id` = $CronID");
}
else
{
	$reportback = '';
	while($row = mysql_fetch_assoc($result))
	{
		$url = 'http://videos.animeftw.tv/convert-to-html5.php?remote=0c4c92f8ed2c06f44b39f14cd94e27e&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'];
		$contents = file_get_contents($url);
		if(strtolower($contents) != 'success')
		{
			$reportback .= $row['seriesname'] . ', episode #' . $row['epnumber'] . ' did not create with error: ' . $contents . "\n";
			mysql_query("UPDATE `episode` SET `html5` = 1 WHERE `id` = " . $row['id']);
		}
		else
		{
			mysql_query("UPDATE `episode` SET `html5` = 1 WHERE `id` = " . $row['id']);
		}
	}
	
	//mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $CronID . "', '" . $currenttime . "', '" . time() . "');");
	//mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $CronID);
	
	if($reportback != '')
	{
		$reports = "MKV to HTML5 video conversion Cron Job.\n\n<br /><br />" . $reportback;
		$Email = new Email('robotman321@animeftw.tv');
		//$Email->Send('7',$reports);
	}
}