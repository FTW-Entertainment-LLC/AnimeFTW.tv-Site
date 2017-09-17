<?php
include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
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
if($port == '80')
{
	header("location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}
//Header area start. (This code loads before anything in the body tags..)
if($_GET['node'] == 'login'){
$PageTitle = 'Login - AnimeFTW.TV';
	if (isset($_POST['username']) && isset($_POST['password'])){
		function makeUrlFriendly($postUsername) {
				// Replace spaces with underscores
				$output = preg_replace("/\s/e" , "_" , $postUsername);

				// Remove non-word characters
				$output = preg_replace("/\W/e" , "" , $output);

				return strtolower($output);
				}
		function isEmail($email)
		{
			return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
		}
		$userName  = $_POST['username'];
		$password = $_POST['password'];
		$last_page = $_POST['last_page'];
		include_once('includes/classes/sessions.class.php');
		$Session = new Sessions();
		//require_once ( 'includes/settings.php' );
		if(!isset($_POST['cookies-set']) || (isset($_POST['cookies-set']) && $_POST['cookies-set'] == 1))
		{
			$error = 'Your Cookies are not enabled, you will not be able to login to AnimeFTW.tv without cookies enabled.';
		}
		else
		{
			if ( array_key_exists ( '_submit_check', $_POST ) )
			{
				if ( $userName != '' && $password != '' )
				{
					if(isEmail($userName))
					{
						// if the username is an email, we change the query string a bit.
						$query = 'SELECT `ID`, `Username`, `Email`, `Active`, `Reason`, `Password` FROM `' . $Config->MainDB . '`.`users` WHERE Email = \'' . mysql_real_escape_string( $userName ) . '\' AND Password = \'' . mysql_real_escape_string( md5 ( $password ) ) . '\'';
					}
					else
					{
						$userName = makeUrlFriendly($userName);
						// if the username is a real username.. then we use a different string.
						$query = 'SELECT `ID`, `Username`, `Email`, `Active`, `Reason`, `Password` FROM `' . $Config->MainDB . '`.`users` WHERE Username = \'' . mysql_real_escape_string( $userName ) . '\' AND Password = \'' . mysql_real_escape_string( md5 ( $password ) ) . '\'';
					}
					$result = mysql_query($query);
					if(mysql_num_rows($result) == 1)
					{
						$row = mysql_fetch_assoc($result);
						if ( $row['Active'] == 1 )
						{
							$Session->setUserSessionData($row['ID'],$row['Username'],(@$_POST['remember'])?TRUE:FALSE);
							$query = 'UPDATE users SET `lastLogin` = \''.time().'\' WHERE `Username`=\'' . mysql_real_escape_string($userName) . '\'';
							mysql_query($query) or die('Error : ' . mysql_error());
							$query = "INSERT INTO `" . $Config->MainDB . "`.`logins` (`ip`, `date`, `uid`, `agent`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$row['ID']."', '".$_SERVER['HTTP_USER_AGENT']."')";
							mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());

							// send an email to the user.
							$Session->sendEmailToUser($row['Email'],$row['ID']);
							if ($last_page == '')
							{
								header ( "Location: http://".$_SERVER['HTTP_HOST']."/user/".$userName );
								exit;
							}
							else if ($last_page == 'http://'.$_SERVER['HTTP_HOST'].'/login.php' || $last_page == 'https://'.$_SERVER['HTTP_HOST'].'/login')
							{
								header ( "Location: http://".$_SERVER['HTTP_HOST']."/user/".$userName );
								exit;
							}
							else {
								header ( "Location: ".$last_page );
								exit;
							}
						}
						elseif ( $row->Active == 0 ) {
							$error = 'Your membership was not activated. Please open the email that we sent and click on the activation link.';
					$failed = FALSE;
						}
						elseif ( $row->Active == 2 ) {
							$error = 'You are suspended!<br /><br /> Reason: '.$row->Reason.'<br /><br />If you feel this suspension is in error, please email: support@animeftw.tv with your username and the reason given above.';
					$failed = FALSE;
						}
					}
					else {
						$query = "INSERT INTO `failed_logins` (`name`, `password`, `ip`, `date`) VALUES
		('" . mysql_real_escape_string($userName) . "', '" . mysql_real_escape_string($password) . "', '" . $_SERVER['REMOTE_ADDR'] . "', '".time()."')";
						mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
						$error = 'Login failed! Password or Username is Incorrect.<br />'.$Config->checkFailedLogins($_SERVER['REMOTE_ADDR']);
					}
				}
				else {
					$error = 'Please use both your username and password to access your account';
				}
			}
		}
	}
}
else if($_GET['node'] == 'register'){
	include_once('includes/classes/register.class.php');
	$Register = new Register();
	$OutputArray = $Register->registerAccount();
	$error = $OutputArray['error'];
	$FailCheck = $OutputArray['FailCheck'];
	$noReg = $OutputArray['noReg'];

	$PageTitle = 'Account Registration  - AnimeFTW.TV';
}
else if($_GET['node'] == 'forgot-password'){
	$PageTitle = 'Password Recovery - AnimeFTW.TV';
	if(array_key_exists ( '_submit_check', $_POST)){
		require_once('includes/settings.php');
		if ( $_POST['somerandomvar'] != '' && valid_email ( $_POST['somerandomvar'] ) == TRUE ){
			$getUser = 'SELECT ID, Username, Temp_pass, Email FROM ' . DBPREFIX . 'users WHERE Email = ' . $db->qstr ( $_POST['somerandomvar'] );
			if ( $db->RecordCount ( $getUser ) == 1 ){
				$temp_pass = random_string ( 'alnum', 12 );
				$row = $db->getRow ( $getUser );
				$query = $db->query ( "UPDATE " . DBPREFIX . "users SET Temp_pass='" . $temp_pass . "', Temp_pass_active=1 WHERE `Email`= " . $db->qstr ( $row->Email ) );
				$subject = "Password Reset Request";
				$message = "Dear " . $row->Username . ", Someone (presumably you), has requested a password reset. We have generated a new password for you to access our website:  <b>$temp_pass</b> . To confirm this change and activate your new password please follow this link to our website: <a href=\"https://www.animeftw.tv/confirm-password/$row->ID/$temp_pass\">https://www.animeftw.tv/confirm-password/$row->ID/$temp_pass</a> . Don't forget to update your profile as well after confirming this change and create a new password. If you did not initiate this request, simply disregard this email, and we're sorry for bothering you.";

				if ( send_email ( $subject, $row->Email, $message ) ) {
					$error = 'New password sent. Please check your email for more details.';
				}
				else {
					$error = 'I failed to send the validation email. Please contact the admin at support@animeftw.tv';
				}
			}
			else {
				$error = 'There is no member to match your email.';
			}
		}
		else {
			$error = 'Invalid email !';
		}
	}
	else {
	}
}
else if($_GET['node'] == 'email-resend')
{
	$PageTitle = 'Welcome Email Resend - AnimeFTW.TV';
	require_once ( 'includes/settings.php' );
	if ( array_key_exists ( '_submit_check', $_POST ) ){
		if ( $_POST['username'] != '' ){
			$getUser = "SELECT ID, Username, Email, Random_key FROM " . DBPREFIX . "users WHERE Username = " . $db->qstr ( $_POST['username'] ) . "";
				if ( $db->RecordCount ( $getUser ) == 1 ){
					$row = $db->getRow ( $getUser );
					$subject = "Activation email from AnimeFTW.tv";
					$message = "Dear ".$row->Username.", this is your activation link to join our website at animeftw.tv. <br /><br /> In order to confirm your membership please click on the following link: <a href=\"http://www.animeftw.tv/confirm?ID=" . $row->ID . "&key=" . $row->Random_key . "\">http://www.animeftw.tv/confirm?ID=" . $row->ID . "&key=" . $row->Random_key . "</a> <br /><br />After you confirm your status with us, please go visit this link: <a href=\"https://www.animeftw.tv/faq\">animeftw FAQ</a> and become associated with the basics of the site, we try to keep order as best as we can so we have some rules in place.<br /><br />Thank you for joining, once this has been confirmed another e-mail will be dispached with links on what you can do in our site!<br /<br /> Regards,<br /><br />FTW Entertainment LLC & AnimeFTW Staff.";
					if ( send_email ( $subject, $row->Email, $message ) ) {
						$error = 'Email Re-sent, please check, your spam box incase it is not in your inbox.';
					}
					else {
						$error = 'The validation email was seemingly not sent, please pop in our <a href="http://animeftw.tv/irc">chatroom</a> and give us a heads up.';
					}
				}
				else {
					$error = 'Member not found. Please create an account <a href="/register">here</a>.';
				}
		}
		else {
			$error = 'Username does NOT exist!';
		}
	}
}
else if($_GET['node'] == 'reviews')
{
	$PageTitle = 'Series Reviews - AnimeFTW.TV';
	if(isset($_GET['subnode']) && $_GET['subnode'] == 'submit'){
		$sid = $_POST['sid'];
		$uid = $_POST['uid'];
		$ver = $_POST['ver'];
		$review = $_POST['review'];
		$rating = $_POST['art_rating'];
		if(md5($uid) == $ver){
	 		$query = sprintf("INSERT INTO reviews (sid, uid, date, review, stars, approved) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($sid, $conn),
				mysql_real_escape_string($uid, $conn),
				mysql_real_escape_string(time(), $conn),
				mysql_real_escape_string($review, $conn),
				mysql_real_escape_string($rating, $conn),
				mysql_real_escape_string('0', $conn));
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
		}
		else {
			header('location: /reviews');
		}
	}
}
else if($_GET['node'] == 'password-confirm')
{
	$PageTitle = 'Password Confirmation - AnimeFTW.TV';
	require_once ( 'includes/settings.php' );

	$query = "SELECT * FROM users WHERE ID = " . mysql_real_escape_string($_GET['ID']);
	$result = mysql_query($query);

	if(mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_assoc($result);
		if($row['Temp_pass'] == $_GET['new'] && $row['Temp_pass_active'] == 1)
		{
			$msg = 'Your new password has been confirmed. You may login using it.';
			$update = mysql_query("UPDATE `users` SET `Password` = '" . mysql_real_escape_string(md5($row['Temp_pass'])) . "', `Temp_pass_active` = 0 WHERE ID = " . mysql_real_escape_string($_GET['ID']));
		}
		else
		{
			$error = 'The new password is already confirmed or is incorrect';
		}
	}
	else {
		$error = 'This member does not exist.';
	}

}
else if($_GET['node'] == 'donate'){
	$PageTitle = 'Site Donations - AnimeFTW.TV';
	include('includes/classes/donate.class.php');
	$donate = new AFTWDonate();
}
else {
	$PageTitle = 'Welcome - AnimeFTW.TV';
}

	include('header.php');
	include('header-nav.php');
	echo psa($profileArray);
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	if($_GET['node'] == 'login' && (isset($error) || isset($msg))){
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='note-message' align='center'>";
		if(isset($error)){
		echo $error;
		}
		if(isset($msg)){
			echo $msg;
		}
		else {'Oooops it didnt work try again >.>';}
		echo "</td>\n";
		echo "</tr>\n</table>\n";
		echo "<br />\n<br />\n";
	}
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	echo '<div class="side-body-bg">';
	if($_GET['node'] == 'login'){
		echo '<span class="scapmain">AnimeFTW.tv Members Login</span>
			<br />
			<span class="poster">Use your AnimeFTW.tv Account to access copious amounts of Anime in the Greatest Quality found on the net.</span>
			</div>
			<div class="tbl"><br />';
		if(isset($_COOKIE['__flc'])){
			$timeleft = $_COOKIE['__flc'] - time();
			$time = round($timeleft/60);
			echo 'ERROR: '.$time.' minute(s) left before reactivation.';
		}
		else {
			echo'<br />';
			if(!isset($_SERVER['HTTP_REFERER'])){
				$referer2 = 'http://www.animeftw.tv/';
			}
			else {$referer2 = $_SERVER['HTTP_REFERER'];}
			echo '<div align="center"><form id="form1" action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<input type="hidden" name="_submit_check" value="1" />
				<input type="hidden" name="issubmit" value="1">
				<input type="hidden" name="cookies-set" value="0" id="cookies-set" />
				<div id="cookie-warning" style="margin-top:-10px;margin-bottom:10px;display:none;">
					<div style="padding:5px;border:1px solid #e76b6b;background-color:#e76b6b;border-radius:5px;color:white;">WARNING: AnimeFTW.tv requires cookies to log in, if they are not enabled you will not be able to log in.</div>
				</div>
				<script type="text/javascript">
				are_cookies_enabled();
				function are_cookies_enabled()
				{
					var cookieEnabled = (navigator.cookieEnabled) ? true : false;

					if (typeof navigator.cookieEnabled == "undefined" && !cookieEnabled)
					{
						document.cookie="testcookie";
						cookieEnabled = (document.cookie.indexOf("testcookie") != -1) ? true : false;
					}
					if(cookieEnabled == false)
					{
						$("#cookie-warning").css("display","");
						$("#cookies-set").val("1");
					}
				}
				</script>
				<table width="500px">
				<tr>
					<td align="right">
					<input name="last_page" type="hidden" value="'.$referer2.'" />
					<label class="left" for="username" style="margin: 0px 0px 0px 0px;color:#555555;">Username:</label>
					</td>
					<td width="340px" align="left">
					<input name="username" id="username" type="text" class="loginForm" style="width: 227px" /><br />
					</td>
				</tr>
				<tr>
					<td align="right">
					<label class="left" for="password" style="margin: 0px 0px 0px 0px;color:#555555;">Password:</label>
					</td>
					<td align="left">
					<input name="password" id="password" type="password" class="loginForm" style="width:154px;" />
					<input name="submit" type="submit" class="button_2" value="Sign In" />
					</td>
				</tr>
				<tr>
				<td colspan="2">
				<div class="cb"></div>
					<div style="margin: 5px 0px 0px 100px;">
							<div style="margin-left:50px;"><input type="checkbox" name="remember" id="remember" checked="checked" />Keep me logged in</div>
							<div style="font-size: 9px;">(Not recommended for public or shared computers)</div>
							<div style="margin: 10px 0px 0px 50px;"><a href="/forgot-password">Forgot Password?</a></div>
							<div style="margin: 10px 0px 0px 50px;">Don\'t have an account? <a href="/register">Register Here.</a></div>
					</td>
				  </tr>
				 </table>
				 </form>
				 <br /><br />
				 <i>AnimeFTW.tv Members Enjoy many perks over the average Anime Streaming site. By logging in with your AnimeFTW.tv Account, you are given access to the net\'s Largest library of on Demand Streaming Anime in HD Quality. <br /><br />Along with the perks that come with being a basic member, users can upgrade their account, "FTW Subscribers" are allowed to enhance their AnimeFTW.tv Account by making them Advanced Members. AMs for short, are allowed to download all our videos and have direct access to the CDN for the fastest download speeds anywhere in the world.</i></div>
				 </div>';
		}
	}

	else if($_GET['node'] == 'register'){
		echo "<span class='scapmain'>AnimeFTW.tv Registration</span>\n";
		echo "<br />\n";
		echo "<span class='poster'>Registration consists of 4 Panels needing to be filled out.</span>\n";
		echo "</div>\n";
		// Start Mid and Right Content
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td valign='top' class='main-mid'>\n";
		echo '<table align="center" border="0" cellpadding="0" cellspacing="0">
	<tr><td>';
		if( isset ( $error ) && (isset($noReg) && $noReg != 'yes'))	{
			echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
			echo "<td class='note-message' align='center'><b>Registration Error: </b>".$error."</td>\n";
			echo "</tr>\n</table>\n";
		}
		else if ( isset($error) && (isset($noReg) && $noReg == 'yes') ){
			echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
			echo "<td class='note-message' align='center'><b>Registration Message: </b>".$error."</td>\n";
			echo "</tr>\n</table></td></tr></table>\n";
		}
		else {
			// for everything else..
			if(isset($error)){
				echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
				echo "<td class='note-message' align='center'><b>Registration Error: </b>".$error."</td>\n";
				echo "</tr>\n</table></td></tr></table>\n";
			}
		}
		if(isset($noReg) && $noReg == 'yes'){
			echo "<br /><br /><br /><br /><br /><br />";
		}
		else {
		echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">
				<input type="hidden" name="_submit_check" value="1" />
				<input type="hidden" name="issubmit" value="1">
				<div id="wizard" class="swMain">
					<ul>
						<li><a href="#step-1"><label class="stepNumber">1</label><span class="stepDesc">Account Details<br /><small>Fill your account details</small></span></a></li>
						<li><a href="#step-2"><label class="stepNumber">2</label><span class="stepDesc">Profile Details<br /><small>Fill your profile details</small></span></a></li>
						<li><a href="#step-3"><label class="stepNumber">3</label><span class="stepDesc">Preferences<br /><small>Some Small Site Settings</small></span></a></li>
						<li><a href="#step-4"><label class="stepNumber">4</label><span class="stepDesc">Validations<br /><small>Prove your not a bot.</small> </span></a></li>
					</ul>
					<div id="step-1"><h2 class="StepTitle">Step 1: Account Details</h2>
					<table cellspacing="3" cellpadding="3" align="center">
						<tr><td align="center" colspan="3">&nbsp;</td></tr>
						<tr>
							<td align="right">Username* :</td>
							<td align="left"><input type="text" id="username" name="username" class="txtBox" onKeyUp="timeoutUsernameCheck()" '; if((isset($FailCheck) && $FailCheck == TRUE)){echo 'value="'.$_POST['username'].'"';} echo '></td>
							<td align="left"><span id="msg_username"></span>&nbsp;<div id="username_exists" style="display: inline; margin-right:140px; float:right;"></div></td>
						</tr>
						<tr>
							<td align="right">Password* :</td>
							<td align="left"><input type="password" id="password" name="password" class="txtBox" '; if((isset($FailCheck) && $FailCheck == TRUE)){echo 'value="'.$_POST['password'].'"';} echo '></td>
							<td align="left"><span id="msg_password"></span>&nbsp;</td>
						</tr>
						<tr>
							<td align="right">Confirm Password* :</td>
							<td align="left"><input type="password" id="cpassword" name="cpassword" class="txtBox" '; if((isset($FailCheck) && $FailCheck == TRUE)){echo 'value="'.$_POST['cpassword'].'"';} echo '></td>
							<td align="left"><span id="msg_cpassword"></span>&nbsp;</td>
						</tr>
						<tr>
							<td align="right">Email* :</td>
							<td align="left"><input type="text" id="email" name="email" class="txtBox" '; if((isset($FailCheck) && $FailCheck == TRUE)){echo 'value="'.$_POST['email'].'"';} echo '></td>
							<td align="left"><span id="msg_email"></span>&nbsp;</td>
						</tr>
						<tr>
							<td align="right">Confirm Email* :</td>
							<td align="left"><input type="text" id="cemail" name="cemail" class="txtBox" '; if((isset($FailCheck) && $FailCheck == TRUE)){echo 'value="'.$_POST['cemail'].'"';} echo '></td>
							<td align="left"><span id="msg_cemail"></span>&nbsp;</td>
						</tr>
					</table>
					</div>
					<div id="step-2">
					<h2 class="StepTitle">Step 2: Profile Details</h2>
					<table cellspacing="3" cellpadding="3" align="center">
						<tr><td align="center" colspan="3">&nbsp;</td></tr>
						<tr>
							<td align="right">First Name :</td>
							<td align="left"><input type="text" id="firstname" name="firstname" value="" class="txtBox"></td>
							<td align="left"><span id="msg_firstname"></span>&nbsp;</td>
						</tr>
						<tr>
							<td align="right">Gender :</td>
							<td align="left">
								<select id="gender" name="gender" class="txtBox">
								  <option value="">-select-</option>
								  <option value="Female">Female</option>
								  <option value="Male">Male</option>
								</select>
							</td>
							<td align="left"><span id="msg_gender"></span>&nbsp;</td>
						</tr>
						<tr>
							<td align="right">Age* :</td>
							<td align="left">
								<select name="ageDay" class="txtBox2">
								<option value="00" selected="selected">--Day--</option>';
								for($i=1; $i<=31; $i++)
								{
									$ri = $i<10?('0'.$i):$i;
									echo '<option value="' . $ri . '">' . $i . '</option>';
								}
								echo '</select>
								<select name="ageMonth" class="txtBox2">
								<option value="00" selected="selected">--Month--</option>';
								$monthsarr = array('January','February','March','April','May','June','July ','August','September','October','November','December');
								for($i=0; $i<=11; $i++)
								{
									$ri = ($i+1)<10?('0'.($i+1)):($i+1);
									echo '<option value="' . $ri . '">' . $monthsarr[$i] . '</option>';
								}
								echo '</select>
								<select name="ageYear" class="txtBox2">
								<option value="0000" selected="selected">--Year--</option>';
								$startyear = date("Y")-90;
								$endyear = date("Y")-12;
								for($i=$endyear; $i>=$startyear; $i--)
								{
									echo '<option value="' . $i . '">' . $i . '</option>';
								}
								echo '</select>
							</td>
						</tr>
					</table>
					</div>
					<div id="step-3">
					<h2 class="StepTitle">Step 3: Preferences</h2>
					<table cellspacing="3" cellpadding="3" align="center">
							<tr>
								<td align="center" colspan="3">&nbsp;</td>
							</tr>
							<tr>
								<td align="right">Timezone* :</td>
								<td align="left">
		<select name="timeZone" class="txtBox">
			<option value="-12"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-12'){echo ' selected="selected"';} echo '>(GMT - 12:00 hours) Enewetak, Kwajalein</option>
			<option value="-11"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-11'){echo ' selected="selected"';} echo '>(GMT - 11:00 hours) Midway Island, Samoa</option>
			<option value="-10"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-10'){echo ' selected="selected"';} echo '>(GMT - 10:00 hours) Hawaii</option>
			<option value="-9.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-9.5'){echo ' selected="selected"';} echo '>(GMT - 9:30 hours) French Polynesia</option>
			<option value="-9"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-9'){echo ' selected="selected"';} echo '>(GMT - 9:00 hours) Alaska</option>
			<option value="-8"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-8'){echo ' selected="selected"';} echo '>(GMT - 8:00 hours) Pacific Time (US &amp; Canada)</option>
			<option value="-7"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-7'){echo ' selected="selected"';} echo '>(GMT - 7:00 hours) Mountain Time (US &amp; Canada)</option>
			<option value="-6"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-6'){echo ' selected="selected"';}else{echo 'selected="selected"';} echo '>(GMT - 6:00 hours) Central Time (US &amp; Canada), Mexico City</option>
			<option value="-5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-5'){echo ' selected="selected"';} echo '>(GMT - 5:00 hours) Eastern Time (US &amp; Canada), Bogota, Lima</option>
			<option value="-4"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-4'){echo ' selected="selected"';} echo '>(GMT - 4:00 hours) Atlantic Time (Canada), Caracas, La Paz</option>
			<option value="-3.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-3.5'){echo ' selected="selected"';} echo '>(GMT - 3:30 hours) Newfoundland</option>
			<option value="-3"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-3'){echo ' selected="selected"';} echo '>(GMT - 3:00 hours) Brazil, Buenos Aires, Falkland Is.</option>
			<option value="-2"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-2'){echo ' selected="selected"';} echo '>(GMT - 2:00 hours) Mid-Atlantic, Ascention Is., St Helena</option>
			<option value="-1"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '-1'){echo ' selected="selected"';} echo '>(GMT - 1:00 hours) Azores, Cape Verde Islands</option>
			<option value="0"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '0'){echo ' selected="selected"';} echo '>(GMT) Casablanca, Dublin, London, Lisbon, Monrovia</option>
			<option value="1"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '1'){echo ' selected="selected"';} echo '>(GMT + 1:00 hours) Brussels, Copenhagen, Madrid, Paris</option>
			<option value="2"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '2'){echo ' selected="selected"';} echo '>(GMT + 2:00 hours) Kaliningrad, South Africa</option>
			<option value="3"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '3'){echo ' selected="selected"';} echo '>(GMT + 3:00 hours) Baghdad, Riyadh, Moscow, Nairobi</option>
			<option value="3.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '3.5'){echo ' selected="selected"';} echo '>(GMT + 3:30 hours) Tehran</option>
			<option value="4"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '4'){echo ' selected="selected"';} echo '>(GMT + 4:00 hours) Abu Dhabi, Baku, Muscat, Tbilisi</option>
			<option value="4.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '4.5'){echo ' selected="selected"';} echo '>(GMT + 4:30 hours) Kabul</option>
			<option value="5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '5'){echo ' selected="selected"';} echo '>(GMT + 5:00 hours) Ekaterinburg, Karachi, Tashkent</option>
			<option value="5.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '5.5'){echo ' selected="selected"';} echo '>(GMT + 5:30 hours) Bombay, Calcutta, Madras, New Delhi</option>
			<option value="5.75"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '5.75'){echo ' selected="selected"';} echo '>(GMT + 5:45 hours) Kathmandu</option>
			<option value="6"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '6'){echo ' selected="selected"';} echo '>(GMT + 6:00 hours) Almaty, Colombo, Dhaka</option>
			<option value="6.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '6.5'){echo ' selected="selected"';} echo '>(GMT + 6:30 hours) Yangon, Naypyidaw, Bantam</option>
			<option value="7"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '7'){echo ' selected="selected"';} echo '>(GMT + 7:00 hours) Bangkok, Hanoi, Jakarta</option>
			<option value="8"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '8'){echo ' selected="selected"';} echo '>(GMT + 8:00 hours) Hong Kong, Perth, Singapore, Taipei</option>
			<option value="8.75"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '8.75'){echo ' selected="selected"';} echo '>(GMT + 8:45 hours) Caiguna, Eucla</option>
			<option value="9"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '9'){echo ' selected="selected"';} echo '>(GMT + 9:00 hours) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
			<option value="9.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '9.5'){echo ' selected="selected"';} echo '>(GMT + 9:30 hours) Adelaide, Darwin</option>
			<option value="10"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '10'){echo ' selected="selected"';} echo '>(GMT + 10:00 hours) Melbourne, Papua New Guinea, Sydney</option>
			<option value="10.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '10.5'){echo ' selected="selected"';} echo '>(GMT + 10:30 hours) Lord Howe Island</option>
			<option value="11"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '11'){echo ' selected="selected"';} echo '>(GMT + 11:00 hours) Magadan, New Caledonia, Solomon Is.</option>
			<option value="11.5"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '11.5'){echo ' selected="selected"';} echo '>(GMT + 11:30 hours) Burnt Pine, Kingston</option>
			<option value="12"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '12'){echo ' selected="selected"';} echo '>(GMT + 12:00 hours) Auckland, Fiji, Marshall Island</option>
			<option value="12.75"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '12.75'){echo ' selected="selected"';} echo '>(GMT + 12:45 hours) Chatham Islands</option>
			<option value="13"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '13'){echo ' selected="selected"';} echo '>(GMT + 13:00 hours) Kamchatka, Anadyr</option>
			<option value="14"'; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['timeZone'] == '14'){echo ' selected="selected"';} echo '>(GMT + 14:00 hours) Kiritimati</option>
			</select>
							  </td>
								<td align="left"><span id="msg_tz"></span>&nbsp;</td>
							</tr>
							<tr>
								<td align="right">Receive Administrator Notifications :</td>
								<td align="left">
									<select name="notifications" class="txtBox">
										<option value="1" '; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['notifications'] == '1'){echo ' selected="selected"';}else{echo ' selected="selected"';} echo '>Yes</option>
										<option value="0" '; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['notifications'] == '0'){echo ' selected="selected"';} echo '>No</option>
									</select>
								</td>
								<td align="left">&nbsp;</td>
							</tr>
							<tr>
								<td align="right">Site PM Notifications :</td>
								<td align="left">
									<select name="sitepmnote" class="txtBox">
										<option value="1" '; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['sitepmnote'] == '1'){echo ' selected="selected"';}else{echo ' selected="selected"';} echo'>Yes</option>
										<option value="0" '; if((isset($FailCheck) && $FailCheck == TRUE) && $_POST['sitepmnote'] == '0'){echo ' selected="selected"';} echo'>No</option>
									</select>
								</td>
								<td align="left">&nbsp;</td>
							</tr>
					   </table>
				</div>
					<div id="step-4">
					<h2 class="StepTitle">Step 4: Validation</h2>
					<table cellspacing="3" cellpadding="3" align="center">
							<tr>
								<td align="center" colspan="3">&nbsp;</td>
							</tr>
							<tr>
								<td align="right" valign="top">Bot Check* :</td>
								<td align="left">
								  <div style="padding-bottom:5px;">Solve this equation to prove you are not a bot: <b>(2+8)-4+3</b></div>
								  <input type="text" maxlength="150" id="google" name="google" class="txtBox" />
							  </td>
								<td align="left" valign="bottom"><span id="msg_google"></span>&nbsp;</td>
							</tr>
							<tr>
								<td align="right" valign="top">Are you Human?* :</td>
								<td align="left"><div align="center">
								<div class="g-recaptcha" data-sitekey="6Lej28MSAAAAAHbSX338LhM9FdQD-RalGgrKSM3Z"></div>
								<span style="color: red;" id="captchaStatus">&nbsp;</span></div></td>
								<td align="left"><span id="msg_recaptcha"></span>&nbsp;</td>
							</tr>
							<tr>
								<td align="right">Agreement* :</td>
								<td align="left"><input name="agreement" type="checkbox" value="yes" id="agreement" /> by Submitting your registration you agree to AnimeFTW.tv\'s <a href="/tos" target="_blank">ToS</a> as well as our <a href="/rules" target="_blank">Rules</a></td>
								<td align="left"><span id="msg_agreement"></span>&nbsp;
								</td>
							</tr>
					   </table>
					   </td>
					</tr>
			   </table>
			</td>
		</tr>
	</table>
				</div>
				</div>
		</form>
		<div align="center">
		*= Is required to signup on AnimeFTW.tv, if you have any questions please use the <a href="/contact-us">Contact Us</a> or hop in the <a href="/irc">Chat</a> and leave us a message.</div>';
		}
	}

