<?php

include('../siteroot.php');

$query = "SELECT episode_tracker.id, episode_tracker.eid, episode.sid FROM episode_tracker, episode WHERE episode_tracker.seriesName = '' AND episode.id=episode_tracker.eid";
$result = mysql_query($query) or die('Error : ' . mysql_error());

while(list($id,$epid,$sid) = mysql_fetch_array($result))
{
	$update = "UPDATE episode_tracker SET seriesName = '".$sid."' WHERE id = '".$id."'";
	mysql_query($update);
	//echo $update."\n";
}
echo "Done";
?>