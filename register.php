<?php
include('init.php');
include('includes/siteroot.php');

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']))
{
	if($_POST['agreement'] == 'yes')
	{
		require_once('includes/settings.php');
		$postUsername = $_POST['username'];
		$postUsername = substr($postUsername, 0, 20);
		if(($_POST['issubmit'] && $_POST['_submit_check']) == 1)
		{
			if ( $_POST['username'] != '' && $_POST['password'] != '' && ($_POST['password'] == $_POST['cpassword']) && ($_POST['email'] == $_POST['cemail']) && valid_email ( $_POST['email'] ) == TRUE )
			{
				if(!isset($_POST['agreement']))
				{
					$error = 'You did not agree with our ToS and our Rules. Please try again.';
					$FailCheck = TRUE;
				}
				else {
					if ( ! checkUnique ( 'Username', $_POST['username'] ) )
					{
						$error = 'Username already taken. Please try again!';
						$FailCheck = TRUE;
					}
					elseif ( ! checkUnique ( 'Email', $_POST['email'] ) )
					{
						$error = 'The email you used is associated with another user. Please try again or use the "forgot password" feature!';
						$FailCheck = TRUE;
					}
					else {	 
						function makeUrlFriendly($postUsername) {
							// Replace spaces with underscores
							$output = preg_replace("/\s/e" , "_" , $postUsername);
							// Remove non-word characters
							$output = preg_replace("/\W/e" , "" , $output);
							return strtolower($output);
						}	
						if($_POST['google'] != '9')
						{
							$error = 'You have failed the bot check, please try again.';
							$FailCheck = TRUE;
						}
						else {
							$splitEmail = split("@",$_POST['email']);
							$finalEmailCheck = checkFakeEmail($splitEmail[1]);
							if($finalEmailCheck >0)
							{
								$error = 'The email you have provided has been marked as spam, please try a different email!';
								$FailCheck = TRUE;
							}
							else {
								$query = $db->query ( "INSERT INTO users (`Username` , `Password`, `registrationDate`, `Email`, `Random_key`, `firstName`, `gender`, `ageDate`, `ageMonth`, `ageYear`, `staticip`, `timeZone`, `sitepmnote`, `notifications`) VALUES (" . $db->qstr ( makeUrlFriendly("$postUsername") ) . ", " . $db->qstr ( md5 ( $_POST['password'] ) ).", '" . time () . "', " . $db->qstr ( $_POST['email'] ) . ", '" . random_string ( 'alnum', 32 ) . "', '" . $_POST['firstname'] . "', '" . $_POST['gender'] . "', '" . $_POST['ageDate'] . "', '" . $_POST['ageMonth'] . "', '" . $_POST['ageYear'] . "', '".$_SERVER['REMOTE_ADDR']."', '".$_POST['timeZone']."', '".$_POST['sitepmnote']."', '".$_POST['notifications']."')" );
								
								$getUser = "SELECT ID, Username, Email, Random_key FROM users WHERE Username = " . $db->qstr ( makeUrlFriendly("$postUsername") ) . "";
								if ( $db->RecordCount ( $getUser ) == 1 )
								{			
									$row = $db->getRow ( $getUser );
									$subject = "Activation email from AnimeFTW.tv";
									$message = "Dear ".$row->Username.", this is your activation link to join our website at animeftw.tv. <br /><br /> In order to confirm your membership please click on the following link: <a href=\"http://www.animeftw.tv/confirm?ID=" . $row->ID . "&key=" . $row->Random_key . "\">http://www.animeftw.tv/confirm?ID=" . $row->ID . "&key=" . $row->Random_key . "</a> <br /><br />After you confirm your status with us, please go visit <a href=\"http://www.animeftw.tv/rules\">our Rules</a> and <a href=\"http://www.animeftw.tv/faq\">our FAQ</a> and become associated with the basics of the site, we try to keep order as best as we can so we have some rules in place.<br /><br />Thank you for joining, please go and visit our rules after you have logged in to familiarize yourself with our site policies! <a href=\"http://www.animeftw.tv/rules\">Found here</a><br /><br /> Regards,<br /><br />FTW Entertainment LLC & AnimeFTW Staff.";
									if ( send_email ( $subject, $row->Email, $message ) ) {
										$msg = 'Account registered as: '.$row->Username.'. Please check your email ('.$row->Email.') for details on how to activate it.';
										$noReg = 'yes';
									}
									else {
										$error = 'I managed to register your membership but failed to send the validation email. Please contact the admin at ' . ADMIN_EMAIL;
									}
								}
								else {
									$error = 'Account Creation Complete, E-mail was not sent, please have the email re-sent using this <a href="http://www.animeftw.tv/email-resend">form</a> Using your New nickname: '.makeUrlFriendly("$postUsername");
									$FailCheck = TRUE;
								}
							}
						}
					}
				}
			}
			else {		
				$error = 'There was an error in your data. Please make sure you filled in all the required data, you provided a valid email address and that the password fields match one another.';
				$FailCheck = TRUE;	
			}
		}
		else {
			$error = 'ERROR: You did not submit from AnimeFTW.tv!';
			$FailCheck = TRUE;	
			// $msg = TRUE;
			# in a real application, you should send an email, create an account, etc
		}
	}
	else {
		$error = 'You failed to agree to our Terms of Service and our Rules, please evaluate your registration for errors.';
		$FailCheck = TRUE;	
	}
}

