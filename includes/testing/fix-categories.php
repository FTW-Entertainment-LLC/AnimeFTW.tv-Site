<?php
exit;
include('../siteroot.php');

$query = "SELECT id, category FROM mainaftw_anime.series";
$result = mysql_query($query) or die('Error : ' . mysql_error());

while(list($id,$category) = mysql_fetch_array($result))
{
	$update = "UPDATE series SET category = '" . $category . ", ' WHERE id = '" . $id . "';";
	mysql_query($update);
	//echo $update."\n";
}
echo "Done";