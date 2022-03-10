<?php
/****************************************************************\
## FileName: footer.v2.class.php
## Author: Brad Riemann
## Usage: Simple Footer Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Footer extends Config {

	//#- Public Functions -#\\
	public function __construct()
	{
		parent::__construct();
	}

	# function Output
	public function Output()
	{
		$output = '';
		$output .= "<br />\n<br />\n<br />\n";
		$output .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$output .= "<td class='footer-panels'>\n";
		$output .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$output .= "<td class='panels' valign='top' width='33%'>\n";
		$output .= "<div class='panel-title'>Latest News</div>\n";
		$output .= "<br />\n";
		$output .= $this->LatestNews();
		$output .= "<td class='panels' valign='top' width='33%'>\n";
		$output .= "<div class='panel-title'>5 Random Anime</div>\n";
		$output .= "<br />\n";
		$output .= $this->RandomAnime(5);
		$output .= "<td class='panels' valign='top' width='33%'>\n";
		$output .= "<div class='panel-title'>Top Anime</div>\n";
		$output .= $this->TopAnime(5,'f');
		$output .= "</td>\n";
		$output .= "<td class='footer-mascot' valign='top'>";
		if($this->UserArray[8] == 1)
		{
			$output .= "<img src='//animeftw.tv/images/holiday/christmas/footer-mascot.png' alt='footer-mascot' border='0' style='position:relative;z-index:1;' />";
		}
		else
		{
			$output .= "<img src='//animeftw.tv/images/birthday/AnimeFTW_FooterFolks_Party.jpg' alt='footer-mascot' border='0' style='position:relative;z-index:1;' />";
		}
		$output .= "</td>\n";
		$output .= "</tr>\n</table>\n";
		$output .= "<table align='left' cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		$output .= "<td class='copyright'>\n";
		$output .= "Powered by <a href='https://www.ftwentertainment.com/' title='FTW Entertainment LLC'>FTW</a> engine v4.0 copyright &copy; 2008 - ".date("Y")." by FTW Entertainment LLC.<br />Theme designed by <a href='https://www.animeftw.tv/user/falcon' title='Falcon of aGXTHEMES.com'>Falcon</a><div align='left' style='padding-top:5px;'><a href='http://ipv6-test.com/validate.php?url=referer'><img src='//animeftw.tv/images/button-ipv6-80x15.png' alt='ipv6 ready' title='AnimeFTW.tv ipv6 ready' border='0' /></a></div>";
		$output .= "</tr>\n</table>\n";
		$output .= "</td>\n";
		$output .= "</tr>\n</table>\n";
		return $output;
	}

	//#- Private Functions -#\\

	# function latestnews
	private function LatestNews()
	{
		$query = "SELECT `forums_threads`.`tid`, `forums_threads`.`ttitle`, `forums_threads`.`tpid`, `forums_threads`.`tfid`, `forums_threads`.`tdate`, `forums_forum`.`fseo` FROM `forums_threads`, `forums_forum` WHERE (`forums_threads`.`tfid`='1' OR `forums_threads`.`tfid`='2' OR `forums_threads`.`tfid`='9') AND `forums_forum`.`fid` = `forums_threads`.`tfid` ORDER BY `forums_threads`.`tid` DESC LIMIT 0, 5";
		$result = $this->mysqli->query($query);
		$i = 0;
		$output = '';
		while(list($tid, $ttitle, $tpid, $tfid, $tdate, $fseo) = $result->fetch_assoc())
		{
			$output .= "<a href='/forums/" . $fseo . "/topic-" . $tid . "/s-0' class='side' title='Posted by " . $this->string_fancyUsername($tpid,NULL,NULL,NULL,NULL,NULL,TRUE)."'>" . stripslashes($ttitle) . "</a>\n";
			$output .= "<br />\n";
			if($i < 4)
			{
				$output .= "<div class='panel-line'></div>\n";
			}
			$i++;
		}
		return $output;
	}

	private function RandomAnime($v)
	{
		$query = "SELECT `id`, `seoname`, `fullSeriesName` FROM `series` WHERE `active`='yes' ORDER BY RAND() LIMIT $v";
		$result = $this->mysqli->query($query);
		$i = 0;
		$output = '';
		while(list($id, $seoname, $fullSeriesName) = $result->fetch_assoc())
		{
			$output .= '<a class="side" href="/anime/' . $seoname . '/" onmouseover="ajax_showTooltip(window.event,\'/scripts.php?view=profiles&amp;show=tooltips&amp;id=' . $id . '\',this);return false" onmouseout="ajax_hideTooltip()">' . $fullSeriesName . "</a>\n";;
			$output .= "<br />\n";
			if($i < 4)
			{
				$output .= "<div class='panel-line'></div>\n";
			}
			$i++;
		}
		return $output;
	}

	private function TopAnime($amount,$location)
	{
		$query = "SELECT `site_topseries`.`lastPosition`, `site_topseries`.`currentPosition`, `series`.`id`, `series`.`seoname`, `series`.`fullSeriesName` FROM `site_topseries`, `series` WHERE `series`.`id`=`site_topseries`.`seriesId` ORDER BY currentPosition ASC LIMIT 0, ".$amount."";
		$result = $this->mysqli->query($query);
		$i = 0;
		$output = '';
		$output .= "<br />\n";
		while(list($lastPosition,$currentPosition,$id,$seoname, $fullSeriesName) = $result->fetch_assoc())
		{
			$output .= '<a class="side" href="/anime/' . $seoname . '/" onmouseover="ajax_showTooltip(window.event,\'/scripts.php?view=profiles&amp;show=tooltips&amp;id=' . $id . '\',this);return false" onmouseout="ajax_hideTooltip()">' . $fullSeriesName . "</a>\n";;
			$output .= "<br />\n";
			if($i < 4)
			{
				$output .= "<div class='panel-line'></div>\n";
			}
			$i++;
		}
		return $output;
	}

}