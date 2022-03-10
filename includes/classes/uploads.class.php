<?php
/****************************************************************\
## FileName: uploads.class.php
## Author: Brad Riemann
## Usage: Provides all Functionality for the Uploads Board
## Copywrite 2011-2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

include($_SERVER['DOCUMENT_ROOT'] . "/includes/classes/config.class.php");

class Uploads extends Config {

	//#- Public Functions -#\\
	public function __construct(){
		parent::__construct();
	}

	public function Output()
	{
		if(!isset($_GET['subpage']) || (isset($_GET['subpage']) && $_GET['subpage'] == 'home'))
		{
			$this->BuildList();
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'edit-series')
		{
			$this->SeriesForm('edit');
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'add-series')
		{
			$this->SeriesForm('add');
		}
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'action')
		{
			// /scripts.php?view=uploads&subpage=action&action=delete&id=
			if(isset($_GET['action']) && $_GET['action'] == 'delete')
			{
				if(isset($_GET['id']) && is_numeric($_GET['id']) && $this->ValidatePermission(78) == TRUE)
				{
					mysqli_query($conn, "DELETE FROM uestatus WHERE id = " . mysqli_real_escape_string($conn, $_GET['id'])) or die(mysqli_error());
					$this->Mod("Delete Upload entry, id: " . $_GET['id']);
				}
				else
				{
				}
			}
			else
			{
			}
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
		if(!isset($_GET['subpage']))
		{
			echo '<div align="center"><b>Notices</b>:<br />- Encoders may ONLY have five(5) series at any one time under Claimed, Encoding or Uploading. To much claiming and not enough doing has resulted in this restriction.<br />- If you are working on an airing series, it is YOUR job to make sure it is up to date, if you cannot get the encode done a certain week, let management know, so we can cover it.</div>';
		}
		echo '<div id="UploadsTrackerMain">';
		echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';
		foreach($StatusArray as &$Status)
		{
			if($Status == 'Live' && ($this->UserArray[2] != 1 && $this->UserArray[2] != 2))
			{
			}
			else {
				$this->BuildRow($Status);
			}
		}
		echo '</div></div>';
		echo '
		<script>
		function ToggleRows(id)
		{
			ShowLoading();
			$(".uploads-row-bottom").hide();
			$("#uploads-row-" + id).toggle();
			HideLoading();
			return false;
		}
		function UploadsFunction(action,status)
		{
			ShowLoading();
			if(action == "add-series")
			{
				$("#UploadsTrackerMain").load("/scripts.php?view=uploads&subpage=add-series&section=" + status, HideLoading());
				return false;
			}
			else if(action == "edit-series")
			{
				$("#UploadsTrackerMain").load("/scripts.php?view=uploads&subpage=edit-series&id=" + status, HideLoading());
				return false;
			}
			else if(action == "home")
			{
				$("#UploadsTrackerMain").load("/scripts.php?view=uploads&subpage=home", HideLoading());
				return false;
			}
			else if(action == "add-series-pre")
			{
				$("#UploadsTrackerMain").load("/scripts.php?view=uploads&subpage=add-series&section=" + status, HideLoading());
				return false;
			}
			HideLoading();
			return false;
		}
		function ShowLoading()
		{
			$("#loaderImage")
				.css({visibility:"visible"})
				.css({opacity:"1"})
				.css({display:"block"})
				.css({height:"18px"})
			;
		}
		function HideLoading()
		{
			$("#loaderImage")
				.fadeTo(800, 0)
			;
		}
		function DeleteItem(id)
		{
			ShowLoading();
			var r=confirm("Deleting a Series from the Uploads Board is Permanent, if you wish to proceed click ok.");
			if (r==true)
			{
				// Delete the row
				$.ajax({
					url: "/scripts.php?view=uploads&subpage=action&action=delete&id=" + id,
					cache: false
				});
				$(\'#UploadsTrackerMain\').load(\'/scripts.php?view=uploads&subpage=home\');
			}
			else
			{
				// don`t do anything.
			}
			HideLoading();
			return false;
		}
		</script>';
		echo '</div>';
		unset($Status);
			$this->Visit();
	}

	private function BuildRow($Status)
	{
		echo '<div class="section-wrapper" style="width:620px;">';
		echo '<div class="section-header-wrapper" style="width:620px;border-bottom:1px solid #99e6ff;;padding-top:5px;">
				<div align="left" style="display:inline;width:580px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:16px;">' . $Status . '</div>
				<div class="add-button" style="float:right;display:inline;width:40px;"><a href="#" onClick="UploadsFunction(\'add-series\',\'' . strtolower($Status) . '\'); return false;" title="Add a series to the ' . $Status . ' Section">Add</a></div>
			</div>';
		if($Status == 'Done')
		{
			$sort = " ORDER BY series ASC";
		}
		else
		{
			$sort = "";
		}
		$query = "SELECT ID, series, prefix, episodes, type, resolution, status, user, updated, anidbsid FROM uestatus WHERE status='" . strtolower($Status) . "'" . $sort;
		$results = mysqli_query($conn, $query);
		$num_rows = mysqli_num_rows($results);
		if($num_rows == 0)
		{
			echo '<div class="uploads-message" style="padding-left:20px;font-size:12px;">There are no rows to display for ' . $Status . '</div>';
		}
		else
		{
			$i = 0;
			while($row = mysqli_fetch_array($results, MYSQL_NUM))
			{
				$Encoder = $this->SingleVarQuery('SELECT Username FROM users WHERE ID = ' . $row[7],'Username');
				$Project = stripslashes($row[1]);
				$Preffix = $row[2];
				if(strlen($Project) >= 37){
					$Project = substr($Project,0,37).'&hellip;';
				}
				if(strlen($Preffix) >= 17){
					$Preffix = substr($Preffix,0,17).'&hellip;';
				}
				if(strtotime($row[8]) >= ($this->UserArray[14]))
				{
					$extrastyle = 'background-color:#2ed51c';
				}
				else
				{
					if($i % 2)
					{
						$extrastyle = 'background-color:#99e6ff;';
					}
					else
					{
						$extrastyle = 'background-color:#e8e8e8;';
					}
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
					$MyEntry = '<img src="//animeftw.tv/images/myentry-star.png" alt="" />';
				}
				else
				{
					$MyEntry = '&nbsp;';
				}
				echo '<div class="uploads-row-wrapper" style="width:620px;padding:2px 0 2px 0;'.$extrastyle.'">';
				echo '<div class="uploads-row-top" style="width:600px;height:14px;padding-bottom:5px;">
						<div style="float:left;display:inline;width:10px;"><a href="#" onClick="ToggleRows(' . $row[0] . '); return false;">+</a></div>
						<div style="float:left;display:inline;width:16px;">' . $MyEntry . '</div>
						<div style="float:left;display:inline;width:270px;" align="left"><span title="' . stripslashes($row[1]) .'">' . $Project . '</span></div>
						<div style="float:left;display:inline;width:80px;" align="center">' . stripslashes($row[3]) . '</div>
						<div style="float:left;display:inline;width:80px;" align="center">' . stripslashes($row[5]) . '</div>
						<div style="float:left;display:inline;width:95px;" align="center"><a href="/user/' . $Encoder . '" target="_blank">' . $Encoder . '</a></div>
						<div style="float:left;display:inline;width:30px;" align="center">' . $AniDBLink . '</div>
					</div>';
				echo '<div id="uploads-row-' . $row[0] . '" class="uploads-row-bottom" style="display:none;width:600px;20px;" align="left">&nbsp;
				<div style="float:left;display:inline;width:300px;" align="left"><span title="' . $row[2] .'">Preffix: ' . $Preffix . '</span>, Updated: ' . date("Y-m-d",strtotime($row[8])) . '</div>
				<div style="float:left;display:inline;width:290px;" align="right">';
				if(($row[7] == $this->UserArray[1]) || ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
				{
					echo '<a href="#" onClick="UploadsFunction(\'edit-series\',\'' . $row[0] . '\'); return false;">Edit</a>';
					if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
					{
						if($this->ValidatePermission(78) == TRUE){
							echo ' | <a href="#" onClick="DeleteItem(' . $row[0] . '); return false;">Delete</a>';
						}
						if($this->ValidatePermission(22) == TRUE){
							echo '| <a href="#" onClick="$(\'#uploads-global-wrapper\').load(\'/scripts.php?view=management&u='.$this->UserArray[1].'&node=series&stage=addseries&step=before&ueid=' . $row[0] . '\'); return false;">Add Series</a> ';
						}
						if($this->ValidatePermission(18) == TRUE){
							echo '| <a href="#" onClick="$(\'#uploads-global-wrapper\').load(\'/scripts.php?view=management&u='.$this->UserArray[1].'&node=episodes&edit=add&step=before&ueid=' . $row[0] . '\'); return false;">Add Episode</a> ';
						}
					}
				}
				echo '</div>
				</div>';
				echo '</div>';
				$i++;
			}
		}

		echo '</div>';
	}

	private function SeriesForm($Type = NULL)
	{
		if($Type == 'edit' && isset($_GET['id']))
		{
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsEdit" name="method" />';
			$SubmitButtonTxt = 'Edit Entry';
			$query = 'SELECT * FROM uestatus WHERE ID = ' . mysqli_real_escape_string($conn, $_GET['id']);
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
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
			$ExtraSettings .= '<input type="hidden" id="ueid" class="method" value="'.$row['ID'].'" name="ueid" />';
		}
		else if($Type == 'add')
		{
			$Status = $_GET['section'];
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsAddition" name="method" />';
			$SubmitButtonTxt = 'Add Entry';
			if(isset($_GET['add-type']) && $_GET['add-type'] == 1 && isset($_GET['to-reencode']))
			{
				$query = "SELECT seriesName, fullSeriesName FROM series WHERE id = " . mysqli_real_escape_string($conn, $_GET['to-reencode']);
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$Series = '[Reencode] ' . stripslashes($row[1]);
				$Prefix = $row[0];$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';
			}
			else
			{
				$Series = '';$Prefix = '';$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';
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
				$result = mysqli_query($conn, $query);
				echo '<select id="to-reencode" name="to-reencode" style="color: #000000;">';
				echo '<option id="0" value="0">Select a Series</option> ';
				while(list($id, $fullSeriesName) = mysqli_fetch_array($result, MYSQL_NUM))
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
					if($Status == 'live' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
					{
						$Disabled = '';
						$LiveOption = '<option value="live"'; if($Status == 'live'){$LiveOption .= ' selected="selected"';} $LiveOption .= '>Live</option>';
					}
					else if($Status != 'live' && ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
					{
						$Disabled = '';
						$LiveOption = '<option value="live"'; if($Status == 'live'){$LiveOption .= ' selected="selected"';} $LiveOption .= '>Live</option>';
					}
					else
					{
						$Disabled = ' disabled="disabled"';
						$LiveOption = '';
					}
					echo '<select name="Status" style="color: #000000;">
							<option value="claimed"'; if($Status == 'claimed'){echo ' selected="selected"';} echo '>Claimed</option>
							<option value="encoding"'; if($Status == 'encoding'){echo ' selected="selected"';} echo '>Encoding</option>
							<option value="uploading"'; if($Status == 'uploading'){echo ' selected="selected"';} echo '>Uploading</option>
							<option value="ongoing"'; if($Status == 'ongoing'){echo ' selected="selected"';} echo '>Ongoing</option>
							<option value="done"'; if($Status == 'done'){echo ' selected="selected"';} echo '>Done</option>'.$LiveOption.'</select>
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
						$result = mysqli_query($conn, $query);
						while(list($ID, $Username) = mysqli_fetch_array($result, MYSQL_NUM))
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
			echo '</table>
			</div>';
			echo '<input type="submit" class="SubmitUploadsForm" id="submit" name="submit" value="' . $SubmitButtonTxt . '">';
			echo '&nbsp;<input type="button" id="goback" name="goback" value="Go Home" onClick="ShowLoading(); $(\'#UploadsTrackerMain\').load(\'/scripts.php?view=uploads&subpage=home\'); HideLoading(); return false;" />';
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
							url: "/scripts.php",
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
									$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Update Successful</div>");';
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

	private function Visit()
	{
		mysqli_query($conn, "UPDATE users SET UploadsVisit = '".time()."' WHERE ID = ".$this->UserArray[1]);
	}
}

?>