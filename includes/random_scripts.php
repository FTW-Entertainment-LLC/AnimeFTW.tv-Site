<?
	include 'config.php';
	include 'newsOpenDb.php';
	include 'siteroot.php';
session_start();
	if(isset($_COOKIE['cookie_id']))
	{
		$globalnonid = $_COOKIE['cookie_id'];
	}
	if(isset($_SESSION['user_id']))
	{
		$globalnonid = $_SESSION['user_id'];
	}
if($_GET['get'] == 'comments')
{
	$person = $_GET['Username'];
	
	if (!isset($_GET['Username']))
	{
	$finalUsername = 'Error.';
	}
	else {
		$query1   = "SELECT comments, dated, epid FROM page_comments WHERE name='".$_GET['Username']."'  ORDER BY dated DESC LIMIT 0,5";
		$result1  = mysql_query($query1) or die(mysql_error());
		$total_comments = mysql_num_rows($result1);
		$finalUsername = ''; 
		$numba = 1;
		if ($total_comments == 0)
		{
		$finalName = '<div class="commentsShowProfile">This User has not left any Comments.</div>';
		}
		else {
			while(list($comments,$dated,$epid) = mysql_fetch_array($result1, MYSQL_NUM))
			{
			$postedDate = strtotime($dated);
			$timeZone = '-6';
			$postedDate = timeZoneChange($postedDate,$timeZone);
			$postedDate = date("l, F jS, Y, h:i a",$postedDate);
			$finalName .= "<div class='commentsShowProfile'>Posted on: ".$postedDate."<br />Posted in: ".checkSeries($epid).", Episode #".checkEpisode($epid)."<br />Comment:<br />".$comments."</div>"."\n";
			$numba++;
			}
		}
						
	}
	echo '<div class="commentsShowProfile" align="center">Showing the latest 5 comments for '.$_GET['Username'].'</div>';
	echo $finalName."\n";
}
if($_GET['get'] == 'friend')
{
	$query1  = "SELECT * FROM users WHERE ID='".$globalnonid."'";
	$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
	$row1 = mysql_fetch_array($result1);
	$Level_accessbeta = $row1['Level_access'];
	if($Level_accessbeta != 3)
	{
		$allowedFriends = 65;
	}
	else {
		$allowedFriends = 20;
	}
	$getFriend = $_GET['friend'];
	if(!isset($_GET['friend']))
	{
		echo 'Error.';
	}
	else {
		#?get=friend&friend=robotman321&add=before|after
		if($_GET['add'] == 'before')
		{
			$query1  = "SELECT * FROM users WHERE Username='".$getFriend."'";
			$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
			$row1 = mysql_fetch_array($result1);
			$total_useres_with_name = mysql_num_rows($result1);
			if($total_useres_with_name == 0)
			{
			}
			else {
				$FID = $row1['ID'];
				$query  = "SELECT * FROM friends WHERE Asker='".$globalnonid."' AND reqFriend='".$FID."'";
				$result = mysql_query($query) or die('Error : ' . mysql_error());
				$onlyFriendships = mysql_num_rows($result);
				if($onlyFriendships == 1)
				{
					$permGranted = $row['permGranted'];
					if($permGranted == 'yes')
					{
						echo "<img src=\"/images/user.png\" alt=\"\" style=\"float:left;padding-top:2px;\" />&nbsp;<a>".$getFriend." is a Friend</a>";
					}
					else {
						echo "<img src=\"/images/user.png\" alt=\"\" style=\"float:left;padding-top:2px;\" />&nbsp;<a>".$getFriend." is a Friend</a>";
					}
				}
				else if ($globalnonid == $FID)
				{
					echo "<img src=\"/images/user.png\" alt=\"\" style=\"float:left;padding-top:2px;\" />&nbsp;<a>This is You</a>";
				}
				else {
					$query  = "SELECT * FROM friends WHERE Asker='".$globalnonid."'";
					$result = mysql_query($query) or die('Error : ' . mysql_error());
					$numberoffriends = mysql_num_rows($result);
					if($numberoffriends < $allowedFriends)
					{
					echo "<img src=\"/images/adduser.png\" alt=\"\" style=\"float:left;padding-top:2px;\" />&nbsp;<a onclick=\"ajax_loadContent('friends','http://" . $siteroot . "/includes/random_scripts.php?get=friend&amp;friend=".$getFriend."&amp;add=after');return false\" style='cursor:pointer;'>Add ".$getFriend." as a Friend</a>";
					}
					else {
						echo "<img src=\"/images/denieduser.png\" alt=\"\" style=\"float:left;padding-top:2px;\" />&nbsp;<a>Friends Maxed out.</a>";
					}
				}
			}
		}
		else if ($_GET['add'] == 'after')
		{
			$query  = "SELECT * FROM users WHERE Username='".$getFriend."'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$FID = $row['ID'];
			
			$query  = "SELECT * FROM friends WHERE Asker='".$globalnonid."' AND reqFriend='".$FID."'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$onlyFriendships = mysql_num_rows($result);
			if($onlyFriendships == 0 && $globalnonid != $FID)
			{
				$query = sprintf("INSERT INTO friends (reqFriend, Asker, permGranted, reqDate) VALUES ('%s', '%s', '%s', '%s')",
							mysql_real_escape_string($FID, $conn),
							mysql_real_escape_string($globalnonid, $conn),
							mysql_real_escape_string('no', $conn),
							mysql_real_escape_string(time(), $conn));
						mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
				echo "<img src=\"/images/adduser.png\" alt=\"\" style=\"float:left;padding-top:2px;\" />&nbsp;<a>".$getFriend." Added!</a>";
			}
			else {
				echo "Error";
			}
		}
		else  {
			echo 'Error.';
		}
	}
}
if($_GET['get'] == 'friendbar')
{
	if(!isset($_GET['zone']))
	{
		$realTimeZone = '-6';
	}
	else {
		if($_GET['zone'] == '')
		{
			$realTimeZone = '-6';
		}
		else {
			$realTimeZone = $_GET['zone'];
		}
	}
	if(isset($_GET['username']))
	{
		$requestedUser = $_GET['username'];
			$query20  = "SELECT ID, Username FROM users WHERE Username='".$_GET['username']."'";
					$result20 = mysql_query($query20) or die('Error : ' . mysql_error());
					$row20 = mysql_fetch_array($result20);
					$UID = $row20['ID'];
					$Username = $row20['Username'];
			$query = mysql_query("SELECT id FROM friends WHERE Asker='".$UID."'"); 
			$CountFriends = mysql_num_rows($query);
			if($CountFriends == 0)
			{
				
				echo $requestedUser.' Does not have any friends Added!';
			}
			else {
				
				if(isset($_GET['pagenav']))
				{
					if($_GET['pagenav'] == 0 && $CountFriends < 10)
					{
						$friendpagingnext = '';
						$limit=0;
					}
					else {
						$limit = $_GET['pagenav']*5;
						$next = $_GET['pagenav']+1;
						$current = $_GET['pagenav'];
						$previousreal = $_GET['pagenav']-1;
						if($limit < $CountFriends)
						{
							if($current > 0)
							{
								$friendpagingprevious = "<a onclick=\"ajax_loadContent('sidebarfriends','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendbar&username=".$requestedUser."&pagenav=".$previousreal."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'  id=\"sleftusernav\"><span>left</span></a>";
						$navigate = '&nbsp;Navigate&nbsp;';
							}
							else {
								$friendpagingprevious = '';
							}
							if($CountFriends > (($_GET['pagenav']+1)*10))
							{
								$friendpagingnext = "<a onclick=\"ajax_loadContent('sidebarfriends','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendbar&username=".$requestedUser."&pagenav=".$next."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'  id=\"srightusernav\"><span>right</span></a>";
						$navigate = '&nbsp;Navigate&nbsp;';
							}
							else {
								$friendpagingnext = "";
							}
							
						}
						else {
						}
						#$CountFriends;
					}
					$query19 = "SELECT reqFriend FROM friends WHERE Asker='".$UID."' ORDER BY reqDate ASC LIMIT ".$limit.",5";
				}
				else {
					if($CountFriends > 10)
					{
						$friendpagingprevious = '';
						$friendpagingnext = "<a onclick=\"ajax_loadContent('sidebarfriends','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendbar&username=".$requestedUser."&pagenav=1&zone=".$realTimeZone."');return false\" style='cursor:pointer;' id=\"srightusernav\"><span>right</span></a>";
						$navigate = '&nbsp;Navigate&nbsp;';
					}
					$query19 = "SELECT reqFriend FROM friends WHERE Asker='".$UID."' ORDER BY reqDate ASC LIMIT 0,10";
				}
				$result19 = mysql_query($query19) or die('Error : ' . mysql_error());
  				while(list($reqFriend) = mysql_fetch_array($result19))
				{
					echo "<div>\n";
					$query8  = "SELECT ID, Username, avatarActivate, avatarExtension, lastActivity FROM users WHERE ID='".$reqFriend."'";
					$result8 = mysql_query($query8) or die('Error : ' . mysql_error());
					$row8 = mysql_fetch_array($result8);
					$miniUID = $row8['ID'];
					$Username2 = $row8['Username'];
					$avatarActivate = $row8['avatarActivate'];
					$avatarExtension = $row8['avatarExtension'];
					$lastActivity = $row8['lastActivity'];
					$lastActivity = timeZoneChange($lastActivity,$realTimeZone);
						$currentDay = date('d-m-Y',time());
						$midnight = strtotime($currentDay);
						$elevenfiftynine = $midnight+86399;
						$yesterdaymidnight = $midnight-86400;
						if($avatarActivate == yes) 
						{
								echo '<div style="float:left;padding-right:10px;"><img src="http://www.animeftw.tv/images/avatars/user'.$miniUID.'.'.$avatarExtension.'" alt="" width="50px" height="50px" />'."</div>\n";
						}
						else {
								echo '<div style="float:left;"><img src="http://www.animeftw.tv/images/avatars/default.gif" alt="" width="50px" height="50px" />'."</div>\n";
						}
						echo "<div style=\"padding-left:5px;\">".checkUserName($Username2)."<br />Last Active:</div>\n";
						if($lastActivity >= $midnight)
							{
								echo "Today, ".date("g:i a",$lastActivity);
							}
							else if ($lastActivity >= $yesterdaymidnight)
							{
								echo "Yesterday, ".date("g:i a",$lastActivity);
							}
						else {
							echo date("M jS, Y g:i a",$lastActivity);
						}
						echo "<hr width=\"90%\" />\n";
					echo "</div>\n";
						
					}
					
			}
			echo "<div align=\"center\">".$friendpagingprevious . $navigate . $friendpagingnext."</div>";
	}
	else {
		echo 'Error';
	}
}
if($_GET['get'] == 'friendpage')
{
	if(!isset($_GET['zone']))
	{
		$realTimeZone = '-6';
	}
	else {
		if($_GET['zone'] == '')
		{
			$realTimeZone = '-6';
		}
		else {
			$realTimeZone = $_GET['zone'];
		}
	}
	if($_GET['show'] == 'yourfriends')
	{
		// this is to show you your friends, do 10 at a time...
		if(isset($_GET['username']))
		{
			$requestedUser = $_GET['username'];
				$query20  = "SELECT * FROM users WHERE Username='".$_GET['username']."'";
						$result20 = mysql_query($query20) or die('Error : ' . mysql_error());
						$row20 = mysql_fetch_array($result20);
						$UID = $row20['ID'];
						$Username = $row20['Username'];
				$query = mysql_query("SELECT * FROM friends WHERE Asker='".$UID."'"); 
				$CountFriends = mysql_num_rows($query);
				if($CountFriends == 0)
				{
					
					echo 'You have not added any friends, go add some Friends!!!';
				}
				else {
					if(isset($_GET['pagenav']))
				{
					if($_GET['pagenav'] == 0 && $CountFriends < 5)
					{
						$friendpagingnext = '';
						$limit=0;
					}
					else {
						$limit = $_GET['pagenav']*5;
						$next = $_GET['pagenav']+1;
						$current = $_GET['pagenav'];
						$previousreal = $_GET['pagenav']-1;
						if($limit < $CountFriends)
						{
							if($current > 0)
							{
									$friendpagingprevious = "<a onclick=\"ajax_loadContent('yourfriends','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendpage&show=yourfriends&username=".$requestedUser."&pagenav=".$previousreal."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'><img src=\"/images/leftarrow.png\" alt\"\" style=\"float:left;\" /></a>";
							$navigate = '&nbsp;Navigate your Friends&nbsp;';
							}
							else {
								$friendpagingprevious = '';
							}
							if($CountFriends > (($_GET['pagenav']+1)*5))
							{
								$friendpagingnext = "<a onclick=\"ajax_loadContent('yourfriends','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendpage&show=yourfriends&username=".$requestedUser."&pagenav=".$next."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'><img src=\"/images/rightarrow.png\" alt\"\" style=\"float:right;padding-right:20px;\" /></a>";
							$navigate = '&nbsp;Navigate your Friends&nbsp;';
							}
							else {
								$friendpagingnext = "";
							}
							
						}
						else {
						}
						#$CountFriends;
					}
					$query19 = "SELECT id, reqFriend FROM friends WHERE Asker='".$UID."' ORDER BY reqDate ASC LIMIT ".$limit.",5";
				}
				else {
					if($CountFriends > 5)
					{
						$friendpagingprevious = '';
						$friendpagingnext = "<a onclick=\"ajax_loadContent('yourfriends','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendpage&show=yourfriends&username=".$requestedUser."&pagenav=1&zone=".$realTimeZone."');return false\" style='cursor:pointer;'><img src=\"/images/rightarrow.png\" alt\"\" style=\"float:right;padding-right:20px;\" /></a>";
							$navigate = '&nbsp;Navigate your Friends&nbsp;';
					}
					$query19 = "SELECT id, reqFriend FROM friends WHERE Asker='".$UID."' ORDER BY reqDate ASC LIMIT 0,5";
				}
				$result19 = mysql_query($query19) or die('Error : ' . mysql_error());
  				while(list($id,$reqFriend) = mysql_fetch_array($result19))
				{
					echo "<div align=\"center\">\n";
					$query8  = "SELECT * FROM users WHERE ID='".$reqFriend."'";
					$result8 = mysql_query($query8) or die('Error : ' . mysql_error());
					$row8 = mysql_fetch_array($result8);
					$miniUID = $row8['ID'];
					$Username2 = $row8['Username'];
					$avatarActivate = $row8['avatarActivate'];
					$avatarExtension = $row8['avatarExtension'];
					$lastActivity = $row8['lastActivity'];
					$lastActivity = timeZoneChange($lastActivity,$realTimeZone);
						$currentDay = date('d-m-Y',time());
						$midnight = strtotime($currentDay);
						$elevenfiftynine = $midnight+86399;
						$yesterdaymidnight = $midnight-86400;
						echo '<table width="60%">
							<tr>
							<td width="10%">';
						if($avatarActivate == yes) 
						{
								echo '<div style="float:left;padding-right:10px;"><img src="/images/avatars/user'.$miniUID.'.'.$avatarExtension.'" alt="" />'."</div>\n";
						}
						else {
								echo '<div style="float:left;"><img src="/images/avatars/default.gif" alt="" />'."</div>\n";
						}
							echo '</td><td>';
							echo "<div style=\"padding-left:15px;\">".checkUserName($Username2)."<br />Last Active:<br />";
						if($lastActivity >= $midnight)
							{
								echo "Today, ".date("g:i a",$lastActivity);
							}
							else if ($lastActivity >= $yesterdaymidnight)
							{
								echo "Yesterday, ".date("g:i a",$lastActivity);
							}
						else {
							echo date("M jS, Y g:i a",$lastActivity);
						}
						echo '</div>';
							echo '</td>
								<td width="25%">';
								if($UID == $globalnonid)
								{
							echo '<div style="padding-right:15px;">
							<a href="/profile/'.$Username2.'">View Friend</a><br />
							<a onclick="return confirm(\'Are you Sure you want to delete your Friend: '.$Username2.'?\')" href="/edit/friends&action=delete&amp;id='. $id. '">Delete Friend</a></div>';
								}
								else {
								}
							echo '</td>
							</tr>
							</table>';
							echo "<hr width=\"45%\" />\n";
						echo "</div>\n";
						
					}					
						
				}
				echo "<div align=\"center\">".$friendpagingprevious . $navigate . $friendpagingnext."</div>";
		}
		else {
			echo 'Error';
		}
	}
	if($_GET['show'] == 'whoaddedyou')
	{
		// this is to show you your friends, do 10 at a time...
		if(isset($_GET['username']))
		{
			$requestedUser = $_GET['username'];
				$query20  = "SELECT * FROM users WHERE Username='".$_GET['username']."'";
						$result20 = mysql_query($query20) or die('Error : ' . mysql_error());
						$row20 = mysql_fetch_array($result20);
						$UID = $row20['ID'];
						$Username = $row20['Username'];
				$query = mysql_query("SELECT * FROM friends WHERE reqFriend='".$UID."'"); 
				$CountFriends = mysql_num_rows($query);
				if($CountFriends == 0)
				{
					
					echo 'No one has added you :( get out there and make some friends!';
				}
				else {
					if(isset($_GET['pagenav']))
				{
					if($_GET['pagenav'] == 0 && $CountFriends < 5)
					{
						$friendpagingnext = '';
						$limit=0;
					}
					else {
						$limit = $_GET['pagenav']*5;
						$next = $_GET['pagenav']+1;
						$current = $_GET['pagenav'];
						$previousreal = $_GET['pagenav']-1;
						if($limit < $CountFriends)
						{
							if($current > 0)
							{
									$friendpagingprevious = "<a onclick=\"ajax_loadContent('whoaddedyou','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendpage&show=whoaddedyou&username=".$requestedUser."&pagenav=".$previousreal."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'><img src=\"/images/leftarrow.png\" alt\"\" style=\"float:left;\" /></a>";
							$navigate = '&nbsp;Navigate people who have added you&nbsp;';
							}
							else {
								$friendpagingprevious = '';
							}
							if($CountFriends > (($_GET['pagenav']+1)*5))
							{
								$friendpagingnext = "<a onclick=\"ajax_loadContent('whoaddedyou','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendpage&show=whoaddedyou&username=".$requestedUser."&pagenav=".$next."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'><img src=\"/images/rightarrow.png\" alt\"\" style=\"float:right;padding-right:20px;\" /></a>";
							$navigate = '&nbsp;Navigate people who have added you&nbsp;';
							}
							else {
								$friendpagingnext = "";
							}
							
						}
						else {
						}
						#$CountFriends;
					}
					$query19 = "SELECT id, Asker FROM friends WHERE reqFriend='".$UID."' ORDER BY reqDate ASC LIMIT ".$limit.",5";
				}
				else {
					if($CountFriends > 5)
					{
						$friendpagingprevious = '';
						$friendpagingnext = "<a onclick=\"ajax_loadContent('whoaddedyou','http://".$_SERVER['HTTP_HOST']."/includes/random_scripts.php?get=friendpage&show=whoaddedyou&username=".$requestedUser."&pagenav=1&zone=".$realTimeZone."');return false\" style='cursor:pointer;'><img src=\"/images/rightarrow.png\" alt\"\" style=\"float:right;padding-right:20px;\" /></a>";
							$navigate = '&nbsp;Navigate people who have added you&nbsp;';
					}
					$query19 = "SELECT id, Asker FROM friends WHERE reqFriend='".$UID."' AND permGranted='no' ORDER BY reqDate ASC LIMIT 0,5";
				}
				$result19 = mysql_query($query19) or die('Error : ' . mysql_error());
  				while(list($id,$Asker) = mysql_fetch_array($result19))
				{
					echo "<div align=\"center\">\n";
					$query8  = "SELECT * FROM users WHERE ID='".$Asker."'";
					$result8 = mysql_query($query8) or die('Error : ' . mysql_error());
					$row8 = mysql_fetch_array($result8);
					$miniUID = $row8['ID'];
					$Username2 = $row8['Username'];
					$avatarActivate = $row8['avatarActivate'];
					$avatarExtension = $row8['avatarExtension'];
					$lastActivity = $row8['lastActivity'];
					$lastActivity = timeZoneChange($lastActivity,$realTimeZone);
						$currentDay = date('d-m-Y',time());
						$midnight = strtotime($currentDay);
						$elevenfiftynine = $midnight+86399;
						$yesterdaymidnight = $midnight-86400;
						echo '<table width="60%">
							<tr>
							<td width="10%">';
						if($avatarActivate == yes) 
						{
								echo '<div style="float:left;padding-right:10px;"><img src="/images/avatars/user'.$miniUID.'.'.$avatarExtension.'" alt="" />'."</div>\n";
						}
						else {
								echo '<div style="float:left;"><img src="/images/avatars/default.gif" alt="" />'."</div>\n";
						}
							echo '</td><td>';
							echo "<div style=\"padding-left:15px;\">".checkUserName($Username2)."<br />Last Active:<br />";
						if($lastActivity >= $midnight)
							{
								echo "Today, ".date("g:i a",$lastActivity);
							}
							else if ($lastActivity >= $yesterdaymidnight)
							{
								echo "Yesterday, ".date("g:i a",$lastActivity);
							}
						else {
							echo date("M jS, Y g:i a",$lastActivity);
						}
						echo '</div>';
							echo '</td>
								<td width="25%">';
								if($UID == $globalnonid)
								{
							echo '<div style="padding-right:15px;">
							<a href="/profile/'.$Username2.'">Visit '.$Username2.'\'s profile to Add them!</a>
							</div>';
							
								}
								else {
								}
							echo '</td>
							</tr>
							</table>';
							echo "<hr width=\"45%\" />\n";
						echo "</div>\n";
						
					}					
						
				}
				echo "<div align=\"center\">".$friendpagingprevious . $navigate . $friendpagingnext."</div><br />";
		}
		else {
			echo 'Error';
		}
	}
}
if($_GET['get'] == 'forumthreads')
{
	echo 'Coming soon...';
	/*if(!isset($_GET['username']))
	{
		echo 'Error No user specified';
	}
	else {
		$FinalDate3 = time()-86400;
		$ruid = reverseCheckUserNameNumberNoLink($_GET['username']);		

		
		$query4  = "SELECT tid, ttitle, tpid, tfid, tdate FROM forums_threads WHERE tpid='".$ruid."' AND tclosed='0' AND tfid LIKE '%".."%' ORDER BY tdate DESC LIMIT 0,5";
		$result4 = mysql_query($query4) or die('Error : ' . mysql_error());
		while(list($tid,$ttitle,$tpid,$tfid,$tdate) = mysql_fetch_array($result4))
		{
			$ttitle = stripslashes($ttitle);
				$query001 = mysql_query("SELECT ftitle FROM forums_forum WHERE fid='$tfid'");
				$row001 = mysql_fetch_array($query001);
				$ftitle = $row001['ftitle'];
				$query02 = mysql_query("SELECT pbody FROM forums_post WHERE ptid='$tid' AND puid='$ruid' AND pistopic='1'");
				$row02 = mysql_fetch_array($query02);
				$pbody = $row02['pbody'];
				$timeZone = $_GET['zone'];
				$postedDate = timeZoneChange($tdate,$timeZone);
				$postedDate = date("l, F jS, Y, h:i a",$postedDate);
				$finalName .= "<div class='commentsShowProfile'>Posted on: ".$postedDate."<br />Posted in: ".$ftitle."<br />Topic Name: ".$ttitle."<br /> Topic Body: ".$pbody."</div>"."\n";
				$threadlimit++;
		}
	echo '<div class="commentsShowProfile" align="center">Showing the latest 5 threads for '.$_GET['Username'].'</div>';
	echo $finalName."\n";
	}*/
}
if($_GET['get'] == 'forumposts')
{
	echo 'Coming soon...';
}
if($_GET['get'] == 'videoreport')
{
	$postInfo = $_POST['info'];
	$posteid = $_POST['eid'];
	$postuid = $_POST['uid'];
	$postip = $_SERVER['REMOTE_ADDR'];
	if(!is_numeric($posteid))
	{
		echo '<div style="color:#FFF;">Error, report not submitted.</div>';
	}
	else {
		if($postInfo == '' || $postInfo == 'Please Explain what was wrong... *note* Please only report 403, 404 and videos cannot be downloaded errors, anything else is NOT site related. refresh if you only hear audio.')
		{
			echo '<div style="color:#FFF;">Error, no report information submitted. Please don\'t be an idiot and submit some information next time!</div>';
		}
		else {
			if($postuid == '' || !is_numeric($postuid))
			{
				echo '<div style="color:#FFF;">Error, report not submitted.</div>';
			}
			else {
				
				// update the episode to say its been reported!
				$query = 'UPDATE episode SET report=\'' . mysql_escape_string(1) . '\'WHERE id=\'' . $posteid . '\'';
   				mysql_query($query) or die('Error : ' . mysql_error());
				
				// insert new submission
				$postInfo = htmlspecialchars($postInfo);
				$query = sprintf("INSERT INTO video_reports (eid, uid, ip, date, information) VALUES ('%s', '%s', '%s', '%s', '%s')",
					mysql_real_escape_string($posteid, $conn),
					mysql_real_escape_string($postuid, $conn),
					mysql_real_escape_string($postip, $conn),
					mysql_real_escape_string(time(), $conn),
					mysql_real_escape_string($postInfo, $conn));
				mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
				echo '<div style="color:#FFF;">Your video error report has been submitted.</div>';
			}
		}
	}
}
if($_GET['get'] == 'tracker-video')
{
	//get initial Data, the following gets the epid and the userid from the cookie/session.
	$videoid = $_GET['vid'];
	$requesteduserid = $globalnonid;
	
	if($requesteduserid == '')
	{
		echo "<script type=\"text/JavaScript\">alert('An Error occoured; Error T.V.-001');</script>";
	}
	else {
		// link should look like this: ?get=tracker-video&eid=1234&add=before|after
		if($_GET['add'] == 'before')
		{
			// Following queries the tracker database, it looks to see if this episode has been added before by this user.
			$query1  = "SELECT id FROM episode_tracker WHERE eid='".$videoid."' AND uid='".$requesteduserid."'";
			$result1 = mysql_query($query1);
			$tracked_episodes = mysql_num_rows($result1);
			
			// Check if theres an episode there or not.. since there can be duplicates, check for more than one...
			if($tracked_episodes >0)
			{
				echo "<img src=\"/images/added_tracker.png\" alt=\"\" title=\"This episode is already in your Tracker1\" style=\"float:left;padding-top:1px;padding-right:3px;\" />&nbsp;<a>In your Tracker!</a>";
			}
			else {
				echo "<img src=\"/images/add_tracker.png\" alt=\"\" style=\"float:left;padding-top:1px;padding-right:3px;\" />&nbsp;<a onclick=\"ajax_loadContent('tracker1','/includes/random_scripts.php?get=tracker-video&vid=".$videoid."&add=after');return false\" style='cursor:pointer;'>Add to your Tracker!</a>";
			}
		}
		else if($_GET['add'] == 'after')
		{
			$query1  = "SELECT id FROM episode_tracker WHERE eid='".mysql_real_escape_string($videoid)."' AND uid='".mysql_real_Escape_string($requesteduserid)."'";
			$result1 = mysql_query($query1);
			$tracked_episodes = mysql_num_rows($result1);
			
			// Check if theres an episode there or not.. since there can be duplicates, check for more than one...
			if($tracked_episodes >0)
			{
				echo "<img src=\"/images/added_tracker.png\" alt=\"\" title=\"This episode is already in your Tracker2\" style=\"float:left;padding-top:1px;padding-right:3px;\" />&nbsp;<a>In your Tracker!</a>";
			}
			else {
				$query1 = "SELECT sid FROM episode WHERE episode.id = '".mysql_real_escape_string($videoid)."'";
				$result1 = mysql_query($query1);
				$row = mysql_fetch_array($result1);
				$query = sprintf("INSERT INTO episode_tracker (uid, eid, seriesName, dateViewed) VALUES ('%s', '%s', '%s', '%s')",
						mysql_real_escape_string($requesteduserid, $conn),
						mysql_real_escape_string($videoid, $conn),
						mysql_real_escape_string($row['sid'], $conn),
						mysql_real_escape_string(time(), $conn));
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
				echo "<img src=\"/images/added_tracker.png\" alt=\"\" style=\"float:left;padding-top:1px;padding-right:3px;\" />&nbsp;<a>Added to your Tracker!</a>";
			}
		}
		else {
			echo "<script type=\"text/JavaScript\">alert('An Error occoured; Error T.V.-002');</script>";
		}
	}
}
if($_GET['get'] == 'utilities'){
	if($_GET['mode'] == 'comutil'){
		if(!isset($_GET['cid']) || !isset($_GET['username']) || !isset($_GET['uid'])){ //Missing some key data returns NOTHING
		}
		else {
			$cid = $_GET['cid'];
			$username = $_GET['username'];
			$uid = $_GET['uid'];
			if($_GET['stage'] == 'before'){
				echo '<a class="linkopacity" title="Vote Comment Up" onclick="ajax_loadContent(\'ca-'.$cid.'\',\'/includes/random_scripts.php?get=utilities&mode=comutil&stage=after&cid='.$cid.'&username='.$username.'&uid='.$uid.'&vote=up\');return false" style="cursor:pointer;"><img src="/images/tinyicons/thumb_up.png" alt="" border="0" /></a>&nbsp;
		<a class="linkopacity" title="Vote Comment Down" onclick="ajax_loadContent(\'ca-'.$cid.'\',\'/includes/random_scripts.php?get=utilities&mode=comutil&stage=after&cid='.$cid.'&username='.$username.'&uid='.$uid.'&vote=down\');return false" style="cursor:pointer;"><img src="/images/tinyicons/thumb_down.png" alt="" border="0" /></a>&nbsp;
		<a href="/pm/compose/'.$uid.'" title="Will open in a new Tab" target="_blank" class="linkopacity" title="PM User"><img src="/images/tinyicons/email.png" alt="" border="0" /></a>&nbsp;
		<a href="javascript:void(0)" class="linkopacity" title="Report Comment" onclick="alert(\'Feature coming soon!\');"><img src="/images/tinyicons/exclamation.png" alt="" border="0" /></a>&nbsp;
		<a href="/user/'.$username.'" class="linkopacity" title="View User\'s Profile"><img src="/images/tinyicons/user.png" alt="" border="0" /></a>';
			}
			if($_GET['stage'] == 'after'){
				$vote = $_GET['vote'];
				if($vote == 'up'){
					echo '<a class="linkopacity" title="Comment Voted Up" style="cursor:pointer;"><img src="/images/tinyicons/thumb_up.png" alt="" border="0" /></a>&nbsp;
		<a class="linkopacity" title="Thank you for your Vote" style="cursor:pointer;"><img src="/images/tinyicons/thumb_down.png" alt="" border="0" /></a>&nbsp;
		<a href="/pm/compose/'.$uid.'" title="Will open in a new Tab" target="_blank" class="linkopacity" title="PM User"><img src="/images/tinyicons/email.png" alt="" border="0" /></a>&nbsp;
		<a href="javascript:void(0)" class="linkopacity" title="Report Comment" onclick="alert(\'Feature coming soon!\');"><img src="/images/tinyicons/exclamation.png" alt="" border="0" /></a>&nbsp;
		<a href="/user/'.$username.'" class="linkopacity" title="View User\'s Profile"><img src="/images/tinyicons/user.png" alt="" border="0" /></a>';
				}
				if($vote == 'down'){
					echo '<a class="linkopacity" title="Thank you for your Vote" style="cursor:pointer;"><img src="/images/tinyicons/thumb_up.png" alt="" border="0" /></a>&nbsp;
		<a class="linkopacity" title="Comment Voted Down" style="cursor:pointer;"><img src="/images/tinyicons/thumb_down.png" alt="" border="0" /></a>&nbsp;
		<a href="/messages/compose/'.$username.'" class="linkopacity" title="PM User"><img src="/images/tinyicons/email.png" alt="" border="0" /></a>&nbsp;
		<a href="javascript:void(0)" class="linkopacity" title="Report Comment" onclick="alert(\'Feature coming soon!\');"><img src="/images/tinyicons/exclamation.png" alt="" border="0" /></a>&nbsp;
		<a href="/user/'.$username.'" class="linkopacity" title="View User\'s Profile"><img src="/images/tinyicons/user.png" alt="" border="0" /></a>';
				}
			}
		}
	}
}
	include 'closedb.php';
?>