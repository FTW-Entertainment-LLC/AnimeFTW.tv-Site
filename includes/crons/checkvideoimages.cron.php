<?php
/****************************************************************\
## FileName: checkvideoimages.cron.php									 
## Author: Brad Riemann										 
## Usage: Bulk job to address all videos that have no episode 
## images
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

include('/home/mainaftw/public_html/includes/siteroot.php');
	
	//vars
	$count = 100;
	$settingid = 11;
	
	$query = "SELECT value FROM settings WHERE id = ".$settingid;
	$results = mysql_query($query);
	$row = mysql_fetch_row($results);
	$current = $row[0];

	// build the query that will tell us how many there were.
	$query = "SELECT episode.id, episode.epprefix, episode.epnumber FROM episode WHERE episode.image = 1 LIMIT ".$current.", ".$count."";
	$query = mysql_query($query);
	while(list($epid,$epprefix,$epnumber) = mysql_fetch_array($query)){
		$url = 'http://cdn.animeftw.tv/site-images/video-images/'.$epprefix.'_'.$epnumber.'_screen.jpeg';
		if(@getimagesize($url)){
		}
		else {
			mysql_query("UPDATE episode SET image = 0 WHERE id = ".$epid);
		}
	}
	mysql_query("UPDATE settings SET value = ".($current+$count)." WHERE id = $settingid");

?>