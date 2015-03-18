<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = '10.151.1.10';
$newsdbuser = 'mainaftw_anime';
$newsdbpass = '26V)YPh:|IJG';
$newsdbname = 'mainaftw_anime';
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die(mysql_error());
mysql_select_db($newsdbname,$x);
?>