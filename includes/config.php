<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = '10.150.14.10';
$newsdbuser = 'mainaftw_anime';
$newsdbpass = '26V)YPh:|IJG';
$newsdbname = 'mainaftw_anime';
if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com')
{
	// this will be for development connections only.
	$newsdbhost 		= '10.150.14.10';
	$newsdbuser 		= 'devadmin_anime';
	$newsdbpass 		= 'L=.zZ76[,TOqwf*&tl';
	$newsdbname 		= 'devadmin_anime';
}
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die('MySQL is having techincal issues.. If you see this for longer than a few seconds, please alert the admins on the facebook page.');
mysql_select_db($newsdbname,$x);
?>