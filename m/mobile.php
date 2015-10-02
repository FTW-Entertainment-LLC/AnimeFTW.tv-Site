<?php
########################-~/\-#################-/\~-#######################
# file: mobile.php
# author: Brad Riemann
# Description: Mobile scripts used in the day-to-day mobile world.
# Comments are provided as-is cause the dev is lazy as hell
########################-~\/-#################-\/~-#######################

// We need to check to make sure your browser is mobile!
include('../includes/mobiledetection.php');
if(!$isMobile){
   echo '<h3>ERROR: This page is to be accessed by mobile browsers only!!!</h3>';
}
else {
include('includes/siteroot.php');
	//----------------------------------------
	// Mobile Registration
	// Basics in Registration Tech, Ho yeah!
	//----------------------------------------
	if($_GET['node'] == 'register'){
		if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){
			if($_POST['agreement'] == 'yes'){
				require_once('../includes/settings.php');
				$postUsername = $_POST['username'];
				$postUsername = substr($postUsername, 0, 20);
				if(($_POST['issubmit'] && $_POST['_submit_check']) == 1){
					if ( $_POST['username'] != '' && $_POST['password'] != '' && valid_email ( $_POST['email'] ) == TRUE ){
						if(!isset($_POST['agreement'])){
							$error = 'You did not agree with our ToS and our Rules. Please try again.';
							$FailCheck = TRUE;
						}
						else {
							if ( ! checkUnique ( 'Username', $_POST['username'] ) ){
								$error = 'Username already taken. Please try again!';
								$FailCheck = TRUE;
							}
							elseif ( ! checkUnique ( 'Email', $_POST['email'] ) ){
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
								$splitEmail = split("@",$_POST['email']);
								$finalEmailCheck = checkFakeEmail($splitEmail[1]);
								if($finalEmailCheck >0){
									$error = 'The email you have provided has been marked as spam, please try a different email!';
									$FailCheck = TRUE;
								}
								else {
									$query = $db->query ( "INSERT INTO users (`Username` , `Password`, `registrationDate`, `Email`, `Random_key`, `firstName`, `gender`, `ageDate`, `ageMonth`, `ageYear`, `staticip`, `timeZone`) VALUES (" . $db->qstr ( makeUrlFriendly("$postUsername") ) . ", " . $db->qstr ( md5 ( $_POST['password'] ) ).", '" . time () . "', " . $db->qstr ( $_POST['email'] ) . ", '" . random_string ( 'alnum', 32 ) . "', '" . $_POST['firstname'] . "', '" . $_POST['gender'] . "', '" . $_POST['ageDate'] . "', '" . $_POST['ageMonth'] . "', '" . $_POST['ageYear'] . "', '".$_SERVER['REMOTE_ADDR']."', '".$_POST['timeZone']."')" );
									$getUser = "SELECT ID, Username, Email, Random_key FROM users WHERE Username = " . $db->qstr ( makeUrlFriendly("$postUsername") ) . "";
									if ( $db->RecordCount ( $getUser ) == 1 ){			
										$row = $db->getRow ( $getUser );
										$subject = "Activation email from AnimeFTW.tv";
										$message = "Dear ".$row->Username.", this is your activation link to join our website at animeftw.tv. <br /><br /> In order to confirm your membership please click on the following link: <a href=\"http://www.animeftw.tv/confirm?ID=" . $row->ID . "&key=" . $row->Random_key . "\">http://www.animeftw.tv/confirm?ID=" . $row->ID . "&key=" . $row->Random_key . "</a> <br /><br />After you confirm your status with us, please go visit <a href=\"http://www.animeftw.tv/rules\">our Rules</a> and <a href=\"http://www.animeftw.tv/faq\">our FAQ</a> and become associated with the basics of the site, we try to keep order as best as we can so we have some rules in place.<br /><br />Thank you for joining, please go and visit our rules after you have logged in to familiarize yourself with our site policies! <a href=\"http://www.animeftw.tv/rules\">Found here</a><br /><br /> Regards,<br /><br />FTW Entertainment LLC & AnimeFTW Staff.";
										if ( send_email ( $subject, $row->Email, $message ) ) {
											$msg = 'Account registered as: '.$row->Username.'. Please check your email ('.$row->Email.') for details on how to activate it.';									
											$noReg = 'yes';
											
											// Insert default notification values into the database.
											$this->mysqli->query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $row->ID . "', " . time() . ", " . time() . ", '2', '4', '0');");
											$this->mysqli->query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $row->ID . "', " . time() . ", " . time() . ", '7', '14', '0');");
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
		$PageTitle = 'AnimeFTW.tv Mobile Registration | '.$siteroot.' | Your DivX Anime streaming Source!';
		include('../includes/m-header.php');
		echo '<div align="left">';
		echo "<span class='scapmain'>AnimeFTW.tv Registration</span><br /><br />\n";
		if ( isset ( $error ) )	{
			echo "<div><b>Registration Error: </b>".$error."</div>\n";
		}
		if ( isset ( $msg ) ){ 
			echo "<div><b>Registration Message: </b>".$msg."</div>\n";
		}
		else {'Oooops it didnt work try again >.>';}
		echo '
			<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">
				<input type="hidden" name="_submit_check" value="1" />
				<input type="hidden" name="issubmit" value="1">          
				<table width="340px">
				<tr>
					<td>
					<label for="username">Username:</label>
					</td>
					<td>
					<input name="username" id="username" type="text" style="width: 227px" '; if($FailCheck == TRUE){echo 'value="'.$_POST['username'].'"';} echo '/><br />
					</td>
				</tr>
				<tr>
					<td>
					<label for="email">Email:</label>
					</td>
					<td>
					<input name="email" id="email" type="text" style="width: 227px;" '; if($FailCheck == TRUE){echo 'value="'.$_POST['email'].'"';} echo ' />
					</td>
				</tr>
				<tr>
					<td>
					<label for="password">Password:</label>
					</td>
					<td>
					<input name="password" id="password" type="password" style="width: 227px;" '; if($FailCheck == TRUE){echo 'value="'.$_POST['password'].'"';} echo ' />
					</td>
				</tr>
	
				<tr>
				<td colspan="2">
				<div class="cb"></div>
					<div style="margin: 5px 0px 0px 70px;">
					<input name="agreement" type="checkbox" value="yes" id="agreement" /> by Submitting your registration you agree to AnimeFTW.tv\'s <a href="/tos" target="_blank">ToS</a> as well as our <a href="/rules" target="_blank">Rules</a>
					<input name="submit" type="submit" class="button_2" value="Register" /></div>
					</td>
				  </tr>
				 </table>
				 </form>
				 </div>';
		include('../includes/m-footer.php');
	} //end of the node=register

} //This is the end of the mobile check
?>