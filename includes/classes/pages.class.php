<?php
/****************************************************************\
## FileName: pages.class.php									 
## Author: Brad Riemann										 
## Usage: Page Layout Class
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWpage{
	var $width;
	var $ssl;
	var $gmessage;
	var $uid;
	
	//getting the page width..
	function get_width($width){
		$this->width = $width;
	}
	//Message on the page, lets get it!
	function get_message($message){
		$this->gmessage = $message;
	}
	// Using a SSL? We need to know!
	function get_ssl($ssl_port){
		if($ssl_port == 80){
			$this->ssl = 'http';
		}
		else {
			$this->ssl = 'https';
		}
	}
	//Lets get the ID number.. Whatever it is.. for whoever it is...
	function get_uid($uid){
		$this->uid = $uid;
	}
	//Top of the main content..
	function mainTop(){
		echo "<table align='center' cellpadding='0' cellspacing='0' width='".$this->width."'>\n<tr>\n<td width='".$this->width."' class='main-bg'>\n";
	}
	//Messages (if any)
	function pageMessage(){
		if(isset($this->gmessage)){
			echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n<td class='note-message' align='center'>".$this->gmessage."</td>\n</tr>\n</table>\n<br />\n<br />\n";
		}
	}
	//Middle Top
	function middleTop(){
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n<td valign='top' class='main-mid'>\n";
	}
	//Middle Bottom
	function middleBottom(){
		echo "</td>\n<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	}
	//Right ads
	function rightAd($ul){
		if($ul == 0 || $ul == 3){
			echo "<div class='side-body-bg'>\n<div class='scapmain'>Advertisement</div>\n<div class='side-body floatfix'>\n<!-- Begin BidVertiser code --><SCRIPT LANGUAGE=\"JavaScript1.1\" SRC=\"http://bdv.bidvertiser.com/BidVertiser.dbm?pid=341006&bid=842663\" type=\"text/javascript\"></SCRIPT><noscript><a href=\"http://www.bidvertiser.com\">internet marketing</a></noscript><!-- End BidVertiser code -->>\n</div></div>\n";
		}	
	}
	//right bottom
	function rightBottom(){
		echo "</td>\n</tr>\n</table>\n";
	}
	//main bottom
	function mainBottom(){
		echo "</td>\n</tr>\n</table>\n";
	}
	//blank right box
	function blankRightBox($title,$body){
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>$title</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo $body;
		echo "</div></div>\n";
	}
	//blank middle box for stuff..
	function blankMiddleBox($header,$subline,$body){
		if($header != NULL || $subline != NULL)
		{
			echo "<div class='side-body-bg'>\n";
			if($header != NULL){
				echo "<span class='scapmain'>$header</span>\n";
				echo "<br />\n";
			}
			if($subline != NULL){
				echo "<span class='poster'>$subline</span>\n";
			}
			echo "</div>\n";
		}
		if($body != NULL){
			echo "<div class='tbl'>$body</div>\n";
			echo "<br />\n";
		}
	}	
	public function bodyTopInfo($message,$bdybr){
		if($bdybr == NULL){$bodyTop = "";}
		else {$bodyTop = "\n";}
		// Start Main BG
		$bodyTop .= "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
		$bodyTop .= "<td width='".THEME_WIDTH."' class='main-bg'>\n";
		// End Main BG
		if($message == NULL){}
		else {
		$bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$bodyTop .= "<td class='note-message' align='center'>".$message."</td>\n";
		$bodyTop .= "</tr>\n</table>\n";
		$bodyTop .= "<br />\n<br />\n";
		}
		// Start Mid and Right Content
		$bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$bodyTop .= "<td valign='top' class='main-mid'>\n";
		return $bodyTop;
	}
	
	public function LatestNews(){				
		mysql_query("SET NAMES 'utf8'"); 
		$query = mysql_query("SELECT t.tid, t.ttitle, t.tpid, t.tfid, t.tdate, f.fseo FROM forums_threads AS t, forums_forum AS f WHERE (t.tfid='1' OR t.tfid='2' OR t.tfid='9') AND f.fid = t.tfid ORDER BY tid DESC LIMIT 0, 1");
		$row = mysql_fetch_array($query);
		$ttitle = stripslashes($row['ttitle']);
		$ttitle = htmlspecialchars($ttitle);
		echo "<a href=\"/forums/".$row['fseo']."/topic-".$row['tid']."/s-0\"><img src=\"/images/latest-news.png\" alt=\"\" title=\"Posted on: ".date("M j Y, h:i A",$row['tdate'])."\" /></a><span class=\"search-text-pre\">News: </span><span class=\"search-text-post\" title=\"$ttitle\">";
		if(strlen($ttitle) <= 45){
			echo $ttitle;
		}
		else {
			echo substr($ttitle,0,41).'&hellip;'; 
		}
		echo "</span>";
	}
}
?>