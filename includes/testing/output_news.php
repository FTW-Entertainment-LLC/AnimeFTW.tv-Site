<?php
include('../global_functions.php');

$query = "SELECT fullSeriesName, description, seoname 
FROM `series` WHERE 
seriesName LIKE '%fatestaynight%' OR
seriesName LIKE '%gunslingergirl%' OR
seriesName LIKE '%hayatenogotoku2%' OR
seriesName LIKE '%kampfer%' OR
seriesName LIKE '%kiddygrade%' OR
seriesName LIKE '%goodbyeteacherdespair%' OR
seriesName LIKE '%handmaidmay%' OR
seriesName LIKE '%kiddygirland%' OR
seriesName LIKE '%zeronotsukaimaf%' OR
seriesName LIKE '%kon%' OR
seriesName LIKE '%kinosjourney%' OR
seriesName LIKE '%ghostintheshellsac%' OR
seriesName LIKE '%thehilldyedrosemadderova
arakawaunderthebridge2%' OR
seriesName LIKE '%dragondrive%' OR
seriesName LIKE '%thevisionofescaflowne%' OR
seriesName LIKE '%fullmetalpanicfumoffu%' OR
seriesName LIKE '%fullmetalpanictsr%' OR
seriesName LIKE '%fullmetalalchemistbrotherhood%' OR
seriesName LIKE '%gareizero%' OR
seriesName LIKE '%gilgamesh%' OR
seriesName LIKE '%hajimenoipponewchallenger%' OR
seriesName LIKE '%fullmetalpanic%' OR
seriesName LIKE '%hayatethecombatbutler%' OR
seriesName LIKE '%firstlovelimited%' OR
seriesName LIKE '%inuyashathefinalact%' OR
seriesName LIKE '%kannagi%' OR
seriesName LIKE '%kurokami%' OR
seriesName LIKE '%fullmetalalchemist%' OR
seriesName LIKE '%hyakko%' OR
seriesName LIKE '%kaiba%' ORDER BY seriesName";
$i = 1;
$result = mysqli_query($conn, $query);
echo '<textarea style="width:500px;height:300px;">';	
while(list($fullSeriesName,$description,$seoname) = mysqli_fetch_array($result))
{
	$description = stripslashes($description);
	$description = preg_replace('#<br\s*/?>#i', "\n", $description);
	echo "<br />\n";
	echo "<a href=\"http://www.animeftw.tv/anime/$seoname/\">$fullSeriesName</a><br />\n";
	echo "<b>Synopsis</b>: \n$description <br />\n";
	$i++;
}
echo '</textarea>';
echo $i;
?>