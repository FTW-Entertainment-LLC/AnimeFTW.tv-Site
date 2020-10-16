<?php
/****************************************************************\
## FileName: createvideoimages.cron.php									 
## Author: Brad Riemann										 
## Usage: Bulk job to address all videos that have no episode 
## images, it will take time to update as
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

include('/home/mainaftw/public_html/includes/siteroot.php');

	// build the query that will tell us how many there were.
	$query = "SELECT COUNT(id) AS id FROM episode WHERE image = 0 LIMIT 0, 20";
	$results = mysqli_query($query);
	$row = mysqli_fetch_array($results);
	if($row['id'] > 0){
		$query = "SELECT episode.id, episode.seriesname, episode.epprefix, episode.epnumber, episode.vidwidth, episode.vidheight, episode.videotype, series.id, series.videoServer, series.stillRelease FROM episode, series WHERE series.seriesName=episode.seriesName AND episode.image = 0 LIMIT 0, 20";
		$query = mysqli_query($query);
		while(list($epid,$seriesname,$epprefix,$epnumber,$vidwidth,$vidheight,$videotype,$sid,$videoServer,$stillRelease) = mysqli_fetch_array($query)){
			$url = 'http://' . $videoServer . '.animeftw.tv/fetch-pictures-v2.php?node=add&remote=true&seriesName=' . $seriesname . '&epprefix=' . $epprefix . '&epnumber=' . $epnumber . '&durration=360&vidwidth=' . $vidwidth . '&vidheight=' . $vidheight . '&videotype=' . $videotype;
			echo file_get_contents($url);
			mysqli_query("UPDATE episode SET image = 1 WHERE id = $epid");
		}
	}
	else {}
?>