if($_GET['m'] == 'register']){
	include('mobile/m-header.php');
	include('mobile/m-footer.php');
}
else {
	$PageTitle = 'AnimeFTW.tv Registration | '.$siteroot.' | Your DivX Anime streaming Source!';
	include('header.php');
	include('header-nav.php');
		// Start Main BG
		echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
		echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Registration</span>\n";
		echo "<br />\n";
		echo "<span class='poster'>Registration consists of 4 Panels needing to be filled out.</span>\n";
		echo "</div>\n";
		// Start Mid and Right Content
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td valign='top' class='main-mid'>\n";
		echo '<table align="center" border="0" cellpadding="0" cellspacing="0">
	<tr><td>';
	if ( isset ( $error ) )	{
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='note-message' align='center'><b>Registration Error: </b>".$error."</td>\n";
		echo "</tr>\n</table>\n";
	}
	if ( isset ( $msg ) ){ 
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='note-message' align='center'><b>Registration Message: </b>".$msg."</td>\n";
		echo "</tr>\n</table>\n";
	}
	else {'Oooops it didnt work try again >.>';}
	if($noReg == 'yes'){
		echo "<br /><br /><br /><br /><br /><br />";
	}
	else {
	echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">
			<input type="hidden" name="_submit_check" value="1" />
			<input type="hidden" name="issubmit" value="1">
			<div id="wizard" class="swMain">
				<ul>
					<li>
						<a href="#step-1">
						<label class="stepNumber">1</label>
						<span class="stepDesc">Account Details<br /><small>Fill your account details</small></span>
						</a>
					</li>
					<li>
						<a href="#step-2">
						<label class="stepNumber">2</label>
						<span class="stepDesc">Profile Details<br /><small>Fill your profile details</small></span>
						</a>
					</li>
					<li>
						<a href="#step-3">
						<label class="stepNumber">3</label>
						<span class="stepDesc">Preferences<br /><small>Some Small Site Settings</small></span>
						</a>
					</li>
					<li>
						<a href="#step-4">
						<label class="stepNumber">4</label>
						<span class="stepDesc">Validations<br /><small>Prove your not a bot.</small> </span>
						</a>
					</li>
				</ul>
				<div id="step-1">	
				<h2 class="StepTitle">Step 1: Account Details</h2>
				<table cellspacing="3" cellpadding="3" align="center">
						<tr>
							<td align="center" colspan="3">&nbsp;</td>
						</tr>        
						<tr>
	
							<td align="right">Username* :</td>
							<td align="left">
							  <input type="text" id="username" name="username" class="txtBox" onKeyUp="timeoutUsernameCheck()" '; if($FailCheck == TRUE){echo 'value="'.$_POST['username'].'"';} echo '>
						  </td>
							<td align="left"><span id="msg_username"></span>&nbsp;
							  <div id="username_exists" style="display: inline; margin-right:140px; float:right;"></td>
						</tr>
						<tr>
							<td align="right">Password* :</td>
	
							<td align="left">
							  <input type="password" id="password" name="password" class="txtBox" '; if($FailCheck == TRUE){echo 'value="'.$_POST['password'].'"';} echo '>
						  </td>
							<td align="left"><span id="msg_password"></span>&nbsp;</td>
						</tr> 
					<tr>
							<td align="right">Confirm Password* :</td>
							<td align="left">
							  <input type="password" id="cpassword" name="cpassword" class="txtBox" '; if($FailCheck == TRUE){echo 'value="'.$_POST['cpassword'].'"';} echo '>
	
						  </td>
							<td align="left"><span id="msg_cpassword"></span>&nbsp;</td>
						</tr>  
						<tr>
							<td align="right">Email* :</td>
	
							<td align="left">
							  <input type="text" id="email" name="email" class="txtBox" '; if($FailCheck == TRUE){echo 'value="'.$_POST['email'].'"';} echo '>
						  </td>
							<td align="left"><span id="msg_email"></span>&nbsp;</td>
						</tr>
						<tr>
							<td align="right">Confirm Email* :</td>
	
							<td align="left">
							  <input type="text" id="cemail" name="cemail" class="txtBox" '; if($FailCheck == TRUE){echo 'value="'.$_POST['cemail'].'"';} echo '>
						  </td>
							<td align="left"><span id="msg_cemail"></span>&nbsp;</td>
						</tr>
				   </table>          			
			</div>
				<div id="step-2">
				<h2 class="StepTitle">Step 2: Profile Details</h2>	
				<table cellspacing="3" cellpadding="3" align="center">
						<tr>
							<td align="center" colspan="3">&nbsp;</td>
	
						</tr>        
						<tr>
							<td align="right">First Name :</td>
							<td align="left">
							  <input type="text" id="firstname" name="firstname" value="" class="txtBox">
						  </td>
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
							<select name="ageDay" class="txtBox2"><option value="00" selected="selected">--Day--</option><option value="01">1</option><option value="02">2</option><option value="03">3</option><option value="04">4</option><option value="05">5</option><option value="06">6</option><option value="07">7</option><option value="08">8</option><option value="09">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>							
						</select>
						<select name="ageMonth" class="txtBox2"><option value="00" selected="selected">--Month--</option><option value="01">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>							 
						</select>
						<select name="ageYear" class="txtBox2"><option value="0000" selected="selected">--Year--</option><option value="2005">2005</option><option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option>
						 </select>
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
		<option value="-12"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-12'){echo ' selected="selected"';} echo '>(GMT - 12:00 hours) Enewetak, Kwajalein</option>
		<option value="-11"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-11'){echo ' selected="selected"';} echo '>(GMT - 11:00 hours) Midway Island, Samoa</option>
		<option value="-10"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-10'){echo ' selected="selected"';} echo '>(GMT - 10:00 hours) Hawaii</option>
		<option value="-9.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-9.5'){echo ' selected="selected"';} echo '>(GMT - 9:30 hours) French Polynesia</option>
		<option value="-9"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-9'){echo ' selected="selected"';} echo '>(GMT - 9:00 hours) Alaska</option>
		<option value="-8"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-8'){echo ' selected="selected"';} echo '>(GMT - 8:00 hours) Pacific Time (US &amp; Canada)</option>
		<option value="-7"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-7'){echo ' selected="selected"';} echo '>(GMT - 7:00 hours) Mountain Time (US &amp; Canada)</option>
		<option value="-6"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-6'){echo ' selected="selected"';}else{echo 'selected="selected"';} echo '>(GMT - 6:00 hours) Central Time (US &amp; Canada), Mexico City</option>
		<option value="-5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-5'){echo ' selected="selected"';} echo '>(GMT - 5:00 hours) Eastern Time (US &amp; Canada), Bogota, Lima</option>
		<option value="-4"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-4'){echo ' selected="selected"';} echo '>(GMT - 4:00 hours) Atlantic Time (Canada), Caracas, La Paz</option>
		<option value="-3.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-3.5'){echo ' selected="selected"';} echo '>(GMT - 3:30 hours) Newfoundland</option>
		<option value="-3"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-3'){echo ' selected="selected"';} echo '>(GMT - 3:00 hours) Brazil, Buenos Aires, Falkland Is.</option>
		<option value="-2"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-2'){echo ' selected="selected"';} echo '>(GMT - 2:00 hours) Mid-Atlantic, Ascention Is., St Helena</option>
		<option value="-1"'; if($FailCheck == TRUE && $_POST['timeZone'] == '-1'){echo ' selected="selected"';} echo '>(GMT - 1:00 hours) Azores, Cape Verde Islands</option>
		<option value="0"'; if($FailCheck == TRUE && $_POST['timeZone'] == '0'){echo ' selected="selected"';} echo '>(GMT) Casablanca, Dublin, London, Lisbon, Monrovia</option>
		<option value="1"'; if($FailCheck == TRUE && $_POST['timeZone'] == '1'){echo ' selected="selected"';} echo '>(GMT + 1:00 hours) Brussels, Copenhagen, Madrid, Paris</option>
		<option value="2"'; if($FailCheck == TRUE && $_POST['timeZone'] == '2'){echo ' selected="selected"';} echo '>(GMT + 2:00 hours) Kaliningrad, South Africa</option>
		<option value="3"'; if($FailCheck == TRUE && $_POST['timeZone'] == '3'){echo ' selected="selected"';} echo '>(GMT + 3:00 hours) Baghdad, Riyadh, Moscow, Nairobi</option>
		<option value="3.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '3.5'){echo ' selected="selected"';} echo '>(GMT + 3:30 hours) Tehran</option>
		<option value="4"'; if($FailCheck == TRUE && $_POST['timeZone'] == '4'){echo ' selected="selected"';} echo '>(GMT + 4:00 hours) Abu Dhabi, Baku, Muscat, Tbilisi</option>
		<option value="4.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '4.5'){echo ' selected="selected"';} echo '>(GMT + 4:30 hours) Kabul</option>
		<option value="5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '5'){echo ' selected="selected"';} echo '>(GMT + 5:00 hours) Ekaterinburg, Karachi, Tashkent</option>
		<option value="5.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '5.5'){echo ' selected="selected"';} echo '>(GMT + 5:30 hours) Bombay, Calcutta, Madras, New Delhi</option>
		<option value="5.75"'; if($FailCheck == TRUE && $_POST['timeZone'] == '5.75'){echo ' selected="selected"';} echo '>(GMT + 5:45 hours) Kathmandu</option>
		<option value="6"'; if($FailCheck == TRUE && $_POST['timeZone'] == '6'){echo ' selected="selected"';} echo '>(GMT + 6:00 hours) Almaty, Colombo, Dhaka</option>
		<option value="6.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '6.5'){echo ' selected="selected"';} echo '>(GMT + 6:30 hours) Yangon, Naypyidaw, Bantam</option>
		<option value="7"'; if($FailCheck == TRUE && $_POST['timeZone'] == '7'){echo ' selected="selected"';} echo '>(GMT + 7:00 hours) Bangkok, Hanoi, Jakarta</option>
		<option value="8"'; if($FailCheck == TRUE && $_POST['timeZone'] == '8'){echo ' selected="selected"';} echo '>(GMT + 8:00 hours) Hong Kong, Perth, Singapore, Taipei</option>
		<option value="8.75"'; if($FailCheck == TRUE && $_POST['timeZone'] == '8.75'){echo ' selected="selected"';} echo '>(GMT + 8:45 hours) Caiguna, Eucla</option>
		<option value="9"'; if($FailCheck == TRUE && $_POST['timeZone'] == '9'){echo ' selected="selected"';} echo '>(GMT + 9:00 hours) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
		<option value="9.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '9.5'){echo ' selected="selected"';} echo '>(GMT + 9:30 hours) Adelaide, Darwin</option>
		<option value="10"'; if($FailCheck == TRUE && $_POST['timeZone'] == '10'){echo ' selected="selected"';} echo '>(GMT + 10:00 hours) Melbourne, Papua New Guinea, Sydney</option>
		<option value="10.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '10.5'){echo ' selected="selected"';} echo '>(GMT + 10:30 hours) Lord Howe Island</option>
		<option value="11"'; if($FailCheck == TRUE && $_POST['timeZone'] == '11'){echo ' selected="selected"';} echo '>(GMT + 11:00 hours) Magadan, New Caledonia, Solomon Is.</option>
		<option value="11.5"'; if($FailCheck == TRUE && $_POST['timeZone'] == '11.5'){echo ' selected="selected"';} echo '>(GMT + 11:30 hours) Burnt Pine, Kingston</option>
		<option value="12"'; if($FailCheck == TRUE && $_POST['timeZone'] == '12'){echo ' selected="selected"';} echo '>(GMT + 12:00 hours) Auckland, Fiji, Marshall Island</option>
		<option value="12.75"'; if($FailCheck == TRUE && $_POST['timeZone'] == '12.75'){echo ' selected="selected"';} echo '>(GMT + 12:45 hours) Chatham Islands</option>
		<option value="13"'; if($FailCheck == TRUE && $_POST['timeZone'] == '13'){echo ' selected="selected"';} echo '>(GMT + 13:00 hours) Kamchatka, Anadyr</option>
		<option value="14"'; if($FailCheck == TRUE && $_POST['timeZone'] == '14'){echo ' selected="selected"';} echo '>(GMT + 14:00 hours) Kiritimati</option>
		</select>
						  </td>
							<td align="left"><span id="msg_tz"></span>&nbsp;</td>
						</tr>          			
						<tr>
							<td align="right">Receive Administrator Notifications :</td>
							<td align="left">
								<select name="notifications" class="txtBox">
									<option value="1" '; if($FailCheck == TRUE && $_POST['notifications'] == '1'){echo ' selected="selected"';}else{echo ' selected="selected"';} echo '>Yes</option>
									<option value="0" '; if($FailCheck == TRUE && $_POST['notifications'] == '0'){echo ' selected="selected"';} echo '>No</option>
								</select>
							</td>
							<td align="left">&nbsp;</td>
						</tr>    
						<tr>
							<td align="right">Site PM Notifications :</td>
							<td align="left">
								<select name="sitepmnote" class="txtBox">
									<option value="1" '; if($FailCheck == TRUE && $_POST['sitepmnote'] == '1'){echo ' selected="selected"';}else{echo ' selected="selected"';} echo'>Yes</option>
									<option value="0" '; if($FailCheck == TRUE && $_POST['sitepmnote'] == '0'){echo ' selected="selected"';} echo'>No</option>
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
							<td align="right" valign="top">ReCaptcha* :</td>
							<td align="left"><div align="center">';
								require_once('recaptcha.php');
								echo recaptcha_get_html($publickey);
						  echo '<span style="color: red;" id="captchaStatus">&nbsp;</span></div></td>
							<td align="left"><span id="msg_recaptcha"></span>&nbsp;</td>
						</tr>   
						<tr>
							<td align="right">Agreement* :</td>
							<td align="left"><input name="agreement" type="checkbox" value="yes" id="agreement" /> by Submitting your registration you agree to AnimeFTW.tv\'s <a href="/tos" target="_blank">ToS</a> as well as our <a href="/rules" target="_blank">Rules</a></td>
							<td align="left"><span id="msg_agreement"></span>&nbsp;
							</td>
						</tr>
				   </table>                 			
			</div>
			</div>
	</form> 
	<div align="center">
	*= Is required to signup on AnimeFTW.tv, if you have any questions please use the <a href="/contact-us">Contact Us</a> or hop in the <a href="/chat">Chat</a> and leave us a message.</div>';
	}
	echo '</td></tr>
	</table>  ';
		
		echo "</td>\n";
		echo "</tr>\n</table>\n";
	
		// Start Main BG
		echo "</td>\n";
		echo "</tr>\n</table>\n";
		// End Main BG
			
	include('footer.php');
}
?>