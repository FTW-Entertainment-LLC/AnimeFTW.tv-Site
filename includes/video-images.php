<?php
include('siteroot.php');
if(!isset($_GET['limit']))
{
	$limit = '0';
}
else
{
	$limit = $_GET['limit'];
} //14645-14911
$query = "SELECT id, epnumber, epprefix, seriesname FROM episode WHERE id > 14644 AND id < 14912";
$result = mysql_query($query) or die('Error : ' . mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	echo "id# " . $row['id'] . ", for series: " . $row['seriesname'] . ", epprefix: " . $row['epprefix'] . ", epnumber: " . $row['epnumber'] . "<br /><img src=\"http://static.ftw-cdn.com/site-images/video-images/".$row['epprefix']."_".$row['epnumber']."_screen.jpeg\" alt=\"Not working, see above\" /><br /><br />\n";
}

?>