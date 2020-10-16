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
					$results = mysqli_query($query);
					$i = 0;
					// we want to query the database, to get a full list of groups, that way we can delete objects where need be..
					while($row = mysqli_fetch_array($results))
					{
						$query = mysqli_query("SELECT id FROM permissions_objects WHERE permission_id = ".$permissionsid." AND type = 1 AND oid = ".$row[0]);
						$count = mysqli_num_rows($query);
						if(in_array($row[0],$value))
						{
							//if the presented valiable is IN the array, that means it needs to be added
							if($count == 1)
							{
								//we have found the package, nothing needed.
							}
							else
							{ //for everything else you need to add it.
								mysqli_query("INSERT INTO permissions_objects (id, oid, type, permission_id) VALUES (NULL, '".mysqli_real_escape_string($row[0])."', '1', '".mysqli_real_escape_string($permissionsid)."')") or die(mysqli_error());
							}
						}
						else
						{ //its not in the array, it can be deleted
							if($count == 1)
							{ // the package was found in the DB, but we dont need it, so it can be deleted
								mysqli_query("DELETE FROM permissions_objects WHERE permission_id = ".$permissionsid." AND type = 1 AND oid = ".$row[0]);
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
				$sid = mysqli_real_escape_string($_POST['sid']);
				$seriesName = mysqli_real_escape_string($_POST['seriesName']);
				$fullSeriesName = mysqli_real_escape_string($_POST['fullSeriesName']);
				$kanji = mysqli_real_escape_string($_POST['kanji']);
				$romaji = mysqli_real_escape_string($_POST['romaji']);
				$synonym = mysqli_real_escape_string($_POST['synonym']);
				$seoname = mysqli_real_escape_string($_POST['seoname']);
				$videoServer = mysqli_real_escape_string($_POST['videoServer']);
				$active = mysqli_real_escape_string($_POST['active']);
				$description = mysqli_real_escape_string($_POST['description2']);
				$ratingLink = mysqli_real_escape_string($_POST['ratingLink']);
				$stillRelease = mysqli_real_escape_string($_POST['stillRelease']);
				$Movies = mysqli_real_escape_string($_POST['Movies']);
				$moviesOnly = mysqli_real_escape_string($_POST['moviesOnly']);
				$OVA = mysqli_real_escape_string($_POST['OVA']);
				$noteReason = mysqli_real_escape_string($_POST['noteReason']);
				$aonly = mysqli_real_escape_string($_POST['aonly']);
				$prequelto = mysqli_real_escape_string($_POST['prequelto']);
				$sequelto = mysqli_real_escape_string($_POST['sequelto']);
				$hd = mysqli_real_escape_string($_POST['hd']);
				$license = mysqli_real_escape_string($_POST['license']);

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
				$seriesType = mysqli_real_escape_string($_POST['seriesType']);
				$seriesList = mysqli_real_escape_string($_POST['seriesList']);
				$ueid = mysqli_real_escape_string($_POST['uploadsEntry']);

				$fullSeriesName = htmlspecialchars($fullSeriesName);
				$kanji = htmlspecialchars($kanji);
				$description = nl2br($description);
				$noteReason = nl2br($noteReason);
				//echo $description;
				mysqli_query("SET NAMES 'utf8'");
				$query = 'UPDATE series
					SET seriesName=\'' . mysqli_real_escape_string($seriesName) .'\',
					fullSeriesName=\'' . $fullSeriesName . '\',
					romaji=\'' . mysqli_real_escape_string($romaji) . '\',
					kanji=\'' . mysqli_real_escape_string($kanji) . '\',
					synonym=\'' . $synonym . '\',
					seoname=\'' . mysqli_real_escape_string($seoname) . '\',
					videoServer=\'' . mysqli_real_escape_string($videoServer) . '\',
					active=\'' . mysqli_real_escape_string($active) . '\',
					description=\'' . $description . '\',
					ratingLink=\'' . mysqli_real_escape_string($ratingLink) . '\',
					stillRelease=\'' . mysqli_real_escape_string($stillRelease) . '\',
					Movies=\'' . mysqli_real_escape_string($Movies) . '\',
					moviesOnly=\'' . mysqli_real_escape_string($moviesOnly) . '\',
					OVA=\'' . mysqli_real_escape_string($OVA) . '\',
					noteReason=\'' . $noteReason . '\',
					aonly=\'' . mysqli_real_escape_string($aonly) . '\',
					prequelto=\'' . mysqli_real_escape_string($prequelto) . '\',
					sequelto=\'' . mysqli_real_escape_string($sequelto) . '\',
					category=\'' . $category . '\',
					seriesType=\'' . mysqli_real_escape_string($seriesType) . '\',
					seriesList=\'' . mysqli_real_escape_string($seriesList) . '\',
					hd=\'' . mysqli_real_escape_string($hd) . '\',
					ueid=\'' . $ueid . '\',
					`license`=\'' . $license . '\',
					`updated`=\'' . time() . '\'
					WHERE id=' . $sid . '';
				mysqli_query($query) or die('Error : ' . mysqli_error());
				$euidd = mysqli_query("UPDATE uestatus SET sid = $sid WHERE ID = $ueid");
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
				$seriesName = mysqli_real_escape_string($_POST['seriesName']);
				$fullSeriesName = mysqli_real_escape_string($_POST['fullSeriesName']);
				$kanji = mysqli_real_escape_string($_POST['kanji']);
				$romaji = mysqli_real_escape_string($_POST['romaji']);
				$synonym = mysqli_real_escape_string($_POST['synonym']);
				$seoname = mysqli_real_escape_string($_POST['seoname']);
				$videoServer = mysqli_real_escape_string($_POST['videoServer']);
				$active = mysqli_real_escape_string($_POST['active']);
				$description = mysqli_real_escape_string($_POST['description2']);
				$ratingLink = mysqli_real_escape_string($_POST['ratingLink']);
				$stillRelease = mysqli_real_escape_string($_POST['stillRelease']);
				$Movies = mysqli_real_escape_string($_POST['Movies']);
				$moviesOnly = mysqli_real_escape_string($_POST['moviesOnly']);
				$OVA = mysqli_real_escape_string($_POST['OVA']);
				$noteReason = mysqli_real_escape_string($_POST['noteReason']);
				$aonly = mysqli_real_escape_string($_POST['aonly']);
				$prequelto = mysqli_real_escape_string($_POST['prequelto']);
				$sequelto = mysqli_real_escape_string($_POST['sequelto']);
				$hd = mysqli_real_escape_string($_POST['hd']);
				$license = mysqli_real_escape_string($_POST['license']);

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
				$seriesType = mysqli_real_escape_string($_POST['seriesType']);
				$seriesList = mysqli_real_escape_string($_POST['seriesList']);
				$ueid = mysqli_real_escape_string($_POST['uploadsEntry']);

				mysqli_query("SET NAMES 'utf8'");
				$query = "INSERT INTO series (`seriesName`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `seoname`, `videoServer`, `active`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesOnly`, `OVA`, `noteReason`, `aonly`, `prequelto`, `sequelto`, `category`, `seriesType`, `seriesList`, `ueid`, `hd`, `license`, `added`, `updated`) VALUES ('$seriesName', '$fullSeriesName', '$romaji', '$kanji', '$synonym', '$seoname', '$videoServer', '$active', '$description', '$ratingLink', '$stillRelease', '$Movies', '$moviesOnly', '$OVA', '$noteReason', '$aonly', '$prequelto', '$sequelto', '$category', '$seriesType', '$seriesList', '$ueid', '$hd', '$license', '" . time() . "', '" . time() . "')";
				mysqli_query($query) or die('There was an error adding the series: ' . mysqli_error() . ', here was the query: ' . $query);

				$sid = $this->SingleVarQuery("SELECT id FROM series WHERE seriesName = '" . $seriesName . "'",'id'); //Get the Series ID through seriesName
				$euidd = mysqli_query("UPDATE uestatus SET sid = $sid WHERE ID = $ueid");
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
				$sid = mysqli_real_escape_string($_POST['sid']);
				$fullSeriesName = mysqli_real_escape_string($_POST['fullSeriesName']);
				$old_vidwidth = mysqli_real_escape_string($_POST['old_vidwidth']);
				$old_vidheight = mysqli_real_escape_string($_POST['old_vidheight']);
				$old_epprefix = mysqli_real_escape_string($_POST['old_epprefix']);
				$old_subGroup = mysqli_real_escape_string($_POST['old_subGroup']);
				$old_videotype = mysqli_real_escape_string($_POST['old_videotype']);
				$old_hd = mysqli_real_escape_string($_POST['old_hd']);
				$vidwidth = mysqli_real_escape_string($_POST['vidwidth']);
				$vidheight = mysqli_real_escape_string($_POST['vidheight']);
				$epprefix = mysqli_real_escape_string($_POST['epprefix']);
				$subGroup = mysqli_real_escape_string($_POST['subGroup']);
				$videotype = mysqli_real_escape_string($_POST['videotype']);
				$hd = mysqli_real_escape_string($_POST['hd']);
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
				mysqli_query("SET NAMES 'utf8'");
				$query = 'UPDATE episode
					SET vidwidth=\'' . $vidwidth .'\',
					vidheight=\'' . $vidheight . '\',
					epprefix=\'' . $epprefix . '\',
					subGroup=\'' . $subGroup . '\',
					videotype=\'' . $videotype . '\',
					hd=\'' . $hd . '\''.$QuerySet.'
					WHERE sid=\'' . $sid . '\'' . $QueryAddon;
				mysqli_query($query) or die('Error : ' . mysqli_error());
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
			if(isset($_POST['start-date']) && isset($_POST['end-date']))
			{
                $startDate = strtotime(urldecode($_POST['start-date']));
                $endDate = strtotime(urldecode($_POST['end-date']));

                include_once("../includes/classes/template.class.php");
                $blastLayout = new Template("../template/eblast/anime-update-container.tpl");
                $blastLayoutContent = '';

                // We work on the Series block first
                $query = "SELECT `id`, `seoname`, `fullSeriesName`, `stillRelease`, `hd` FROM `series` WHERE `added` >= ${startDate} AND `added` <= ${endDate}";
                $result = mysqli_query($query);
                $seriesArray = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $seriesArray[] = $row;
                }
                $numSeriesRows = mysqli_num_rows($result);
                $totalTemplateRows = ceil($numSeriesRows/3);

                $seriesLayout = new Template("../template/eblast/anime-update-block.tpl");
                $seriesLayout->set('block-name', 'Newly Added Series');

                $allSeriesOutput = '';
                for ($r=0; $r < $totalTemplateRows; $r++) {
                    $rowStart = $r*3;
                    $rowLayout = new Template("../template/eblast/anime-update-row.tpl");
                    $rowInfo = '';
                    for ($i=$rowStart; $i<($rowStart+3); $i++) {
                        if (($i+1) <= $numSeriesRows) {
                            $seriesTemplate = new Template("../template/eblast/anime-series-update-entry.tpl");
                            $seriesTemplate->set('series-link','http://www.animeftw.tv/anime/' . $seriesArray[$i]['seoname'] . '/');
                            $seriesTemplate->set('series-name',stripslashes($seriesArray[$i]['fullSeriesName']));
                            $seriesTemplate->set('series-image','https://img03.animeftw.tv/seriesimages/' . $seriesArray[$i]['id'] . '.jpg');

                            $seriesDetails = '<br>';
                            if ($seriesArray[$i]['stillRelease'] == 'yes') {
                                $seriesDetails .= '<img src="https://img03.animeftw.tv/airing_icon.gif" width="14" alt="This series is still airing!" style="font:16px/20px Arial, Helvetica, sans-serif; color:#000; box-shadow:0 1px 2px 0 rgba(0,0,0,0.5); vertical-align:top;"  />';
                            }
                            if ($seriesArray[$i]['hd'] == 2) {
                                $seriesDetails .= '<img src="https://img03.animeftw.tv/series-pages/hd-720p-icon.png" width="25" alt="Watch in 720p" style="font:16px/20px Arial, Helvetica, sans-serif; color:#000; box-shadow:0 1px 2px 0 rgba(0,0,0,0.5); vertical-align:top;"  />
                                <img src="https://img03.animeftw.tv/series-pages/hd-1080p-icon.png" width="25" alt="Watch in 1080p" style="font:16px/20px Arial, Helvetica, sans-serif; color:#000; box-shadow:0 1px 2px 0 rgba(0,0,0,0.5); vertical-align:top;"  />';
                            } else if ($seriesArray[$i]['hd'] == 1) {
                                $seriesDetails .= '<img src="https://img03.animeftw.tv/series-pages/hd-720p-icon.png" width="25" alt="Watch in 720p" style="font:16px/20px Arial, Helvetica, sans-serif; color:#000; box-shadow:0 1px 2px 0 rgba(0,0,0,0.5); vertical-align:top;"  />';
                            }

                            $seriesTemplate->set('series-details',$seriesDetails);
                            $rowInfo .= $seriesTemplate->output();

                            if (($i+1) % 3 == 0 && $i != 0) {
                            } else {
                                $rowInfo .= "    <td width=\"8\" style=\"min-width:8px;\"></td>";
                            }
                        }
                    }

                    $rowLayout->set('update-entry', $rowInfo);

                    $allSeriesOutput .= $rowLayout->output();
                    unset($rowLayout);
                }
                $seriesLayout->set('block-row', $allSeriesOutput);
                $blastLayoutContent .= $seriesLayout->output();

                // New episodes built here
                /*$query = "SELECT
                            `episode`.`id`, `episode`.`epname`, `episode`.`epnumber`, `series`.`fullSeriesName`, `series`.`seoname`
                        FROM
                            `episode`
                        LEFT JOIN
                            `series` ON `series`.`id` = `episode`.`sid`
                        WHERE
                            `episode`.`updated` >= ${startDate} AND
                            `episode`.`updated` <= ${endDate}";
                            echo $query;
                $result = mysqli_query($query);
                $numEpRows = mysqli_num_rows($result);

                if ($numEpRows != 0) {
                    $episodeArray = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $episodeArray[] = $row;
                    }
                    $totalTemplateRows = ceil($numEpRows/3);

                    $episodeLayout = new Template("../template/eblast/anime-update-block.tpl");
                    $episodeLayout->set('block-name', 'Newly Added Episodes');
                    $allEpisodeOutput = '';

                    $episodeLayout->set('block-row', $allEpisodeOutput);
                    $blastLayoutContent .= $episodeLayout->output();
                }*/

                // Fill out the last of the container template.
                $blastLayout->set('anime-update-block', $blastLayoutContent);
				echo '<textarea style="height:325px;overflow-y:scroll;overflow-x:none;border:1px solid #0C90BB;width:100%" onclick="this.select()">';
                echo $blastLayout->output();
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
				$episodes = mysqli_real_escape_string($_POST['episodesdoing'])."/".mysqli_real_escape_string($_POST['episodetotal']);
				$dimmensions = mysqli_real_escape_string($_POST['width'])."x".mysqli_real_escape_string($_POST['height']);
				if(isset($_POST['Status']) && ($_POST['Status'] == 'ongoing' || $_POST['Status'] == 'done'))
				{
					$Change = 1;
				}
				else
				{
					$Change = 0;
				}
				$query = "INSERT INTO uestatus (`series`, `prefix`, `episodes`, `type`, `resolution`, `status`, `user`, `updated`, `anidbsid`, `fansub`, `sid`, `change`, `hd`, `airing`) VALUES ('" . mysqli_real_escape_string($_POST['Series']) . "', '" . mysqli_real_escape_string($_POST['Prefix']) . "', '" . $episodes . "', '" . mysqli_real_escape_string($_POST['Type']) . "', '" . $dimmensions . "', '" . mysqli_real_escape_string($_POST['Status']) . "', '" . mysqli_real_escape_string($_POST['user']) . "', NOW(), '" . mysqli_real_escape_string($_POST['anidb']) . "', '" . mysqli_real_escape_string($_POST['fansub']) . "', '" . mysqli_real_escape_string($_POST['sid']) . "', " . $Change . ", '" . mysqli_real_escape_string($_POST['hdresolution']) . "', '" . mysqli_real_escape_string($_POST['seriesstatus']) . "')";
				mysqli_query($query) or die(mysqli_error());

				$this->ModRecord('Added a Series to the Tracker, ' . mysqli_real_escape_string($_POST['Series']));
                if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv' && (isset($_POST['Status']) && isset($_POST['Previous-Status']) && $_POST['Status'] != $_POST['Previous-Status'] && ($_POST['Status'] == 'done' || $_POST['Status'] == 'ongoing'))){
                    $slackData = "*New Series added to the tracker by " . $this->string_fancyUsername($_POST['uploader'],NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . "*: \nTitled: " . $_POST['Series'] . "\nPosted in the " . $_POST['Status'] . " Section. <https://www.animeftw.tv/manage/#uploads>";
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
				$episodes = mysqli_real_escape_string($_POST['episodesdoing'])."/".mysqli_real_escape_string($_POST['episodetotal']);
				$dimmensions = mysqli_real_escape_string($_POST['width'])."x".mysqli_real_escape_string($_POST['height']);
				$query = "UPDATE `uestatus` SET
				`series` = '" . mysqli_real_escape_string($_POST['Series']) . "',
				`prefix` = '" . mysqli_real_escape_string($_POST['Prefix']) . "',
				`episodes` = '" . $episodes . "',
				`type` = '" . mysqli_real_escape_string($_POST['Type']) . "',
				`resolution` = '" . $dimmensions . "',
				`status` = '" . mysqli_real_escape_string($_POST['Status']) . "',
				`user` = '" . mysqli_real_escape_string($_POST['uploader']) . "',
				`updated` = NOW(),
				`anidbsid` = '" . mysqli_real_escape_string($_POST['anidb']) . "',
				`fansub` = '" . mysqli_real_escape_string($_POST['fansub']) . "',
				`sid` = '" . mysqli_real_escape_string($_POST['sid']) . "',
				`change` = " . $Change. ",
				`hd` = '" . mysqli_real_escape_string($_POST['hdresolution']) . "',
				`airing` = '" . mysqli_real_escape_string($_POST['seriesstatus']) . "'
				WHERE `uestatus`.`ID` = " . mysqli_real_escape_string($_POST['ueid']);
				mysqli_query($query) or die(mysqli_error());

				// ADDED: 27/03/15 by Robotman321
				// will update the Requests entry to ensure things are kept up to date.
                // UPDATED: 06/05/2016 by Robotman321
                // Uses Database value to verify status.
                $query = "SELECT `value` FROM `settings` WHERE `name` = 'all_upload_statuses' LIMIT 1";
                $result = mysqli_query($query);

                $row = mysqli_fetch_assoc($result);

				$EntryStatuses = explode('|', $row['value']);

				// we will want to map the request to the uploads entry down the line, it will keep things working correctly..
				$query = "UPDATE `requests` SET `status` = '" . array_search($_POST['Status'], $EntryStatuses) . "' WHERE `anidb` = " . mysqli_real_escape_string($_POST['anidb']);
				mysqli_query($query) or die(mysqli_error());

				$this->ModRecord('Editted Tracker entry ' . mysqli_real_escape_string($_POST['ueid']));
                if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv' && (isset($_POST['Status']) && isset($_POST['Previous-Status']) && ($_POST['Status'] != $_POST['Previous-Status'] || ($_POST['Status'] == $_POST['Previous-Status'] && $_POST['Status'] == 'ongoing')) && ($_POST['Status'] == 'done' || $_POST['Status'] == 'ongoing'))){
                    $slackData = "*New Series updated on the tracker by " . $this->string_fancyUsername($_POST['uploader'],NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . "*: \nTitled: " . $_POST['Series'] . "\nPosted in the " . $_POST['Status'] . " Section. <https://www.animeftw.tv/manage/#uploads>";
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
				$query = "SELECT Password, Level_access FROM users WHERE ID = " . mysqli_real_escape_string($_POST['s']);
				$result = mysqli_query($query);
				$row = mysqli_fetch_array($result);
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
						$additional = ', avatarActivate=\'' . mysqli_real_escape_string($avataractive) . '\', avatarExtension=\'' . mysqli_real_escape_string($avatarextension) . '\', personalMsg=\'' . mysqli_real_escape_string($personalmsg) . '\', memberTitle=\'' . mysqli_real_escape_string($membertitle) . '\', aboutMe=\'' . mysqli_real_escape_string($aboutme) . '\', interests=\'' . mysqli_real_escape_string($interests) . '\', signatureActive=\'' . mysqli_real_escape_string($sigactive) . '\', Signature=\'' . mysqli_real_escape_string($Signature) . '\'';
					}
					else if($row['Level_access'] != 3 && $row['Level_access'] != 1 && $row['Level_access'] != 2){
					//AMs and staff can see these...
						if($Signature != '')
						{
							$additional = ', aboutMe=\'' . mysqli_real_escape_string($aboutme) . '\', interests=\'' . mysqli_real_escape_string($interests) . '\', Signature=\'' . mysqli_real_escape_string($Signature).'\', signatureActive=\'yes\'';
						}
						else
						{
							$additional = ', aboutMe=\'' . mysqli_real_escape_string($aboutme) . '\', interests=\'' . mysqli_real_escape_string($interests) . '\', Signature=\'' . mysqli_real_escape_string($Signature).'\'';
						}
					}
					else {
					// basic members can change these..
						$additional = '';
					}
					$query = 'UPDATE users SET
					firstName=\'' . mysqli_real_escape_string($firstname) . '\',
					lastName=\'' . mysqli_real_escape_string($lastname) . '\',
					gender=\'' . mysqli_real_escape_string($gender) . '\',
					ageDate=\'' . mysqli_real_escape_string($ageday) . '\',
					ageMonth=\'' . mysqli_real_escape_string($agemonth) . '\',
					ageYear=\'' . mysqli_real_escape_string($ageyear) . '\',
					country=\'' . mysqli_real_escape_string($country) . '\',
					msnAddress=\'' . mysqli_real_escape_string($msn) . '\',
					aimName=\'' . mysqli_real_escape_string($aim) . '\',
					yahooName=\'' . mysqli_real_escape_string($yim) . '\',
					skypeName=\'' . mysqli_real_escape_string($skype) . '\',
					icqNumber=\'' . mysqli_real_escape_string($icq) . '\',
					showEmail=\'' . mysqli_real_escape_string($showemail) . '\',
					showChat=\'' . mysqli_real_escape_string($showChat) . '\',
					theme=\'' . mysqli_real_escape_string($theme) . '\'
					'.$additional.'
					WHERE ID=\'' . $rid . '\'';
					//echo $query;
					//echo $_SERVER['REQUEST_URI'];
   					mysqli_query($query) or die('Error : ' . mysqli_error());
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
					$results = mysqli_query("UPDATE store_items SET category = '" . mysqli_real_escape_string($_POST['item-categories']) . "', name = '" . mysqli_real_escape_string($_POST['name']) . "', price = '" . mysqli_real_escape_string($_POST['price']) . "', availability = '" . mysqli_real_escape_string($_POST['availability']) . "', description = '" . mysqli_real_escape_string($_POST['description']) . "', productnum = '" . mysqli_real_escape_string($_POST['productnum']) . "', pictures = '" . mysqli_real_escape_string($_POST['pictures']) . "', picturetype = '" . mysqli_real_escape_string($_POST['picturetype']) . "', weight = '" . mysqli_real_escape_string($_POST['weight']) . "' WHERE id = " . mysqli_real_escape_string($_POST['id']));
					if(!$results)
					{
						echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysqli_error() . '</div>';
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
				$results = mysqli_query("INSERT INTO `mainaftw_anime`.`store_items` (`id` ,`category` ,`name` ,`price` ,`availability` ,`description` ,`productnum` ,`pictures` ,`picturetype` ,`weight`) VALUES (NULL , '" . mysqli_real_escape_string($_POST['item-categories']) . "', '" . mysqli_real_escape_string($_POST['name']) . "', '" . mysqli_real_escape_string($_POST['price']) . "', '" . mysqli_real_escape_string($_POST['availability']) . "', '" . mysqli_real_escape_string($_POST['description']) . "', '" . mysqli_real_escape_string($_POST['productnum']) . "', '" . mysqli_real_escape_string($_POST['pictures']) . "', '" . mysqli_real_escape_string($_POST['picturetype']) . "', '" . mysqli_real_escape_string($_POST['weight']) . "');");
				if(!$results)
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysqli_error() . '</div>';
					exit;
				}
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Addition Compelted. <a href="#" onClick="AdminFunction(\'manage-stock\',\'edit\',\'' . mysqli_insert_id() . '\'); return false;">Add Inventory for this Item.</a></div>';
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
					$results = mysqli_query("UPDATE store_items SET category = '" . mysqli_real_escape_string($_POST['item-categories']) . "', name = '" . mysqli_real_escape_string($_POST['name']) . "', price = '" . mysqli_real_escape_string($_POST['price']) . "', availability = '" . mysqli_real_escape_string($_POST['availability']) . "', description = '" . mysqli_real_escape_string($_POST['description']) . "', productnum = '" . mysqli_real_escape_string($_POST['productnum']) . "', pictures = '" . mysqli_real_escape_string($_POST['pictures']) . "', picturetype = '" . mysqli_real_escape_string($_POST['picturetype']) . "', weight = '" . mysqli_real_escape_string($_POST['weight']) . "' WHERE id = " . mysqli_real_escape_string($_POST['id']));
					if(!$results)
					{
						echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysqli_error() . '</div>';
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
				$results = mysqli_query("INSERT INTO `mainaftw_anime`.`store_items` (`id` ,`category` ,`name` ,`price` ,`availability` ,`description` ,`productnum` ,`pictures` ,`picturetype` ,`weight`) VALUES (NULL , '" . mysqli_real_escape_string($_POST['item-categories']) . "', '" . mysqli_real_escape_string($_POST['name']) . "', '" . mysqli_real_escape_string($_POST['price']) . "', '" . mysqli_real_escape_string($_POST['availability']) . "', '" . mysqli_real_escape_string($_POST['description']) . "', '" . mysqli_real_escape_string($_POST['productnum']) . "', '" . mysqli_real_escape_string($_POST['pictures']) . "', '" . mysqli_real_escape_string($_POST['picturetype']) . "', '" . mysqli_real_escape_string($_POST['weight']) . "');");
				if(!$results)
				{
					echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">There was an error when attempting to execute the query: ' . mysqli_error() . '</div>';
					exit;
				}
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;">Item Addition Compelted. <a href="#" onClick="AdminFunction(\'manage-stock\',\'edit\',\'' . mysqli_insert_id() . '\'); return false;">Add Inventory for this Item.</a></div>';
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
				$query = 'UPDATE episode SET epnumber=\'' . mysqli_real_escape_string($epnumber) . '\', sid=\'' . mysqli_real_escape_string($sid) .'\', epname=\'' . mysqli_real_escape_string($epname) . '\', vidheight=\'' . mysqli_real_escape_string($vidheight) . '\', vidwidth=\'' . mysqli_real_escape_string($vidwidth) . '\', epprefix=\'' . mysqli_real_escape_string($epprefix) . '\', subGroup=\'' . mysqli_real_escape_string($subGroup) . '\', Movie=\'' . mysqli_real_escape_string($Movie) . '\', videotype=\'' . mysqli_real_escape_string($videotype) . '\', `hd`=\'' . mysqli_real_escape_string($hd) . '\' WHERE id=' . $id . '';
				mysqli_query($query) or die('Error : ' . mysqli_error());
				$this->ModRecord('Edit Episode #' . $epnumber . ' of id ' . $id);
				echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;width:100%;">Episode Update Completed Successfully.</div>';
			}
			else
			{
				echo '<div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;">Failed: Authorization was wrong.</div>';
			}
		}
        else if ($_POST['method'] == 'MassUploadUpdate' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2)) {
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
		$epnumber = mysqli_real_escape_string($_POST['epnumber']);
		$sid = mysqli_real_escape_string($_POST['sid']);
		$epname = mysqli_real_escape_string(urldecode($_POST['epname']));
		$vidheight = mysqli_real_escape_string($_POST['vidheight']);
		$vidwidth = mysqli_real_escape_string($_POST['vidwidth']);
		$epprefix = mysqli_real_escape_string($_POST['epprefix']);
		$subGroup = mysqli_real_escape_string($_POST['subGroup']);
		$Movie = mysqli_real_escape_string($_POST['Movie']);
		$Remember = NULL; //This gave an annoying undefined index error everytime these fields were unchecked.
		if(isset($_POST['Remember'])){
			$Remember = mysqli_real_escape_string($_POST['Remember']);
		}
		$Changed = NULL;
		if(isset($_POST['Changed'])){
			$Changed = mysqli_real_escape_string($_POST['Changed']);
		}
		$addtime = mysqli_real_escape_string($_POST['date']);
		$videotype =mysqli_real_escape_string( $_POST['videotype']);
		$hd = mysqli_real_escape_string($_POST['hd']);
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
			$epnumber = mysqli_real_escape_string($i);
			if($auto){ //If it's auto, we know that $eptitles is initialized
				$epname = $eptitles[$i];
			}
			$NextEp = $i+1;
			// ADDED: 8/13/14 - robotman321
			// Queries the database to make sure that
			$query = "INSERT INTO `episode` (`sid`, `epnumber`, `epname`, `seriesname`, `vidheight`, `vidwidth`, `subGroup`, `epprefix`, `Movie`, `date`, `videotype`, `uid`, `hd`) VALUES ('$sid', '$epnumber', '$epname', 'unknown', '$vidheight', '$vidwidth', '$subGroup', '$epprefix', '$Movie', '$addtime', '$videotype', '" . $this->UserArray[1] . "', '" . $hd . "')";
			$query;
			$results = mysqli_query($query);
			if(!$results)
			{
				echo 'There was an error processing that request, error num #1001: ' . mysqli_error();
				exit;
			}
			echo '<!--Success--><div align="center" style="color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;width:100%;">Episode #' . $epnumber . ' Added, titled: ' . $epname . ' Added Successfully.<div>';
			// now we check to see if we can use: RecordNotification($sid,$eid)
			$airingCheck = mysqli_query("SELECT episode.id AS epid, series.id AS sid, series.stillRelease, series.fullSeriesName FROM series, episode WHERE series.id = '".$sid."' AND episode.sid = series.id AND episode.epnumber = '".$epnumber."'");
			$ar = mysqli_fetch_array($airingCheck);
			if($ar['stillRelease'] == 'yes')
			{
				// put the notification in the system that the end user needs to check their notifications!
				$this->recordNotification($ar['sid'],$ar['epid']);

				//If a series is still releasing, add the entry to the email database.
				// v1 is the episode id, for matching in the system when an email needs to go out.
				mysqli_query("INSERT INTO email (`id`, `date`, `sid`, `v1`, `v2`) VALUES (NULL,'" . time() . "', '" . $ar['sid'] . "', '" . $ar['epid'] . "', '1');");
			}
			$this->ModRecord('Add Episode #'.$epnumber.' to  '.$ar['fullSeriesName']);

			if($Changed == 'on')
			{
				// changed entry is on, we need to update the uploads board
                $query = "UPDATE uestatus SET `change` = 0 WHERE ID = " . mysqli_real_escape_string($_POST['ueid']);
				mysqli_query($query);
				$this->ModRecord("Removed Notifications for Entry " . $_POST['ueid'] . ' in the Uploads Board');
			}
		}
	}
	private function updatePreSequel($sid, $prequelto, $sequelto)
	{
		if($prequelto != 0)//If the prequel is updated, we update that series sequel to this one.
		{
			$query = 'UPDATE series SET sequelto=\'' . mysqli_real_escape_string($sid) . '\' WHERE id=' . mysqli_real_escape_string($prequelto) . '';
			mysqli_query($query) or die('Error : ' . mysqli_error());
		}
		if($sequelto != 0)//If the sequel is, or also was updated with the prequel, we update that series prequel to this one.
		{
			$query = 'UPDATE series SET prequelto=\'' . mysqli_real_escape_string($sid) . '\' WHERE id=' . mysqli_real_escape_string($sequelto) . '';
			mysqli_query($query) or die('Error : ' . mysqli_error());
		}
	}

	private function recordNotification($sid,$eid)
	{
		mysqli_query("INSERT INTO notifications (uid, date, type, d1, d2, d3) VALUES (NULL, '".time()."', '0', '".$sid."', '".$eid."', 'NULL')");
	}
}
