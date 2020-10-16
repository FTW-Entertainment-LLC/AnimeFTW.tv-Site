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
	$results = mysqli_query($conn, $query);
	$row = mysqli_fetch_row($results);
	$current = $row[0];

	// build the query that will tell us how many there were.
	$query = "SELECT episode.id, episode.sid FROM episode WHERE episode.image = 1 LIMIT ".$current.", ".$count."";
	$query = mysqli_query($conn, $query);
	while(list($epid,$sid) = mysqli_fetch_array($query)){
		$url = "http://cdn.animeftw.tv/site-images/video-images/{$sid}/{$epid}_screen.jpeg";
		if(@getimagesize($url)){
		}
		else {
			mysqli_query($conn, "UPDATE episode SET image = 0 WHERE id = ".$epid);
		}
	}
	mysqli_query($conn, "UPDATE settings SET value = ".($current+$count)." WHERE id = $settingid");

?>