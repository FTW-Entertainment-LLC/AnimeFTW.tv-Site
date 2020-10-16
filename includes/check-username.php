<?php
include('siteroot.php');
// Connect to MySQL DB
function makeUrlFriendly($postUsername) {
// Replace spaces with underscores
$output = str_replace(" " , "_" , $postUsername);
// Remove non-word characters
//$output = preg_replace("/\W/e" , "" , $output);
return strtolower($output);
}
$do = $_GET['do'];
switch($do) {
	case 'check_username_exists':
		if(!get_magic_quotes_gpc()){$username = addslashes($_GET['username']);}
		else{$username = $_GET['username'];}
		$username = makeUrlFriendly($username);
		$count = mysqli_num_rows(mysqli_query($conn, "SELECT ID FROM users WHERE Username='".mysqli_real_escape_string($conn, $username)."' OR `display_name` ='".mysqli_real_escape_string($conn, $username)."'"));
		if($count > 0) {
		// User name not available
		echo '<span style="color:FF0000">Username Already Taken</span>';
		}else{
			echo '<span style="color:009900">Username Available</span>';
		}
	break;
default:
	echo 'No action specified.';
break;
}
?>
