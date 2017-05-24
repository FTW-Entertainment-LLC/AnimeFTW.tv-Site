<?php
/****************************************************************\
## FileName: tracker.class.php									 
## Author: Brad Riemann										 
## Usage: Tracker Class and subfunctions
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

//include('includes/classes/config.class.php');
class AFTWTracker extends Config {
	// Vars	
	var $pp, $tz, $sigim, $host, $ruid, $UserArray;	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/*################*\
	# public functions #
	\*################*/
	public function get_vars($pp,$tz,$ruid){ //per page, for putting everything together
		parent::__construct();
		$this->pp = $pp;
		$this->tz = $tz;
		$this->sigim = array('air','blacklagoon','codegeass','deathnote','elfenlied','exia','ichigo','kamina','loulu','luckystar','naruto','rukia','sasuke','trigun','whentheycry');
		$this->host = $_SERVER['HTTP_HOST'];
		$this->ruid = mysql_real_escape_string(htmlentities($ruid));
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	public function ShowTracker($page)
	{
		if($page == 'signatures')
		{
			$this->BuildSigs();			
			echo '<script>
				$(document).ready(function(){
					$(".row2").click(function() {
						$("#" + this.id).select();
					});
				});
			</script>';
				
		}
		else if($page == 'del')
		{
			$this->DelEntry();
		}
		else
		{
			$this->BuildList();
		}
	}
		
	/*#################*\
	# private functions #
	\*#################*/
	
	private function SQLQuery($query,$type)
	{
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		if($type == 1){ //Needs results NAOW?!
			$result = mysql_result($result , 0);
		}
		else if($type == 0){ //Cause Arrays are BAMF.
			$result = mysql_fetch_array($result);
		}
		else {}
		return $result;
	}
	
	private function DelEntry(){
		if(!$_GET['tid'] || !is_numeric($_GET['tid']))
		{
			echo "<div class=\"redmsg\">Error Deleting Tracker Entry, Try again - ER002</div>";
		}
		else 
		{
			$tid = mysql_real_escape_string($_GET['tid']);
			$q = $this->SQLQuery("SELECT id, uid FROM episode_tracker WHERE id='".$tid."'",0);
			if($q['uid'] == $this->ruid && $this->UserArray[2] != 3)
			{
				echo "<div class=\"redmsg\">Tracker Entry was deleted Successfully.</div>";	
				//echo "DELETE FROM episode_tracker WHERE id = '".$q['id']."'";
				$this->SQLQuery("DELETE FROM episode_tracker WHERE id = '" . mysql_real_escape_string($q['id']) . "'",2);		
			}
			else 
			{
				echo "<div class=\"redmsg\">Error Deleting Tracker Entry, Try again - ER001</div>";
			}
		}
	}
	
	private function BuildList()
	{
		$thispage = "/scripts.php?view=tracker&id=".$this->ruid;
		$tclass = 'tracker2';
		$rs = $this->SQLQuery("SELECT COUNT(id) FROM episode_tracker WHERE uid='" . mysql_real_escape_string($this->ruid) . "'",1);
		if($rs == 0)
		{
			echo "<div class=\"redmsg\">No Episodes have been tracked for this user.</div>";
		}
		else
		{
			echo '<div id="tracker-wrapper">';
			echo '<div align="center" style="padding-bottom:5px;"><a href="#" rel="#profile" onClick="$(\'#tracker-wrapper\').load(\'/scripts.php?view=tracker&id=' . $this->ruid . '\'); return false;">Main View</a> | <a href="#" rel="#profile" onClick="$(\'#tracker-wrapper\').load(\'/scripts.php?view=tracker&id=' . $this->ruid . '&sub=signatures\'); return false;">Signatures View</a></div>';
			if(!isset($_GET['page']))
			{
				$cpage = 0;
			}
			else
			{
				$cpage = $_GET['page'];
			}
			$this->pagingV1("tracker-wrapper",$rs,$this->pp,$cpage,$thispage);
			$q = $this->SQLQuery("SELECT id, eid, dateViewed FROM episode_tracker WHERE uid='".$this->ruid."' ORDER BY dateViewed DESC LIMIT ".$cpage.", ".$this->pp,2);
			echo "<br />";
			while(list($id, $eid, $dateViewed) = mysql_fetch_array($q, MYSQL_NUM))
			{
				$dateViewed = timeZoneChange($dateViewed,$this->UserArray[3]);
				$dateViewed = date("l, F jS, Y, h:i a",$dateViewed);				
				if(($this->ruid == $this->UserArray[1] && $this->UserArray[2] != 3) || ($this->UserArray[2] == 1 || $this->UserArray[2] == 2))
				{
					$delete = '<a href="#" class="delete-entry" id="tracker-entry-' . $id . '"><img src="/images/tinyicons/cancel.png" class="tracker_more" style="float:right;" alt="" title="delete this entry" /></a>';
				}
				else
				{
					$delete = '<a href="#" class="null-delete-entry"><img src="/images/tinyicons/cancel.png" class="tracker_more" style="float:right;" alt="" title="delete this entry" /></a>';
				}
				echo '<div class="'.$tclass.'" id="entry-' . $id . '" style="margin-bottom:10px;">'.$delete.$this->constructEntryDetails($eid,$dateViewed) . '</div>'."\n"; 
			}
			$this->pagingV1("tracker-wrapper",$rs,$this->pp,$cpage,$thispage);
			echo '<br />';
			echo '</div>';
			echo '<script>
				$(document).ready(function(){
					$(".delete-entry").click(function() {
						var r = confirm(\'This will Delete this tracker entry from our servers, are you sure?\');
						if(r == true)
						{
							var this_id = $(this).attr("id").substring(14);
							$.ajax({
								url: "' . $thispage . '&sub=del&tid=" + this_id,
								cache: false
							});
							$("#entry-" + this_id).css("background-color","red").css("color","white").slideUp();
							return false;
						}
						else
						{
						}
						return false;
					});
					$(".null-delete-entry").click(function() {
						alert(\'Deleting Entries from your Tracker is for Advanced Members Only! Sign up today to gain more functionality for your AnimeFTW.tv profile!\');
						return false;
					});
				});
			</script>';
		}
	}
	
	private function BuildSigs()
	{
		echo '<div align="center" style="padding-bottom:5px;"><a href="#" rel="#profile" onClick="$(\'#tracker-wrapper\').load(\'/scripts.php?view=tracker&id=' . $this->ruid . '\'); return false;">Main View</a> | <a href="#" rel="#profile" onClick="$(\'#tracker-wrapper\').load(\'/scripts.php?view=tracker&id=' . $this->ruid . '&sub=signatures\'); return false;">Signatures View</a></div>';
		$u = $this->SQLQuery("SELECT Username FROM users WHERE ID = '".$this->ruid."'",0);
		foreach ($this->sigim as &$value){
			echo $this->CompLink($u['Username'],$value).' <br />';
		}
		unset($this->sigim);
	}
	private function CompLink($u,$im)
	{
		return '<div align="center"><img src="https://'.$this->host.'/images/tracker-sig/'.$im.'/'.$u.'.gif" width="350" height="100"><br />Forum Code<br /><textarea id="tracker-' . $im . '" class="row2" rows="1" cols="80" readonly="readonly">[url=https://'.$this->host.'/user/'.$u.'][img]https://'.$this->host.'/images/tracker-sig/'.$im.'/'.$u.'.gif[/img][/url]</textarea></div>';
	}
	
	private function constructEntryDetails($epid,$date)
	{
		$query = "SELECT `episode`.`epnumber`, `episode`.`epname`, `series`.`seoname`, `series`.`fullSeriesName` FROM `series`, `episode` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id` = $epid";
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		//return checkEpisodeSeriesName($eid).'<br /> Viewed on '.$dateViewed.', Entitled: '.checkEpisode2($eid).'';
		
		return ' Viewed Series <a href="/anime/' . $row['seoname'] . '/" target="_blank">' . $row['fullSeriesName'] . '</a> Episode #' . $row['epnumber'] . ' <br /> Viewed on ' . $date . ', Entitled: <a href="/anime/' . $row['seoname'] . '/ep-' . $row['epnumber'] . '" target="_blank">' . $row['epname'] . '</a>';
	}
	
	public function currentEpisodeAvailability($epid)
	{
		$query = "SELECT `round` FROM `watchlist`, `episode` WHERE `watchlist`.`uid` = " . $this->UserArray[1] . " AND `episode`.`id` = '" . mysql_real_escape_string($epid) . "' AND `watchlist`.`sid` = `episode`.`sid`";
        $result = mysql_query($query);
        if (!$result) {
            $round = 0;
        } else {
            $row = mysql_fetch_assoc($result);
            $round = $row['round'];
        }
        $row = mysql_fetch_assoc($result);
		$query = "SELECT `id`, `dateViewed`, `seriesName` FROM `episode_tracker` WHERE `eid` = $epid AND `uid` = " . $this->UserArray[1] . " AND `round` = " . $round;
		$result = mysql_query($query);
        if ($result) {
            $count = mysql_num_rows($result);
        } else {
            $count = 0;
        }
		
		if ($count == 0) {
			// There is no count, give them the ability to add this to their Tracker.
			echo '
			<div style="font-size:14px;" class="tracker-button">
				<img src="' . $this->Host . '/add_tracker.png" alt="" title="Add This video to your Tracker!" style="float:left;padding-top:1px;padding-right:3px;" />&nbsp;<a href="#" onClick="return false;" style="color:black;" id="episode-' . $epid . '" class="add-to-tracker">Add to the Tracker.</a>
			</div>
			<div style="padding-top:2px;" class="tracker-added-date">
				
			</div>';
		} else {
			$row = mysql_fetch_assoc($result);            
			echo '
			<div style="font-size:14px;" class="tracker-button">
				<img src="' . $this->Host . '/added_tracker.png" alt="" title="This video is already in your Tracker for this Round!" style="float:left;padding-top:1px;padding-right:3px;" />&nbsp;<span>In your Tracker.</span>
			</div>
			<div style="padding-top:2px;" class="tracker-added-date">
				Added ' . date("F jS Y", $row['dateViewed']) . '
			</div>';
		}
		echo '
		<script>
			$(document).ready(function(){
				$(".add-to-tracker").click(function() {
					var this_id = $(this).attr("id").substring(8);
					
					$.ajax({
						url: "/scripts.php?view=tracker&subview=add-entry&id=" + this_id,
						cache: false,
						success: function(response) {
							if(response.indexOf("Success") >= 0){
								$(".tracker-button").html(\'<img src="' . $this->Host . '/added_tracker.png" alt="" title="Addition Successful!" style="float:left;padding-top:1px;padding-right:3px;" />&nbsp;<span>Added Successfully!</span>\');
								$(".tracker-added-date").html(response);
							}
							else
							{
								alert("There was an error trying to process that request.");
							}
						}
					});
					
				});
			});
		</script>
		';
	}
	
	// Records the episode entry.
	public function addTrackerEntry($epid)
	{
        $query = "SELECT `round` FROM `watchlist`, `episode` WHERE `watchlist`.`uid` = " . $this->UserArray[1] . " AND `episode`.`id` = '" . mysql_real_escape_string($epid) . "' AND `watchlist`.`sid` = `episode`.`sid`";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
		$query = "SELECT id FROM episode_tracker WHERE eid = " . mysql_real_escape_string($epid) . ' AND uid = ' . $this->UserArray[1] . ' AND `round` = ' . $row['round'];
		$result = mysql_query($query);
		if($result)
		{
			$count = @mysql_num_rows($result);
		}
		else
		{
			$count = 0;
		}
		if($count == 0 || ($this->UserArray[2] != 3 && $count > 0))
		{
			$query = "INSERT INTO episode_tracker (`uid`, `eid`, `seriesName`, `dateViewed`, `round`) VALUES ('" . $this->UserArray[1] . "', '" . mysql_real_escape_string($epid) . "', (SELECT `sid` FROM episode WHERE `id` = " . mysql_real_escape_string($epid) . "), '" . time() . "', '" . $row['round'] . "')";
			$result = mysql_query($query);
		}
		echo '<!-- Success --> on ' . date("F jS Y");
	}
}

?>