<?php
include('global_functions.php');
echo "<h3>List of referals from the eBlasts</h3>";
$query = "SELECT elinkId, date, ip FROM ereferals ORDER BY date DESC";
$result2 = mysql_query($query) or die('Error : ' . mysql_error());
		
while(list($elinkId,$date,$ip) = mysql_fetch_array($result2))
{
	$query   = "SELECT normalLink FROM elinks WHERE id='$elinkId'";
	$result  = mysql_query($query) or die('Error : ' . mysql_error()); 
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				
	echo $ip." visited on ".date("l, F jS, Y, h:i a",$date).". Link was for ".$row['normalLink']."<br />\n";
}
?>