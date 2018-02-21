<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = 'localhost';
$newsdbuser = 'mainaftw_anime';
$newsdbpass = '26V)YPh:|IJG';
$newsdbname = 'mainaftw_anime';
if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com'||$_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com')
{
	// this will be for development connections only.
	$newsdbhost 		= 'localhost';
	$newsdbuser 		= 'devsftw9_anime';
	$newsdbpass 		= 'L=.zZ76[,TOqwf*&tl';
	$newsdbname 		= 'devsftw9_anime';
}
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die(mysql_error());
mysql_set_charset($x,"utf8");
mysql_select_db($newsdbname,$x);
?>