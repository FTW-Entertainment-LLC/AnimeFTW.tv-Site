<?php
/****************************************************************\
## FileName: am-check.cron.php								 
## Author: Brad Riemann										 
## Usage: Cron for checking advanced members to see if someone is past their time..
## Copyright 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

	require_once("/home/mainaftw/public_html/includes/classes/config.class.php");
	require_once("/home/mainaftw/public_html/includes/classes/email.class.php"); 

$query = "SELECT Username, advanceDate, advanceLevel, advanceActive FROM users WHERE Level_access = 7";
$result = mysqli_query($query) or die('Error : ' . mysqli_error());

//vars
$CronID = 3;
$day = 86400;
$dafter = time()+$day;
$rv = '';
while(list($Username,$advanceDate,$advanceLevel,$advanceActive) = mysqli_fetch_array($result)){
	if($advanceLevel == 1){
		$advanceDate = date("l, F jS, Y, h:i a", $advanceDate);
		$blahdate = strtotime($advanceDate." +1 month");
	}
	else {
		$advanceDate = date("l, F jS, Y, h:i a", $advanceDate);
		$blahdate = strtotime($advanceDate." +".$advanceLevel." months");
	}
	$testfuture = date("l, F jS, Y, h:i a", $blahdate);
	
	if($blahdate <= time()){
		$rv .= $Username.' needs to be moved back! They expired on '.$testfuture."\n";
		//echo $Username.' needs to be moved back! They expired on '.$testfuture.'<br /> '."\n";
	}
	if(($blahdate > time()) && ($blahdate <= $dafter)){
		if($advanceActive == 'no'){
			$rv .= $Username.' is going to be moved in the next day. '.$testfuture."\n";
			//echo $Username.' is going to be moved in the next day. '.$testfuture.' <br />'."\n";
		}
		else {
			$rv .= $Username.' is still active, they will re-up on: '.$testfuture."\n";
		}
	}
}
if($rv == ''){
}
else { 
	$reports = "Advanced Member Check\n\n<br /><br />" . $rv;
	$Email = new Email('support@animeftw.tv');
	$Email->Send('3',$reports);
}

	// Update the logs, and then make sure the cron is reset.
	mysqli_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $CronID . "', '" . $currenttime . "', '" . time() . "');");
	mysqli_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $CronID);


?>