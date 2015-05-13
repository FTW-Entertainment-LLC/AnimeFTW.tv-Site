<?php
	//require_once ( 'includes/settings.php' );
	include_once("includes/classes/config.class.php");
	include_once("includes/classes/sessions.class.php");
	$Session = new Sessions();
	$Session->logoutOfSession();
	//don't echo out anything before this function
	//logout ();
?>