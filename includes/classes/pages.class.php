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
			echo "<div class='side-body-bg'>\n<div class='scapmain'>Advertisement</div>\n<div class='side-body floatfix'>\n";
			?>
				<!-- Insticator API Embed Code -->
				<div id="insticator-container">
				<link rel="stylesheet" href="https://embed.insticator.com/embedstylesettings/getembedstyle?embedUUID=693d677f-f905-4a76-8223-3ed59a38842d">
				<div id="div-insticator-ad-1"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-1");</script></div>
				<div id="insticator-embed">
				<div id='insticator-api-iframe-div'><script>(function (d) {var id='693d677f-f905-4a76-8223-3ed59a38842d',cof = 1000 * 60 * 10,cbt = new Date(Math.floor(new Date().getTime() / cof) * cof).getTime(),js = 'https://embed.insticator.com/assets/javascripts/embed/insticator-api.js?cbt='+cbt, f = d.getElementById("insticator-api-iframe-div").appendChild(d.createElement('iframe')),doc = f.contentWindow.document;f.setAttribute("id","insticatorIframe"); f.setAttribute("frameborder","0"); doc.open().write('<script>var insticator_embedUUID = \''+id+'\'; var insticatorAsync = true;<\/script><body onload="var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + js + '\'" >');doc.close(); })(document);</script><noscript><a href="https://embed.insticator.com">Please enable JavaScript.</a></noscript></div>
				</div>
				<div id="div-insticator-ad-2"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-2");</script></div>
				</div>
				<!-- End Insticator API Embed Code -->
			<?
			echo "</div></div>\n";
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
	public function bodyTopInfo($message,$bdybr,$profileArray,$SettingsArray){
		if($bdybr == NULL){$bodyTop = "";}
		else {$bodyTop = "\n";}
		// Start Main BG
		$bodyTop .= "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
		$bodyTop .= "<td width='".THEME_WIDTH."' class='main-bg'>\n";
		$bodyTop .= '
		<div id="ad-wrapper" style="height:100%;position:absolute;z-index:0;">
			<div id="ad-sidebar" style="width:220px;float:left;margin:-10px 0 0 -245px;position:absolute;z-index:0;">';
		if(!isset($SettingsArray[17])){
			$bodyTop .= "<div class='side-body-bg'>";
			$bodyTop .= "<div class='scapmain'>Game</div>\n";
			$bodyTop .= "<div class='side-body floatfix'>\n";
			$bodyTop .= '<!-- Insticator API Embed Code -->
					<div id="insticator-container">
					<link rel="stylesheet" href="https://embed.insticator.com/embedstylesettings/getembedstyle?embedUUID=693d677f-f905-4a76-8223-3ed59a38842d">
					<div id="insticator-embed">';
					$bodyTop .= "
					<div id='insticator-api-iframe-div'><script>(function (d) {var id='693d677f-f905-4a76-8223-3ed59a38842d',cof = 1000 * 60 * 10,cbt = new Date(Math.floor(new Date().getTime() / cof) * cof).getTime(),js = 'https://embed.insticator.com/assets/javascripts/embed/insticator-api.js?cbt='+cbt, f = d.getElementById(\"insticator-api-iframe-div\").appendChild(d.createElement('iframe')),doc = f.contentWindow.document;f.setAttribute(\"id\",\"insticatorIframe\"); f.setAttribute(\"frameborder\",\"0\"); doc.open().write('<script>var insticator_embedUUID = \''+id+'\'; var insticatorAsync = true;<\/script><body onload=\"var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + js + '\'\" >');doc.close(); })(document);</script><noscript><a href=\"https://embed.insticator.com\">Please enable JavaScript.</a></noscript></div>";
					$bodyTop .= '
					</div>';
				$bodyTop .= '
				</div>
				<!-- End Insticator API Embed Code -->';
			$bodyTop .= "</div>\n";
			$bodyTop .= "</div>\n";
		}
		if($profileArray[2] == 0 || $profileArray[2] == 3){
			$bodyTop .= "<div class='side-body-bg'>";
			$bodyTop .= "<div class='scapmain'>Advertisement</div>\n";
			$bodyTop .= "<div class='side-body floatfix'>\n";
			if($profileArray[2] == 3){
				$bodyTop .= '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
			}
			$bodyTop .= '<!-- Insticator API Embed Code -->
					<div id="insticator-container">
						<div id="div-insticator-ad-1"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-1");</script></div>
					</div>
					<!-- End Insticator API Embed Code -->';
			$bodyTop .= "</div>\n";
			$bodyTop .= "</div>\n";
		}
		$bodyTop .= '
		</div>
		</div>';
		// End Main BG
		if($message == NULL){}
		else {
		$bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$bodyTop .= "<td class='note-message' align='center'>".$message."</td>\n";
		$bodyTop .= "</tr>\n</table>\n";
		$bodyTop .= "<br />\n<br />\n";
		}
		if($profileArray[2] == 0 || $profileArray[2] == 3){
			$bodyTop .= '
			<div id="ad-wrapper" style="height:100%;position:relative;z-index:0;float:right;">
				<div id="ad-sidebar" style="width:220px;float:right;margin:-10px 0 0 30px;position:absolute;z-index:0;">';
			$bodyTop .= "<div class='side-body-bg'>";
			$bodyTop .= "<div class='scapmain'>Advertisement</div>\n";
			$bodyTop .= "<div class='side-body floatfix'>\n";
			if($profileArray[2] == 3){
				$bodyTop .= '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
			}
			$bodyTop .= '<!-- Insticator API Embed Code -->
					<div id="insticator-container">
					<div id="div-insticator-ad-2"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-2");</script></div>
				</div>
				<!-- End Insticator API Embed Code -->';
			$bodyTop .= "</div>\n";
			$bodyTop .= "</div>\n";
			$bodyTop .= '
			</div>
			</div>';
		}
		// Start Mid and Right Content
		$bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$bodyTop .= "<td valign='top' class='main-mid'>\n";
		return $bodyTop;
	}
	
	public function LatestNews($profileArray){				
		mysql_query("SET NAMES 'utf8'"); 
        if ($profileArray[2] != 0 && $profileArray[2] != 3) {
            $showForumPosts = "'1','2','9', '14'";
        } else {
            $showForumPosts = "'1','2','9'";
        }
		$query = mysql_query("SELECT `tid`, `ttitle`, `tpid`, `tfid`, `tdate`, `fseo` FROM `forums_threads` INNER JOIN `forums_forum` ON `forums_forum`.`fid`=`forums_threads`.`tfid` WHERE `forums_threads`.`tfid` in (" . $showForumPosts . ") ORDER BY `tid` DESC LIMIT 0, 1");
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