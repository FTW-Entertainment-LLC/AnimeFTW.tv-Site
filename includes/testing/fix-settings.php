<?php
include('../classes/config.class.php');
exit;
$query = "SELECT `ID`, `sitepmnote`, `notifications` FROM `mainaftw_anime`.`users` WHERE `Active` = 1";
$result = mysqli_query($query);

while($row = mysqli_fetch_assoc($result))
{
	// First check to see if they want to recieve notifications from site pms
	if($row['sitepmnote'] == 1)
	{
		// they opted for the default, nothing needed
	}
	else
	{
		// they don't want to receive our emails :(
		$suplementalquery = mysqli_query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $row['ID'] . "', " . time() . ", " . time() . ", '2', '4', '0');");
	}
	// check to see if they want to receive admin emails
	if($row['notifications'] == 1)
	{
		// nope, they want us to email them!
	}
	else
	{
		// sandpanda..
		$suplementalquery = mysqli_query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $row['ID'] . "', " . time() . ", " . time() . ", '7', '14', '0');");
	}
}