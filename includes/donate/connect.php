<?php

/* Database config */

$db_host		= 'localhost';
$db_user		= 'mainaftw_anime';
$db_pass		= '26V)YPh:|IJG';
$db_database		= 'mainaftw_anime';

/* End config */


$link = @mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');

mysql_set_charset('utf8');
mysql_select_db($db_database,$link);

?>