<?php
/****************************************************************\
## FileName: email.class.php									 
## Author: Brad Riemann										 
## Usage: Email Sending Class, contains templates and functions for ALL emails sent from AFTW
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Email extends Config {
	
	var $Body, $Subject, $To, $BCC;
	
	/******************\
	# Public Functions #
	\******************/
	
	public function __construct($To = "brad@ftwentertainment.com",$BCC = NULL)
	{
		parent::__construct();
		$this->To = $To;
		$this->BCC = $BCC;
	}
	
	public function Send($Type,$Var1)
	{
		$this->BuildTemplate($Type,$Var1);
		$this->NewSendMail($this->Subject,$this->To,$this->Body,$this->BCC);
		return TRUE;
	}
	
	private function NewSendMail($subject,$to,$body,$bcc = NULL)
	{
		if($bcc == NULL)
		{
			$bcc = '';
		}
		else
		{
			$bcc = 'Bcc: ' . $bcc . "\r\n";
		}
		ini_set('sendmail_from', 'no-reply@animeftw.tv');
		$headers = 'From: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'Reply-To: AnimeFTW.tv <support@animeftw.tv>' . "\r\n" .
			$bcc .
			'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $body, $headers);
	}
	
	private function BuildTemplate($Type,$Var1 = NULL)
	{
		// if the type is 0, it's a notification about an order being submitted, we need the Type and the Order ID
		if($Type == 0 || $Type == 1 || $Type == 4)
		{
			$query = "SELECT store_orders.id, store_orders.total_price, store_orders.date_submitted, store_orders.status, store_orders.tracking_num, store_orders.payment_method, store_orders.payment_id, store_order_paypallogs.shipping, store_order_paypallogs.first_name, store_order_paypallogs.last_name, store_order_paypallogs.address_street, store_order_paypallogs.address_city, store_order_paypallogs.address_state, store_order_paypallogs.address_zip, store_order_paypallogs.address_country FROM store_orders, store_order_paypallogs WHERE store_order_paypallogs.txn_id=payment_id AND store_orders.cart_id=" . mysqli_real_escape_string($Var1);
			$results = mysqli_query($query);
			$row = mysqli_fetch_array($results);
			
			//list($cart_id,$order_id,$total_price,$date_submitted,$date_updated,$status,$tracking_num) = mysqli_fetch_array($results)
			// $Var1 is going to be the cart_id, since the email is going out, it means that we are to send based on the cart which has been moved to inactive, and has created an entry in the orders tab.
			$this->Subject = "AnimeFTW.tv Store Order Update, Order ID #" . str_pad($row['id'], 8, '0', STR_PAD_LEFT);

			if($Type == 0)
			{
				$this->Body = "Thank you for shopping the Store at AnimeFTW.tv. Depending on your location and shipping method, you should receive your product(s) within 3 to 5 business days after we confirm your payment.\n\n";
				$OrderStatus = "**Processing Order**";
			}
			else if($Type == 1)
			{
				$this->Body = "Thank you for shopping the Store at AnimeFTW.tv. We have received the order, but are currently waiting for the payment to be processed. Once the Order Payment has been processed, you will receive another email indiating that our team is working to fulfill the Order.\n\n";
				$OrderStatus = "Ordered, Awaiting Payment to Clear";
			}
			else if($Type == 4)
			{
				$this->Body = "Thank you for shopping the Store at AnimeFTW.tv. Your order has been marked as shipped in the system, please visit the store to find your tracking information for the packages delivery!\n\n";
				$OrderStatus = "**Order Shipped**";
			}
			else
			{
			}
			$this->Body = "Your satisfaction is important to us, so if you have any questions please don't hesitate to email or or post on the forums. When contacting us about this order, please be sure to include your order number.

You may track the progress of this order by logging onto the site and navigating to your account portion of the store, found here: https://www.animeftw.tv/store/account

Order Number: " . str_pad($row['id'], 8, '0', STR_PAD_LEFT) . "
Order Date: " . date("l d F, Y",$row['date_submitted']) . "
Payment Method: Paypal
Payment ID: " . $row['payment_id'] . "
Order Status: " . $OrderStatus . "
						
Products
------------------------------------------------------\n";

$subresults = mysqli_query("SELECT (SELECT item_size FROM store_inventory WHERE id=store_orders_items.inventory_id) AS item_size, store_orders_items.quantity, store_items.name, store_items.price FROM store_orders_items, store_items WHERE store_orders_items.cart_id=" . $Var1 . " AND store_items.id=store_orders_items.item_id");		

while(list($item_size,$quantity,$name,$price,) = mysqli_fetch_array($subresults))
{
	$this->Body .= "$quantity x $name = " . ($quantity*$price) . " \nSize $item_size\n";
}			
$this->Body .= "------------------------------------------------------

Sub-Total: $" . ($row['total_price']-$row['shipping']) . "
United States Postal Service (Priority): $" . $row['shipping'] . "\n";

if($Type == 4)
{
	// tracking number added.
	$this->Body .= "Tracking Number: " . $row['tracking_num'] . "\n";
}
$this->Body .= "Total: $" . $row['total_price'] . "
			
Shipping Address:
" . $row['first_name'] ." " . $row['last_name'] ."
" . $row['address_street'] . "
" . $row['address_city'] . ", " . $row['address_state'] . " " . $row['address_zip'] . "
" . $row['address_country'] . "
			
Billing Address:
" . $row['first_name'] ." " . $row['last_name'] ."
" . $row['address_street'] . "
" . $row['address_city'] . ", " . $row['address_state'] . " " . $row['address_zip'] . "
" . $row['address_country'] . "
			
------------------------------------------------------
This is not a receipt! For questions or comments, please post on the forums or contact support@animeftw.tv, referencing order id " . str_pad($row['id'], 8, '0', STR_PAD_LEFT);

mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Store Update for Cart " . $row['id'] . "');");
		}
		else if($Type == 2)
		{
			$this->Subject = 'Video Image Creation Errors.';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'Video Image Creation Error Email');");
		} 
		else if($Type == 3)
		{
			$this->Subject = 'Advanced Member Check';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'Advanced Member Check');");
		} 
		else if($Type == 5)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'AnimeFTW.tv Management - Login Failure';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'Failed Login Attempt AFTW Management');");
		}
		else if($Type == 6)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'AnimeFTW.tv Management - Password Change Request';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Management Password Change Request');");
		}
		else if($Type == 7)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'AnimeFTW.tv Automated Video Muxing Process';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Management Password Change Request');");
		}
		else if($Type == 8)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'AnimeFTW.tv Management - New Series Added';
			
			$variable = "These are the Values for a new Series:\n\n";
			foreach($Var1 as $key => $value)
			{
				$variable .= $key . ' - ' . stripslashes($value) . "\n";
			}
			
			$this->Body = $variable;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Management New Series Notification');");
		}
		else if($Type == 9)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'New Session at AnimeFTW.tv!';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'New Session at AnimeFTW.tv.');");
		}
		else if($Type == 10)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'AnimeFTW.tv Session Removal!';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Session Removed.');");
		}
		else if($Type == 11)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'Activation email from AnimeFTW.tv';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Account Registration');");
		}
		else if($Type == 12)
		{
			// emails for the failing to log in to the Management console.
			$this->Subject = 'Failed Registration - AnimeFTW.tv';
			$this->Body = $Var1;
			
			mysqli_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'AnimeFTW.tv Account Registration Failure Notice');");
		}
		else
		{
			// There is no template, exit the script from sending
			exit;
		}
	}
}

?>