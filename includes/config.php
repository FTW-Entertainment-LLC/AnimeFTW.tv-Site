<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = '10.151.1.10';
$newsdbuser = 'mainaftw_anime';
$newsdbpass = '26V)YPh:|IJG';
$newsdbname = 'mainaftw_anime';
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die('MySQL is having techincal issues.. If you see this for longer than a few seconds, please alert the admins on the facebook page.');
mysql_select_db($newsdbname,$x);
?>