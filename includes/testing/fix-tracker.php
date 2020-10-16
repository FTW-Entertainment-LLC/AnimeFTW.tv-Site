<?php

include('../siteroot.php');

$query = "SELECT episode_tracker.id, episode_tracker.eid, episode.sid FROM episode_tracker, episode WHERE episode_tracker.seriesName = '' AND episode.id=episode_tracker.eid";
$result = mysqli_query($query) or die('Error : ' . mysqli_error());

while(list($id,$epid,$sid) = mysqli_fetch_array($result))
{
	$update = "UPDATE episode_tracker SET seriesName = '".$sid."' WHERE id = '".$id."'";
	mysqli_query($update);
	//echo $update."\n";
}
echo "Done";
?>