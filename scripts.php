<?php
include_once('includes/global_functions.php');
include_once('includes/classes/pages.class.php');
//include_once('includes/classes/toplist.class.php');

if(isset($_SERVER['HTTP_CF_VISITOR'])){
    $decoded = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
    if($decoded['scheme'] == 'http'){
        // http requests
        $port = 80;
    } else {
        $port = 443;
    }
} else {
    $port = $_SERVER['SERVER_PORT'];
}
if($port == 443) {
	$Host = 'https://img03.animeftw.tv';
} else {
	$Host = 'http://img03.animeftw.tv';
}

$globalvarsquery = mysql_query("SELECT * FROM global_settings WHERE id='1'");

$query = "SELECT `name`, `value` FROM settings WHERE (name = 'videos_active' OR name = 'comments_active' OR name = 'posting_comments' OR name = 'applications_status' OR name = 'application_round')";
$results = mysql_query($query);

$globalvars = array();

while($row = mysql_fetch_assoc($results))
{
	$globalvars[$row['name']] = $row['value'];
}

//$globalvars = mysql_fetch_array($globalvarsquery);
$videos_active = $globalvars['videos_active'];
$comments_active = $globalvars['comments_active'];
$posting_comments = $globalvars['posting_comments'];
$applications_status = $globalvars['applications_status'];
$application_round = $globalvars['application_round'];

if($_SERVER['REMOTE_ADDR'] == '202.156.10.227'){
	header("location: http://www.google.com");
}

