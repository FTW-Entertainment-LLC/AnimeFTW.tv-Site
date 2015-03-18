<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = 'localhost';
$newsdbuser = 'root';
$newsdbpass = '';
$newsdbname = 'devadmin_anime';
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die(mysql_error());
mysql_select_db($newsdbname,$x);
?>