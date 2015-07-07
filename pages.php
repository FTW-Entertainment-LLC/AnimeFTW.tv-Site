<?php
include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
$PageTitle = 'AnimeFTW.tv Registration | '.$_SERVER['HTTP_HOST'].' | Your DivX Anime streaming Source!';

include('header.php');
include('header-nav.php');
$index_global_message = "";
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// End Main BG
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	if($_GET['node'] == 'stats')
	{
				$FinalDate3 = time()-86400;
				//how many are online~query
				$query = mysql_query("SELECT ID FROM users WHERE lastLogin>='".$FinalDate2."'"); 
				$online_users = mysql_num_rows($query) or die("Error: ". mysql_error(). " with query ". $query);
				//how many active in last minute
				$query22 = mysql_query("SELECT ID FROM users WHERE lastActivity>='".$FinalDate3."'"); 
				$online_users_24hours = mysql_num_rows($query22) or die("Error: ". mysql_error(). " with query ". $query22);
				//sidebar st00fs					
					$query2 = mysql_query("SELECT id FROM episode"); 
					$full_total_episodes = mysql_num_rows($query2) or die("Error: ". mysql_error(). " with query ". $query2);
					
					$query3 = mysql_query("SELECT id FROM series WHERE active='yes'"); 
					$total_series = mysql_num_rows($query3) or die("Error: ". mysql_error(). " with query ". $query3);
					
					$query4 = mysql_query("SELECT id FROM page_comments"); 
					$total_comments = mysql_num_rows($query4) or die("Error: ". mysql_error(). " with query ". $query4);
				//gender queries!
					//male
					$query5 = mysql_query("SELECT ID FROM users WHERE gender='male'"); 
					$total_male = mysql_num_rows($query5) or die("Error: ". mysql_error(). " with query ". $query5);
					//female
					$query6 = mysql_query("SELECT ID FROM users WHERE gender='female'"); 
					$total_female = mysql_num_rows($query6) or die("Error: ". mysql_error(). " with query ". $query6);
				//avatar queries!
					// how many active?
					$query7 = mysql_query("SELECT ID FROM users WHERE avatarActivate='yes'"); 
					$total_avatars = mysql_num_rows($query7) or die("Error: ". mysql_error(). " with query ". $query7);
					// how many gif?
					$query8 = mysql_query("SELECT ID FROM users WHERE avatarExtension='gif'"); 
					$total_avatars_gif = mysql_num_rows($query8) or die("Error: ". mysql_error(). " with query ". $query8);
					// how many jpgs?
					$query9 = mysql_query("SELECT ID FROM users WHERE avatarExtension='jpg'"); 
					$total_avatars_jpg = mysql_num_rows($query9) or die("Error: ". mysql_error(). " with query ". $query9);
					// how many pngs?
					$query10 = mysql_query("SELECT ID FROM users WHERE avatarExtension='png'"); 
					$total_avatars_png = mysql_num_rows($query10) or die("Error: ". mysql_error(). " with query ". $query10);
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Site Statistics</span>\n";
		echo "<br />\n";
		echo "</div>\n";
				echo '<div align="center">There have been '.$online_users_24hours.' registered users online in the past 24 hours!<br /><br /></div>
					<div style="width:100%">';
				$query19 = "SELECT Username, lastActivity FROM users WHERE lastActivity>='".$FinalDate3."' ORDER BY lastActivity DESC";
				$result19 = mysql_query($query19) or die('Error : ' . mysql_error());
  				while(list($Username,$lastActivity) = mysql_fetch_array($result19))
				{
					$lastActivity = timeZoneChange($lastActivity,$timeZone);
					$lastLogin2 = date("l, F jS, Y, h:i a",$lastActivity);
					$query20  = "SELECT Level_access, advanceActive, advanceImage, advancePreffix FROM users WHERE Username='".$Username."'";
					$result20 = mysql_query($query20) or die('Error : ' . mysql_error());
					$row20 = mysql_fetch_array($result20);
					$Level_access = $row20['Level_access'];
					$advanceActive = $row20['advanceActive'];
					$advanceImage = $row20['advanceImage'];
					$advancePreffix = $row20['advancePreffix'];
				if ($Level_access == 1)
				{
					echo '<img src="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/images/adminbadge.gif" alt="Admin of Animeftw" style="vertical-align:middle;" />';
					 echo '<span style="'.$advancePreffix.'"><a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/user/' . $Username . '" title="1last click on '.$lastLogin2.'">' . $Username . '</a></span>, ';
					
				}
				else if ($Level_access == 2)
				{
					echo '<img src="http://'.$_SERVER['HTTP_HOST'].'/images/manager.gif" alt="Manager of Animeftw" style="vertical-align:middle;" />';
					 echo '<span style="'.$advancePreffix.'"><a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/user/' . $Username . '" title="last click on '.$lastLogin2.'">' . $Username . '</a></span>, ';
					
				}
				else if($Level_access == 7)
				{
					echo '<img src="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/images/advancedimages/'.$advanceImage.'.gif" alt="Advanced User Title" style="vertical-align:middle;" />';
					if ($advancePreffix == '')
					{
					 echo '<a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/user/' . $Username . '" title="last click on '.$lastLogin2.'">' . $Username . '</a>, ';
					}
					else
					{
					echo '<span style="'.$advancePreffix.'"><a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/user/' . $Username . '" title="login: '.$lastLogin2.'">' . $Username . '</a></span>, ';
					}
				}
				
				else
				{
					
					echo '<a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/user/'.$Username.'" title="last click on '.$lastLogin2.'">'.$Username.'</a>, ';
				}
				}	
				echo "<br /><br />";
					//who was online~query
				
				echo '</div>';
				echo 'Our users have posted '.$total_comments.' Comments to date<br />';
				echo '<br />We have '.$total_series.' series up for viewing!<br />';
				echo '<br />'.$full_total_episodes.' episodes are online and in our database and ready for watching<br />';
				echo '<br />'.$total_male.' of our users have updated themselves to reflect themselves as Male<br />';
				echo '<br />'.$total_female.' of our users have updated themselves to reflect themselves as Female<br />';
				echo '<br />';
				echo '<br />'.$total_avatars.' of our users have updated their avatars<br />';
				echo '<br /> - '.$total_avatars_gif.' of those avatars are gifs<br />';
				echo '<br /> - '.$total_avatars_jpg.' of those avatars are jpgs<br />';
				echo '<br /> - '.$total_avatars_png.' of those avatars are png<br />';
				echo '</div>';
				echo '
					<h2>'.$language['latest_5_comments'].'</h2>
						coming soon!
						<div class="date"></div>
       		<h2>'.$language['10_newest_members'] .'</h2>';
							$query = "SELECT Username FROM users WHERE active='1' ORDER BY id DESC LIMIT 0, 10";
							$result = mysql_query($query) or die('Error : ' . mysql_error());
							$self = $_SERVER['PHP_SELF'];

						while(list($Username) = mysql_fetch_array($result, MYSQL_NUM))
						{
							echo '<a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/user/'.$Username.'">'.$Username.'</a>, ';
						}
						
						?>
        	<div class="date"></div>
       		<h2><?=$language['15_latest_episodes'];?></h2>
            <br />
                <?
				if($PermissionLevelAdvanced != 0 && $PermissionLevelAdvanced !=3)
			{
			$query = "SELECT id, seriesname, epname, epnumber FROM episode ORDER BY id DESC LIMIT 0, 15";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$self = $_SERVER['PHP_SELF'];
	
			while(list($id, $seriesname, $epname, $epnumber) = mysql_fetch_array($result, MYSQL_NUM))
			{
			$query2 = "SELECT seriesName, fullSeriesName, seoname FROM series WHERE seriesName='".$seriesname."'";
			$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
			list($seriesName2, $fullSeriesName, $seoname) = mysql_fetch_array($result2, MYSQL_NUM);
			$fullSeriesName = stripslashes($fullSeriesName);
			$epname = stripslashes($epname);
			echo '<div> - Episode #' . $epnumber . ' added to series <a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/">' . $fullSeriesName . '</a> titled: <span style="font-weight:bold;">' . $epname . '</span></div>
						<br />';
			}
		   echo' </div>';
		}
		else {
			$query = "SELECT id, seriesname, epname, epnumber FROM episode ORDER BY date DESC LIMIT 0, 15";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$self = $_SERVER['PHP_SELF'];
	
			while(list($id, $seriesname, $epname, $epnumber) = mysql_fetch_array($result, MYSQL_NUM))
			{
			$query2 = "SELECT seriesName, fullSeriesName, seoname FROM series WHERE seriesName='".$seriesname."'";
			$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
			list($seriesName2, $fullSeriesName, $seoname) = mysql_fetch_array($result2, MYSQL_NUM);
			$fullSeriesName = stripslashes($fullSeriesName);
			$epname = stripslashes($epname);
			echo '<div> - Episode #' . $epnumber . ' added to series <a href="'.$IsSecure.'://'.$_SERVER['HTTP_HOST'].'/videos/'.$seoname.'/">' . $fullSeriesName . '</a> titled: <span style="font-weight:bold;">' . $epname . '</span></div>
						<br />';
			}
		   echo' </div>';
		}

	}
	# Content here
	#
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	# Right Content here
	#
	/*echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>Panel 1 Title Here</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo "I am a random text and I will repeat. I am a random text and I will repeat. I am a random text and I will repeat. I am a random text and I will repeat.";
	echo "</div></div>\n"*/;
	if($userArray[2] == 0 || $userArray[2] == 3){
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>Advertisement</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo "<!-- Begin BidVertiser code --><SCRIPT LANGUAGE=\"JavaScript1.1\" SRC=\"http://bdv.bidvertiser.com/BidVertiser.dbm?pid=341006&bid=842663\" type=\"text/javascript\"></SCRIPT><noscript><a href=\"http://www.bidvertiser.com\">internet marketing</a></noscript><!-- End BidVertiser code --> ";
	echo "</div></div>\n";
	}	
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
		
include('footer.php')
?>