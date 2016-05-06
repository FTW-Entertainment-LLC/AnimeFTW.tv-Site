<?php
/****************************************************************\
## FileName: processData.class.php									 
## Author: Brad Riemann										 
## Edits by: Hani Mayahi
## Usage: All POST and GET Data is thrown to this class
## Copywrite 2011-2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class processData extends Config {

	var $UserArray;

	//#- Public Functions -#\\
	public function __construct($UserArray)
	{
		$this->UserArray = $UserArray;
		parent::__construct();
		parent::outputUserInformation();
		include("../includes/classes/anidb.class.php");
		$this->processPostedData();
	}
	
	private function processPostedData()
	{
		if($_POST['method'] == 'SettingsSubmit' && $this->ValidatePermission(61) == TRUE)
		{
			$this->uid = $_POST['uid'];
			$this->ModRecord('Update Site Settings');
			//begin not so complex-complex operations
			foreach($_POST as $name => $value) 
			{
				//first we need to check to loop through variables..
				if(is_array($value))
				{
					$permissionsid = substr($name, 4);
					$query = "SELECT groupID FROM site_groups";
					$results = mysql_query($query);
					$i = 0;
					// we want to query the database, to get a full list of groups, that way we can delete objects where need be..
					while($row = mysql_fetch_array($results))
					{
						$query = mysql_query("SELECT id FROM permissions_objects WHERE permission_id = ".$permissionsid." AND type = 1 AND oid = ".$row[0]);
						$count = mysql_num_rows($query);
						if(in_array($row[0],$value))
						{ 
							//if the presented valiable is IN the array, that means it needs to be added
							if($count == 1)
							{ 
								//we have found the package, nothing needed.
							}
							else 
							{ //for everything else you need to add it.
								mysql_query("INSERT INTO permissions_objects (id, oid, type, permission_id) VALUES (NULL, '".mysql_real_escape_string($row[0])."', '1', '".mysql_real_escape_string($permissionsid)."')") or die(mysql_error());
							}
						}
						else 
						{ //its not in the array, it can be deleted
							if($count == 1)
							{ // the package was found in the DB, but we dont need it, so it can be deleted
								mysql_query("DELETE FROM permissions_objects WHERE permission_id = ".$permissionsid." AND type = 1 AND oid = ".$row[0]);
							}
							else 
							{ 
								// it's not in the database anyway, go home..
							}
						}
					}
				}
				else {
					// it's not an array so lets forget about it.
				}
			}
			echo 'Success';
		}
		else if($_POST['method'] == 'EditSeries' && $this->ValidatePermission(23) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$sid = mysql_real_escape_string($_POST['sid']);
				$seriesName = mysql_real_escape_string($_POST['seriesName']);
				$fullSeriesName = mysql_real_escape_string($_POST['fullSeriesName']);
				$kanji = mysql_real_escape_string($_POST['kanji']);
				$romaji = mysql_real_escape_string($_POST['romaji']);
				$synonym = mysql_real_escape_string($_POST['synonym']);
				$seoname = mysql_real_escape_string($_POST['seoname']);
				$videoServer = mysql_real_escape_string($_POST['videoServer']);
				$active = mysql_real_escape_string($_POST['active']);
				$description = mysql_real_escape_string($_POST['description2']);
				$ratingLink = mysql_real_escape_string($_POST['ratingLink']);
				$stillRelease = mysql_real_escape_string($_POST['stillRelease']);
				$Movies = mysql_real_escape_string($_POST['Movies']);
				$moviesOnly = mysql_real_escape_string($_POST['moviesOnly']);
				$OVA = mysql_real_escape_string($_POST['OVA']);
				$noteReason = mysql_real_escape_string($_POST['noteReason']);
				$aonly = mysql_real_escape_string($_POST['aonly']);
				$prequelto = mysql_real_escape_string($_POST['prequelto']);
				$sequelto = mysql_real_escape_string($_POST['sequelto']);
				$hd = mysql_real_escape_string($_POST['hd']);
				$license = mysql_real_escape_string($_POST['license']);
				
				// ADDED 10/11/2014 by robotman321
				// will count the amount of categories to make sure everything is added right.
				$category = ' , ';
				$i = 0;
				$count = count($_POST['category']);
				foreach($_POST['category'] as $Category)
				{
					$category .= $Category;
					if($i < $count)
					{
						$category .= ' , ';
					}
					$i++;
				}
				$seriesType = mysql_real_escape_string($_POST['seriesType']);
				$seriesList = mysql_real_escape_string($_POST['seriesList']);
				$ueid = mysql_real_escape_string($_POST['uploadsEntry']);
											
				$fullSeriesName = htmlspecialchars($fullSeriesName);
				$kanji = htmlspecialchars($kanji);
				$description = nl2br($description);
				$noteReason = nl2br($noteReason);
				//echo $description;
				mysql_query("SET NAMES 'utf8'"); 
				$query = 'UPDATE series
					SET seriesName=\'' . mysql_real_escape_string($seriesName) .'\', 
					fullSeriesName=\'' . $fullSeriesName . '\', 
					romaji=\'' . mysql_real_escape_string($romaji) . '\', 
					kanji=\'' . mysql_real_escape_string($kanji) . '\',
					synonym=\'' . $synonym . '\',
					seoname=\'' . mysql_real_escape_string($seoname) . '\', 
					videoServer=\'' . mysql_real_escape_string($videoServer) . '\', 
					active=\'' . mysql_real_escape_string($active) . '\', 
					description=\'' . $description . '\', 
					ratingLink=\'' . mysql_real_escape_string($ratingLink) . '\', 
					stillRelease=\'' . mysql_real_escape_string($stillRelease) . '\', 
					Movies=\'' . mysql_real_escape_string($Movies) . '\', 
					moviesOnly=\'' . mysql_real_escape_string($moviesOnly) . '\', 
					OVA=\'' . mysql_real_escape_string($OVA) . '\', 
					noteReason=\'' . $noteReason . '\', 
					aonly=\'' . mysql_real_escape_string($aonly) . '\', 
					prequelto=\'' . mysql_real_escape_string($prequelto) . '\', 
					sequelto=\'' . mysql_real_escape_string($sequelto) . '\', 
					category=\'' . $category . '\', 
					seriesType=\'' . mysql_real_escape_string($seriesType) . '\', 
					seriesList=\'' . mysql_real_escape_string($seriesList) . '\',
					hd=\'' . mysql_real_escape_string($hd) . '\',
					ueid=\'' . $ueid . '\',
					`license`=\'' . $license . '\'
					WHERE id=' . $sid . '';
				mysql_query($query) or die('Error : ' . mysql_error());	
				$euidd = mysql_query("UPDATE uestatus SET sid = $sid WHERE ID = $ueid");
				$this->updatePreSequel($sid, $prequelto, $sequelto);
					
				$this->ModRecord('Edit series, ' . $fullSeriesName);
				echo 'Success';
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'AddSeries' && $this->ValidatePermission(22) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$seriesName = mysql_real_escape_string($_POST['seriesName']);
				$fullSeriesName = mysql_real_escape_string($_POST['fullSeriesName']);
				$kanji = mysql_real_escape_string($_POST['kanji']);
				$romaji = mysql_real_escape_string($_POST['romaji']);
				$synonym = mysql_real_escape_string($_POST['synonym']);
				$seoname = mysql_real_escape_string($_POST['seoname']);
				$videoServer = mysql_real_escape_string($_POST['videoServer']);
				$active = mysql_real_escape_string($_POST['active']);
				$description = mysql_real_escape_string($_POST['description2']);
				$ratingLink = mysql_real_escape_string($_POST['ratingLink']);
				$stillRelease = mysql_real_escape_string($_POST['stillRelease']);
				$Movies = mysql_real_escape_string($_POST['Movies']);
				$moviesOnly = mysql_real_escape_string($_POST['moviesOnly']);
				$OVA = mysql_real_escape_string($_POST['OVA']);
				$noteReason = mysql_real_escape_string($_POST['noteReason']);
				$aonly = mysql_real_escape_string($_POST['aonly']);
				$prequelto = mysql_real_escape_string($_POST['prequelto']);
				$sequelto = mysql_real_escape_string($_POST['sequelto']);
				$hd = mysql_real_escape_string($_POST['hd']);
				$license = mysql_real_escape_string($_POST['license']);
				
				// ADDED 10/11/2014 by robotman321
				// will count the amount of categories to make sure everything is added right.
				$category = ' , ';
				$i = 0;
				$count = count($_POST['category']);
				foreach($_POST['category'] as $Category)
				{
					$category .= $Category;
					if($i < $count)
					{
						$category .= ' , ';
					}
					$i++;
				}
				$seriesType = mysql_real_escape_string($_POST['seriesType']);
				$seriesList = mysql_real_escape_string($_POST['seriesList']);
				$ueid = mysql_real_escape_string($_POST['uploadsEntry']);
							
				mysql_query("SET NAMES 'utf8'");
				$query = "INSERT INTO series (`seriesName`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `seoname`, `videoServer`, `active`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesOnly`, `OVA`, `noteReason`, `aonly`, `prequelto`, `sequelto`, `category`, `seriesType`, `seriesList`, `ueid`, `hd`, `license`) VALUES ('$seriesName', '$fullSeriesName', '$romaji', '$kanji', '$synonym', '$seoname', '$videoServer', '$active', '$description', '$ratingLink', '$stillRelease', '$Movies', '$moviesOnly', '$OVA', '$noteReason', '$aonly', '$prequelto', '$sequelto', '$category', '$seriesType', '$seriesList', '$ueid', '$hd', '$license')";
				mysql_query($query) or die('There was an error adding the series: ' . mysql_error() . ', here was the query: ' . $query);
				
				$sid = $this->SingleVarQuery("SELECT id FROM series WHERE seriesName = '" . $seriesName . "'",'id'); //Get the Series ID through seriesName
				$euidd = mysql_query("UPDATE uestatus SET sid = $sid WHERE ID = $ueid");
				$this->updatePreSequel($sid, $prequelto, $sequelto);
				$this->ModRecord('Add Series, ' . $FullSeriesName);
				//Send an email to mimby cause she wants to be in teh know
				include("/home/mainaftw/public_html/includes/classes/email.class.php");
				$Email = new Email('mimby@animeftw.tv, robotman321@animeftw.tv','support@animeftw.tv');
				$Email->Send(8,$_POST);
				echo 'Success';
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'MassEpisodeUpdate' && $this->ValidatePermission(72) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$sid = mysql_real_escape_string($_POST['sid']);
				$fullSeriesName = mysql_real_escape_string($_POST['fullSeriesName']);
				$old_vidwidth = mysql_real_escape_string($_POST['old_vidwidth']);
				$old_vidheight = mysql_real_escape_string($_POST['old_vidheight']);
				$old_epprefix = mysql_real_escape_string($_POST['old_epprefix']);
				$old_subGroup = mysql_real_escape_string($_POST['old_subGroup']);
				$old_videotype = mysql_real_escape_string($_POST['old_videotype']);
				$old_hd = mysql_real_escape_string($_POST['old_hd']);
				$vidwidth = mysql_real_escape_string($_POST['vidwidth']);
				$vidheight = mysql_real_escape_string($_POST['vidheight']);
				$epprefix = mysql_real_escape_string($_POST['epprefix']);
				$subGroup = mysql_real_escape_string($_POST['subGroup']);
				$videotype = mysql_real_escape_string($_POST['videotype']);
				$hd = mysql_real_escape_string($_POST['hd']);
				$UpdateType = $_POST['UpdateType'];
				
				if($old_epprefix != $epprefix) // if the episode prefix changed, then it nullifies the episodes images..
				{
					$QuerySet = ", image = '0'";
				}
				else 
				{
					$QuerySet = "";
				}
				
				if($UpdateType == 0) // Update ONLY episodes
				{
					$QueryAddon = " AND Movie = 0";
				}
				else if($UpdateType == 1) //Update ONLY movies
				{
					$QueryAddon = " AND Movie = 1";
				}
				else if($UpdateType == 2) // Update BOTH movies and episodes..
				{
					$QueryAddon = "";
				}
				else // default to ONLY episodes.. best and safest practice.. 
				{
					$QueryAddon = " AND Movie = 0";
				}
				
				$fullSeriesName = stripslashes($fullSeriesName);
				//echo $description;
				mysql_query("SET NAMES 'utf8'"); 
				$query = 'UPDATE episode
					SET vidwidth=\'' . $vidwidth .'\', 
					vidheight=\'' . $vidheight . '\', 
					epprefix=\'' . $epprefix . '\', 
					subGroup=\'' . $subGroup . '\', 
					videotype=\'' . $videotype . '\',
					hd=\'' . $hd . '\''.$QuerySet.'
					WHERE sid=\'' . $sid . '\'' . $QueryAddon;
				mysql_query($query) or die('Error : ' . mysql_error());
				$this->ModRecord('Mass Episode Edit for Series ' . $fullSeriesName . ', old -vh:' . $old_vidheight . ', -vw:' . $old_vidwidth . ', -pref:' . $old_epprefix . ', -sg:' . $old_subGroup . ', -vt:' . $old_videotype . '');
				echo 'Success';
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}		
		else if($_POST['method'] == 'AdminSeriesSearch' && $this->ValidatePermission(24) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				if($_POST['SeriesName'] == '')
				{
					echo '<div align="center">Please input the name of a Series!</div>';
				}
				else
				{
					include_once("series.class.php");
					$S = new Series();
				}
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}	
		else if($_POST['method'] == 'SeriesAnnouncementBuilder' && $this->ValidatePermission(73) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$query = "SELECT seoname, fullSeriesName, description FROM series WHERE";
				$i = 0;
				foreach($_POST['sid'] as $name => $value) 
				{
					if($i > 0)
					{
						$query .= " OR";
					}
					$query .= " id = $value";
					$i++;
				}
				$result = mysql_query($query);
				echo '<textarea style="height:175px;overflow-y:scroll;overflow-x:none;border:1px solid #0C90BB;width:100%" onclick="this.select()">';
				while($row = mysql_fetch_array($result))
				{
					$description = stripslashes($row['description']);
					echo '<br /><span style="font-size:11px;"><span style="font-family: verdana, geneva, sans-serif; "><a href="http://www.animeftw.tv/anime/' . $row['seoname'] . '/">' . stripslashes($row['fullSeriesName']) . '</a></span></span><br />'."\n";
					echo '<strong style="font-family: verdana, geneva, sans-serif; font-size: 11px; ">Synopsis:&nbsp;</strong><span style="font-size:11px;"><span style="font-family: verdana, geneva, sans-serif; ">' . $description . '</span><br />'."\n\r";
				}
				//echo print_r($_POST);
				echo '</textarea>';
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'UploadsAddition')
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$episodes = mysql_real_escape_string($_POST['episodesdoing'])."/".mysql_real_escape_string($_POST['episodetotal']);
				$dimmensions = mysql_real_escape_string($_POST['width'])."x".mysql_real_escape_string($_POST['height']);
				if(isset($_POST['Status']) && ($_POST['Status'] == 'ongoing' || $_POST['Status'] == 'done'))
				{
					$Change = 1;
				}
				else
				{
					$Change = 0;
				}
				$query = "INSERT INTO uestatus (`series`, `prefix`, `episodes`, `type`, `resolution`, `status`, `user`, `updated`, `anidbsid`, `fansub`, `sid`, `change`) VALUES ('" . mysql_real_escape_string($_POST['Series']) . "', '" . mysql_real_escape_string($_POST['Prefix']) . "', '" . $episodes . "', '" . mysql_real_escape_string($_POST['Type']) . "', '" . $dimmensions . "', '" . mysql_real_escape_string($_POST['Status']) . "', '" . mysql_real_escape_string($_POST['user']) . "', NOW(), '" . mysql_real_escape_string($_POST['anidb']) . "', '" . mysql_real_escape_string($_POST['fansub']) . "', '" . mysql_real_escape_string($_POST['sid']) . "', " . $Change . ")";
				mysql_query($query) or die(mysql_error());
				
				$this->ModRecord('Added a Series to the Tracker, ' . mysql_real_escape_string($_POST['Series']));
                if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv' && (isset($_POST['Status']) && isset($_POST['Previous-Status']) && $_POST['Status'] != $_POST['Previous-Status'] && ($_POST['Status'] == 'done' || $_POST['Status'] == 'ongoing'))){
                    $slackData = "*New Series added to the tracker by " . $this->string_fancyUsername($_POST['uploader'],NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . "*: \nTitled: " . $_POST['Series'] . "\nPosted in the " . $_POST['Status'] . " Section. <https://www.animeftw.tv/manage/#uploads| Manage this entry.>";
                    $slack = $this->postToSlack($slackData,1);
                }
				echo 'Success';
				//echo $query;
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'UploadsEdit')
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{                
				// if the update was performed in the ongoing or done category, we need to record that as something updated so that we alert the head honchos
				if(isset($_POST['Status']) && ($_POST['Status'] == 'ongoing' || $_POST['Status'] == 'done'))
				{
					$Change = 1;
				}
				else
				{
					$Change = 0;
				}
				$episodes = mysql_real_escape_string($_POST['episodesdoing'])."/".mysql_real_escape_string($_POST['episodetotal']);
				$dimmensions = mysql_real_escape_string($_POST['width'])."x".mysql_real_escape_string($_POST['height']);
				$query = "UPDATE `uestatus` SET 
				`series` = '" . mysql_real_escape_string($_POST['Series']) . "', 
				`prefix` = '" . mysql_real_escape_string($_POST['Prefix']) . "', 
				`episodes` = '" . $episodes . "', 
				`type` = '" . mysql_real_escape_string($_POST['Type']) . "', 
				`resolution` = '" . $dimmensions . "', 
				`status` = '" . mysql_real_escape_string($_POST['Status']) . "', 
				`user` = '" . mysql_real_escape_string($_POST['uploader']) . "',
				`updated` = NOW(), 
				`anidbsid` = '" . mysql_real_escape_string($_POST['anidb']) . "', 
				`fansub` = '" . mysql_real_escape_string($_POST['fansub']) . "',
				`sid` = '" . mysql_real_escape_string($_POST['sid']) . "',
				`change` = " . $Change. "
				WHERE `uestatus`.`ID` = " . mysql_real_escape_string($_POST['ueid']);
				mysql_query($query) or die(mysql_error());
				
				// ADDED: 27/03/15 by Robotman321
				// will update the Requests entry to ensure things are kept up to date.
                // UPDATED: 06/05/2016 by Robotman321
                // Uses Database value to verify status.
                $query = "SELECT `value` FROM `settings` WHERE `name` = 'all_upload_statuses' LIMIT 1";
                $result = mysql_query($query);
                
                $row = mysql_fetch_assoc($result);
                
				$EntryStatuses = explode($row['value'], '|');
				// we will want to map the request to the uploads entry down the line, it will keep things working correctly..
				$query = "UPDATE `requests` SET `status` = '" . array_search($_POST['Status'], $EntryStatuses) . "' WHERE `anidb` = " . mysql_real_escape_string($_POST['anidb']);
				mysql_query($query) or die(mysql_error());
				
				$this->ModRecord('Editted Tracker entry ' . mysql_real_escape_string($_POST['ueid']));
                if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv' && (isset($_POST['Status']) && isset($_POST['Previous-Status']) && ($_POST['Status'] != $_POST['Previous-Status'] || ($_POST['Status'] == $_POST['Previous-Status'] && $_POST['Status'] == 'ongoing')) && ($_POST['Status'] == 'done' || $_POST['Status'] == 'ongoing'))){
                    $slackData = "*New Series updated on the tracker by " . $this->string_fancyUsername($_POST['uploader'],NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . "*: \nTitled: " . $_POST['Series'] . "\nPosted in the " . $_POST['Status'] . " Section. <https://www.animeftw.tv/manage/#uploads| Manage this entry.>";
                    $slack = $this->postToSlack($slackData,1);
                }
				echo 'Success';
				//echo $query;
			}
			else 
			{
				echo 'Failed: Authorization was wrong.';
			}
		}
		else if($_POST['method'] == 'UserEdit')
		{
			if(!isset($_POST['s']))
			{
					echo 'Error: Your post was nulled.';
			}
			else
			{
				$query = "SELECT Password, Level_access FROM users WHERE ID = " . mysql_real_escape_string($_POST['s']);
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				if((isset($_POST['Authorization']) && $_POST['Authorization'] == md5($_SERVER['REMOTE_ADDR'].$row['Password'].$_SERVER['HTTP_USER_AGENT']) && ($row['Level_access'] != 1 && $row['Level_access'] != 2)) || ((isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001') && ($row['Level_access'] == 1 || $row['Level_access'] == 2)))
				{
					$rid = @$_POST['id'];
					$sid = $_POST['s'];
					$active = @$_POST['Active'];
					$reason = @$_POST['Reason'];
					$level_access = urldecode(@$_POST['Level_access']);
					$candownload = urldecode(@$_POST['canDownload']);
					$firstname = urldecode(@$_POST['firstName']);
					$lastname = urldecode(@$_POST['lastName']);
					$gender = urldecode(@$_POST['gender']);
					$ageday = urldecode(@$_POST['ageDate']);
					$agemonth = urldecode(@$_POST['ageMonth']);
					$ageyear = urldecode(@$_POST['ageYear']);
					$country = urldecode(@$_POST['country']); 
					$msn = urldecode(@$_POST['msnAddress']);
					$aim = urldecode(@$_POST['aimName']);
					$yim = urldecode(@$_POST['yahooName']);
					$skype = urldecode(@$_POST['skypeName']);
					$icq = urldecode(@$_POST['icqNumber']);
					$showemail = urldecode(@$_POST['showEmail']);
					$avataractive = urldecode(@$_POST['avatarActivate']);
					$avatarextension = urldecode(@$_POST['avatarExtension']);
					$personalmsg = urldecode(@$_POST['personalMsg']);
					$membertitle = urldecode(@$_POST['memberTitle']);
					$aboutme = urldecode(@$_POST['aboutMe']);
					$interests = urldecode(@$_POST['Interests']);
					$sigactive = urldecode(@$_POST['signatureActive']);
					$Signature = urldecode(@$_POST['Signature']);
					$showChat = urldecode(@$_POST['showChat']);
					$theme = urldecode(@$_POST['theme']);
					if($row['Level_access'] == 1 || $row['Level_access'] == 2){
						$additional = ', avatarActivate=\'' . mysql_real_escape_string($avataractive) . '\', avatarExtension=\'' . mysql_real_escape_string($avatarextension) . '\', personalMsg=\'' . mysql_real_escape_string($personalmsg) . '\', memberTitle=\'' . mysql_real_escape_string($membertitle) . '\', aboutMe=\'' . mysql_real_escape_string($aboutme) . '\', interests=\'' . mysql_real_escape_string($interests) . '\', signatureActive=\'' . mysql_real_escape_string($sigactive) . '\', Signature=\'' . mysql_real_escape_string($Signature) . '\'';
					}
					else if($row['Level_access'] != 3 && $row['Level_access'] != 1 && $row['Level_access'] != 2){
					//AMs and staff can see these...
						if($Signature != '')
						{
							$additional = ', aboutMe=\'' . mysql_real_escape_string($aboutme) . '\', interests=\'' . mysql_real_escape_string($interests) . '\', Signature=\'' . mysql_real_escape_string($Signature).'\', signatureActive=\'yes\'';
						}
						else
						{
							$additional = ', aboutMe=\'' . mysql_real_escape_string($aboutme) . '\', interests=\'' . mysql_real_escape_string($interests) . '\', Signature=\'' . mysql_real_escape_string($Signature).'\'';
						}
					}
					else {
					// basic members can change these..
						$additional = '';
					}
					$query = 'UPDATE users SET 
					firstName=\'' . mysql_real_escape_string($firstname) . '\', 
					lastName=\'' . mysql_real_escape_string($lastname) . '\', 
					gender=\'' . mysql_real_escape_string($gender) . '\', 
					ageDate=\'' . mysql_real_escape_string($ageday) . '\', 
					ageMonth=\'' . mysql_real_escape_string($agemonth) . '\', 
					ageYear=\'' . mysql_real_escape_string($ageyear) . '\', 
					country=\'' . mysql_real_escape_string($country) . '\', 
					msnAddress=\'' . mysql_real_escape_string($msn) . '\', 
					aimName=\'' . mysql_real_escape_string($aim) . '\', 
					yahooName=\'' . mysql_real_escape_string($yim) . '\', 
					skypeName=\'' . mysql_real_escape_string($skype) . '\', 
					icqNumber=\'' . mysql_real_escape_string($icq) . '\', 
					showEmail=\'' . mysql_real_escape_string($showemail) . '\', 
					showChat=\'' . mysql_real_escape_string($showChat) . '\', 
					theme=\'' . mysql_real_escape_string($theme) . '\'
					'.$additional.'
					WHERE ID=\'' . $rid . '\'';
					//echo $query;
					//echo $_SERVER['REQUEST_URI'];
   					mysql_query($query) or die('Error : ' . mysql_error());
					echo 'Success';

				}
				else
				{
					echo 'Failed: Authorization was wrong.';
				}
			}
		}
		else if($_POST['method'] == 'EditStoreItem' && $this->ValidatePermission(84) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				if(!isset($_POST['id']) || !is_numeric($_POST['id']))
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: No ID provided.</div>';
				}
				else
				{
					$this->uid = $_POST['uid'];
					$this->ModRecord('Edit Item to the Store');
					$results = mysql_query("UPDATE store_items SET category = '" . mysql_real_escape_string($_POST['item-categories']) . "', name = '" . mysql_real_escape_string($_POST['name']) . "', price = '" . mysql_real_escape_string($_POST['price']) . "', availability = '" . mysql_real_escape_string($_POST['availability']) . "', description = '" . mysql_real_escape_string($_POST['description']) . "', productnum = '" . mysql_real_escape_string($_POST['productnum']) . "', pictures = '" . mysql_real_escape_string($_POST['pictures']) . "', picturetype = '" . mysql_real_escape_string($_POST['picturetype']) . "', weight = '" . mysql_real_escape_string($_POST['weight']) . "' WHERE id = " . mysql_real_escape_string($_POST['id']));
					if(!$results)
					{
						echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysql_error() . '</div>';
						exit;
					}
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Update Completed.</div>';
				}
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'AddStoreItem' && $this->ValidatePermission(86) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$this->ModRecord('Add Item to the Store');
				$results = mysql_query("INSERT INTO `mainaftw_anime`.`store_items` (`id` ,`category` ,`name` ,`price` ,`availability` ,`description` ,`productnum` ,`pictures` ,`picturetype` ,`weight`) VALUES (NULL , '" . mysql_real_escape_string($_POST['item-categories']) . "', '" . mysql_real_escape_string($_POST['name']) . "', '" . mysql_real_escape_string($_POST['price']) . "', '" . mysql_real_escape_string($_POST['availability']) . "', '" . mysql_real_escape_string($_POST['description']) . "', '" . mysql_real_escape_string($_POST['productnum']) . "', '" . mysql_real_escape_string($_POST['pictures']) . "', '" . mysql_real_escape_string($_POST['picturetype']) . "', '" . mysql_real_escape_string($_POST['weight']) . "');");
				if(!$results)
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysql_error() . '</div>';
					exit;
				}
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Addition Compelted. <a href="#" onClick="AdminFunction(\'manage-stock\',\'edit\',\'' . mysql_insert_id() . '\'); return false;">Add Inventory for this Item.</a></div>';
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'EditStoreItem' && $this->ValidatePermission(84) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				if(!isset($_POST['id']) || !is_numeric($_POST['id']))
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: No ID provided.</div>';
				}
				else
				{
					$this->uid = $_POST['uid'];
					$this->ModRecord('Edit Item to the Store');
					$results = mysql_query("UPDATE store_items SET category = '" . mysql_real_escape_string($_POST['item-categories']) . "', name = '" . mysql_real_escape_string($_POST['name']) . "', price = '" . mysql_real_escape_string($_POST['price']) . "', availability = '" . mysql_real_escape_string($_POST['availability']) . "', description = '" . mysql_real_escape_string($_POST['description']) . "', productnum = '" . mysql_real_escape_string($_POST['productnum']) . "', pictures = '" . mysql_real_escape_string($_POST['pictures']) . "', picturetype = '" . mysql_real_escape_string($_POST['picturetype']) . "', weight = '" . mysql_real_escape_string($_POST['weight']) . "' WHERE id = " . mysql_real_escape_string($_POST['id']));
					if(!$results)
					{
						echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysql_error() . '</div>';
						exit;
					}
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Update Completed.</div>';
				}
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'AddStoreItem' && $this->ValidatePermission(86) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->uid = $_POST['uid'];
				$this->ModRecord('Add Item to the Store');
				$results = mysql_query("INSERT INTO `mainaftw_anime`.`store_items` (`id` ,`category` ,`name` ,`price` ,`availability` ,`description` ,`productnum` ,`pictures` ,`picturetype` ,`weight`) VALUES (NULL , '" . mysql_real_escape_string($_POST['item-categories']) . "', '" . mysql_real_escape_string($_POST['name']) . "', '" . mysql_real_escape_string($_POST['price']) . "', '" . mysql_real_escape_string($_POST['availability']) . "', '" . mysql_real_escape_string($_POST['description']) . "', '" . mysql_real_escape_string($_POST['productnum']) . "', '" . mysql_real_escape_string($_POST['pictures']) . "', '" . mysql_real_escape_string($_POST['picturetype']) . "', '" . mysql_real_escape_string($_POST['weight']) . "');");
				if(!$results)
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysql_error() . '</div>';
					exit;
				}
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Addition Compelted. <a href="#" onClick="AdminFunction(\'manage-stock\',\'edit\',\'' . mysql_insert_id() . '\'); return false;">Add Inventory for this Item.</a></div>';
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'AddEpisode' && $this->ValidatePermission(18) == TRUE)
		{
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				$this->addEpisode();
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else if($_POST['method'] == 'EditEpisode' && $this->ValidatePermission(19) == TRUE)
		{
			
			if(isset($_POST['Authorization']) && $_POST['Authorization'] == '0110110101101111011100110110100001101001')
			{
				// This is where the magic happens..
				$id = $_POST['id'];
				$epnumber = $_POST['epnumber'];
				$sid = $_POST['sid'];
				$epname = $_POST['epname'];
				$vidheight = $_POST['vidheight'];
				$vidwidth = $_POST['vidwidth'];
				$epprefix = $_POST['epprefix'];
				$subGroup = $_POST['subGroup'];
				$Movie = $_POST['Movie'];
				$videotype = $_POST['videotype'];
				$hd = $_POST['hd'];
				$epname = stripslashes($epname);
				$epprefix    = stripslashes($epprefix);				  
				// update the item in the database
				$query = 'UPDATE episode SET epnumber=\'' . mysql_real_escape_string($epnumber) . '\', sid=\'' . mysql_real_escape_string($sid) .'\', epname=\'' . mysql_real_escape_string($epname) . '\', vidheight=\'' . mysql_real_escape_string($vidheight) . '\', vidwidth=\'' . mysql_real_escape_string($vidwidth) . '\', epprefix=\'' . mysql_real_escape_string($epprefix) . '\', subGroup=\'' . mysql_real_escape_string($subGroup) . '\', Movie=\'' . mysql_real_escape_string($Movie) . '\', videotype=\'' . mysql_real_escape_string($videotype) . '\', `hd`=\'' . mysql_real_escape_string($hd) . '\' WHERE id=' . $id . '';
				mysql_query($query) or die('Error : ' . mysql_error());
				$this->ModRecord('Edit Episode #' . $epnumber . ' of id ' . $id);
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;width:100%;">Episode Update Completed Successfully.</div>';
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
		else {
			echo 'You posted a method of: '.$_POST['method'].' And it has not been setup yet.';
			print_r($this->ValidatePermission);
		}
	}
	private function addEpisode(){
		$auto = FALSE;
		$AniDB;
		if(isset($_POST['entry-type']) && $_POST['entry-type'] == '1' && isset($_POST['fromep']) && $_POST['fromep'] != "" && isset($_POST['toep'])&&$_POST['toep']!=""){
			$auto = TRUE;
			$AniDB  = new AniDB();
		}
		$anidbid = $_POST['anidbid'];
		$fromep = $_POST['fromep'];
		$toep = $_POST['toep'];
		$epnumber = mysql_real_escape_string($_POST['epnumber']);
		$sid = mysql_real_escape_string($_POST['sid']);
		$epname = mysql_real_escape_string(urldecode($_POST['epname']));
		$vidheight = mysql_real_escape_string($_POST['vidheight']);
		$vidwidth = mysql_real_escape_string($_POST['vidwidth']);
		$epprefix = mysql_real_escape_string($_POST['epprefix']);
		$subGroup = mysql_real_escape_string($_POST['subGroup']);
		$Movie = mysql_real_escape_string($_POST['Movie']);
		$Remember = NULL; //This gave an annoying undefined index error everytime these fields were unchecked.
		if(isset($_POST['Remember'])){
			$Remember = mysql_real_escape_string($_POST['Remember']);
		}
		$Changed = NULL;
		if(isset($_POST['Changed'])){
			$Changed = mysql_real_escape_string($_POST['Changed']);
		}
		$addtime = mysql_real_escape_string($_POST['date']);
		$videotype =mysql_real_escape_string( $_POST['videotype']);
		$hd = mysql_real_escape_string($_POST['hd']);
		if($auto){
			if(!is_numeric($anidbid)){
				echo 'Error: Anidb value is not numeric.';
				exit;
			}
		}
		if($addtime == '0')
		{
			$addtime = '0';
		}
		else
		{
			$addtime = time();
		}
		
		$eptitles; //declaration
		if($auto==FALSE){
			$fromep = $epnumber;
			$toep = $epnumber;
		}else{
			if($toep>$AniDB->getEpisodeCount($anidbid)){
				echo 'Error: Max episode is higher than AniDB\'s episode.';
				exit;
			}else{
				$eptitles = $AniDB->getEpisodeTitles($anidbid, $fromep, $toep);
			}
		}
		if($fromep<0||$toep<0){ //If someone writes in negative values for whatever reason..
			echo 'Error: Negative episode numbers';
			exit;
		}
		
		for($i=$fromep;$i<=$toep;$i++){
			$epnumber = mysql_real_escape_string($i);
			if($auto){ //If it's auto, we know that $eptitles is initialized
				$epname = $eptitles[$i];
			}
			$NextEp = $i+1;
			// ADDED: 8/13/14 - robotman321
			// Queries the database to make sure that 
			$query = "INSERT INTO `episode` (`sid`, `epnumber`, `epname`, `seriesname`, `vidheight`, `vidwidth`, `subGroup`, `epprefix`, `Movie`, `date`, `videotype`, `uid`, `hd`) VALUES ('$sid', '$epnumber', '$epname', 'unknown', '$vidheight', '$vidwidth', '$subGroup', '$epprefix', '$Movie', '$addtime', '$videotype', '" . $this->UserArray[1] . "', '" . $hd . "')";
			$query;
			$results = mysql_query($query);
			if(!$results)
			{
				echo 'There was an error processing that request, error num #1001: ' . mysql_error();
				exit;
			}
			echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;width:100%;">Episode #' . $epnumber . ' Added, titled: ' . $epname . ' Added Successfully.<div>';
			// now we check to see if we can use: RecordNotification($sid,$eid)
			$airingCheck = mysql_query("SELECT episode.id AS epid, series.id AS sid, series.stillRelease, series.fullSeriesName FROM series, episode WHERE series.id = '".$sid."' AND episode.sid = series.id AND episode.epnumber = '".$epnumber."'");
			$ar = mysql_fetch_array($airingCheck);
			if($ar['stillRelease'] == 'yes')
			{
				// put the notification in the system that the end user needs to check their notifications!
				$this->recordNotification($ar['sid'],$ar['epid']);
				
				//If a series is still releasing, add the entry to the email database.
				// v1 is the episode id, for matching in the system when an email needs to go out.
				mysql_query("INSERT INTO email (`id`, `date`, `sid`, `v1`, `v2`) VALUES (NULL,'" . time() . "', '" . $ar['sid'] . "', '" . $ar['epid'] . "', '1');");
			}							
			$this->ModRecord('Add Episode #'.$epnumber.' to  '.$ar['fullSeriesName']);
			
			if($Changed == 'on')
			{
				// changed entry is on, we need to update the uploads board
				mysql_query("UPDATE uestatus SET `change` = 0 WHERE ID = " . mysql_real_escape_string($_POST['ueid']));
				$this->ModRecord("Removed Notifications for Entry " . $_POST['ueid'] . ' in the Uploads Board');
			}
		}
	}
	private function updatePreSequel($sid, $prequelto, $sequelto)
	{
		if($prequelto != 0)//If the prequel is updated, we update that series sequel to this one.
		{
			$query = 'UPDATE series SET sequelto=\'' . mysql_real_escape_string($sid) . '\' WHERE id=' . mysql_real_escape_string($prequelto) . '';
			mysql_query($query) or die('Error : ' . mysql_error());	
		}
		if($sequelto != 0)//If the sequel is, or also was updated with the prequel, we update that series prequel to this one.
		{
			$query = 'UPDATE series SET prequelto=\'' . mysql_real_escape_string($sid) . '\' WHERE id=' . mysql_real_escape_string($sequelto) . '';
			mysql_query($query) or die('Error : ' . mysql_error());	
		}
	}
	
	private function recordNotification($sid,$eid)
	{
		mysql_query("INSERT INTO notifications (uid, date, type, d1, d2, d3) VALUES (NULL, '".time()."', '0', '".$sid."', '".$eid."', 'NULL')");
	}
}