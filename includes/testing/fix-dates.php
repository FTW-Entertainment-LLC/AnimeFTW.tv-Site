<?php
include('../classes/config.class.php');

$query = "SELECT `id`, `date` FROM `mainaftw_stats`.`series_stats`";
$result = mysql_query($query);

while($row = mysql_fetch_assoc($result))
{
	//2032014
	$year = substr($row['date'], -4, 4);
	$day = substr($row['date'], -6, 2);
	$month = substr($row['date'], 0, -6);
	$thisday = strtotime($year . '-' . $month . '-' . $day);
	
	mysql_query("UPDATE `mainaftw_stats`.`series_stats` SET `date` = " . $thisday . " WHERE `id` = " . $row['id']);
}