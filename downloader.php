<?php

include("includes/classes/config.class.php");
$Config = new Config();

$dir = '/home/mainaftw/public_html/downloads/';
$fullPath = $dir.$_GET['f'];

$filedata = @file_get_contents($fullPath);

//id 	name 	count 	permissions 	active 
$query = "SELECT `id`, `name`, `permissions`, `active` FROM downloads WHERE `name` = '" . mysql_real_escape_string($_GET['f']) . "' AND `permissions` LIKE '%" . $Config->UserArray[2] . "%'";
$results = mysql_query($query);
$count = mysql_num_rows($results);

if($count < 1)
{
	die("File does not exist.");
}
else
{
	$row = mysql_fetch_assoc($results);
	// SUCCESS
	if($row['active'] == 1)
	{
		// GET A NAME FOR THE FILE
		$basename = $_GET['f'];

		// THESE HEADERS ARE USED ON ALL BROWSERS
		header("Content-Type: application-x/force-download");
		header("Content-Disposition: attachment; filename=" . $row['name']);
		header("Content-length: " . (string)(strlen($filedata)));
		header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

		// THIS HEADER MUST BE OMITTED FOR IE 6+
		if (FALSE === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE '))
		{
			header("Cache-Control: no-cache, must-revalidate");
		}

		// THIS IS THE LAST HEADER
		header("Pragma: no-cache");

		// FLUSH THE HEADERS TO THE BROWSER
		flush();

		// CAPTURE THE FILE IN THE OUTPUT BUFFERS - WILL BE FLUSHED AT SCRIPT END
		ob_start();
		echo $filedata;
			
		$results = mysql_query("UPDATE downloads SET `count` = `count`+1 WHERE id = " . $row['id']);
		$results = mysql_query("INSERT INTO `downloads_log` (`id`, `download_id`, `useragent`, `date`, `ip`) VALUES (NULL, '" . $row['id'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . time() . "', '" . $_SERVER['REMOTE_ADDR'] . "');");
	}
	else 
	{
		die('File does not exist... ');
	}
}