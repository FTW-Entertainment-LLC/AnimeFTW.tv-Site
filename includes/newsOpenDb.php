<?php
// This is an example opendb.php
$conn = @mysqli_connect($newsdbhost, $newsdbuser, $newsdbpass, $newsdbname);
if(!$conn)
{
	echo 'We seem to be having issues with our connection to the Database, please hang tight.';
}
?>
