<?php
/****************************************************************\
## FileName: config.class.php									 
## Author: Brad Riemann										 
## Usage: Configuration Class and Functions
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com')
{
	$rootdirectory = $_SERVER['DOCUMENT_ROOT'];
}
else
{
	$rootdirectory = '/home/mainaftw/public_html';
}

include_once($rootdirectory . "/includes/config_site.php");

class Config {
	public $UserArray, $PermArray, $SettingsArray, $DefaultSettingsArray, $Host, $MainDB, $StatsDB, $RecentEps=array();

	public function __construct(){		
		// Declare the main database
		$this->MainDB = 'mainaftw_anime';
		if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com')
		{
			$this->MainDB = 'devadmin_anime'; // Main DB for everything else
		}
		
		$this->BuildUser(); // build our user array
			
		if($_SERVER['SERVER_PORT'] == 443)
		{
			$this->Host = 'https://d206m0dw9i4jjv.cloudfront.net';
		}
		else
		{
			$this->Host = 'http://img02.animeftw.tv';
			//$this->Host = 'http://d206m0dw9i4jjv.cloudfront.net';
		}
		
		// construct the site settings for the user, if they are logged in..
		$this->array_buildSiteSettings();
		
		// build the site default settings..
		$this->array_buildDefaultSiteSettings();
		
		// generate the list of recently viewed videos.
		$this->array_buildRecentlyWatchedEpisodes();
	}
	
	private function BuildUser()
	{	
		// we need to check if the token and authentication are setup correctly.
		$query = "SELECT COUNT(id) as `count` FROM `" . $this->MainDB . "`.`user_session` WHERE `id` = '" . mysql_real_escape_string($_COOKIE['vd']) . "' AND `uid` = '" . mysql_real_escape_string($_COOKIE['au']) . "' AND `validate` = '" . mysql_real_escape_string($_COOKIE['hh']) . "'";
		$result = mysql_query($query);
		$count = mysql_result($result, 0);
		if($count > 0)
		{
			$query = "UPDATE `" . $this->MainDB . "`.`user_session` SET `updated` = " . time() . " WHERE `id` = '" . mysql_real_escape_string($_COOKIE['vd']) . "' AND `uid` = '" . mysql_real_escape_string($_COOKIE['au']) . "' AND `validate` = '" . mysql_real_escape_string($_COOKIE['hh']) . "'";
			$result = mysql_query($query);
			unset($query);
			unset($result);
			$query = "SELECT `Level_access`, `timeZone`, `Active`, `Username`, `canDownload`, `postBan`, `theme`, `forumBan`, `messageBan`, `viewNotifications`, `html5`, `ssl`, `advanceActive`, `UploadsVisit` FROM users WHERE ID='" . mysql_real_escape_string($_COOKIE['au']) . "'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$Logged = 1;
			$UserID = mysql_real_escape_string($_COOKIE['au']);
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
			$AdvanceActive = $row['advanceActive'];
			$UploadsVisit = $row['UploadsVisit'];
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
			$UserID = 0;
			$forumBan = 0;
			$messageBan = 0;
			$viewNotifications = 0;
			$html5 = 0;
			$ssl = 0;
		}
		$array = array($Logged,$UserID,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme,$forumBan,$messageBan,0,$viewNotifications,$AdvanceActive,$UploadsVisit,$html5,$ssl);
		$this->UserArray = $array;
	}
	
	private function array_buildSiteSettings()
	{
		$this->SettingsArray = array();
		
		if($this->UserArray[0] == 1)
		{
			// the user is logged in, book em dan-o
			$query = "SELECT * FROM `user_setting` WHERE `uid` = " . $this->UserArray[1];
			$result = mysql_query($query);
			
			$count = mysql_num_rows($result);
			if($count > 0)
			{
				while($row = mysql_fetch_assoc($result))
				{
					$this->SettingsArray[$row['option_id']] = $row;
				}
			}
		}
		else
		{
		}
	}
	
	private function array_buildDefaultSiteSettings()
	{
		$this->DefaultSettingsArray = array();
		
		$query = "SELECT * FROM `user_setting_option`";
		$result = mysql_query($query);
			
		while($row = mysql_fetch_assoc($result))
		{
			$this->DefaultSettingsArray[$row['id']] = $row;
		}
	}
	
	public function ValidatePermission($permission)
	{		
		if(is_numeric($permission))
		{
			/*
			# OID of 1, means it is a Group request
			# OID of 2, means it is a single user Request
			*/
			$query = "SELECT deny FROM permissions_objects WHERE permission_id = " . $permission . " AND ((type = 1 AND oid = ".$this->UserArray[2].") OR (type = 2 AND oid = ".$this->UserArray[1]."))";
			$results = mysql_query($query);   
			$count = mysql_num_rows($results);
			if($count > 0)
			{    
                $Deny = 0;
                while($row = mysql_fetch_array($results))
                {
                    if($row['deny'] == 1)
                    {
                        $Deny = 1;
                    }
                }
                if($Deny == 1)
                {
                    // if it finds a 1 in the array, its because there is a deny somewhere..
                    return FALSE;
                }
                else
                {
                    // a deny option was not found in the system.. go ahead..
                    return TRUE;
                }
			}
			else 
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	// takes a query and a var and retunrs 
	public function SingleVarQuery($query,$var)
	{
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		return $row[$var];
	}
	
	// records the mod function right into the database.
	public function ModRecord($type)
	{
		mysql_query("INSERT INTO modlogs (uid, ip, agent, date, script, request_url) VALUES ('" . $this->UserArray[1] . "', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".time()."', '".$type."', '".mysql_real_escape_string($_SERVER['REQUEST_URI'])."')");
	}
	
	// we dont know what it does.. it just looks cool.
	public function Build($var1,$var2){
		$sarray = array( 
			'a' => '$2a$10$m5eebjaxijtnafbhqt863n$',
			'b' => '$2a$10$1rdche03z0y65yuirbx9j2$', 
			'c' => '$2a$10$w58kxl7rgj4h47rujjkgw2$', 
			'd' => '$2a$10$1mwo8ykqm89s4mgbq6eftg$', 
			'e' => '$2a$10$7opxsns435g60bitirv5g2$', 
			'f' => '$2a$10$i6qmrb5bd2j2y2evs8v4xr$',
			'g' => '$2a$10$tbzoqkirdj267u7lw6t64m$', 
			'h' => '$2a$10$dy40suy5eeg7rforo8b4bg$',
			'i' => '$2a$10$6fwfgsg30neqin81jzbs4a$', 
			'j' => '$2a$10$ac0y5ebdgt82v0hwzomdyr$', 
			'k' => '$2a$10$dn7xhvqunhv89wtxhfucpp$', 
			'l' => '$2a$10$2yaocsfe83lhva9hq132zp$', 
			'm' => '$2a$10$u2uxxmb0vujcd0w04dgyrv$', 
			'n' => '$2a$10$j7dh66ex6a2cu4v34jtdv7$', 
			'o' => '$2a$10$809qcxw7df2ror8355hwby$', 
			'p' => '$2a$10$wowii9akv7q5pee3eqtsiq$', 
			'q' => '$2a$10$aqhfns3hvo94hdsd6rd8xb$', 
			'r' => '$2a$10$chjsfo8w0k3pahal5jjukl$', 
			's' => '$2a$10$xhromb9gw55u84mew26iqm$', 
			't' => '$2a$10$zend8794gsmihxnvn4hr89$', 
			'u' => '$2a$10$83q8psnll2orz8gjibphqy$', 
			'v' => '$2a$10$8exwykcd97v3fbp26gqe3b$', 
			'w' => '$2a$10$cmueo47hk4rdpdozx6sb3r$', 
			'x' => '$2a$10$wqjavr92fq7kn1kh8tb27x$',
			'y' => '$2a$10$6rzgtbmuxpodbnfmgs3gk9$',
			'z' => '$2a$10$1kvphqm78zdqoeqmfuf6g3$',
			'0' => '$2a$10$xtjha3kw75l05y53kli9rc$', 
			'1' => '$2a$10$iloqaoeqpu4o47nmvv4cj6$',
			'2' => '$2a$10$ngiv7kq9nbro9xxqdwedup$',
			'3' => '$2a$10$5ikgle3duc5su9jk78j108$',
			'4' => '$2a$10$254b10z996dviqliffkng0$',
			'5' => '$2a$10$e04cbsiin8lwc8n20qw3id$',
			'6' => '$2a$10$u1vodloj7l2xtuy3c9hq4x$',
			'7' => '$2a$10$dypumh5ep81ndi3qkf41u2$',
			'8' => '$2a$10$bj939q6rjvgzfqzfct0tfq$',
			'9' => '$2a$10$yfy65x2fucihnce0722m9s$'
		);
		$var2 = substr(strtolower($var2), 0, 1);
		$final = crypt($var1, $sarray[$var2]);
		return $final;
	}
	
	//Paging function for the management pages, version two
	public function pagingV1($DivID,$count,$perpage,$start,$link)
	{
		$num = $count;
		$per_page = $perpage; // Number of items to show per page
		$showeachside = 4; //  Number of items to show either side of selected page
		if(empty($start)){$start = 0;}  // Current start position
		else{$start = $start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		
		// ADDED: 8/21/14 by Robotman321
		// Used to make the pages "nicer"
		if($max_pages == 1)
		{
			$front = "<span style=\"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;\">$max_pages Page</span>&nbsp;";
		}
		else
		{
			$front = "<span style=\"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;\">$max_pages Pages</span>&nbsp;";
		}
		
		if(($start-$per_page) >= 0)
		{
			$next = $start-$per_page;
			$startpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($next>0?("&page=").$next:"") . '\');return false;" style="padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;">&lt;</a>';
		}
		else 
		{
			$startpage = '';
		}
		if($start+$per_page<$num){
			$endpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.'&page='.max(0,$start+1) . '\');return false;" style="padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;">&gt;</a>';
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
			$style=($y==$start)?"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;font-weight:bold;":"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;";
			if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
			{
				$middlepage .= '<a style="'.$style.'" href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($y>0?("&page=").$y:"") . '\');return false;">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}
		
		echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}
	
	public function formatUsername($ID,$target = 'self',$lastActivity = NULL) 
	{
		$query = "SELECT `Username`, `display_name`, `Level_access`, `advancePreffix`, `advanceImage`, `Active` FROM `users` WHERE `ID`='" . mysql_real_escape_string($ID) . "'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_assoc($result);
		$Username = $row['Username']; 
		$display_name = $row['display_name']; 
		$Level_access = $row['Level_access'];
		$Active = $row['Active'];
		$advanceImage = $row['advanceImage'];
		$advancePreffix = $row['advancePreffix'];
		
		// Added 8/10/2014 - robotman321
		// If the user has a custom Display_name, we make that the primary username
		if($display_name != $Username && $display_name != NULL)
		{
			// The display name has been setup, lets use that
		}
		else
		{
			$display_name = $Username;
		}
		
		if($target == 'blank')
		{
			$linklocation = ' target="_blank"';
		}
		else
		{
			$linklocation = '';
		}
		if($Active == 1)
		{
			if($lastActivity != NULL)
			{
				$title = ' title="last click on ' . date("l, F jS, Y, h:i a",$lastActivity) . '"';
			}
			else
			{
				$title = '';
			}
			$link = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $Username . '"' . $title . '>';
			if($advancePreffix != NULL || $advancePreffix != '')
			{
				$spanbefore = '<span style="">';
				$spanafter = '</span>';
			}
			else
			{
				$spanbefore = '';
				$spanafter = '';
			}
			if($Level_access == 1)
			{
				$fixedUsername = $spanbefore . '<img src="/images/admin-icon.png" alt="Admin of AnimeFTW.tv" title="AnimeFTW.tv Administrator" style="vertical-align:middle;width:16px;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
			}
			else if($Level_access == 2)
			{
				$fixedUsername = $spanbefore . '<img src="/images/manager-icon.png" alt="Group manager of AnimeFTW.tv" title="AnimeFTW.tv Staff Manager" style="vertical-align:middle;width:16px;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
			}
			else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
			{
				// /images/staff-icon.png
				$fixedUsername = $spanbefore . '<img src="/images/staff-icon.png" alt="Staff Member of AnimeFTW.tv" title="AnimeFTW.tv Staff Member" style="vertical-align:middle;width:16px;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
			}
			else if($Level_access == 7)
			{
				$fixedUsername = '<img src="/images/advancedimages/'.$advanceImage.'.png" title="AnimeFTW.tv Advanced Member" alt="Advanced User Title" style="vertical-align:middle;" border="0" /><a href="/user/'.$Username.'">'.$display_name.'</a>';
			}
			else
			{
				$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"' . $linklocation . '>'.$display_name.'</a>';
			}
		}
		else {
			$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"' . $linklocation . '><s>'.$display_name.'</s></a>';
		}
		return $fixedUsername;
	}
	
	public function formatAvatar($ID,$target = 'self')
	{
		$query = "SELECT `ID`, `Username`, `avatarActivate`, `avatarExtension` FROM `users` WHERE `ID`='" . mysql_real_escape_string($ID) . "'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_assoc($result);
		if($row['avatarActivate'] == 'no')
		{
			$avatar = '<img src="' . $this->Host . '/avatars/default.gif" alt="avatar" height="50px" border="0" />';
		}
		else
		{
			$avatar = '<img src="' . $this->Host . '/avatars/user' . $row['ID'] . '.' . $row['avatarExtension'] . '" alt="User avatar" height="60px" border="0" />';
		}
		if($target == 'blank')
		{
			$linklocation = ' target="_blank"';
		}
		else
		{
			$linklocation = '';
		}
		$fixedAvatar = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $row['Username'] . '"' . $linklocation . '>' . $avatar . '</a>';
		return $fixedAvatar;
	}
	
	public function validateAPIUser($username,$password)
	{
		$query = "SELECT ID FROM `users` WHERE Username = '" . mysql_real_escape_string($username) . "' AND Password = '" . md5($password) . "'";
		$results = mysql_query($query);
		
		$count = mysql_num_rows($results);
		
		if($count > 0)
		{
			// we found a row
			$row = mysql_fetch_array($results);
			$returnArray = array(TRUE,$row['ID']);
		}
		else
		{
			$returnArray = array(FALSE,"0");
		}		
		return $returnArray;
	}
	
	private function array_buildRecentlyWatchedEpisodes()
	{
		// let's only load this when it's a video page..
		if($_SERVER['PHP_SELF'] == '/videos.php')
		{
			$query = "SELECT `eid`, `time`, `updated`, `max` FROM `episode_timer` WHERE `uid` = " . $this->UserArray[1];
			$result = mysql_query($query);
			
			if(!$result)
			{
				echo 'There was an issue with the communications.';
			}
			else
			{
				$count = mysql_num_rows($result);
				if($count > 0)
				{
					$i = 0;
					while($row = mysql_fetch_assoc($result))
					{
						$this->RecentEps[$row['eid']]['time'] = $row['time'];
						$this->RecentEps[$row['eid']]['updated'] = $row['updated'];
						$this->RecentEps[$row['eid']]['max'] = $row['max'];			
						$i++;
					}
				}
				else
				{
					$this->RecentEps[] = 0;
				}
			}
		}
	}
	public function uploadsEntrySelect($upload_id, $extra)
	{
		$query = "SELECT ID, series FROM uestatus ORDER BY series ASC";
		$results = mysql_query($query);
		
		if(!$results)
		{
			echo 'There was an error with the MySQL Query: ' . mysql_error();
			exit;
		}
		$Data = '<select '.$extra.'name="uploadsEntry" style="color: #000000;width:570px;" class="text-input"><option value="0"> Select an Entry </option>';
		while($row = mysql_fetch_assoc($results))
		{
			// make sure to check if it is numeric, if it is, we can push it to the actual good stuff
			if($upload_id == $row['ID'])
			{
				$Data .= '<option value="' . $row['ID'] . '" selected="selected">' . $row['series'] . '</option>';
			}
			else
			{
				$Data .= '<option value="' . $row['ID'] . '">' . $row['series'] . '</option>';
			}
		}
		$Data .= '</select>';
		return $Data;
	}
	public function buildCategories()
	{
		$query = "SELECT * FROM `categories`";
		$result = mysql_query($query);
		while($row = mysql_fetch_assoc($result))
		{
			$this->Categories[$row['id']]['id'] = $row['id'];
			$this->Categories[$row['id']]['name'] = $row['name'];
			$this->Categories[$row['id']]['description'] = $row['description'];
		}
	}
	
	public function generateRandomString($length = 10)
	{
		$randomString = substr(str_shuffle(MD5(microtime())), 0, $length);
		return $randomString;
	}
	
	public function getOS($agent)
	{
		$os_platform    =   "Unknown OS Platform";
		$os_array       =   array(
			'/windows nt 10/i'     	=>  'Windows 10',
			'/windows nt 6.3/i'     =>  'Windows 8.1',
			'/windows nt 6.2/i'     =>  'Windows 8',
			'/windows nt 6.1/i'     =>  'Windows 7',
			'/windows nt 6.0/i'     =>  'Windows Vista',
			'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     =>  'Windows XP',
			'/windows xp/i'         =>  'Windows XP',
			'/windows nt 5.0/i'     =>  'Windows 2000',
			'/windows me/i'         =>  'Windows ME',
			'/win98/i'              =>  'Windows 98',
			'/win95/i'              =>  'Windows 95',
			'/win16/i'              =>  'Windows 3.11',
			'/macintosh|mac os x/i' =>  'Mac OS X',
			'/mac_powerpc/i'        =>  'Mac OS 9',
			'/linux/i'              =>  'Linux',
			'/ubuntu/i'             =>  'Ubuntu',
			'/iphone/i'             =>  'iPhone',
			'/ipod/i'               =>  'iPod',
			'/ipad/i'               =>  'iPad',
			'/android/i'            =>  'Android',
			'/blackberry/i'         =>  'BlackBerry',
			'/webos/i'              =>  'Mobile'
		);

		foreach($os_array as $regex => $value)
		{
			if(preg_match($regex, $agent))
			{
				$os_platform    =   $value;
			}
		}

		return $os_platform;
	}

	public function getBrowser($agent)
	{
		$browser        =   "Unknown Browser";
		$browser_array  =   array(
			'/msie/i'       =>  'Internet Explorer',
			'/firefox/i'    =>  'Firefox',
			'/safari/i'     =>  'Safari',
			'/chrome/i'     =>  'Chrome',
			'/opera/i'      =>  'Opera',
			'/netscape/i'   =>  'Netscape',
			'/maxthon/i'    =>  'Maxthon',
			'/konqueror/i'  =>  'Konqueror',
			'/mobile/i'     =>  'Handheld Browser',
			'/palemoon/i'	=>	'Palemoon'
		);

		foreach($browser_array as $regex => $value)
		{
			if(preg_match($regex,  $agent))
			{
				$browser    =   $value;
			}
		}

		return $browser;
	}
}