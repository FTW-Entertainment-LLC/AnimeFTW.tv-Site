<?php
/****************************************************************\
## FileName: config.class.php									 
## Author: Brad Riemann
## Version: 5.0.0
## Usage: Configuration Class and Functions
## Copywrite 2011-2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/



class Config {
	
	public $UserArray, $PermArray;
	private $config;

	public function __construct($config)
	{
		include($_SERVER['DOCUMENT_ROOT'] . "/classes/db.class.php");
		$this->config = $config;
		$this->BuildUser(); // build our user array
		$this->BuildUserPermissions();
	}
	
	private function BuildUser()
	{
		//session_start();
	    if(isset($_COOKIE['cookie_id']) || (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == TRUE))
		{
			if(isset($_COOKIE['cookie_id']))
			{
				$UserID = $_COOKIE['cookie_id'];
			} 
			else if(isset($_SESSION['user_id']))
			{
				$UserID = $_SESSION['user_id']; 
			} 
		}
		else 
		{
			$UserID = NULL;
		}
		if($UserID != NULL)
		{
			$db = new db($this->config);
			$db->query("SELECT `Username`, `Password`, `viewNotifications`, `UploadsVisit`, `Email`, `Active`, `Level_access`, `canDownload`, `advanceActive`, `forumBan`, `messageBan`, `postBan`, `timeZone`, `theme`, `showChat` FROM users WHERE ID='" . mysql_real_escape_string($UserID) . "'");
			$row = $db->get();
			if(isset($_COOKIE['authenticate']) && $_COOKIE['authenticate'] == md5($_SERVER['REMOTE_ADDR'] . $row['Password'] . $_SERVER['HTTP_USER_AGENT'])) 
			{
				//they clear the authentication process...
				$Logged = 1;
				$db->query('UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $UserID . '\'');
				$PermissionLevelAdvanced = $row['Level_access'];
				$timeZone = $row['timeZone'];
				$bannedornot = $row['Active'];
				$name = $row['Username'];
				$canDownload = $row['canDownload'];
				$postBan = $row['postBan'];
				$siteTheme = $row['theme'];
				$forumBan = $row['forumBan'];
				$messageBan = $row['messageBan'];
				$showChat = $row['showChat'];
				$viewNotifications = $row['viewNotifications'];
				$AdvanceActive = $row['advanceActive'];
				$UploadsVisit = $row['UploadsVisit'];
			}
			else 
			{
				if(isset($_SESSION['user_id']))
				{
					$Logged = 1;
					$PermissionLevelAdvanced = $row['Level_access'];
					$db->query('UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $UserID . '\'');
					$timeZone = $row['timeZone'];
					$bannedornot = $row['Active'];
					$name = $row['Username'];
					$canDownload = $row['canDownload'];
					$postBan = $row['postBan'];
					$siteTheme = $row['theme'];
					$forumBan = $row['forumBan'];
					$messageBan = $row['messageBan'];
					$showChat = $row['showChat'];
					$viewNotifications = $row['viewNotifications'];
					$AdvanceActive = $row['advanceActive'];
					$UploadsVisit = $row['UploadsVisit'];
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
					$UserID = 0;
					$forumBan = 0;
					$messageBan = 0;
					$showChat = 0;
					$viewNotifications = 0;
					$AdvanceActive = 0;
					$UploadsVisit = 0;
				}
			}
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
			$UserID = 0;
			$forumBan = 0;
			$messageBan = 0;
			$showChat = 0;
			$viewNotifications = 0;
			$AdvanceActive = 0;
			$UploadsVisit = 0;
		}
		$array = array($Logged,$UserID,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme,$forumBan,$messageBan,$showChat,$viewNotifications,$AdvanceActive,$UploadsVisit);
		$this->UserArray = $array;
	}
	
	private function BuildUserPermissions()
	{
		$db = new db($this->config);
		$db->query("SELECT permission_id FROM permissions_objects WHERE (type = 1 AND oid = ".$this->UserArray[2].") OR (type = 2 AND oid = ".$this->UserArray[1].")");
		$array = array();
		while($row = $db->get())
		{
			$array[] = $row['permission_id'];
		}
		$this->PermArray = $array;
	}
	
	public function ValidatePermission($permission)
	{
		if(in_array($permission,$this->PermArray))
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	}
	
	// takes a query and a var and retunrs 
	public function SingleVarQuery($query,$var)
	{
		$db = new db($this->config);
		$db->query($query);
		return $db->get($var);
	}
}