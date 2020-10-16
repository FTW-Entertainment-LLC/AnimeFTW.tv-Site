<?php
include('../classes/config.class.php');

$query = "SELECT `id`, `date` FROM `mainaftw_stats`.`series_stats`";
$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result))
{
	//2032014
	$year = substr($row['date'], -4, 4);
	$day = substr($row['date'], -6, 2);
	$month = substr($row['date'], 0, -6);
	$thisday = strtotime($year . '-' . $month . '-' . $day);
	
	mysqli_query($conn, "UPDATE `mainaftw_stats`.`series_stats` SET `date` = " . $thisday . " WHERE `id` = " . $row['id']);
}