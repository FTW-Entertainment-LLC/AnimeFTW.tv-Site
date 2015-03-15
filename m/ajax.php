<?php
include("../includes/classes/config.v2.class.php");


// if its port 80, lets make sure that its not the registration or login page..
if((isset($_GET['page']) && $_GET['page'] == 'login') && $_SERVER['SERVER_PORT'] == 80)
{
	// we push them to the login page.
	echo '<script>window.location = "https://www.animeftw.tv/m/login";</script>';
	exit;
}
else if((isset($_GET['page']) && $_GET['page'] == 'register') && $_SERVER['SERVER_PORT'] == 80)
{
	// push them to the registration page
	echo '<script>window.location = "https://www.animeftw.tv/m/register";</script>';
	exit;
}
else
{
	// make sure the app is on port 80 to improve speed.
	if($_SERVER['SERVER_PORT'] == 443 && ($_GET['page'] != 'register' && $_GET['page'] != 'login') && !isset($_POST['method']))
	{
		// redirecting to make sure the app keeps its cool
		echo '<script>window.location = "http://www.animeftw.tv/m/?page=' . $_GET['page'] . '";</script>';
		exit;		
	}
	else
	{
	}
}


if(isset($_POST['method']))
{
	include("includes/secure.class.php");
	$Secure = new Secure($_GET['page']);
}
else
{
	include("includes/mobile.class.php");

	$Mobile = new Mobile($_GET['page']);
	$Mobile->DisplayPage();
}