<?php

require "paypal_integration_class/paypal.class.php";
require "config.php";
require "connect.php";

$p = new paypal_class;
$p->paypal_url = $payPalURL;

if ($p->validate_ipn()) {
	if($p->ipn_data['payment_status']=='Completed')
	{
		$amount = $p->ipn_data['mc_gross'] - $p->ipn_data['mc_fee'];
		
		mysql_query("	INSERT INTO dc_donations (transaction_id,donor_email,amount,original_request)
						VALUES (
							'".esc($p->ipn_data['txn_id'])."',
							'".esc($p->ipn_data['payer_email'])."',
							".(float)$amount.",
							'".esc(http_build_query($_POST))."'
						)");
	}
}

function esc($str)
{
	global $link;
	return mysql_real_escape_string($str,$link);
}
?>