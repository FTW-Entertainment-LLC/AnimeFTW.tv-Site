<?php
/****************************************************************\
## FileName: uploads.class.php									 
## Author: Brad Riemann										 
## Usage: Provides all Functionality for the Uploads Board
## Copywrite 2011-2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Uploads extends Config {
	
	//#- Public Functions -#\\
	public function __construct(){
		parent::__construct();
		$this->deployUploads();
	}
	
	private function deployUploads()
	{
		if(!isset($_GET['subpage']) || (isset($_GET['subpage']) && $_GET['subpage'] == 'home'))
		{
			echo '<div class="body-container srow">';
			$this->BuildList();
			echo '</div>';
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'edit-series')
		{
			echo '<div class="body-container srow">';
			$this->SeriesForm('edit');
			echo '</div>';
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'add-series')
		{
			echo '<div class="body-container srow">';
			$this->SeriesForm('add');
			echo '</div>';
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'action')
		{
			// /ajax.php?node=uploads&subpage=action&action=delete&id=
			if(isset($_GET['action']) && $_GET['action'] == 'delete')
			{
				if(isset($_GET['id']) && is_numeric($_GET['id']) && $this->ValidatePermission(78) == TRUE)
				{
					mysql_query("DELETE FROM uestatus WHERE id = " . mysql_real_escape_string($_GET['id'])) or die(mysql_error());
					$this->Mod("Delete Upload entry, id: " . $_GET['id']);
				}
				else
				{
				}
			}
			else if(isset($_GET['action']) && $_GET['action'] == 'remove-notification')
			{
				if(!isset($_GET['id']) || (isset($_GET['id']) && !is_numeric($_GET['id'])))
				{
					// id is not set, dont do anything
				}
				else
				{
					mysql_query("UPDATE uestatus SET `change` = 0 WHERE ID = " . mysql_real_escape_string($_GET['id']));
					$this->Mod("Removed Notifications for Entry " . $_GET['id'] . ' in the Uploads Board');
				}
			}
			else
			{
			}
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'load-more-entries')
		{
			// This is a function used by the auto loading system, to load more in line at a time.
			if(isset($_GET['status']))
			{
				$Status = $_GET['status'];
			}
			else
			{
				die("There was an error trying to load the selected section. Please let Brad know this was the request url: " . $_SERVER['REQUEST_URI']);
			}
			$this->BuildRow($Status);
		}
		else
		{
			echo 'The page you requested does not exist... yet..';
		}
	}
	
	//#- Private Functions -#\\
	
	private function BuildList()
	{
		$Statuses = $this->SingleVarQuery("SELECT value FROM settings WHERE name = 'upload_tracker_statuses'","value");
		$StatusArray = preg_split("/\|+/", $Statuses);
		echo '<div id="uploads-global-wrapper">';
		if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
		{
			echo '<div style="float:right;"><input type="button" name="ManualUpdate" onClick="ManualUpdate(); return false" value="Clear Notifications" /></div>';
		}
		echo '<div align="center"><b>Notices</b>:<br />- Encoders may ONLY have five(5) series at any one time under Claimed, Encoding or Uploading. To much claiming and not enough doing has resulted in this restriction.<br />- If you are working on an airing series, it is YOUR job to make sure it is up to date, if you cannot get the encode done a certain week, let management know, so we can cover it.</div>';
		if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
		{
			echo $this->encodersListing();
		}
		echo '<div id="UploadsTrackerMain">';
		echo '<div>';	
		foreach($StatusArray as &$Status)
		{
			if($Status == 'Live' && ($this->UserArray[2] != 1 && $this->UserArray[2] != 2))
			//if($Status == 'Live')
			{
			}
			else {
				echo '<div id="' . strtolower($Status) . '-wrapper">';
				$this->BuildRow($Status);
				echo '</div>';
			}
		}
		echo '</div></div>';
		echo '
		<script>
		function ToggleRows(id)
		{
			$(".uploads-row-bottom").hide();
			$("#uploads-row-" + id).toggle();
			return false;
		}
		function UploadsFunction(action,status)
		{
			if(action == "add-series")
			{
				$("#UploadsTrackerMain").load("ajax.php?node=uploads&subpage=add-series&section=" + status);
				return false;
			}
			else if(action == "edit-series")
			{
				$("#UploadsTrackerMain").load("ajax.php?node=uploads&subpage=edit-series&id=" + status);
				return false;
			}
			else if(action == "home")
			{
				$("#UploadsTrackerMain").load("ajax.php?node=uploads&subpage=home");
				return false;
			}
			else if(action == "add-series-pre")
			{
				$("#UploadsTrackerMain").load("ajax.php?node=uploads&subpage=add-series&section=" + status);
				return false;
			}
			return false;
		}
		function DeleteItem(id)
		{
			var r=confirm("Deleting a Series from the Uploads Board is Permanent, if you wish to proceed click ok.");
			if (r==true)
			{
				// Delete the row
				$.ajax({
					url: "ajax.php?node=uploads&subpage=action&action=delete&id=" + id,
					cache: false
				});
				//$(\'#uploads-global-wrapper\').load(\'ajax.php?node=uploads&subpage=home\');
				$("#uploads-" + id).css("background-color", "red").css("color","white").fadeOut();
			}
			else
			{
				// don`t do anything.
			} 
			return false;
		}
		function ManualUpdate()
		{
			var r=confirm("Are you sure you want to clear out the entry Highlights?");
			if (r==true)
			{
				// Delete the row
				$.ajax({
					url: "ajax.php?node=uploads&subpage=action&action=timestampupdate",
					cache: false
				});
				$(\'#uploads-global-wrapper\').load(\'ajax.php?node=uploads&subpage=home\');
			}
			else
			{
				// don`t do anything.
			} 
			return false;
		}
		function RemoveNotification(id)
		{
			var r=confirm("Are you sure you want to remove the Notification of this Entry? ");
			if (r==true)
			{
				// Delete the row
				$.ajax({
					url: "ajax.php?node=uploads&subpage=action&action=remove-notification&id=" + id,
					cache: false
				});
				$("#sub-uploads-" + id).css("background-color", "");
				$("#change-" + id).html("&nbsp;");
			}
			else
			{
				// don`t do anything.
			} 
			return false;
		}
		</script>';
		echo '</div>';
		unset($Status);
			$this->Visit();
	}
	
	private function BuildRow($Status)
	{
		// $limit, hardcoded amount of entries per section
		$limit = 60;
		
		echo '<div class="section-wrapper" style="width:870px;">';
		echo '<div class="section-header-wrapper" style="width:860px;padding-top:7px;border-bottom:1px solid #e8e8e8;">
				<div align="left" style="display:inline;width:820px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:16px;">' . ucwords($Status) . '</div>
				<div class="add-button" style="float:right;display:inline;width:40px;"><a href="#" onClick="UploadsFunction(\'add-series\',\'' . strtolower($Status) . '\'); return false;" title="Add a series to the ' . $Status . ' Section">Add</a></div>
			</div>';
		
		echo '<div class="uploads-row-top" style="width:860px;height:14px;padding-bottom:5px;border-bottom:1px solid #99e6ff;">
				<div style="display:inline-block;width:10px;">&nbsp;</div>
				<div style="display:inline-block;width:16px;">&nbsp;</div>
				<div style="display:inline-block;width:480px;font-size:13px;" align="left">Entry Name</div>
				<div style="display:inline-block;width:80px;font-size:13px;" align="center">Eps.</div>
				<div style="display:inline-block;width:80px;font-size:13px;" align="center">Res.</div>
				<div style="display:inline-block;min-width:115px;font-size:13px;" align="center">Encoder</div>
				<div style="display:inline-block;width:30px;font-size:13px;" align="center">AniDB</div>
				<div id="change-' . $row[0] . '" style="display:inline-block;width:20px;" align="center">&nbsp;</div>
			</div>';
		if($Status == 'Done')
		{
			$sort = " ORDER BY series ASC";
		}
		else if(strtolower($Status) == 'ongoing')
		{
			$sort = " ORDER BY `uestatus`.`updated` DESC";
		}
		else
		{
			$sort = "";
		}
		if(isset($_GET['showme']))
		{
			if(is_numeric($_GET['showme']))
			{
				$option = ' AND user = ' . mysql_real_escape_string($_GET['showme']);
			}
			else
			{
				$option = ' AND user = ' . $this->UserArray[1];
			}
		}
		else
		{
			$option = '';
		}
		
		// ADDED: 8/21/14 by Robotman321
		// Feature enables the usage of paging across the uploads board. (for each section).
		if(isset($_GET['page']))
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 0;
		}
		
		$query = "SELECT `ID`, `series`, `prefix`, `episodes`, `type`, `resolution`, `status`, `user`, `updated`, `anidbsid`, `sid`, `change` FROM uestatus WHERE status='" . strtolower($Status) . "'" . $option . $sort . ' LIMIT ' . $page . ', ' . $limit;
		$results = mysql_query($query);
		$num_rows = mysql_num_rows($results);
		if($num_rows == 0)
		{
			echo '<div class="uploads-message" style="padding-left:20px;font-size:12px;">There are no rows to display for ' . $Status . '</div>';
		}
		else 
		{
			$i = 0;
			while($row = mysql_fetch_array($results, MYSQL_NUM))
			{
				$Project = stripslashes($row[1]);
				$Preffix = $row[2];
				if(strlen($Project) >= 78)
				{
					$Project = substr($Project,0,75).'&hellip;'; 
				}
				if(strtolower($Status) == 'ongoing' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
				{
					$oneweekago = time()-604800;
					$twoweekago = time()-1209600;
					$threeweekago = time()-1814400;
					$lastupdate = date('l F jS Y',strtotime($row[8]));
					if(strtotime($row[8]) > $twoweekago && strtotime($row[8]) <= $oneweekago)
					{
						// a week old
						$overdue = '<span title="Entry has not been updated in over a week. (' . $lastupdate . ')"><img src="' . $this->Host . '/management/one-week-overdue.png" alt="" /></span>&nbsp;';
					}
					else if(strtotime($row[8]) > $threeweekago && strtotime($row[8]) <= $twoweekago)
					{
						// two weeks overdue
						$overdue = '<span title="Entry has not been updated in over two weeks. (' . $lastupdate . ')"><img src="' . $this->Host . '/management/two-week-overdue.png" alt="" /></span>&nbsp;';
					}
					else if(strtotime($row[8]) < $threeweekago)
					{
						// three weeks or later
						$overdue = '<span title="Entry has not been updated in over three weeks. (' . $lastupdate . ')"><img src="' . $this->Host . '/management/three-week-overdue.png" alt="" /></span>&nbsp;';
					}
					else
					{
						$overdue = '';
					}
				}
				else
				{
					$overdue = '';
				}
				if($row[11] == 1 && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
				{
					$ChangedEntry = 'background-color:#2ed51c';
					$ModActionChange = '<a href="#" class="remove-notification" onClick="RemoveNotification(' . $row[0] . '); return false;" title="Click here to remove the notification for this entry."><img src="/images/management/accept.png" alt="" style="padding-left:9px;width:13px;padding-top:1px;" /></a>'; //the settings for the mod action, this allows for a mod to mark a series as having been addressed.
				}
				else
				{
					$ChangedEntry = '';
					$ModActionChange = '&nbsp;'; //the settings for the mod action, this allows for a mod to mark a series as having been addressed.
				}
				if($i % 2)
				{
					$extrastyle = 'background-color:#99e6ff;';
				}
				else
				{
					$extrastyle = 'background-color:#e8e8e8;';
				}
				if($row[9] == 0)
				{
					$AniDBLink = '<span style="color:#dddbdb;" title="No AniDB Link at this time">AniDB</span>';
				}
				else
				{
					$AniDBLink = '<a title="Clicking this link will open a new Tab/Window" href="http://anidb.net/perl-bin/animedb.pl?show=anime&aid=' . $row[9] . '" target="_blank">AniDB</a>';
				}	
				if($row[7] == $this->UserArray[1])
				{
					$MyEntry = '<img src="/images/myentry-star.png" alt="" style="height:14px;" />';
				}
				else 
				{
					$MyEntry = '&nbsp;';
				}
				echo '<div style="' . $extrastyle . '" id="uploads-' . $row[0] . '">';
				echo '<div class="uploads-row-wrapper" style="width:870px;padding:2px 0 2px 0;' . $ChangedEntry . '" id="sub-uploads-' . $row[0] . '">';
				echo '<div class="uploads-row-top" style="width:860px;height:14px;padding-bottom:5px;">
						<div style="display:inline-block;width:10px;"><a href="#" onClick="ToggleRows(' . $row[0] . '); return false;">+</a></div>
						<div style="display:inline-block;width:16px;">' . $MyEntry . '</div>
						<div style="display:inline-block;width:480px;" align="left">' . $overdue . '<span title="' . stripslashes($row[1]) .'">' . $Project . '</span></div>
						<div style="display:inline-block;width:80px;" align="center">' . stripslashes($row[3]) . '</div>
						<div style="display:inline-block;width:80px;" align="center">' . stripslashes($row[5]) . '</div>
						<div style="display:inline-block;min-width:115px;" align="center">' . $this->formatUsername($row[7],'blank') . '</div>
						<div style="display:inline-block;width:30px;" align="center">' . $AniDBLink . '</div>
						<div id="change-' . $row[0] . '" style="display:inline-block;width:20px;" align="center">' . $ModActionChange . '</div>
					</div>';
				echo '<div id="uploads-row-' . $row[0] . '" class="uploads-row-bottom" style="display:none;width:860px;20px;" align="left">&nbsp;
				<div style="display:inline-block;width:540px;" align="left"><span title="' . $row[2] .'">Preffix: ' . $Preffix . '</span>, Updated: ' . date("Y-m-d",strtotime($row[8])) . '</div>
				<div style="display:inline-block;width:300px;" align="right">';
				if(($row[7] == $this->UserArray[1]) || ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
				{
					echo '<a href="#" onClick="UploadsFunction(\'edit-series\',\'' . $row[0] . '\'); return false;">Edit Entry</a>'; 
					if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
					{
						if($this->ValidatePermission(78) == TRUE)
						{
							echo ' | <a href="#" onClick="DeleteItem(' . $row[0] . '); return false;">Delete Entry</a>';
						}
						if($this->ValidatePermission(22) == TRUE)
						{
							if($row[10] == 0)
							{
								echo '| <a href="#" onClick="$(\'html, body\').animate({ scrollTop: 0 }, \'slow\');$(\'#uploads-global-wrapper\').load(\'ajax.php?node=series&stage=addseries&step=before&ueid=' . $row[0] . '\'); return false;">Add Series</a> ';
							}
							else
							{
								echo '| <a href="#" onClick="$(\'html, body\').animate({ scrollTop: 0 }, \'slow\');$(\'#uploads-global-wrapper\').load(\'ajax.php?node=series&stage=edit&sid=' . $row[10] . '&ueid=0\'); return false;">Edit Series</a> ';
							}
						}						
						if($this->ValidatePermission(18) == TRUE)
						{
							//if the series ID is equal to 0, it has not been assigned a series yet, so you need to add it.
							if($row[10] == 0)
							{
								//echo '| <a href="#" onClick="alert(\'The Series needs to be added to the site before any episodes can be made.\'); return false;">Add Episode</a> ';
								echo '| <span style="color:#dddbdb;" title="This entry is not matched with a Series on the Site, Please edit.">Add Episode</span>';
							}
							else
							{
								echo '| <a href="#" onClick="$(\'html, body\').animate({ scrollTop: 0 }, \'slow\');$(\'#right-column\').load(\'ajax.php?node=episodes&page=add&ueid=' . $row[0] . '\'); return false;">Add Episode</a> ';
							}
						}					
					}
				}
				echo '</div>
				</div>';
				echo '</div>';
				echo '</div>';
				$i++;
			}
		}
		
		$query = "SELECT `ID` FROM `uestatus` WHERE `status`='" . strtolower($Status) . "'" . $option;
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		echo '<div style="padding:5px;">';
		$this->pagingV1(strtolower($Status) . '-wrapper',$count,$limit,$page,'/manage/ajax.php?node=uploads&subpage=load-more-entries&status=' . strtolower($Status));
		echo '</div>';
		echo '</div>';
	}
	
	private function SeriesForm($Type = NULL)
	{
		if($Type == 'edit' && isset($_GET['id']))
		{
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsEdit" name="method" />';
			$SubmitButtonTxt = 'Edit Entry';
			$query = 'SELECT * FROM uestatus WHERE ID = ' . mysql_real_escape_string($_GET['id']);
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$episodes = split("/",$row['episodes']);
			$resolution = split("x",$row['resolution']);
			$episodesdoing = $episodes[0];
			$episodetotal = $episodes[1];
			$width = $resolution[0];
			$height = $resolution[1];
			$Series = $row['series'];
			$Prefix = $row['prefix'];
			$SeriesType = $row['type'];
			$anidb = $row['anidbsid'];
			$Status = $row['status'];
			$user = $row['user'];
			$Fansub = $row['fansub'];
			$sid = $row['sid'];
			$ExtraSettings .= '<input type="hidden" id="ueid" class="method" value="'.$row['ID'].'" name="ueid" />';
		}
		else if($Type == 'add')
		{
			$Status = $_GET['section'];
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsAddition" name="method" />';
			$SubmitButtonTxt = 'Add Entry';
			if(isset($_GET['add-type']) && $_GET['add-type'] == 1 && isset($_GET['to-reencode']))
			{
				$query = "SELECT seriesName, fullSeriesName FROM series WHERE id = " . mysql_real_escape_string($_GET['to-reencode']);
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$Series = '[Reencode] ' . stripslashes($row[1]);
				$Prefix = $row[0];$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';$sid = 0;
			}
			else
			{
				$Series = '';$Prefix = '';$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';;$sid = 0;
			}
		}
		else 
		{
			$Status = 'claimed';
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsAddition" name="method" />';
			$SubmitButtonTxt = 'Add Entry';
			$Series = '';$Prefix = '';$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';
		}
		//echo '<a href="#" onClick="UploadsFunction(\'home\',\'null\'); return false;">Home</a>';
		echo '<br />';
		echo '<div id="form_results" class="form_results">&nbsp;</div>';
		if((!isset($_GET['add-type']) && $_GET['subpage'] == 'add-series') || (isset($_GET['add-type']) && $_GET['add-type'] == 1 && !isset($_GET['to-reencode'])))
		{
			if(isset($_GET['add-type']) && $_GET['add-type'] == 1)
			{
				echo '<div align="center">Now that you have chosen to do a Reencode, please choose from the list below.</div><br />';
				$query = "SELECT id, fullSeriesName FROM series ORDER BY fullSeriesName ASC";
				$result = mysql_query($query);
				echo '<select id="to-reencode" name="to-reencode" style="color: #000000;">';
				echo '<option id="0" value="0">Select a Series</option> ';
				while(list($id, $fullSeriesName) = mysql_fetch_array($result, MYSQL_NUM))
				{
					$fullSeriesName = stripslashes($fullSeriesName);
					echo '<option id="'.$id.'" value="'.$id.'">'.$fullSeriesName.'</option> ';
				}
				echo '</select>';
				echo '<script>
				$("#to-reencode").change(function() {
					var id = $(this).find(\':selected\')[0].id;
					UploadsFunction(\'add-series-pre\',\'' . $_GET['section'] . '&add-type=1&to-reencode=\' + id); 
					return false;
				});
				</script>';
			}
			else
			{
				echo '<div align="center" style="padding:10px;">Please Choose the type of series you will be adding to the Uploads Tracker, if you are reworking a series that is in an older format, please choose "Reencoded Series", if this is a completely new series to the site, use "New Series".</div>';
				echo '<table width="620" border="0" cellpadding="2" cellspacing="1" align="center">';
				echo '<tr>';
				echo '<td><div align="center"><label for="reencode-type" style="color:#000;width:100%;font-size:14px;">Re-Encode a Series?</label></div><div align="center"><input type="radio" name="series-type" value="reencode" id="reencode-type" onChange="UploadsFunction(\'add-series-pre\',\'' . $_GET['section'] . '&add-type=1\'); return false;" /></div></td>';
				echo '<td><div align="center"><label for="new-series-type" style="color:#000;width:100%;font-size:14px;">Add a New Series?</label></div><div align="center"><input type="radio" name="series-type" id="new-series-type" value="new-series" onChange="UploadsFunction(\'add-series-pre\',\'' . $_GET['section'] . '&add-type=0\'); return false;" /></div></td>';
				echo '</tr>';
				echo '</table>';
			}
		}
		else 
		{
			echo '<form method="POST" action="#" id="UploadsForm">';
			echo '
				<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
				<input type="hidden" name="user" value="'.$this->UserArray[1].'" />
				' . $ExtraSettings . '
				<table width="620" border="0" cellpadding="2" cellspacing="1" align="center">
				<tr>
					<td width="150" class="fontcolor"><b>Series Name</b></td>
					<td>
						<input name="Series" type="text" id="Series" size="25" value="'.$Series.'" class="text-input" />
						<label for="Series" id="SeriesError" class="FormError">A Series Name is Required.</label>
					</td>
				</tr>
				<tr> 
					<td width="150" class="fontcolor"><b>Episode Preffix</b></td>
					<td><input name="Prefix" type="text" class="text-input" id="Prefix" size="25" value="'.$Prefix.'" />
						<label for="Prefix" id="PrefixError" class="FormError">The episode prefix is required.</label>
					</td>
				</tr> 
				<tr> 
					<td width="150" class="fontcolor"><b>Episodes</b><br /> </td>
					<td>
						<input name="episodesdoing" type="text" class="text-input" id="episodesdoing" style="width:40px;" value="'.$episodesdoing.'" /> / 
						<input name="episodetotal" type="text" class="text-input" id="episodetotal" style="width:40px;" value="'.$episodetotal.'" />
						<label for="episodesdoing" id="episodesdoingError" class="FormError">The current Episode is Required.</label>
						<label for="episodetotal" id="episodetotalError" class="FormError">The total Episodes are Required.</label>
					</td>
				</tr>
				<tr> 
					<td width="150" class="fontcolor"><b>Resolution</b><br /></td>
					<td>
						<input name="width" type="text" class="text-input" id="width" style="width:40px;" value="'.$width.'" /> x 
						<input name="height" type="text" class="text-input" id="height" style="width:40px;" value="'.$height.'" />
						<label for="width" id="widthError" class="FormError">The Length of the Video is Required.</label>
						<label for="height" id="heightError" class="FormError">The Width of the Video is Required.</label>
					</td>
				</tr>
				<tr> 
					<td width="150" class="fontcolor"><b>Series Type</b></td>
					<td>
						<select name="Type" style="color: #000000;">
							<option value="series"'; if($SeriesType == 'series'){echo ' selected="selected"';} echo '>Series</option>
							<option value="movie"'; if($SeriesType == 'movie'){echo ' selected="selected"';} echo '>Movie</option>
							<option value="ova"'; if($SeriesType == 'ova'){echo ' selected="selected"';} echo '>Ova</option>
						</select>
					</td>
				</tr>				
				<tr> 
					<td width="150" class="fontcolor"><b>Status</b></td>
					<td>';
					$EntryList = '';
					$EntryArray = $this->array_entryStatus();
					foreach($EntryArray AS $key=>$EntryStatus)
					{
						if(strtolower($EntryStatus) == 'live')
						{
							if($Status == 'live' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
							{
								$Disabled = '';
								$EntryList .= '<option value="live"'; 
								if($Status == 'live')
								{
									$EntryList .= ' selected="selected"';
								}
								$EntryList .= '>Live</option>';
							}
							else if($Status != 'live' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
							{
								$Disabled = '';
								$EntryList .= '<option value="live"'; 
								if($Status == 'live')
								{
									$EntryList .= ' selected="selected"';
								}
								$EntryList .= '>Live</option>';
							}
							else
							{
								$Disabled = ' disabled="disabled"';
								$EntryList .= '';
							}
						}
						else
						{
							$EntryList .= '<option value="' . strtolower($EntryStatus) . '"'; 
							if($Status == strtolower($EntryStatus))
							{
								$EntryList .= ' selected="selected"';
							}
							$EntryList .= '>' . $EntryStatus . '</option>';
						}
					}
					echo '<select name="Status" style="color: #000000;">';
					echo $EntryList;
					echo '
						</select>
					</td>
				</tr>
				<tr> 
					<td width="150" class="fontcolor"><b>AniDB ID</b></td>
					<td>
						<input name="anidb" type="text" class="text-input" id="anidb" style="width:40px;" value="'.$anidb.'" />
						<label for="anidb" id="anidbError" class="FormError">The AniDB ID is Required.</label>
					</td>
				</tr>
				<tr> 
					<td width="150" class="fontcolor"><b>Fansub Group</b></td>
					<td>
						<input name="fansub" type="text" class="text-input" id="fansub" style="width:150px;" value="'.$Fansub.'" />
						<label for="fansub" id="fansubError" class="FormError">The Fansub is Required.</label>
					</td>
				</tr>';
			if($Type == 'edit' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
			{
				echo '<tr> 
					<td width="150" class="fontcolor"><b>Encoder</b></td>
					<td>
						<select id="uploader" name="uploader" style="color: #000000;">';
						$query = "SELECT ID, Username FROM users WHERE (Level_access = 1 OR Level_access = 2 OR Level_access = 4 OR Level_access = 5 OR Level_access = 6) ORDER BY Username ASC";
						echo '<option id="'.$user.'" value="'.$user.'">Encoder No longer with us.</option> ';
						$result = mysql_query($query) or die('Error : ' . mysql_error());
						while(list($ID, $Username) = mysql_fetch_array($result, MYSQL_NUM))
						{
							echo '<option id="'.$ID.'" value="'.$ID.'"'; if($ID == $user){echo' selected';} echo '>'.$Username.'</option> ';
						}
						echo '</select>
						<label for="uploader" id="uploaderError" class="FormError">An Encoder is ALWAYS required</label>
					</td>
				</tr>';
			}
			else
			{
				echo '<input type="hidden" name="uploader" value="'.$user.'" />';
			}
			if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
			{
				echo '<tr> 
					<td width="150" class="fontcolor"><b>Series ID</b></td>
					<td>';
					echo $this->availableSeries($sid);
					echo '
					</td>
				</tr>';
			}
			else
			{
				echo '<input type="hidden" name="sid" value="' . $sid . '" />';
			}
			echo '</table>
			</div>';
			echo '<input type="submit" class="SubmitUploadsForm" id="submit" name="submit" value="' . $SubmitButtonTxt . '">';
			echo '&nbsp;<input type="button" id="goback" name="goback" value="Go Home" onClick="$(\'#right-column\').load(\'ajax.php?node=uploads&subpage=home\'); return false;" />';
			if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
			{
				// if the SeriesID is > 0 it means that it has been set (0 by default), so we show the Add episode button, 
				if($sid != 0)
				{
					$AddEpisodeStyle = '';
				}
				else
				{
					$AddEpisodeStyle = ' style="display:none;"';
				}
				echo '&nbsp;<input type="button" id="addepisode" name="addepisode" value="Add Episode" onClick="$(\'html, body\').animate({ scrollTop: 0 }, \'slow\');$(\'#right-column\').load(\'ajax.php?node=episodes&page=add&ueid=' . $row['ID'] . '\'); return false;"' . $AddEpisodeStyle . ' />';
			}
			echo '</form>';
		}
		echo '<script>
				$(function() {
					$(\'.form_results\').hide();
					$(\'.text-input\')
						.css({border:"1px solid #CCC"})
						.css({color:"#5A5655"})
						.css({font:"13px Verdana,Arial,Helvetica,sans-serif"})
						.css({padding:"2px"})
					;
					$(\'.text-input\').focus(function(){
						$(this).css({border:"1px solid #0C90BB"});
					});
					$(\'.text-input\').blur(function(){
						$(this).css({border:"1px solid #CCC"});
					});
					$(".SubmitUploadsForm").click(function() {	
						$(\'.form_results\').slideUp();												
						$(\'label\').hide();
						var Series = $("input#Series").val();
						if (Series == "") {
							$("label#SeriesError").show();
							$("input#Series").focus();
							return false;
						}
						var Prefix = $("input#Prefix").val();
						if (Prefix == "") {
							$("label#PrefixError").show();
							$("input#Prefix").focus();
							return false;
						}
						var episodesdoing = $("input#episodesdoing").val();
						if (episodesdoing == "") {
							$("label#episodesdoingError").show();
							$("input#episodesdoing").focus();
							return false;
						}
						var episodetotal = $("input#episodetotal").val();
						if (episodetotal == "") {
							$("label#episodetotalError").show();
							$("input#episodetotal").focus();
							return false;
						}
						var width = $("input#width").val();
						if (width == "") {
							$("label#widthError").show();
							$("input#width").focus();
							return false;
						}
						var height = $("input#height").val();
						if (height == "") {
							$("label#heightError").show();
							$("input#height").focus();
							return false;
						}
						var anidb = $("input#anidb").val();
						if (anidb == "") {
							$("label#anidbError").show();
							$("input#anidb").focus();
							return false;
						}
						$.ajax({
							type: "POST",
							url: "ajax.php",
							data: $(\'#UploadsForm\').serialize(),
							success: function(html) {
								if(html == \'Success\'){';
								if($Type == 'add')
								{
									echo '
									$(\'#UploadsForm\')[0].reset();
									$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>" + Series + " Added Successfully</div>");';
								}
								else // its an edit.. duh
								{
									echo '
									$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Update Successful</div>");
									if($("#sid").val() > 0)
									{
										$("#addepisode").css("display", "");
									}';
								}
								echo '											
									$(\'.form_results\').delay(8000).slideUp();
								}
								else{
									$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
								}
							}
						});
						return false;
					});
					return false;
				});
				</script>';	
	}
	
	private function encodersListing()
	{
		$query = "SELECT ID, Username FROM users WHERE (Level_access = 1 OR Level_access = 2 OR Level_access = 5) AND ID != " . $this->UserArray[1] . " ORDER BY Username";
		$results = mysql_query($query);
		
		if(!$results)
		{
			echo 'There was an error with the query.';
			exit;
		}
		$count = mysql_num_rows($results);
		
		echo '<div align="left">Encoders:<br /><a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=uploads&subpage=home\'); return false;">All Encodes</a> | 
		<a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=uploads&subpage=home&showme=yes\'); return false;">My Encodes</a> | ';
		$i = 1;
		while($row = mysql_fetch_assoc($results))
		{
			echo '<a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=uploads&subpage=home&showme=' . $row['ID'] . '\'); return false;">' . $row['Username'] . '</a> ';
			if($i < $count)
			{
				echo '| ';
			}
			$i++;
		}
		echo '</div>';
	}
	
	private function availableSeries($sid = 0)
	{
		$query = "SELECT id, fullSeriesName FROM series ORDER BY fullSeriesName";
		$results = mysql_query($query);
		
		if(!$results)
		{
			echo 'There was an error with the query ' . mysql_error();
			exit;
		}
		
		$data = '<select id="sid" name="sid">
		<option value="0"> Select a Series</option>';
		if($this->ValidatePermission(22) == TRUE)
		{
			$data .= '<option value="0"> Add a Series </option>';
		}
		
		while($row = mysql_fetch_array($results))
		{
			if($row['id'] == $sid)
			{
				$data .= '<option value="' . $row['id'] . '" selected="selected">' . $row['fullSeriesName'] . '</option>';
			}
			else
			{
				$data .= '<option value="' . $row['id'] . '">' . $row['fullSeriesName'] . '</option>';				
			}
		}
		$data .= '</select>';
		return $data;
	}
	
	private function Visit()
	{
		//mysql_query("UPDATE users SET UploadsVisit = '".time()."' WHERE ID = ".$this->UserArray[1]);
	}
	
	private function array_entryStatus()
	{
		$dbname = $this->SingleVarQuery("SELECT DATABASE()","DATABASE()");
		$query = "SELECT `value` FROM `".$dbname."`.`settings` WHERE `id` = 12";
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		$EntryStatus = explode("|", $row['value']);
		return $EntryStatus;
	}
}