<?php
include('/home/mainaftw/public_html/includes/global_functions.php');

	$query = "SELECT id, fullSeriesName FROM series ORDER BY id";
	$result = mysql_query($query) or die('Error : ' . mysql_error());
	$startNumber = 1;
  	while(list($id,$fullSeriesName) = mysql_fetch_array($result)){
		$query2 = mysql_query("SELECT id FROM site_topseries WHERE seriesID='".$id."'"); 
		$databaseCheck = mysql_num_rows($query2);
		if($databaseCheck == 0){
			$query = sprintf("INSERT INTO site_topseries (seriesID, seriesName, lastPosition, currentPosition) VALUES ('%s', '%s', '%s', '%s')",
				mysql_real_escape_string($id, $conn),
				mysql_real_escape_string($fullSeriesName, $conn),
				mysql_real_escape_string($startNumber, $conn),
				mysql_real_escape_string($startNumber, $conn));
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			echo 'Series Name: '.$fullSeriesName.'. ID# '.$id.'. Added. ';
			$query = sprintf("INSERT INTO topseriescalc (seriesId, countedPages, pagePercentage) VALUES ('%s', '%s', '%s')",
				mysql_real_escape_string($id, $conn),
				mysql_real_escape_string('0', $conn),
				mysql_real_escape_string('0', $conn));
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			echo 'Series Name: '.$fullSeriesName.' Added to the topsite listing.<br />';
			$startNumber++;
		}
		else {
			echo 'Series Name: '.$fullSeriesName.'. ID# '.$id.'. Already in the Database.<br />';
			$startNumber++;
		}
	}

	mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '11', '" . time() . "', '" . time() . "');");
	mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = 11");