<?php
/****************************************************************\
## FileName: stats.class.php									 
## Author: Brad Riemann										 
## Usage: Statistics class
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

//include_once('includes/classes/config.class.php');
class AFTWstats extends Config {
	var $type; //Type of stat page
	var $format; //Formatting var
	var $la; //Level Access Var
	var $zone;
	var $UserArray;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
		$this->array_buildSiteSettings();
	}
	
	//gather all of the data...
	function get_type($i){$this->type = $i;}
	function get_format($i){$this->format = $i;}
	function get_la($i){$this->la = $i;}
	function get_zone($i){$this->zone = $i;}
	
	//Basic usage stats, for the right column
	public function UsageStats()
	{
		$query = "SELECT `id`, `name`, `content` FROM `stats` WHERE id >= 1 OR id <= 18";
		$results = mysql_query($query);
		if(!$results)
		{
			echo 'There were errors with the setup.';
			exit;
		}
		
		$stats = array();
		while($row = mysql_fetch_array($results))
		{
			$stats[$row['id']]['name'] = $row['name'];
			$stats[$row['id']]['content'] = $row['content'];
		}
		
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Site Statistics</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<div class="rstats">
			<div style="padding:2px;"> - ' . $stats[3]['content'] . ' Series.</div>
			<div style="padding:2px;"> - ' . $stats[4]['content'] . ' Episodes Online.</div>
			<div style="padding:2px;"> - ' . $stats[16]['content'] . ' Registered users.</div>
			<div style="padding:2px;"> - ' . $stats[18]['content'] . ' Episodes Tracked.</div>
			<div style="padding:2px;"> - ' . $stats[2]['content'] . ' Episode Comments.</div>
			<div style="padding:2px;"> - ' . $stats[5]['content'] . ' minutes of video.</div>
			<div style="padding:2px;"> - ' . $stats[6]['content'] . ' hours of videos.</div>
			<div style="padding:2px;"> - ' . round($stats[7]['content']/1024) . ' GB of video.</div>
			<div style="padding:2px;"> - ' . $stats[8]['content'] . ' Status Changes.</div>
			<div align="center" style="font-size:8px;">Stats updated Every 2 hours.</div>
		</div>';
		echo "</div></div>\n";
	}	
	//birthday function
	private function Birthday($bm,$bd,$by){
		if($by == '0000'){return 'Unknown';}
		else {
			$ageTime = mktime(0, 0, 0, $bm, $bd, $by); // Get the person's birthday timestamp
			$t = time(); // Store current time for consistency
			$age = ($ageTime < 0) ? ( $t + ($ageTime * -1) ) : $t - $ageTime;
			$year = 60 * 60 * 24 * 365;
			$ageYears = $age / $year;
			return floor($ageYears);
		}
	}
    
    public function donationBox($horizontal = false)
    {
        $query = "SELECT `value` FROM `settings` WHERE `id` = 10";
        $result = mysql_query($query);
        
        if (!$result) {
            echo 'There was an issue running the donation box query';
            exit;
        }
        $row = mysql_fetch_assoc($result);
        if ($row['value'] == 1) {
            if ($horizontal == false) {
                echo "<div class='side-body-bg'>";
                echo "<div class='scapmain'>Donate to AnimeFTW.tv!</div>\n";
                echo "<div class='side-body floatfix'>\n";
                echo '<div align="center">
                    <a href="/donate">
                        <img src="//img03.animeftw.tv/support-aftw.png" alt="support aftw" />
                    </a>
                </div>';
                echo "</div></div>\n";
            } elseif ($horizontal == true) {
                echo '<div align="center">
                    <a href="/donate">
                        <img src="//img03.animeftw.tv/support-aftw-horizontal.png" alt="support aftw" />
                    </a>
                </div>';                
            } else {
            }
        }
    }
    
	public function BirthdayBox(){
		$Month = date("m");
		$Day = date("d");
		$result = mysql_query("SELECT COUNT(ID) AS numrows FROM users WHERE ageDate = $Day AND ageMonth = $Month AND Active = 1") or die('Error : Error: Error: Error');
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Today's Birthdays</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo "Today, on ".date("F jS").", AnimeFTW.tv has ".$row['numrows']." Members celebrating their Birthday!<br /><br />Visit the <a href=\"/birthdays\">Birthday Zone</a> and See who is having their Birthday Today!!";
		echo "</div></div>\n";
		
	}
	public function TodaysBirthdays(){
		$Month = date("m");
		$Day = date("d");
		$result = mysql_query("SELECT ID, ageDate, ageYear, ageMonth FROM users WHERE ageDate = $Day AND ageMonth = $Month AND Active = 1") or die('Error : Error: Error: Error');
		echo "<div class='side-body' align=\"center\">\n";
		while(list($ID,$ageDate,$ageYear,$ageMonth) = mysql_fetch_array($result)){
			echo $this->formatUsername($ID) . " - Age ".$this->Birthday($ageMonth,$ageDate,$ageYear)."<br />";
		}
		echo "<div>";
	}
	
	public function LatestEpisodes(){
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Latest Episodes(Airing)</div>\n";
		echo "<div class='side-body floatfix'>\n";
		if($this->la == 0)
		{
			echo '<div align="center">Please <a href="/login">Login</a> or <a href="/register">Register</a> to see the latest episodes.</div>';
		}
		else
		{
			if($this->UserArray[2] == 0)
			{
				$aonly = " AND `series`.`aonly` = 0";
			}
			else if($this->UserArray[2] == 3)
			{
				$aonly = " AND `series`.`aonly` <= 1";
			}
			else
			{
				$aonly = "";
			}
			$query = "SELECT `epnumber`, `epname`, `date`, `fullSeriesName`, `seoname` FROM `episode` INNER JOIN `series` ON `episode`.`sid`=`series`.`id` AND `series`.`stillRelease` = 'yes'" . $aonly . " ORDER BY `episode`.`id` DESC LIMIT 0, 10";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			while(list($epnumber,$epname,$date,$fullSeriesName,$seoname) = mysql_fetch_array($result, MYSQL_NUM)){
				$fullSeriesName = stripslashes($fullSeriesName);
				$epname = stripslashes($epname);
				if($date == 0){
					$date = 'Unknown';
				}
				else {
					$date = $this->timeZoneChange($date,$this->zone);
					$date = date("M j Y, h:i A",$date);
				}
				echo '<div align="center"><span style="font-weight:bold;"><a href="/anime/'.$seoname.'/ep-'.$epnumber.'">' . $epname . '</a></span><br />Posted in: <a href="/anime/'.$seoname.'/">' . $fullSeriesName . '</a><br />Added on: '.$date.'</div><br />';
			}
		}
		if($this->UserArray[0] == 1) {
			echo "<div align=\"right\"><a href=\"/rss/episodes\"><img src=\"/images/rss_feed_icon.png\" alt=\"rss feed\" width=\"30px\" /></a></div></div></div>";
		}
		else {
			echo "</div></div>";
		}
	}
	
	public function LatestSeries(){
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Latest Series</div>\n";
		echo "<div class='side-body floatfix'>\n";
		if($this->la == 0){$aonly = " AND aonly = 0";}
		else if($this->la == 3){$aonly = " AND aonly < 2";}
		else {$aonly = "";}
			$query = "SELECT id, fullSeriesName, seoname FROM series WHERE active = 'yes'".$aonly." ORDER BY id DESC LIMIT 0, 10";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			while(list($id,$fullSeriesName,$seoname) = mysql_fetch_array($result, MYSQL_NUM)){
				$fullSeriesName = stripslashes($fullSeriesName);
			echo '<div align="center" style="padding:3px;"><a href="/anime/'.$seoname.'/" onmouseover="ajax_showTooltip(window.event,\'/scripts.php?view=profiles&amp;show=tooltips&amp;id='.$id.'\',this);return false;" onmouseout="ajax_hideTooltip()">'.$fullSeriesName.'</a> was Added</div>';
			}
		if($this->UserArray[0] == 1) {
			echo "<div align=\"right\"><a href=\"/rss/series\"><img src=\"/images/rss_feed_icon.png\" alt=\"rss feed\" width=\"30px\" /></a></div></div></div>";
		}
		else {
			echo "</div></div>";
		}
	}
	
	public function TopWatchList(){
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Top My WatchList Series</div>\n";
		echo "<div class='side-body floatfix'>\n";
		$results = mysql_query("SELECT COUNT(watchlist.id), series.seoname, series.fullSeriesName FROM watchlist, series WHERE series.id=watchlist.sid GROUP BY watchlist.sid ORDER BY COUNT(watchlist.id) DESC LIMIT 0, 10");
		echo "<ol class=\"top10\">";
		while(list($cid,$seoname,$fullSeriesName) = mysql_fetch_array($results, MYSQL_NUM)){
			echo '<li type="1"><a href="/anime/'.$seoname.'/" title="'.$cid.' Users Watching">'.$fullSeriesName.'</a></li>'."\n";
		}
		echo "</ol>";
		echo "</div></div>";
	}
	
	public function ShowStoreCategories()
	{
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Store Categories</div>\n";
		echo "<div class='side-body floatfix'>\n";
		$query = "SELECT name FROM store_category WHERE type = 0 ORDER BY name";
		$results = mysql_query($query);
		echo '<div align="left">';
		while($row = mysql_fetch_assoc($results))
		{
			echo '<div style="font-size:14px;">- <a href="/store/' . strtolower($row['name']) . '">' . $row['name'] . '</a></div>';
		}
		echo '</div>';
		echo "</div></div>";
	}
	
	public function BuildStats(){
		/*
		// Update the total users
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE Active = 1) WHERE name = 'total_users'");
		
		// Update the total episode count
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM episode) WHERE name = 'total_episodes'");
		
		// Update the total series count
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM series) WHERE name = 'total_series'");
		
		// Update the total page comments
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM page_comments WHERE type = 0) WHERE name = 'total_comments'");
		
		// Update the total profile page comments
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM page_comments WHERE type = 1) WHERE name = 'total_comments_profile'");
		
		// Update the total episodes tracked
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM episode_tracker) WHERE name = 'total_tracker_rows'");
		
		// Upudate the total statuses
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM status) WHERE name = 'total_statuses'");
		
		// Update total users in last 24 hours
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE lastActivity>='".(time()-86400)."') WHERE name = 'total_24_hour_users'");
		
		// Update total watchlist entries
		mysql_query("UPDATE stats SET content = (SELECT COUNT(id) FROM watchlist) WHERE name = 'total_mywatchlist'");
		
		// Update total Males
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE gender = 'male') WHERE name = 'total_males'");
		
		// Update total females
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE gender = 'female') WHERE name = 'total_female'");
		
		// update total active avatars
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE avatarActivate = 'yes') WHERE name = 'total_avatars'");
		
		//
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE avatarActivate = 'yes' AND avatarExtension = 'gif') WHERE name = 'total_avatars_gif'");
		
		//
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE avatarActivate = 'yes' AND avatarExtension = 'jpg') WHERE name = 'total_avatars_jpgs'");
		
		//
		mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE avatarActivate = 'yes' AND avatarExtension = 'png') WHERE name = 'total_avatars_pngs'");
		//mysql_query("UPDATE stats SET content = (SELECT COUNT(ID) FROM users WHERE avatarActivate = 'yes' AND avatarExtension = 'gif') WHERE name = 'total_users'");
		
		
		// Calculations.
		$minutes_of_total_eps = $total_episodes*24;
		$length_of_total_eps = ($total_episodes*24)/60;
		$size_of_videos = substr((($total_episodes*125)/1024), 0, 5);
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Site Statistics</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<div class="rstats"><div> - '.$total_series.' Series.</div><div> - '.$total_episodes.' Episodes Online.</div><div> - '.$total_users.' Registered users.</div><div> - '.$total_tracked_eps.' Episodes Tracked.</div><div> - '.$total_comments.' Comments.</div><div> - '.$minutes_of_total_eps.' minutes of video.</div><div> - '.$length_of_total_eps.' hours of videos.</div><div> - '.$size_of_videos.' GB of video.</div><div> - '.$total_statusud.' Status Changes.</div></div>';
		echo "</div></div>\n";*/
	}
}
?>