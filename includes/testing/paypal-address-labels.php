<?php
include('../classes/config.class.php');

$query = "SELECT `first_name`, `last_name`, `address_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country` FROM `paypal_logs` WHERE submission_date >= 1412121600 AND txn_type = 'subscr_signup' AND address_status != '' LIMIT 0, 25";
$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result))
{
	echo '<div style="margin-bottom:15px;">';
	echo '<div>' . $row['first_name'] . ' ' . $row['last_name'] . '</div>';
	echo '<div>' . nl2br($row['address_street']) . '</div>';
	if($row['address_country'] == 'Norway' || $row['address_country'] == 'Italy' || $row['address_country'] == 'Netherlands')
	{
		$AlternateCity = ' ' . $row['address_city'];
		echo '<div>';
	}
	else
	{
		$AlternateCity = '';
		echo '<div>' . $row['address_city'] . ', ' . $row['address_state'] . ' ';
	}
	if($row['address_country'] == 'United Kingdom' || $row['address_country'] == 'Bahamas')
	{
		echo '<br />';
	}
	echo $row['address_zip'] . $AlternateCity . '</div>';
	echo '<div>' . $row['address_country'] . '</div>';
	echo '</div>';
}