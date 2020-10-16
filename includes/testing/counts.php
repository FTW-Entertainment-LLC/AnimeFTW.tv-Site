<?php
include("../classes/config.class.php");

$query = "SELECT `episode`.`id` AS id, `series`.`id` AS sid FROM `episode`, `series` WHERE `series`.`seriesName`=`episode`.`seriesname` AND `episode`.`sid` = 0 ORDER BY `episode`.`id`";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result))
{
	$query = "UPDATE `episode` SET `sid` = " . $row['sid'] . " WHERE `id` = " . $row['id'] . "; ";
	//echo $query;
	mysqli_query($conn, $query);
}

