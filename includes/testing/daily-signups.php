<?php
include('../classes/config.class.php');

for($i=1825;$i>0;$i--)
{
	$xdaysago = $i*86400; // gives us the value for how many days ago, we subtract that against the time() value
	$today = strtotime(date("d F Y"));
	$startofday = $today-$xdaysago;
	$endofday = $startofday+86399;
	
	$query = "SELECT COUNT(ID) AS NumSignups FROM `users` WHERE `registrationDate` >= " . $startofday . " AND `registrationDate` <= " . $endofday . " ";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);	
	//echo $query;
	mysqli_query($conn, "INSERT INTO `mainaftw_stats`.`user_stats` (`id`, `type`, `var1`, `var2`) VALUES (NULL, '1', '" . $startofday . "', '" . $row['NumSignups'] . "')");
}