<?php
include("../classes/config.class.php");

$query = "SELECT `episode`.`id` AS id, `series`.`id` AS sid FROM `episode`, `series` WHERE `series`.`seriesName`=`episode`.`seriesname` AND `episode`.`sid` = 0 ORDER BY `episode`.`id`";

$result = mysql_query($query);

while($row = mysql_fetch_assoc($result))
{
	$query = "UPDATE `episode` SET `sid` = " . $row['sid'] . " WHERE `id` = " . $row['id'] . "; ";
	//echo $query;
	mysql_query($query);
}

