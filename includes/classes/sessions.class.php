<?php
/****************************************************************\
## FileName: sessions.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Version 2.0 of the sessions class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Sessions extends Config {
	
	var $UserArray, $ThisDomain;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	public function setUserSessionData($id,$username,$rememberme)
	{
		// We need to set the following cookies to ensure that we are secure in what we do.
		// cookies:
		//	au - id of the user
		//  hh - hash - shared secret, uid and mktime
		//  vd - hash based on the user agent (just the browser) and the username
		$randomkey = $this->generateRandomString(200);
		$vdkey = MD5($this->getOS($_SERVER['HTTP_USER_AGENT']) . $username . "dcb93b6e8d4fdcc8be4bc95e61ee1a28" . time());
		// set the sessions
		setcookie("au", $id, time() + (60*60*24*365), "/", $this->ThisDomain, 0, 1);
		setcookie("hh", $randomkey, time() + (60*60*24*365), "/", $this->ThisDomain, 0, 1);
		setcookie("vd", $vdkey, time() + (60*60*24*365), "/", $this->ThisDomain, 0, 1);
		// set the information in the database.
		$query = "INSERT INTO `" . $this->MainDB . "`.`user_session` (`id`, `added`, `updated`, `uid`, `agent`, `validate`, `ip`) VALUES ('" . mysql_real_escape_string($vdkey) . "', '" . time() . "', '" . time() ."', '" . $id . "', '" . mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) . "', '" . mysql_real_escape_string($randomkey) . "', '" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "')";
		
		$result = mysql_query($query);
		if(!$result)
		{
			echo 'Error processing the update ' . mysql_error();
			echo $query;
		}
	}
	
	public function checkUserSession()
	{
		// by default the system checks the user for validation at config run time..
		// Since this class never gets fired without config, it is redundant to have a full check here.
		return $this->UserArray;
	}
	
	public function logoutOfSession()
	{
		// remove it from the database.
		$query = "DELETE FROM `" . $this->MainDB . "`.`user_session` WHERE `id` = '" . mysql_real_escape_string($_COOKIE['vd']) . "' AND `uid` = '" . $this->UserArray[1] . "' AND `validate` = '" . mysql_real_escape_string($_COOKIE['hh']) . "'";
		
		$result = mysql_query($query);
		if(!$result)
		{
			echo 'Error processing the update ' . mysql_error();
			echo $query;
		}
		else
		{
			// set the sessions
			setcookie("au", "", time() - (60*60*24*365), "/", $this->ThisDomain, 0, 1);
			setcookie("hh", "", time() - (60*60*24*365), "/", $this->ThisDomain, 0, 1);
			setcookie("vd", "", time() - (60*60*24*365), "/", $this->ThisDomain, 0, 1);
			
			// redirect them to the login page
			header("location: /login");
		}
	}
	
	public function removeSession($Type=0,$allSessions = false)
	{
		if(($this->UserArray[2] == 1 || $this->UserArray[2] == 2) || $this->UserArray[1] == $_GET['uid'])
		{
			if($Type == 0)
			{
				// desktop session
				if($allSessions != false)
				{
					// We will want to remove ALL sessions of the requested.
					$query = "DELETE FROM `" . $this->MainDB . "`.`user_session` WHERE `uid` = " . mysql_real_escape_string($_GET['uid']);
					
					if($_GET['uid'] == $this->UserArray[1])
					{
						// if the user id is the same as the logged in user, we want to ensure that the existing session is not logged out.
						$queryAddon = " AND `id` != " . mysql_real_escape_string($_COOKIE['vd']);
					}
					else
					{
						// nope, give em hell and banish all of the sessions.
						$queryAddon = "";
					}
				}
				else
				{
					// only one session.
					$query = "DELETE FROM `" . $this->MainDB . "`.`user_session` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'";
				}
			}
			else
			{
				// api sessions
				if($allSessions != false)
				{
					// We will want to remove ALL sessions of the requested, since this is not a current session thing, we can remove all, all the time.
					$query = "DELETE FROM `" . $this->MainDB . "`.`developers_api_sessions` WHERE `uid` = " . mysql_real_escape_string($_GET['uid']);
				}
				else
				{
					// only one session.
					$query = "DELETE FROM `" . $this->MainDB . "`.`developers_api_sessions` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'";
				}
			}
			
			// result
			$result = mysql_query($query);
			if(!$result)
			{
				echo 'Error in executing the Query.';
				exit;
			}
			echo 'Success';
		}
		else
		{
			echo 'PERMISSION DENIED.';
		}
	}
	
	public function sendEmailToUser($email)
	{
		$bcc = '';
		// let's send an email to let them know of the new session.
		ini_set('sendmail_from', 'no-reply@animeftw.tv');
		$headers = 'From: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'Reply-To: AnimeFTW.tv <support@animeftw.tv>' . "\r\n" .
			$bcc .
			'X-Mailer: PHP/' . phpversion();
			
		$body = "== This is an automated message! ==\n\n";
		$body .= "Your account was logged in to a new session at AnimeFTW.tv, the details are as follow.\n\n";
		$body .= "Date: " . date("r") . "\n";
		$body .= "Browser: " . $this->getBrowser($_SERVER['HTTP_USER_AGENT']) . "\n";
		$body .= "Operating System: " . $this->getOS($_SERVER['HTTP_USER_AGENT']) . "\n";
		$body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
		$body .= "If you believe that this session is invalid, please log in to your AnimeFTW.tv account. Navigate to your profile, edit your settings and view the `Session` tab to remove this session.\n\n";
		$body .= "- Your friends at AnimeFTW.tv.";
		
		//mail($email,"New session at AnimeFTW.tv!", $body, $headers);
		//mysql_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', 'New Session at AnimeFTW.tv.');");
	}
}