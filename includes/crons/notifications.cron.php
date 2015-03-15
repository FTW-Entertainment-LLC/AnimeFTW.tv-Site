<?php
/****************************************************************\
## FileName: notifications.cron.php								 
## Author: Brad Riemann										 
## Usage: Cron for cleaning up notifications
## Copyright 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/
include('/home/mainaftw/public_html/includes/classes/config.class.php');
include('/home/mainaftw/public_html/includes/classes/notifications.class.php');

	// The object of this cron script
	// is to provide a simple way to clear out old notifications
	// this can be achieved with a simple query, but must be run every 15 minutes.

$notification = new AFTWNotifications();
$notification->CronJob();

?>