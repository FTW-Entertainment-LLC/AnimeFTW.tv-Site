<?php

include 'includes/config.php';
	include 'includes/newsOpenDb.php';
	if(isset($_POST['id'])){
		$id = $_POST['id'];
	}
	else {
		$id = 0;
	}
$id = htmlspecialchars($id);

if (isset($_POST['delete']) && $_POST['delete'] == TRUE){

$sql = mysql_query("DELETE FROM uestatus WHERE uestatus.ID='$id'") or die(mysql_error());

echo "<script> parent.location.href=\"http://".$_SERVER['HTTP_HOST']."/uploads\"; </script>";

} else {
$series = htmlspecialchars($_POST['series']);
$prefix = htmlspecialchars($_POST['prefix']);
$status = htmlspecialchars($_POST['uestatus']);
$user = htmlspecialchars($_POST['user']);
$anidbid = htmlspecialchars($_POST['anidbsid']);
$type = htmlspecialchars($_POST['type']);
$episodes = htmlspecialchars($_POST['episodesdoing']). "/" .htmlspecialchars($_POST['episodestotal']);
$resolution = htmlspecialchars($_POST['resolutionx']). "x" .htmlspecialchars($_POST['resolutiony']);
$sql = mysql_query("UPDATE uestatus SET series='$series', prefix='$prefix', episodes='$episodes', type='$type', resolution='$resolution', status='$status', user='$user', anidbsid='$anidbid', updated=NOW() WHERE uestatus.ID='$id'") or die(mysql_error());

echo "<script> parent.location.href=\"http://".$_SERVER['HTTP_HOST']."/uploads?actioned=$id\"; </script>";
}

?>
