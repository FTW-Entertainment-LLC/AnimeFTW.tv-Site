<?php
#***********************************************************
#* cart-cleanup.php, cleanup script for outdated carts in the AFTW store
#* Written by Brad Riemann
#* Copywrite 2013 FTW Entertainment LLC
#* Distribution of this is stricty forbidden!
#***********************************************************

include("/home/mainaftw/public_html/includes/classes/config.class.php"); 

/*
* The object of this script, is to parse through the AFTW Store Carts and remove old store items.. 
* otherwise we have trouble with people buying things out of stock.
*/
$CronID = 5;
$CurrentDate = time(); // Our current Time
$GracePeriod = 6;  // 6 hours grace period for carts, we may have to adjust down the line 
$PastDate = $CurrentDate - (60*60*$GracePeriod);

$query = "SELECT store_orders_items.id FROM store_orders_items, store_cart WHERE store_orders_items.cart_id=store_cart.id AND store_cart.active = 0 AND store_cart.date <= $PastDate";

$results = mysql_query($query);
$numrow = mysql_num_rows($results);
if($numrow > 0)
{
	while($row = mysql_fetch_array($results))
	{
		mysql_query("DELETE FROM store_orders_items WHERE id = " . $row['id']);
		
	}
}
else
{
	// nothing to see here.
}
// Update the logs, and then make sure the cron is reset.
mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $CronID . "', '" . $CurrentDate . "', '" . time() . "');");
mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $CronID);

?>