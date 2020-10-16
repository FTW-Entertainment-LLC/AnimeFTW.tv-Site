<?php
exit;
include('../siteroot.php');

$query = "SELECT id, category FROM mainaftw_anime.series";
$result = mysqli_query($query) or die('Error : ' . mysqli_error());

while(list($id,$category) = mysqli_fetch_array($result))
{
	$update = "UPDATE series SET category = '" . $category . ", ' WHERE id = '" . $id . "';";
	mysqli_query($update);
	//echo $update."\n";
}
echo "Done";