else if($_GET['node'] == 'forgot-password'){
	echo '<span class="scapmain">AnimeFTW.tv Members Password Recovery</span>
		<br />
		<span class="poster">Forgot your password to the site? No problem! Use this form to get an email for your temp password.</span>
		</div>
		<div class="tbl"><br />';
		if(isset($error)){echo '<div align="center" style="font-size:18px;">'.$error.'</div>';}
    echo '<div align="center"><form id="form1" action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<input type="hidden" name="_submit_check" value="1" />
			<input type="hidden" name="issubmit" value="1">
			<input type="hidden" name="var" value="1">
            <table width="500px">
            <tr>
            	<td align="right">
                <label class="left" for="somerandomvar" style="margin: 0px 0px 0px 0px;color:#555555;">Email:</label>
                </td>
                <td align="left">
                <input name="somerandomvar" id="somerandomvar" type="text" class="loginForm" style="width:154px;" />
                <input name="submit" type="submit" class="button_2" value="Submit" />
                </td>
            </tr>
            <tr>
            <td colspan="2">
            <div class="cb"></div>
				<div style="margin: 5px 0px 0px 50px;">
						<div align="center" style="font-size: 9px;">When you submit the above form, AnimeFTW.tv will email you with a link to establish a temp password.</div>
             	</td>
              </tr>
             </table>
             </form>
			 </div>
			 </div>';
}
else if($_GET['node'] == 'email-resend'){
	echo '<span class="scapmain">AnimeFTW.tv Welcome Email Resend.</span>
		<br />
		<span class="poster">Didn&rsquo;t get your Welcome Email? Use this form to resend the email so you can activate your account!.</span>
		</div>
		<div class="tbl"><br />';
		if(isset($error)){echo '<div align="center" style="font-size:18px;">'.$error.'</div>';}
    echo '<div align="center"><form id="form1" action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<input type="hidden" name="_submit_check" value="1" />
			<input type="hidden" name="issubmit" value="1">
			<input type="hidden" name="var" value="2">
            <table width="500px">
            <tr>
            	<td align="right">
                <label class="left" for="username" style="margin: 0px 0px 0px 0px;color:#555555;">Username:</label>
                </td>
                <td align="left">
                <input name="username" id="username" type="text" class="loginForm" style="width:154px;" />
                <input name="submit" type="submit" class="button_2" value="Submit" />
                </td>
            </tr>
            <tr>
            <td colspan="2">
            <div class="cb"></div>
				<div style="margin: 5px 0px 0px 50px;">
						<div align="center" style="font-size: 9px;">When you submit the above form, AnimeFTW.tv will email you the welcome email, allowing you to complete the registration of your account.</div>
             	</td>
              </tr>
             </table>
             </form>
			 </div>
			 </div>';
}
else if($_GET['node'] == 'advanced-signup'){
		echo '<span class="scapmain">AnimeFTW.TV Advanced Members</span>
		<br />
		<span class="poster">Interested in all of the features that come with being Advanced Member of the site, Read on!</span>
		</div>
		<div class="tbl"><br />';
		echo '<div align="center">As the site has grown, so has our offering, many users asked us, how could we support the site, one the devoted itself to the highest quality anime on the net. After much thought, we came up with the Advanced Member Title. Since the conception of the Advanced Member, we have evolved the status to something more. FTW Subscribers, users who are a FTW Subscriber have access to all of the premium benefits on all of FTW Entertainment LLC\'s Sites, Being an Advanced Member is a Subsidary of this overall perk.</div> <br /><br />
				<div>To be an FTW Subscriber, there is a monthly re-accuring fee, ranging from $5.00 USD a month to $4.10 USD a month, depending on the package  chosen is how much you pay. To be an FTW Subscriber, is to be a part of something bigger, AnimeFTW.TV and FTW Entertainment LLC are on a mission to bring high Quality Anime to the Masses, for free. We want you to be excited about your daily dose of anime, not just because the story is good, because the <i>quallity</i> is amazing.<br /><br />Below are some lists of perks that you will get on AnimeFTW.TV for signing up as a FTW Subscriber.</div>
				<div align="center"><h5>(Click Images to see a bigger image)<h5></div>
				<div align="left"><a href="/images/externalamdownloadnew.png" target="_blank"><img src="/images/externalamdownloadnew.png" width="350" alt="External AM downloads" style="float:left;" /></a><br /><b>Know What episodes you love?</b> <br />Download them from the episodes listing in a flash!</div><br /><br /><br /><br />
				<div align="right"><a href="/images/AMstatus.png" target="_blank"><img src="/images/AMstatus.png" width="350" alt="AM Status" style="float:right;" /></a><b>Like to let people know of your current doings?</b> <br />Set your status on your profile page!</div><br /><br /><br />
				<div align="left"><a href="/images/personalamsettings.png" target="_blank"><img src="/images/personalamsettings.png" height="300" alt="AM Profiles" style="float:left;" /></a><br /><b>Love profiles?</b> <br />With even more options for your account, go nuts!</div><br /><br /><br /><br /><br /><br /><br />
				<div align="right"><a href="/images/html5video.png" target="_blank"><img src="/images/html5video.png" width="350" alt="HTML5 Player" style="float:right;" /></a><b>Want to use the HTML5 Video player?</b> <br />Choose to keep the DivX player, or start using the HTML5 player right away!</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				<div align="left"><a href="/images/sslsupport.png" target="_blank"><img src="/images/sslsupport.png" width="350" alt="Site Wide SSL Support" style="float:left;" /></a><br /><b>Don\'t want someone snooping on your anime?</b> <br />Use AnimeFTW.tv securely, on ALL pages!</div><br />
				<br /><br />
                <div align="center" style="padding-top:100px;">
				<table width="90%" border="0" cellspacing="1" cellpadding="1">
  <tr bgcolor="#CCCCCC">
    <td align="center"><b>Service</b></td>
    <td align="center"><b>No Membership</b></td>
    <td align="center"><b>Basic Membership</b></td>
    <td align="center"><b>Advanced Membership</b></td>
  </tr>
  <tr>
    <td align="center">Streaming</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center">Comments account</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Profile to Update</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center">Basic Profile Fields</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Friends Allowed</td>
    <td align="center">&nbsp;</td>
    <td align="center">15</td>
    <td align="center">45</td>
  </tr>
   <tr bgcolor="#CCCCCC">
    <td align="center">Interests Section</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">About You Section</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
   <tr bgcolor="#CCCCCC">
    <td align="center">Nickname customization</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Icon before Nickname</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
   <tr bgcolor="#CCCCCC">
    <td align="center">Direct Downloads</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Signature</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
   <tr bgcolor="#CCCCCC">
    <td align="center">Nickname Change</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Enhanced Episode Tracker</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center">No Ads or Waiting Periods</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Profile Status</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center">HTML5 Video</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
  <tr>
    <td align="center">Site Wide SSL Support</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="/images/checkmark.gif" alt="check mark" /></td>
  </tr>
</table>
</div>
<br />
<br />
<div>
Since becoming an Advanced Member is a product of being a FTW Subscriber, we are asking everyone to continue over to <a href="https://ftwentertainment.com/supporters">FTW Entertainment\'s Supporters Zone</a> To continue with the signup process, once your account has been updated as a FTW Subscriber you will see the AnimeFTW.TV Advanced Status come to life.</div><br /><div align="center"><h2><a href="https://ftwentertainment.com/subscribers">Continue to FTW Entertainment\'s Subscriber Zone</a></h2></div>';
		echo '</div>';
}
else if($_GET['node'] == 'reviews'){
	echo '<span class="scapmain">AnimeFTW.TV Series Reviews</span>
	<br />
	<span class="poster">Loved a specific series? Well review it for other to see!</span>
	</div>
	<div class="tbl"><br />';
	if($profileArray[0] == 0){
		echo '<h2>ERROR</h2><br />Please <a href="https://www.animeftw.tv/login">Login</a> so that you can review a Series!';
	}
	else {
		$totalreviews = mysql_query("SELECT * FROM reviews WHERE uid='$globalnonid'");
		$totalreviews = mysql_num_rows($totalreviews);
		if($profileArray[2] == 3){
			$possibleUserreviews = 5;
		}
		else {
			$possibleUserreviews = 500;
		}
		if($totalreviews == $possibleUserreviews)
		{
			echo '<h2>ERROR</h2><br />You have exceeded your allotted reviews of: '.$possibleUserreviews.'<br /><br />Become an Advanced Member today and get Unlimited reviews!';
		}
		else {
		  if(!isset($_GET['subnode'])){
			  ?>
                <h2>Reviewed Series</h2>
                <i>Below are your current list of Reviewed <b>Approved</b> Series</i>
                <br />
                <?=userReviewedSeries($profileArray[2],1);?>
                <br />
                <br />
                <h2>Available Series to Review</h2><br />
		   <?php
		   		$v = new AFTWVideos(); //Build our videos
				echo "<br /><div id=\"lister\" style=\"min-height:3640px;\">";
				echo $v->showListing(0,NULL,$profileArray[2],0,TRUE);

					echo '<br /></div>
				</div>';


		  }
		   if($_GET['subnode'] == 'review'){
			   //Pull the respective Series ID number and get the series info and name..
				$ReviewedSeries = $_GET['sid'];
				$possiblereviews = mysql_query("SELECT * FROM reviews WHERE uid='$profileArray[2]' AND sid='$ReviewedSeries'");
				$reviews = mysql_num_rows($possiblereviews);
				if($reviews == 0)
				{
					$reviewsquery = mysql_query("SELECT fullSeriesName, description FROM series WHERE id='$ReviewedSeries'");
					$seriesCount = mysql_num_rows($reviewsquery);
				   echo '<div class="mpart">
						<h2>AnimeFTW Series Reviews!</h2><br /><br />';
					if($seriesCount == 0)
					{
						echo '<b>ERROR: NO Such Series! Click back or <a href="/reviews/">return</a> to the Reviews Home page.';
					}
					else {
					$reviewInfo = mysql_fetch_array($reviewsquery);
					$rFullSeriesName = $reviewInfo['fullSeriesName'];
					$rdescription = $reviewInfo['description'];
					$rdescription = stripslashes($rdescription);
					   echo 'You are about to review the series:<br /><br />';
						echo checkSeriesSid($ReviewedSeries).'</a><br />
							<br /><i>Synopsis</i>:
							<br />'.$rdescription.'<br /><br />';

							echo "<form id='postingform' action='http://".$siteroot."/reviews/submitted/' method='post' name='REPLIER' enctype='multipart/form-data'>
					<input type='hidden' name='sid' value='$ReviewedSeries' />
					<input type='hidden' name='uid' value='$profileArray[1]' />
					<input type='hidden' name='ver' value='".md5($profileArray[1])."' />\n";
					echo 'Stars:<br />
					<select name="art_rating" id="art_rating">
					<option value="0" id="art_rating0">Choose</option>
					<option value="1" id="art_rating1">1 - Dreadful</option>
					<option value="2" id="art_rating2">2 - Poor</option>
					<option value="3" id="art_rating3">3 - Fair</option>
					<option value="4" id="art_rating4">4 - Good</option>
					<option value="5" id="art_rating5">5 - Excellent</option>
					</select><br /><br />';
				   echo "Review:<br /><div style='padding-bottom:8px'>
				   <textarea id='review' name='review' cols='80' rows='10' style='padding:4px;width:90%'></textarea>
				   </div><br />
		<input name='submit' id='submit' type='submit' value='Submit Review' />
				</form>\n";
					}
				}
				else {
					echo '<br />ERROR, you have already Reviewed this series! Only one Review per series!<br />';
				}
				echo '<div class="date"></div>
				</div>';
		   }
		   if($_GET['subnode'] == 'submit')
		   {

			   echo '<div class="mpart">
				<h2>AnimeFTW Series Reviews! - Review submitted!</h2><br />
					<br />Thank you for submitting a review, our reports team will look over your review and approve/deny accordingly!<br /><br />Please remember that Basic Members are allowed to have 5 submitted reviews, be they pending or approved.<br /><br />  Want to review more series? Become an <a href="/advanced-signup">Advanced Member</a> and get reviewing today!
				<div class="date"></div>
				</div>';
		   }
		}
	}
}
else if($_GET['node'] == 'password-confirm'){

	if ( isset ( $error ) ) {
		echo $error;
	}
	else {
		echo $msg;
	}
}
else if($_GET['node'] == 'donate'){
	$donate->Output();
}
else {
	echo 'Nothing to see here.';
}
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
    echo "&nbsp;</td>\n";
	echo "</tr>\n</table>\n";
    echo "&nbsp;</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG

include('footer.php')
?>
