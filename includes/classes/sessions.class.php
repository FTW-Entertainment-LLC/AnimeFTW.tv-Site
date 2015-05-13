<?php
/****************************************************************\
## FileName: sessions.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Version 2.0 of the sessions class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Sessions extends Config {
	
	var $UserArray;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function setUserSessionData($id,$username,$rememberme)
	{
		// We need to set the following cookies to ensure that we are secure in what we do.
		// cookies:
		//	au - id of the user
		//  hh - hash - shared secret, uid and mktime
		//  vd - hash based on the user agent (just the browser) and the username
		$randomkey = $this->generateRandomString(200);
		$vdkey = MD5($this->getOS() . $username . "dcb93b6e8d4fdcc8be4bc95e61ee1a28" . time());
		// Set the domain..
		$ThisDomain = ".animeftw.tv";
		if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com')
		{
			$ThisDomain = ".ftwdevs.com";
		}
		// set the sessions
		setcookie("au", $id, time() + (60*60*24*365), "/", $ThisDomain, 0, 1);
		setcookie("hh", $randomkey, time() + (60*60*24*365), "/", $ThisDomain, 0, 1);
		setcookie("vd", $vdkey, time() + (60*60*24*365), "/", $ThisDomain, 0, 1);
		// set the information in the database.
		$query = "INSERT INTO `" . $this->MainDB . "`.`user_session` (`id`, `added`, `updated`, `uid`, `agent`, `validate`) VALUES ('" . mysql_real_escape_string($vdkey) . "', '" . time() . "', '" . time() ."', '" . $id . "', '" . mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) . "', '" . mysql_real_escape_string($randomkey) . "')";
		
		$result = mysql_query($query);
		if(!$result)
		{
			echo 'Error processing the update ' . mysql_error();
			echo $query;
		}
	}
	
	public function checkUserSession()
	{
		// we need to check if the token and authentication are setup correctly.
		$query = "SELECT COUNT(id) as `count` FROM `" . $this->MainDB . "`.`user_session` WHERE `id` = '" . mysql_real_escape_string($_COOKIE['vd']) . "' AND `uid` = '" . mysql_real_escape_string($_COOKIE['au']) . "' AND `validate` = '" . mysql_real_escape_string($_COOKIE['hh']) . "'";
		$result = mysql_query($query);
		$count = mysql_result($result, 0);
		if($count > 0)
		{
			$query = "SELECT `Level_access`, `timeZone`, `Active`, `Username`, `canDownload`, `postBan`, `theme`, `forumBan`, `messageBan`, `viewNotifications`, `html5`, `ssl` FROM users WHERE ID='" . mysql_real_escape_string($_COOKIE['au']) . "'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$Logged = 1;
			$globalnonid = mysql_real_escape_string($_COOKIE['au']);
			$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . mysql_real_escape_string($_COOKIE['au']) . '\'';
			mysql_query($query) or die('Error : ' . mysql_error());
			$PermissionLevelAdvanced = $row['Level_access'];
			$timeZone = $row['timeZone'];
			$bannedornot = $row['Active'];
			$name = $row['Username'];
			$canDownload = $row['canDownload'];
			$postBan = $row['postBan'];
			$siteTheme = $row['theme'];
			$forumBan = $row['forumBan'];
			$messageBan = $row['messageBan'];
			$viewNotifications = $row['viewNotifications'];
			$html5 = $row['html5'];
			$ssl = $row['ssl'];
		}
		else
		{
			$Logged = 0;
			$PermissionLevelAdvanced = 0;
			$timeZone = '-6';
			$canDownload = 0;
			$siteTheme = 0;
			$postBan = 0;
			$name = '';
			$bannedornot = 0;
			$globalnonid = 0;
			$forumBan = 0;
			$messageBan = 0;
			$viewNotifications = 0;
			$html5 = 0;
			$ssl = 0;
		}
		return array($Logged,$globalnonid,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme,$forumBan,$messageBan,0,$viewNotifications,$html5,$ssl);
	}
	
	public function logoutOfSession()
	{
		// Set the domain..
		$ThisDomain = ".animeftw.tv";
		if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com')
		{
			$ThisDomain = ".ftwdevs.com";
		}
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
			setcookie("au", "", time() - (60*60*24*365), "/", $ThisDomain, 0, 1);
			setcookie("hh", "", time() - (60*60*24*365), "/", $ThisDomain, 0, 1);
			setcookie("vd", "", time() - (60*60*24*365), "/", $ThisDomain, 0, 1);
			
			// redirect them to the login page
			header("location: /login");
		}
	}
}