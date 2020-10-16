<?php
	include 'config_site.php';
	include 'newsOpenDb.php';
	include 'global_functions.php';
	//get current date then go back 24 hours
	$currentDate = date('d-m-Y',time());
	$currentstrtotime = strtotime($currentDate);
	$currentstrtotime = $currentstrtotime-86400;
	$onlyonce = 1;
	$starttime = time();
	if($onlyonce == 1)
	{
		$correctorder = 1;
		while($correctorder <= 5)
		{
			if($correctorder == 1)
			{
				echo "Begin clearing of the topseriescalc table<br /> \n";
				//zero out the calc so we start "fresh"
				$allzeros = mysqli_query("UPDATE topseriescalc SET countedPages='0', pagePercentage='0'");
				echo "done clearing that... begin deleting of old not needed episode logs.<br /> \n";
				$correctorder++;
			}
			elseif($correctorder == 2)
			{
				$prefix = mysqli_query("DELETE FROM episodestats WHERE date<'".$currentstrtotime."'");
				echo "done deleting those... begin first round.<br /> \n";
				$correctorder++;
			}
			elseif($correctorder == 3)
			{
				$result1 = mysqli_query("SELECT epSeriesId FROM episodestats ORDER BY id") or die('Error : ' . mysqli_error());
				while(list($epSeriesId) = mysqli_fetch_array($result1))
				{
					$query = "UPDATE topseriescalc SET countedPages = countedPages+1 WHERE seriesId = $epSeriesId";
					$result = mysqli_query($query) or die('Error : ' . mysqli_error());
				}
				echo "<br />Adding episodes done, begin not so complex calculations.<br />";
				$correctorder++;
			}
			elseif($correctorder == 4)
			{
				// now we do the calculation one.. *could be resourcive intensive x.x*
				$query2 = "SELECT seriesId, countedPages FROM topseriescalc ORDER BY id";
				$result2 = mysqli_query($query2) or die('Error : ' . mysqli_error());
				while(list($seriesId,$countedPages) = mysqli_fetch_array($result2))
				{
					$query3 = mysqli_query("SELECT COUNT(id) AS maxEps FROM episode WHERE sid = '$seriesId'");
					$row3     = mysqli_fetch_array($query3, MYSQL_ASSOC);
					$maxEps = $row3['maxEps'];
					if($countedPages == 0)
					{
						$percentages = '0.00';
					}
					else
					{
						$percentages = @$countedPages/@$maxEps;
						$percentages = round($percentages,2);
					}
					$query4 = "UPDATE topseriescalc SET pagePercentage='$percentages' WHERE seriesId = $seriesId";
					$result4 = mysqli_query($query4) or die('Error : ' . mysqli_error());
				}
				echo "<br />Calculations done, moving on to the last piece, updating the system for the stat fix.<br />";
				$correctorder++;
			}
			else {
				//Phew thats done.. now lets go and update the toplist!
				$query5 = "SELECT seriesId, pagePercentage FROM topseriescalc ORDER BY pagePercentage DESC";
				$result5 = mysqli_query($query5) or die('Error : ' . mysqli_error());
				$toplistingnumber = 1;
				while(list($seriesId,$pagePercentage) = mysqli_fetch_array($result5))
				{
					$query6 = "SELECT currentPosition FROM site_topseries WHERE seriesID='$seriesId'";
							$result6 = mysqli_query($query6) or die('Error : ' . mysqli_error());
							$row6 = mysqli_fetch_array($result6); 
							$lastPosition = $row6['currentPosition'];
							if($lastPosition == 0)
							{
								$lastPosition = $toplistingnumber;
							}
					$query7 = 'UPDATE site_topseries SET lastPosition=\'' . mysqli_escape_string($lastPosition) . '\', currentPosition=\'' . mysqli_escape_string($toplistingnumber) .'\' WHERE seriesID=\'' . $seriesId . '\'';
					$result7 = mysqli_query($query7) or die('Error : ' . mysqli_error());
					$toplistingnumber++;
				}
				$correctorder++;
			}
		
		}		
		echo $correctorder;
		$onlyonce++;
	}
	// and last but not least, to set all the fields that need to be 0 to zero!
	#$killtable = mysqli_query("TRUNCATE TABLE episodestats");
	mysqli_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '12', '" . $starttime . "', '" . time() . "');");
	mysqli_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = 12");
include 'closedb.php';
?>