<?php
/****************************************************************\
## FileName: footer.class.php									 
## Author: Brad Riemann										 
## Usage: Simple Footer Class
## Copywrite 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWFooter extends Config {
	
	//#- Vars -#\\
	#var ;
	
	//#- Contruct -#\\
	
	//#- Public Functions -#\\
	public function __construct(){
		parent::__construct();
	}
	
	# function Output
	public function Output(){
		echo "<br />\n<br />\n<br />\n";
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='footer-panels'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='panels' valign='top' width='33%'>\n";
		echo "<div class='panel-title'>Latest News</div>\n";
		echo "<br />\n";
		$this->LatestNews();
		echo "<td class='panels' valign='top' width='33%'>\n";
		echo "<div class='panel-title'>5 Random Anime</div>\n";
		echo "<br />\n";
		$this->RandomAnime(5);
		echo "<td class='panels' valign='top' width='33%'>\n";
		echo "<div class='panel-title'>Top Anime</div>\n";
		$this->TopAnime(5,'f');
		echo "</td>\n";
		echo "<td class='footer-mascot' valign='top'>";		
		if($this->UserArray[8] == 1){
			echo "<img src='/images/holiday/christmas/footer-mascot.png' alt='footer-mascot' border='0' style='position:relative;z-index:1;' />";
		}
		else {
			echo "<img src='/images/birthday/AnimeFTW_FooterFolks_Party.jpg' alt='footer-mascot' border='0' style='position:relative;z-index:1;' />";
		}
		echo "</td>\n";
		echo "</tr>\n</table>\n";
		echo "<table align='left' cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='copyright'>\n";
		echo "Powered by <a href='http://www.ftwentertainment.com/' title='FTW Entertainment LLC'>FTW</a> engine v4.0 copyright &copy; 2008 - ".date("Y")." by FTW Entertainment LLC.<br />Theme designed by <a href='http://www.animeftw.tv/user/falcon' title='Falcon of aGXTHEMES.com'>Falcon</a><div align='left' style='padding-top:5px;'>
<a href='http://ipv6-test.com/validate.php?url=referer'><img src='/images/button-ipv6-80x15.png' alt='ipv6 ready' title='AnimeFTW.tv ipv6 ready' border='0' /></a></div>";
		echo "</tr>\n</table>\n";	
		echo "</td>\n";
		echo "</tr>\n</table>\n";
	}
	
	//#- Private Functions -#\\
	
	# function latestnews
	private function LatestNews(){
		$query = "SELECT tid, ttitle, tpid, tfid, tdate FROM forums_threads WHERE tfid='1' OR tfid='2' OR tfid='9' ORDER BY tid DESC LIMIT 0, 5";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$i = 0;
		while(list($tid, $ttitle, $tpid, $tfid, $tdate) = mysql_fetch_array($result)) {
			$query1  = "SELECT fseo FROM forums_forum WHERE fid='$tfid'";
			$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
			$row1 = mysql_fetch_array($result1);
			echo "<a href='/forums/".$row1['fseo']."/topic-".$tid."/s-0' class='side'>".stripslashes($ttitle)."</a>\n";
			echo "<br />\n";
			if($i < 4){echo "<div class='panel-line'></div>\n";}
			$i++;
		}
	}
	
	private function RandomAnime($v){
		$query = "SELECT id FROM series WHERE active='yes' ORDER BY RAND() LIMIT $v";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$i = 0;
		while(list($id) = mysql_fetch_array($result)) {						
			echo $this->BuildSeriesList($id);
			echo "<br />\n";
			if($i < 4){echo "<div class='panel-line'></div>\n";}
			$i++;
		}
	}
	
	private function TopAnime($amount,$location) {
		$query = "SELECT seriesId, lastPosition, currentPosition FROM site_topseries ORDER BY currentPosition ASC LIMIT 0, ".$amount."";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$i = 0;
		echo "<br />\n";	
		while(list($id,$lastPosition,$currentPosition) = mysql_fetch_array($result)){						
			echo $this->BuildSeriesList($id);
			echo "<br />\n";
			if($i < 4){echo "<div class='panel-line'></div>\n";}
			$i++;
		}
	}
	
	private function BuildSeriesList($id){
		$query = mysql_query("SELECT seoname, fullSeriesName FROM series WHERE id = ".$id);
		$row = mysql_fetch_array($query);
		return "<a class='side tooltip-overlay' href='/anime/".$row['seoname']."/' data-node='/scripts.php?view=profiles&show=tooltips&id=" . $id . "'>".$row['fullSeriesName']."</a>\n";
	}
	
}
?>