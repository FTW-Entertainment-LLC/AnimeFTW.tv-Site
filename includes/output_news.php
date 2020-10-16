<?php
include('global_functions.php');

$query = "SELECT fullSeriesName, description, seoname 
FROM `series` WHERE 
fullSeriesName LIKE '%rurouni kenshin%' OR
fullSeriesName LIKE '%ef - %' OR
fullSeriesName LIKE '%elfen lied%' OR
fullSeriesName LIKE '%ergo proxy%' OR
fullSeriesName LIKE '%gurren lagann%' OR
fullSeriesName LIKE '%vampire knight%' OR
fullSeriesName LIKE '%hakuouki%' OR
fullSeriesName LIKE '%Accelerated World%' OR
fullSeriesName LIKE '%Card Captor Sakura%' OR
fullSeriesName LIKE '%CCSakura Movies%' OR
fullSeriesName LIKE '%BT X%' OR
fullSeriesName LIKE '%Hen Zemi%' OR
fullSeriesName LIKE '%Quiz Magic Academy%' OR
fullSeriesName LIKE '%Eureka Seven Ao%' OR
fullSeriesName LIKE '%Jormungand%' OR
fullSeriesName LIKE '%Daily Lives of High School Boys%' ORDER BY seriesName";
$i = 1;
$result = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
echo '<textarea style="width:500px;height:300px;">';	
while(list($fullSeriesName,$description,$seoname) = mysqli_fetch_array($result))
{
	$description = stripslashes($description);
	$description = preg_replace('#<br\s*/?>#i', "\n", $description);
	echo "\n";
	echo "[url=http://www.animeftw.tv/videos/$seoname/]$fullSeriesName [/url]\n";
	echo "[i]Synopsis[/i]: \n$description \n";
	$i++;
}
echo '</textarea>';
echo $i;
?>