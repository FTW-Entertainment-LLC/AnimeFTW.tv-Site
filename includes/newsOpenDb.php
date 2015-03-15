<?php
// This is an example opendb.php
$conn = @mysql_connect($newsdbhost, $newsdbuser, $newsdbpass) or die ('We seem to be having issues with our connection to the Database, please hang tight.');
mysql_select_db($newsdbname);
if(!$conn)
{
	echo 'We seem to be having issues with our connection to the Database, please hang tight.';
}
?>