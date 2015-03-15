<?php
/****************************************************************\
## FileName: users.class.php								 
## Author: Brad Riemann								 
## Usage: Users sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Users extends Config {

	public function __construct()
	{
		parent::__construct();
		if(isset($_GET['cookie']))
		{
			$this->tabCookie($_GET['cookie']);
		}
		else
		{
			echo '<div  class="body-container">';
			if(isset($_GET['stage']))
			{
				$stage = $_GET['stage'];
			}
			else 
			{
				$stage = '';
			}
			if(isset($_GET['page']))
			{
				$page = $_GET['page'];
			}
			else 
			{
				$page = 0;
			}
			if($this->ValidatePermission(2) == TRUE)
			{
				$this->ManageUsers($stage,$page);
			}
			echo '<div id="user-body-wrapper"></div>
			</div>';
		}
	}
	
	private function tabCookie($tab)
	{
		setcookie("manage-tab", $tab, time() + 31104000);
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
		$lactive = '/manage/ajax.php?node=users&stage=active';
		$linactive = '/manage/ajax.php?node=users&stage=inactive';
		$lsuspended = '/manage/ajax.php?node=users&stage=suspended';
		$ladvanced = '/manage/ajax.php?node=users&stage=advanced';
		$lflogins = '/manage/ajax.php?node=users&stage=failed-logins';
		$llogins = '/manage/ajax.php?node=users&stage=logins';
		$lfindusers = '/manage/ajax.php?node=users&stage=findusers';
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
		if($go == 4)
		{
			// we check to make sure they are logged in.
			if($this->UserArray[0] == 1)
			{
				$cid = mysql_real_escape_string($_GET['id']);
				if(isset($_GET['modaction']) && $_GET['modaction'] == 'delete')
				{
					$error = "<div class=\"redmsg\">$cid 's account was deleted successfully (NOT!)- This function will never work.. Super Failsafe!</div><br />";
					$fscript = 'Delete User';
					//$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $globalnonid . '\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'suspend')
				{
					$error = "<div class=\"redmsg\">The account suspension of $cid has been completed successfully.</div><br />";
					$fscript = 'Suspend User';
					$query = 'UPDATE users SET Active = 2 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'unforumban')
				{
					$error = "<div class=\"redmsg\">You have reinstated $cid's forum access successfully.</div><br />";
					$fscript = 'Forum UnBan';
					$query = 'UPDATE users SET forumBan = 0 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'forumban')
				{
					$error = "<div class=\"redmsg\">You have revoked $cid's forum access successfully.</div><br />";
					$fscript = 'Forum Ban';
					$query = 'UPDATE users SET forumBan = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'uncommentban')
				{
					$error = "<div class=\"redmsg\">$cid's Comment posting abilities have been reapproved.</div><br />";
					$fscript = 'Comment Un Ban';
					$query = 'UPDATE users SET postBan = 0 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'commentban')
				{
					$error = "<div class=\"redmsg\">$cid's Comment posting abilities have been denied.</div><br />";
					$fscript = 'Comment Ban';
					$query = 'UPDATE users SET postBan = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'pmban')
				{
					$error = "<div class=\"redmsg\">$cid will no longer be able to send PMs.</div><br />";
					$fscript = 'PM Ban';
					$query = 'UPDATE users SET messageBan = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'unpmban')
				{
					$error = "<div class=\"redmsg\">$cid will be able to Send Pms now.</div><br />";
					$fscript = 'PM Ban';
					$query = 'UPDATE users SET messageBan = 0 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else if(isset($_GET['modaction']) && $_GET['modaction'] == 'unsuspend')
				{
					$error = "$uid has been unsuspended successfully.";
					$fscript = 'UnSuspend User';
					$query = 'UPDATE users SET Active = 1 WHERE ID=\''.$cid.'\'';
					$validfun = TRUE;
				}
				else 
				{
					$error = 'Error: Your request was not processed.';
					$validfun = FALSE;
				}
				if($validfun == FALSE)
				{
					echo $error;
				}
				else 
				{
					mysql_query($query) or die('Error : ' . mysql_error());
					$this->ModRecord($fscript);
					echo 'Success';
				}
			}
			else{
				$error = 'Error: You submitted an invalid function, please try again.';
			}
		}
		
		//build the main nav
		echo '<table width="100%">';
		echo '<tr>';
		if($this->ValidatePermission(3) == TRUE)
		{
			echo '<td colspan="3"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$llogins.'\');return false;">Logins</a></div></td>';
			$logins = '<td colspan="3"><div align="center">'.$qlogins.'</div></td>';
		}
		else 
		{
			echo '<td colspan="3">&nbsp;</td>';
			$logins = '<td colspan="3">&nbsp;</td>';
		}
		if($this->ValidatePermission(4) == TRUE)
		{
			echo '<td colspan="3"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$lflogins.'\');return false;">Failed Logins</a></div></td>';
			$flogins = '<td colspan="3"><div align="center">'.$qflogins.'</div></td>';
		}
		else 
		{
			echo '<td colspan="3">&nbsp;</td>';
			$flogins = '<td colspan="3">&nbsp;</td>';
		}
		if($this->ValidatePermission(5) == TRUE)
		{
			echo '<td colspan="3"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$lfindusers.'\');return false;">Find Users</a></div></td>';
		}
		else 
		{
			echo '<td colspan="3">&nbsp;</td>';
		}
		echo '</tr><tr>';
		echo $logins.$flogins;
		echo '<td colspan="3">&nbsp;</td>';
		echo '</tr><tr>';
		if($this->ValidatePermission(6) == TRUE)
		{
			echo '<td colspan="2"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$lactive.'\');return false;">Active</a></div></td>';
			$active = '<td colspan="2"><div align="center">'.$qactive.'</div></td>';
		}
		else 
		{
			echo '<td colspan="2">&nbsp;</td>';
			$active = '<td colspan="2">&nbsp;</td>';
		}
		if($this->ValidatePermission(7) == TRUE)
		{
			echo '<td colspan="2"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$linactive.'\');return false;">Inactive</a></div></td>';
			$inactive = '<td colspan="2"><div align="center">'.$qinactive.'</div></td>';
		}
		else 
		{
			echo '<td colspan="2">&nbsp;</td>';
			$inactive = '<td colspan="2">&nbsp;</td>';
		}
		echo '<td>&nbsp;</td>';
		if($this->ValidatePermission(8) == TRUE)
		{
			echo '<td colspan="2"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$lsuspended.'\');return false;">Suspended</a></div></td>';
			$suspended = '<td colspan="2"><div align="center">'.$qsuspended.'</div></td>';
		}
		else {echo '<td colspan="2">&nbsp;</td>';$suspended = '<td colspan="2">&nbsp;</td>';}
		if($this->ValidatePermission(9) == TRUE)
		{
			echo '<td colspan="2"><div align="center"><a href="#" onClick="$(\'#right-column\').load(\''.$ladvanced.'\');return false;">Advanced</a></div></td>';
			$advanced = '<td colspan="2"><div align="center">'.$qadvanced.'</div></td>';
		}
		else 
		{
			echo '<td colspan="2">&nbsp;</td>';
			$advanced = '<td colspan="2">&nbsp;</td>';
		}
		echo '</tr><tr>';
		echo $active.$inactive;
		echo '<td>&nbsp;</td>';
		echo $suspended.$advanced;
		echo '</tr></table>';
		if(isset($error)){echo $error;}
		// build our list
		if($go >= 1 && $go <= 3)
		{
			echo '<div>';	
			$result  = mysql_query($query) or die('Error : ' . mysql_error());
			$paging = $this->pagingV1('right-column',$rowcount,30,$count,$link);
			if($go == 1)
			{
				echo $paging;
				$i = 0;
				while(list($ID,$username,$registrationDate,$staticip,$active) = mysql_fetch_array($result))
				{
					$this->buildList($ID,$username,$registrationDate,$staticip,$active,$go,$link,$i);
					$i++;
				}
			}
			else if($go == 2)
			{
				echo $paging;
				$i = 0;
				while(list($id,$name,$password,$ip,$date) = mysql_fetch_array($result))
				{
					$this->buildList($id,$name,$password,$ip,$date,$go,$link,$i);
					$i++;
				}
			}
			else 
			{
				echo $paging;
				$i = 0;
				while(list($id,$ip,$date,$uid,$agent) = mysql_fetch_array($result))
				{
					$this->buildList($id,$ip,$date,$uid,$agent,$go,$link,$i);
					$i++;
				}
			}
			echo '</div>';
		} 
		else 
		{
			if($go == 4){}
			else if($go == 5 && $this->ValidatePermission(5) == TRUE)
			{				
				echo '<div class="tbl"><br /><div align="center">
				<form method="GET" name="usersearch" id="usersearch">
				<input type="hidden" name="id" value="12356">	
				<table width="500px"><tr><td align="right"><label class="left" for="username" style="margin: 0px 0px 0px 0px;color:#555555;">Username:</label></td>
				<td align="left">
				<input name="username" id="username" type="text" class="loginForm" style="width:154px;" value="' . @$_GET['username'] . '" />
				<input name="submit" type="submit" class="button_2" value="Submit" />
				</td></tr>
				<tr><td colspan="2">
				<div class="cb"></div>
				<div style="margin: 5px 0px 0px 100px;">
				<div align="center" style="font-size: 9px;">Use the above form, to find users on the site.</div>								
				</td></tr></table></form></div></div>';
				if($_GET['part'] == 'after')
				{					
					$query = "SELECT ID, Username, Email, lastActivity, staticip, Active, Level_access, forumBan, messageBan, postBan FROM users WHERE Username LIKE '%".mysql_escape_string($_GET['username'])."%'";
					$result  = mysql_query($query) or die('Error : ' . mysql_error());
					$count = mysql_num_rows($result);
					if($count > 24)
					{
						echo '<div style="height:940px;overflow-y:scroll;overflow-x:none;">';
					}
					else
					{
						echo '<div>';
					}
					$i = 0;
					while(list($ID,$Username,$Email,$lastActivity,$staticip,$Active,$Level_access,$forumBan,$messageBan,$postBan) = mysql_fetch_array($result))
					{
						$this->buildList($ID,$Username,NULL,$staticip,$Active,$go,$link,$i,$Email,$lastActivity,$Level_access,$forumBan,$messageBan,$postBan);
						$i++;
					}
					echo '</div>';
				}
			}
			else {
				echo '<div style="padding:10px;" align="center">Please use the buttons above to navigate through the User Management System.</div>';
			}
		}
		echo '
		<script>
			$(document).ready(function() {
				$(".user-actions").on("change", function() {
					
					var user_id = $(this).attr("id").substring(5);
					var user_action = $(this).val();
					var req_url = "ajax.php?node=users&stage=modedit&modaction=" + user_action + "&id=" + user_id;
					$.get(req_url, function(data,status){
						if(data.indexOf("Success") >= 0)
						{
							$("#hov-msg").html("<div align=\'center\' class=\'msg-green\'>Account Adjustment Completed</div>").show().delay(1000).hide(2000);
						}
						else
						{
							$("#hov-msg").html("<div align=\'center\' class=\'msg-red\'>There was an Error with the request</div>").show().delay(1000).hide(2000);
						}
					});
					
					return false;
				});
				$("#usersearch").submit(function() {
					
					var request_user = $("#username").val();					
					$("#right-column").load("ajax.php?node=users&stage=findusers&part=after&username=" + request_user);
					
					return false;
				});
			});
		</script>';
	}
	
	private function Query($var)
	{
		if($var == 'fl')
		{
			$iquery = "SELECT COUNT(id) FROM failed_logins";
		}
		else if($var == 'l') 
		{
			$iquery = "SELECT COUNT(id) FROM logins";
		}
		else if($var == 'active') 
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 1";
		}
		else if($var == 'inactive') 
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 0";
		}
		else if($var == 'sus') 
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 2";
		}
		else if($var == 'advanced') 
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Level_access = 7";
		}
		else if($var == 'episodes')
		{
			$iquery = "SELECT COUNT(id) FROM episode";
		}
		else if($var == 'series')
		{
			$iquery = "SELECT COUNT(id) FROM series";
		}
		else {}
		$query = mysql_query($iquery); 
		$total = mysql_result($query, 0);
		return $total;
		//unset $query;
	}
	
	private function buildList($id,$username,$regDate = NULL,$ip,$active,$type,$link,$i,$Email = NULL,$lastActivity = NULL,$Level_access = NULL,$forumBan = NULL,$messageBan = NULL,$postBan = NULL){
		if($i % 2)
		{
			echo '<div style="padding:5px;">';
		}
		else
		{
			echo '<div style="padding:5px;background-color:white;">';
		}
		if($type == 1)
		{
			echo '<a href="http://www.animeftw.tv/user/'.$username.'" target="_blank">'.$username.'</a> <em>Registered on: '.date('d-m-y',$regDate).'</em> ip: <a href="http://ip-lookup.net?ip='.$ip.'" target="_blank">'.$ip.'</a>';
			echo '<div style="float:right;">';
			echo '<select name="change" class="user-actions" id="user-' . $id . '">';
			echo '<option value="#">- Actions -</option>';
			if($active == 1 || $active == 0)
			{
				echo'<option value="suspend">Suspend</option>';
			}
			if($active == 2)
			{
				echo'<option value="unsuspend">Un-Suspend</option>';
			}
			echo'<option value="delete">Delete</option>
				<option value="forumban">Forum Ban</option>
				<option value="commentban">Comment Ban</option>
				<option value="pmban">Site PM Ban</option>';
			echo '</select>';
			echo '</div>';
		}
		else if($type == 2)
		{
			echo '<a href="http://ip-lookup.net?ip='.$ip.'" target="_blank">'.$ip.'</a> failed on '.date('m-d-y',$active).', user: '.$username.', password: '.$regDate;
		}
		else if($type == 3)
		{
			echo '<a href="http://ip-lookup.net?ip='.$username.'" target="_blank">'.$username.'</a> logged in on '.date('m-d-y',$regDate).' for user ' . $this->formatUsername($ip);
		}
		else { //5 type..
		//brad, E-mail: roboy7736@yahoo.com, User Id# 5, Last Active: 03/2/2012 22:52
    	//Registration IP: 0.0.0.0, Account Status: Active, Access Level: 3
		if ($active == 0)
		{
			$accountstatus = 'In-Active';
		}
		elseif ($active == 1)
		{
			$accountstatus = 'Active';
		}
		else 
		{
			$accountstatus = 'Suspended';
		}
			$la = date("m/j/Y G:i",$lastActivity);
			echo '<a href="http://www.animeftw.tv/user/'.$username.'" target="_blank">'.$username.'</a>, E-mail: '.$Email.', ID# '.$id.'<br /> Last Active: '.$la.', Registration IP: '.$ip.'<br /> Account Status: '.$accountstatus.', Access Level: '.$Level_access;
			echo '<div style="float:right;margin-top:-20px;">';
			echo '<select name="change" class="user-actions" id="user-' . $id . '">';
			echo '<option>----------</option>';
			if($active == 1)
			{
				echo '<option value="suspend">Suspend</option>';
			}
			else if($active == 0 || $active == 2)
			{
				echo '<option value="activate">Activate</option>';
			}
			else 
			{
			}			
			echo '<option value="delete">Delete</option>';			
			if($forumBan == 1)
			{
				echo '<option value="unforumban">Forum Unban</option>';
			}
			else 
			{
				echo '<option value="forumban">Forum Ban</option>';
			}			
			if($messageBan == 1)
			{
				echo '<option value="uncommentban">Comment Unban</option>';
			}
			else 
			{
				echo '<option value="commentban">Comment Ban</option>';
			}			
			if($postBan == 1)
			{
				echo '<option value="unpmban">Site PM Unban</option>';
			}
			else
			{
				echo '<option value="pmban">Site PM Ban</option>';
			}				
			echo '</select>';
			echo '</div>';
		}
		echo '</div>';
	}
}

?>