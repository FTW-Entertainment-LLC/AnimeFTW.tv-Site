<?php
include 'config.php';
include 'newsOpenDb.php';
if(!isset($_GET['start'])){
	echo 'Start spot is not set..';
}
else {
	$start = $_GET['start'];
	$query = "SELECT epid, uid, rating FROM page_comments WHERE epid != '' AND rating != 0";
	$results = mysql_query($query);
	while($row = mysql_fetch_array($results)){
		$epid = "v".$row['epid'];
		mysql_query("INSERT INTO ratings (rating_id,rating_num,IP) VALUES ('$epid','".$row['rating']."','".$row['uid']."')") or die(mysql_error());
	}
}
?>