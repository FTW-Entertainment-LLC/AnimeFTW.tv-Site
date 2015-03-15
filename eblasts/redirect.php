<?php
			include '../includes/config.php';
			include '../includes/newsOpenDb.php';
if($_GET['link'])
{
	$query  = "SELECT normalLink FROM elinks WHERE enqryptLink='".$_GET['link']."'";
	$result = mysql_query($query) or die('Error : ' . mysql_error());
	$row = mysql_fetch_array($result);
	$normalLink = $row['normalLink'];
	header( "location: ".$normalLink);
	exit;
}
else {
}
?>