if($port == '443')
{
	$sslornot = 'https';
}
else {
	$sslornot = 'http';
}
if(isset($_GET['view']) && $_GET['view'] == 'profiles'){
	include_once 'includes/global_functions.php';
	if($_GET['show'] == 'tooltips'){
		$id = $_GET['id'];
		$query  = "SELECT description FROM series WHERE id='".mysql_real_escape_string($id)."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$verifier = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		$description = stripslashes($row['description']);
		echo '<table><tr><td width="20%" valign="top"><img src="/images/resize/anime/large/'.$id.'.jpg" alt="" /></td><td valign="top"><b>Description:</b><br />'.$description.'</td></tr></table>';
	}	
	if($_GET['show'] == 'user-tips'){
		$id = $_GET['id'];
		$query  = "SELECT Username, gender, ageMonth, ageYear, country, avatarActivate, avatarExtension, personalMsg FROM users WHERE ID='".$id."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$verifier = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		$description = stripslashes($row['description']);
		echo '<table><tr><td width="20%" valign="top"><img src="/images/avatars/user'.$id.'.png" alt="" /></td><td valign="top"><b>Description:</b><br />'.$description.'</td></tr></table>';
	}
	if($_GET['show'] == 'eptips'){
		$id = $_GET['id'];
		$query  = "SELECT epnumber, epPrefix, image, sid FROM episode WHERE id='".mysql_real_escape_string($id)."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$verifier = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		if($row['image'] == 0){
			$imgUrl = $Host . '/video-images/noimage.png';
		}
		else {
			$imgUrl = "{$Host}/video-images/{$row['sid']}/{$id}_screen.jpeg";
		}
		echo '<table><tr><td valign="top"><img src="'.$imgUrl.'" alt="Episode: '.$row['epnumber'].'" width="395px" /></td></tr></table>';
		//echo '<table><tr><td width="20%" valign="top"><img src="/images/resize/anime/large/'.$id.'.jpg" alt="" /></td><td valign="top"><b>Description:</b><br />'.$description.'</td></tr></table>';
	}	
}
if(isset($_GET['view']) && $_GET['view'] == 'user')
{

	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	//include_once 'includes/global_functions.php';
	//include_once 'includes/aftw.class.php';
	$id = $_GET['uid'];
	$con = $_GET['content'];
	if($id != $profileArray[1]){ echo 'GO BACK FROM WHENCE YOU CAME!';}
	else {
		if($_GET['edit'] == 'profile'){
			if($_GET['fieldname'] == 'status'){
				//check the status, see if it matches the latest one in the DB.
				$s = mysql_fetch_array(mysql_query("SELECT status AS s, date AS d FROM status WHERE uid='".mysql_real_escape_string($id)."' ORDER BY id DESC LIMIT 0, 1"));
				$pfin = $s['s']." posted on ".date("M dS, Y",$s['d']);
				if(($con == $pfin) || ($con == 'No Status Updates!')){
					//Status is the same.. silly users clicking things..
					echo $con;
				} else {
					//Not the same status? Well lets update and display the new one!
					$con = strip_tags($con);
					$con = mysql_real_escape_string($con);
					$date = time();
					mysql_query("INSERT INTO status (id, date, status, uid) VALUES (NULL, '".$date."', '".$con."', '".$id."')");
					echo $con." posted on ".date("M dS, Y",$date);
				}
			}
			if($_GET['fieldname'] == 'pm'){
				//We need to check the DB... Since we already limit who can change this.. lets make sure we check again..
				$s = mysql_fetch_array(mysql_query("SELECT personalMsg AS pm FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
				if($con == $s['pm']){
					//Ok, the content is the same as the one in the DB, no changes!
					echo $con;
				}
				else {
					//We have a change??? Lets update!!!
					$con = strip_tags($con); // clean and scrub!
					$con = mysql_real_escape_string($con); //more scrubbing!
					mysql_query("UPDATE users SET personalMsg = '$con' WHERE ID = '".mysql_real_escape_string($id)."'");
					echo $con;
				}					
			}
			if($_GET['fieldname'] == 'aboutme'){
				//First thing is first, check the DB for what we needs!
				$s = mysql_fetch_array(mysql_query("SELECT aboutMe AS am FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
				$am = stripslashes($s['am']); //convert to <br /> tags.. cause the system REALLY needs it..
				if($con == $am){
					//About me in the same, no change!
					$con = nl2br($con);
					echo $con;
				}
				else {
					//Caution changes are a comin'!
					$con = htmlspecialchars($con);
					$con = mysql_escape_string($con); //more scrubbing!
					mysql_query("UPDATE users SET aboutMe = '$con' WHERE ID = '$id'");
					$a = mysql_fetch_array(mysql_query("SELECT aboutMe AS am FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
					$con2 = stripslashes($a['am']);
					echo $con2;					
				}
			}
			if($_GET['fieldname'] == 'interests'){
				//First thing is first, check the DB for what we needs!
				$s = mysql_fetch_array(mysql_query("SELECT interests FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
				$in = stripslashes($s['interests']);
				if($con == $in){
					//interests in the same, no change!
					$in = nl2br($in);
					echo $in;
				}
				else {
					//Caution changes are a comin'!
					$con = htmlspecialchars($con);
					$con = mysql_escape_string($con); //more scrubbing!
					mysql_query("UPDATE users SET interests = '$con' WHERE ID = '".mysql_real_escape_string($id)."'");
					$a = mysql_fetch_array(mysql_query("SELECT interests FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
					$con2 = stripslashes($a['interests']);
					echo $con2;	
				}
			}
			if($_GET['fieldname'] == 'msnAddress' || $_GET['fieldname'] == 'aimName' || $_GET['fieldname'] == 'yahooName' || $_GET['fieldname'] == 'skypeName' || $_GET['fieldname'] == 'icqNumber'){
				$fn = $_GET['fieldname'];
				//First thing is first, check the DB for what we needs!
				$s = mysql_fetch_array(mysql_query("SELECT $fn FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
				$var = stripslashes($s[$fn]);
				if($con == $var){
					//interests in the same, no change!
					$var = nl2br($var);
					echo $var;
				}
				else {
					//Caution changes are a comin'!
					$con = htmlspecialchars($con);
					$con = mysql_escape_string($con); //more scrubbing!
					mysql_query("UPDATE users SET $fn = '$con' WHERE ID = '".mysql_real_escape_string($id)."'");
					$a = mysql_fetch_array(mysql_query("SELECT $fn FROM users WHERE ID='".mysql_real_escape_string($id)."'"));
					$con2 = stripslashes($a[$fn]);
					echo $con2;	
				}
			}
			//echo nl2br($_GET['content'])."&nbsp;".$_GET['fieldname'];
		}
		else {
			echo "ERROR YOU DUMB ASS!!!  ".$_SERVER['REQUEST_URI'];
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'comments')
{
	echo '<div>';
	$person = $_GET['id'];
	$person = mysql_real_escape_string($person);
	if (!isset($_GET['id']) || !is_numeric($person)){
		$finalUsername = 'Error.';
	}
	else {
		$query1   = "SELECT `page_comments`.`comments`, `page_comments`.`dated`, `series`.`seoname`, `series`.`fullSeriesName`, `episode`.`epnumber`, `episode`.`Movie` FROM `page_comments`, `series`, `episode` WHERE `page_comments`.`uid`='".$person."' AND `page_comments`.`type` = 0 AND `episode`.`id`=`page_comments`.`epid` AND `series`.`id`=`episode`.`sid` ORDER BY dated DESC LIMIT 0,10";
		$result1  = mysql_query($query1) or die(mysql_error().$query1);
		$total_comments = mysql_num_rows($result1);
		$finalUsername = ''; 
		$numba = 1;
		if ($total_comments == 0)
		{
			$finalName = '<div class="side-body">This User has not left any Comments.</div>';
		}
		else
		{
			while(list($comments,$dated,$seoname,$fullSeriesName,$epnumber,$Movie) = mysql_fetch_array($result1, MYSQL_NUM))
			{
				$postedDate = strtotime($dated);
				$timeZone = '-6';
				$postedDate = timeZoneChange($postedDate,$timeZone);
				$postedDate = date("l, F jS, Y, h:i a",$postedDate);
				if($Movie != 0)
				{
					// its a movie..
					$videotype = 'movie-';
				}
				else
				{
					$videotype = 'ep-';
				}
				$finalName .= "<div class=\"side-body\">Posted on: ".$postedDate."<br />Posted in: <a href=\"/anime/" . $seoname . "/\" target=\"_blank\" title=\"Opens in a new window\">" . stripslashes($fullSeriesName) . "</a>, Episode #<a href=\"/anime/" . $seoname . "/" . $videotype . $epnumber . "\" target=\"_blank\" title=\"Opens in a new window\">" . $epnumber . "</a><br />Comment:<br />" . stripslashes($comments) . "</div><br />"."\n";
				$numba++;
			}
		}			
	}
	echo '<div align="center" style="padding:4px 5px 5px 5px;">Showing the latest 10 comments for '.checkUserNameNumberNoLink($person).'</div>';
	echo $finalName."\n";
	echo '</div>';
}
if(isset($_GET['view']) && $_GET['view'] == 'friendbar'){
	if(!isset($_GET['zone']))	{
		$realTimeZone = '-6';
	}
	else {
		if($_GET['zone'] == ''){
			$realTimeZone = '-6';
		}
		else {
			$realTimeZone = $_GET['zone'];
		}
	}
	if(isset($_GET['id'])){
		$aid = $_GET['id']; //requested user's ID
			$query = mysql_query("SELECT id FROM friends WHERE Asker='".mysql_real_escape_string($aid)."'"); 
			$u = mysql_fetch_array(mysql_query("SELECT Username FROM users WHERE ID='".mysql_real_escape_string($aid)."'"));
			$CountFriends = mysql_num_rows($query);
			if($CountFriends == 0){				
				echo '<br />'.$u['Username'].' Does not have any friends Added!';
			}
			else {
				if(isset($_GET['pagenav'])){
					if($_GET['pagenav'] == 0 && $CountFriends < 16){
						$fpn = '';
						$limit=0;
					}
					else {
						$limit = $_GET['pagenav']*8;
						$next = $_GET['pagenav']+1;
						$current = $_GET['pagenav'];
						$previousreal = $_GET['pagenav']-1;
						if($limit < $CountFriends){
							if($current > 0){
								$fpp = "<a onclick=\"$('#fb1').load('/scripts.php?view=friendbar&id=".$aid."&pagenav=".$previousreal."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'  id=\"sleftusernav\"><span>left</span></a>";
						$navigate = '&nbsp;<span style="margin-top:-20px;">Navigate</span>&nbsp;';
							}
							else {
								$fpp = '';
							}
							if($CountFriends > (($_GET['pagenav']+1)*8)){
								$fpn = "<a onclick=\"$('#fb1').load('/scripts.php?view=friendbar&id=".$aid."&pagenav=".$next."&zone=".$realTimeZone."');return false\" style='cursor:pointer;'  id=\"srightusernav\"><span>right</span></a>";
						$navigate = '&nbsp;<span style="margin-top:-20px;">Navigate</span>&nbsp;';
							}
							else {
								$fpn = "";
							}
							
						}
						else {
						}
					}
					$query19 = "SELECT u.ID, u.Username, u.avatarActivate, u.avatarExtension FROM friends AS f, users AS u WHERE u.ID=f.reqFriend AND f.Asker='".mysql_real_escape_string($aid)."' ORDER BY u.Username ASC LIMIT ".$limit.",8";
				}
				else {
					if($CountFriends > 16)
					{
						$fpp = '';
						$fpn = "<a onclick=\"$('#fb1').load('/scripts.php?view=friendbar&id=".$aid."&pagenav=1&zone=".$realTimeZone."');return false\" style='cursor:pointer;' id=\"srightusernav\"><span>right</span></a>";
						$navigate = '&nbsp;<span style="margin-top:-20px;">Navigate</span>&nbsp;';
					}
					$query19 = "SELECT u.ID, u.Username, u.avatarActivate, u.avatarExtension FROM friends AS f, users AS u WHERE u.ID=f.reqFriend AND f.Asker='".$aid."' ORDER BY u.Username ASC LIMIT 0,8";
				}
				echo "<div align=\"center\" style=\"padding-top:5px;\">".$fpp . $navigate . $fpn."</div>";
				echo '<div align="center"><br /><table>';
				$i=0;
				$b=0;
				$result19 = mysql_query($query19) or die('Error : ' . mysql_error());
  				while(list($uid,$username,$avatarActivate,$avatarExtension) = mysql_fetch_array($result19))
				{
					if($avatarActivate == 'yes'){
						$av = '<img src="//www.animeftw.tv/images/avatars/user'.$uid.'.'.$avatarExtension.'" alt="" style="max-width:65px;padding:5px;" />';
					}
					else {
						$av = '<img src="//www.animeftw.tv/images/avatars/default.gif" alt="" style="max-width:65px;padding:5px;" />';
					}
					echo '<td align="center" valign="top">';
					echo '<a href="/user/'.$username.'">'.$av.'</a><br />';
					echo '<a href="/user/'.$username.'">'.$username.'</a>';
					echo '</td>';
					if($i % 2){if($b==3){echo '</tr>';}else{echo '</tr><tr>';}$b++;}
					$i++;
				}
			}
			echo '</table>';
	}
	else {
		echo 'Error';
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'settings'){
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(FALSE);
	$profileArray = $Config->outputUserInformation();
	
	include_once('includes/classes/users.class.php');
	$u = new AFTWUser();
	$u->connectProfile($profileArray);
	if(isset($_GET['go']) && $_GET['go'] == 'password'){
		$u->PasswordSettings($profileArray[2],$_GET['id'],$profileArray[1],$profileArray[3]);
	}
	else if(isset($_GET['go']) && $_GET['go'] == 'email'){
		$u->EmailSettings($profileArray[2],$_GET['id'],$profileArray[1],$profileArray[3]);
	}
	else if(isset($_GET['go']) && $_GET['go'] == 'post')
	{
        include_once('includes/classes/settings.class.php');
        $s = new Settings();
		$s->processSiteSettingsUpdate($profileArray);
		exit; // so we don't try and process anything else..
	}
	else if(isset($_GET['go']) && $_GET['go'] == 'notifications'){
        include_once('includes/classes/settings.class.php');
        $s = new Settings(true);
		$s->processSiteSettingsUpdate();
	}
	else 
	{
		echo '
		<div class="fds">
			<div style="display:inline-block;">Settings:</div>
			<div class="user-settings-link-header';
			if(isset($_GET['edit']) && $_GET['edit'] == 'user-settings' || !isset($_GET['edit']))
			{
				echo ' header-active';
			}
			echo '" id="account-setting-header"><a href="#" onClick="loadSettings(' . $_GET['id'] . ',0); return false;">Account</a></div>
			<div class="user-settings-link-header';
			if(isset($_GET['edit']) && $_GET['edit'] == 'site-settings')
			{
				echo ' header-active';
			}
			echo '" id="site-setting-header"><a href="#" onClick="loadSettings(' . $_GET['id'] . ',1); return false;">Site</a></div>
			<div class="user-settings-link-header';
			if(isset($_GET['edit']) && $_GET['edit'] == 'user-sessions')
			{
				echo ' header-active';
			}
			echo '" id="session-setting-header"><a href="#" onClick="loadSettings(' . $_GET['id'] . ',3); return false;">Sessions</a></div>
			';
			if($profileArray[2] == 1 || $profileArray[2] == 2)
			{
				echo '
				<div class="user-settings-link-header';
				if(isset($_GET['edit']) && $_GET['edit'] == 'user-logs')
				{
					echo ' header-active';
				}
				echo '" id="user-setting-header"><a href="#" onClick="loadSettings(' . $_GET['id'] . ',2); return false;">Logs</a></div>';
			}
			echo '
			<div style="display:inline-block;">
				<div style="display:none;" id="settings-loading-bar">
					<img src="' . $Host . '/loading-mini.gif" alt="" />
				</div>
			</div>
		</div><br />';
		if(isset($_GET['edit']) && $_GET['edit'] == 'site-settings')
		{
			$u->UserSiteSettings($profileArray,$_GET['id']);
		}
		else if(isset($_GET['edit']) && $_GET['edit'] == 'user-logs')
		{
			$u->UserLogs($profileArray,$_GET['id']);
		}
		else if(isset($_GET['edit']) && $_GET['edit'] == 'user-sessions')
		{
			$u->UserSessions($profileArray,$_GET['id']);
		}
		else
		{
			$u->UserProfileSettings($profileArray,$_GET['id']);
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'profile')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	
	if(isset($_GET['subview'])){
		if(!isset($_GET['id']) || (!is_numeric($_GET['id']) && (!is_numeric($_GET['id']) && $_GET['subview'] != 'manage-session')))
		{
			echo 'One of these things is not like the other (error).';
		}
		else {
			$uid = mysql_real_escape_string($_GET['id']);
			if($_GET['subview'] == 'friendbutton')
			{
				include_once('includes/classes/users.class.php');
				$Users = new AFTWUser();
				$Users->connectProfile($profileArray);
				$Users->showFriendProfileButton($uid, $profileArray);
			}
			else if($_GET['subview'] == 'friend-notification')
			{
				$query  = "SELECT id FROM friends WHERE Asker='".$profileArray[1]."' AND reqFriend='".mysql_real_escape_string($_GET['id'])."'";
				$result = mysql_query($query) or die('Error : ' . mysql_error());
				$onlyFriendships = mysql_num_rows($result);
				if($onlyFriendships == 0 && $profileArray[1] != $_GET['id'])
				{
					/*$query = sprintf("INSERT INTO friends (reqFriend, Asker, permGranted, reqDate) VALUES ('%s', '%s', '%s', '%s')",
						mysql_real_escape_string($uid, $conn),
						mysql_real_escape_string($profileArray[1], $conn),
						mysql_real_escape_string('no', $conn),
						mysql_real_escape_string(time(), $conn));
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
					//phase one done, next we find out what we just inserted was..
					$result1 = mysql_query("SELECT id FROM friends WHERE Asker = '".$profileArray[1]."' ORDER BY id DESC LIMIT 0, 1") or die('Error : ' . mysql_error());
					$row1 = mysql_fetch_array($result1);
					//we have our target. Proceed.
					$query = sprintf("INSERT INTO notifications (uid, date, type, d1, d2, d3) VALUES ('%s', '%s', '%s', '%s', NULL, NULL)",
						mysql_real_escape_string($uid, $conn),
						mysql_real_escape_string(time(), $conn),
						mysql_real_escape_string('1', $conn),
						mysql_real_escape_string($row1['id'], $conn));
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
					echo "<a href=\"#\" onclick=\"return false;\"><img src='http://".$_SERVER['HTTP_HOST']."/images/adduserv2.png' alt='' /><span>Added to Friends!</span></a>";
					*/
					echo 'Oh snap!';
				}
				else 
				{
					echo "Error";
				}
			}
			else if($_GET['subview'] == 'rmcomments'){
				if(!isset($_GET['s']) || !isset($_GET['uid']) || !isset($_GET['sid']) || !is_numeric($_GET['id'])){
					echo 'There was an error with your query.';
				}
				else {
					$id = mysql_escape_string($_GET['id']);
					if(md5($_GET['uid']) != $_GET['sid']){ // sid needs to equal the uid.. cause then its legit..
						echo 'Error in your query - s1';
					}
					else { //legit request, lets DO this
						if($_GET['s'] == 'b'){ //user clicked to delete a comment
							$query = 'UPDATE page_comments SET is_approved=\'0\' WHERE id=' . $id . '';
			   				mysql_query($query) or die('Error : ' . mysql_error());
						}
						else if($_GET['s'] == 'a'){ //user clicked to UNDO their "delete"
							$query = 'UPDATE page_comments SET is_approved=\'1\' WHERE id=' . $id . '';
			   				mysql_query($query) or die('Error : ' . mysql_error());
						}
						else {
							echo 'There was another error.. try again.';
						}
					}
				}
				echo 'success';
			}
			else if($_GET['subview'] == 'manage-session')
			{
				include_once('includes/classes/sessions.class.php');
				$Session = new Sessions();
				$Session->connectProfile($profileArray);
				if(!isset($_GET['type']))
				{
					echo 'Action not successful.';
				}
				else
				{
					if(isset($_GET['allsessions']))
					{
						$Session->removeSession($_GET['type'],TRUE);
					}
					else
					{
						$Session->removeSession($_GET['type']);
					}
				}
			}
			else {
				echo 'Error: No subview here.';
			}
		}
	}
	else {
		echo 'Error: You have chosen an invalid subroutine.';
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'management')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	
	if(isset($_GET['u'])){
		if($_GET['u'] != $profileArray[1] && !isset($_GET['phpcli-auth'])){
			echo 'ERROR: S-M3';
		}
		else {
			$uid = mysql_escape_string($_GET['u']);
			$q = mysql_fetch_array(mysql_query("SELECT Level_access AS la FROM users WHERE ID='".$uid."'"));
			if($q['la'] == 1 || $q['la'] == 2){
				include_once('includes/classes/config.class.php');
				include_once('includes/classes/management.class.php');
				$m = new AFTWManagement();
				$m->Con($uid,$application_round);
				if(!isset($_GET['node'])){$node = 'error';} else {$node = $_GET['node'];}
				$m->PublicOutput($node);
			}
			else {
				echo 'ERROR: S-M2';
			}
		}
	}
	else { //uvar not set, no furthur
		echo 'ERROR: S-M1';
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'utilities')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	if(isset($_GET['mode']) && $_GET['mode'] == 'comment-votes')
	{
		if(!isset($_GET['cid']) || !is_numeric($_GET['cid']))
		{
			echo 'Error';
		}
		else
		{
			///scripts.php?view=utilities&mode=comment-votes&cid=11706&vote=u
			$query = "SELECT id FROM `ratings` WHERE `rating_id` = 'c" . $_GET['cid'] . "' AND `IP` = '" . $profileArray[1] . "'";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			if($count < 1)
			{
				if(isset($_GET['vote']) && $_GET['vote'] == 'up')
				{
					$query = "INSERT INTO `ratings` (`rating_id`, `rating_num`, `IP`, `v1`) VALUE ('c" . $_GET['cid'] . "', '1', '" . $profileArray[1] . "', '" . $_SERVER['REQUEST_URI'] . "');";
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
					echo 'success';
				}
				else if(isset($_GET['vote']) && $_GET['vote'] == 'down')
				{
					$query = "INSERT INTO `ratings` (`rating_id`, `rating_num`, `IP`, `v1`) VALUE ('c" . $_GET['cid'] . "', '2', '" . $profileArray[1] . "', '" . $_SERVER['REQUEST_URI'] . "');";
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
					echo 'success';
				}
				else
				{
					echo 'Unknown error.';
				}
			}
			else
			{
				echo 'You have already voted on this user.';
			}
		}
	} else if (isset($_GET['mode']) && $_GET['mode'] == 'hide-dot') {
        // action=hide-dot&value=notifiaction-dot
        if (isset($_GET['value']) && $_GET['value'] == 'notification-dot') {
            // Remove the notification dot for the userbar
            // add a cookie that never expires so it does not appear.
            setcookie("enahnced-bar", "1", time() + (60*60*24*365), "/", $Config->ThisDomain, 0, 1);
        } else {
            // Dont send output.
        }
    } else
	{
		echo 'Utility section';
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'tracker')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	include_once('includes/classes/tracker.class.php');
	$tr = new AFTWTracker();
	$tr->connectProfile($Config->outputUserInformation());
	if(isset($_GET['subview']) && $_GET['subview'] == 'add-entry')
	{
		if(!isset($_GET['id']) || !is_numeric($_GET['id']))
		{
			echo 'Fail';
		}
		else
		{
			$tr->addTrackerEntry($_GET['id']);
		}
	}
	else if(isset($_GET['subview']) && $_GET['subview'] == 'add-entry-check')
	{
		// This is just going to throw back a date for our automated process.
		echo '<!-- Success --> on ' . date("F jS Y");
	}
	else
	{
		if(!isset($_GET['id']) && !is_numeric($_GET['id'])){
			echo "Incorrect, error.";
		}
		else {
			$tr->get_vars(40,$profileArray[3],$_GET['id']);
			if(isset($_GET['sub'])){
				$page = $_GET['sub'];
			}
			else {
				$page = '';
			}
			echo $tr->ShowTracker($page);
		}
	}
	/* Things to do
	# - Basic Model, latest adds 
	# - Signatures (tab?), straight forward
	# - [Feature] ability for advanced members to have commenting on entries
	# - [Feature] ability to delete for Advanced Members
	*/
	
	
}
if(isset($_GET['view']) && $_GET['view'] == 'notifications')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	if($profileArray[0] == 0){
	}
	else {
		include_once('includes/classes/notifications.class.php');
		$N = new AFTWNotifications();
		$N->connectProfile($profileArray);
		
		if(!isset($_GET['show'])){
			$N->Output();
		}
		else {
			if(isset($_GET['show']) && $_GET['show'] == 'sprite'){
				echo $N->ShowSprite();
			}
			else if(isset($_GET['show']) && $_GET['show'] == 'profile')
			{
				echo $N->showProfile();
			}
			else {
				echo 'hello there.';
			}
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'watchlist')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	
	include_once('includes/classes/watchlist.class.php');
	if(isset($_GET['function']) && $_GET['function'] == 'submit-form')
	{
		$W = new AFTWWatchlist($profileArray);
		$W->processFormData();
	}
	else
	{
		if($profileArray[0] == 0){
		}
		else 
		{
			$W = new AFTWWatchlist($profileArray);
			$W->Output();
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'donate')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	
	if($profileArray[0] == 0){
	}
	else {
		include_once('includes/classes/donate.class.php');
		$d = new AFTWDonate();
		$d->Build($profileArray);
		$d->ScriptsOutput();
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'profile-comments')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	
	if(!isset($_GET['uid']) || !is_numeric($_GET['uid']))
	{
	}
	else
	{
		include_once('includes/classes/users.class.php');
		$u = new AFTWUser();
		$u->connectProfile($profileArray);
		$u->get_id($_GET['uid']);
		if(isset($_GET['page']))
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 0;
		}
		$u->ShowProfileComments($profileArray[1],$page);
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'toplist')
{
	include_once('includes/classes/config.v2.class.php');
	include_once('includes/classes/toplist.v2.class.php');
	
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$TopList = new toplist();
	$TopList->connectProfile($Config->outputUserInformation());
	$TopList->scriptsFunctions();
}
if(isset($_GET['view']) && $_GET['view'] == 'cart')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	include_once('includes/classes/store.class.php');
	
	if(isset($_POST) && isset($_POST['verify_sign']))
	{
		$ProcessOrder = new ProcessOrders(); 
		$ProcessOrder->connectProfile($Config->outputUserInformation());
		$ProcessOrder->init($_POST);
	}
	else
	{
		$Cart = new Shopping_Cart('aftw_cart');
		$Cart->connectProfile($Config->outputUserInformation());
		
		if ( !empty($_GET['order_code']) && !empty($_GET['quantity']) ) 
		{
			//$quantity = $Cart->getItemQuantity($_GET['order_code'])+$_GET['quantity'];
			$Cart->AddItemToCart($_GET['order_code'], $_GET['quantity']);
			
		}
		
		if ( !empty($_GET['quantity']) ) 
		{
			foreach ( $_GET['quantity'] as $order_code=>$quantity ) {
				$Cart->setItemQuantity($order_code, $quantity);
			}
		}
		
		if ( !empty($_GET['remove']) ) 
		{
			foreach ( $_GET['remove'] as $order_code ) {
				$Cart->setItemQuantity($order_code, 0);
			}
		}
		$Cart->save();
		$Cart->ShowCart();
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'cart-admin')
{
	include_once('includes/classes/config.class.php');
	include_once('includes/classes/store.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$Store = new Store();
	$Store->connectProfile($Config->outputUserInformation());
	$Store->AdminInit();
}
if(isset($_GET['view']) && $_GET['view'] == 'episodes')
{
	include_once('includes/classes/config.class.php');
	include_once('includes/classes/videos.class.php');
	if(!isset($_GET['page']) || !is_numeric($_GET['page']) || !isset($_GET['sid']) || !isset($_GET['epnumber']))
	{
		echo 'Error';
	}
	else
	{
		$Config = new Config();
		$Config->buildUserInformation(TRUE);
		$V = new AFTWVideos();
		$V->connectProfile($Config->outputUserInformation());
		$V->NextEpisodesV2();
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'check-episode')
{
	include_once("includes/classes/config.v2.class.php");
	include_once("includes/classes/episode.v2.class.php");
	if(!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['time']))
	{
		echo 'We were unable to process your request.';
	}
	else
	{
		// let's process the request because they at least gave us some decent information.
		$Episode = new Episode($_GET);
		$output = $Episode->array_recordEpisodeTime();
		if($output['status'] == 200)
		{
			// the output indicated a success, return true.
			echo 'Success';
		}
		else
		{
			// failure.. just failure..
			echo 'Failure ';
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'commentsv2')
{
	
	include_once("includes/classes/config.v2.class.php");
	include_once("includes/classes/comments.v2.class.php");
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	if(isset($_GET['process']))
	{
		// processing data
		$Comments = new Comment();
		$Comments->connectProfile($Config->outputUserInformation());
		$Comments->processComment();
	}
	else
	{
		if(!isset($_GET['epid']))
		{
			echo 'Something went wrong.. oh darn..';
		}
		else
		{
			$Comments = new Comment(NULL,NULL,NULL,NULL,$_GET['epid']);
			$Comments->connectProfile($Config->outputUserInformation());
			// this is where we will post everything
			if(isset($_GET['sub']))
			{
				$Comments->processSubmission();
			}
			else
			{
				$Comments->showComments();
			}
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'tooltip')
{
	if(isset($_GET['show']) && $_GET['show'] == 'episode')
	{
		if(isset($_GET['id']) && is_numeric($_GET['id']))
		{
			include_once('includes/classes/config.class.php');
			include_once('includes/classes/videos.class.php');
			$Config = new Config();
			$Config->buildUserInformation(TRUE);
			$V = new AFTWVideos();
			$V->connectProfile($Config->outputUserInformation());
			
			if(isset($_GET['image']))
			{
				$V->showEpisodeTooltip($_GET['id'],1);
			}
			else
			{
				$V->showEpisodeTooltip($_GET['id']);
			}
		}
		else
		{
			echo 'A wild error appeared!';
		}
	}
	else
	{
		echo 'The function was not defined.';
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'dynamic-load')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	
	if(isset($_GET['page']) && isset($_GET['id']))
	{
		if(isset($_GET['show']) && $_GET['show'] == 'episodes')
		{
			include_once('includes/classes/videos.class.php');
			$V = new AFTWVideos();
			
			$V->connectProfile($Config->outputUserInformation());
			$V->showAvailableVideos(0,$_GET['id'],1,TRUE);
		}
		else if(isset($_GET['show']) && $_GET['show'] == 'watchlist')
		{
			include_once('includes/classes/watchlist.class.php');
			$W = new AFTWWatchlist($Config->outputUserInformation());
			// if the stage is set, it's a new addition.
			if(isset($_GET['stage']) && $_GET['stage'] == 'add-series')
			{
				$W->checkSeriesEntry($_GET['id'],TRUE);
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'view-details')
			{
                $W->viewEntryDetails($_GET['id']);
			}
			else
			{
			}
		}
	}
	else
	{
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'avatar-upload')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	$profileArray = $Config->outputUserInformation();
	
	############ Configuration ##############
	$thumb_square_size      = 200; //Thumbnails will be cropped to 200x200 pixels
	$max_image_size         = 500; //Maximum image size (height and width)
	$thumb_prefix           = "thumb_"; //Normal thumb Prefix
	$destination_folder     = '/home/mainaftw/public_html/images/avatars/'; //upload directory ends with / (slash)
	$jpeg_quality           = 90; //jpeg quality
	##########################################
	
	//continue only if $_POST is set and it is a Ajax request
	if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

		// check $_FILES['ImageFile'] not empty
		if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
				die('Image file is Missing!'); // output error when above checks fail.
		}
	   
		//get uploaded file info before we proceed
		$image_name = $_FILES['image_file']['name']; //file name
		$image_size = $_FILES['image_file']['size']; //file size
		$image_temp = $_FILES['image_file']['tmp_name']; //file temp

		$image_size_info    = getimagesize($image_temp); //gets image size info from valid image file
	   
		if($image_size_info){
			$image_width        = $image_size_info[0]; //image width
			$image_height       = $image_size_info[1]; //image height
			$image_type         = $image_size_info['mime']; //image type
		}else{
			die("Make sure image file is valid!");
		}

		//switch statement below checks allowed image type
		//as well as creates new image from given file
		switch($image_type){
			case 'image/png':
				$image_res =  imagecreatefrompng($image_temp); break;
			case 'image/gif':
				$image_res =  imagecreatefromgif($image_temp); break;          
			case 'image/jpeg': case 'image/pjpeg':
				$image_res = imagecreatefromjpeg($image_temp); break;
			default:
				$image_res = false;
		}

		if($image_res)
		{
			$errormsg = '';
			// check to make sure that the size for a basic member isnt stupid huge..
			if($profileArray[2] == 3 && ($image_height > 150 || $image_width > 150))
			{
				$errormsg .= "Dimensions are greater than 150x150.";
			}
			else
			{
				if($profileArray[1] == $_POST['uid'] || $profileArray[2] == 1 || $profileArray[2] == 2)
				{				
					$target_dir = "/home/mainaftw/public_html/images/avatars/";
					$uploadOk = 1;
					$imageFileType = pathinfo($_FILES["image_file"]["name"],PATHINFO_EXTENSION);
					$filename = 'user' . $profileArray[1] . '.' . $imageFileType;
					$target_file = $target_dir . $filename;
					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["image_file"]["tmp_name"]);
					if($check !== false)
					{
						$uploadOk = 1;
					}
					else
					{
						$errormsg .= "File is not an image.";
						$uploadOk = 0;
					}
					// Check file size
					if($profileArray[2] == 3)
					{
						$filesize = '153600';
					}
					else
					{
						$filesize = '307200';
					}
					if($_FILES["image_file"]["size"] > $filesize)
					{
						$errormsg .= "Sorry, your file is too large. ";
						$uploadOk = 0;
					}
					// Allow certain file formats
					if(strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "gif")
					{
						$errormsg .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed. " . $imageFileType . ' ';
						$uploadOk = 0;
					}
					// Check if $uploadOk is set to 0 by an error
					if($uploadOk == 0)
					{
						echo '<img src="/images/avatars/user' . $_POST['uid'] . '.' . $_POST['extension'] . '" alt="user-avatar" />';
						$errormsg .= "Sorry, your file was not uploaded.";
						// if everything is ok, try to upload file
					}
					else
					{
						if(move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file))
						{
							include_once("includes/classes/ftp.class.php");

							$ftp = new FTP("ftp.images.animeftw.tv");
							$ftp->login("ftpimages", "mui(;Qg_5T~+");
							$ftp->put($target_file, "avatars/" . $filename);
							$errormsg .= "Upload Successful! <br />Populating the CDN may take some time if the image was the same extension.";
							mysql_query("UPDATE `users` SET `avatarActivate` = 'yes', `avatarExtension` = '" . mysql_real_escape_string($imageFileType) . "' WHERE `ID` = " . mysql_real_escape_string($_POST['uid']));
							/* We have succesfully resized and created thumbnail image
							We can now output image to user's browser or store information in the database*/
							echo '<img src="/images/avatars/' . $filename . '" alt="user-avatar" />';
						}
						else
						{
							echo '<img src="/images/avatars/user' . $_POST['uid'] . '.' . $_POST['extension'] . '" alt="user-avatar" />';
							$errormsg .= "Sorry, there was an error uploading your file.";
						}
					}
				}
				else
				{
					$errormsg .= 'You do not have access to this function.';
				}
			}
			echo $errormsg;
			imagedestroy($image_res); //freeup memory
		}
	}

	##### Saves image resource to file #####
	function save_image($source, $destination, $image_type, $quality){
		switch(strtolower($image_type)){//determine mime type
			case 'image/png':
				imagepng($source, $destination); return true; //save png file
				break;
			case 'image/gif':
				imagegif($source, $destination); return true; //save gif file
				break;          
			case 'image/jpeg': case 'image/pjpeg':
				imagejpeg($source, $destination, $quality); return true; //save jpeg file
				break;
			default: return false;
		}
	}
}
if(isset($_GET['view']) && $_GET['view'] == 'reviews')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	include_once('includes/classes/reviews.class.php');
	$R = new Review();
	$R->connectProfile($Config->outputUserInformation());
	$R->processReview();
}
if(isset($_POST['method'])){
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	include_once('includes/classes/management.class.php');
	$m = new AFTWManagement();
	$m->connectProfile($Config->outputUserInformation());
	$m->PostProcess();
}

// A built in function totally designed to be managed from the requests class.
if(isset($_GET['view']) && $_GET['view'] == 'anime-requests')
{
	include_once('includes/classes/config.class.php');
	$Config = new Config();
	$Config->buildUserInformation(TRUE);
	include_once('includes/classes/request.class.php');
	$AR = new AnimeRequest();
	$AR->connectProfile($Config->outputUserInformation());
	$AR->initFunctions(); // We initialize the backend functions handler, this way everything happens IN the class file.
	/*
		Hani I moved all of your functions to the request.class.php script, similar to the cart-admin function where everything happens there instead of here (less clutter, more organized)
	*/
}
if(isset($_GET['view']) && $_GET['view'] == 'api'){
    if(isset($_GET['subview']) && $_GET['subview'] == 'validate-key') {
        if(isset($_POST['key'])) {
            include_once('includes/classes/config.v2.class.php');
            include_once('includes/classes/device.v2.class.php');
            $Config = new Config();
            $Config->buildUserInformation(TRUE);
            $Device = new Device();
            $Device->connectProfile($Config->outputUserInformation());
            $Device->processKeyInput();
        } else {
            echo 'No key was given, please resubmit with a valid key.';
        }
    }
    else {
    }
}
