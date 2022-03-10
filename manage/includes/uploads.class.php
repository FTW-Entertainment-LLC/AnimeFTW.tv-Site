<?php
/****************************************************************\
## FileName: uploads.class.php
## Author: Brad Riemann
## Usage: Provides all Functionality for the Uploads Board
## Copywrite 2011-2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Uploads extends Config {

	//#- Public Functions -#\\
	public function __construct()
	{
		parent::__construct(TRUE);
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
        else if (isset($_GET['subpage']) && $_GET['subpage'] == 'qc') {
            echo '<div class="body-container srow">';
            $this->displayQcList();
            echo '</div>';
        }
        else if (isset($_GET['subpage']) && $_GET['subpage'] == 'mass-update') {
            $this->displayMassUpdates();
        }
		else if(isset($_GET['subpage']) && $_GET['subpage'] == 'action')
		{
			// /ajax.php?node=uploads&subpage=action&action=delete&id=
			if(isset($_GET['action']) && $_GET['action'] == 'delete')
			{
				if(isset($_GET['id']) && is_numeric($_GET['id']) && $this->ValidatePermission(78) == TRUE)
				{
					mysqli_query($conn, "DELETE FROM `uestatus` WHERE `id` = " . mysqli_real_escape_string($conn, $_GET['id'])) or die(mysqli_error());
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
					mysqli_query($conn, "UPDATE uestatus SET `change` = 0 WHERE ID = " . mysqli_real_escape_string($conn, $_GET['id']));
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
			// ADDED: 2015/08/29
			// Checks if a video tech has 5 or more entries in the claimed/encoding/uploading boards at the same time..
			if($this->UserArray[2] == 5 && (strtolower($Status) == 'claimed' || strtolower($Status) == 'encoding' || strtolower($Status) == 'uploading')){
				$query = "SELECT COUNT(id) AS numrows FROM `uestatus` WHERE `user` = " . $this->UserArray[1] . " AND (`status` = 'claimed' OR `status` = 'uploading' OR `status` = 'encoding')";
				$result = mysqli_query($conn, $query);
				if(!$result){
					echo "There was an error while running the query to count entries, please try again.";
					exit;
				}
				$row = mysqli_fetch_assoc($result);
				$Limited = TRUE;
				if($row['numrows'] < 5){
					// they can add a series.
					$Limited = FALSE;
				}
			}
			else {
				$Limited = FALSE;
			}
			$this->BuildRow($Status,$Limited);
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
		echo $this->encodersListing();
		echo '<div id="UploadsTrackerMain">';
		echo '<div>';
        echo '<input type="hidden" class="uploadsCheckbox" name="method" value="MassUploadUpdate">';
		foreach($StatusArray as &$Status)
		{
			if($Status == 'Live' && ($this->UserArray[2] != 1 && $this->UserArray[2] != 2))
			//if($Status == 'Live')
			{
			}
			else {
				// ADDED: 2015/08/29
				// Checks if a video tech has 5 or more entries in the claimed/encoding/uploading boards at the same time..
				if($this->UserArray[2] == 5 && (strtolower($Status) == 'claimed' || strtolower($Status) == 'encoding' || strtolower($Status) == 'uploading')){
					$query = "SELECT COUNT(id) AS numrows FROM `uestatus` WHERE `user` = " . $this->UserArray[1] . " AND (`status` = 'claimed' OR `status` = 'uploading' OR `status` = 'encoding')";
					$result = mysqli_query($conn, $query);
					if(!$result){
						echo "There was an error while running the query to count entries, please try again.";
						exit;
					}
					$row = mysqli_fetch_assoc($result);
					$Limited = TRUE;
					if($row['numrows'] < 5){
						// they can add a series.
						$Limited = FALSE;
					}
				}
				else {
					$Limited = FALSE;
				}
				echo '<div id="' . strtolower($Status) . '-wrapper">';
				$this->BuildRow($Status,$Limited);
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
			else if(action == "update-encoder-filter")
			{
				if(status == "home")
				{
					$("#right-column").load("ajax.php?node=uploads&subpage=home");
				}
				else
				{
					$("#right-column").load("ajax.php?node=uploads&subpage=home&showme=" + status);
				}
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
        $(".checkAllCheckboxes").click(function () {
            $(".uploadsCheckbox").prop(\'checked\', $(this).prop(\'checked\'));
        });
        $(".uploadsCheckbox").click(function() {
            if($(this).is(":checked")) {
                $(".answer").show(300);
            } else {
                $(".answer").hide(200);
            }
        });
		</script>';
		echo '</div>';
		unset($Status);
			$this->Visit();
	}

	private function BuildRow($Status,$Limited)
	{
		// $limit, hardcoded amount of entries per section
		$limit = 30;

		if($Limited == FALSE){
			// not limited...
			$LimitedText = '<a href="#" onClick="UploadsFunction(\'add-series\',\'' . strtolower($Status) . '\'); return false;" title="Add a series to the ' . $Status . ' Section">Add</a>';
		}
		else {
			$LimitedText = '<a href="#" onClick="alert(\'You have 5 entries in Encoding, Uploading or Claimed, finish a series before adding another.\'); return false;" title="Add a series to the ' . $Status . ' Section">Add</a>';
		}
        $manageButton = '';
        $mainHeaderWidth = '820px';
        if ($this->UserArray[2] == 1 || $this->UserArray[2] == 2) {
            $LimitedText .= '&nbsp; <input type="checkbox" class="checkAllCheckboxes">';
            $manageButton = '<div style="display:inline-block;width:70px;"><input type="button" class="massManageButton" value="Manage"></div>';
            $mainHeaderWidth = '720px';
        }

		echo '<div class="section-wrapper" style="width:870px;">';
		echo '<div class="section-header-wrapper" style="width:860px;padding-top:7px;border-bottom:1px solid #e8e8e8;">
				<div align="left" style="display:inline-block;width:' . $mainHeaderWidth . ';font-family:Verdana,Arial,Helvetica,sans-serif;font-size:16px;">' . ucwords($Status) . '</div>
                ' . $manageButton . '
				<div class="add-button" style="float:right;display:inline-block;width:50px;">
					' . $LimitedText . '
				</div>
			</div>';

		echo '<div class="uploads-row-top" style="width:860px;height:14px;padding-bottom:5px;border-bottom:1px solid #99e6ff;">
				<div style="display:inline-block;width:1.860465%">&nbsp;</div>
				<div style="display:inline-block;width:1.860465%">&nbsp;</div>
				<div style="display:inline-block;width:55.9997%;font-size:13px;" align="left">Entry Name</div>
				<div style="display:inline-block;width:7.3023255%;font-size:13px;" align="center">Eps.</div>
				<div style="display:inline-block;width:7.3023255%;font-size:13px;" align="center">Res.</div>
				<div style="display:inline-block;width:13.37209302%;font-size:13px;" align="center">Encoder</div>
				<div style="display:inline-block;width:3.488372%;font-size:13px;" align="center">AniDB</div>
				<div id="change-' . $row[0] . '" style="display:inline-block;width:4.3255813%;" align="center">&nbsp;</div>
			</div>';
		if($Status == 'Done')
		{
			$sort = " ORDER BY series ASC";
		}
		else
		{
			$sort = " ORDER BY `uestatus`.`updated` DESC";
		}
		$navOptions = '';
		if(isset($_GET['showme']))
		{
			if(is_numeric($_GET['showme']))
			{
				$option = ' AND user = ' . mysqli_real_escape_string($conn, $_GET['showme']);
				$navOptions = '&showme=' . $_GET['showme'];
			}
			else
			{
				$option = ' AND user = ' . $this->UserArray[1];
				$navOptions = '&showme=' . $_GET['showme'];
			}
		}
		else
		{
			if(isset($_GET['search']) && $_GET['search'] == 'encoder'){
				if(isset($_GET['for']) && is_numeric($_GET['for'])){
					$option = ' AND `user` = ' . mysqli_real_escape_string($conn, $_GET['for']);
					$navOptions = '&search=encoder&for=' . $_GET['for'];
				}
				else {
					# it was not numeric, we will need to do a search of the system for the user.
					$query = "SELECT `ID` FROM `users` WHERE `Username` LIKE '" . mysqli_real_escape_string($conn, $_GET['for']) . "'";
					$result = mysqli_query($conn, $query);
					if(!$result){
					}
					else {
						$count = mysqli_num_rows($result);
						if($count > 0){
							$row = mysqli_fetch_assoc($result);
							$option = ' AND `user` = ' . $row['ID'];
							$navOptions = '&search=encoder&for=' . $row['ID'];
						}
					}
				}
			}
			else if(isset($_GET['search']) && $_GET['search'] == 'series') {
				if(isset($_GET['for'])){
					$option = ' AND (`series` LIKE \'%' . mysqli_real_escape_string($conn, $_GET['for']) . '%\' OR `prefix` LIKE \'%' . mysqli_real_escape_string($conn, $_GET['for']) . '%\')';
					$navOptions = '&search=series&for=' . $_GET['for'];
				}
			}
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
				$Project = stripslashes($row[1]);
				$Preffix = $row[2];
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
                // Mod actions checkbox.
				$ChangedEntry = '';
				$ModActionChange = '&nbsp;';
				if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
				{
                    $ModActionChange = '<div style="display:inline-block;width:48.99995%;" align="center"><input type="checkbox" name="uploads-entry-checkbox[]" class="uploadsCheckbox" value="' . $row[0] . '"></div><div align="center" style="display:inline-block;width:48.99995%;">';
                    if ($row[11] == 1) {
				        $ChangedEntry = 'background-color:#2ed51c';
                        //the settings for the mod action, this allows for a mod to mark a series as having been addressed.
                        $ModActionChange .= '<a href="#" class="remove-notification" onClick="RemoveNotification(' . $row[0] . '); return false;" title="Click here to remove the notification for this entry."><img src="//animeftw.tv/images/management/accept.png" alt="" style="padding-left:9px;width:13px;padding-top:1px;" /></a>';
                    }
                    $ModActionChange .= '</div>';

				}

                $extrastyle = $i % 2 ? 'background-color:#99e6ff;' : 'background-color:#e8e8e8;';
                $AniDBLink = $row[9] == 0 ? '<span style="color:#dddbdb;" title="No AniDB Link at this time">AniDB</span>' : '<a title="Clicking this link will open a new Tab/Window" href="http://anidb.net/perl-bin/animedb.pl?show=anime&aid=' . $row[9] . '" target="_blank">AniDB</a>';
			    $MyEntry = $row[7] == $this->UserArray[1] ? '<img src="//animeftw.tv/images/myentry-star.png" alt="" style="height:14px;" />' : '';

				echo '<div style="' . $extrastyle . '" id="uploads-' . $row[0] . '">';
				echo '<div class="uploads-row-wrapper" style="width:870px;padding:5px 0 0 0;' . $ChangedEntry . '" id="sub-uploads-' . $row[0] . '">';
				echo '<div class="uploads-row-top" style="width:100%;min-height:12px;padding-bottom:5px;">
						<div style="display:inline-block;width:1.860465%;vertical-align:top;"><a href="#" onClick="ToggleRows(' . $row[0] . '); return false;">+</a></div>
						<div style="display:inline-block;width:1.860465%;vertical-align:top;">' . $MyEntry . '</div>
						<div style="display:inline-block;width:54.9997%;vertical-align:top;" align="left">' . $overdue . '<span title="' . stripslashes($row[1]) .'">' . $Project . '</span></div>
						<div style="display:inline-block;width:7.3023255%;vertical-align:top;" align="center">' . stripslashes($row[3]) . '</div>
						<div style="display:inline-block;width:7.3023255%;vertical-align:top;" align="center">' . stripslashes($row[5]) . '</div>
						<div style="display:inline-block;width:13.37209302%;vertical-align:top;" align="center">' . $this->formatUsername($row[7],'blank') . '</div>
						<div style="display:inline-block;width::3.488372%;vertical-align:top;" align="center">' . $AniDBLink . '</div>
						<div id="change-' . $row[0] . '" style="display:inline-block;width:5.3255813%;vertical-align:top;" align="center"><div style="width:100%;">' . $ModActionChange . '</div></div>
					</div>';
				echo '<div id="uploads-row-' . $row[0] . '" class="uploads-row-bottom" style="display:none;width:100%;padding-bottom:5px;" align="left">&nbsp;
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
		$result = mysqli_query($conn, $query);

		$count = mysqli_num_rows($result);
		echo '<div style="padding:5px;">';
		$this->pagingV1(strtolower($Status) . '-wrapper',$count,$limit,$page,'/manage/ajax.php?node=uploads' . $navOptions . '&subpage=load-more-entries&status=' . strtolower($Status));
		echo '</div>';
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
			$sid = $row['sid'];
            $hdResolution = $row['hd'];
            $seriesStatus = $row['airing'];
			$ExtraSettings .= '<input type="hidden" id="ueid" class="method" value="'.$row['ID'].'" name="ueid" />';
		}
		else if($Type == 'add')
		{
			$Status = $_GET['section'];
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsAddition" name="method" />';
			$SubmitButtonTxt = 'Add Entry';
			if(isset($_GET['add-type']) && $_GET['add-type'] == 1 && isset($_GET['to-reencode']))
			{
				$query = "SELECT `seriesName`, `fullSeriesName` FROM series WHERE id = " . mysqli_real_escape_string($conn, $_GET['to-reencode']);
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$Series = '[Reencode] ' . stripslashes($row[1]);
				$Prefix = $row[0];$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';$sid = 0;$hdResolution=0;$seriesStatus=0;
			}
			else
			{
				$Series = '';$Prefix = '';$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';$sid = 0;$hdResolution=0;$seriesStatus=0;
			}
		}
		else
		{
			$Status = 'claimed';
			$ExtraSettings = '<input type="hidden" id="method" class="method" value="UploadsAddition" name="method" />';
			$SubmitButtonTxt = 'Add Entry';
			$Series = '';$Prefix = '';$episodesdoing = '';$episodetotal = '';$width = '';$height = '';$SeriesType = 'series';$anidb = '';$Fansub = '';$hdResolution=0;$seriesStatus=0;
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
                <div align="center">Note: Do NOT enter the top resolution in the Series Name field anymore, this value has been broken out into the "Top Resolution" Field.</div>
				<table width="720" border="0" cellpadding="2" cellspacing="1" align="center">
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
					<td width="150" class="fontcolor"><b>Top Resolution</b><br /></td>
					<td>
						<input type="radio" name="hdresolution" value="0" id="480p-resolution"'; if ($hdResolution == 0){echo ' checked';} echo'> <label for="480p-resolution">480p</label>
                        <input type="radio" name="hdresolution" value="1" id="720p-resolution"'; if ($hdResolution == 1){echo ' checked';} echo'> <label for="720p-resolution">720p</label>
                        <input type="radio" name="hdresolution" value="2" id="1080p-resolution"'; if ($hdResolution == 2){echo ' checked';} echo'> <label for="1080p-resolution">1080p</label>
					</td>
				</tr>
				<tr>
					<td width="150" class="fontcolor"><b>Entry Type</b></td>
					<td>
						<select name="Type" style="color: #000000;">
							<option value="series"'; if($SeriesType == 'series'){echo ' selected="selected"';} echo '>Series</option>
							<option value="movie"'; if($SeriesType == 'movie'){echo ' selected="selected"';} echo '>Movie</option>
							<option value="ova"'; if($SeriesType == 'ova'){echo ' selected="selected"';} echo '>Ova</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="150" class="fontcolor"><b>Series Status</b></td>
					<td>
						<input type="radio" name="seriesstatus" value="0" id="completed-series"'; if ($seriesStatus == 0){echo ' checked';} echo'> <label for="completed-serie">Completed</label>
    					<input type="radio" name="seriesstatus" value="1" id="airing-series"'; if ($seriesStatus == 1){echo ' checked';} echo'> <label for="airing-series">Airing</label>
					</td>
				</tr>
				<tr>
					<td width="150" class="fontcolor"><b>Status</b></td>
					<td>
                    <input type="hidden" name="Previous-Status" id="Previous-Status" value="' . $Status . '" />';
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
						$(\'label.FormError\').hide();
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
		$results = mysqli_query($conn, $query);

		if(!$results)
		{
			echo 'There was an error with the query.';
			exit;
		}
		$count = mysqli_num_rows($results);
        echo '<div id="uploads-global-wrapper">';
		echo '<div align="center"><b>Notices</b>:<br />- Encoders may ONLY have five(5) series at any one time under Claimed, Encoding or Uploading. To much claiming and not enough doing has resulted in this restriction.<br />- If you are working on an airing series, it is YOUR job to make sure it is up to date, if you cannot get the encode done a certain week, let management know, so we can cover it.</div>';
		if (isset($_GET['subpage']) && $_GET['subpage'] == 'qc') {
            echo'
		<div style="display:inline-block;width:200px;vertical-align:top;">
			<div align="left">
				<span style="font-size:9px;">Launch Main Uploads Interface</span><br />
                <a href="#" id="uploads-home-button">Click Here</a>
			</div>
		</div>';
        } else {
            if ($this->UserArray[2] == 1 || $this->UserArray[2] == 2) {
                echo '
            <div style="display:inline-block;width:120px;vertical-align:top;">
                <div align="left">
                    <span style="font-size:9px;">Filter by Encoder:</span><br />
                    <select id="filter-by-encoder" style="color: #000000;">
                        <option value="home">All Encodes</option>
                        <option value="' . $this->UserArray[1] . '"'; if(isset($_GET['showme']) && $_GET['showme'] == $this->UserArray[1]){echo'selected="selected"';} echo '>My Encodes</option>';
                while($row = mysqli_fetch_assoc($results))
                {
                    if(isset($_GET['showme']) && $_GET['showme'] == $row['ID']){
                        echo '<option value="' . $row['ID'] . '" selected="selected">' . $row['Username'] . '</option>';
                    }
                    else {
                        echo '<option value="' . $row['ID'] . '">' . $row['Username'] . '</option>';
                    }
                }
                $seriesSearch = '';
                $clearMessage = FALSE;
                if(isset($_GET['search']) && $_GET['search'] == 'series'){
                    $seriesSearch = $_GET['for'];
                    $clearMessage = TRUE;
                }
                $encoderSearch = '';
                if(isset($_GET['search']) && $_GET['search'] == 'encoder'){
                    $encoderSearch = $_GET['for'];
                    $clearMessage = TRUE;
                }
                echo '
                        </select>
                    </div>
                </div>
                <div style="display:inline-block;width:200px;vertical-align:top;">
                    <div align="left">
                        <span style="font-size:9px;">Search by Series Name:</span>
                        <form id="series-search-form">
                            <input type="text" id="series-search" name="series-search" value="' . $seriesSearch . '" style="width:100px;" class="text-input" />
                            <input type="submit" value="Submit" id="series-form-submit" />
                        </form>
                    </div>
                </div>
                <div style="display:inline-block;width:200px;vertical-align:top;">
                    <div align="left">
                        <span style="font-size:9px;">Search by Encoder:</span>
                        <form id="encoder-search-form">
                            <input type="text" id="encoder-search" name="encoder-search" value="' . $encoderSearch . '" style="width:100px;" class="text-input" />
                            <input type="submit" value="Submit" id="encoder-form-submit"  />
                        </form>
                    </div>
                </div>';
            }
        }
        if ($this->UserArray[2] == 1 || $this->UserArray[2] == 2 || $this->UserArray[2] == 5) {
            echo'
		<div style="display:inline-block;width:200px;vertical-align:top;">
			<div align="left">
				<span style="font-size:9px;">Launch QC Interface</span><br />
                <a href="#" id="qc-interface-link">Click Here</a>
			</div>
		</div><br />';
        }
		if($clearMessage == TRUE){
		echo '
		<div style="display:inline-block;width:100;vertical-align:top;"><br/>
			<span>
			<a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=uploads&subpage=home\'); return false;">Clear Search</a>
			</span>
		</div>';
		}
		echo '
		<script>
		$(document).ready(function() {
			$(\'.text-input\')
				.css({border:"1px solid #CCC"})
				.css({color:"#5A5655"})
				.css({font:"13px Verdana,Arial,Helvetica,sans-serif"})
				.css({padding:"2px"})
			;
			$("#filter-by-encoder").change(function() {
				var user_id = $(this).val();
				UploadsFunction(\'update-encoder-filter\',user_id);
				return false;
			});
			$("#encoder-search-form").on("submit",function(){
				$("#encoder-search").css("border-color","");
				var encoder_val = $("#encoder-search").val();
				if(encoder_val == ""){
					$("#encoder-search").css("border-color","red").focus();
				}
				else {
					$("#right-column").load("ajax.php?node=uploads&subpage=home&search=encoder&for=" + encoder_val);
				}
				return false;
			});
			$("#series-search-form").submit(function(){
				$("#series-search").css("border-color","");
				var series_val = $("#series-search").val();
				if(series_val == ""){
					$("#series-search").css("border-color","red").focus();
				}
				else {
					$("#right-column").load("ajax.php?node=uploads&subpage=home&search=series&for=" + series_val);
				}
				return false;
			});
			$("#qc-interface-link").on("click",function(){
				var encoder_val = $("#encoder-search").val();
                $("#right-column").load("ajax.php?node=uploads&subpage=qc");
				return false;
			});
			$("#uploads-home-button").on("click",function(){
                $("#right-column").load("ajax.php?node=uploads");
				return false;
			});
            $(".massManageButton").click(function() {
                $.ajax({
                    type: "POST",
                    url: "ajax.php?node=uploads&subpage=mass-update",
                    data: $(\'input.uploadsCheckbox\').serialize(),
                    success: function(html) {
                        $(\'#UploadsTrackerMain\').show().html(html);
                    }
                });
                return false;
            });
		});
		</script>';
	}

	private function availableSeries($sid = 0)
	{
		$query = "SELECT id, fullSeriesName FROM series ORDER BY fullSeriesName";
		$results = mysqli_query($conn, $query);

		if(!$results)
		{
			echo 'There was an error with the query ' . mysqli_error();
			exit;
		}

		$data = '<select id="sid" name="sid">
		<option value="0"> Select a Series</option>';
		if($this->ValidatePermission(22) == TRUE)
		{
			$data .= '<option value="0"> Add a Series </option>';
		}

		while($row = mysqli_fetch_array($results))
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
		//mysqli_query($conn, "UPDATE users SET UploadsVisit = '".time()."' WHERE ID = ".$this->UserArray[1]);
	}

	private function array_entryStatus()
	{
		$dbname = $this->SingleVarQuery("SELECT DATABASE()","DATABASE()");
		$query = "SELECT `value` FROM `".$dbname."`.`settings` WHERE `id` = 12";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		$EntryStatus = explode("|", $row['value']);
		return $EntryStatus;
	}

    private function displayQcList() {
        $url = "http://videos.animeftw.tv/users/list-files.php";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result=curl_exec($ch);

        curl_close($ch);

        $returnArray = json_decode($result, true);

        echo $this->encodersListing();

        echo "<table border=\"0\">\n";
        echo "<thead>\n";
        echo "<tr><th>Series</th></tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
        foreach ($returnArray as $subArray) {
            if (is_array($subArray)) {
                foreach ($subArray as $folderName => &$valueArray) {
                    if ($folderName != 'dev') {
                        echo "<tr>\n";
                        echo "<td>\n";
                        echo "<table>";
                        echo "<tr><th>${folderName}</th></tr>\n";
                        foreach ($valueArray as $file) {
                            echo "<tr><td><div><a id=\"file-" . str_replace('.', '-', $file) . "\" href=\"//videos.animeftw.tv/users/fay/${folderName}/${file}\" target=\"_blank\" class=\"view-file\">${file}<a/></div>
                            <div id=\"video-" . str_replace('.', '-', $file) . "\" style=\"display:none;\" class=\"video-player-div\"><video controls preload=\"none\" width=\"848\" height=\"480\" poster=\"//img03.animeftw.tv/video-images/noimage.png\"><source src=\"https://videos.animeftw.tv/users/fay/${folderName}/${file}\" type=\"video/mp4\" \></video></div></td></tr>\n";
                        }
                        echo "</table>";
                        echo "</td>\n";
                        echo "</tr>\n";
                    }
                }
            }
        }
        echo "</tbody>\n";
        echo "</table>\n";
        echo '
        <script>
            $(document).ready(function() {
                $(".view-file").on("click",function(){
                    $(".video-player-div").hide();
                    $(".view-file").css("font-weight", "");
                    $(this).css("font-weight", "bold");
                    var this_id = $(this).attr(\'id\').substring(5);
                    $("#video-" + this_id).show();
                    return false;
                });
            });
        </script>';
    }

    private function displayMassUpdates() {
        if ($this->UserArray[2] == 1 || $this->UserArray[2] == 2) {
            if (!isset($_GET['action'])) {
                // Array ( [method] => MassUploadUpdate [uploads-entry-checkbox] => Array ( [0] => 10 [1] => 9 [2] => 7 [3] => 4 [4] => 5 [5] => 3 [6] => 2 [7] => 6 [8] => 8 [9] => 1 ) )
                // Parse throuugh uploads entries that we will be updating
                // List out the entries for them to understand that will be updated.

                $query = "SELECT `ID`, `series`, `status`, `hd`, `airing` FROM `uestatus` WHERE `ID` in (";
                $entryIds = '';
                foreach ($_POST['uploads-entry-checkbox'] as $key => &$value) {
                    $entryIds .= "$value,";
                }
                $entryIds = rtrim($entryIds,',');
                $query .= $entryIds . ")";

                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo '<div align="center">ERROR: There was a problem executing the requested query.</div>';
                    exit;
                }

                // 1. List series in a table.
                echo '<div class="mass-updates-container">';

                echo '<div class="uploads-row-top" style="width:860px;height:14px;padding-bottom:5px;border-bottom:1px solid #99e6ff;">';
                echo '  <div style="display:inline-block;width:35.99998%;">Entry Name</div>';
                echo '  <div style="display:inline-block;width:15.99998%;">Status</div>';
                echo '  <div style="display:inline-block;width:15.99998%;">Quality Level</div>';
                echo '  <div style="display:inline-block;width:15.99998%;">Video State</div>';
                echo '</div>';
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $hd = '480p';
                    if ($row['hd'] == 1) {
                        $hd = '720p';
                    } else if ($row['hd'] == 2){
                        $hd = '1080p';
                    }
                    $airing = 'Completed';
                    if ($row['airing'] == 1) {
                        $airing = 'Airing';
                    }

    				if ($i % 2) {
    					echo '<div style="background-color:#99e6ff;">';
    				} else {
    					echo '<div style="background-color:#e8e8e8;">';
    				}
    				echo '<div class="uploads-row-wrapper" style="min-width:870px;padding:2px 0 2px 0;" id="sub-uploads-' . $row['ID'] . '">';
    				echo '<div class="uploads-row-top" style="width:860px;min-height:14px;padding-bottom:5px;">';
                    echo '  <div style="display:inline-block;width:35.99998%;">' . $row['series'] . '</div>';
                    echo '  <div style="display:inline-block;width:15.99998%;">' . $row['status'] . '</div>';
                    echo '  <div style="display:inline-block;width:15.99998%;">' . $hd . '</div>';
                    echo '  <div style="display:inline-block;width:15.99998%;">' . $airing . '</div>';
                    echo '</div>
                    </div>
                    </div>';
                    $i++;
                }
                echo '</div>';
                echo '<div align="center" style="padding-top:10px;">Choose an option below to update the above entries.</div>';
                echo '<div class="option-selection-container" style="width:100%">';
                echo '  <div align="center" style="padding-top:5px;">';
                echo '      <input type="hidden" id="entry-ids" value="' . $entryIds . '">';
                echo '      <div style="display:inline-block;width:30.33332%;background-color:#99e6ff;vertical-align:top;height:100%;padding:5px;">';
                echo '          <span>Move to a different Status</span>';
                echo '          <select id="status-change">';
                echo '              <option>Choose a new Status</option>';
                foreach ($this->displayUploadsStatus() as $status) {
                    echo '              <option value="' . $status . '">' . $status . '</option>';
                }
                echo '          </select>';
                echo '      </div>';
                echo '      <div style="display:inline-block;width:30.33332%;background-color:#e8e8e8;vertical-align:top;height:100%;padding:5px;">';
                echo '          <span>Change Quality Level</span><br>';
                echo '          <select id="quality-selector">';
                echo '              <option>Quality level</option>';
                echo '              <option value="0">480p</option>';
                echo '              <option value="1">720p</option>';
                echo '              <option value="2">1080p</option>';
                echo '          </select>';
                echo '      </div>';
                echo '      <div style="display:inline-block;width:30.33332%;background-color:#99e6ff;vertical-align:top;height:100%;padding:5px;">';
                echo '          <input type="button" id="generate-json-data" value="Generate Ray JSON data"><br>(Verify data prior to using.)';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
                echo '<div align="center" style="padding-top:10px;">Result output will be displayed below.</div>';
                echo '  <div id="output-container" style="width:100%">';
                echo '  </div>';
                echo '</div>';
                echo '
                <script>
                    var uploads_ids = $("#entry-ids").val();
                    $("#generate-json-data").on("click", function() {
                        $.get("ajax.php?node=uploads&subpage=mass-update&action=generate-json-data&entries=" + uploads_ids, function(data) {
                            $("#output-container").html(data);
                        });
                    });
                    $("#quality-selector").on("change", function() {
                        var selectedOption = $("#quality-selector option:selected").val();
                        $.get("ajax.php?node=uploads&subpage=mass-update&action=change-quality-level&entries=" + uploads_ids + "&option=" + selectedOption, function(data) {
                            $("#output-container").html(data);
                        });
                    });
                    $("#status-change").on("change", function() {
                        var selectedOption = $("#status-change option:selected").val();
                        $.get("ajax.php?node=uploads&subpage=mass-update&action=change-status&entries=" + uploads_ids + "&option=" + selectedOption, function(data) {
                            $("#output-container").html(data);
                        });
                    });
                </script>';
            } else {
                if (isset($_GET['entries'])) {
                    if (isset($_GET['action']) && $_GET['action'] == 'generate-json-data') {
                        $query = "SELECT `ID`, `series`, `prefix`, `fansub`, `hd` FROM `uestatus` WHERE `ID` in (" . mysqli_real_escape_string($conn, $_GET['entries']) . ")";
                        $result = mysqli_query($conn, $query);

                        if (!$result) {
                            echo 'Error querying for entries.';
                            exit;
                        }
                        echo '<textarea style="width:98.999%;min-height:250px;">';
                        $count = count(explode(',', $_GET['entries']));
                        $i=0;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo $this->generateJsonEntry($row);
                            $i++;
                            if ($i < $count) {
                                echo ',';
                            }
                        }
                        echo '</textarea>';
                    } else if (isset($_GET['action']) && $_GET['action'] == 'change-quality-level') {
                        if (isset($_GET['option'])) {
                            $query = "UPDATE `uestatus` SET `hd` = '" . mysqli_real_escape_string($conn, $_GET['option']) . "' WHERE `ID` in (" . mysqli_real_escape_string($conn, $_GET['entries']) . ")";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                echo 'There was an error executing the query.';
                                exit;
                            }
                            echo '<div style="padding:10px;font-size:16px;" align="center">All Entries were updated successfully.</div>';
                        } else {
                            echo 'Ensure an option is properly selected and try again.';
                        }
                    } else if (isset($_GET['action']) && $_GET['action'] == 'change-status') {
                        if (isset($_GET['option'])) {
                            $option = urldecode($_GET['option']);
                            $query = "UPDATE `uestatus` SET `status` = '" . mysqli_real_escape_string($conn, $option) . "' WHERE `ID` in (" . mysqli_real_escape_string($conn, $_GET['entries']) . ")";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                echo 'There was an error executing the update query.';
                                exit;
                            }
                            echo '<div style="padding:10px;font-size:16px;" align="center">All Entries were updated successfully.</div>';
                        } else {
                            echo 'Ensure an option is properly selected and try again.';
                        }
                    } else {
                        echo 'Warning: Action not defined.';
                    }
                } else {
                    echo 'Entries were not defined, try again.';
                }
            }
        } else {
            echo 'You are not authorized for this function.';
        }
    }

    private function displayUploadsStatus() {
        $query = "SELECT `value` FROM `settings` WHERE `name` = 'all_upload_statuses'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "ERROR processing request for UploadsStatus.";
            exit;
        }

        $row = mysqli_fetch_assoc($result);

        $statuses = explode('|', $row['value']);
        return $statuses;
    }

    private function generateJsonEntry($entryArray) {
        if ($entryArray['hd'] == 2) {
            $quality = '1080';
        } else if ($entryArray['hd'] == 1) {
            $quality = '720';
        } else {
            $quality = '480';
        }
        return '
    {
        "title": "' . $entryArray['series'] . '",
        "prefix": "' . $entryArray['prefix'] . '",
        "regex": ".*' . $entryArray['series'] . ' - (\\\\d\\\\d)(?:v2)?.*.mkv",
        "uploadsID": ' . $entryArray['ID'] . ',
        "quality": ' . $quality . ',
        "finished_episodes": [],
        "finished_encodes": [],
        "notification": "batch",
        "feed": "nyaasi",
        "feeduser": "' . $entryArray['fansub'] . '",
        "feedsearch": "' . $entryArray['series'] . ' ' . $quality . '"
    }';
    }
}