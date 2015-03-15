<?php

include 'includes/config.php';
	include 'includes/newsOpenDb.php';

$series = htmlspecialchars($_POST['series']);
$prefix = htmlspecialchars($_POST['prefix']);
$status = htmlspecialchars($_POST['uestatus']);
$user = htmlspecialchars($_POST['user']);
$anidbid = htmlspecialchars($_POST['anidbsid']);
$type = htmlspecialchars($_POST['type']);
$episodes = htmlspecialchars($_POST['episodesdoing']). "/" .htmlspecialchars($_POST['episodestotal']);
$resolution = htmlspecialchars($_POST['resolutionx']). "x" .htmlspecialchars($_POST['resolutiony']);



$sql = mysql_query("INSERT INTO uestatus (series, prefix, episodes, type, resolution, status, user, anidbsid, updated) VALUES('$series', '$prefix', '$episodes', '$type', '$resolution','$status','$user','$anidbid', CURDATE())") or die(mysql_error());



$topres = mysql_query("SELECT * FROM uestatus ORDER BY uestatus.ID DESC LIMIT 1;");

while($row=mysql_fetch_array($topres)){

header('Location: http://'.$_SERVER['HTTP_HOST'].'/uploads?actioned='.$row[ID]);
}
?>
