<?php

// NewSystem DB connect
$newsdbhost = 'localhost';
$newsdbuser = 'mainaftw_anime';
$newsdbpass = '26V)YPh:|IJG';
$newsdbname = 'mainaftw_anime';
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die('We are currently experiencing an issue with MySQL, the data corruption that happened due to the broken raid did not result in loss of files, we are hoping for 100% recovery for MySQL, please hang tight.');
mysql_select_db($newsdbname,$x);
?>