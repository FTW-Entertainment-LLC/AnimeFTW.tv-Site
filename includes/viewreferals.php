<?php
include('global_functions.php');
echo "<h3>List of referals from the eBlasts</h3>";
$query = "SELECT elinkId, date, ip FROM ereferals ORDER BY date DESC";
$result2 = mysqli_query($query) or die('Error : ' . mysqli_error());
		
while(list($elinkId,$date,$ip) = mysqli_fetch_array($result2))
{
	$query   = "SELECT normalLink FROM elinks WHERE id='$elinkId'";
	$result  = mysqli_query($query) or die('Error : ' . mysqli_error()); 
	$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
				
	echo $ip." visited on ".date("l, F jS, Y, h:i a",$date).". Link was for ".$row['normalLink']."<br />\n";
}
?>