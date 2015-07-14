<?php
/****************************************************************\
## FileName: register.class.php		 
## Author: Brad Riemann			 
## Usage: Registration Class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Register extends Config {
	
	public $OutputArray = array();
	public function __construct()
	{
		parent::__construct();
	}
	
	public function registerAccount()
	{
		$OutputArray['noReg'] = 'no';
		if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){
			if($_POST['agreement'] == 'yes'){
				$postUsername = $_POST['username'];
				$postUsername = substr($postUsername, 0, 20);
				if(($_POST['issubmit'] && $_POST['_submit_check']) == 1){
					if ( $_POST['username'] != '' && $_POST['password'] != '' && ($_POST['password'] == $_POST['cpassword']) && ($_POST['email'] == $_POST['cemail']) && $this->valid_email($_POST['email']) == TRUE )
					{
						if(!isset($_POST['agreement']))
						{
							$OutputArray['error'] = 'You did not agree with our ToS and our Rules. Please try again.';
							$OutputArray['Failcheck'] = TRUE;
						}
						else
						{
							if(!$this->checkUnique('Username', $_POST['username']))
							{
								$OutputArray['error'] = 'Username already taken. Please try again!';
								$OutputArray['FailCheck'] = TRUE;
							}
							else if(!$this->checkUnique('display_name', $_POST['username']))
							{
								$OutputArray['error'] = 'Username already taken. Please try again!';
								$OutputArray['FailCheck'] = TRUE;
							}
							else if (!$this->checkUnique ( 'Email', $_POST['email'] ) )
							{
								$OutputArray['error'] = 'The email you used is associated with another user. Please try again or use the "forgot password" feature!';
								$OutputArray['FailCheck'] = TRUE;
							}
							else {
								if($_POST['google'] != '9'){
									$OutputArray['error'] = 'You have failed the bot check, please try again.';
									$OutputArray['FailCheck'] = TRUE;
								}
								else {
									$splitEmail = explode("@",$_POST['email']);
									$finalEmailCheck = $this->checkFakeEmail($splitEmail[1]);
									if($finalEmailCheck >0){
										$OutputArray['error'] = 'The email you have provided has been marked as spam, please try a different email!';
										$OutputArray['FailCheck'] = TRUE;
									}
									else
									{
										$query = "INSERT INTO users (`Username`, `display_name`, `Password`, `registrationDate`, `Email`, `Random_key`, `firstName`, `gender`, `ageDate`, `ageMonth`, `ageYear`, `staticip`, `timeZone`) VALUES (" . $this->sanitizeInput ( $this->makeUrlFriendly("$postUsername") ) . ", " . $this->sanitizeInput ( $this->makeUrlFriendly("$postUsername") ) . ", " . $this->sanitizeInput ( md5 ( $_POST['password'] ) ).", '" . time () . "', " . $this->sanitizeInput($_POST['email'] ) . ", '" . $this->generateRandomString(32) . "', " . $this->sanitizeInput(@$_POST['firstname']) . ", " . $this->sanitizeInput(@$_POST['gender']) . ", " . $this->sanitizeInput(@$_POST['ageDate']) . ", " . $this->sanitizeInput(@$_POST['ageMonth']) . ", " . $this->sanitizeInput(@$_POST['ageYear']) . ", '".$_SERVER['REMOTE_ADDR']."', ".$this->sanitizeInput($_POST['timeZone']).")";
										$result = mysql_query($query);
										$getUser = "SELECT `ID`, `Username`, `Email`, `Random_key` FROM `users` WHERE `Username` = " . $this->sanitizeInput($this->makeUrlFriendly("$postUsername") ) . "";
										$results = mysql_query($getUser);
										if(mysql_num_rows($results) == 1)
										{
											$row = mysql_fetch_assoc($results);
											$message = "Dear ".$row['Username'].", this is your activation link to join our website at animeftw.tv. \n\nIn order to confirm your membership please click on the following link: https://www.animeftw.tv/confirm?ID=" . $row['ID'] . "&key=" . $row['Random_key'] . "\n\nAfter you confirm your status with us, please go visit https://www.animeftw.tv/rules - our Rules and https://www.animeftw.tv/faq - our FAQ and become associated with the basics of the site, we try to keep order as best as we can so we have some rules in place. \n\nThank you for joining, please go and visit our rules after you have logged in to familiarize yourself with our site policies! https://www.animeftw.tv/rules - Found here \n\nRegards, \n\nFTW Entertainment LLC & AnimeFTW Staff.";
											
											// First check to see if they want to recieve notifications from site pms
											if(isset($_POST['sitepmnote']) && $_POST['sitepmnote'] == 1)
											{
												// they opted for the default, nothing needed
											}
											else
											{
												// they don't want to receive our emails :(
												$suplementalquery = mysql_query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $row->ID . "', " . time() . ", " . time() . ", '2', '4', '0');");
											}
											// check to see if they want to receive admin emails
											if(isset($_POST['notifications']) && $_POST['notifications'] == 1)
											{
												// nope, they want us to email them!
											}
											else
											{
												// sandpanda..
												$suplementalquery = mysql_query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $row->ID . "', " . time() . ", " . time() . ", '7', '14', '0');");
											}
											include_once("email.class.php");
											$Email = new Email($row['Email']);
											//6,$vars);
											if($Email->Send(11,$message))
											{
												$OutputArray['error'] = 'Account registered as: '.$row['Username'].'. Please check your email ('.$row['Email'].') for details on how to activate it.';
												$OutputArray['noReg'] = 'yes';
												$OutputArray['FailCheck'] = FALSE;
											}
											else
											{
												$OutputArray['error'] = 'I managed to register your membership but failed to send the validation email. Please contact the admin at support@animeftw.tv';
												$OutputArray['FailCheck'] = FALSE;
											}
										}
										else {
											$OutputArray['error'] = 'There was an issue registering your account.<br />'  . $query;
											$OutputArray['FailCheck'] = TRUE;
										}
									}
								}
							}
						}
					}
					else {		
						$OutputArray['error'] = 'There was an error in your data. Please make sure you filled in all the required data, you provided a valid email address and that the password fields match one another.';
						$OutputArray['FailCheck'] = TRUE;	
					}
				}
				else {
					$OutputArray['error'] = 'error: You did not submit from AnimeFTW.tv!';
					$OutputArray['FailCheck'] = TRUE;	
					// $msg = TRUE;
					# in a real application, you should send an email, create an account, etc
				}
			}
			else {
				$OutputArray['error'] = 'You failed to agree to our Terms of Service and our Rules, please evaluate your registration.';
				$OutputArray['FailCheck'] = TRUE;	
			}
			return $OutputArray;
		}
	}
	
	public function confirmAccount()
	{
		$OutputArray = array('error' => NULL, 'msg' => NULL);
		if ( $_GET['ID'] != '' && is_numeric ( $_GET['ID'] ) == TRUE && strlen ( $_GET['key'] ) == 32 && ctype_alnum( $_GET['key'] ) == TRUE ) {
			$query = "SELECT ID, Password, Random_key, Active FROM `users` WHERE ID = " . $this->sanitizeInput($_GET['ID']);
			$result = mysql_query($query);
			
			if(mysql_num_rows($result) == 1)
			{
				$row = mysql_fetch_assoc($result);
				if( $row['Active'] == 1 ) {
					$OutputArray['error'] = 'This member is already active ! Please login to your account.';
				}
				elseif($row['Random_key'] != $_GET['key'] ) {
					$OutputArray['error'] = 'The confirmation key that was generated for this member does not match with the one entered !';
				}
				else {
					$update = mysql_query("UPDATE `users` SET Active = 1 WHERE ID=" . $this->sanitizeInput($row['ID']));
					include_once('sessions.class.php');
					$Session = new Sessions();
					$Session->setUserSessionData($row['ID'],$row['Username'],TRUE);
					header("Location: /");
				}
			}
			else {		
				$OutputArray['error'] = 'User not found !';		
			}
		}
		else {
			$OutputArray['error'] = 'Invalid data provided !';
		}
		return $OutputArray;
	}
	
	private function checkUnique($field,$compared)
	{
		$query = mysql_query("SELECT COUNT(*) as total FROM `users` WHERE " . $field . " = " . $this->sanitizeInput($compared));
		
		$query = mysql_fetch_assoc($query);
		
		if ( $query->total == 0 ) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	private function makeUrlFriendly($postUsername) {
		// Replace spaces with underscores
		$output = preg_replace("/\s/e" , "_" , $postUsername);
		// Remove non-word characters
		$output = preg_replace("/\W/e" , "" , $output);
		return strtolower($output);
	}
	private function checkFakeEmail($subject) {
		$fakes = array("10minutemail.com","20minutemail.com","anonymbox.com","beefmilk.com","bsnow.net","bugmenot.com","deadaddress.com","despam.it","disposeamail.com","dodgeit.com","dodgit.com","dontreg.com","e4ward.com","emailias.com","emailwarden.com","enterto.com","gishpuppy.com","goemailgo.com","greensloth.com","guerrillamail.com","guerrillamailblock.com","hidzz.com","incognitomail.net","jetable.org","kasmail.com","lifebyfood.com","lookugly.com","mailcatch.com","maileater.com","mailexpire.com","mailin8r.com","mailinator.com","mailinator.net","mailinator2.com","mailmoat.com","mailnull.com","meltmail.com","mintemail.com","mt2009.com","myspamless.com","mytempemail.com","mytrashmail.com","netmails.net","odaymail.com","pookmail.com","shieldedmail.com","smellfear.com","sneakemail.com","sogetthis.com","soodonims.com","spam.la","spamavert.com","spambox.us","spamcero.com","spamex.com","spamfree24.com","spamfree24.de","spamfree24.eu","spamfree24.info","spamfree24.net","spamfree24.org","spamgourmet.com","spamherelots.com","spamhole.com","spaml.com","spammotel.com","spamobox.com","spamspot.com","tempemail.net","tempinbox.com","tempomail.fr","temporaryinbox.com","tempymail.com","thisisnotmyrealemail.com","trash2009.com","trashmail.net","trashymail.com","tyldd.com","yopmail.com","zoemail.com","tradermail.info","zippymail.info","suremail.info","safetymail.info","binkmail.com","tradermail.info","zippymail.info","suremail.info","safetymail.info","PutThisInYourSpamDatabase.com","SpamHerePlease.com","SendSpamHere.com","chogmail.com","SpamThisPlease.com","frapmail.com","obobbo.com","devnullmail.com","bobmail.info","slopsbox.com");
		
		$num_bots=0;
		foreach($fakes as $num=>$fakes){
		preg_match("/".$fakes."/i", $subject, $matches);
			if(count($matches) >0){
			$num_bots++;
			}
			else{
			}
		}
		return $num_bots;
	}
	
	private function sanitizeInput($string, $magic_quotes = false)
	{
		if (!$magic_quotes) {
			if (strnatcmp(PHP_VERSION, '4.3.0') >= 0) {
				return "'" . mysql_real_escape_string($string) . "'";
			}
			$string = str_replace("'", "\\'" , str_replace('\\', '\\\\', str_replace("\0", "\\\0", $string)));
			return  "'" . $string . "'"; 
		}
		return "'" . str_replace('\\"', '"', $string) . "'";
	}
	
	private function valid_email ( $str )
	{
		return ( ! preg_match ( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str ) ) ? FALSE : TRUE;
	}
}