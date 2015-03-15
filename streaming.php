<?php
 /********************************************************

   FTW Streaming service
   Build	12.4
   Author	Adam Treadway
   Copyright	2012 AnimeFTW.tv
   Desc		Streams video files over a PHP protocol.
		Uses various methods of system check to
		prevent ripping and download outside our
		provided means of delivery.


 ********************************************************/

 //Very important. Prevents video from timing out after 30 seconds of streaming.
 set_time_limit(0);

 // Configuration

 // Connection Details
 $db_name	= "mainaftw_anime";
 $db_user	= "mainaftw_anime";
 $db_pass	= "26V)YPh:|IJG";
 $db_table	= "streaming";

 $link = mysql_connect("127.0.0.1",$db_user,$db_pass);
 mysql_select_db($db_name);

 //Get requested video
 if($request = isset($_GET['v']) ? $_GET['v'] : FALSE) {
	$sql = "SELECT * FROM `".$db_table."` WHERE `id`='$request' LIMIT 1";
	$query = mysql_query($sql,$link) or die(mysql_error());
	if(mysql_num_rows($query)) {
		$row = mysql_fetch_assoc($query);

		$path = "http://".$row['server'].".animeftw.tv/".$row['directory']."/".$row['file'];

		header("Content-type: video/x-matroska");
		$f = fopen($path,"r");
		$r = fread($f,$row['fsize']);
		echo $r;
		fclose($f);

	} else {
		echo "Requested video not available.";
	}
 } else {
	echo "Requested video not available.";
 }

 mysql_close($link);