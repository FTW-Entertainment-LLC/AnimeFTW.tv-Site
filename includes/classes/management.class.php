<?php
/****************************************************************\
## FileName: management.class.php
## Author: Brad Riemann
## Usage: Management Class and Functions
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/
include_once('includes/classes/config.class.php');

class AFTWManagement extends Config {
	var $uid, $appround;

	/******************\
	# Public Functions #
	\******************/

	public function __construct(){
		parent::__construct();
	}

	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}

	public function Con($uid,$appround){
		$this->uid = $uid;
		$this->appround = $appround;
	}

	// This functino is used to process post requests sent to scripts.php, we should only be sending things we understand..
	public function PostProcess(){
		if(isset($_POST['method'])){
			$this->PrcessPostedData();
		}
		else {
			echo 'You did not submit all the details, please try again.';
		}
	}

	public function PublicOutput($node){
		if($node == 'users'){
			echo $this->TopNav();
			if(isset($_GET['stage'])){$stage = $_GET['stage'];}
			else {$stage = '';}
			if(isset($_GET['page'])){$page = $_GET['page'];}
			else {$page = 0;}
			if($this->ValidatePermission(2) == TRUE){
				$this->ManageUsers($stage,$page);
			}
		}
		else if($node == 'comments'){
			echo $this->TopNav();
			if($this->ValidatePermission(10) == TRUE){
				$this->ManageComments();
			}
			/*
			Stages: (left to do)
				- Edit comment
				- Paging comments
			*/
		}
		else if($node == 'episodes'){
			echo $this->TopNav();
			if($this->ValidatePermission(17) == TRUE || isset($_GET['phpcli-auth'])){
				$this->ManageEpisodes();
			}
			/*
			Stages
				- search episode names
				- Search series (full names?)(double conversion)
				- Serach episode numbers
				- Delete option
				- Add episode option
			*/
		}
		else if($node == 'series'){
			if($this->ValidatePermission(21) == TRUE){
				$this->ManageSeries();
			}
		}
		else if($node == 'applications'){
			echo $this->TopNav();
			if($this->ValidatePermission(26) == TRUE){
				$this->ManageApplications();
			}
		}
		else if($node == 'errors'){
			echo $this->TopNav();
			if($this->ValidatePermission(30) == TRUE){
				echo 'This is for the errors panel..';
			}
		}
		else if($node == 'reviews'){
			echo $this->TopNav();
			if($this->ValidatePermission(33) == TRUE){
				include("reviews.management.class.php");
				$Reviews = new Reviews();
				if(isset($_GET['mod']))
				{
					$Reviews->processRequest();
				}
				else
				{
					$Reviews->displayReviews();
				}
			}
		}
		else if($node == 'mail'){
			echo $this->TopNav();
			if($this->ValidatePermission(38) == TRUE){
				echo 'This is for the mail panel..';
			}
		}
		else if($node == 'logs'){
			echo $this->TopNav();
			if($this->ValidatePermission(41) == TRUE){
				echo 'This is for the logs panel..';
			}
		}
		else if($node == 'mywatchlist'){
			echo $this->TopNav();
			if($this->ValidatePermission(44) == TRUE){
				echo 'This is for the My WatchList panel..';
			}
		}
		else if($node == 'forums'){
			if($this->ValidatePermission(48) == TRUE){
				$this->ManageForums();
			}
		}
		else if($node == 'settings'){
			if($this->ValidatePermission(61) == TRUE){
				$this->ManageSettings();
			}
		}
		else {
			echo 'ERROR: S-M3';
		}
	}

	private function TopNav(){
		if($this->ValidatePermission(1) == TRUE){
			echo '<div class="management-nav">';
			if($this->ValidatePermission(2) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=users\'); return false;"><img src="//i.animeftw.tv/management/manage_users.png" height="25px" alt="" title="Manage Users" /></a></div>';
			}
			if($this->ValidatePermission(10) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=comments\'); return false;"><img src="//i.animeftw.tv/management/manage_comments.png" height="25px" alt="" title="Manage Comments" /></a></div>';
			}
			if($this->ValidatePermission(17) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=episodes\'); return false;"><img src="//i.animeftw.tv/management/manage_episodes.png" height="25px" alt="" title="Manage Episodes" /></a></div>';
			}
			if($this->ValidatePermission(21) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=series\'); return false;"><img src="//i.animeftw.tv/management/manage_series.png" height="25px" alt="" title="Manage Series" /></a></div>';
			}
			if($this->ValidatePermission(26) == TRUE){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=applications\'); return false;"><img src="//i.animeftw.tv/management/manage_applications.png" height="25px" alt="" title="Manage Applications" /></a></div>';
			}
			if($this->ValidatePermission(30) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=errors\'); return false;"><img src="//i.animeftw.tv/management/manage_error_reports.png" height="25px" alt="" title="Manage Error Reports" /></a></div>';
			}
			if($this->ValidatePermission(33) == TRUE){
				$query = mysql_query("SELECT COUNT(id) FROM reviews WHERE approved = 0;");
				$count = mysql_result($query, 0);
				if($count > 0)
				{
					$reviewCount = '<div style="display:inline-block;margin:0 0 -10px -10px;"><span id="requestJewelNotif2" style="height:11px; background-color:#f03d25; border-radius:2px; border:1px solid #d83722; -webkit-box-shadow: 0 0 1px 0 rgba(0, 0, 0, 1); line-height:11px; color:#fff; font-size:9px; position:inherit; top:-5px; left:15px; text-align:center; padding:0 1px 0 1px; z-index:1;">' . $count . '</span></div>';
				}
				else
				{
					$reviewCount = '';
				}
				echo '<div style="display:inline-block;"><div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=reviews\'); return false;"><img src="//i.animeftw.tv/management/manage_reviews.png" height="25px" alt="" title="Manage Reviews" /></a></div>' . $reviewCount . '</div>';
			}
			if($this->ValidatePermission(38) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=mail\'); return false;"><img src="//i.animeftw.tv/management/manage_mail.png" height="25px" alt="" title="Manage Emails" /></a></div>';
			}
			if($this->ValidatePermission(41) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=logs\'); return false;"><img src="//i.animeftw.tv/management/manage_logs.png" height="25px" alt="" title="Manage Site Logs" /></a></div>';
			}
			if($this->ValidatePermission(44) == TRUE && 1 == 2){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=mywatchlist\'); return false;"><img src="//i.animeftw.tv/management/manage_my_watchlist.png" height="25px" alt="" title="Manage My WatchList Entries" /></a></div>';
			}
			if($this->ValidatePermission(48) == TRUE){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=forums\'); return false;"><img src="//i.animeftw.tv/management/manage_forum_objects.png" height="25px" alt="" title="Manage Forum Posts and Threads" /></a></div>';
			}
			if($this->ValidatePermission(61) == TRUE){
				echo '<div style="display:inline-block;"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=settings\'); return false;"><img src="//i.animeftw.tv/management/manage_settings.png" height="25px" alt="" title="Manage Settings" /></a></div>';
			}
			echo '</div>';
		}
	}
	/*******************\
	# Private Functions #
	\*******************/

	private function Query($var){
		if($var == 'fl'){
			$iquery = "SELECT COUNT(id) FROM failed_logins";
		}
		else if($var == 'l') {
			$iquery = "SELECT COUNT(id) FROM logins";
		}
		else if($var == 'active') {
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 1";
		}
		else if($var == 'inactive') {
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 0";
		}
		else if($var == 'sus') {
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 2";
		}
		else if($var == 'advanced') {
			$iquery = "SELECT COUNT(ID) FROM users WHERE Level_access = 7";
		}
		else if($var == 'episodes'){
			$iquery = "SELECT COUNT(id) FROM episode";
		}
		else if($var == 'series'){
			$iquery = "SELECT COUNT(id) FROM series";
		}
		else {}
		$query = mysql_query($iquery);
		$total = mysql_result($query, 0);
		return $total;
		//unset $query;
	}

	private function RemoteBuildEpImage($url){
		$file = file_get_contents($url);
		echo $file;
	}

	private function NewSendMail($subject,$to,$body){
		ini_set('sendmail_from', 'no-reply@animeftw.tv');
		$headers = 'From: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'Reply-To: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $body, $headers);
	}

	private function ManageUsers($stage,$page){
		$count = $page;
		//build the info
		$qactive = $this->Query('active');
		$qinactive = $this->Query('inactive');
		$qsuspended = $this->Query('sus');
		$qadvanced = $this->Query('advanced');
		$qflogins = $this->Query('fl');
		$qlogins = $this->Query('l');
		// links
		$lactive = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=active';
		$linactive = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=inactive';
		$lsuspended = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=suspended';
		$ladvanced = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=advanced';
		$lflogins = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=failed-logins';
		$llogins = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=logins';
		$lfindusers = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=findusers';
		if($stage == 'active'){
			$go = 1;
			$rowcount = $qactive;
			$query = "SELECT ID, Username, registrationDate, staticip, Active FROM users WHERE Active = 1 ORDER BY ID DESC LIMIT $count, 30";
			$link = $lactive;
		}
		else if($stage == 'inactive'){
			$go = 1;
			$rowcount = $qinactive;
			$query = "SELECT ID, Username, registrationDate, staticip, Active FROM users WHERE Active = 0 ORDER BY ID DESC LIMIT $count, 30";
			$link = $linactive;
		}
		else if($stage == 'suspended'){
			$go = 1;
			$rowcount = $qsuspended;
			$query = "SELECT ID, Username, registrationDate, staticip, Active FROM users WHERE Active = 2 ORDER BY ID DESC LIMIT $count, 30";
			$link = $lsuspended;
		}
		else if($stage == 'advanced'){
			$go = 1;
			$rowcount = $qadvanced;
			$query = "SELECT ID, Username, registrationDate, staticip, Active FROM users WHERE Level_access = 7 ORDER BY ID DESC LIMIT $count, 30";
			$link = $ladvanced;
		}
		else if($stage == 'failed-logins'){
			$go = 2;
			$rowcount = $qflogins;
			$query = "SELECT id, name, password, ip, date FROM failed_logins ORDER BY id DESC LIMIT $count, 30";
			$link = $lflogins;
		}
		else if($stage == 'logins'){
			$go = 3;
			$rowcount = $qlogins;
			$query = "SELECT id, ip, date, uid, agent FROM logins ORDER BY id DESC LIMIT $count, 30";
			$link = $llogins;
		}
		else if($stage == 'modedit'){
			$go = 4;
		}
		else if($stage == 'findusers'){
			$go = 5;
			$link = $lfindusers;
		}
		else {$go = 0;}
		if($go == 4){
			if(isset($_GET['id'])){
				$uid = checkUserNameNumberNoLink(mysql_real_escape_string($_GET['id']));
				$cid = mysql_real_escape_string($_GET['id']);
				if(isset($_GET['modaction']) && $_GET['modaction'] == 'delete'){
					$error = "<div class=\"redmsg\">$uid 's account was deleted successfully (NOT!)- This function will never work.. Super Failsafe!</div><br />";
					$fscript = 'Delete User';
					//$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $globalnonid . '\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'suspend'){
					$error = "<div class=\"redmsg\">The account suspension of $uid has been completed successfully.</div><br />";
					$fscript = 'Suspend User';
					$query = 'UPDATE users SET Active = 2 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'fban'){
					$error = "<div class=\"redmsg\">You have revoked $uid's forum access successfully.</div><br />";
					$fscript = 'Forum Ban';
					$query = 'UPDATE users SET forumBan = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'cban'){
					$error = "<div class=\"redmsg\">$uid's Comment posting abilities have been denied.</div><br />";
					$fscript = 'Comment Ban';
					$query = 'UPDATE users SET postBan = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'pmban'){
					$error = "<div class=\"redmsg\">$uid will no longer be able to send PMs.</div><br />";
					$fscript = 'PM Ban';
					$query = 'UPDATE users SET messageBan = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'unsuspend'){
					$error = "<div class=\"redmsg\">$uid has been unsuspended successfully.</div><br />";
					$fscript = 'UnSuspend User';
					$query = 'UPDATE users SET Active = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else {
					$error = 'Error: Your request was not processed.';
					$validfun = FALSE;
				}
				if($validfun == FALSE){}
				else {
					mysql_query($query) or die('Error : ' . mysql_error());
					$this->ModRecord($fscript);
				}
			}
			else{
				$error = 'Error: You submitted an invalid function, please try again.';
			}
		}

		//build the main nav
		echo '<table width="100%">';
		echo '<tr>';
		if($this->ValidatePermission(3) == TRUE){
			echo '<td colspan="3"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$llogins.'\');return false;">Logins</a></div></td>';
			$logins = '<td colspan="3"><div align="center">'.$qlogins.'</div></td>';
		}
		else {echo '<td colspan="3">&nbsp;</td>';$logins = '<td colspan="3">&nbsp;</td>';}
		if($this->ValidatePermission(4) == TRUE){
			echo '<td colspan="3"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$lflogins.'\');return false;">Failed Logins</a></div></td>';
			$flogins = '<td colspan="3"><div align="center">'.$qflogins.'</div></td>';
		}
		else {echo '<td colspan="3">&nbsp;</td>';$flogins = '<td colspan="3">&nbsp;</td>';}
		if($this->ValidatePermission(5) == TRUE){
			echo '<td colspan="3"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$lfindusers.'\');return false;">Find Users</a></div></td>';
		}
		else {echo '<td colspan="3">&nbsp;</td>';}
		echo '</tr><tr>';
		echo $logins.$flogins;
		echo '<td colspan="3">&nbsp;</td>';
		echo '</tr><tr>';
		if($this->ValidatePermission(6) == TRUE){
			echo '<td colspan="2"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$lactive.'\');return false;">Active</a></div></td>';
			$active = '<td colspan="2"><div align="center">'.$qactive.'</div></td>';
		}
		else {echo '<td colspan="2">&nbsp;</td>';$active = '<td colspan="2">&nbsp;</td>';}
		if($this->ValidatePermission(7) == TRUE){
			echo '<td colspan="2"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$linactive.'\');return false;">Inactive</a></div></td>';
			$inactive = '<td colspan="2"><div align="center">'.$qinactive.'</div></td>';
		}
		else {echo '<td colspan="2">&nbsp;</td>';$inactive = '<td colspan="2">&nbsp;</td>';}
		echo '<td>&nbsp;</td>';
		if($this->ValidatePermission(8) == TRUE){
			echo '<td colspan="2"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$lsuspended.'\');return false;">Suspended</a></div></td>';
			$suspended = '<td colspan="2"><div align="center">'.$qsuspended.'</div></td>';
		}
		else {echo '<td colspan="2">&nbsp;</td>';$suspended = '<td colspan="2">&nbsp;</td>';}
		if($this->ValidatePermission(9) == TRUE){
			echo '<td colspan="2"><div align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$ladvanced.'\');return false;">Advanced</a></div></td>';
			$advanced = '<td colspan="2"><div align="center">'.$qadvanced.'</div></td>';
		}
		else {echo '<td colspan="2">&nbsp;</td>';$advanced = '<td colspan="2">&nbsp;</td>';}
		echo '</tr><tr>';
		echo $active.$inactive;
		echo '<td>&nbsp;</td>';
		echo $suspended.$advanced;
		echo '</tr></table>';
		if(isset($error)){echo $error;}
		// build our list
		if($go >= 1 && $go <= 3){
			echo '<div style="height:395px;overflow-y:scroll;overflow-x:none;">';
			$result  = mysql_query($query) or die('Error : ' . mysql_error());
			$paging = $this->InternalPaging($rowcount,30,$count,$link);
			if($go == 1){
				echo $paging;
				while(list($ID,$username,$registrationDate,$staticip,$active) = mysql_fetch_array($result)){
					$this->BuildList($ID,$username,$registrationDate,$staticip,$active,$go,$link);
				}
			}
			else if($go == 2){
				echo $paging;
				while(list($id,$name,$password,$ip,$date) = mysql_fetch_array($result)){
					$this->BuildList($id,$name,$password,$ip,$date,$go,$link);
				}
			}
			else {
				echo $paging;
				while(list($id,$ip,$date,$uid,$agent) = mysql_fetch_array($result)){
					$this->BuildList($id,$ip,$date,$uid,$agent,$go,$link);
				}
			}
			echo '</div>';
		}
		else {
			if($go == 4){}
			else if($go == 5 && $this->ValidatePermission(5) == TRUE){

				echo '<div class="tbl"><br /><div align="center">
				<form method="GET" name="usersearch" id="usersearch" onSubmit="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=findusers&part=after&this=bogus\' + getFormElementValuesAsString(document.forms[\'usersearch\']));">
<input type="hidden" name="id" value="12356">
				<table width="500px"><tr><td align="right"><label class="left" for="username" style="margin: 0px 0px 0px 0px;color:#555555;">Username:</label></td>
				<td align="left">
				<input name="username" id="username" type="text" class="loginForm" style="width:154px;" />
				<input name="submit" type="button" class="button_2" value="Submit" onclick="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=findusers&part=after&this=bogus\' + getFormElementValuesAsString(document.forms[\'usersearch\']));" />
				</td></tr>
				<tr><td colspan="2">
				<div class="cb"></div>
				<div style="margin: 5px 0px 0px 100px;">
				<div align="center" style="font-size: 9px;">Use the above form, to find users on the site.</div>
				</td></tr></table></form></div></div>';
				if($_GET['part'] == 'after'){
					$query = "SELECT ID, Username, Email, lastActivity, staticip, Active, Level_access, forumBan, messageBan, postBan FROM users WHERE Username LIKE '%".mysql_real_escape_string($_GET['username'])."%'";
					$result  = mysql_query($query) or die('Error : ' . mysql_error());
					echo '<div style="height:340px;overflow-y:scroll;overflow-x:none;">';
					while(list($ID,$Username,$Email,$lastActivity,$staticip,$Active,$Level_access,$forumBan,$messageBan,$postBan) = mysql_fetch_array($result)){
						$this->BuildList($ID,$Username,NULL,$staticip,$Active,$go,$link,$Email,$lastActivity,$Level_access,$forumBan,$messageBan,$postBan);
					}
					echo '</div>';
				}
			}
			else {
				echo '<div style="padding:10px;" align="center">Please use the buttons above to navigate through the User Management System.</div>';
			}
		}
	}

	private function ManageComments(){
		if($this->ValidatePermission(10) == TRUE){
			if(!isset($_GET['do'])){
				if(!isset($_GET['mode']) && $this->ValidatePermission(11) == TRUE){
					$query = "SELECT * from page_comments WHERE type = 0 ORDER by id DESC LIMIT 0, 60";
					$result = mysql_query($query);
					$count = mysql_num_rows($result);
					if ($count>0) {
						echo '<br /><div style="height:443px;overflow-y:scroll;overflow-x:none;">';
						echo "<table cellpadding='1' cellspacing='0' width='100%'>";
						echo "<tr class='minortext'><td>Page</td><td>Ep #</td><td>Name</td></td><td>Comment</td><td>Dated</td><td colspan='3' align='center' width='15px'>Actions</td></tr>";
						while($myrow = mysql_fetch_array($result))
						{
							echo "<tr>\n";
							$series_ID = $myrow['page_id'];
							echo "<td class='minortext'>".checkSeries($myrow['epid'])."</td>\n";
							echo "<td class='minortext'>".checkEpisode($myrow['epid']). "</td>\n";
							echo "<td class='minortext'>". checkUserNameNumber($myrow['uid']). "</td>\n";
							$comments = $myrow['comments'];
							if (strlen($comments)>60) {$comments = substr($comments,0,56). " .."; }
							echo "<td class='minortext'>". $comments. "</td>\n";
							$when = explode(" ",$myrow['dated']);
							echo "<td class='minortext'>". $when[0]. "</td>\n";

							// do you want to look - if there's an admin comment or long comment ...
							$see_me = "";
							if ( ($myrow['admin_comment']!="") || (strlen($myrow['comments'])>60) ) {$see_me = 1;}
							if($this->ValidatePermission(11) == TRUE){
								echo "<td>";
								if ($see_me == 1) {
									echo "<a href=\"#\"><img src='//i.animeftw.tv/editor/magnify3.gif' width='16' height='15' alt='show in full' border='0'/></a>";
								} else {
									echo "&nbsp;";
								}
								echo "</td>";
							}
							else {
								echo '<td>&nbsp;</td>';
							}
							if($this->ValidatePermission(12) == TRUE){
								echo "<td>";
								echo "<a href=\"#\" onClick=\"javascript:ajax_loadContent('manageedit','http://".$_SERVER['HTTP_HOST']."/scripts.php?view=management&u=".$this->uid."&node=comments&do=edit&id=".$myrow['id']."'); return false;\">";
								echo "<img src='//i.animeftw.tv/editor/edit.gif' width='16' height='15' alt='edit' border='0' />";
								echo "</a>";
								echo "</td>";
							}
							else {
								echo '<td>&nbsp;</td>';
							}
							$cnf = "Are you sure you want to delete the post by ";
							if ($myrow['name']==""){
								$cnf.= "anonymous?";
							} else {
								$cnf.= $myrow['name']. "?";
							}
							if($this->ValidatePermission(13) == TRUE){
								echo "<td>";
								echo "<a href=\"#\" onClick=\"javascript:ajax_loadContent('manageedit','http://".$_SERVER['HTTP_HOST']."/scripts.php?view=management&u=".$this->uid."&node=comments&do=delete&id=".$myrow['id']."&confirm=before'); return false;\">";
								echo "<img src='//i.animeftw.tv/editor/delete.gif' width='16' height='15' alt='delete' border='0' />";
								echo "</a>";
								echo "</td>\n";
							}
							else {
								echo '<td>&nbsp;</td>';
							}
							echo "</tr>";
						}
						echo "</table><br/>\n";
						echo "</div>";
					}
				}
				else {
				}
			}
			else {
				if($_GET['do'] == 'edit' && $this->ValidatePermission(12) == TRUE){
				}
				else if($_GET['do'] == 'delete' && $this->ValidatePermission(13) == TRUE){
					if(!isset($_GET['id']) && !is_numeric($_GET['id'])){
						echo 'Error: Invalid ID entered.';
					}
					else {
						if($_GET['confirm'] == 'before'){
							echo '<div align="center"><h4>Confirm deletion for this comment?</h4><h5><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=comments&do=delete&id='.$_GET['id'].'&confirm=after\'); return false;">Yes</a></h5><h3><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=comments\'); return false;">No</a></h3></div>';
						}
						else if($_GET['confirm'] == 'after'){
							$query  = "DELETE FROM page_comments WHERE id = '".$_GET['id']."'";
							mysql_query($query) or die('Error : ' . mysql_error());
							$this->ModRecord('Delete Comment');
							echo "<div class=\"redmsg\">Comment Deleted Successfully.</div><br />";
							echo '<div align="center"><a href="#" onclick="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=comments\'); return false;"><h3>Continue</h3></a></div>';
						}
						else {
						}
					}
				}
				else if($_GET['do'] == 'view' && $this->ValidatePermission(11) == TRUE){
				}
				else {
				}
			}
		}
	}

	private function ManageApplications(){
		if(!isset($_GET['go'])){
			if(isset($_GET['action'])){
				$id = mysql_real_escape_string($_GET['id']);
				if($_GET['action'] == 'status'){
					if(isset($_GET['status']) && $_GET['status'] == 'pending'){
						$error = "<div class=\"redmsg\">Application has been set to Pending.</div>";
						$fscript = 'Set Application Pending';
						$query = 'UPDATE applications_submissions SET Status = \'Pending\' WHERE id = '.$id;
					}
					else if(isset($_GET['status']) && $_GET['status'] == 'accepted'){
						$error = "<div class=\"redmsg\">Application has been Accepted.</div>";
						$fscript = 'Sep Application Accepted';
						$query = 'UPDATE applications_submissions SET Status = \'Accepted\' WHERE id = '.$id;
					}
					else if(isset($_GET['status']) && $_GET['status'] == 'denied'){
						$error = "<div class=\"redmsg\">Application has been denied.</div>";
						$fscript = 'Set Application Denied';
						$query = 'UPDATE applications_submissions SET Status = \'Denied\' WHERE id = '.$id;
					}
					else if(isset($_GET['status']) && $_GET['status'] == 'underreview'){
						$error = "<div class=\"redmsg\">Application changed to Under Review.</div>";
						$fscript = 'Set Application Under Review';
						$query = 'UPDATE applications_submissions SET Status = \'Under Review\' WHERE id = '.$id;
					}
					else {
					}
				}
				else if ($_GET['action'] == 'delete'){
					if(!is_numeric($_GET['id']) || !isset($_GET['id'])){
						echo 'ERROR: ID issue.';
					}
					else {
						$error = "<div class=\"redmsg\">Application has been deleted.</div>";
						$fscript = 'Delete Application';
						$query = 'DELETE FROM applications_submissions WHERE id = '.$id;
					}
				}
				else {
				}
				mysql_query($query) or die('Error : ' . mysql_error());
				$this->ModRecord($fscript);
			}
			if(isset($error)){$height = "425px";echo $error;}else{$height = "450px";}
			echo '<br /><div>';
			echo '<div style="float:right;width:130px;" align="center"><b>App Functions</b></div>';
			echo '<div style="float:right;width:122px;" align="left"><span style="padding-left:20px;"><b>Age</b></span></div>
			<div style="float:right;width:122px;" align="center"><b>Company</b></div>
			<div style="float:right;width:137px;" align="left"><b>Position Applied for</b></div>
			<div style="width:122px;" align="center"><b>Username</b></div>';
			echo '</div>';
			echo '<div style="height:'.$height.';overflow-y:scroll;overflow-x:none;">';
			$query = "SELECT a.id, a.positionID, a.username, a.company, a.Age, a.Status, u.ID FROM applications_submissions AS a, users AS u WHERE a.appRound = ".$this->appround." AND u.Username=a.username ORDER BY a.id DESC";
			$result  = mysql_query($query) or die('Error : ' . mysql_error());
			while(list($id,$positionID,$username,$company,$Age,$Status,$uid) = mysql_fetch_array($result)){
				$query = mysql_query("SELECT COUNT(id) FROM applications_sectests WHERE uid=$uid");
				$total = mysql_result($query, 0);
				if($Status == 'Pending'){$style = '#009933;color:#fff';}else if($Status == 'Accepted'){$style = '#0E9FCE';}else if($Status == 'Under Review'){$style = '#FFCC00';}else{$style = '#CC3300;color:#fff';}
				if($Age == ''){$Age = '&nbsp;';}
				echo '<br /><div style="background-color:'.$style.';padding:5px;">';
				echo '<div style="float:right;width:130px;background-color:#fff;color:#000;" align="center"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=applications&go=view&id='.$id.'\'); return false;">View</a> |
				<a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=applications&go=view&id='.$id.'\'); return false;">Edit</a> | ';
				if($total > 0){
					echo '<a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->uid.'&node=applications&go=sectest&id='.$uid.'\'); return false;">Sec Test</a>';
				}
				else {
					echo '<span style="color:#000">Sec Test</span>';
				}
				echo '</div>
				<div style="float:right;width:122px;" align="center">'.$Age.'</div>
				<div style="float:right;width:122px;" align="center">'.$company.'</div>
				<div style="float:right;width:137px;padding-left:3px;" align="center">'.$positionID.'</div>';
				$q = mysql_fetch_array(mysql_query("SELECT ID, Email FROM users WHERE Username = '".$username."'"));
				echo '<div style="width:122px;background-color:#fff;color:#000;" align="center"><a href="#" title="'.$q['Email'].'" onclick="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=findusers&part=after&username='.$username.'\');">'.$username.'</a></div>';
				echo '</div>';

			}
			echo '</div>';
		}
		else {
			if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
				echo 'Error: ID Not set.';
			}
			else {
				if($_GET['go'] == 'view'){
					echo '<div><h3>Showing application details</h3>';
					$query  = "SELECT * FROM applications_submissions WHERE id= '{$_GET['id']}'";
				  	$result = mysql_query($query) or die('Error : ' . mysql_error());
				   	list($id, $positionID, $username, $company, $reqInformation, $Age, $Status) = mysql_fetch_array($result, MYSQL_NUM);
					$reqInformation = stripslashes($reqInformation);
					$reqInformation = nl2br($reqInformation);
					if($Age == ''){$Age = '--/--/---';}
					echo '<div>';
					echo '<div style="padding:5px;"><div style="width:130px;float:left;padding-right:10px;" align="right"><b>Username:</b></div><div style="width:510px;" align="left">'.$username.'</div></div>';
					echo '<div style="padding:5px;"><div style="width:130px;float:left;padding-right:10px;" align="right"><b>Position:</b></div><div style="width:510px;" align="left">'.$positionID.'</div></div>';
					echo '<div style="padding:5px;"><div style="width:130px;float:left;padding-right:10px;" align="right"><b>Application Status:</b></div><div style="width:510px;" align="left">'.$Status.'</div></div>';
					echo '<div style="padding:5px;"><div style="width:130px;float:left;padding-right:10px;" align="right"><b>Application:</b></div><div style="width:640px;height:200px;overflow-y:scroll;overflow-x:none;" align="center">'.$reqInformation.'</div></div>';
					echo '<div style="padding:5px;"><div style="width:130px;float:left;padding-right:10px;" align="right"><b>Age:</b></div><div style="width:510px;" align="left">'.$Age.'</div></div>';
					echo '<div style="padding:5px;"><div style="width:130px;float:left;padding-right:10px;" align="right"><b>Company:</b></div><div style="width:510px;" align="left">'.$company.'</div></div>';
					echo '</div>';
					echo '<br /><div align="center">Actions: <input name="submit" type="button" class="button_2" value="Back" onclick="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications\'); return false;" />&nbsp;';
					echo '<input name="submit" type="button" class="button_2" value="Delete" onclick="if(confirm(\'This will delete this application, are you sure?\')) ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications&action=delete&id='.$id.'\'); return false;" /><br /><br />';
					echo '<input name="submit" type="button" class="button_2" value="Pending" onclick="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications&id='.$id.'&action=status&status=pending\'); return false;" />';
					echo '<input name="submit" type="button" class="button_2" value="Under Review" onclick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications&id='.$id.'&action=status&status=underreview\'); return false;" />';
					echo '<input name="submit" type="button" class="button_2" value="Accepted" onclick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications&id='.$id.'&action=status&status=accepted\'); return false;" />';
					echo '<input name="submit" type="button" class="button_2" value="Denied" onclick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications&id='.$id.'&action=status&status=denied\'); return false;" />';
					echo '</div>';
					echo '</div>';
				}
				else if($_GET['go'] == 'sectest'){
					$q = mysql_fetch_array(mysql_query("SELECT s.date, s.q1, s.q2, s.q3, s.q4, s.q5, s.q6, s.q7, s.q8, s.q9, s.q10, s.q11, s.q12, s.q13, s.q14, s.q15, s.q16, s.q17, u.Username FROM applications_sectests AS s, users AS u WHERE u.ID=s.uid AND s.uid = '".mysql_real_escape_string($_GET['id'])."'"));
					echo '<div>Viewing the Security test for '.$q['Username'].'<br /><br />';
					echo '<div style="height:435px;overflow-y:scroll;overflow-x:none;">';
					$qarray = array($q['q1'],$q['q2'],$q['q3'],$q['q4'],$q['q5'],$q['q6'],$q['q7'],$q['q8'],$q['q9'],$q['q10'],$q['q11'],$q['q12'],$q['q13'],$q['q14'],$q['q15'],$q['q16'],$q['q17']);
					$i = 1;
					foreach ($qarray as &$value) {
						echo 'Q'.$i.':&nbsp;'.$value.'<br /><br />';
						$i++;
					}
					unset($value);
					echo '</div>';
					echo '<div align="center">Actions: <input name="submit" type="button" class="button_2" value="Back" onclick="ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=applications\'); return false;" />&nbsp;';
					echo '</div>';
				}
				else {
				}
			}

		}
	}

	private function ManageEpisodes(){
		// set the global variables to this function
		$limit = 60; //limit to 30 rows
		$link = 'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=episodes'; //we needed a link, cause were awesome.
		if(isset($_GET['sname'])){
			$link .= "&sname=".$_GET['sname'];
			$_SERVER['HTTP_REFERER'] = $link;
		}

		if(!isset($_GET['eid']) && !isset($_GET['edit'])){
			//some small variable sets for this part of the function
			$rowcount = $this->Query('episodes'); //grab the total amount of episodes!
			if(isset($_GET['page'])){$start = $_GET['page'];}else {$start = 0;} //clean start points, cause everyone loves them
			if(isset($_GET['sname'])){
				$sname = htmlentities($_GET['sname']);
				$query = "SELECT id, epnumber, epname, seriesname, image FROM episode WHERE seriesname = '".$sname."' ORDER BY id DESC LIMIT ".$start.", ".$limit;
			}
			else {
				$query = "SELECT id, epnumber, epname, seriesname, image FROM episode ORDER BY id DESC LIMIT ".$start.", ".$limit;
			}
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			echo '<div style="padding-top:5px;">';
			echo '<div style="float:right;padding-right:80px;"><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'&edit=add&step=before\'); return false;"><b>Add Episode</b></a></div>';
			$this->InternalPaging($rowcount,$limit,$start,$link); //($count,$perpage,$start,$link)
			echo '</div>';
			//begin!
			echo '<div class="Results" id="Results" style="display:none;"></div>';
			echo '<div class="erow"><div class="eleftcol" align="center"><b>Episode Title</b></div><div class="eepcol" align="center"><b>Ep#</b></div><div class="eseriescol" align="center"><b>Series</b></div><div><b>Functions</b></div></div>';
			echo '<div style="height:435px;overflow-y:scroll;overflow-x:none;">';
			$i = 0;$style1 = 'style="background-color:#E5E5E5;"';$style2 = 'style="background-color:#B8EAFA;"';
			while($r = mysql_fetch_array($result)){
				if($i % 2){$style = $style1;}else {$style = $style2;}
				echo '<div class="erow">';
				echo '<div class="eleftcol" '.$style.'>'.$r['epname'].'</div>';
				echo '<div class="eepcol" align="center" '.$style.'>'.$r['epnumber'].'</div>';
				echo '<div class="eseriescol" align="center" '.$style.'>'.$r['seriesname'].'</div>';
				if($r['image'] == 0){
					$addImageIcon = ' | <a href="#" onClick="$(\'#Results\').load(\''.$link.'&edit=image-add&epid='.$r['id'].'\'); return false;">IC</a>';
				}
				else {
					$addImageIcon = ' | <a href="#" onClick="$(\'#Results\').load(\''.$link.'&edit=image-add&epid='.$r['id'].'\'); return false;">IC</a>';
					//$addImageIcon = ' | <a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'&edit=image-add&epid='.$r['id'].'\'); return false;">IC</a>';
				}
				echo '<div class="eactioncol" align="center" '.$style.'><a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'&eid='.$r['id'].'&stage=before\'); return false;" title="Edit this Episode"><img src="//i.animeftw.tv/editor/edit.gif" height="11px" alt="" /></a> | <a href="#"><img src="//i.animeftw.tv/editor/delete.gif" alt="" height="11px" /></a>'.$addImageIcon.'</div>';
				echo '</div>';
				$i++;
			}
			echo '</div>';
		}
		else {
			if(isset($_GET['eid'])){
				if($_GET['stage'] == 'before'){
				// We want to build the edit screen prior to doing anything, just so people don't flip their shit..
				$query  = "SELECT id, epnumber, seriesname, epname, vidheight, vidwidth, epprefix, subGroup, Movie, videotype FROM episode WHERE id= '{$_GET['eid']}'";
			   $result = mysql_query($query) or die('Error : ' . mysql_error());
			   list($id, $epnumber, $seriesName, $epname, $vidheight, $vidwidth, $epprefix, $subGroup, $Movie, $videotype) = mysql_fetch_array($result, MYSQL_NUM);
				$seriesName    = htmlspecialchars($seriesName);
				//$epname = htmlspecialchars($epname);
				$epprefix    = htmlspecialchars($epprefix);
				$seriesName    = stripslashes($seriesName);
				$epname = stripslashes($epname);
				$epprefix    = stripslashes($epprefix);
							echo '<form method="GET" name="editep" id="editep">
				<input type="hidden" name="id" value="' . $id . '">
				<table width="620px" border="0" cellpadding="2" cellspacing="1" class="box" align="center">
				<tr><td width="200" class="fontcolor">Episode #</td><td><input name="epnumber" type="text" class="input3" id="epnumber" size="25" value="' . $epnumber . '"></td></tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr><td width="150" class="fontcolor">Episode Name</td><td><input name="epname" type="text" class="input3" id="epname" size="25" value="' . $epname . '"></td></tr>
					<tr><td colspan="2" class="fontcolor">If none put: i.e.: bleach 152, series name then ep #</td></tr>
					<tr><td width="150" class="fontcolor">Video Width</td><td><input name="vidwidth" type="text" class="input3" id="vidwidth" size="5" value="' . $vidwidth . '"></td></tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr><td width="150" class="fontcolor">Video Height</td><td><input name="vidheight" type="text" class="input3" id="vidheight" size="5" value="' . $vidheight . '"></td></tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr><td width="150" class="fontcolor">Fansub Group</td><td><input name="subGroup" type="text" class="input3" id="subGroup" size="25" value="' . $subGroup . '"></td></tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr><td width="150" class="fontcolor">Episode Prefix</td><td><input name="epprefix" type="text" class="input3" id="epprefix" size="25" value="' . $epprefix . '"></td></tr>
					<tr><td colspan="2" class="fontcolor">i.e. <b>samcham</b>_1_ns.divx, if these are not all the same the episode wont work.</td> </tr>
					<tr><td width="150" class="fontcolor">Series Name</td><td>
						<select name="seriesName" style="color: #000000;">';
					$query2 = "SELECT seriesName, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
					$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
					while(list($seriesName2, $fullSeriesName) = mysql_fetch_array($result2, MYSQL_NUM)){
					$fullSeriesName = stripslashes($fullSeriesName);
				echo '<option id="'.$seriesName2.'" value="'.$seriesName2.'"'; if($seriesName == $seriesName2){echo' selected';} echo '>'.$fullSeriesName.'</option> ';
					}
					 echo '</select>
					</td></tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr><td width="150" class="fontcolor">MKV, DIVX or MP4<i>is this the new MKV, DivX or MP4 style videos?</i></td>
					  <td><select name="videotype" style="color: #000000;">
							<option value="divx" '; if($videotype == 'divx'){echo 'selected="selected"';} echo '>DIVX</option>
							<option value="mkv" '; if($videotype == 'mkv'){echo 'selected="selected"';} echo '>MKV</option>
							<option value="mp4" '; if($videotype == 'mp4'){echo 'selected="selected"';} echo '>MP4</option>
						</select></td></tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr>
					  <td width="150" class="fontcolor">Movie?</td>
					  <td><select name="Movie" style="color: #000000;">
							<option value="0" '; if($Movie == '0'){echo 'selected="selected"';} echo '>No</option>
							<option value="1" '; if($Movie == '1'){echo 'selected="selected"';} echo '>Yes</option>
						</select></td>
					</tr>
				<tr>
				<td colspan="2" align="center">
				<input type="button" value="Go Back" name="back" id="back" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'\');">
				<input type="button" value="Update Episode" name="edit" id="edit" onclick="ajax_loadContent(\'manageedit\',\''.$link.'&eid='.$id.'&stage=after&random=sometime\' + getFormElementValuesAsString(document.forms[\'editep\']));"></td>
				</tr>
				</table>
				</form>';
				}
				else if($_GET['stage'] == 'after'){
					// This is where the magic happens..
					$id = $_GET['eid'];
					$epnumber = $_GET['epnumber'];
					$seriesName = $_GET['seriesName'];
					$epname = $_GET['epname'];
					$vidheight = $_GET['vidheight'];
					$vidwidth = $_GET['vidwidth'];
					$epprefix = $_GET['epprefix'];
					$subGroup = $_GET['subGroup'];
					$Movie = $_GET['Movie'];
					$videotype = $_GET['videotype'];
					$seriesName  = stripslashes($seriesName);
					$epname = stripslashes($epname);
					$epprefix    = stripslashes($epprefix);
				   // update the item in the database
				   $query = 'UPDATE episode SET epnumber=\'' . mysql_real_escape_string($epnumber) . '\', seriesname=\'' . mysql_real_escape_string($seriesName) .'\', epname=\'' . mysql_real_escape_string($epname) . '\', vidheight=\'' . mysql_real_escape_string($vidheight) . '\', vidwidth=\'' . mysql_real_escape_string($vidwidth) . '\', epprefix=\'' . mysql_real_escape_string($epprefix) . '\', subGroup=\'' . mysql_real_escape_string($subGroup) . '\', Movie=\'' . mysql_real_escape_string($Movie) . '\', videotype=\'' . mysql_real_escape_string($videotype) . '\' WHERE id=' . $_GET['eid'] . '';
					mysql_query($query) or die('Error : ' . mysql_error());
					$msg = "<div class=\"redmsg\">Please verify that all the information is correct, then proceed. </div><br />";
					$msg .= "<dl><dt>Episode #:</dt><dd>$epnumber</dd></dl>";
					$msg .= "<dl><dt>Episode Name:</dt><dd> $epname</dd></dl>";
					$msg .= "<dl><dt>Video Height:</dt><dd> $vidheight</dd></dl>";
					$msg .= "<dl><dt>Video Width:</dt><dd> $vidwidth</dd></dl>";
					$msg .= "<dl><dt>Episode Prefix:</dt><dd> $epprefix</dd></dl>";
					$msg .= "<dl><dt>Fansub:</dt><dd> $subGroup</dd></dl>";
					$msg .= "<dl><dt>Video Type:</dt><dd> $videotype</dd></dl>";
					$msg .= '<div align="center"><input type="button" value="Continue" name="back" id="back" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'\');">
	<input type="button" value="Update Episode" name="edit" id="edit" onclick="ajax_loadContent(\'manageedit\',\''.$link.'&eid='.$id.'&stage=before\');"></div>';

					echo $msg;

				}
				else {
					echo 'Error. NO FOOD FOR YOU!@#';
				}
			}
			else {
				if(isset($_GET['edit']) && $_GET['edit'] == 'add'){
					if(isset($_GET['step'])){
						if($_GET['step'] == 'before'){
						}
						if($_GET['step'] == 'after'){
							$epnumber = mysql_real_escape_string($_GET['epnumber']);
							$seriesName1 = mysql_real_escape_string($_GET['seriesName']);
							$epname = mysql_real_escape_string(urldecode($_GET['epname']));
							$vidheight = mysql_real_escape_string($_GET['vidheight']);
							$vidwidth = mysql_real_escape_string($_GET['vidwidth']);
							$epprefix = mysql_real_escape_string($_GET['epprefix']);
							$subGroup = mysql_real_escape_string($_GET['subGroup']);
							$Movie = mysql_real_escape_string($_GET['Movie']);
							$Remember = mysql_real_escape_string($_GET['Remember']);
							$addtime = mysql_real_escape_string($_GET['date']);
							$videotype =mysql_real_escape_string( $_GET['videotype']);
							if($addtime == '0'){
								$addtime = '0';
							}
							else {
								$addtime = time();
							}
							$NextEp = $epnumber+1;
							$query = sprintf("INSERT INTO episode (epnumber, seriesname, epname, vidheight, vidwidth, subGroup, epprefix, Movie, date, videotype, uid) VALUES ('$epnumber', '$seriesName1', '$epname', '$vidheight', '$vidwidth', '$subGroup', '$epprefix', '$Movie', '$addtime', '$videotype', '$this->UserArray[1]')");
							mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
							$msg = "<div class=\"redmsg\">Episode #$epnumber Added, titled: $epname </div>";
							// now we check to see if we can use: RecordNotification($sid,$eid)
							$airingCheck = mysql_query("SELECT episode.id AS epid, series.id AS sid, series.stillRelease FROM series, episode WHERE series.seriesName = '".$seriesName1."' AND episode.seriesname = series.seriesName AND episode.epnumber = '".$epnumber."'");
							$ar = mysql_fetch_array($airingCheck);
							if($ar['stillRelease'] == 'yes'){
								$this->RecordNotification($ar['sid'],$ar['epid']);
							}
							$this->ModRecord('Add Episode #'.$epnumber.' to series '.$seriesName1);

						}
						echo '
						<br /><div style="height:470px;overflow-y:scroll;overflow-x:none;">
						'.$msg.'
						<form method="GET" name="addep" id="addep">
						<input type="hidden" name="id" value="bradrocks">
						 <table width="620px" border="0" cellpadding="1" cellspacing="1" align="center">
					<tr>
					  <td width="200" class="fontcolor">Episode #</td>
					  <td><input name="epnumber" type="text" class="input3" id="epnumber" size="25"'; if($Remember == TRUE){echo ' value="'.$NextEp.'"';} echo' /></td>
					</tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr>
					  <td width="150" class="fontcolor">Episode Name</td>
					  <td><input name="epname" type="text" class="input3" id="epname" size="25"></td>
					</tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
				  <tr>
					  <td width="150" class="fontcolor">Video Width</td>
					  <td><input name="vidwidth" type="text" class="input3" id="vidwidth" size="5"'; if($Remember == TRUE){echo ' value="'.$vidwidth.'"';} echo' /></td>
					</tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr>
					  <td width="150" class="fontcolor">Video Height</td>
					  <td><input name="vidheight" type="text" class="input3" id="vidheight" size="5"'; if($Remember == TRUE){echo ' value="'.$vidheight.'"';} echo' /></td>
					</tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
					<tr>
					  <td width="150" class="fontcolor">Fansub Group</td>
					  <td><input name="subGroup" type="text" class="input3" id="subGroup" size="25"'; if($Remember == TRUE){echo ' value="'.$subGroup.'"';} echo' ></td>
					</tr>
					<tr><td colspan="2" class="fontcolor">&nbsp;</td> </tr>
					<tr>
					  <td width="150" class="fontcolor">Episode Prefix</td>
					  <td><input name="epprefix" type="text" class="input3" id="epprefix" size="25"'; if($Remember == TRUE){echo ' value="'.$epprefix.'"';} echo' /></td>
					</tr>
					<tr>
					  <td colspan="2" class="fontcolor">i.e. <b>samcham</b>_1_ns.divx, if these are not all the same the episode wont work.</td>
				  </tr>
					<tr>
					  <td width="150" class="fontcolor">Series Name</td>
					  <td>
					  <select name="seriesName" style="color: #000000;">
				  <option>-Choose Series-</option>';

					  $query2 = "SELECT seriesName, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
					$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
					while(list($seriesName, $fullSeriesName) = mysql_fetch_array($result2, MYSQL_NUM)){
						$fullSeriesName = stripslashes($fullSeriesName);
						if(($seriesName1 == $seriesName && $Remember == TRUE) || isset($_GET['preseriesname']) && $_GET['preseriesname'] == $seriesName)
						{
							echo '<option value="'.$seriesName.'" selected="selected">'.$fullSeriesName.'</option> ';
						}
						else {
						}
						echo '<option value="'.$seriesName.'">'.$fullSeriesName.'</option> ';
					}

					 echo '</select>
					  </td>
					</tr>
					<tr>
					  <td colspan="2" class="fontcolor">&nbsp;</td>
				  </tr>
					<tr>
					  <td width="150" class="fontcolor">Movie? <i> is this a movie?</i></td>
					  <td><select name="Movie" style="color: #000000;">
							<option value="0" selected="selected">No</option>
							<option value="1">Yes</option>
						</select></td>
					</tr>
					<tr>
					  <td colspan="2" class="fontcolor">&nbsp;</td>
				  </tr>
					<tr>
					  <td width="150" class="fontcolor">MKV, DIVX or MP4<i>is this the new MKV, DivX or MP4 style videos?</i></td>
					  <td><select name="videotype" style="color: #000000;">
							<option value="divx"'; if($videotype == 'divx' && $Remember == TRUE){echo ' selected="selected"';} echo'>DivX</option>
							<option value="mkv"'; if(($videotype == 'mkv' && $Remember == TRUE)||!isset($Remember)){echo ' selected="selected"';} echo'>MKV</option>
							<option value="mp4"'; if($videotype == 'mp4' && $Remember == TRUE){echo ' selected="selected"';} echo'>MP4</option>
						</select></td>
					</tr>
					<tr>
					  <td colspan="2" class="fontcolor">&nbsp;</td>
				  </tr>
					<tr>
					  <td width="150" class="fontcolor">Silent Episode? <i> Should this not be put on the latest episode listing?</i></td>
					  <td><select name="date" style="color: #000000;">
							<option value="1"'; if($addtime == '1'){echo ' selected="selected"';} echo'>No</option>
							<option value="0"'; if($addtime == '0' && $Remember == TRUE){echo ' selected="selected"';} echo'>Yes</option>
						</select></td>
					</tr>
					<tr>
				<td colspan="2" align="center">
				<input type="checkbox" name="Remember" id="Remember"'; if($Remember == TRUE){echo ' checked="checked"';} echo' />
				Remember certain fields?&nbsp;&nbsp;
				<input type="button" value="Add Episode" name="add" id="add" onclick="ajax_loadContent(\'manageedit\',\''.$link.'&edit=add&step=after&random=sometime\' + getFormElementValuesAsString(document.forms[\'addep\']));">&nbsp;<input type="button" value="Go Back" name="back" id="back" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'\');"> </td>
				</tr>
				  </table>
						</form>';

					}
					else {
						echo 'WHY AM I GETTING THESE ERRORS';
					}
				}
				else if(isset($_GET['edit']) && $_GET['edit'] == 'image-add'){
					// /scripts.php?view=management&u=1&node=episodes&edit=image-add
					if(!isset($_GET['epid'])){
						echo 'Invalid';
					}
					else {
						if(!isset($_GET['point']) || (isset($_GET['point']) && $_GET['point'] == 'before'))
						{
							$script_location = '/home/mainaftw/public_html/scripts.php phpcli-auth=true view=management u='.$_GET['u'].' node=episodes sname='.$_GET['sname'].' edit=image-add epid='.$_GET['epid'].' point=after';
							$CMD = exec("php-cgi -f " . $script_location);
							echo $CMD;
						}
						else
						{
							$epid = mysql_real_escape_string($_GET['epid']);
							$results = mysql_query("SELECT episode.seriesname, episode.epprefix, episode.epnumber, episode.vidwidth, episode.vidheight, episode.videotype, series.videoServer FROM episode, series WHERE series.seriesName=episode.seriesName AND episode.id = '".$epid."'");
							$row = mysql_fetch_array($results);
							$url = 'http://' . $row['videoServer'] . '.animeftw.tv/fetch-pictures-v2.php?node=add&remote=true&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'];
							//echo $url;
							$createscript = $this->RemoteBuildEpImage($url);
							if($createscript == 'Success')
							{
								echo '<script>alert("There was an error Creating that Image! Error: '.$createscript.'");</script>';
							}
							else
							{
								echo '<script>alert("Image Creation for ' . $row['seriesname'] . ' episode ' . $row['epnumber'] . ' Completed!");</script>';
							}
							//echo '<div align="center">Image Creation has been completed<br />Please verify that the image has shown up below. If not, please alert brad asap.<br /><img src="http://static.ftw-cdn.com/site-images/video-images/'.$row['epprefix'].'_'.$row['epnumber'].'_screen.jpeg" alt="" height="200px" /><br /><br /><input type="button" value="Back to Episode Listing" name="edit" id="edit" onclick="ajax_loadContent(\'manageedit\',\''.$link.'&sname='.$row['seriesname'].'\'); return false;"></div>';
							mysql_query("UPDATE episode SET image = 1 WHERE id = $epid");
						}
					}
				}
				else {
					echo 'Error, we didn\'t know what your trying to do..';
				}
			}
		}
	}

	// function here

	private function RecordNotification($sid,$eid){
		mysql_query("INSERT INTO notifications (uid, date, type, d1, d2, d3) VALUES (NULL, '".time()."', '0', '".$sid."', '".$eid."', 'NULL')");
	}

	private function BuildList($id,$username,$regDate = NULL,$ip,$active,$type,$link,$Email = NULL,$lastActivity = NULL,$Level_access = NULL,$forumBan = NULL,$messageBan = NULL,$postBan = NULL){
		echo '<div style="padding:5px;">';
		if($type == 1){
			echo '<a href="/user/'.$username.'" target="_blank">'.$username.'</a> <em>Registered on: '.date('d-m-y',$regDate).'</em> ip: <a href="http://ip-lookup.net?ip='.$ip.'" target="_blank">'.$ip.'</a>';
			echo '<div style="float:right;">';
			echo '<select name="change">';
			echo '<option value="#">- Actions -</option>';
			if($active == 1 || $active == 0){
				echo'<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=suspend&id='.$id.'\'); return false;">Suspend</option>';
			}
			if($active == 2){
				echo'<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=unsuspend&id='.$id.'\'); return false;">Un-Suspend</option>';
			}
			echo'<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=delete&id='.$id.'\'); return false;">Delete</option>
				<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=fban&id='.$id.'\'); return false;">Forum Ban</option>
				<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=cban&id='.$id.'\'); return false;">Comment Ban</option>
				<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=pmban&id='.$id.'\'); return false;">Site PM Ban</option>';
			echo '</select>';
			echo '</div>';
		}
		else if($type == 2){
			echo '<a href="http://ip-lookup.net?ip='.$ip.'" target="_blank">'.$ip.'</a> failed on '.date('m-d-y',$active).', user: '.$username.', password: '.$regDate;
		}
		else if($type == 3) {
			echo '<a href="http://ip-lookup.net?ip='.$username.'" target="_blank">'.$username.'</a> logged in on '.date('m-d-y',$regDate).' for user '.checkUserNameNumber($ip);
		}
		else { //5 type..
		//brad, E-mail: roboy7736@yahoo.com, User Id# 5, Last Active: 03/2/2012 22:52
    	//Registration IP: 0.0.0.0, Account Status: Active, Access Level: 3
		if ($active == 0){$accountstatus = 'In-Active';}else if ($active == 1){$accountstatus = 'Active';}else {$accountstatus = 'Suspended';}
			$la = date("m/j/Y G:i",$lastActivity);
			echo '<a href="/user/'.$username.'">'.$username.'</a>, E-mail: '.$Email.', ID# '.$id.'<br /> Last Active: '.$la.', Registration IP: '.$ip.'<br /> Account Status: '.$accountstatus.', Access Level: '.$Level_access;
			echo '<div style="float:right;">';
			echo '<select name="change">';
			echo '<option>----------</option>';
			if($active == 1){echo '<option onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=suspend&id='.$id.'\'); return false;">Suspend</option>';}else if($active == 0 || $active == 2){echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=unsuspend&id='.$id.'\'); return false;">Activate</option>';}else {}
			echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=delete&id='.$id.'\'); return false;">Delete</option>';
			if($forumBan == 1){echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=unfban&id='.$id.'\'); return false;">Forum Unban</option>';}else {echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=fban&id='.$id.'\'); return false;">Forum Ban</option>';}
			if($messageBan == 1){echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=uncban&id='.$id.'\'); return false;">Comment Unban</option>';}else {echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=cban&id='.$id.'\'); return false;">Comment Ban</option>';}
			if($postBan == 1){echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=unpmban&id='.$id.'\'); return false;">Site PM Unban</option>';}else {echo '<option value="#" onClick="javascript:ajax_loadContent(\'manageedit\',\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=management&u='.$this->uid.'&node=users&stage=modedit&modaction=pmban&id='.$id.'\'); return false;">Site PM Ban</option>';}
			echo '</select>';
			echo '</div>';
		}
		echo '</div>';
	}

	//Paging function for the management pages
	private function InternalPaging($count,$perpage,$start,$link){
		$num = $count;
		$per_page = $perpage; // Number of items to show per page
		$showeachside = 4; //  Number of items to show either side of selected page
		if(empty($start)){$start = 0;}  // Current start position
		else{$start = $start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		$front = "<span>$max_pages Pages</span>&nbsp;";
		if(($start-$per_page) >= 0){
			$next = $start-$per_page;
			$startpage = '<a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.($next>0?("&page=").$next:"").'\');return false;">&lt;</a>';
		}
		else {$startpage = '';}
		if($start+$per_page<$num){
			$endpage = '<a href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.'&page='.max(0,$start+1).'\');return false;">&gt;</a>';
		}
		else {
			$endpage = '';
		}
		$eitherside = ($showeachside * $per_page);
		if($start+1 > $eitherside){
			$frontdots = " ...";
		}
		else {$frontdots = '';}
		$pg = 1;
		$middlepage = '';
		for($y=0;$y<$num;$y+=$per_page)
		{
			$class=($y==$start)?"pageselected":"";
			if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
			{
				$middlepage .= '<a id="'.$class.'" href="#" onClick="javascript:ajax_loadContent(\'manageedit\',\''.$link.($y>0?("&page=").$y:"").'\');return false;">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}

		echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}

	//Paging function for the management pages, version two
	private function InternalPagingV2($DivID,$count,$perpage,$start,$link)
	{
		$num = $count;
		$per_page = $perpage; // Number of items to show per page
		$showeachside = 4; //  Number of items to show either side of selected page
		if(empty($start)){$start = 0;}  // Current start position
		else{$start = $start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		$front = "<span>$max_pages Pages</span>&nbsp;";
		if(($start-$per_page) >= 0){
			$next = $start-$per_page;
			$startpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($next>0?("&page=").$next:"") . '\');return false;">&lt;</a>';
		}
		else {$startpage = '';}
		if($start+$per_page<$num){
			$endpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.'&page='.max(0,$start+1) . '\');return false;">&gt;</a>';
		}
		else {
			$endpage = '';
		}
		$eitherside = ($showeachside * $per_page);
		if($start+1 > $eitherside){
			$frontdots = " ...";
		}
		else {$frontdots = '';}
		$pg = 1;
		$middlepage = '';
		for($y=0;$y<$num;$y+=$per_page)
		{
			$class=($y==$start)?"pageselected":"";
			if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
			{
				$middlepage .= '<a id="'.$class.'" href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($y>0?("&page=").$y:"") . '\');return false;">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}

		echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}

	private function ManageSeries(){
		if($this->ValidatePermission(21) == TRUE){
			$link = '/scripts.php?view=management&u='.$this->uid.'&node=series'; // base url
			$limit = 30; //series per page
			$DivID = 'seriesg';
			if(isset($_GET['page'])){$page = $_GET['page'];}
			else {$page = 0;}
			//if(!isset($_GET['subnode'])){
			if(!isset($_GET['stage'])){
				if(!isset($_GET['nonav']) || $_GET['nonav'] != 'true'){
					echo $this->TopNav();
					echo '<div align="center" style="padding-top:5px;">';
					echo '<a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&nonav=true\'); return false;">Home</a> ';
					if($this->ValidatePermission(22) == TRUE){
						echo '| <a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&stage=addseries&step=before\'); return false;">Add Series</a> ';
					}
					if($this->ValidatePermission(24) == TRUE){
						echo '| <a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&stage=search\'); return false;">Series Search</a> ';
					}
					if($this->ValidatePermission(25) == TRUE){
						echo '| <a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&stage=upload\'); return false;">Upload Series Image</a> ';
					}
					if($this->ValidatePermission(72) == TRUE){
						echo '| <a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&stage=mass-update\'); return false;">Mass Updates</a> ';
					}
					if($this->ValidatePermission(73) == TRUE){ //announce
						echo '| <a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&stage=announce\'); return false;">Announcement Builder</a> ';
					}
				}
				echo '</div>
				<div id="ContentStuff" class="ContentStuff">';
				$TotalSeries = $this->Query('series'); //count all of the series please.
				$query = "SELECT id, seriesName, fullSeriesName, seoname, videoServer, active, description, ratingLink, stillRelease, Movies, moviesOnly, OVA, noteActivate, noteReason, category FROM series ORDER BY id DESC LIMIT $page, $limit";
				mysql_query("SET NAMES 'utf8'");
				$result = mysql_query($query) or die('Error : ' . mysql_error());

				echo '<div id="seriesg">';
				echo '<div style="padding:3px;">';
				//$this->InternalPaging($TotalSeries,$limit,$page,$link); //($count,$perpage,$start,$link)
				$this->InternalPagingV2($DivID,$TotalSeries,$limit,$page,($link.'&nonav=true'));
				echo '</div>';
				echo '<div style="height:435px;overflow-y:scroll;overflow-x:none;">';
				$i = 0;
				$fivearray = array(1 => '<br />',2 => '<br /><br />',3 => '<br /><br /><br />',4 => '<br /><br /><br /><br />',5 => '<br /><br /><br /><br /><br />',6 => '<br /><br /><br /><br /><br /><br />',7 => '<br /><br /><br /><br /><br /><br /><br />');
				while(list($id, $seriesName, $fullSeriesName, $seoname, $videoServer, $active, $description, $ratingLink, $stillRelease, $Movies, $moviesOnly, $OVA, $noteActivate, $noteReason, $category) = mysql_fetch_array($result, MYSQL_NUM)){
					$query = mysql_query("SELECT id FROM episode WHERE seriesname='".$seriesName."' AND Movie = 0");
					$CountEpisodes = mysql_num_rows($query);
					if($moviesOnly == 1){$moviesOnly = 'yes';}else {$moviesOnly = 'no';}
					if($noteActivate == 1){$noteActivate = 'yes';}else {$noteActivate = 'no';}
					if($active == 'no'){$active = "<span class=\"sinactive\">In-Active</span>";}else {$active = "<span class=\"sactive\">Active</span>";}
					$description = stripslashes($description);
					$fullSeriesName = stripslashes($fullSeriesName);
					$dlength = strlen($description);
					if($dlength > 800){
						$gvar = ceil(($dlength-800)/55);
						$gvar = $fivearray[$gvar];
					}
					else {$gvar = '';}
					if($i % 2){ $srow = ' class="srow2"';} else {$srow = ' class="srow"';}
					echo '<div'.$srow.'><div class="srightcol">'.$description.'</div>
					<div class="sleftcol">
					<div align="center"><a href="/anime/'.$seoname.'/">View Series</a>';
					if($this->ValidatePermission(23) == TRUE){
						echo '| <a href="#" onClick="$(\'#ContentStuff\').load(\''.$link.'&stage=edit&step=before&sid='.$id.'\'); return false;">Edit Series</a>';
					}
					echo '</div>
					<b>Series Name:</b> ' . $fullSeriesName . '<br />
					<b>Series Site Active?</b> '.$active.'<br />
					<b>Kanji:</b> '.checkKanji($seriesName).'<br />
					<b>Romaji:</b> '.checkRomaji($seriesName).'<br />
					<b>Video Server:</b> '.$videoServer.'<br />
					<b>Still Airing?</b> '.$stillRelease.'<br />
					<b>Movies only?</b> '.$moviesOnly.'<br />
					<b>Seres Note Active?</b> '.$noteActivate.'<br />
					<b>Total Episodes:</b> '.$CountEpisodes.'<br />
					<b>Number of Movies:</b> '.$Movies.'<br />
					<b>Genres:</b> '.$category.'<br />
					<b>Rating:</b><br /><img src="//i.animeftw.tv/ratings/' . $ratingLink . '" alt="" title="This series\'s rating" />
					'.$gvar.'
					</div>
					</div>';
					$i++;
				}
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
			else {
				if($_GET['stage'] == 'search' && $this->ValidatePermission(24) == TRUE){
					echo '<div class="tbl"><br />
						<div align="center">';
					echo '<form method="POST" name="AdminSeriesSearch" id="AdminSeriesSearch">';
					echo '<input type="hidden" id="method" class="method" value="AdminSeriesSearch" name="method" />
						<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
						<input type="hidden" name="uid" value="'.$this->uid.'" />
						<table width="650px">
						<tr>
							<td align="left" colspan="2">
								<div align="center">
									<input name="SeriesName" id="SeriesName" type="text" class="text-input" style="width:200px;" />
									<input name="submit" type="button" class="SubmitForm" value="Search" />
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="margin: 5px 0px 0px 0px;">
									<div align="center" style="font-size: 9px;">Use the above form, to search through series on the site, use the Romaji, Kanji or Series Names and results will be returned.<br />(Results Below)</div>
								</div>
							</td>
						</tr>
						</table>
						</form>
						</div></div>';
						echo '<div id="form_results" class="form_results">&nbsp;</div>';
						echo '<script>
						$(function() {
							$(\'#SeriesName\').keypress(function(event){

								if (event.keyCode == 10 || event.keyCode == 13)
								{
									event.preventDefault();
									$(\'.SubmitForm\').click();
								}
							});
							$(\'.form_results\').hide();
							$(\'.text-input\')
								.css({border:"1px solid #CCC"})
								.css({color:"#5A5655"})
								.css({font:"13px Verdana,Arial,Helvetica,sans-serif"})
								.css({padding:"2px"})
							;
							$(\'.text-input\').focus(function(){
								$(this).css({border:"1px solid #0C90BB"});
							});
							$(\'.text-input\').blur(function(){
								$(this).css({border:"1px solid #CCC"});
							});
							$(".SubmitForm").click(function() {
								$.ajax({
									type: "POST",
									url: "/scripts.php",
									data: $(\'#AdminSeriesSearch\').serialize(),
									success: function(html) {
										$(\'.form_results\').show().html(html);
									}
								});
								return false;
							});
							return false;
						});
						</script>';
				}
				else if($_GET['stage'] == 'upload' && $this->ValidatePermission(25) == TRUE){
					echo 'Upload feature coming soon';
				}
				else if($_GET['stage'] == 'announce' && $this->ValidatePermission(73) == TRUE){
					echo '<br /><div align="center">The announcement Builder is a simple script that facilitates the ability to grab the neccessary data for creating proper announcements when releasing mass updates of Series.</div><br /><b>Choose from Available Series:</b>';
					echo '<div style="height:125px;overflow-y:scroll;overflow-x:none;border:1px solid #0C90BB;">';
					$query = "SELECT id, fullSeriesName FROM series ORDER by seriesName ASC";
					$result = mysql_query($query);
					echo '<form action="POST" id="SeriesAnnouncementBuilder">
					<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
					<input type="hidden" id="method" class="method" value="SeriesAnnouncementBuilder" name="method" />';
					while($row = mysql_fetch_array($result))
					{
						echo '<div style="float:left;display:inline;width:300px;">
						<input type="checkbox" name="sid[]" value="' . $row['id'] . '" id="sid-' . $row['id'] . '" />&nbsp;<label for="sid-' . $row['id'] . '" style="display:inline;color:#5A5655;">' . $row['fullSeriesName'] . '</label>
						</div>';
					}
					echo '</form>';
					echo '</div><br /><b>Output:</b><br />';
					echo '<div id="SeriesAnnouncementOutput"><textarea style="height:175px;overflow-y:scroll;overflow-x:none;border:1px solid #0C90BB;width:100%" onclick="this.select()"></textarea></div>';
						echo '
						<script>
						$(document).ready(function() {
							$(\'#SeriesAnnouncementBuilder input\').change(function() {
								$.ajax({
									type: "POST",
									url: "/scripts.php",
									data: $(\'#SeriesAnnouncementBuilder\').serialize(),
									success: function(html) {
										$(\'#SeriesAnnouncementOutput\').show().html(html);
									}
								});
								return false;
							});
						});
						</script>';
				}
				else if($_GET['stage'] == 'mass-update' && $this->ValidatePermission(72) == TRUE)
				{
					if(!isset($_GET['seriesname']))
					{
						$ReqSeriesName = '';
						echo '<div align="center">The Mass update Tool is used to update a series` episodes from a single interface, since this is technically a series function I have placed it here instead of in the episode section as it makes it easier to just find it and run. -Brad</div>';
						echo '<div align="center" style="padding-top:5px;">';
							echo '<select name="AvailableSeries" id="AvailableSeries" style="color: #000000;">';
						$query2 = "SELECT seriesName, fullSeriesName FROM series ORDER BY fullSeriesName ASC";
						echo '<option id="0" value="0">Choose a Series</option> ';
						$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
						while(list($seriesName, $fullSeriesName) = mysql_fetch_array($result2, MYSQL_NUM)){
							$fullSeriesName = stripslashes($fullSeriesName);
							echo '<option id="'.$seriesName.'" value="'.$seriesName.'"'; if($seriesName == $ReqSeriesName){echo' selected';} echo '>'.$fullSeriesName.'</option> ';
						}
						echo '</select>';
						echo '</div><br />';
						echo '<div id="SeriesOptions">&nbsp;</div>';
						echo '
						<script>
						$(document).ready(function() {
							$(\'#AvailableSeries\').change(function() {
								$(\'#SeriesOptions\').load(\''.$link.'&stage=mass-update&seriesname=\' + $(\'select\').val());
							});
						});
						</script>';
					}
					else
					{
						$query = "SELECT episode.vidheight, episode.vidwidth, episode.epprefix, episode.subGroup, episode.videotype, series.fullSeriesName FROM episode, series WHERE episode.seriesname = '" . mysql_real_escape_string($_GET['seriesname']) . "' AND series.seriesName=episode.seriesname LIMIT 0, 1";
						$results = mysql_query($query);
						$row = mysql_fetch_array($results);
						echo '<div id="form_results" class="form_results">&nbsp;</div>';
						echo '<form method="POST" name="MassEpisodeEdit" id="MassEpisodeEdit">';
						//echo $_GET['seriesname'];
						echo '
						<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
						<input type="hidden" id="method" class="method" value="MassEpisodeUpdate" name="method" />
						<input type="hidden" name="fullSeriesName" value="' . $row['fullSeriesName'] . '" />
						<input type="hidden" name="seriesname" value="' . $_GET['seriesname'] . '" />
						<input type="hidden" name="old_vidwidth" value="' . $row['vidwidth'] . '" />
						<input type="hidden" name="old_vidheight" value="' . $row['vidheight'] . '" />
						<input type="hidden" name="old_epprefix" value="' . $row['epprefix'] . '" />
						<input type="hidden" name="old_subGroup" value="' . $row['subGroup'] . '" />
						<input type="hidden" name="old_videotype" value="' . $row['videotype'] . '" />
						<input type="hidden" name="uid" value="'.$this->uid.'" />
						<table width="620px" border="0" cellpadding="2" cellspacing="1" align="center">
						<tr>
							<td width="130px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Video Width</td>
							<td>
								<input name="vidwidth" type="text" id="vidwidth" style="width:50px;" value="' . $row['vidwidth'] . '" class="text-input" />
								<label for="vidwidth" id="vidwidthError" class="FormError">The Video Width is Required</label>
							</td>

						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Video Height</td>
							<td>
								<input name="vidheight" type="text" id="vidheight" style="width:50px;" value="' . $row['vidheight'] . '" class="text-input" />
								<label for="vidheight" id="vidheightError" class="FormError">The Video Height is Required</label>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Episode Preffix</td>
							<td>
								<input name="epprefix" type="text" class="text-input" style="width:200px;" id="epprefix" value="' . $row['epprefix'] . '" />
								<label for="epprefix" id="epprefixError" class="FormError">The Episode Prefix is Required</label>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Fansub Group</td>
							<td>
								<input name="subGroup" type="text" class="text-input" id="subGroup" style="width:150px;" value="' . $row['subGroup'] . '" />
								<label for="subGroup" id="subGroupError" class="FormError">The Fansub Group is Required</label>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Video Type</td>
							<td>
								<select name="videotype" style="color: #000000;">
								<option value="divx"'; if($row['videotype'] == 'divx'){echo ' selected="selected"';} echo'>DivX</option>
								<option value="mkv"'; if($row['videotype'] == 'mkv'){echo ' selected="selected"';} echo'>MKV</option>
								<option value="mp4"'; if($row['videotype'] == 'mp4'){echo ' selected="selected"';} echo'>MP4</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Update Type [<a href="#" onClick="return false;" title="Use with caution, this will update Episodes, Movies and BOTH if selected.">?</a>]</td>
							<td>
								<select name="UpdateType" style="color: #000000;">
								<option value="0" selected="selected">Episodes Only</option>
								<option value="1">Movies Only</option>
								<option value="2">Episodes AND Movies</option>
								</select>
							</td>
						</tr>
						</table><br />';
						echo '<input type="submit" class="SubmitForm" id="submit" name="submit" value="Update All Episodes">';
						echo '</form>';
							echo '</form>';
							echo '<script>
						$(function() {
							$(\'.form_results\').hide();
							$(\'.text-input\')
								.css({border:"1px solid #CCC"})
								.css({color:"#5A5655"})
								.css({font:"13px Verdana,Arial,Helvetica,sans-serif"})
								.css({padding:"2px"})
							;
							$(\'.text-input\').focus(function(){
								$(this).css({border:"1px solid #0C90BB"});
							});
							$(\'.text-input\').blur(function(){
								$(this).css({border:"1px solid #CCC"});
							});
							$(".SubmitForm").click(function() {
								$(\'label\').hide();
								var vidwidth = $("input#vidwidth").val();
								if (vidwidth == "") {
									$("label#vidwidthError").show();
									$("input#vidwidth").focus();
									return false;
								}
								var vidheight = $("input#vidheight").val();
								if (vidheight == "") {
									$("label#vidheightError").show();
									$("input#vidheight").focus();
									return false;
								}
								var epprefix = $("input#epprefix").val();
								if (epprefix == "") {
									$("label#epprefixError").show();
									$("input#epprefix").focus();
									return false;
								}
								var subGroup = $("input#subGroup").val();
								if (subGroup == "") {
									$("label#subGroupError").show();
									$("input#subGroup").focus();
									return false;
								}
								var c=confirm("All Checks were passed, please note, there is no going back, were all the settings correct?");
								if(c==false)
								{
									return false;
								}
								else
								{
									$.ajax({
										type: "POST",
										url: "/scripts.php",
										data: $(\'#MassEpisodeEdit\').serialize(),
										success: function(html) {
											if(html == \'Success\'){
												$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Episode Mass Update was Successful!</div>");
												$(\'.form_result\').delay(8000).slideUp();
											}
											else{
												$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
											}
										}
									});
									return false;
								}
							});
							return false;
						});
						</script>';
					}
					// The idea behind this feature, is that it will give a list of series, followed by all the options for  the episodes, so you can change the eppisode prefix, width, height, sub group, image status, video type, in bulk. The best way to implement will be to have the system use a GET request back to the same script, to define the seriesname, if the seriesname is defined it will then pull up the first episodes options.. this is dangerous as things down the line could have multiple dimmensions.. possibly a way to show multiples? like a group by... food for later thoughts..
				}
				else if(($_GET['stage'] == 'edit' && $this->ValidatePermission(23) == TRUE) || ($_GET['stage'] == 'addseries' && $this->ValidatePermission(22) == TRUE))
				{
					if($_GET['stage'] == 'edit' && $this->ValidatePermission(23) == TRUE)
					{
						$Type = 'edit';
						if(!is_numeric($_GET['sid'])){
							echo 'That id is not valid..';
							exit;
						}
						else {
							$sid = mysql_real_escape_string($_GET['sid']);
							$sid = htmlentities($sid);
							$query2  = "SELECT id, seriesName, fullSeriesName, romaji, kanji, synonym, seoname, videoServer, active, description, ratingLink, stillRelease, Movies, moviesOnly, OVA, noteReason, aonly, sequelto, prequelto, category, seriesType, seriesList FROM series WHERE id='$sid'";
							mysql_query("SET NAMES 'utf8'");
							$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
							list($id, $seriesName, $fullSeriesName, $romaji, $kanji, $synonym, $seoname, $videoServer, $active, $description, $ratingLink, $stillRelease, $Movies, $moviesOnly, $OVA, $noteReason, $aonly, $sequelto, $prequelto, $category, $seriesType, $seriesList) = mysql_fetch_array($result2, MYSQL_NUM);
							$description = str_replace("<br />", "\n", $description);

							$description = stripslashes($description);
							$noteReason = stripslashes($noteReason);
							$fullSeriesName = stripslashes($fullSeriesName);

							$HiddenInputs = '<input type="hidden" name="sid" value="' . $id . '" id="sid" />
							<input type="hidden" id="method" class="method" value="EditSeries" name="method" />';
							$SubmitTXT = 'Update Series';
						}
					}
					else if($_GET['stage'] == 'addseries' && $this->ValidatePermission(22) == TRUE)
					{
						if(isset($_GET['ueid']))
						{
							$Type = 'add';
							$HiddenInputs = '<input type="hidden" id="method" class="method" value="AddSeries" name="method" />';
							$SubmitTXT = 'Add Series';
							$query = "SELECT series, prefix, anidbsid FROM uestatus WHERE id = " . mysql_real_escape_string($_GET['ueid']);
							$result = mysql_query($query);
							$row = mysql_fetch_array($result);
							$SeriesPrefix = substr($row['series'], 0, 10);
							$SeriesPrefix = strtolower($SeriesPrefix);
							if($SeriesPrefix == '[reencode]')
							{
								$FixedName = substr($row['series'], 11);
							}
							else
							{
								$FixedName = $row['series'];
							}
							$seoname = strtolower($FixedName);
							$seoname = str_replace(" ", "-", $seoname);
							$id = ''; $seriesName = $row['prefix']; $fullSeriesName = $FixedName; $romaji = ''; $kanji = ''; $synonym = ''; $videoServer = ''; $active = 'no'; $description = ''; $ratingLink = '15+.jpg'; $stillRelease = ''; $Movies = 0; $moviesOnly = ''; $OVA = ''; $noteReason = ''; $aonly = ''; $sequelto = ''; $prequelto = ''; $category = ''; $seriesType = '1'; $seriesList = '';
						}
						else
						{
							$Type = 'add';
							$HiddenInputs = '<input type="hidden" id="method" class="method" value="AddSeries" name="method" />';
							$SubmitTXT = 'Add Series';
							$id = ''; $seriesName = ''; $fullSeriesName = ''; $romaji = ''; $kanji = ''; $synonym = ''; $seoname = ''; $videoServer = ''; $active = 'no'; $description = ''; $ratingLink = '15+.jpg'; $stillRelease = ''; $Movies = 0; $moviesOnly = ''; $OVA = ''; $noteReason = ''; $aonly = ''; $sequelto = ''; $prequelto = ''; $category = ''; $seriesType = '1'; $seriesList = '';
						}
					}
					else
					{
						$Type = '';
						$HiddenInputs = '';
						$SubmitTXT = '';
					}
					echo '<div id="form_results" class="form_results">&nbsp;</div>';
					echo '<form method="POST" action="#" id="SeriesForm">';
					echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';
					echo $HiddenInputs;
					echo '
					<input type="hidden" name="hidden" value="0" id="hidden" />
					<input type="hidden" name="videoServer" value="videos" id="videoServer" />
					<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
					<input type="hidden" name="uid" value="'.$this->uid.'" />
					<table width="620" border="0" cellpadding="2" cellspacing="1" align="center">
						<tr>
							<td width="250" class="fontcolor"><b><i>Base Series Name</i></b><br /> <i>This should be the fullname of the series with no capitals, spaces or underscores, I.E:</i><br /> <b>airgear</b></td>
							<td>
								<input name="seriesName" type="text" id="seriesName" size="25" value="'.$seriesName.'" class="text-input" />
								<label for="seriesName" id="seriesNameError" class="FormError">A Base Series Name is Required.</label>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Full Series Name</i></b><br /> <i>This should be the fullname of the series WITH proper capitilization and spaces, I.E:</i><br /> <b>Air Gear</b></td>
							<td><input name="fullSeriesName" type="text" class="text-input" id="fullSeriesName" size="25" value="'.$fullSeriesName.'" />
								<label for="fullSeriesName" id="fullSeriesNameError" class="FormError">A Full Series Name is Required.</label>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Romaji</i></b><br /> </td>
							<td><input name="romaji" type="text" class="text-input" id="romaji" size="25" value="'.$romaji.'" />
								<label for="romaji" id="romajiError" class="FormError">The Romaji Name is Required.</label>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Kanji</i></b><br /></td>
							<td><input name="kanji" type="text" class="text-input" id="kanji" size="25" value="'.$kanji.'" />
								<label for="kanji" id="kanjiError" class="FormError">The Kanji Name is Required.</label>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Synonym</i></b><br /> <i>Sometimes a series is known by more than the official title, those go here, use commas to separate each name.</i></td>
							<td><textarea name="synonym" id="synonym" cols="55" rows="5" class="text-input">'.$synonym.'</textarea></td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>SEO Name</i></b><br /> <i>This should be the fullname of the series without Capitals, Spaces should be replaced with hyphens (-). I.E:</i><br /> <b>air-gear</b></td>
							<td><input name="seoname" type="text" class="text-input" id="seoname" size="25" value="'.$seoname.'" />
								<label for="seoname" id="seonameError" class="FormError">A SEO Name is Required.</label>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Site Active?</i></b><br /> <i>Is this series site active? No is the Default</i></td>
							<td>
								<select name="active" style="color: #000000;">
									<option value="no"'; if($active == 'no'){echo ' selected="selected"';} echo '>No</option>
									<option value="yes"'; if($active == 'yes'){echo ' selected="selected"';} echo '>Yes</option>
								</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Series Synopsis/description</i></b><br /> <i>Take the description from the series on <a href="http://anidb.net">AniDB.net</a> and paste it here, NO html required</i></td>
							<td><textarea name="description2" id="description2" cols="55" rows="5" class="text-input">'.$description.'</textarea></td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Series Genres</i></b><br /> <i>Take the "categories" from the series on <a href="http://anidb.net">AniDB.net</a> and paste it here, NO Special characters []&lt;&gt; just words and commas.</i></td>
							<td><textarea name="category" id="category" cols="55" rows="5" class="text-input">'.$category.'</textarea></td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Series Rating</i></b><br /><i>Choose the rating that goes with the series.</i></td>
							<td>
								<table>
								<tr>
									<td><div align="center"><img src="//i.animeftw.tv/ratings/e.jpg" alt="Everyone" /><br /><input type="radio" name="ratingLink" value="e.jpg" '; if($ratingLink == 'e.jpg'){echo 'checked="checked"';} echo ' /></td></div></td>
									<td><div align="center"><img src="//i.animeftw.tv/ratings/12+.jpg" alt="12+" /><br /><input type="radio" name="ratingLink" value="12+.jpg" '; if($ratingLink == '12+.jpg'){echo 'checked="checked"';} echo '  /></td></div></td>
									<td><div align="center"><img src="//i.animeftw.tv/ratings/15+.jpg" alt="15+" /><br /><input type="radio" name="ratingLink" value="15+.jpg" '; if($ratingLink == '15+.jpg'){echo 'checked="checked"';} echo '  /></td></div></td>
									<td><div align="center"><img src="//i.animeftw.tv/ratings/18+.jpg" alt="18+" /><br /><input type="radio" name="ratingLink" value="18+.jpg" '; if($ratingLink == '18+.jpg'){echo 'checked="checked"';} echo '  /></td></div></td>
								</tr>
							</table>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Still Releasing?</i></b><br /> <i>Is this Series still releasing? Default is No</i></td>
							<td><select name="stillRelease" style="color: #000000;">
								<option value="no" '; if($stillRelease == 'no'){echo 'selected="selected"';} echo ' >No</option>
								<option value="yes" '; if($stillRelease == 'yes'){echo 'selected="selected"';} echo ' >Yes</option>
							</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Movies:</i></b><br /> <i>Does this Series Have any movies?</i></td>
							<td><select name="Movies" style="color: #000000;">
								<option value="0" '; if($Movies == '0'){echo 'selected="selected"';} echo '>0</option>
								<option value="1" '; if($Movies == '1'){echo 'selected="selected"';} echo '>1</option>
								<option value="2" '; if($Movies == '2'){echo 'selected="selected"';} echo '>2</option>
								<option value="3" '; if($Movies == '3'){echo 'selected="selected"';} echo '>3</option>
								<option value="4" '; if($Movies == '4'){echo 'selected="selected"';} echo '>4</option>
								<option value="5" '; if($Movies == '5'){echo 'selected="selected"';} echo '>5</option>
								<option value="6" '; if($Movies == '6'){echo 'selected="selected"';} echo '>6</option>
								<option value="7" '; if($Movies == '7'){echo 'selected="selected"';} echo '>7</option>
							</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Movie Only Series?</i></b><br /> <i>Is this Series Movies only? No is Default</i></td>
							<td>
								<select name="moviesOnly" style="color: #000000;">
									<option value="0" '; if($moviesOnly == '0'){echo 'selected="selected"';} echo '>No</option>
									<option value="1" '; if($moviesOnly == '1'){echo 'selected="selected"';} echo '>Yes</option>
								</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>OVA Only Series?</i></b><br /> <i>Is this Series OVA only? No is Default</i></td>
							<td>
								<select name="OVA" style="color: #000000;">
									<option value="0" '; if($OVA == '0'){echo 'selected="selected"';} echo '>No</option>
									<option value="1" '; if($OVA == '1'){echo 'selected="selected"';} echo '>Yes</option>
								</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Note Reason</i></b><br /> <i>Only if Yes is above, fill this out and the reason will be placed on the site, NO HTML is needed</i></td>
							<td><textarea name="noteReason" id="noteReason" cols="55" rows="5" class="text-input">' . $noteReason . '</textarea></td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Series Type</i></b><br /> <i>Is this series an MKV, DIVX or MP4 based series?</i></td>
							<td>
								<select name="seriesType" style="color: #000000;">
									<option value="0" '; if($seriesType == '0'){echo 'selected="selected"';} echo '>DivX</option>
									<option value="1" '; if($seriesType == '1'){echo 'selected="selected"';} echo '>MKV</option>
									<option value="2" '; if($seriesType == '2'){echo 'selected="selected"';} echo '>MP4</option>
								</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Prequel</i></b><br /> <i>What series is a Prequel to this?</i></td>
							<td>
								<select name="prequelto" style="color: #000000;">';
									echo '<!-- '.$prequelto.' -->';
									$query2 = "SELECT id, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
									if($prequelto == 0){
										echo '<option id="0" value="0">None</option> ';
									}
									else {
										echo '<option id="0" value="0">None</option> ';
									}
									$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
									while(list($id2, $fullSeriesName) = mysql_fetch_array($result2, MYSQL_NUM))
									{
										$fullSeriesName = stripslashes($fullSeriesName);
										echo '<option id="'.$id2.'" value="'.$id2.'"'; if($id2 == $prequelto){echo' selected';} echo '>'.$fullSeriesName.'</option> ';

									}
									echo '</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Sequel</i></b><br /> <i>What series is a Sequel to this?</i></td>
							<td>
								<select name="sequelto" style="color: #000000;">';
									echo '<!-- '.$sequelto.' -->';
									$query2 = "SELECT id, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
									if($sequelto == 0){
										echo '<option id="0" value="0">None</option> ';
									}
									else {
										echo '<option id="0" value="0">None</option> ';
									}
									$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
									while(list($id2, $fullSeriesName) = mysql_fetch_array($result2, MYSQL_NUM)){
										$fullSeriesName = stripslashes($fullSeriesName);
										echo '<option id="'.$id2.'" value="'.$id2.'"'; if($id2 == $sequelto){echo' selected';} echo '>'.$fullSeriesName.'</option> ';
									}
								echo '</select>
							</td>
						</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Member Level?</i></b><br /> <i>What Level of Membership should be required to view this series?</i></td>
							<td>
								<select name="aonly" style="color: #000000;">
									<option value="0" '; if($aonly == '0'){echo 'selected="selected"';} echo '>Unregistered +</option>
									<option value="1" '; if($aonly == '1'){echo 'selected="selected"';} echo '>Basic +</option>
									<option value="2" '; if($aonly == '2'){echo 'selected="selected"';} echo '>Advanced +</option>
								</select></td>
							</tr>
						<tr><td colspan="2" class="fontcolor">&nbsp;</td></tr>
						<tr>
							<td width="150" class="fontcolor"><b><i>Series Type?</i></b><br /> <i>Is it Anime, is it a Drama?</i> ** do not use **</td>
							<td>
								<select name="seriesList" style="color: #000000;">
									<option value="0" '; if($seriesList == '0'){echo 'selected="selected"';} echo '>Anime</option>
									<option value="1" '; if($seriesList == '1'){echo 'selected="selected"';} echo '>Drama</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<br />
							</td>
						</tr>
					</table>
					</div>';
							echo '<input type="submit" class="SubmitForm" id="submit" name="submit" value="' . $SubmitTXT . '">';
							if(isset($_GET['ueid']))
							{
								echo '&nbsp;<input type="button" value="Back to the Tracker" onClick="$(\'#uploads-global-wrapper\').load(\'/scripts.php?view=uploads\'); return false;" />';
							}
							echo '</form>';
							echo '<script>
						$(function() {
							$(\'.form_results\').hide();
							$(\'.text-input\')
								.css({border:"1px solid #CCC"})
								.css({color:"#5A5655"})
								.css({font:"13px Verdana,Arial,Helvetica,sans-serif"})
								.css({padding:"2px"})
							;
							$(\'.text-input\').focus(function(){
								$(this).css({border:"1px solid #0C90BB"});
							});
							$(\'.text-input\').blur(function(){
								$(this).css({border:"1px solid #CCC"});
							});
							$(".SubmitForm").click(function() {';
							if($Type == 'add')
							{ //seoname, description2, category
								echo '
								$(\'label\').hide();
								var seriesName = $("input#seriesName").val();
								if (seriesName == "") {
									$("label#seriesNameError").show();
									$("input#seriesName").focus();
									return false;
								}
								var fullSeriesName = $("input#fullSeriesName").val();
								if (fullSeriesName == "") {
									$("label#fullSeriesNameError").show();
									$("input#fullSeriesName").focus();
									return false;
								}
								var romaji = $("input#romaji").val();
								if (romaji == "") {
									$("label#romajiError").show();
									$("input#romaji").focus();
									return false;
								}
								var kanji = $("input#kanji").val();
								if (kanji == "") {
									$("label#kanjiError").show();
									$("input#kanji").focus();
									return false;
								}
								var seoname = $("input#seoname").val();
								if (seoname == "") {
									$("label#seonameError").show();
									$("input#seoname").focus();
									return false;
								}
								';
							}
							else // its an edit.. duh
							{
							}
							echo '
								$.ajax({
									type: "POST",
									url: "/scripts.php",
									data: $(\'#SeriesForm\').serialize(),
									success: function(html) {
										if(html == \'Success\'){';
										if($Type == 'add')
										{
											echo '
											$("#SeriesForm")[0].reset();
											$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>" + fullSeriesName + " Added Successfully</div>");';
										}
										else // its an edit.. duh
										{
											echo '
											$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Update Successful</div>");';
										}
										echo '
											$(".form_results").delay(8000).slideUp();
										}
										else{
											alert(html);
											$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
										}
									}
								});
								return false;
							});
							return false;
						});
						</script>';

				}
				else {
					echo 'WTF Were you doing, were you trying to be cool? Error S-002';
				}
			}
		}
	}

	/*private function ManageComments(){
	}*/

	private function ManageSettings(){
		if(!isset($_GET['subnode'])){
			echo $this->TopNav();
			echo '<br /><div align="center">Manage: <a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u='.$this->uid.'&node=settings&subnode=permissions\'); return false;">Permissions</a> | <a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u='.$this->uid.'&node=settings&subnode=site-settings\'); return false;">Site Settings</a> | <a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u='.$this->uid.'&node=settings&subnode=site-groups\'); return false;">Groups</a> </div>
		<div id="ContentStuff" class="ContentStuff" style="display:hidden;"><div align="center">Choose From the Above Options.</div></div>';
		}
		if(isset($_GET['subnode']) && $_GET['subnode'] == 'permissions'){
			$query = mysql_query("SELECT * FROM permissions");
			$count = mysql_num_rows($query);
			echo '<div id="form_results" class="form_results">&nbsp;</div>';
			echo '<form method="POST" action="#" id="SettingsForm"><input type="hidden" name="uid" value="'.$this->uid.'" />';
			echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';
			echo '<table>';
			$i = 1;
			while($row = mysql_fetch_assoc($query)){
				if($row['parent'] == ''){
					if($i > 1){
						echo '</tr>'."\n";
					}
					if($i != ($count-1)){
						echo '<tr>'."\n";
					}
					$pluslink = '+';
				}
				else {
					echo '</tr><tr>'."\n";
					$pluslink = '&nbsp;';
				}
				echo '<td width="2%"><!-- parent: '.$i.' -->'.$pluslink.'</td>'."\n";
				echo '<td><div align="left">Name: '.$row['name'].'</div></td>'."\n";
				echo '</tr>'."\n";
				echo '<tr>'."\n";
				echo '<td colspan="2">Permissions:<br /> '.$this->BuildGroupPermissionObjects($row['id']).'</td>'."\n";
				if($i == $count){
					echo '</tr>'."\n";
				}
				$i++;
			}
			echo '</table>';
			echo '</div>';
			echo '
			<input type="hidden" id="method" class="method" value="SettingsSubmit" name="method" /><input type="submit" class="SubmitForm" id="submit" name="submit" value="Submit">';
			echo '</form>';
		}
		else if(isset($_GET['subnode']) && $_GET['subnode'] == 'site-settings'){
			$query = mysql_query("SELECT * FROM settings");
			$count = mysql_num_rows($query);
			echo '<div id="form_results" class="form_results">&nbsp;</div>';
			echo '<form method="POST" action="#" id="SettingsForm"><input type="hidden" name="uid" value="'.$this->uid.'" />';
			echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';
			echo '<table>';
			echo '<tr><td><b>Setting</b></td><td><b>Value</b></td></tr>';
			while($row = mysql_fetch_assoc($query)){
				echo '<tr><td width="50%" valign="top"><div align="left">'.$row['display_name'].'</div></td>'."\n";
				echo '<td><input type="text" name="'.$row['name'].'" id="'.$row['name'].'" value="'.$row['value'].'" /></td>'."\n";
				echo '</tr>'."\n";
			}
			echo '</table>';
			echo '</div>';
			echo '
			<input type="hidden" id="method" class="method" value="SiteSettingsSubmit" name="method" /><input type="submit" class="SubmitForm" id="submit" name="submit" value="Submit">';
			echo '</form>';
		}
		else if(isset($_GET['subnode']) && $_GET['subnode'] == 'site-groups'){
			$query = mysql_query("SELECT * FROM site_groups");
			$count = mysql_num_rows($query);
			echo '<div id="form_results" class="form_results">&nbsp;</div>';
			echo '<form method="POST" action="#" id="SettingsForm"><input type="hidden" name="uid" value="'.$this->uid.'" />';
			echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';
			echo '<table>';
			echo '<tr><td><b>ID Number</b></td><td><b>Group ID#</b></td></tr>';
			while($row = mysql_fetch_assoc($query)){
				echo '<tr><td width="25%">'.$row['groupID'].'</td>'."\n";
				echo '<td valign="top"><div align="left"><input type="text" name="'.$row['groupName'].'" id="'.$row['name'].'" value="'.$row['groupName'].'" /></div></td>'."\n";
				echo '</tr>'."\n";
			}
			echo '</table>';
			echo '</div>';
			echo '
			<input type="hidden" id="method" class="method" value="GroupSettingsSubmit" name="method" /><input type="submit" class="SubmitForm" id="submit" name="submit" value="Submit">';
			echo '</form>';
		}
		else {
		}
			echo '<script>
						$(function() {
							$(\'.form_results\').hide();

							$(".SubmitForm").click(function() {
								$.ajax({
									type: "POST",
									url: "/scripts.php",
									data: $(\'#SettingsForm\').serialize(),
									success: function(html) {
										if(html == \'Success\'){
											$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Update Successful</div>");
											$(\'.form_result\').delay(8000).slideUp();
										}
										else{
											$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
										}
									}
								});
								return false;
							});
							return false;
						});
						</script>';
	}

	private function ManageForums(){
		if(!isset($_GET['subnode'])){
			echo $this->TopNav();
			echo '<br />
			<div align="center">Manage:
			<a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u='.$this->uid.'&node=forums&subnode=forums\'); return false;">Forums</a>
			| <a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u='.$this->uid.'&node=forums&subnode=threads\'); return false;">Threads</a>
			| <a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u='.$this->uid.'&node=forums&subnode=posts\'); return false;">Posts</a>
			</div>
			<div id="ContentStuff" class="ContentStuff" style="display:hidden;"><div align="center">Choose From the Above Options.</div></div>';
		}
		else {
			if($_GET['subnode'] == 'forums'){
			}
			else if($_GET['subnode'] == 'threads'){
			}
			else if($_GET['subnode'] == 'posts'){
			}
			else {
				echo 'Couldn\'t figure out your request.';
			}
		}
	}

	/*
	Function of the following code is to hook into manage settings and provide a user friendly look to the permissions window
	*/

	private function BuildGroupPermissionObjects($pid = NULL){
		$return = '';
		// first step is to get the list of groups available.
		$query = "SELECT groupID, groupName FROM site_groups";
		$results = mysql_query($query);
		$i = 0;
		while($group_array = mysql_fetch_array($results)){
			$subquery = "SELECT id FROM permissions_objects WHERE permission_id = $pid AND oid = ".$group_array[0];
			$query = mysql_query($subquery);
			$count = mysql_num_rows($query);
			if($count == 1){
				$checked = ' checked="checked"';
			}
			else {
				$checked = '';
			}
			$return .= '<div style="display:inline;float:left;width:185px;"><input type="checkbox" name="pid-'.$pid.'[]" value="'.$group_array[0].'"'.$checked.' id="pid-'.$pid.'_'.$group_array[0].'" /><label style="display:inline;color:#000;" for="pid-'.$pid.'_'.$group_array[0].'">'.$group_array[1].'</label>  &nbsp;</div>';
			$i++;
			if($i == 3 || $i == 6 || $i == 9 || $i == 12){
				$return .= '<br />';
			}
		}
		return $return;
	}

	//The following is used to process posted data..
	private function PrcessPostedData(){
		if($_POST['method'] == 'SettingsSubmit' && $this->ValidatePermission(61) == TRUE){
			$this->uid = $_POST['uid'];
			$this->ModRecord('Update Site Settings');
			//begin not so complex-complex operations
			foreach($_POST as $name => $value) {
				//first we need to check to loop through variables..
				if(is_array($value)){
					$permissionsid = substr($name, 4);
					$query = "SELECT groupID FROM site_groups";
					$results = mysql_query($query);
					$i = 0;
					// we want to query the database, to get a full list of groups, that way we can delete objects where need be..
					while($row = mysql_fetch_array($results)){
						$query = mysql_query("SELECT id FROM permissions_objects WHERE permission_id = ".$permissionsid." AND type = 1 AND oid = ".$row[0]);
						$count = mysql_num_rows($query);
						if(in_array($row[0],$value)){ //if the presented valiable is IN the array, that means it needs to be added
							if($count == 1){ //we have found the package, nothing needed.
							}
							else { //for everything else you need to add it.
								mysql_query("INSERT INTO permissions_objects (id, oid, type, permission_id) VALUES (NULL, '".mysql_real_escape_string($row[0])."', '1', '".mysql_real_escape_string($permissionsid)."')") or die(mysql_error());
							}
						}
						else { //its not in the array, it can be deleted
							if($count == 1){ // the package was found in the DB, but we dont need it, so it can be deleted
								mysql_query("DELETE FROM permissions_objects WHERE permission_id = ".$permissionsid." AND type = 1 AND oid = ".$row[0]);
							}
							else { // it's not in the database anyway, go home..
							}
						}
					}
				}
				else {
					// it's not an array so lets forget about it.
				}
			}
			echo 'Success';
		}
		else if($_POST['method'] == 'EditSeries' && $this->ValidatePermission(23) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$sid = mysql_real_escape_string($_POST['sid']);
				$seriesName = mysql_real_escape_string($_POST['seriesName']);
				$fullSeriesName = mysql_real_escape_string($_POST['fullSeriesName']);
				$kanji = mysql_real_escape_string($_POST['kanji']);
				$romaji = mysql_real_escape_string($_POST['romaji']);
				$synonym = mysql_real_escape_string($_POST['synonym']);
				$seoname = mysql_real_escape_string($_POST['seoname']);
				$videoServer = mysql_real_escape_string($_POST['videoServer']);
				$active = mysql_real_escape_string($_POST['active']);
				$description = mysql_real_escape_string($_POST['description2']);
				$ratingLink = mysql_real_escape_string($_POST['ratingLink']);
				$stillRelease = mysql_real_escape_string($_POST['stillRelease']);
				$Movies = mysql_real_escape_string($_POST['Movies']);
				$moviesOnly = mysql_real_escape_string($_POST['moviesOnly']);
				$OVA = mysql_real_escape_string($_POST['OVA']);
				$noteReason = mysql_real_escape_string($_POST['noteReason']);
				$aonly = mysql_real_escape_string($_POST['aonly']);
				$prequelto = mysql_real_escape_string($_POST['prequelto']);
				$sequelto = mysql_real_escape_string($_POST['sequelto']);
				$category = mysql_real_escape_string($_POST['category']);
				$seriesType = mysql_real_escape_string($_POST['seriesType']);
				$seriesList = mysql_real_escape_string($_POST['seriesList']);

				$fullSeriesName = htmlspecialchars($fullSeriesName);
				$kanji = htmlspecialchars($kanji);
				$description = nl2br($description);
				$noteReason = nl2br($noteReason);
				//echo $description;
				mysql_query("SET NAMES 'utf8'");
				$query = 'UPDATE series
					SET seriesName=\'' . mysql_real_escape_string($seriesName) .'\',
					fullSeriesName=\'' . $fullSeriesName . '\',
					romaji=\'' . mysql_real_escape_string($romaji) . '\',
					kanji=\'' . mysql_real_escape_string($kanji) . '\',
					synonym=\'' . $synonym . '\',
					seoname=\'' . mysql_real_escape_string($seoname) . '\',
					videoServer=\'' . mysql_real_escape_string($videoServer) . '\',
					active=\'' . mysql_real_escape_string($active) . '\',
					description=\'' . $description . '\',
					ratingLink=\'' . mysql_real_escape_string($ratingLink) . '\',
					stillRelease=\'' . mysql_real_escape_string($stillRelease) . '\',
					Movies=\'' . mysql_real_escape_string($Movies) . '\',
					moviesOnly=\'' . mysql_real_escape_string($moviesOnly) . '\',
					OVA=\'' . mysql_real_escape_string($OVA) . '\',
					noteReason=\'' . $noteReason . '\',
					aonly=\'' . mysql_real_escape_string($aonly) . '\',
					prequelto=\'' . mysql_real_escape_string($prequelto) . '\',
					sequelto=\'' . mysql_real_escape_string($sequelto) . '\',
					category=\'' . $category . '\',
					seriesType=\'' . mysql_real_escape_string($seriesType) . '\',
					seriesList=\'' . mysql_real_escape_string($seriesList) . '\'
					WHERE id=' . $sid . '';
				mysql_query($query) or die('Error : ' . mysql_error());
				$this->updatePreSequel($sid, $prequelto, $sequelto);

				$this->ModRecord('Edit series, ' . $fullSeriesName);
				echo 'Success';
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'AddSeries' && $this->ValidatePermission(22) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$seriesName = mysql_real_escape_string($_POST['seriesName']);
				$fullSeriesName = mysql_real_escape_string($_POST['fullSeriesName']);
				$kanji = mysql_real_escape_string($_POST['kanji']);
				$romaji = mysql_real_escape_string($_POST['romaji']);
				$synonym = mysql_real_escape_string($_POST['synonym']);
				$seoname = mysql_real_escape_string($_POST['seoname']);
				$videoServer = mysql_real_escape_string($_POST['videoServer']);
				$active = mysql_real_escape_string($_POST['active']);
				$description = mysql_real_escape_string($_POST['description2']);
				$ratingLink = mysql_real_escape_string($_POST['ratingLink']);
				$stillRelease = mysql_real_escape_string($_POST['stillRelease']);
				$Movies = mysql_real_escape_string($_POST['Movies']);
				$moviesOnly = mysql_real_escape_string($_POST['moviesOnly']);
				$OVA = mysql_real_escape_string($_POST['OVA']);
				$noteReason = mysql_real_escape_string($_POST['noteReason']);
				$aonly = mysql_real_escape_string($_POST['aonly']);
				$prequelto = mysql_real_escape_string($_POST['prequelto']);
				$sequelto = mysql_real_escape_string($_POST['sequelto']);
				$category = mysql_real_escape_string($_POST['category']);
				$seriesType = mysql_real_escape_string($_POST['seriesType']);
				$seriesList = mysql_real_escape_string($_POST['seriesList']);

				mysql_query("SET NAMES 'utf8'");
				$query = "INSERT INTO series (seriesName, fullSeriesName, romaji, kanji, synonym, seoname, videoServer, active, description, ratingLink, stillRelease, Movies, moviesOnly, OVA, noteReason, aonly, prequelto, sequelto, category, seriesType, seriesList) VALUES ('$seriesName', '$fullSeriesName', '$romaji', '$kanji', '$synonym', '$seoname', '$videoServer', '$active', '$description', '$ratingLink', '$stillRelease', '$Movies', '$moviesOnly', '$OVA', '$noteReason', '$aonly', '$prequelto', '$sequelto', '$category', '$seriesType', '$seriesList')";
				mysql_query($query) or die('Could not connect, way to go retard: ' . mysql_error());

				$sid = $this->SingleVarQuery("SELECT id FROM series WHERE seriesName = '" . $seriesName . "'",'id'); //Get the Series ID through seriesName
				$this->updatePreSequel($sid, $prequelto, $sequelto);
				$this->ModRecord('Add Series, ' . $FullSeriesName);
				echo 'Success';
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'MassEpisodeUpdate' && $this->ValidatePermission(72) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$seriesname = mysql_real_escape_string($_POST['seriesname']);
				$fullSeriesName = mysql_real_escape_string($_POST['fullSeriesName']);
				$old_vidwidth = mysql_real_escape_string($_POST['old_vidwidth']);
				$old_vidheight = mysql_real_escape_string($_POST['old_vidheight']);
				$old_epprefix = mysql_real_escape_string($_POST['old_epprefix']);
				$old_subGroup = mysql_real_escape_string($_POST['old_subGroup']);
				$old_videotype = mysql_real_escape_string($_POST['old_videotype']);
				$vidwidth = mysql_real_escape_string($_POST['vidwidth']);
				$vidheight = mysql_real_escape_string($_POST['vidheight']);
				$epprefix = mysql_real_escape_string($_POST['epprefix']);
				$subGroup = mysql_real_escape_string($_POST['subGroup']);
				$videotype = mysql_real_escape_string($_POST['videotype']);
				$UpdateType = $_POST['UpdateType'];

				if($old_epprefix != $epprefix) // if the episode prefix changed, then it nullifies the episodes images..
				{
					$QuerySet = ", image = '0'";
				}
				else
				{
					$QuerySet = "";
				}

				if($UpdateType == 0) // Update ONLY episodes
				{
					$QueryAddon = " AND Movie = 0";
				}
				else if($UpdateType == 1) //Update ONLY movies
				{
					$QueryAddon = " AND Movie = 1";
				}
				else if($UpdateType == 2) // Update BOTH movies and episodes..
				{
					$QueryAddon = "";
				}
				else // default to ONLY episodes.. best and safest practice..
				{
					$QueryAddon = " AND Movie = 0";
				}

				$fullSeriesName = stripslashes($fullSeriesName);
				//echo $description;
				mysql_query("SET NAMES 'utf8'");
				$query = 'UPDATE episode
					SET vidwidth=\'' . $vidwidth .'\',
					vidheight=\'' . $vidheight . '\',
					epprefix=\'' . $epprefix . '\',
					subGroup=\'' . $subGroup . '\',
					videotype=\'' . $videotype . '\''.$QuerySet.'
					WHERE seriesname=\'' . $seriesname . '\'' . $QueryAddon;
				mysql_query($query) or die('Error : ' . mysql_error());
				$this->ModRecord('Mass Episode Edit for Series ' . $fullSeriesName . ', old -vh:' . $old_vidheight . ', -vw:' . $old_vidwidth . ', -pref:' . $old_epprefix . ', -sg:' . $old_subGroup . ', -vt:' . $old_videotype . '');
				echo 'Success';
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'AdminSeriesSearch' && $this->ValidatePermission(24) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				if($_POST['SeriesName'] == '')
				{
					echo '<div align="center">Please input the name of a Series!</div>';
				}
				else
				{
					$input = mysql_real_escape_string($_POST['SeriesName']);
					mysql_query("SET NAMES 'utf8'");
					$query   = "SELECT id, seriesName, fullSeriesName, seoname, kanji, romaji, ratingLink, category FROM series WHERE ( fullSeriesName LIKE '%".$input."%' OR romaji LIKE '%".$input."%' OR kanji LIKE '%".$input."%' ) ORDER BY seriesName ASC LIMIT 100";
					$result  = mysql_query($query) or die('Error : ' . mysql_error());
					$ts = mysql_num_rows($result);
					if($ts < 1)
					{
						echo '<div align="center">There were no results found for: <b>' . stripslashes($input) . '</b></div>';
					}
					else
					{
						echo '<div align="center">Showing ' . $ts . ' Results for: <b>' . stripslashes($input) . '</b></div>';
						echo '<div style="height:360px;overflow-y:scroll;overflow-x:none;">';
						while($row = mysql_fetch_array($result))
						{
							echo '<div style="padding:5px 0px 5px 0px;height:210px;">
									<div style="float:left;"><img src="//static.ftw-cdn.com/site-images/seriesimages/' . $row['id'] . '.jpg" alt="" height="200px" /></div>
									<div style="display:inline;padding-left:10px;">
										<span><a href="/anime/' . $row['seoname'] . '/">View Series</a> | <a href="#" onclick="$(\'#ContentStuff\').load(\'/scripts.php?view=management&u=' . $_POST['uid']. '&node=series&stage=edit&step=before&sid=' . $row['id'] . '\'); return false;">Edit Series</a></span><br />
										&nbsp;<span><b>Series Name:</b> ' . stripslashes($row['fullSeriesName']). '</span><br />
										&nbsp;<span><b>Seo Name:</b> ' . $row['seoname'] . '</span><br />
										&nbsp;<span><b>Romaji:</b> ' . $row['romaji'] . '</span><br />
										&nbsp;<span><b>Kanji:</b> ' . $row['kanji'] . '</span><br />
										&nbsp;<span><b>Categories:</b> ' . $row['category'] . '</span>
									</div>
								</div>';
						}
						echo '</div>';
					}
				}
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'SeriesAnnouncementBuilder' && $this->ValidatePermission(73) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$query = "SELECT seoname, fullSeriesName, description FROM series WHERE";
				$i = 0;
				foreach($_POST['sid'] as $name => $value)
				{
					if($i > 0)
					{
						$query .= " OR";
					}
					$query .= " id = $value";
					$i++;
				}
				$result = mysql_query($query);
				echo '<textarea style="height:175px;overflow-y:scroll;overflow-x:none;border:1px solid #0C90BB;width:100%" onclick="this.select()">';
				while($row = mysql_fetch_array($result))
				{
					$description = stripslashes($row['description']);
					echo '<br /><span style="font-size:11px;"><span style="font-family: verdana, geneva, sans-serif; "><a href="http://www.animeftw.tv/anime/' . $row['seoname'] . '/">' . stripslashes($row['fullSeriesName']) . '</a></span></span><br />'."\n";
					echo '<strong style="font-family: verdana, geneva, sans-serif; font-size: 11px; ">Synopsis:&nbsp;</strong><span style="font-size:11px;"><span style="font-family: verdana, geneva, sans-serif; ">' . $description . '</span><br />'."\n\r";
				}
				//echo print_r($_POST);
				echo '</textarea>';
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'UploadsAddition')
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$episodes = mysql_real_escape_string($_POST['episodesdoing'])."/".mysql_real_escape_string($_POST['episodetotal']);
				$dimmensions = mysql_real_escape_string($_POST['width'])."x".mysql_real_escape_string($_POST['height']);
				$query = "INSERT INTO uestatus (series, prefix, episodes, type, resolution, status, user, updated, anidbsid, fansub) VALUES ('" . mysql_real_escape_string($_POST['Series']) . "', '" . mysql_real_escape_string($_POST['Prefix']) . "', '" . $episodes . "', '" . mysql_real_escape_string($_POST['Type']) . "', '" . $dimmensions . "', '" . mysql_real_escape_string($_POST['Status']) . "', '" . mysql_real_escape_string($_POST['user']) . "', NOW(), '" . mysql_real_escape_string($_POST['anidb']) . "', '" . mysql_real_escape_string($_POST['fansub']) . "')";
				mysql_query($query) or die(mysql_error());
				echo 'Success';
				//echo $query;
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'UploadsEdit')
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$episodes = mysql_real_escape_string($_POST['episodesdoing'])."/".mysql_real_escape_string($_POST['episodetotal']);
				$dimmensions = mysql_real_escape_string($_POST['width'])."x".mysql_real_escape_string($_POST['height']);
				$query = "UPDATE `uestatus` SET
				`series` = '" . mysql_real_escape_string($_POST['Series']) . "',
				`prefix` = '" . mysql_real_escape_string($_POST['Prefix']) . "',
				`episodes` = '" . $episodes . "',
				`type` = '" . mysql_real_escape_string($_POST['Type']) . "',
				`resolution` = '" . $dimmensions . "',
				`status` = '" . mysql_real_escape_string($_POST['Status']) . "',
				`user` = '" . mysql_real_escape_string($_POST['uploader']) . "',
				`updated` = NOW(),
				`anidbsid` = '" . mysql_real_escape_string($_POST['anidb']) . "',
				`fansub` = '" . mysql_real_escape_string($_POST['fansub']) . "'
				WHERE `uestatus`.`ID` = " . mysql_real_escape_string($_POST['ueid']);
				mysql_query($query) or die(mysql_error());
				echo 'Success';
				//echo $query;
			}
			else
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'UserEdit')
		{
			// we need to make sure an id is given.. we will use this later
			if(!isset($_POST['id']))
			{
					echo 'Error: Your post was nulled.';
			}
			else
			{
				// query the database, we need the basic information of the user we are editing
				$query = "SELECT `Username`, `display_name`, `Password`, `Level_access` FROM users WHERE ID = " . mysql_real_escape_string($_POST['id']);
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				if(($_POST['id'] == $this->UserArray[1]) || ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
				{
					// #1, if the user is the same as the submitted data, then they can pass
					// #2, if the user posting the data is an admin or a manager, allow them through

                    // Check if the user has the ability to change their display_name.
                    // We will allow managers to change them as well as admins.
                    if ((($this->UserArray[1] != $_POST['id'] && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2)) || ($_POST['id'] == $this->UserArray[1] && ($this->UserArray[2] != 3))) && (($_POST['displayName'] != $row['display_name'] && $_POST['displayName'] != $row['Username']) || ($_POST['displayName'] == $row['Username'] && $_POST['displayName'] != $row['display_name']))) {
                        // We need to check to make sure this display name is not in existance.
                        $query = "SELECT COUNT(ID) AS userNameCount FROM `users` WHERE '" . mysql_real_escape_string(@$_POST['displayName']) . "' IN(`Username`,`display_name`);";
                        $result = mysql_query($query);
                        $usercount = mysql_fetch_object($result);
                        if ($usercount->userNameCount == 0 || ($usercount->userNameCount > 0 && $_POST['displayName'] == $row['Username']) || ($_POST['displayName'] == $this->UserArray[5] && $_POST['id'] == $this->UserArray[1])) {
                            $display_name = ' `display_name`=\'' . mysql_real_escape_string(@$_POST['displayName']) . '\',';
                        } else {
        					echo 'Display Name is already taken, please try another.';
                            exit;
                        }
                    } else {
                        $display_name = '';
                    }
					$rid = @$_POST['id'];
					$sid = $_POST['s'];
					$active = @$_POST['Active'];
					$reason = @$_POST['Reason'];
					$level_access = urldecode(@$_POST['Level_access']);
					$candownload = urldecode(@$_POST['canDownload']);
					$firstname = urldecode(@$_POST['firstName']);
					$lastname = urldecode(@$_POST['lastName']);
					$gender = urldecode(@$_POST['gender']);
					$ageday = urldecode(@$_POST['ageDate']);
					$agemonth = urldecode(@$_POST['ageMonth']);
					$ageyear = urldecode(@$_POST['ageYear']);
					$country = urldecode(@$_POST['country']);
					$msn = urldecode(@$_POST['msnAddress']);
					$aim = urldecode(@$_POST['aimName']);
					$yim = urldecode(@$_POST['yahooName']);
					$skype = urldecode(@$_POST['skypeName']);
					$icq = urldecode(@$_POST['icqNumber']);
					$showemail = urldecode(@$_POST['showEmail']);
					$Alias = urldecode(@$_POST['Alias']); // Email Alias
					$avataractive = urldecode(@$_POST['avatarActivate']);
					$avatarextension = urldecode(@$_POST['avatarExtension']);
					$personalmsg = urldecode(@$_POST['personalMsg']);
					$membertitle = urldecode(@$_POST['memberTitle']);
					$aboutme = urldecode(@$_POST['aboutMe']);
					$interests = urldecode(@$_POST['Interests']);
					$sigactive = urldecode(@$_POST['signatureActive']);
					$Signature = urldecode(@$_POST['Signature']);
					$showChat = urldecode(@$_POST['showChat']);
					$theme = urldecode(@$_POST['theme']);
					$notes = urldecode(@$_POST['notes']);
					$preffix = urldecode(@$_POST['preffix']);

					if($Alias == '')
					{
						$EmailAlias = '`Alias`=NULL,';
					}
					else
					{
						$EmailAlias = ' `Alias`=\'' . mysql_real_escape_string($Alias) . '\',';
					}

					// if the users access level is an admin or manager, give them the ability to edit everything
					if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
					{
						$additional = ',' . $EmailAlias . $display_name . ' `canDownload`=\'' . mysql_real_escape_string($candownload) . '\', `Level_access`=\'' . mysql_real_escape_string($level_access) . '\', `advanceImage`=\'' . mysql_real_escape_string($preffix) . '\', `avatarActivate`=\'' . mysql_real_escape_string($avataractive) . '\', `avatarExtension`=\'' . mysql_real_escape_string($avatarextension) . '\',' .
                        ' `personalMsg`=\'' . mysql_real_escape_string($personalmsg) . '\', `memberTitle`=\'' . mysql_real_escape_string($membertitle) . '\', `aboutMe`=\'' . mysql_real_escape_string($aboutme) . '\', `interests`=\'' . mysql_real_escape_string($interests) . '\', `signatureActive`=\'' . mysql_real_escape_string($sigactive) . '\', `Signature`=\'' . mysql_real_escape_string($Signature) . '\', `notes`=\'' . mysql_real_escape_string($notes) . '\'';
					}
					else if($this->UserArray[2] == 4 || $this->UserArray[2] == 5 || $this->UserArray[2] == 6 || $this->UserArray[2] == 7)
					{
						//AMs and staff can see these...
						if($Signature != '')
						{
							$additional = ',' . $EmailAlias . ' `advanceImage`=\'' . mysql_real_escape_string($preffix) . '\', `aboutMe`=\'' . mysql_real_escape_string($aboutme) . '\', `interests`=\'' . mysql_real_escape_string($interests) . '\', `Signature`=\'' . mysql_real_escape_string($Signature).'\', `signatureActive`=\'yes\'';
						}
						else
						{
							$additional = ',' . $EmailAlias . ' `advanceImage`=\'' . mysql_real_escape_string($preffix) . '\', `aboutMe`=\'' . mysql_real_escape_string($aboutme) . '\', `interests`=\'' . mysql_real_escape_string($interests) . '\', `Signature`=\'' . mysql_real_escape_string($Signature).'\'';
						}
					}
					else {
					// basic members can change these..
						$additional = '';
					}
					$query = 'UPDATE users SET
					`firstName`=\'' . mysql_real_escape_string($firstname) . '\',
					`lastName`=\'' . mysql_real_escape_string($lastname) . '\',
					`gender`=\'' . mysql_real_escape_string($gender) . '\',
					`ageDate`=\'' . mysql_real_escape_string($ageday) . '\',
					`ageMonth`=\'' . mysql_real_escape_string($agemonth) . '\',
					`ageYear`=\'' . mysql_real_escape_string($ageyear) . '\',
					`country`=\'' . mysql_real_escape_string($country) . '\',
					`msnAddress`=\'' . mysql_real_escape_string($msn) . '\',
					`aimName`=\'' . mysql_real_escape_string($aim) . '\',
					`yahooName`=\'' . mysql_real_escape_string($yim) . '\',
					`skypeName`=\'' . mysql_real_escape_string($skype) . '\',
					`icqNumber`=\'' . mysql_real_escape_string($icq) . '\',
					`showEmail`=\'' . mysql_real_escape_string($showemail) . '\',
					`theme`=\'' . mysql_real_escape_string($theme) . '\'
					'.$additional.'
					WHERE `ID`=\'' . mysql_real_escape_string($rid) . '\'';
					//echo $query;
					//echo $_SERVER['REQUEST_URI'];
   					mysql_query($query) or die('Error : ' . mysql_error());
					echo 'Success';
					$this->ModRecord("Account id " . $rid . " edited by " . $row['Username']);
				}
				else
				{
					echo 'Failed: Authorization was wrong.';
				}
			}
		}
		else if($_POST['method'] == 'EditStoreItem' && $this->ValidatePermission(84) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				if(!isset($_POST['id']) || !is_numeric($_POST['id']))
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: No ID provided.</div>';
				}
				else
				{
					$this->uid = $_POST['uid'];
					$this->ModRecord('Edit Item to the Store');
					$results = mysql_query("UPDATE store_items SET category = '" . mysql_real_escape_string($_POST['item-categories']) . "', name = '" . mysql_real_escape_string($_POST['name']) . "', price = '" . mysql_real_escape_string($_POST['price']) . "', availability = '" . mysql_real_escape_string($_POST['availability']) . "', description = '" . mysql_real_escape_string($_POST['description']) . "', productnum = '" . mysql_real_escape_string($_POST['productnum']) . "', pictures = '" . mysql_real_escape_string($_POST['pictures']) . "', picturetype = '" . mysql_real_escape_string($_POST['picturetype']) . "', weight = '" . mysql_real_escape_string($_POST['weight']) . "' WHERE id = " . mysql_real_escape_string($_POST['id']));
					if(!$results)
					{
						echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysql_error() . '</div>';
						exit;
					}
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Update Completed.</div>';
				}
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'AddStoreItem' && $this->ValidatePermission(86) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$this->ModRecord('Add Item to the Store');
				$results = mysql_query("INSERT INTO `mainaftw_anime`.`store_items` (`id` ,`category` ,`name` ,`price` ,`availability` ,`description` ,`productnum` ,`pictures` ,`picturetype` ,`weight`) VALUES (NULL , '" . mysql_real_escape_string($_POST['item-categories']) . "', '" . mysql_real_escape_string($_POST['name']) . "', '" . mysql_real_escape_string($_POST['price']) . "', '" . mysql_real_escape_string($_POST['availability']) . "', '" . mysql_real_escape_string($_POST['description']) . "', '" . mysql_real_escape_string($_POST['productnum']) . "', '" . mysql_real_escape_string($_POST['pictures']) . "', '" . mysql_real_escape_string($_POST['picturetype']) . "', '" . mysql_real_escape_string($_POST['weight']) . "');");
				if(!$results)
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysql_error() . '</div>';
					exit;
				}
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Addition Compelted. <a href="#" onClick="AdminFunction(\'manage-stock\',\'edit\',\'' . mysql_insert_id() . '\'); return false;">Add Inventory for this Item.</a></div>';
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'EditEmail')
		{
			if((isset($_POST['id']) && ($_POST['id'] == $this->UserArray[1])) || (isset($_POST['id']) && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2)))
			{
				if(isset($_POST['email']) &&($_POST['email'] == $_POST['email_confirm']))
				{
					$query = "SELECT ID FROM users WHERE Email = '" . mysql_real_escape_string($_POST['email']) . "'";
					$result = mysql_query($query);
					$count = mysql_num_rows($result);
					if($count > 0)
					{
						echo '<div style="font-size:9px;">Error: Email Taken Already.<br />Try Again.</div>';
					}
					else
					{
						// we need to validate that the password is correct before letting them change it.
						$result = mysql_query("SELECT ID FROM users WHERE ID = " . mysql_real_escape_string($_POST['id']) . " AND Password = '" . mysql_real_escape_string(md5($_POST['password'])) . "'");
						$count = mysql_num_rows($result);
						if($count > 0)
						{
							mysql_query("UPDATE users SET Email = '" . mysql_real_escape_string($_POST['email']) . "' WHERE ID='" . mysql_real_escape_string($_POST['id']) . "'");
							$subject = "Email Change at AnimeFTW.tv";
							$message = "Hello " . $_POST['username'] . ", this is just a friendly email to let you know that your Email was changed on our website. If this was unexpected, we encourage you to go to your profile https://".$_SERVER['HTTP_HOST']."/user/" . $_POST['username'] . " and make sure that it is up to date.\n\n If you have any questions please let us know by posting in our forums, http://".$_SERVER['HTTP_HOST']."/forums . \n\n Regards, \n\n FTW Entertainment LLC & AnimeFTW.tv Staff.";
							$this->NewSendMail($subject,$_POST['email'],$message);
							$this->NewSendMail($subject,$_POST['oldemail'],$message);
							echo '<div style="font-size:9px;"><!-- Success -->Password Changed Successfully.</div>';
						}
						else
						{
							echo '<div style="font-size:9px;">Error: You current password was wrong.</div>';
						}
					}
				}
				else
				{
					echo 'Email Address not supplied.';
				}
				// check the email, if it's in the system already they can't change it to that and let them know.
			}
			else
			{
				print_r($this->UserArray);
			}
		}

		else if($_POST['method'] == 'EditPassword')
		{
			if((isset($_POST['id']) && ($_POST['id'] == $this->UserArray[1])) || (isset($_POST['id']) && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2)))
			{
				if(isset($_POST['current-password']) &&($_POST['pw_c'] == $_POST['pw']))
				{
						// we need to validate that the password is correct before letting them change it.
						$result = mysql_query("SELECT `ID`, `Email` FROM users WHERE ID = " . mysql_real_escape_string($_POST['id']) . " AND Password = '" . mysql_real_escape_string(md5($_POST['current-password'])) . "'");
						$count = mysql_num_rows($result);
						if($count > 0)
						{
							mysql_query("UPDATE users SET Password = '" . mysql_real_escape_string(md5($_POST['pw'])) . "' WHERE ID='" . mysql_real_escape_string($_POST['id']) . "'");
							$subject = "Password Change at AnimeFTW.tv";
							$message = "Hello " . $_POST['Username'] . ", this is just a friendly email to let you know that your Password was changed on our website. If this was unexpected, we encourage you to go to your profile http://" . $_SERVER['HTTP_HOST'] . "/user/" . strtolower($_POST['Username']) . " and make sure that it is up to date.\n\n If you have any questions please let us know by posting in our forums, http://" . $_SERVER['HTTP_HOST'] . "/forums.\n\n Regards,\nFTW Entertainment LLC & AnimeFTW.tv Staff.";
							$row = mysql_fetch_assoc($result);
							$this->NewSendMail($subject,$row['Email'],$message);
							echo '<div style="font-size:9px;"><!-- Success -->Email Changed Successfully.</div>';
						}
						else
						{
							echo '<div style="font-size:9px;">Error: Your current password was wrong.</div>';
						}
				}
				else
				{
					echo 'Email Address not supplied.';
				}
				// check the email, if it's in the system already they can't change it to that and let them know.
			}
			else
			{
				print_r($this->UserArray);
			}
		}
		else {
			echo 'You posted a method of: '.$_POST['method'].' And it has not been setup yet.';
		}
	}

	private function updatePreSequel($sid, $prequelto, $sequelto)
	{
		if($prequelto != 0)//If the prequel is updated, we update that series sequel to this one.
		{
			$query = 'UPDATE series SET sequelto=\'' . mysql_real_escape_string($sid) . '\' WHERE id=' . mysql_real_escape_string($prequelto) . '';
			mysql_query($query) or die('Error : ' . mysql_error());
		}
		if($sequelto != 0)//If the sequel is, or also was updated with the prequel, we update that series prequel to this one.
		{
			$query = 'UPDATE series SET prequelto=\'' . mysql_real_escape_string($sid) . '\' WHERE id=' . mysql_real_escape_string($sequelto) . '';
			mysql_query($query) or die('Error : ' . mysql_error());
		}
	}
}

?>
