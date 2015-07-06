<?php
/****************************************************************\
## FileName: watchlist.class.php									 
## Author: Brad Riemann										 
## Usage: Watchlist functions and class.
## Copywrite 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWWatchlist {
	
	//#- Vars -#\\
	var $UserArray, $Host, $StatusArray = array();
	
	//#- Contruct -#\\
	public function __construct($UserArray){
		$this->UserArray = $UserArray;
		
		if($_SERVER['SERVER_PORT'] == 443)
		{
			$this->Host = 'https://d206m0dw9i4jjv.cloudfront.net';
		}
		else
		{
			$this->Host = 'http://img02.animeftw.tv';
			//$this->Host = 'http://d206m0dw9i4jjv.cloudfront.net';
		}
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	//#- Public Functions -#\\
	
	# function Output
	public function Output(){
		if(isset($_GET['node']) && $_GET['node'] == 'seriesview'){
			$this->SeriesView();
		}
		else if(isset($_GET['node']) && $_GET['node'] == 'profileview'){
			$this->ProfileView();
		}
		else if(isset($_GET['node']) && $_GET['node'] == 'subprofileview'){
			$this->SubProfileView();
		}
		else {
			echo 'Try again...';
		}
	}
	
	//#- Private Functions -#\\
	
	# function Query
	private function Query($q){
		$query = mysql_query($q); 
		return $query;
	}
	
	# function SeriesView
	private function SeriesView(){
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
			echo 'Whoops, try again.';
		}
		else {
			$sid = htmlspecialchars($_GET['id']);
			if(isset($_GET['stage']) && $_GET['stage'] == 'before'){
				$result  = mysql_query("SELECT id FROM watchlist WHERE uid = '" . $this->UserArray[1] . "' AND sid = '".mysql_real_escape_string($sid)."'");
				$watchlist_total = mysql_num_rows($result);
				if($watchlist_total == 1){ // we have them now, give them the DENIED access!
					
					// here
					$this->SubProfileView(TRUE);
					//echo '<img src="/images/added_tracker.png" alt="" style="float:left;padding-top:1px;padding-right:3px;" /> <a href="/user">In My WatchList</a>';
				}
				else { // it's not in their watchlist, give them the option to do so
					echo '<img src="' . $this->Host . '/add_tracker.png" alt="" style="float:left;padding-top:1px;padding-right:3px;" /> <a href="#" onClick="$(\'#watchlistseries\').load(\'/scripts.php?view=watchlist&node=seriesview&id='.$sid.'&stage=after\'); return false;">Add to My WatchList</a>';
				}
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'after'){
				$result  = mysql_query("SELECT id FROM watchlist WHERE uid = '".$this->UserArray[1]."' AND sid = '".mysql_real_escape_string($sid)."'");
				$watchlist_total = mysql_num_rows($result);
				if($watchlist_total == 1){ // we have them now, give them the DENIED access!
					//echo '<img src="/images/added_tracker.png" alt="" style="float:left;padding-top:1px;padding-right:3px;" /> <a href="/user">In My WatchList</a>';
					$this->SubProfileView(TRUE);
					
					// here
					
				}
				else { // it's not in their watchlist, so lets add it!
					$query = "INSERT INTO watchlist (`uid`, `date`, `update`, `sid`, `tracker`) VALUES ('".$this->UserArray[1]."', '".time()."', '".time()."', '".mysql_real_escape_string($sid)."', '0')";
					mysql_query($query) or die(mysql_error());	
					//echo '<img src="/images/added_tracker.png" alt="" style="float:left;padding-top:1px;padding-right:3px;" /> <a href="#" onClick="return false;">Added to My WatchList</a>';
					$this->SubProfileView(TRUE);
					
					// here
				}
			}
			else {
				echo 'Go back from whence you came..';
			}
		}
	}
	
	#function ProfileView
	private function ProfileView(){
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
			echo 'There was an error with your Request.';
			//echo $_SERVER['REQUEST_URI'];
		}
		else {
			echo '<div class="fds">My AnimeFTW.tv WatchList</div><br />';
			$uid = htmlspecialchars($_GET['id']);
			$perpage = 20;
			$start = $_GET['page'];
			$link = "/scripts.php?view=watchlist&node=profileview&id=".$uid;
			$divid = 'watchlistprofile';
			if(!is_numeric($_GET['page']) || !isset($_GET['page'])){
				$start = 0;
			}
			if(isset($_GET['del']) && is_numeric($_GET['del'])){
				$did = htmlspecialchars($_GET['del']);
				$did = mysql_real_escape_string($did);
				$query = mysql_query("SELECT id FROM watchlist WHERE id = '".$did."' AND uid = '".$this->UserArray[1]."'");
				$watchlist_total = mysql_num_rows($query);
				if($watchlist_total == 1){ // Delete the record
					mysql_query("DELETE FROM watchlist WHERE id = '".$did."'");
					echo "<div class=\"redmsg\">My WatchList Entry Deleted.</div><br />";
				}
				else {}
			}
			$results = mysql_query("SELECT watchlist.id, watchlist.uid, watchlist.date, watchlist.update, watchlist.sid, watchlist.currentep, watchlist.tracker, watchlist.tracker_latest, watchlist.comment, watchlist_statuses.StatusName FROM watchlist, watchlist_statuses WHERE watchlist.uid = '".mysql_real_escape_string($uid)."' AND watchlist.status=watchlist_statuses.id ORDER BY watchlist_statuses.StatusName DESC, watchlist.date DESC LIMIT ".$start.", ".$perpage);
			$results2 = mysql_query("SELECT watchlist.id, watchlist_statuses.StatusName FROM watchlist, watchlist_statuses WHERE watchlist.uid = '".mysql_real_escape_string($uid)."' AND watchlist.status=watchlist_statuses.id ORDER BY watchlist_statuses.StatusName DESC");
			$total_rows = mysql_num_rows($results2);
			if($total_rows == 0){
				echo "<div class=\"redmsg\">This user does not have any My WatchList entries.</div><br />";
			}
			else {
				$this->InternalPaging($total_rows,$perpage,$start,$link,$divid);
				echo '<br />';
				while(list($id,$uid,$date,$update,$sid,$currentep,$tracker,$tracker_latest,$comment,$StatusName) = mysql_fetch_array($results))
				{
					$this->BuildSeries($id,$sid,$comment,$date,$update,$currentep,$tracker,$tracker_latest,$comment,$StatusName,$uid,$divid,$link);
				}
				$this->InternalPaging($total_rows,$perpage,$start,$link,$divid);
				// the next script will take whatever element is selected and show the ajax page for it so it can do updates..
				if($this->UserArray[1] == $_GET['id'] || ($this->UserArray[2] == 1 || $this->UserArray[2] == 2)){
					echo '<script>
						$(".watchlist-element-icon").click(function() {
							$("#" + $(this).attr(\'id\') + "-ed").load("/scripts.php?view=watchlist&node=subprofileview&stage=before&id=" + $(this).attr(\'id\'));
							$(".watchlist-element-ub").hide(500);
							$("#" + $(this).attr(\'id\') + "-ed").show(500);
						});
						window.onbeforeunload = function() { return "Warning! Make sure you have saved your WatchList Entries before changing the page."; };
					</script>';
				}
			}
		}
	}
		
	# function BuildSeries
	private function BuildSeries($id,$sid,$comment,$date = NULL,$update = NULL,$currentep,$tracker = NULL,$tracker_latest = NULL,$comment,$StatusName,$CurrentUser,$divid,$link){
		if($tracker == NULL || $tracker == 0){
			$results = mysql_query("SELECT series.id, series.fullSeriesName, series.seoname, COUNT(episode.id) AS MaxEps FROM series, episode WHERE series.id = ".$sid." AND episode.sid=series.id");
		}
		else { //counts are based on the tracker entries
			if($tracker_latest == 1){ // latest tracker stats for the episode
				$results = mysql_query("SELECT episode.epnumber FROM episode_tracker, episode WHERE episode.id=episode_tracker.eid AND episode_tracker.uid = '".$CurrentUser."' AND episode_tracker.seriesName = '".$sid."' ORDER BY episode.epnumber DESC LIMIT 0, 1 ");
				$row = mysql_fetch_array($results);
				$currentep = $row['epnumber'];
			}
			else {
				$results = mysql_query("SELECT COUNT(episode.id) as MaxEps FROM episode_tracker, episode WHERE episode.id=episode_tracker.eid AND episode_tracker.uid = '".$CurrentUser."' AND episode_tracker.seriesName = '".$sid."'");
				$row = mysql_fetch_array($results);
				$currentep = $row['MaxEps'];
			}			
			$results = mysql_query("SELECT series.id, series.fullSeriesName, series.seoname, COUNT(episode.id) AS MaxEps FROM series, episode WHERE series.id = ".$sid." AND episode.sid=series.id");
		}
		
		$row = mysql_fetch_array($results);
		$seriesImage = $this->Host . '/seriesimages/'.$row['id'].'.jpg';
		if($comment == ''){$comment = '';}else{$comment = 'Comment: '.$comment.'<br />';}
		if($this->UserArray[1] == $CurrentUser || ($this->UserArray[2] == 1 || $this->UserArray[2] == 2)){
			$editor = '<div id="whitelist_more" class="whitelist_more"><a href="#" onClick="return false;" class="watchlist-element-icon" id="wl-'.$id.'" title="Edit this My WatchList Entry!"><img src="/images/page_edit.png" alt="" /></a>&nbsp;<a href="#" onClick="$(\'#'.$divid.'\').load(\''.$link.'&del='.$id.'\'); return false;"><img src="/images/tinyicons/cancel.png" alt="" title="Delete this My WatchList entry!" /></a></div>';
			$DropDownInfo = '<br /><br /><br /><div id="wl-'.$id.'-ed" class="watchlist-element-ub" style="display:none;">Loading..</div>';
		}
		else {$editor = '';$DropDownInfo = '';}
		echo '<div id="tbl-wl">'.$editor.'<img src="'.$seriesImage.'" alt="" style="float:left;padding:5px;margin:0;" width="50px" /><div style="padding: 5px 5px 5px;"><span style="font-size:16px;"><a href="/anime/'.$row['seoname'].'/">'.stripslashes($row['fullSeriesName']).'</a></span><br />'.stripslashes($comment).' Episodes Watched: '.$currentep.' of '.$row['MaxEps'].'<br /><span>WatchList Status: '.$StatusName.'</span> '.$DropDownInfo.'</div></div><br />';
	}
	
	# function SubProfileView
	private function SubProfileView($TinyMode = NULL){
		if(isset($_GET['stage'])){	
			if($TinyMode == TRUE){
				$sid = $_GET['id'];
				$query = "SELECT id, uid, sid, comment FROM watchlist WHERE sid = ".mysql_real_escape_string($sid)." AND uid = ".$this->UserArray[1];
				$results = mysql_query($query);
				$row = mysql_fetch_array($results);
				$wid = $row['id'];
				$guid = $row['uid'];
				$gsid = $row['sid'];	
			}
			else {
				$wid = $_GET['id'];
				$wid = substr($wid, 3);	
			
				$query = "SELECT uid, sid, comment FROM watchlist WHERE id = ".$wid."";
				$results = mysql_query($query);
				$row = mysql_fetch_array($results);
				$guid = $row['uid'];
				$gsid = $row['sid'];	
			}	
			
			if($row['comment'] != ''){$topdown = 'margin-top:-10px;';}else {$topdown = '';}
			echo '<div id="watchlist-edit-details-'.$wid.'" style="padding-top:5px;'.$topdown.'">';
			
			if($_GET['edit'] == 'true' && $this->UserArray[1] == $row['uid']){ //they pass, let them through
				$status = mysql_real_escape_string($_GET['Status']);
				$UpdateType = mysql_real_escape_string($_GET['UpdateType']);
				if($UpdateType == 1)
				{
					$TrackerLatest = mysql_real_escape_string($_GET['TrackerLatest']);
				}
				else
				{
					$TrackerLatest = 0;
				}
				$Email = mysql_real_escape_string($_GET['Emails']);
				$currentep = mysql_real_escape_string($_GET['Currentep']);
				$Comment = mysql_real_escape_string(urldecode($_GET['Comment']));
				$Comment = htmlspecialchars($Comment);
				$query = "UPDATE `watchlist` SET `update` = '".time()."', `status` = '".$status."', `email` = '".$Email."', `currentep` = '".$currentep."', `tracker` = '".$UpdateType."', `tracker_latest` = '".$TrackerLatest."', `comment` = '".$Comment."' WHERE `watchlist`.`id` = ".$wid;
				//echo $query;
				mysql_query($query);
				echo '<span style="color:#E30707;font-size:10px;" id="update-text">Update Succesful!<br /></span>';
				echo '<script>
				$(function() {
					 setTimeout(function() {
						$(\'#update-text\').fadeOut(1400);
					 },3000);                        
			
				  });
				</script>';
			}		
			else {
			}
			if($TinyMode == TRUE || (isset($_GET['tinymode']) && $_GET['tinymode'] == 'true')){
				echo '<span style="font-size:12px;border-bottom:1px #ccc solid;">My WatchList Entry Settings&nbsp;&nbsp;&nbsp;</span>';
			}
			else {
				echo '<span style="font-size:12px;border-bottom:1px #ccc solid;">Entry Settings&nbsp;&nbsp;&nbsp;</span>';
			}
			
			$query = "SELECT * FROM watchlist WHERE id = ".$wid."";
			$results = mysql_query($query);
			$row = mysql_fetch_array($results);			
			$total_rows = mysql_num_rows($results);
			
			$update = date('M jS, Y g:ia', $row['update']);
			$addded = date('M jS, Y g:ia', $row['date']);
			if($TinyMode == TRUE || (isset($_GET['tinymode']) && $_GET['tinymode'] == 'true')){
			}
			else {
				echo '<div align="right" style="float:right;margin-top:-20px;">';
				echo '<span style="color:#ccc;font-size:10px;">Added on '.$addded.'</span><br />';
				echo '<span style="color:#ccc;font-size:10px;">Last updated on '.$update.'</span><br />';
				echo '</div>';
			}
			echo '<form method="GET" id="submit_wl-'.$wid.'" name="submit_wl-'.$wid.'">';
			echo '<input type="hidden" name="wid" id="wid" value="'.$wid.'" />';
			
			$subquery = mysql_query("SELECT id, StatusName FROM watchlist_statuses ORDER BY StatusName ASC");
			echo '<div style="padding-bottom:5px;"><span style="color:#7A7A7A;font-size:10px;">Entry Status:</span><br />';
			echo '<select name="Status" id="Status">';
			while(list($id,$StatusName) = mysql_fetch_array($subquery)){
				if($row['status'] == $id){
					$selected = ' selected="selected"';
				}
				else {
					$selected = '';
				}
				echo '<option value="'.$id.'"'.$selected.'>'.$StatusName.'</option>';
			}
			echo '</select></div>';
			echo '<div style="padding-bottom:5px;"><span style="color:#7A7A7A;font-size:10px;">Tracker or Manual Updates:</span><span style="font-size:9px;">[<a href="#" onClick="return false;" title="Use the tracker or use a manual update. If you select Tracker, this will automatically populate based on additions from the tracker. If you choose manual, you will need to update this!">?</a>]</span><br />';
			echo '<select name="UpdateType" id="UpdateType">';
			echo '	<option value="0"'; if($row['tracker'] == 0){echo ' selected="selected"';} echo '>Manual</option>';
			echo '	<option value="1"'; if($row['tracker'] == 1){echo ' selected="selected"';} echo '>Tracker</option>';
			echo '</select></div>';
			if($row['tracker'] == 1)
			{
				// The entry is being trcked by the tracker, not by hand.. so we change this..
				$TrackerLatest = $row['tracker_latest'];
				$TLDefaultStyle = 'padding-bottom:5px;';
			}
			else
			{
				// for everything else..
				$TrackerLatest = 0;
				$TLDefaultStyle = 'padding-bottom:5px;display:none;';
			}
			echo '<div style="' . $TLDefaultStyle . '" id="tracker-latest-wrapper"><span style="color:#7A7A7A;font-size:10px;">Tracker Reporting Style:</span><span style="font-size:9px;">[<a href="#" onClick="return false;" title="Use either Cumulative (total) or Latest Tracker Entries to report to your My WatchList Status.">?</a>]</span><br />';
			echo '<select name="TrackerLatest" id="TrackerLatest">';
			echo '	<option value="0"'; if($TrackerLatest == 0){echo ' selected="selected"';} echo '>Cumulative</option>';
			echo '	<option value="1"'; if($TrackerLatest == 1){echo ' selected="selected"';} echo '>Latest</option>';
			echo '</select></div>';	
			echo '<script>
				$(document).ready(function(){                       
					$("#UpdateType").change(function() {						
						if($("#UpdateType").val() == 1)
						{
							$("#tracker-latest-wrapper").show();
						}
						else 
						{
							$("#tracker-latest-wrapper").hide();
						}
					});
				});
				</script>';
			echo '<div style="padding-bottom:5px;"><span style="color:#7A7A7A;font-size:10px;">Current Episode:</span><span style="font-size:9px;">[<a href="#" onClick="return false;" title="This will change based on if you are recording with the Tracker or by hand.">?</a>]</span><br />';
			if($row['tracker'] == 0){
				echo '<input type="text" name="Currentep" id="Currentep" value="'.$row['currentep'].'" size="5" />';
			}
			else {
				echo '<input type="hidden" name="Currentep" id="Currentep" value="'.$row['currentep'].'" />';
				echo '<span style="color:#0CB304;">Tracker logging is Active</span>';
			}
			echo '</div>';
			if($row['tracker'] == 1){
				echo '<div><span style="color:#7A7A7A;font-size:10px;">Episode Tracker Episodes:</span><span style="font-size:9px;">[<a href="#" onClick="return false;" title="There are all the episodes in our episode tracker for this series.">?</a>]</span>
				<div id="TrackerEpisodes">';
				$query = "SELECT episode.epnumber, episode.epname FROM episode_tracker, episode WHERE episode.id=episode_tracker.eid AND episode_tracker.uid = '".$guid."' AND episode_tracker.seriesName = '".$gsid."' ORDER BY episode.epnumber";
				$results = mysql_query($query);
				while(list($epnumber,$epname) = mysql_fetch_array($results)){
					echo "<div class=\"z\">Episode #".$epnumber.", titled: ".$epname."</div>";
				}
				echo '</div><div id="pagingControls" style="padding-bottom:5px;"></div>';
				echo '<script type="text/javascript">				
					var pager = new Imtech.Pager();
					$(document).ready(function() {
						pager.paragraphsPerPage = 8; // set amount elements per page
						pager.pagingContainer = $(\'#TrackerEpisodes\'); // set of main container
						pager.paragraphs = $(\'div.z\', pager.pagingContainer); // set of required containers
						pager.showPage(1);
					});
					</script>';
			}
			if($TinyMode == TRUE || (isset($_GET['tinymode']) && $_GET['tinymode'] == 'true')){
				$tinymode = '&tinymode=true';
			}
			else {
				$tinymode = '';
			}
			echo '<div align="right" style="float:right;"><input type="button" value="Update" name="update" id="update" onclick="$(\'#watchlist-edit-details-'.$wid.'\').load(\'/scripts.php?view=watchlist&node=subprofileview&stage=edit&edit=true&id=wl-'.$wid.$tinymode.'&random=sometime\' + getFormElementValuesAsString(document.forms[\'submit_wl-'.$wid.'\'])); return false;"></div>';
			echo '<div style="padding-bottom:5px;"><span style="color:#7A7A7A;font-size:10px;">Email Updates:</span><span style="font-size:9px;">[<a href="#" onClick="return false;" title="Receive email updates when they become available for this series">?</a>]</span><br />';
			echo '<select name="Emails" id="Emails">';
			echo '	<option value="1"'; if($row['email'] == 1){echo ' selected="selected"';} echo '>Yes</option>';
			echo '	<option value="0"'; if($row['email'] == 0){echo ' selected="selected"';} echo '>No</option>';
			echo '</select></div>';
			echo '<div style="padding-bottom:5px;"><span style="color:#7A7A7A;font-size:10px;">Entry Comments:</span><span style="font-size:9px;">[<a href="#" onClick="return false;" title="Record Notes and Comments about this Entry, visible to all Members.">?</a>]</span><br />';
			if($TinyMode == TRUE || (isset($_GET['tinymode']) && $_GET['tinymode'] == 'true')){
				$CommentWidth = '200px';
			}
			else {
				$CommentWidth = '400px;';
			}
			echo '<textarea rows="2" style="width:'.$CommentWidth.'" id="Comment" name="Comment">'.$row['comment'];
			echo '</textarea></div>';	
			echo '</form>';
			if($TinyMode == TRUE || (isset($_GET['tinymode']) && $_GET['tinymode'] == 'true')){
				echo '<div align="right"><a href="/user">View All of your My WatchList Entries!</a></div>';
			}
		}
		else {
		}
		echo '</div>';
	}

	# function InternalPaging
	private function InternalPaging($count,$perpage,$start,$link,$divid){
		$num = $count;
		$per_page = $perpage; // Number of items to show per page
		$showeachside = 4; //  Number of items to show either side of selected page
		if(empty($start)){$start = 0;}  // Current start position
		else{$start = $start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		$front = "<span>$max_pages Pages</span>&nbsp;";
		if(($start-$per_page) >= 0){
			$next = $start-$per_page;
			$startpage = '<a href="#" onClick="$(\'#'.$divid.'\').load(\''.$link.($next>0?("&page=").$next:"").'\');return false;">&lt;</a>';
		}
		else {$startpage = '';}
		if($start+$per_page<$num){
			$endpage = '<a href="#" onClick="$(\'#'.$divid.'\').load(\''.$link.'&page='.max(0,$start+1).'\');return false;">&gt;</a>';
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
			$class=($y==$start)?"pageselected":"";
			if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
			{
				$middlepage .= '<a id="'.$class.'" href="#" onClick="$(\'#'.$divid.'\').load(\''.$link.($y>0?("&page=").$y:"").'\');return false;">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}
		
		echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}
	
	public function checkSeriesEntry($sid,$add = FALSE)
	{
	
		$this->array_watchListStatuses(); // build the various statuses we can use in a watchlist.
		
		$query = "SELECT `id`, `uid`, `date`, `update`, `sid`, `status`, `email`, `currentep`, `tracker`, `tracker_latest`, `comment` FROM `watchlist` WHERE `sid` = " . mysql_real_escape_string($sid) . " AND `uid` = " . $this->UserArray[1];
		
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		
		if($count > 0 || $add == TRUE)
		{
			
			if($add == TRUE && $count == 0)
			{
				// we want to add a new series to the My WatchList system.
				$query = "INSERT INTO watchlist (`uid`, `date`, `update`, `sid`, `tracker`, `comment`, `status`) VALUES ('".$this->UserArray[1]."', '".time()."', '".time()."', '".mysql_real_escape_string($sid)."', '0', '', '1')";
				$result = mysql_query($query);
				
				 $row = array('id' => mysql_insert_id(), 'uid' => $this->UserArray[1], 'date' => time(), 'update' => time(), 'sid' => $sid, 'status' => '1', 'email' => 1, 'currentep' => 0, 'tracker' => '0', 'tracker_latest' => '', 'comment' => '');
			}
			else
			{
				$row = mysql_fetch_assoc($result);
			}
			$ShowForm = TRUE;
		}
		else
		{
			$ShowForm = FALSE;
		}
		
		// Now we show the form
		if($ShowForm == TRUE)
		{
			echo '
			<form id="watchlist-form">
			<input name="wid" id="wid" value="' . $row['id'] . '" type="hidden" />
			<div>
				<div style="display:inline-block;width:49%;">
					<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Entry Status:</div>
					<div style="font-size:12px;color:#242424;">
						<select name="Status" id="Status" class="loginForm">
						';
						foreach($this->StatusArray AS $Status)
						{
							if($row['status'] == $Status['id'])
							{
								$selected = ' selected="selected"';
							}
							else
							{
								$selected = '';
							}
							echo '<option value="' . $Status['id'] . '" ' . $selected . '>' . $Status['StatusName'] . '</option>';
						} 
						echo '
						</select>
					</div>
				</div>
				<div style="display:inline-block;width:49%;padding-left:2px;">
					<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Tracker or Manuel Updates:</div>
					<div style="font-size:12px;color:#242424;">
						<select name="UpdateType" id="UpdateType" class="loginForm">
							<option value="0"'; if($row['tracker'] == 0){echo ' selected="selected"';} echo '>Manual</option>
							<option value="1"'; if($row['tracker'] == 1){echo ' selected="selected"';} echo '>Tracker</option>
						</select>
					</div>
				</div>
			</div>
			<div style="padding-top:5px;">
				<div style="display:inline-block;width:49%;">
					<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Current Episode:</div>
					<div style="font-size:12px;color:#242424;">';					
					if($row['tracker'] == 1)
					{
						// The entry is being tracked by the tracker, not by hand.. so we change this..
						$TrackerLatest = $row['tracker_latest'];
						$TrackerActiveDiv = '';
						$TrackerInActiveDiv = 'display:none;';
					}
					else
					{
						// for everything else..
						$TrackerLatest = 0;
						$TrackerActiveDiv = 'display:none;';
						$TrackerInActiveDiv = '';
					}
					echo '
						<div id="tracker-active-div" style="' . $TrackerActiveDiv . '">
							<input name="CurrentepHidden" id="CurrentepHidden" value="0" type="hidden" class="loginForm" />
							<span style="color:#0CB304;">Tracker logging is Active</span>
						</div>
						<div id="tracker-inactive-div" style="' . $TrackerInActiveDiv . '">
							<input type="text" style="width:60px;" name="CurrentepActive" id="CurrentepActive" value="'.$row['currentep'].'" size="5" class="loginForm" />
						</div>
					</div>
				</div>
				<div style="display:inline-block;width:49%;padding-left:2px;">
					<div id="tracker-latest-wrapper" style="' . $TrackerActiveDiv . '">
						<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Tracker Reporting Style:</div>
						<div style="font-size:12px;color:#242424;">
							<select name="TrackerLatest" id="TrackerLatest" class="loginForm">
								<option value="0"'; if($TrackerLatest == 0){echo ' selected="selected"';} echo '>Cumulative</option>
								<option value="1"'; if($TrackerLatest == 1){echo ' selected="selected"';} echo '>Latest</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div style="padding-top:5px;">
				<div style="display:inline-block;width:49%;vertical-align:top;">
					<div>
						<div style="font-size:8px;color:#c0c0c0;vertical-align:top;display:inline-block;width:60%;">Email Updates:</div>
						<div style="font-size:12px;color:#242424;">
							<select name="Emails" id="Emails" class="loginForm">
								<option value="1"'; if($row['email'] == 1){echo ' selected="selected"';} echo '>Yes</option>
								<option value="0"'; if($row['email'] == 0){echo ' selected="selected"';} echo '>No</option>
							</select>
						</div> 
					</div>
					<div style="padding-top:5px;' . $TrackerActiveDiv . '" id="tracked-episode-listing">';
					echo $this->buildTrackedEpisodes($this->UserArray[1],$sid);
					echo '
					</div>
				</div>
				<div style="display:inline-block;width:49%;vertical-align:top;padding-left:2px;">
					<div>
						<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Entry Notes:</div>
						<div style="font-size:12px;color:#242424;">
							<textarea style="width:175px;height:70px;font-size:10px;color:gray;" id="Comment" name="Comment" class="loginForm">' . $row['comment'] . '</textarea>
						</div>
					</div>
					<div style="padding-top:5px;">
						<div style="display:inline-block;">
							<input type="submit" name="submit-button" value=" Update " style="font-size:10px;color:black;" id="watchlist-submit-button" />
						</div>
						<div style="display:inline-block;">
							<div style="display:none;" id="watchlist-loading-image"><img src="' . $this->Host . '/loading-mini.gif" alt="" /></div>
							<div style="display:none;" id="watchlist-post-submit"><span style="color:green;">Completed</span></div>
						</div>
					</div>
				</div>
			</div>
			</form>';		
		}
		else
		{
			echo '
			This series in not in your My WatchList.<br />
			<a href="#" onClick="return false;" class="add-to-my-watchlist" id="wl-series-' . $sid . '">Add this series</a> to your My WatchList today!<br />
			Don\'t know what the My WatchList is, or how to use it? Fear not! <a href="/faq/how-to-use-my-watchlist/" target="_blank" title="This link opens in a new window">Click here</a> to learn how the My WatchList can blow your mind.';
		}
	}
	
	public function viewEntryDetails($sid)
	{
		$this->buildTrackedEpisodes($this->UserArray[1],$sid,$_GET['page']);
	}
	
	private function array_watchListStatuses()
	{
		$query = "SELECT `id`, `StatusName` FROM `watchlist_statuses` ORDER BY `StatusName` ASC";
		$result = mysql_query($query);
		
		$i = 0;
		while($row = mysql_fetch_assoc($result))
		{
			$this->StatusArray[$i]['id'] = $row['id'];
			$this->StatusArray[$i]['StatusName'] = $row['StatusName'];
			$i++;
		}
	}
	
	private function buildTrackedEpisodes($uid,$sid,$page = 0,$count = 5)
	{
		echo '
						<div>
							<div style="display:inline-block;width:49%;font-size:8px;color:#c0c0c0;vertical-align:top;">Tracked Episodes:</div>
							<div style="display:inline-block;width:45%;font-size:10px;color:#c0c0c0;vertical-align:top;">';
							$this->trackedEpisodeNav($uid,$sid,$page,$count);
							echo '
							</div>
						</div>
						<div style="font-size:12px;color:#242424;">';
		$query = "SELECT `episode`.`epnumber`, `episode`.`epname`, `episode`.`Movie` FROM `episode_tracker`, `episode` WHERE `episode`.`id`=`episode_tracker`.`eid` AND `episode_tracker`.`uid` = '" . $uid . "' AND `episode_tracker`.`seriesName` = '" .$sid . "' ORDER BY `episode`.`epnumber` LIMIT " . ($page*$count) . ", " . $count . "";
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		
		if($count < 1)
		{
			echo '<div style="font-size:10px;" align="center">Nothing tracked!</div>';
		}
		else
		{
			while($row = mysql_fetch_assoc($result))
			{
				if($row['Movie'] == 1)
				{
					// movie...
					$vidtype = 'Movie';
				}
				else
				{
					$vidtype = 'Ep';
				}
				if(strlen(stripslashes($row['epname'])) > 14)
				{
					$episodename = '<span title="' . stripslashes($row['epname']) . '">' . substr(stripslashes($row['epname']),0,13) . '..</span>';
				}
				else
				{
					$episodename = stripslashes($row['epname']);
				}
				echo '<div style="font-size:10px;">' . $vidtype . ' #' . $row['epnumber'] . ' - ' . $episodename . '</div>'."\n";
			}
		}
		echo '
						</div>';
	}
	
	private function trackedEpisodeNav($uid,$sid,$currentpage,$count)
	{
		//<a href="#">&lt;</a> <a href="#">1</a> <a href="#">2</a> <a href="#">3</a> <a href="#">4</a> <a href="#">&gt;</a>
		$query = "SELECT COUNT(id) AS numrows FROM `episode_tracker` WHERE `uid` = $uid AND `seriesName` = $sid";
		$result = mysql_query($query);
		
		$row = mysql_fetch_assoc($result);
		if($row['numrows'] > 0)
		{
			$totalpages = ceil($row['numrows']/$count);
			
			// if there are more than 4 pages, we build the previous and next buttons.
			if($totalpages > 4)
			{
				if($currentpage > 0)
				{
					$previousbutton = '<a href="#" class="wl-previous-button" id="prev-page" onClick="$(\'#tracked-episode-listing\').load(\'/scripts.php?view=dynamic-load&show=watchlist&stage=view-details&id=' . $sid . '&page=' . ($currentpage-1) . '\');return false;"><span style="font-weight:bold;">&lt;</span></a>';
				}
				else
				{
					$previousbutton = '&lt;';
				}
				if($currentpage < ($totalpages-1))
				{
					$nextbutton = '<a href="#" class="wl-next-button" id="next-page" onClick="$(\'#tracked-episode-listing\').load(\'/scripts.php?view=dynamic-load&show=watchlist&stage=view-details&id=' . $sid . '&page=' . ($currentpage+1) . '\');return false;"><span style="font-weight:bold;">&gt;</span></a>';
				}
				else
				{
					$nextbutton = '&gt;';
				}
				
				if($currentpage > 3)
				{
					$startatpage = $currentpage-2;
				}
				else
				{
					$startatpage = $currentpage;
				}
			}
			else
			{
				if($currentpage <= 2)
				{
					$startpage = $currentpage;
				}
				else
				{
					$startatpage = $currentpage-2;
				}
			}
			
			echo $previousbutton . ' ' . $totalpages . ' pages ' . $nextbutton;
		}
		else
		{
		}
	}
	
	public function processFormData()
	{
		if(!isset($_POST['wid']) || !isset($_POST['Status']) || !isset($_POST['UpdateType']) || !isset($_POST['CurrentepHidden']) || !isset($_POST['CurrentepActive']) || !isset($_POST['TrackerLatest']) || !isset($_POST['Emails']) || !isset($_POST['Comment']))
		{
		}
		else
		{
			$status = mysql_real_escape_string($_POST['Status']);
			$UpdateType = mysql_real_escape_string($_POST['UpdateType']);
			if($UpdateType == 1)
			{
				$TrackerLatest = mysql_real_escape_string($_POST['TrackerLatest']);
				$currentep = 0;
			}
			else
			{
				$TrackerLatest = 0;
				$currentep = mysql_real_escape_string($_POST['CurrentepActive']);
			}
			$Email = mysql_real_escape_string($_POST['Emails']);
			
			$Comment = mysql_real_escape_string(urldecode($_POST['Comment']));
			$Comment = htmlspecialchars($Comment);
			$query = "UPDATE `watchlist` SET `update` = '" . time() . "', `status` = '" . $status . "', `email` = '" . $Email . "', `currentep` = '" . $currentep . "', `tracker` = '" . $UpdateType . "', `tracker_latest` = '" . $TrackerLatest . "', `comment` = '" . $Comment . "' WHERE `id` = '" . mysql_real_escape_string($_POST['wid']) . "' AND `uid` = '" . mysql_real_escape_string($this->UserArray[1]) . "'";
			$result = mysql_query($query);
			echo $query;
		}
	}
}
?>