<?php
/****************************************************************\
## FileName: users.class.php			 
## Author: Brad Riemann				 
## Usage: User Class implementation system.
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWUser extends Config{
	var $id;
	var $ssl;
	var $username;
	var $password;
	var $UserArray = array();
	var $SiteSettings = array();
	var $SiteUserSettings = array();
	var $ImageHost;
	
	public function __construct()
	{
		parent::__construct();
		// this is for the usage of the CDN, all images will be there and if its secure we want to use it.
		if($_SERVER['SERVER_PORT'] == 443)
		{
			$this->ImageHost = 'https://d206m0dw9i4jjv.cloudfront.net';
		}
		else
		{
			$this->ImageHost = 'http://img02.animeftw.tv';
			//$this->ImageHost = 'http://d206m0dw9i4jjv.cloudfront.net';
		}
	}
	
	//grab our username
	function get_username($username){
		$this->username = $username;
	}	
	//grab our api password
	function get_password($password){
		$this->password = $password;
	}	
	// Let's set our ID
	function get_id($user_id){
		$this->id = $user_id;
	}
	// Using a SSL?
	function get_ssl($ssl_port){
		if($ssl_port == 80){
			$this->ssl = 'http';
		}
		else {
			$this->ssl = 'https';
		}
	}
	//Construct the edit fields...
	private function Conedit($type,$var,$uid){
		if($this->id == $uid){
			return '<span id="'.$type.'" class="editText">'.$var.'</span>';
			}
		else {
			return $var;
			}
	}
	
	private function NewSendMail($subject,$to,$body){
		ini_set('sendmail_from', 'no-reply@animeftw.tv');
		$headers = 'From: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'Reply-To: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $body, $headers);
	}
	
	// Get the basic User information
	function checkUserName($type) {
		//0=full-link,1=full-no link,2=Username-no link
		if($type == 2)
		{
			$query = "SELECT `Username`, `display_name` FROM users WHERE ID='".$this->id."'";
		}
		else 
		{
			$query = "SELECT `Username`, `display_name`, `Level_access`, `advanceImage`, `Active` FROM `users` WHERE `ID`='".$this->id."'";
		}
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		if($type == 0)
		{
			$frontUser = '<a href="'.$this->ssl.'://'.$_SERVER['HTTP_HOST'].'/profile/' . $row['Username'] . '">';
			$endUser = '</a>';
		}
		else 
		{
			$frontUser = '';
			$endUser = '';
		}
		if($type == 2)
		{
			$fixedUsername = $row['Username'];
		}
		else
		{
			if($row['Active'] == 1)
			{
				if($row['Level_access'] == 1)
				{
					$fixedUsername = '<img src="'.$this->ssl.'://static.ftw-cdn.com/site-images/adminbadge.gif" alt="Admin of Animeftw" style="vertical-align:middle;" border="0" />' . $frontUser . $row['Username'] . $endUser;
				}
				else if($row['Level_access'] == 2)
				{
					$fixedUsername = '<img src="'.$this->ssl.'://static.ftw-cdn.com/site-images/manager.gif" alt="Group manager of Animeftw" style="vertical-align:middle;" border="0" />'.$frontUser.$row['Username'].$endUser;
				}
				else if($row['Level_access'] == 7)
				{
					$fixedUsername = '<img src="'.$this->ssl.'://static.ftw-cdn.com/site-images/advancedimages/'.$row['advanceImage'].'.gif" alt="Advanced User Title" style="vertical-align:middle;" border="0" />'.$frontUser.$row['Username'].$endUser;
				}
				else
				{
					$fixedUsername = $frontUser.$row['Username'].'</a>';
				}
			}
			else {$fixedUsername = $frontUser.'<s>'.$row['Username'].'</s>'.$endUser;}
		}
		return $fixedUsername;
	}
	//return variable from the Username
	public function nVar($var){
		$query = "SELECT $var FROM users WHERE Username='".$this->username."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		return $row[$var];
	}
	//return variable from the Username
	function iVar($var){
		$query = "SELECT $var FROM users WHERE Username='".$this->id."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		return $row[$var];
	}
	//check username and password for api
	function apiUserCheck(){
		$query = "SELECT id FROM users WHERE Username='".$this->username."' AND Password='".$this->password."'";
		$result = mysql_query($query);
		$ApiReturn = mysql_num_rows($result);
		return $ApiReturn;
	}	
	//account activeness so they can access everything..
	public function apiActiveCheck(){
		$query = "SELECT id FROM users WHERE Username='".$this->username."' AND Active = 1";
		$result = mysql_query($query);
		$ApiReturn = mysql_num_rows($result);
		return $ApiReturn;
	}	
	//return variable from the Username
	function vUser(){
		$query = "SELECT ID FROM users WHERE Username='".$this->username."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$CheckReturn = mysql_num_rows($result);
		return $CheckReturn;
	}
	//birthday function
	private function Birthday($bm,$bd,$by){
		$today = date("dm");
		if($today == ($bd.$bm))
		{
			$addon = '&nbsp;<img src="/images/birthdaycake.png" title="It`s My Birthday!" alt="birthday cake" style="height:14px;" />';
		}
		else
		{
			$addon = '';
		}
		if($by == '0000' || $by == ''){return 'Unknown';}
		else {
			if($bd == ''){$bd = 00;}
			$ageTime = mktime(0, 0, 0, $bm, $bd, $by); // Get the person's birthday timestamp
			$t = time(); // Store current time for consistency
			$age = ($ageTime < 0) ? ( $t + ($ageTime * -1) ) : $t - $ageTime;
			$year = 60 * 60 * 24 * 365;
			$ageYears = $age / $year;
			return floor($ageYears).$addon;
		}
	}
	//profile function
	public function Profile($uid){
		$query = "SELECT ID, Level_access AS la, personalMsg AS pm, gender, country, stateRegion, registrationDate AS rd, ageDate AS ad, ageYear AS ay, ageMonth AS am FROM users WHERE ID='".$this->UserArray['ID']."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$r = mysql_fetch_array($result);
		if(($r['country'] != '') && ($r['stateRegion'] != '')){$loc = $r['stateRegion'].','.$r['country'];}
		else{$loc = $r['country']|$r['stateRegion'];}
		if($r['rd'] == 'unknown'){$rd = 'June, 2008';}
		else {$rd = date('F, Y',$r['rd']);}
			$query = "SELECT groupName AS gn FROM site_groups WHERE groupID='".$r['la']."'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$g = mysql_fetch_array($result);
			// Personal Title Start
			if($r['pm'] == ''){
				if($r['la'] != 3 && $uid == $this->id){
					$pm = "<span id=\"pm\" class=\"editText\">Member</span>";
				}
				else{
					$pm = "Member";
				}
			}
			else{
				if($uid == $this->id){
					$pm = "<span id=\"pm\" class=\"editText\">".$r['pm']."</span>";
				}
				else {
					$pm = $r['pm'];
				}
			}
			//Gender bender!
			if($r['gender'] == ''){$gn = 'Unknown';}else{$gn = $r['gender'];}
			echo "<div><div class='fds'>".$pm."</div><br /><table><tr><td align=\"right\">Member ID:</td><td>".$r['ID']."</td></tr><tr><td align=\"right\">Account Type:</td><td>".$g['gn']."</td></tr><tr><td align=\"right\">Age:</td><td>".$this->Birthday($r['am'],$r['ad'],$r['ay'])."</td></tr><tr><td align=\"right\">Gender:</td><td>".$gn."</td></tr><tr><td align=\"right\">Location:</td><td>".$loc."</td></tr><tr><td align=\"right\">Joined:</td><td>".$rd."</td></tr></table></div><br />";
	}
	function About($uid){
		$query = "SELECT Level_access AS la, aboutMe AS am FROM users WHERE ID='".$this->UserArray['ID']."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$r = mysql_fetch_array($result);
		$am = stripslashes($r['am']);
		$am = nl2br($am);
		if($am == ''){if($uid == $this->id && $r['la'] != 3){echo "<div><div class='fds'>About Me</div><br /><span id=\"aboutme\" class=\"editText\">You have not Configured your About me! Click here to Edit it!</span></div><br />";}}
		else {
			if($r['la'] != 3){
				echo "<div><div class='fds'>About Me</div><br />";
				if($uid == $this->id){
					echo "<span id=\"aboutme\" class=\"editText\">".$am."</span>";
				}
				else{
					echo $am;
				}
				echo "</div><br />";
			}
			else {}
		}
	}
	function Interests($uid){
		$query = "SELECT Level_access AS la, interests AS ints FROM users WHERE ID='".$this->UserArray['ID']."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$r = mysql_fetch_array($result);
		$ints = stripslashes($r['ints']);
		$ints = nl2br($ints);
		if($ints == ''){if($uid == $this->id && $r['la'] != 3){echo "<div><div class='fds'>Interests</div><br /><span id=\"interests\" class=\"editText\">You have not Configured your Interests! Click here to Edit it!</span></div><br />";}}
		else {if($r['la'] != 3){echo "<div><div class='fds'>My Interests</div><br />";if($uid == $this->id){echo "<span id=\"interests\" class=\"editText\">".$ints."</span>";}else{echo $ints;}echo "</div><br />";}else {}}
	}
	function Signature($uid){
		$query = "SELECT Level_access AS la, Signature AS sig, signatureActive as sa FROM users WHERE ID='".$this->UserArray['ID']."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$r = mysql_fetch_array($result);
		$sig = stripslashes($r['sig']);
		$sig = nl2br($sig);
		if($r['sa'] == 'no'){
			if($uid == $this->id && $r['la'] != 3){
				echo "<div><div class='fds'>Forum Signature</div><br /><span id=\"signature\" class=\"editText\">You have not Configured your Signature! Click here to Edit it!</span></div><br />";
			}
		}
		else {
			if($r['la'] != 3){
				echo "<div><div class='fds'>Forum Signature</div><br />";
				if($uid == $this->id){
					echo "<span id=\"signature\" class=\"editText\">".$sig."</span>";
				}
				else{
					echo $sig;
				}
				echo "</div><br />";
			}
			else {}
		}
	}
	function Status($uid){
		$query = "SELECT date, status FROM status WHERE uid='".$this->id."' ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$tr = mysql_num_rows($result);
		if($tr == 0){
			if($uid == $this->id){$sm = "<span id=\"status\" class=\"editText\">No Status Updates!</span>";}
			else {$sm = "No Status Updates!";}
		}
		else {
			$r = mysql_fetch_array($result);
			if($uid == $this->id){$sm = "<span id=\"status\" class=\"editText\">".$r['status']." posted on ".date("M dS, Y",$r['date'])."</span>";}
			else {$sm = $r['status']." posted on ".date("M dS, Y",$r['date']);}
		}
		return $sm;
	}
	private function SQLQuery($query,$type){
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		if($type == 1){
			$result = mysql_result($result , 0);
		}
		else if($type == 0){
			$result = mysql_fetch_array($result);
		}
		else {}
		return $result;
	}
	public function ProfileStats($uid = NULL){
		echo "<div class=\"pstats\">";
		echo "<dl><dt>Profile Views:</dt><dd>".$this->SQLQuery("SELECT views FROM users WHERE ID='".$this->id."'",1)."</dd></dl>";
		echo "<dl><dt>Comments Left:</dt><dd>".$this->SQLQuery("SELECT COUNT(id) FROM page_comments WHERE uid='".$this->id."'",1)."</dd></dl>";
		echo "<dl><dt>Episodes Rated:</dt><dd>".$this->SQLQuery("SELECT COUNT(id) FROM ratings WHERE IP='".$this->id."'",1)."</dd></dl>";
		echo "<dl><dt>Episodes Tracked:</dt><dd>".$this->SQLQuery("SELECT COUNT(id) FROM episode_tracker WHERE uid='".$this->id."'",1)."</dd></dl>";
		echo "</div>";
		if($uid != $this->id){
			$this->SQLQuery("UPDATE users SET views = views+1 WHERE ID='".$this->id."'",3); //update the profile views
		}
	}
	//Function to display contact info, ho yeah!
	public function ContactInfo($uid){
		$query = "SELECT Email, Alias, msnAddress, aimName, yahooName, skypeName, icqNumber, showEmail FROM users WHERE ID='".$this->id."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		//echo 'E-Mail: MSN: AIM: YIM: Skype: ICQ: Twitter: Facebook:';
		if($row['showEmail'] == 'yes'){$se = $row['Email'];}else{$se = 'Not Given';} //to show email or not to show email..
		if(isset($row['msnAddress'])){$sm = $this->Conedit('msnAddress',$row['msnAddress'],$uid);}else{$sm = $this->Conedit('msnAddress','Not Given',$uid);} //grab the MSN address..
		if(isset($row['aimName'])){$sa = $this->Conedit('aimName',$row['aimName'],$uid);}else{$sa = $this->Conedit('aimName','Not Given',$uid);} //grab the AIM address..
		if(isset($row['yahooName'])){$sy = $this->Conedit('yahooName',$row['yahooName'],$uid);}else{$sy = $this->Conedit('yahooName','Not Given',$uid);} //grab the Yahoo address..
		if(isset($row['skypeName'])){$ss = $this->Conedit('skypeName',$row['skypeName'],$uid);}else{$ss = $this->Conedit('skypeName','Not Given',$uid);} //grab the Skype address..
		if(isset($row['icqNumber'])){$si = $this->Conedit('icqNumber',$row['icqNumber'],$uid);}else{$si = $this->Conedit('icqNumber','Not Given',$uid);} //grab the ICQ address..
		if($row['Alias'] != 'NULL')
		{
			$se = $row['Alias'];
		}
		echo '<div style="padding-bottom:5px;"><div class="conout"><img src="/images/profile-images/mail.png" alt="" title="Email" /><div>&nbsp;'.$se.'</div></div></div>';		
		echo '<div style="padding-bottom:5px;"><div class="conout"><img src="/images/profile-images/msn_butterfly.png" alt="" title="MSN Address" /><div>&nbsp;'.$sm.'</div></div></div>';
		echo '<div style="padding-bottom:5px;"><div class="conout"><img src="/images/profile-images/aim.png" alt="" title="AIM" /><div>&nbsp;'.$sa.'</div></div></div>';
		echo '<div style="padding-bottom:5px;"><div class="conout"><img src="/images/profile-images/yahoo.png" alt="" title="YIM" /><div>&nbsp;'.$sy.'</div></div></div>';
		echo '<div style="padding-bottom:5px;"><div class="conout"><img src="/images/profile-images/skype.png" alt="" title="Skype" /><div>&nbsp;'.$ss.'</div></div></div>';
		echo '<div style="padding-bottom:5px;"><div class="conout"><img src="/images/profile-images/15_icq.png" alt="" title="ICQ" /><div>&nbsp;'.$si.'</div></div></div>';
		
	}
	
	public function UserProfileSettings($profileArray,$ruid)
	{
		$la = $profileArray[2];
		$yuid = $profileArray[1];
		$timeZone = $profileArray[3];
		
		if(($la != 1 && $la != 2) && ($ruid != $yuid))
		{
			echo 'There was an error in your request.';
			//echo '<br />'.$_SERVER['REQUEST_URI'];
			// if the request id equals the submitter id, then let them pass, compare as well, if the access level is not equyal to 1 or 2, 
		}
		else
		{
			echo '<form method="POST" name="ProfileEdit" id="ProfileEdit">';	
			$query = "SELECT * FROM users WHERE ID='" . mysql_real_escape_string($ruid) . "'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$ID = $row['ID'];
			$Username = $row['Username'];
			$lastActivity = $row['lastActivity']; //time
			$lastActivity = timeZoneChange($lastActivity,$timeZone);
			$lastActivity = date("M j Y, h:i A",$lastActivity);
			$registrationDate = $row['registrationDate']; //time
			$registrationDate = timeZoneChange($registrationDate,$timeZone);
			$registrationDate = date("M j Y, h:i A",$registrationDate);
			$Email = $row['Email'];
			$Alias = $row['Alias'];
			$Active = $row['Active'];
			$Reason = $row['Reason'];
			$lastLogin = $row['lastLogin']; //time
			$lastLogin = timeZoneChange($lastLogin,$timeZone);
			$lastLogin = date("M j Y, h:i A",$lastLogin);
			$staticip = $row['staticip'];
			$Level_access = $row['Level_access'];
			$canDownload = $row['canDownload'];
			$ageDate = $row['ageDate'];
			$ageYear = $row['ageYear'];
			$ageMonth = $row['ageMonth'];
			$country = $row['country'];
			$aboutMe = stripslashes($row['aboutMe']);
			$interests = stripslashes($row['interests']);
			$Signature = stripslashes($row['Signature']);
			$notes = stripslashes($row['notes']);
			$advanceImage = $row['advanceImage'];
			$advanceLevel = $row['advanceLevel'];
			echo'
			<div class="psettings">
			<input type="hidden" name="s" value="' . $yuid . '" />
			<input type="hidden" name="id" value="' . $ID . '" />
			<input type="hidden" name="Username" value="'.$Username.'" />
			<input type="hidden" name="Email" value="'.$Email.'" />';
			if($la == 1 || $la == 2)
			{
				echo '
				<script>
					loadRedactor();
				</script>';
				echo '<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />';
			}
			else
			{
				echo '<input type="hidden" name="Authorization" value="'.$_COOKIE['authenticate'].'" id="Authorization" />';
			}
			
			if($la == 1 || $la == 2){
				echo '
			<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Admin Information</div>
				<dl>
					<dt>Username:</dt>
					<dd>'.$Username.'</dd>
				</dl>
				<dl>
					<dt>Last Login:</dt>
					<dd>'.$lastLogin.'</dd>
				</dl>
				<dl>
					<dt>Last Active:</dt>
					<dd>'.$lastActivity.'</dd>
				</dl>
				<dl>
					<dt>Registration Date:</dt>
					<dd>'.$registrationDate.'</dd>
				</dl>
				<dl>
					<dt>Registration IP:</dt>
					<dd><a href="http://ip-lookup.net?ip='.$staticip.'" target="_blank">'.$staticip.'</a></dd>
				</dl>
				<dl>
					<dt>Account Status:</dt>
					<dd>';					
					if($la == 1 && $ruid != 1){
						echo '<select name="Active" style="color: #000000;" class="loginForm">';
					}
					else {
						echo '
						<input type="hidden" name="Active" value="'.$Active.'" />
						<select name="Active2" style="color: #000000;" disabled="disabled" class="loginForm">';
					}
					echo '	<option value="0"'; if($Active == '0') {echo' selected="selected"';} echo'>In-Active</option>
							<option value="1"'; if($Active == '1') {echo' selected="selected"';} echo'>Active</option>
							<option value="2"'; if($Active == '2') {echo' selected="selected"';} echo'>Suspended</option>
						</select></dd>
				</dl>';
					if ($Active == 2){
						echo'<dl>
						<dt>Suspension Note:</dt>
						<dd><textarea name="Reason" id="Reason" cols="25" rows="5" class="loginForm">'.$Reason.'</textarea>';
						echo '</dd>';
						echo '</dl>';
					  }
				echo '
				<dl>
					<dt>Access Level:</dt>
					<dd>';
					if($la != 1 && $la != 2){
					}
					else {
						if($ruid == $yuid)
						{
							echo '
							<input type="hidden" name="Level_access" value="'.$Level_access.'" />
							<select name="Level_access2" style="color: #000000;" disabled="disabled" class="loginForm">';
							echo '<option value="1"'; if($Level_access == '1') {echo' selected="selected"';} echo'>Admin</option>';
							echo '<option value="2"'; if($Level_access == '2') {echo' selected="selected"';} echo'>Manager</option>';
							echo '<option value="3"'; if($Level_access == '3') {echo' selected="selected"';} echo'>Normal Member</option>';
							echo '<option value="4"'; if($Level_access == '4') {echo' selected="selected"';} echo'>Coding Staff</option>';
							echo '<option value="5"'; if($Level_access == '5') {echo' selected="selected"';} echo'>General Staff</option>';
							echo '<option value="6"'; if($Level_access == '6') {echo' selected="selected"';} echo'>Moderator</option>';
							echo '<option value="7"'; if($Level_access == '7') {echo' selected="selected"';} echo'>Advanced Member</option>';
						}
						else
						{
							// if the mod is a manager or admin, they got here, managers can only edit users who are basic staff and below
							if($la == 2)
							{
								if($Level_access == '1' || $Level_access == '2')
								{
									echo '
									<input type="hidden" name="Level_access" value="'.$Level_access.'" />
									<select name="Level_access2" style="color: #000000;" disabled="disabled" class="loginForm">';
									echo '<option value="1"'; if($Level_access == '1') {echo' selected="selected"';} echo'>Admin</option>';
									echo '<option value="2"'; if($Level_access == '2') {echo' selected="selected"';} echo'>Manager</option>';
									echo '<option value="3"'; if($Level_access == '3') {echo' selected="selected"';} echo'>Normal Member</option>';
									echo '<option value="4"'; if($Level_access == '4') {echo' selected="selected"';} echo'>Coding Staff</option>';
									echo '<option value="5"'; if($Level_access == '5') {echo' selected="selected"';} echo'>General Staff</option>';
									echo '<option value="6"'; if($Level_access == '6') {echo' selected="selected"';} echo'>Moderator</option>';
									echo '<option value="7"'; if($Level_access == '7') {echo' selected="selected"';} echo'>Advanced Member</option>';
								}
								else
								{
									echo '<select name="Level_access" style="color: #000000;" class="loginForm">';
									echo '<option value="3"'; if($Level_access == '3') {echo' selected="selected"';} echo'>Normal Member</option>';
									echo '<option value="4"'; if($Level_access == '4') {echo' selected="selected"';} echo'>Coding Staff</option>';
									echo '<option value="5"'; if($Level_access == '5') {echo' selected="selected"';} echo'>General Staff</option>';
									echo '<option value="6"'; if($Level_access == '6') {echo' selected="selected"';} echo'>Moderator</option>';
									echo '<option value="7"'; if($Level_access == '7') {echo' selected="selected"';} echo'>Advanced Member</option>';
								}
							}
							else
							{
								echo '<select name="Level_access" style="color: #000000;" class="loginForm">';
								echo '<option value="1"'; if($Level_access == '1') {echo' selected="selected"';} echo'>Admin</option>';
								echo '<option value="2"'; if($Level_access == '2') {echo' selected="selected"';} echo'>Manager</option>';
								echo '<option value="3"'; if($Level_access == '3') {echo' selected="selected"';} echo'>Normal Member</option>';
								echo '<option value="4"'; if($Level_access == '4') {echo' selected="selected"';} echo'>Coding Staff</option>';
								echo '<option value="5"'; if($Level_access == '5') {echo' selected="selected"';} echo'>General Staff</option>';
								echo '<option value="6"'; if($Level_access == '6') {echo' selected="selected"';} echo'>Moderator</option>';
								echo '<option value="7"'; if($Level_access == '7') {echo' selected="selected"';} echo'>Advanced Member</option>';
							}
						}
						echo '
						</select></td>
						</tr>';
					}
				echo '</dd>
				</dl>';
				if($la == 1 || $la == 2){
					echo '
					<dl>
						<dt>Non-Advanced<br /> Downloading Perms:</dt>
						<dd>';
					echo '<select name="canDownload" style="color: #000000;" class="loginForm">
								<option value="1"'; if($canDownload == '1') {echo' selected="selected"';} echo'>Yes</option>
								<option value="0"'; if($canDownload == '0') {echo' selected="selected"';} echo'>No</option>
							</select>';
					echo '</dd>
						</dl>';
				}
				else {}
				echo '
				<dl>
					<dt>Account Notes:<br /><br />Use formatting like:<br /> dd/mm/yy - note - name</dt>
					<dd><textarea class="loginForm" id="notes" name="notes" style="width:375px;height:100px;">'.$notes.'</textarea></dd>
				</dl>';
				echo '<br />';
			}
			echo '
				<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Site Settings</div>';
			if($row['Level_access'] != 3 || ($la == 1 || $la == 2))
			{
				$AMFeature = '';
			}
			else
			{
				$AMFeature = ' disabled="disabled"';
			}
			if($row['display_name'] == NULL)
			{
				$Display_Name = $row['Username'];
			}
			else
			{
				$Display_Name = $row['display_name'];
			}
			echo '
			<dl>
				<dt>Display Name:<div style="font-size:8px;">An <a href="/advanced-signup" target="_blank">Advanced Member</a> Feature</div></dt>
				<dd><input name="displayName" type="text" class="loginForm" id="displayName" size="25" value="' . $Display_Name . '"' . $AMFeature . ' /></dd>
			</dl>';
			echo '<dl>
				<dt>Site PM Notifications:<br /><i>This controls if you get<br /> notified for new personal messages.</i></dt>
				<dd><select name="sitepmnote" class="loginForm">
						<option value="0">Receive PMs?</option>
						<option value="1"'; if($row['sitepmnote'] == '1'){echo ' selected="selected"';} echo '>Yes</option>
						<option value="0"'; if($row['sitepmnote'] == '0'){echo ' selected="selected"';} echo '>No</option>
					</select></dd>
			</dl><br />';
			echo '<dl>
				<dt>Admin Notifications:<br /><i>Do you want to get emails<br /> from the Admins on important updates?.</i></dt>
				<dd><select name="notifications" class="loginForm">
						<option value="0">Receive Admin Emails?</option>
						<option value="1"'; if($row['notifications'] == '1'){echo ' selected="selected"';} echo '>Yes</option>
						<option value="0"'; if($row['notifications'] == '0'){echo ' selected="selected"';} echo '>No</option>
					</select></dd>
			</dl><br /><br />';
			echo '<dl>
				<dt>Site Theme:<br /><i>Choose the Theme <br /> you want to use on the site.</i></dt>
				<dd><select name="theme" class="loginForm">
						<option value="0">Default Theme</option>
						<option value="1"'; if($row['theme'] == '1'){echo ' selected="selected"';} echo '>Christmas Theme</option>
					</select></dd>
			</dl><br />';
			echo '<dl> 
				<dt>Site Only SSL:<br /><i>Make the site only display <br /> through Secure Socket Layers (SSL).</i><div style="font-size:8px;">An <a href="/advanced-signup" target="_blank">Advanced Member</a> Feature</div></dt>
				<dd><select name="ssl-support" id="ssl-support" class="loginForm"' . $AMFeature . '>
						<option value="0">Full Site SSL?</option>
						<option value="1"'; if($row['ssl'] == '1'){echo ' selected="selected"';} echo '>Yes</option>
						<option value="0"'; if($row['ssl'] == '0'){echo ' selected="selected"';} echo '>No</option>
					</select></dd>
			</dl><br /><br />';
			echo '<script>
				$("#ssl-support").change(function() {
					var html5 = $("#html5").val();
					if(html5 == 0)
					{
						var r = confirm("To enable full site SSL Support, you MUST use the HTML5 player, click OK to set the HTML5 player option.");
						if (r == true)
						{
							txt = "You pressed OK!";
							$("select#html5").val("1");
						}
						else 
						{
							$("select#ssl-support").val("0");
						}
					}
				});
				</script>';
			echo '<dl>
				<dt>HTML5 Player:<br /><i>View HTML5 compatible <br />videos by default.</i><div style="font-size:8px;">An <a href="/advanced-signup" target="_blank">Advanced Member</a> Feature</div></dt>
				<dd><select name="html5" id="html5" class="loginForm"' . $AMFeature . '>
						<option value="0"'; if($row['html5'] == '0' || ($row['html5'] == '1' && $AMFeature != '')){echo ' selected="selected"';} echo '>DivX Player</option>
						<option value="1"'; if($row['html5'] == '1' && $AMFeature == ''){echo ' selected="selected"';} echo '>HTML5 Player</option>
					</select></dd>
			</dl><br /><br />';
			if($row['Level_access'] == 7 || $row['Level_access'] == 1)
			{
				echo '<dl>
				<dt>Username Preffix:<br /><i>Customize the image that <br /> sits in front of <br />your username.</i><div style="font-size:8px;">An <a href="/advanced-signup" target="_blank">Advanced Member</a> Feature<br /><br /></div></dt>
				<dd>
					<div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-white-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-white.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-white" id="advanced-white-img"'; if($advanceImage == 'advanced-white' || $advanceImage == 'default'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-black-img" style="padding-left:6px;""><img src="/images/advancedimages/advanced-black.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-black" id="advanced-black-img"'; if($advanceImage == 'advanced-black'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-salmon-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-salmon.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-salmon" id="advanced-salmon-img"'; if($advanceImage == 'advanced-salmon'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-pink-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-pink.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-pink" id="advanced-pink-img"'; if($advanceImage == 'advanced-pink'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-violet-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-violet.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-violet" id="advanced-violet-img"'; if($advanceImage == 'advanced-violet'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-purple-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-purple.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-purple" id="advanced-purple-img"'; if($advanceImage == 'advanced-purple'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-blue-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-blue.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-blue" id="advanced-blue-img"'; if($advanceImage == 'advanced-blue'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-cyan-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-cyan.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-cyan" id="advanced-cyan-img"'; if($advanceImage == 'advanced-cyan'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-green-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-green.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-green" id="advanced-green-img"'; if($advanceImage == 'advanced-green'){echo ' checked="checked"';} echo ' /></div>
						</div>
					</div>
					<div style="padding-top:10px;">
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-limegreen-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-limegreen.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-limegreen" id="advanced-limegreen-img"'; if($advanceImage == 'advanced-limegreen'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-orange-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-orange.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-orange" id="advanced-orange-img"'; if($advanceImage == 'advanced-orange'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-red-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-red.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-red" id="advanced-red-img"'; if($advanceImage == 'advanced-red'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="advanced-yellow-img" style="padding-left:6px;"><img src="/images/advancedimages/advanced-yellow.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="advanced-yellow" id="advanced-yellow-img"'; if($advanceImage == 'advanced-yellow'){echo ' checked="checked"';} echo ' /></div>
						</div>						
						<div style="display:inline-block;width:30px;">
							<div><label for="green-img" style="padding-left:6px;"><img src="/images/advancedimages/green.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="green" id="green-img"'; if($advanceImage == 'green'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="robins-egg-img" style="padding-left:6px;"><img src="/images/advancedimages/robins-egg.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="robins-egg" id="robins-egg-img"'; if($advanceImage == 'robins-egg'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="blue-img" style="padding-left:6px;"><img src="/images/advancedimages/blue.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="blue" id="blue-img"'; if($advanceImage == 'blue'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="pink-img" style="padding-left:6px;"><img src="/images/advancedimages/pink.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="pink" id="pink-img"'; if($advanceImage == 'pink'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="red-img" style="padding-left:6px;"><img src="/images/advancedimages/red.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="red" id="red-img"'; if($advanceImage == 'red'){echo ' checked="checked"';} echo ' /></div>
						</div>
					</div>
					<div style="padding-top:10px;">
						<div style="display:inline-block;width:30px;">
							<div><label for="yellow-img" style="padding-left:6px;"><img src="/images/advancedimages/yellow.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="yellow" id="yellow-img"'; if($advanceImage == 'yellow'){echo ' checked="checked"';} echo ' /></div>
						</div>
						<div style="display:inline-block;width:30px;">
							<div><label for="purple-img" style="padding-left:6px;"><img src="/images/advancedimages/purple.png" alt="" /></label></div>
							<div align="center"><input type="radio" name="preffix" value="purple" id="purple-img"'; if($advanceImage == 'purple'){echo ' checked="checked"';} echo ' /></div>
						</div>';
						// Advance Members for life
						if($advanceLevel == '9999')
						{
							echo '
							<div style="display:inline-block;width:30px;">
								<div><label for="advanced-AMplus" style="padding-left:6px;"><img src="/images/advancedimages/AMplus.png" alt="" /></label></div>
								<div align="center"><input type="radio" name="preffix" value="AMplus" id="advanced-AMplus"'; if($advanceImage == 'AMplus'){echo ' checked="checked"';} echo ' /></div>
							</div>';
						}
					echo '
					</div>
				</dd>
			</dl><br /><br />';
			}
			echo '
			<br />';
			echo '			
			<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Personal Information</div>
			<dl>
				<dt>First Name:</dt>
				<dd><input name="firstName" type="text" class="loginForm" id="firstName" size="25" value="'.$row['firstName'].'" /></dd>
			</dl>
			<dl>
				<dt>Last Name:</dt>
				<dd><input name="lastName" type="text" class="loginForm" id="lastName" size="25" value="'.$row['lastName'].'" /></dd>
			</dl>
			<dl>
				<dt>Gender:</dt>
				<dd><select name="gender" style="" class="loginForm">';
				if ($row['gender'] == '' ){
					echo '<option value="">--Gender--</option>
					<option value="female">Female</option>
					<option value="male">Male</option>';
				}
				else if ($row['gender'] == 'male' || $row['gender'] == 'Male' ){
					echo '<option value="">--Gender--</option>
					<option value="female">Female</option>
					<option value="male" selected>Male</option>';
				}
				elseif ($row['gender'] == 'female' || $row['gender'] == 'Female' ){
					echo '<option value="">--Gender--</option>
					<option value="female" selected>Female</option>
					<option value="male">Male</option>';
				}
				 echo'</select></dd>
			</dl>
			<dl>
				<dt>Age:</dt>
				<dd>
				<select name="ageDate" class="loginForm">
					<option value="00" selected="selected">--Day--</option>';
					for($i=1; $i<=31; $i++)
					{
						$ri = $i<10?('0'.$i):$i;
						echo '<option value="' . $ri . '"'; if($ageDate == $ri){echo' selected';} echo '>' . $i . '</option>';
					}						
					echo '</select>
					<select name="ageMonth" class="loginForm">
						<option value="00" selected="selected">--Month--</option>';
						$monthsarr = array('January','February','March','April','May','June','July ','August','September','October','November','December');
						for($i=0; $i<=11; $i++)
						{
							$ri = ($i+1)<10?('0'.($i+1)):($i+1);
							echo '<option value="' . $ri . '"'; if($ageMonth == $ri){echo' selected ';} echo '>' . $monthsarr[$i] . '</option>';
						}					 
						echo '</select>
					<select name="ageYear" class="loginForm">
						<option value="0000" selected="selected">--Year--</option>';
						$startyear = date("Y")-90;
						$endyear = date("Y")-12;
						for($i=$endyear; $i>=$startyear; $i--)
						{
							echo '<option value="' . $i . '"'; if($ageYear == $i){echo' selected ';} echo '>' . $i . '</option>';
						}
						echo '</select>
				</dd>
			</dl>
			<dl>
				<dt>Country:</dt>
				<dd><select name="country" class="loginForm">
				<option value=""'; if($country == ''){echo' selected ';} echo '>Select Your Country</option>';
				$query = "SELECT `name`, `value` FROM `site_variables` WHERE `type` = 1 ORDER BY `name` ASC";
				$result = mysql_query($query);
				while($row = mysql_fetch_assoc($result))
				{
					if($row['name'] == $country)
					{
						$selected  = ' selected="selected"';
					}
					else
					{
						$selected  = '';
					}
					echo '<option value="' . $row['name'] . '"' . $selected . '>' . $row['name'] . '</option>';
				}
				echo '
				</select></dd>
			</dl>
			<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Contact Information</div>
			<dl>
				<dt>MSN Address:</dt>
				<dd><input name="msnAddress" type="text" class="loginForm" id="msnAddress" size="25" value="'.$row['msnAddress'].'" /></dd>
			</dl>
			<dl>
				<dt>AIM:</dt>
				<dd><input name="aimName" type="text" class="loginForm" id="aimName" size="25" value="'.$row['aimName'].'" /></dd>
			</dl>
			<dl>
				<dt>Yahoo IM:</dt>
				<dd><input name="yahooName" type="text" class="loginForm" id="yahooName" size="25" value="'.$row['yahooName'].'" /></dd>
			</dl>
			<dl>
				<dt>Skype:</dt>
				<dd><input name="skypeName" type="text" class="loginForm" id="skypeName" size="25" value="'.$row['skypeName'].'" /></dd>
			</dl>
			<dl>
				<dt>ICQ:</dt>
				<dd><input name="icqNumber" type="text" class="loginForm" id="icqNumber" size="25" value="'.$row['icqNumber'].'" /></dd>
			</dl>
			<dl>
				<dt>Email:</dt>
				<dd>'.$row['Email'].' <span style="font-size:6px;"><a href="#" onClick="window.scrollTo(0,0); return false;">to edit your email click here</a></span></dd>
			</dl>';
			echo '
			<dl>
				<dt>Email Alias:<div style="font-size:8px;">An <a href="/advanced-signup" target="_blank">Advanced Member</a> Feature</div></dt>
				<dd><input name="Alias" type="text" class="loginForm" id="Alias" size="25" value="'.$row['Alias'].'"' . $AMFeature . ' /></dd>
			</dl>
			<div style="text-align:center;font-size:10px;">Use this to Hide your real email from members. If this is set, the system will change the email shown on your profile to this value.</div>';
			echo '
			<dl>
				<dt>Show Email?</dt>
				<dd><select name="showEmail" class="loginForm">
					<option value="no">Show Email?</option>
					<option value="yes"'; if($row['showEmail'] == 'yes'){echo ' selected="selected"';} echo '>Yes</option>
					<option value="no"'; if($row['showEmail'] == 'no'){echo ' selected="selected"';} echo '>No</option>
				</select></dd>
			</dl>';
			
			if($la == 1 || $la == 2){
				echo '
				<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Misc Information</div>
				<dl>
					<dt>Avatar Active?:</dt>
					<dd><select name="avatarActivate" class="loginForm">
					<option value="no">Avatar Active?</option>
					<option value="yes"'; if($row['avatarActivate'] == 'yes'){echo ' selected="selected"';} echo '>Yes</option>
					<option value="no"'; if($row['avatarActivate'] == 'no'){echo ' selected="selected"';} echo '>No</option>
					</select></dd>
				</dl>
				<dl>
					<dt>Avatar Type:</dt>
					<dd><input name="avatarExtension" type="text" class="loginForm" id="avatarExtension" size="25" value="'.$row['avatarExtension'].'" /></dd>
				</dl>
				<dl>
					<dt>User Title:</dt>
					<dd><input name="personalMsg" type="text" class="loginForm" id="personalMsg" size="25" value="'.$row['personalMsg'].'" /></dd>
				</dl>
				<dl>
					<dt>Staff Badges:</dt>
					<dd><select name="memberTitle" class="loginForm">
					<option value="none">None</option>
					<option value="admin"'; if($row['memberTitle'] == 'admin'){echo ' selected="selected"';} echo '>Admin</option>
					<option value="adminjames"'; if($row['memberTitle'] == 'adminjames'){echo ' selected="selected"';} echo '>Admin James</option>
					<option value="forummod"'; if($row['memberTitle'] == 'forummod'){echo ' selected="selected"';} echo '>Forum Mod</option>
					<option value="groupmanager"'; if($row['memberTitle'] == 'groupmanager'){echo ' selected="selected"';} echo '>Manager</option>
					<option value="staff"'; if($row['memberTitle'] == 'staff'){echo ' selected="selected"';} echo '>Staff</option>
					</select></dd>
				</dl>';
			}
			
			if($row['Level_access'] != 3 || ($la == 1 || $la == 2)){
				echo '
				<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Advanced Member Features</div>';
				if($row['Level_access'] == 7){
					echo '
					<dl>
						<dt>AM Active:</dt>';
						if($row['advanceActive'] == 'no'){
							echo '<dd><span style="background-color:#FF0000;color:#fff;padding:1px 5px 1px 5px;">Membership is In-Active!!</span></dd>';
						}
						else {
							echo '<dd><span style="background-color:#00FF00;padding:1px 5px 1px 5px;">Membership is Active</span></dd>';
						}
						echo '
					</dl>
					<dl>
						<dt>AM Months:</dt>
						<dd>'.$row['advanceLevel'].' Month(s)</dd>
					</dl>
					<dl>
						<dt>Current AM Period:</dt>
						<dd>';
						if($row['advanceLevel'] == 1){
							$advanceDate = date("l, F jS, Y, h:i a", $row['advanceDate']);
							$blahdate = strtotime($advanceDate." +1 month");
							$testfuture = date("l, F jS, Y, h:i a", $blahdate);
						}
						else {
							$advanceDate = date("l, F jS, Y, h:i a", $row['advanceDate']);
							$blahdate = strtotime($advanceDate." +".$row['advanceLevel']." months");
							$testfuture = date("l, F jS, Y, h:i a", $blahdate);
						}
						echo $advanceDate.' till '.$testfuture;
						echo '</dd>
					</dl>';
				}
				echo '<dl>
					<dt>About Me:</dt>
					<dd>&nbsp;</dd>
				</dl>
				<textarea id="aboutMe" name="aboutMe" style="width:550px;height:150px;">'.$aboutMe.'</textarea>
				<dl>
					<dt>My Interests:</dt>
					<dd>&nbsp;</dd>
				</dl>';
				echo '<textarea id="Interests" name="Interests" style="width:550px;height:150px;" class="loginForm">'.$interests.'</textarea>';	
				if($la == 1 || $la == 2){
					echo '<dl>
						<dt>Signature Active:</dt>
						<dd><select name="signatureActive" class="loginForm">
								<option value="no">Sig Actve?</option>
								<option value="yes"'; if($row['signatureActive'] == 'yes'){echo ' selected="selected"';} echo '>Yes</option>
								<option value="no"'; if($row['signatureActive'] == 'no'){echo ' selected="selected"';} echo '>No</option>
							</select></dd>
					</dl>';
				}
				echo '<dl>
					<dt>Signature:</dt>
					<dd>&nbsp;</dd>
				</dl>
				<textarea id="Signature" name="Signature" style="width:550px;height:150px;" class="loginForm">'.$Signature.'</textarea>';
				
			}
			echo '
			<br /><br />
				</div>';
			echo '<div id="form_results" class="form_results" style="height:20px;">&nbsp;</div>';
				
				echo '
			<input name="method" type="hidden" class="method" value="UserEdit" />
			<input name="submit" type="button" class="SubmitFormUser" value="Submit Changes" />
				</form>';
		}
	}
	
	public function UserLogs($profileArray,$ruid)
	{
		$this->UserArray = $profileArray;
		
		// This will show the 
		if($this->UserArray[2] != 1 && $this->UserArray[2] != 2)
		{
			echo 'There was an error in your request.';
			//echo '<br />'.$_SERVER['REQUEST_URI'];
			// if the request id equals the submitter id, then let them pass, compare as well, if the access level is not equyal to 1 or 2, 
		}
		else
		{
			
			echo '
			<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Site Logins</div>';
			$query = "SELECT * FROM `logins` WHERE uid = " . mysql_real_escape_string($ruid) . " ORDER BY `logins`.`date` DESC LIMIT 0, 40";
			$result = mysql_query($query);
			
			if(mysql_num_rows($result) < 1)
			{
				echo '<div align="center">No logins were detected for this user.</div>';
			}
			else
			{
				while($row = mysql_fetch_assoc($result))
				{
					echo '
					<div>
						<div style="width:20%;display:inline-block;">' . $row['date'] . '</div>
						<div style="width:20%;display:inline-block;">' . $row['ip'] . '</div>
						<div style="width:50%;display:inline-block;">' . $row['agent'] . '</div>
					</div>';
				}
			}
		}
	}
	
	public function UserSiteSettings($profileArray,$ruid)
	{
		$this->UserArray = $profileArray;
		
		// This will show the 
		if(($this->UserArray[2] != 1 && $this->UserArray[2] != 2) && ($ruid != $this->UserArray[1]))
		{
			echo 'There was an error in your request.';
			//echo '<br />'.$_SERVER['REQUEST_URI'];
			// if the request id equals the submitter id, then let them pass, compare as well, if the access level is not equyal to 1 or 2, 
		}
		else
		{
			//build constants first..
			$this->array_userSiteSettings($ruid); //builds the list of user specific settings.
			$this->array_availableSiteSettings(); //builds the list of options for each option.
			
			// then we build the rest of the data..
			echo '
			<form id="SiteSettings">
			<input type="hidden" name="method" value="EditSiteSettings" />
			<input type="hidden" name="uid" value="' . $ruid . '" />
			<div align="center">
				Manage your site, email and security settings for AnimeFTW.tv with this form.
			</div>
			<br />';
			$query = "SELECT * FROM `user_setting_type` ORDER BY `name`";
			$result = mysql_query($query);
			
			while($row = mysql_fetch_assoc($result))
			{
				echo '
				<div>
					<div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">' . stripslashes($row['name']) . '</div>
				</div>
				<div>' . $this->returnSiteSettings($row['id']) . '
				</div>
				<br />';
			}
			echo '
			<div align="right">
				<div style="min-height:16px;">
					<div class="form_results" style="display:none;"></div>
				</div>
			<input type="submit" value=" Update " name="settings-submit" id="settings-submit" /></div>
			</form>
			<script>
				$("#settings-submit").click(function(){
					$.ajax({
						type: "POST",
						url: "/scripts.php?view=settings&go=post",
						data: $("#SiteSettings").serialize(),
						success: function(html)
						{
							if(html.indexOf("Success") >= 0)
							{
								$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Profile Update completed successfully.</div>");											
								$(".form_results").delay(8000).slideUp();
							}
							else{
								$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Error Updating Profile: " + html + "</div>");
							}
						}
					});
					return false;
				});
			</script>';
		}
	}
	
	private function returnSiteSettings($SettingCatId = NULL)
	{
		if($SettingCatId == NULL)
		{
			// null so we want to just dump all of the settings
			$SQLAddon = "";
			$returndata = array();
		}
		else
		{
			// not null, we need to proceed as though this is individual..
			$SQLAddon = " WHERE `parent` = $SettingCatId";
			$returndata = '';
		}
		$query = "SELECT * FROM `user_setting_option`$SQLAddon";
		$result = mysql_query($query);
		// id, name, description, parent, type, group, added, default_option 
		
		$count = mysql_num_rows($result);
		
		if($count == 0)
		{
			$returndata = 'There are no entries for this option yet.';
		}
		else
		{
			$i = 0;
			while($row = mysql_fetch_assoc($result))
			{				
				if($SettingCatId == NULL)
				{
					$returndata[$row['id']] = $row;
				}
				else
				{
					$groups = explode(":", $row['group']);
					if($i%2)
					{
						$Style = 'background:#4bccf6;';
					}
					else
					{
						$Style = 'background:#dadada;';
					}
					
					if(in_array($this->UserArray[2],$groups))
					{
						// are they allowed to select the option.
						$AMOnly = '';
						$Disabled = FALSE;
					}
					else
					{
						// the user's array was not in the group, continue..
						$AMOnly = '<span style="font-size:10px;"><a href="/advanced-signup">Advanced Feature</a></span>';
						$Disabled = TRUE;
					}
					$returndata .= '
					<div style="padding: 5px 0 5px 4px;' . $Style . '">
						<div style="display:inline-block;width:70%;vertical-align:top;">
							<div style="font-size:14px;border-bottom:1px solid gray" align="center">' . stripslashes($row['name']) . '</div>
							<div>' . stripslashes($row['description']) . '</div>
						</div>
						<div style="display:inline-block;width:29%;vertical-align:top;">
							<div align="center">' . $AMOnly . '<br />' . $this->settingFormType($row['id'],$row['type'],$row['default_option'],$Disabled) . '</div>
						</div>
					</div>';
				}
				$i++;
			}
		}
		return $returndata;
	}
	
	private function settingFormType($id,$type,$default_option,$disabled)
	{
		$returndata = '';
		if($disabled == TRUE)
		{
			$disabled = ' disabled="disabled"';
		}
		else
		{
			$disabled = '';
		}
		if($type == 0)
		{
			// select form.. let's render the data.
			$returndata .= '<select id="setting-' . $id . '" name="setting-' . $id . '" class="loginForm"' . $disabled . '>';
			foreach($this->SiteSettings[$id] AS $AvailableOptions)
			{
				$Disabled = FALSE; // we set this by default, useful later..
				// first check to see if this exists in the array
				if(array_key_exists($id,$this->SiteUserSettings))
				{
					// option was selected by the user
					if($this->SiteUserSettings[$id]['value'] == $AvailableOptions['id'])
					{
						$Selected = ' selected="selected"'; // this is obviously selected..
						// this option was selected by the user.. we need to make sure it's not disabled..
						if($this->SiteUserSettings[$id]['disabled'] == 1)
						{
							// we use this option and disable the ability to select anything else..
							$Disabled = TRUE;					
						}
						else
						{
						}
					}
					else
					{
						$Selected = '';
					}
				}
				else
				{
					if($default_option == $AvailableOptions['id'])
					{
						$Selected = ' selected="selected"';
					}
					else
					{
						$Selected = '';
					}
				}
				$returndata .= '<option value="' . $AvailableOptions['id'] . '"' . $Selected . '>' . $AvailableOptions['name'] . '</option>';
			}
			$returndata .= '</select>';
		}
		else
		{
		}
		return $returndata;
	}
	
	private function array_userSiteSettings($ruid)
	{
		//builds the list of user specific settings.
		$query = "SELECT * FROM `user_setting` WHERE `uid` = " . mysql_real_escape_string($ruid);
		$result = mysql_query($query);
		$this->SiteUserSettings = array();
		
		$count = mysql_num_rows($result);
		if($count > 0)
		{
			while($row = mysql_fetch_assoc($result))
			{
				$this->SiteUserSettings[$row['option_id']] = $row; 
			}
		}
	}
	private function array_availableSiteSettings()
	{
		//builds the list of options for each option.
		$query = "SELECT * FROM `user_setting_option_values`";
		// id 	name 	option_id 
		$result = mysql_query($query);
		$this->SiteSettings = array();
		
		while($row = mysql_fetch_assoc($result))
		{
			$this->SiteSettings[$row['option_id']][$row['id']]['id'] = $row['id'];
			$this->SiteSettings[$row['option_id']][$row['id']]['name'] = $row['name'];
			$this->SiteSettings[$row['option_id']][$row['id']]['option_id'] = $row['option_id'];
		} 
	}	
	
	public function processSiteSettingsUpdate($profileArray)
	{
		//$this->UserArray = $profileArray;
		if(!isset($_POST['uid']))
		{
			echo 'There were critical pieces missing for this submission.';
		}
		else
		{
			if(($this->UserArray[2] != 1 && $this->UserArray[2] != 2) && ($_POST['uid'] != $this->UserArray[1]))
			{
				echo 'You are not authorized for this function. ';
			}
			else
			{
				$SiteSettings = $this->returnSiteSettings();
				foreach($_POST AS $key => &$value)
				{
					$option_id = substr($key,8);
					if(substr($key,0,7) == 'setting')
					{
						$query = "SELECT `id` FROM `user_setting` WHERE `uid` = " . mysql_real_escape_String($_POST['uid']) . " AND `option_id` = " . mysql_real_escape_string($option_id);
						$result = mysql_query($query);
						$count = mysql_num_rows($result);
						
						// this is a setting.. check to see if its a default or not.
						if($SiteSettings[$option_id]['default_option'] == $value)
						{
							if($count < 1)
							{
								echo 'Nothing to remove for id ' . substr($key,0,7);
							}
							else
							{
								// this setting is the same as what the default should be, we will delete any entries that may exist so the system will know to take defaults.
								$query = "DELETE FROM `user_setting` WHERE `uid` = " . mysql_real_escape_String($_POST['uid']) . " AND `option_id` = " . mysql_real_escape_string($option_id);
								$result = mysql_query($query);
							}
						}
						else
						{
							if($count >= 1)
							{
								echo 'Nothing to add for id ' . substr($key,0,7);
							}
							else
							{
								// this setting is not the same as the default, so we need to add it to the database.
								$result = mysql_query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . mysql_real_escape_String($_POST['uid']) . "', " . time() . ", " . time() . ", " . $option_id . ", " . mysql_real_escape_string($value) . ", 0)");
							}
						}
					}
					else
					{
					}
				}
				echo 'Success';
			}
		}
	}
	
	// public function to show profile comments on the right side. bool:uid, for the current user viewing the site.
	public function ShowProfileComments($uid,$page = NULL)
	{
		echo '<div class="ProfileComments" id="ProfileComments">';
		if($page != NULL)
		{
			$CurrentCount = $page*15;
		}
		else
		{
			$CurrentCount = 0;
		}
		$numrows = "SELECT u.ID, u.Username, u.avatarActivate, u.avatarExtension, c.id, c.comments, c.ip, c.dated FROM page_comments AS c, users AS u WHERE page_id = 'u".$this->id."' AND c.uid=u.ID AND c.is_approved = 1";
		$numrows = mysql_query($numrows);
		$numrows = mysql_num_rows($numrows);
		
		/* Comment paging method */
		if($numrows > 15){
			$totalpages = ceil($numrows/15);
			if($totalpages > 2 && $page < ($totalpages-3))
			{
				$LastPage = '<div title="Go to Last Page" style="padding:0 5px 0 5px;width:10px;font-size:14px;color:#777;background-color:#fff;border:1px solid #e1dedd;display:inline;"><a href="#" onClick="$(\'#ProfileComments\').load(\'/scripts.php?view=profile-comments&uid=' . $this->id . '&page=' . ($totalpages-1) . '\'); return false;">&gt;&gt;</a></div>';
			}
			else
			{
				$LastPage = '';
			}
			if($page > 2)
			{
				$FirstPage = '<div title="Go to Last Page" style="padding:0 5px 0 5px;width:10px;font-size:14px;color:#777;background-color:#fff;border:1px solid #e1dedd;display:inline;"><a href="#" onClick="$(\'#ProfileComments\').load(\'/scripts.php?view=profile-comments&uid=' . $this->id . '&page=0\'); return false;">&lt;&lt;</a></div>';
			}
			else
			{
				$FirstPage = '';
			}
			//if($totalpages > 4
			$ProfileCommentsFull = '<div class="comment-paging" align="right" style="padding:5px 0 5px 0;">Nav: ' . $FirstPage . ' ';
			
			// Pages BEFORE the curent page
			for($i=($page-2); $i<$page; $i++)
			{
				if($i < 0)
				{
				}
				else
				{
					if($page == $i)
					{
						$commentstyle = 'padding:0 5px 0 5px;width:10px;font-size:14px;color:#777;background-color:#e1dedd;border:1px solid #aaaaaa;display:inline;';	
					}
					else 
					{
						$commentstyle = 'padding:0 5px 0 5px;width:10px;font-size:14px;color:#777;background-color:#fff;border:1px solid #e1dedd;display:inline;';
					}
					$ProfileCommentsFull .= '<div style="'.$commentstyle.'"><a href="#" onClick="$(\'#ProfileComments\').load(\'/scripts.php?view=profile-comments&uid=' . $this->id . '&page=' . $i . '\'); return false;">' . ($i+1) . '</a></div>&nbsp;';
				}
			}
			
			// Pages AFTER the curent page
			for($i=$page; $i<($page+3); $i++)
			{
				if($i > ($totalpages-1))
				{
				}
				else
				{
					if($page == $i)
					{
						$commentstyle = 'padding:0 5px 0 5px;width:10px;font-size:14px;color:#777;background-color:#e1dedd;border:1px solid #aaaaaa;display:inline;';	
					}
					else 
					{
						$commentstyle = 'padding:0 5px 0 5px;width:10px;font-size:14px;color:#777;background-color:#fff;border:1px solid #e1dedd;display:inline;';
					}
					$ProfileCommentsFull .= '<div style="'.$commentstyle.'"><a href="#" onClick="$(\'#ProfileComments\').load(\'/scripts.php?view=profile-comments&uid=' . $this->id . '&page=' . $i . '\'); return false;">' . ($i+1) . '</a></div>&nbsp;';
				}
			}
			$ProfileCommentsFull .= $LastPage . '</div>';
		}
		else {
		}
		echo $ProfileCommentsFull;	
		echo '<div id="dynm">
		<div id="flash"></div>
		</div>';	
		/* Comment Loop */
		if($numrows == 0){
			echo '<div id="errmsg" align="center">No comments on this profile..</div>';
		}
		else {
			$query = "SELECT u.ID, u.Username, u.avatarActivate, u.avatarExtension, c.id, c.comments, c.ip, c.dated FROM page_comments AS c, users AS u WHERE page_id = 'u".$this->id."' AND c.uid=u.ID AND c.is_approved = 1 ORDER BY c.dated DESC LIMIT $CurrentCount, 15";
			$result = mysql_query($query);	
			while(list($ID,$Username,$avatarActivate,$avatarExtension,$cid,$comments,$ip,$dated) = mysql_fetch_array($result)){
				if($uid == $this->id){
					$topd = '<div id="pcommod"><div class="pcommodtxt"><a href="#" id="dico'.$cid.'" onClick="javascript:moddel(\''.$cid.'\',\''.$this->id.'\',\''.md5($this->id).'\'); return false;" title="Delete Comment"><img src="/images/tinyicons/cancel.png" alt="" border="0"></a>&nbsp;<a id="uico'.$cid.'" href="/user/'.$Username.'" title="Reply to Comment"><img src="/images/tinyicons/reply_go.png" alt="" border="0"></a></div><div id="c-'.$cid.'" style="display:none;" align="center"><a href="#" onClick="javascript:modundel(\''.$cid.'\',\''.$this->id.'\',\''.md5($this->id).'\'); return false;">Click Here to un-delete this comment.</a></div>';
					$bottomd = '</div>';
				}
				else {$topd = '';$bottomd = '';}
				if($avatarActivate == 'no'){$avatar = '<img src="' . $this->ImageHost . '/avatars/default.gif" alt="avatar" width="40px" style="padding:2px;" border="0" />';}
				else {$avatar = '<img src="' . $this->ImageHost . '/avatars/user'.$ID.'.'.$avatarExtension.'" alt="User avatar" width="40px" style="padding:2px;" border="0" />';}
				//THE variables.
				$comments = stripslashes($comments);
				$comments = nl2br($comments);
				$dated = strtotime($dated);
				echo $topd;
				echo '<a name="'.$cid.'"></a><div id="c'.$cid.'" class="side-body floatfix">';
				echo '<div id="dropmsg0" class="dropcontent">';
				echo '<div style="float:right;">'.$avatar.'</div>'; // avatar ftw
				echo '<div style="padding-bottom:2px;">'.checkUserName($Username).' - <span title="Posted '.date('l, F jS, o \a\t g:i a',$dated).'">'.date('M jS',$dated).'</span></div>'; //title of the comment
				echo '<div style="max-width:195px;word-wrap:break-word;">'.$comments.'</div>';	// Comment goes here
				echo '</div></div>';
				echo $bottomd;
				echo '<div style="height:2px;">&nbsp;</div>';
			}
		}
		if($uid == 0){
			echo '<div align="center">Please <a href="/login">login</a> to post comments.</div>';
		}
		else {
			if($uid == $this->id && $this->id != 1){
				$msg = ' This is your profile, to comment, post on their profile!';
				$disable = ' disabled="disabled"';
			}
			else {
				$msg = '';
				$disable = '';
			}
				echo '
				<div align="center" style="padding:5px 0 5px 0;">
				<form action="#" method="post" id="ProfileCommentForm">
				<input type="hidden" name="form-type" value="ProfileComment" />
				<input type="hidden" name="pid" id="pid" value="'.$this->id.'"/>
				<textarea id="comment" name="comment" style="width:230px;height:60px;"'.$disable.'>'.$msg.'</textarea><br />&nbsp;<input type="submit" class="submitpc" value=" Submit Comment "'.$disable.' />
				</form></div>';
		}
		echo $ProfileCommentsFull;
		echo '</div>';
	}
	public function EmailSettings($la,$ruid,$yuid,$timeZone){
		if(($la != 1 && $la != 2) && ($ruid != $yuid))
		{
			echo 'There was an error in your request.';
			echo '<br />'.$_SERVER['REQUEST_URI'];
			// if the request id equals the submitter id, then let them pass, compare as well, if the access level is not equyal to 1 or 2, 
		}
		else 
		{
			$query = "SELECT ID, Username, Email FROM users WHERE ID='".$ruid."'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$ID = $row['ID'];$Username = $row['Username'];$Email = $row['Email'];
			
			echo'	
			<div align="left" style="width:310px;">
				<div style="font-size:16px;padding-left:10px;border-bottom:1px solid #E3E3E3;">Change Email</div>
				<form method="GET" action="#" name="emailupdate" id="emailupdate">
				<input type="hidden" name="id" value="' . $ID . '" />
				<input type="hidden" name="username" value="' . $Username . '" />
				<input type="hidden" name="method" value="EditEmail" />
				<input type="hidden" name="oldemail" value="'.$Email.'" />
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:30%;" style="text-align:right;">Current Email:</div>
					<div style="display:inline-block;width:65%;" style="text-align:left;" id="current-email-div">'.$Email.'</div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:30%;" style="text-align:right;">New Email:</div>
					<div style="display:inline-block;width:65%;" style="text-align:left;"><input name="email" type="text" class="loginForm" id="email" style="width:170px;" value="" /></div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:30%;" style="text-align:right;">Confirm Email:</div>
					<div style="display:inline-block;width:65%;" style="text-align:left;"><input name="email_confirm" type="text" class="loginForm" id="email_confirm" style="width:170px;" value="" /></div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:30%;" style="text-align:right;">Password:</div>
					<div style="display:inline-block;width:65%;" style="text-align:left;"><input name="password" type="password" class="loginForm" id="password" style="width:170px;" value="" /></div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:40%;">
						<input name="submit" type="button" class="button_2" value="Update Email" id="emailsubmit" />
					</div>
					<div style="display:inline-block;width:55%;" align="center">
						<div class="form_results" style="display:none;">
							<img src="/images/loading-mini.gif" alt="loading" /> Loading
						</div>
					</div>
				</div>
				</form>
			</div>';
		}
	}
	
	public function PasswordSettings($la,$ruid,$yuid,$timeZone){
		if(($la != 1 && $la != 2) && ($ruid != $yuid)){
			echo 'There was an error in your request.';
			echo '<br />'.$_SERVER['REQUEST_URI'];
			// if the request id equals the submitter id, then let them pass, compare as well, if the access level is not equyal to 1 or 2, 
		}
		else 
		{
			$query = "SELECT ID, Username, Email FROM users WHERE ID='".$ruid."'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$ID = $row['ID'];
			$Username = $row['Username'];
			$Email = $row['Email'];
			echo'	
			<div align="left" style="width:310px;">
				<div style="font-size:16px;padding-left:10px;border-bottom:1px solid #E3E3E3;">Change Password</div>
				<form method="GET" action="#" name="passwordupdate" id="passwordupdate">
				<input type="hidden" name="id" value="' . $ID . '" />
				<input type="hidden" name="Username" value="'.$Username.'" />
				<input type="hidden" name="method" value="EditPassword" />
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:35%;" style="text-align:right;">New Password:</div>
					<div style="display:inline-block;width:60%;" style="text-align:left;"><input name="pw" type="password" class="loginForm" id="pw" style="width:150px;" value="" /></div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:35%;" style="text-align:right;">Confirm Password:</div>
					<div style="display:inline-block;width:60%;" style="text-align:left;"><input name="pw_c" type="password" class="loginForm" id="pw_c" style="width:150px;" value="" /></div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:35%;" style="text-align:right;">Current Password:</div>
					<div style="display:inline-block;width:60%;" style="text-align:left;"><input name="current-password" type="password" class="loginForm" id="current-password" style="width:150px;" value="" /></div>
				</div>
				<div style="width:100%;padding:5px;">
					<div style="display:inline-block;width:40%;">
						<input name="submit" type="button" class="button_2" value="Update Password" id="passwordsubmit" />
					</div>
					<div style="display:inline-block;width:55%;" align="center">
						<div class="form_results_password" style="display:none;">
							<img src="/images/loading-mini.gif" alt="loading" /> Loading
						</div>
					</div>
				</div>
				</form>
			</div>';
		}
	}
	
	// Added 08/10/2014 - robotman321
	// supplemental function to get all of the user details into a single variable for the user page.
	public function array_getUserDetails($Username)
	{
		$query = "SELECT `ID`, `Username`, `display_name`, `avatarExtension`, `avatarActivate` FROM `users` WHERE `Username` = '" . mysql_real_escape_string($Username) . "'";
		
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		
		if($count > 0)
		{
			// count is greater than 0, whch means the user exists!
			$this->UserArray['count'] = 1;
			$row = mysql_fetch_assoc($result);
			foreach($row as $key => $value)
			{
				if($key == 'display_name' && ($value == 'NULL' || $value == ''))
				{
					$this->UserArray[$key] = $this->UserArray['Username'];
				}
				else
				{
					$this->UserArray[$key] = $value;
				}
			}
			return 1;
		}
		else
		{
			$this->UserArray['count'] = 0;
			return 0;
		}
	}
}

?>