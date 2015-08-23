<?php
	//require_once ( 'includes/settings.php' );
	include_once("includes/classes/config.class.php");
	include_once("includes/classes/sessions.class.php");
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$Session = new Sessions();
	$Session->connectProfile($Config->outputUserInformation());
	$Session->logoutOfSession();
	//don't echo out anything before this function
	//logout ();
?>