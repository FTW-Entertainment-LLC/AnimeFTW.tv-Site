<?php
#***********************************************************
#* global_functions.php
#* Written by Brad Riemann
#* Copywrite 2008-2011 FTW Entertainment LLC
#* Distrobution of this is stricty forbidden
#***********************************************************

			include 'config_site.php';
			include 'newsOpenDb.php';
			$siteroot = 'www.animeftw.tv';
			if($_SERVER['SERVER_PORT'] == 443)
			{
				//$CDNHost = 'https://d206m0dw9i4jjv.cloudfront.net';
				$CDNHost = 'https://img03.animeftw.tv';
			}
			else
			{
				$CDNHost = 'http://img03.animeftw.tv';
			}
#-----------------------------------------------------------
# Function html_to_bbcode
# Replaces html with bbcode!
# Gotta remember, preg_replace hates >,/'s so you need to cancel out with \
#-----------------------------------------------------------

				function html_to_bbcode($bbcode){

     				$searchFor = array(
										'/<s\>(.*?)<\/s\>/is',
										'/<b\>(.*?)<\/b\>/is',
										'/<i\>(.*?)<\/i\>/is',
										'/<a href="(.*?)"\\>(.*?)<\/a\>/is',
										'/<img src="(.*?)" alt="user image" \/\>/is',
										'/<img src="(.*?)" \/\>/is',
										'/<div align="center"\>(.*?)<\/div\>/is',
										'/<u\>(.*?)<\/u\>/is',
										'/<div align="left"\>(.*?)<\/div\>/is',
										'/<div align="right"\>(.*?)<\/div\>/is',
										'/\<br(\s*)?\/?\>/i',
										'/<div class="quotetop"\>QUOTE<div class="quotemain"\>(.*?)<\/div\><\/div\>/is'
										);
     				$replaceWith = array(
										'[s]$1[/s]',
										'[b]$1[/b]',
										'[i]$1[/i]',
										'[url=$1]$2[/url]',
										'[img]$1[/img]',
										'[img]$1[/img]',
										'[center]$1[/center]',
										'[u]$1[/u]',
										'[left]$1[/left]',
										'[right]$1[/right]',
										"\n",
										'[quote]$1[/quote]'
										);
     				$html = preg_replace($searchFor, $replaceWith, $bbcode);
					return $html;
				}

			#-----------------------------------------------------------
			# Function bbcode_to_html
			# Replaces bbcode with html for the database!
			#-----------------------------------------------------------
				function code_to_html($bbcode){

     				$searchFor = array(
										'/\[s\](.*?)\[\/s\]/is',
										'/\[b\](.*?)\[\/b\]/is',
										'/\[i\](.*?)\[\/i\]/is',
										'/\[url\=(.*?)\](.*?)\[\/url\]/is',
										'/\[img\](.*?)\[\/img\]/is',
										'/\[center\](.*?)\[\/center\]/is',
										'/\[u\](.*?)\[\/u\]/is',
										'/\[left\](.*?)\[\/left\]/is',
										'/\[right\](.*?)\[\/right\]/is',
										'/\\n/is',
										'/\[quote\](.*?)\[\/quote\]/is'
										);
     				$replaceWith = array(
										'<s>$1</s>',
										'<b>$1</b>',
										'<i>$1</i>',
										'<a href="$1">$2</a>',
										'<img src="$1" alt="user image" />',
										'<div align="center">$1</div>',
										'<u>$1</u>',
										'<div align="left">$1</div>',
										'<div align="right">$1</div>',
										'<br />',
										'<div class="quotetop">QUOTE<div class="quotemain">$1</div></div>'
										);
     				$html = preg_replace($searchFor, $replaceWith, $bbcode);
					return $html;
					}
			#-----------------------------------------------------------
			# Function bbcode_to_smilies
			# Replaces bbcode with smiley html for the database!
			#-----------------------------------------------------------
				function bbcode_to_smilies($bbcode){

     				$searchFor = array(
										'/<img src="images/smilies/smile.gif" alt="" \/\>/is',
										'/<i\>(.*?)<\/i\>/is',
										'/<a href="(.*?)"\\>(.*?)<\/a\>/is',
										'/<img src="(.*?)" \/\>/is',
										'/<div align="center"\>(.*?)<\/div\>/is',
										'/<u\>(.*?)<\/u\>/is',
										'/<div align="left"\>(.*?)<\/div\>/is',
										'/<div align="right"\>(.*?)<\/div\>/is'
										);
     				$replaceWith = array(
										':)',
										'[i]$1[/i]',
										'[url=$1]$1[/url]',
										'[img]$1[/img]',
										'[center]$1[/center]',
										'[u]$1[/u]',
										'[left]$1[/left]',
										'[right]$1[/right]'
										);
     				$html = preg_replace($searchFor, $replaceWith, $bbcode);
					return $html;
					}
			#-----------------------------------------------------------
			# Function bbcode_to_smilies
			# Replaces bbcode with smiley html for the database!
			#-----------------------------------------------------------
				function smilies_to_bbcode($bbcode){

     				$searchFor = array(
										'/\:\)/is'
										);
     				$replaceWith = array(
										'<img src=\'images/smilies/smile.gif\' />'
										);
     				$html = preg_replace($searchFor, $replaceWith, $bbcode);
					return $html;
					}
			#-----------------------------------------------------------
			# Function underscoresToSpaces
			# Checks and replaces underscores with spaces!
			#-----------------------------------------------------------

			function underscoresToSpaces($postedData) {
			// Replace spaces with underscores
			$output = preg_replace("/_/e" , " " , $postedData);

			// Remove non-word characters
			$output = preg_replace("/\W/e" , "_" , $output);

			return strtolower($output);
			}
			function threadPagingV2($paging) {

			}

			#-----------------------------------------------------------
			# Function checkUserName
			# Checks a username against the DB and adds preffixs as needed
			#-----------------------------------------------------------

			function checkUserName ($username) {
			$query = "SELECT Username, Level_access, advanceImage, Active, advancePreffix
						FROM users
						WHERE Username='$username'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$Username = $row['Username'];
			$Level_access = $row['Level_access'];
			$Active = $row['Active'];
			$advanceImage = $row['advanceImage'];
			$advancePreffix = $row['advancePreffix'];
			if($Active == 1)
			{
				if($advancePreffix != NULL || $advancePreffix != '')
				{
					$spanbefore = '<span style="">';
					$spanafter = '</span>';
				}
				else
				{
					$spanbefore = '';
					$spanafter = '';
				}
				$link = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $Username . '">';
				if($Level_access == 1)
				{
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/admin-icon.png" alt="Admin of AnimeFTW.tv" style="vertical-align:middle;width:16px;" border="0" title="AnimeFTW.tv Administrator" />' . $link . $Username . '</a>' . $spanafter;
				}
				else if($Level_access == 2)
				{
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/manager-icon.png" alt="Group manager of AnimeFTW.tv" style="vertical-align:middle;width:16px;" border="0" title="AnimeFTW.tv Staff Manager" />' . $link . $Username . '</a>' . $spanafter;
				}
				else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
				{
					// //animeftw.tv/images/staff-icon.png
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/staff-icon.png" alt="Staff Member of AnimeFTW.tv" style="vertical-align:middle;width:16px;" border="0" title="AnimeFTW.tv Staff Member" />' . $link . $Username . '</a>' . $spanafter;
				}
				else if($Level_access == 7)
				{
					$fixedUsername = '<img src="//animeftw.tv/images/advancedimages/'.$advanceImage.'.png" alt="Advanced User Title" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Advanced Member" /><a href="/user/'.$Username.'">'.$Username.'</a>';
				}
				else
				{
					$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'">'.$Username.'</a>';
				}
			}
			else {
				$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"><s>'.$Username.'</s></a>';
			}
			return $fixedUsername;
			}

			#----------------------------------------------------------------------------
			# Function checkUserNameNoLink
			# Checks a username against the DB and adds preffixs as needed, no link
			#----------------------------------------------------------------------------

			function checkUserNameNoLink ($username) {
			$query = "SELECT Username, Level_access, advanceImage, Active, advancePreffix
						FROM users
						WHERE Username='$username'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$Username = $row['Username'];
			$Level_access = $row['Level_access'];
			$Active = $row['Active'];
			$advanceImage = $row['advanceImage'];
			$advancePreffix = $row['advancePreffix'];
			if($Active == 1)
			{
				if($advancePreffix != NULL || $advancePreffix != '')
				{
					$spanbefore = '<span style="">';
					$spanafter = '</span>';
				}
				else
				{
					$spanbefore = '';
					$spanafter = '';
				}
				if($Level_access == 1)
				{
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/admin-icon.png" alt="Admin of AnimeFTW.tv" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Administrator" />' . $Username . $spanafter;
				}
				else if($Level_access == 2)
				{
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/manager-icon.png" alt="Group manager of AnimeFTW.tv" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Staff Manager" />'  . $Username . $spanafter;
				}
				else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
				{
					// //animeftw.tv/images/staff-icon.png
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/staff-icon.png" alt="Staff Member of AnimeFTW.tv" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Staff Member" />'  . $Username . $spanafter;
				}
				else if($Level_access == 7)
				{
					$fixedUsername = '<img src="//animeftw.tv/images/advancedimages/'.$advanceImage.'.png" alt="Advanced User Title" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Advanced Member" />'.$Username.'';
				}
				else
				{
					$fixedUsername = ''.$Username.'';
				}
			}
			else
			{
				$fixedUsername = '<s>'.$Username.'</s>';
			}
			return $fixedUsername;
			}

			#----------------------------------------------------------------------------
			# Function bbsmiliesToCode
			# looks for smiley bbcode and converts to the html code
			#----------------------------------------------------------------------------

		function bbsmiliesToCode ($bbcode) {
					//Smilies fix
					//Zigbigidorlu was here =D
					$basedir = "//animeftw.tv/images/forumimages";
					$searchFor = array(
										'/\:\)/is',
										'/\:D/is',
										'/\;\)/is',
										'/\&lt;\.\&lt;/is',
										'/\^\_\^/is',
										'/\:P/is',
										'/\:p/is',
										'/\:o/is',
										'/\:O/is',
										'/\:\(/is',
										'/o\.o/is',
										'/XD/is',
										'/xD/is',
										);
     				$replaceWith = array(
										'<img src="'.$basedir.'/smilies/smile.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/biggrin.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/wink.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/dry.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/happy.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/tongue.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/tongue.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/ohmy.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/ohmy.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/sad.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/huh.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/doh.gif" alt="" />',
										'<img src="'.$basedir.'/smilies/doh.gif" alt="" />',
										);
     				$html = preg_replace($searchFor, $replaceWith, $bbcode);
					return $html;
		}
			#-----------------------------------------------------------
			# Function checkUserNameNumber
			# Checks a username against the DB and adds preffixs as needed
			#-----------------------------------------------------------

			function checkUserNameNumber ($ID,$lastActivity = NULL) {
			$query = "SELECT Username, Level_access, advanceImage, Active, advancePreffix
						FROM users
						WHERE ID='$ID'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$Username = $row['Username'];
			$Level_access = $row['Level_access'];
			$Active = $row['Active'];
			$advanceImage = $row['advanceImage'];
			$advancePreffix = $row['advancePreffix'];
			if($Active == 1)
			{
				if($lastActivity != NULL)
				{
					$title = ' title="last click on ' . date("l, F jS, Y, h:i a",$lastActivity) . '"';
				}
				else
				{
					$title = '';
				}
				if($advancePreffix != NULL || $advancePreffix != '')
				{
					$spanbefore = '<span style="">';
					$spanafter = '</span>';
				}
				else
				{
					$spanbefore = '';
					$spanafter = '';
				}
				$link = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $Username . '"' . $title . '>';
				if($Level_access == 1)
				{
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/admin-icon.png" alt="Admin of AnimeFTW.tv" style="vertical-align:middle;width:16px;" border="0" title="AnimeFTW.tv Administrator" />' . $link . $Username . '</a>' . $spanafter;
				}
				else if($Level_access == 2)
				{
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/manager-icon.png" alt="Group manager of AnimeFTW.tv" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Manager" />' . $link . $Username . '</a>' . $spanafter;
				}
				else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
				{
					// //animeftw.tv/images/staff-icon.png
					$fixedUsername = $spanbefore . '<img src="//animeftw.tv/images/staff-icon.png" alt="Staff Member of AnimeFTW.tv" style="vertical-align:middle;width:16px;" border="0" title="AnimeFTW.tv Staff Member" />' . $link . $Username . '</a>' . $spanafter;
				}
				else if($Level_access == 7)
				{
					$fixedUsername = '<img src="//animeftw.tv/images/advancedimages/'.$advanceImage.'.png" alt="Advanced User Title" style="vertical-align:middle;" border="0" title="AnimeFTW.tv Advanced Member" /><a href="/user/'.$Username.'">'.$Username.'</a>';
				}
				else
				{
					$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"' . $title . '>'.$Username.'</a>';
				}
			}
			else {
				$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"' . $title . '><s>'.$Username.'</s></a>';
			}
			return $fixedUsername;
			}
			#-----------------------------------------------------------
			# Function checkUserNameNumberNoLink
			# Checks a username against the DB and adds preffixs as needed
			#-----------------------------------------------------------

			function checkUserNameNumberNoLink($ID) {
			$query = "SELECT Username
						FROM users
						WHERE ID='$ID'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$Username = $row['Username'];
			return $Username;
			}
			#-----------------------------------------------------------
			# Function checkUserNameNumberNoLink
			# Checks a Username against the DB and get an ID
			#-----------------------------------------------------------

			function reverseCheckUserNameNumberNoLink($Username) {
			$query = "SELECT ID
						FROM users
						WHERE Username='$Username'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$ID = $row['ID'];
			return $ID;
			}
			#-----------------------------------------------------------
			# Function timeZoneChange
			# Checks the Date, and adds subtracts an hour based on a users timezone
			#-----------------------------------------------------------

			function timeZoneChange($date,$timezone) {
				$timezone = (60*60)*($timezone+6);
				$revisedDate = $date+($timezone);
				return $revisedDate;
			}

			#-----------------------------------------------------------
			# Function checkSeries
			# checks the series for a given id number
			#-----------------------------------------------------------

			function checkSeries($epid) {
				$query = "SELECT s.seoname AS sname, s.fullSeriesName AS fname FROM series AS s, episode AS e WHERE e.seriesname=s.seriesName AND e.id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seoname = $row['sname'];
				$fullSeriesName = $row['fname'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'</a>';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function reverseCheckSeries
			# checks the series for a given id number
			#-----------------------------------------------------------

			function reverseCheckSeries($seriesName) {
				$query = "SELECT seriesName FROM series WHERE fullSeriesName LIKE '%".$seriesName."%'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seriesname = $row['seriesName'];
				return $seriesname;
			}

			#-----------------------------------------------------------
			# Function seoCheck
			# checks the series for a given id number
			#-----------------------------------------------------------

			function seoCheck($seriesName) {
				$query = "SELECT seoname FROM series WHERE seriesName='$seriesName'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seoname = $row['seoname'];
				return $seoname;
			}

			#-----------------------------------------------------------
			# Function checkSeriesSid
			# checks the series for a given id number
			#-----------------------------------------------------------

			function checkSeriesSid($sid) {
				$query = "SELECT * FROM series WHERE id='$sid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seoname = $row['seoname'];
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				if($row['OVA'] == 0)
				{
					if($row['moviesOnly'] == 0)
					{
						$filmType = 'videos';
					}
					else {
						$filmType = 'movies';
					}
				}
				else {
					$filmType = 'ovas';
				}

				$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'</a>';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeriesNoLink
			# checks the series for a given seriesname
			#-----------------------------------------------------------

			function checkSeriesNoLink($seriesname) {
				$query = "SELECT fullSeriesName FROM series WHERE seriesName='$seriesname'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				return $fullSeriesName;
			}

			#-----------------------------------------------------------
			# Function checkSeriesNoLinkId
			# checks the series for a given series Id
			#-----------------------------------------------------------

			function checkSeriesNoLinkId($sid) {
				$query = "SELECT fullSeriesName FROM series WHERE id='$sid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				return $fullSeriesName;
			}

			#-----------------------------------------------------------
			# Function checkEpNoLink
			# when given various credentials, system checks items against
			#-----------------------------------------------------------

			function checkEpNoLink($eid,$variable) {
					$query = "SELECT epnumber AS variable FROM episode WHERE id='$eid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$variablefinal = $row['variable'];
				$variablefinal = stripslashes($variablefinal);
				return $variablefinal;
			}

			#-----------------------------------------------------------
			# Function checkSeriesId
			# checks the seriesname and returns the ID
			#-----------------------------------------------------------

			function checkSeriesId($seriesname) {
				$query = "SELECT id FROM series WHERE seriesName='$seriesname'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$id = $row['id'];
				return $id;
			}

			#-----------------------------------------------------------
			# Function checkEpisode
			# With a given Series ID And Episode number it
			# spits out a valid link, only shows the ep number as a link
			#-----------------------------------------------------------

			function checkEpisode($epid) {
				$query = "SELECT e.epnumber, e.Movie, s.seoname, s.OVA FROM episode AS e, series AS s WHERE e.seriesname=s.seriesName AND e.id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$EpNumber = $row['epnumber'];
				$Movie = $row['Movie'];
				$seoname = $row['seoname'];
				if($row['OVA'] == 0){
					if($Movie == 0){
						$fileType = 'ep';
					}
					else {
						$fileType = 'movie';
					}
				}
				else {
					$fileType = 'ep';
				}
				$FinalLink = '<a href="/anime/'.$seoname.'/'.$fileType.'-'.$EpNumber.'">'.$EpNumber.'</a>';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkEpisode2
			# With a given Series ID And Episode number it
			# spits out a valid link, only shows the ep number as a link
			#-----------------------------------------------------------

			function checkEpisode2($epid) {
				$query = "SELECT * FROM episode WHERE id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seriesname = $row['seriesname'];
				$EpNumber = $row['epnumber'];
				$EpName = $row['epname'];
				$Movie = $row['Movie'];
				$query = "SELECT * FROM series WHERE seriesName='$seriesname'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seoname = $row['seoname'];
				$EpName = stripslashes($EpName);
				if($row['OVA'] == 0)
				{
					if($Movie == 0)
					{
						$filmType = 'anime';
						$fileType = 'ep';
					}
					else {
						$filmType = 'anime';
						$fileType = 'movie';
					}
				}
				else {
					$filmType = 'anime';
					$fileType = 'ova';
				}
				$FinalLink = '<a href="/'.$filmType.'/'.$seoname.'/'.$fileType.'-'.$EpNumber.'">'.$EpName.'</a>';
				return $FinalLink;
			}


			#-----------------------------------------------------------
			# Function checkEpisodeName
			# With a given Series ID And Episode number it
			# spits out a valid link, only shows the ep number as a link
			#-----------------------------------------------------------

			function checkEpisodeName($epid) {
				$query = "SELECT * FROM episode WHERE id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$EpName = $row['epname'];
				$EpName = stripslashes($EpName);
				$FinalLink = $EpName;
				return $FinalLink;
			}
			#-----------------------------------------------------------
			# Function checkEpisodeLinkOnly
			# With a given Series ID And Episode number it
			# spits out a valid link, only shows the ep number as a link
			#-----------------------------------------------------------

			function checkEpisodeLinkOnly($epid) {
				$query = "SELECT * FROM episode WHERE id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seriesname = $row['seriesname'];
				$EpNumber = $row['epnumber'];
				$Movie = $row['Movie'];
				$query = "SELECT * FROM series WHERE seriesName='$seriesname'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seoname = $row['seoname'];
				if($row['OVA'] == 0)
				{
					if($Movie == 0)
					{
						$filmType = 'videos';
						$fileType = 'ep';
					}
					else {
						$filmType = 'movies';
						$fileType = 'movie';
					}
				}
				else {
					$filmType = 'ovas';
					$fileType = 'ep';
				}
				$FinalLink = 'http://www.animeftw.tv/'.$filmType.'/'.$seoname.'/'.$fileType.'-'.$EpNumber.'';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkEpisodeFull
			# With a given ID number, it spits out series and episode
			# ID number with full link to video
			#-----------------------------------------------------------

			function checkEpisodeFull($epid) {
				$query = "SELECT * FROM episode WHERE id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seriesname = $row['seriesname'];
				$EpNumber = $row['epnumber'];
				$Movie = $row['Movie'];
				$query = "SELECT fullSeriesName, seoname FROM series WHERE seriesName='$seriesname'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$seoname = $row['seoname'];
				if($row['OVA'] == 0)
				{
					if($Movie == 0)
					{
						$filmType = 'videos';
						$fileType = 'ep';
					}
					else {
						$filmType = 'movies';
						$fileType = 'movie';
					}
				}
				else {
					$filmType = 'ovas';
					$fileType = 'ep';
				}
				$FinalLink = '<a href="/'.$filmType.'/'.$seoname.'/'.$fileType.'-'.$EpNumber.'">Ep# '.$EpNumber.' of '.$fullSeriesName.'</a>';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkEpisodeSeriesName
			# With a given ID number, it spits out series and episode
			# ID number with full link to video
			#-----------------------------------------------------------

			function checkEpisodeSeriesName($epid) {
				$query = "SELECT * FROM episode WHERE id='$epid'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$seriesname = $row['seriesname'];
				$EpNumber = $row['epnumber'];
				$query = "SELECT fullSeriesName FROM series WHERE seriesName='$seriesname'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$FinalLink = 'Viewed Series '.$fullSeriesName.' Episode #'.$EpNumber;
				return $FinalLink;
			}


			#-----------------------------------------------------------
			# Function checkSeries2
			# checks the series for a given id number
			#-----------------------------------------------------------

			function checkSeries2($SeriesId) {
				$query = "SELECT * FROM series WHERE id='$SeriesId'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				if($row['OVA'] == 0)
				{
					if($row['moviesOnly'] == 0)
					{
						$filmType = 'anime';
					}
					else {
						$filmType = 'movies';
					}
				}
				else {
					$filmType = 'ovas';
				}
					$FinalLink = '<a href="/'.$filmType.'/'.$seoname.'/">'.$fullSeriesName.'</a>';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeries3
			# checks the series for a given id number
			#-----------------------------------------------------------

			function checkSeries3($SeriesName) {
				$query = "SELECT * FROM series WHERE seriesName='$SeriesName'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				if($row['OVA'] == 0)
				{
					if($row['moviesOnly'] == 0)
					{
						$filmType = 'videos';
					}
					else {
						$filmType = 'movies';
					}
				}
				else {
					$filmType = 'ovas';
				}
				if($row['active'] == 'no' || $row['aonly'] == '1' )
				{
					$FinalLink = 'na';
				}
				else {
					$FinalLink = '<a href="/'.$filmType.'/'.$seoname.'/">'.$fullSeriesName.'</a>';
				}
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeries4
			# checks the series for a given seriesname (for the topnav)
			#-----------------------------------------------------------

			function checkSeries4($SeriesName) {
				$query = "SELECT fullSeriesName, seoname, OVA, moviesonly, seriesType, aonly FROM series WHERE seriesName='$SeriesName'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				$Type = '';
				//Lets make sure our link is correct! CAnt be going to the wrong link!
				if($row['OVA'] == 0)
				{
					if($row['moviesOnly'] == 0)
					{
						$filmType = 'videos';
					}
					else {
						$filmType = 'movies';
					}
				}
				else {
					$filmType = 'ovas';
				}
				// Below will figure out if its Advanced only or not!
				if($row['aonly'] == 0)
				{
					$Type .= '';
				}
				else if ($row['aonly'] == 1)
				{
					$Type .= '';
				}
				else {
					$Type .= '<img src="//animeftw.tv/images/advancedonly.png" alt="Advanced Members only Series" title="This is an Advanced only Series!" />&nbsp;';
				}
				// Lets find out if its MKV or not!
				if($row['seriesType'] == 0)
				{
					$Type .= '';
				}
				else if($row['seriesType'] == 1)
				{
					$Type .= '<img src="//animeftw.tv/images/mkv-series.png" alt="MKV series" title="This series is in DivX Web 2.0 Format" />';
				}
				else {
					$Type .= '';
				}
					$FinalLink = '<a href="/'.$filmType.'/'.$seoname.'/">'.$fullSeriesName.'&nbsp;'.$Type.'</a>';
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeriesWIcons
			# checks the series for a given id number and gives
			# it an airing icon
			#-----------------------------------------------------------

			function checkSeriesWIcons($SeriesId) {
				$query = "SELECT fullSeriesName, seoname, stillRelease, seriesType, seriesList, moviesOnly FROM series WHERE seriesId='$SeriesId'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				$stillRelease = $row['stillRelease'];
				$seriesList = $row['seriesList'];
				$moviesOnly = $row['moviesOnly'];
				if($seriesList == 0)
				{
					$seriesList = 'anime';
				}
				else if($seriesList == 1)
				{
					$seriesList = 'drama';
				}
				else {
					$seriesList = 'amv';
				}
				if($row['seriesType'] == 0)
				{
					$Type = '';
				}
				else if($row['seriesType'] == 1)
				{
					$Type = '<img src="' . $CDNHost . '/mkv-series.png" alt="MKV series" title="This series is in DivX Web 2.0 Format"  style="vertical-align:middle;" border="0" />';
				}
				else {
					$Type = '';
				}
				if($moviesOnly == 1)
				{
					$Type .= '&nbsp;<img src="' . $CDNHost . '/movie_blue.png" alt="Movie" title="This is a Movie"  style="vertical-align:middle;" border="0" />';
				}
				if($stillRelease == 'yes')
				{
					$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'&nbsp;<img src="' . $CDNHost . '/airing_icon.gif" alt="Airing" title="This Series is Airing" style="vertical-align:middle;" border="0" />&nbsp;'.$Type.'</a>';
				}
				else {
					$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'&nbsp;'.$Type.'</a>';
				}
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeriesWIconsV2
			# checks the series for a given id number and gives
			# it an airing icon --Version2--
			#-----------------------------------------------------------

			function checkSeriesWIconsV2($SeriesId) {
				$query = "SELECT fullSeriesName, seoname, stillRelease, seriesType, seriesList, moviesOnly FROM series WHERE seriesId='$SeriesId'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				$stillRelease = $row['stillRelease'];
				$seriesList = $row['seriesList'];
				$moviesOnly = $row['moviesOnly'];

				if($seriesList == 0)
				{
					$seriesList = 'anime';
				}
				else if($seriesList == 1)
				{
					$seriesList = 'drama';
				}
				else {
					$seriesList = 'amv';
				}
				if($row['seriesType'] == 0)
				{
					$Type = '';
				}
				else if($row['seriesType'] == 1)
				{
					$Type = '&nbsp;<img src="' . $CDNHost . '/mkv-series.png" alt="MKV series" title="This series is in DivX Web 2.0 Format" style="vertical-align:middle;" border="0" />';
				}
				else {
					$Type = '';
				}
				if($stillRelease == 'yes')
				{
					$airing = '&nbsp;<img src="' . $CDNHost . '/airing_icon.gif" alt="Airing" title="This Series is Airing" style="vertical-align:middle;" border="0" />';
				}

				else {
					$airing = '&nbsp;';
				}
				if($moviesOnly == 1)
				{
					$Type .= '&nbsp;<img src="' . $CDNHost . '/movie_blue.png" alt="Movie" title="This is a Movie"  style="vertical-align:middle;" border="0" />';
				}
				//if($row['seriesList']

				$FinalLink = '<a href="/'.$seriesList.'/'.$seoname.'/">'.$fullSeriesName.'</a>'.$airing.$Type;
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeriesWIconsV3
			# checks the series for a given id number and gives
			# it an airing icon --Version3--
			#-----------------------------------------------------------

			function checkSeriesWIconsV3($SeriesId) {
				$query = "SELECT id, fullSeriesName, seoname, description, stillRelease, seriesType, seriesList, moviesOnly FROM series WHERE id='$SeriesId'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				$stillRelease = $row['stillRelease'];
				$seriesList = $row['seriesList'];
				$moviesOnly = $row['moviesOnly'];
				$description = $row['description'];
				$description = stripslashes($description);

				if($seriesList == 0)
				{
					$seriesList = 'anime';
				}
				else if($seriesList == 1)
				{
					$seriesList = 'drama';
				}
				else {
					$seriesList = 'amv';
				}
				if($row['seriesType'] == 0)
				{
					$Type = '';
				}
				else if($row['seriesType'] == 1)
				{
					$Type = '&nbsp;<img src="' . $CDNHost . '/mkv-series.png" alt="MKV series" title="This series is in DivX Web 2.0 Format" style="vertical-align:middle;" border="0" />';
				}
				else {
					$Type = '';
				}
				if($stillRelease == 'yes')
				{
					$airing = '&nbsp;<img src="' . $CDNHost . '/airing_icon.gif" alt="Airing" title="This Series is Airing" style="vertical-align:middle;" border="0" />';
				}

				else {
					$airing = '&nbsp;';
				}
				if($moviesOnly == 1)
				{
					$Type .= '&nbsp;<img src="' . $CDNHost . '/movie_blue.png" alt="Movie" title="This is a Movie"  style="vertical-align:middle;" border="0" />';
				}
				//$FinalLink = '<a href="/'.$seriesList.'/'.$seoname.'/"><span class="formInfo">'.$fullSeriesName.'<span style="display: none;" class="animetip">';
				//$FinalLink .= '<table><tr><td width="10%" valign="top"><img src="//animeftw.tv/images/resize/anime/large/'.$row['id'].'.jpg" alt="" /></td><td valign="top"><b>Description:</b><br />'.$description.'</td></tr></table>';
				//$FinalLink .= '</span></span></a>'.$airing.$Type;

				$FinalLink = '<a href="/'.$seriesList.'/'.$seoname.'/" onmouseover="ajax_showTooltip(window.event,\'http://'.$_SERVER['HTTP_HOST'].'/scripts.php?view=profiles&amp;show=tooltips&amp;id='.$row['id'].'\',this);return false" onmouseout="ajax_hideTooltip()">'.$fullSeriesName.'</a>'.$airing.$Type;
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeriesReviews
			# checks the series for a given id number and gives
			# it an airing icon
			#-----------------------------------------------------------

			function checkSeriesReviews($SeriesId) {
				$query = "SELECT * FROM series WHERE seriesId='$SeriesId'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$sid = $row['id'];
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];
				$stillRelease = $row['stillRelease'];

				if($stillRelease == 'yes')
				{
					$FinalLink = '<a href="/reviews/series-'.$sid.'/">'.$fullSeriesName.'</a>&nbsp;<img src="//animeftw.tv/images/airing_icon.gif" alt="Airing" title="This Series is Airing" style="vertical-align:middle;" border="0" />';
				}
				else {
					$FinalLink = '<a href="/reviews/series-'.$sid.'/">'.$fullSeriesName.'</a>';
				}
				return $FinalLink;
			}

			#-----------------------------------------------------------
			# Function checkSeriesOvas
			# checks the series for a given id number
			#-----------------------------------------------------------

			function checkSeriesOvas($SeriesId) {
				$query = "SELECT * FROM series WHERE seriesId='$SeriesId'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$fullSeriesName = $row['fullSeriesName'];
				$fullSeriesName = stripslashes($fullSeriesName);
				$seoname = $row['seoname'];

				$FinalLink = '<a href="/ovas/'.$seoname.'/">'.$fullSeriesName.'</a>';
				return $FinalLink;
			}



			#-------------------------------------------------------------
			# Function seriesTopSeriesRank
			# This function takes the top anime and
			# spits them out for all to see.
			#-------------------------------------------------------------

		function seriesTopSeriesRank($seriesId) {
				$query = "SELECT lastPosition, currentPosition FROM site_topseries
						WHERE seriesId='".$seriesId."'
						ORDER BY currentPosition
						ASC ";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$lastPosition = $row['lastPosition'];
				$currentPosition = $row['currentPosition'];
				$singleRank = '';
					$listedName = checkSeries2($seriesId);
					if($currentPosition < $lastPosition)
					{
						$Rank = $currentPosition.'&nbsp;<img src="' . $CDNHost . '/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: '.$lastPosition.'" />';
					}
					else if ($currentPosition == $lastPosition)
					{
						$Rank = $currentPosition.'&nbsp;<img src="' . $CDNHost . '/arrow_none.gif" title="Rank Unchanged, Previous Rank: '.$lastPosition.'" alt="" />';
					}
					else {
						$Rank = $currentPosition.'&nbsp;<img src="' . $CDNHost . '/arrow_down.gif" alt="" title="Rank Went Down, Previous Rank: '.$lastPosition.'" />';
					}

					if($listedName == 'na')
					{
						$singleRank .= 'This Series is not Ranked on the Top list';
					}
					else {
						if($currentPosition == '')
						{
							$singleRank .= 'This Series is not Ranked on the Top list';
						}
						else {
							$singleRank .= $listedName.' is ranked #<b>'.$Rank."</b> on AnimeFTW.tv\n";
						}
					}

				return $singleRank;
			}

			#-------------------------------------------------------------
			# Function topSelectiveAnime
			# With Given number amount of outputted anime is displayed
			# put in number for how many returned.
			#-------------------------------------------------------------

			function topSelectiveAnime($amount,$location) {
				$query = "SELECT series.id, series.seoname, series.fullSeriesName, site_topseries.lastPosition, site_topseries.currentPosition FROM series, site_topseries WHERE series.id=site_topseries.seriesId ORDER BY currentPosition ASC LIMIT 0, ".$amount."";
				$result = mysqli_query($conn, $query);
				$i = 0;
				echo "<br />\n";
				while(list($id,$seoname,$fullSeriesName,$lastPosition,$currentPosition) = mysqli_fetch_array($result))
				{
					$onmouse = 'onmouseover="ajax_showTooltip(window.event,\'/scripts.php?view=profiles&amp;show=tooltips&amp;id='.$id.'\',this);return false" onmouseout="ajax_hideTooltip()"';
					echo "<a class='side' href='/anime/".$seoname."/' ".$onmouse.">".$fullSeriesName."</a>\n";
					echo "<br />\n";
					if($i < 4){echo "<div class='panel-line'></div>\n";}
					$i++;
				}
			}

			#-------------------------------------------------------------
			# Function addTopicView
			# Each page refresh makes a new pageview for a given topic
			#-------------------------------------------------------------

			function addTopicView($tid) {
				$query = "UPDATE forums_threads SET tviews = tviews+1 WHERE tid = $tid";
				$result = mysqli_query($conn, $query);

			}

			#-------------------------------------------------------------
			# Function recordEpisodeTopseries
			# Each page refresh makes a new pageview for a given topic
			# if the user's ip is not in the database already
			#-------------------------------------------------------------

			function recordEpisodeTopseries($seriesId,$ip,$epNumber) {
				//Get the Date for today, all 24 hours
				$currentDay = date('d-m-Y',time());
				$midnight = strtotime($currentDay);
				$elevenfiftynine = $midnight+86399;
				//check for any rows that were done today...
				$query20  = mysqli_query($conn, "SELECT * FROM episodestats WHERE ip='".$ip."' AND epSeriesId='".$seriesId."' AND epNumber='".$epNumber."' AND date>='".$midnight."'");
				$Countrows = mysqli_num_rows($query20);
				if($Countrows == 0)
				{
					$query = "INSERT INTO episodestats (epSeriesId, ip, date, epnumber)
VALUES ('$seriesId', '$ip', '".time()."', '$epNumber')";
					mysqli_query($conn, $query) or die('Could not connect, way to godddd retard:' . mysqli_error());
				}
			}

			#-------------------------------------------------------------
			# Function checkServer
			# Checks the userID against the DB for encoders
			# then outputs what server thay are on.
			#-------------------------------------------------------------

			function checkServer($id) {
				$query1 = "SELECT server FROM encoders WHERE id='$id'";
				$result1 = mysqli_query($conn, $query1);
				$row = mysqli_fetch_array($result1);
				$server = $row['server'];
				$returnvar = checkUserNameNumberNoLink($id).' is on '.$server.'.';
				return $returnvar;
			}

			#-------------------------------------------------------------
			# Function setFailedLoginCookie
			# sets a cookie saying a user cannot login for 15 min
			#-------------------------------------------------------------

			function setFailedLoginCookie(){
				setcookie ( "__flc", time() + 900, time() + 900, '/' );
			}

			#-------------------------------------------------------------
			# Function checkFailedLogins
			# Checks failed logins against the server,
			# once it hits 5 then it blocks the user for 15 minutes by
			# setting a cookie that expires in 15 min.
			#-------------------------------------------------------------

			function checkFailedLogins($ip) {
				$fivebefore = time()-300;
				$query1 = "SELECT ip FROM `failed_logins` where date>='".$fivebefore."' AND ip='".$ip."'";
				$result1 = mysqli_query($conn, $query1);
				$total_fails = mysqli_num_rows($result1);
				if($total_fails == 1){
					$statement = '1 of 5 Failed Login attempts Used.';
				}
				else if ($total_fails == 2){
					$statement = '2 of 5 Failed Login attempts Used.';
				}
				else if ($total_fails == 3){
					$statement = '3 of 5 Failed Login attempts Used.';
				}
				else if ($total_fails == 4){
					$statement = '4 of 5 Failed Login attempts Used.';
				}
				else {
					$statement = '5 of 5 Failed Login attempts Used.<br /> You will be forbidden from logging in for the next 15 minutes.';
					setFailedLoginCookie();
				}
				return $statement;
			}

			#-------------------------------------------------------------
			# Function cronMail
			# sends emails based upon the crons list of to-dos
			# 1= site pm, 2=episode note
			#-------------------------------------------------------------

			function cronMail($toid,$fromid,$emailtype,$date) {
				//query to user
					$query1 = "SELECT Username, Email FROM users WHERE ID='$toid'";
					$result1 = mysqli_query($conn, $query1);
					$row = mysqli_fetch_array($result1);
					$toUsername = $row['Username'];
					$toEmail = $row['Email'];

				//site pm email
				if($emailtype == 1)
				{
				//query from user
					$query2 = "SELECT Username FROM users WHERE ID='$fromid'";
					$result2 = mysqli_query($conn, $query2);
					$row2 = mysqli_fetch_array($result2);
					$fromUsername = $row2['Username'];

					$query1 = "SELECT id, msgSubject, msgBody FROM messages WHERE date='$date' AND rid='$toid' AND sid='$fromid'";
					$result1 = mysqli_query($conn, $query1);
					$row = mysqli_fetch_array($result1);
					$msgId = $row['id'];
					$msgSubject = $row['msgSubject'];
					$msgBody = $row['msgBody'];
				//begin email buildup
					$subject = 'New Site Personal Message!';
					$to = $toEmail;    //  their email
					$body =  "Hi $toUsername,<br /><br />\n";
					$body .= "$fromUsername has sent you a site PM entitled: $msgSubject <br />\n";
					$body .= "--------------------------------------------------------------------------------<br />\n";
					$body .= "$msgBody <br />\n";
					$body .= "--------------------------------------------------------------------------------<br />\n";
					$body .= "You can view this message here: <br />\n";
					$body .= "<a href='http://www.animeftw.tv/messages/view/$msgId'>http://www.animeftw.tv/messages/view/$msgId</a>\n";
					$mime_boundary = "----FTW_ENTERTAINMENT_LLC----".md5(time());
					$headers = "From: AnimeFTW.tv Notifications <notifications@animeftw.tv>\n";
					$headers .= "Reply-To: AnimeFTW.tv Notifications <notifications@animeftw.tv>\n";
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-Type: text/html; boundary=\"$mime_boundary\"\n";
					$body .= "<br /><br /><i>If you wish to opt out of PM notifications please <a href='http://www.animeftw.tv/edit/notifications'>edit</a> your notification methods</i><br />\n";
					$body = wordwrap($body,70);
				//away we go!
					mail($to, $subject, $body, $headers);

				}
			}

			#-------------------------------------------------------------
			# Function videoCheck
			# checks episode id to the report column in the db,
			# 1=reported, 0=normal
			#-------------------------------------------------------------

			function videoCheck($eid) {
				$query1 = "SELECT report FROM episode WHERE id='$eid'";
				$result1 = mysqli_query($conn, $query1);
				$row = mysqli_fetch_array($result1);
				$report = $row['report'];
				if($report == 0)
				{
					$videoreport = '<a onClick="javascript:showReport();" style="border:none;cursor:pointer;"><img src="//animeftw.tv/images/reportavideo.png" /></a>
';
				}
				else {
					$videoreport = '<a style="border:none;cursor:pointer;" title="This video has been reported with an error!"><img src="//animeftw.tv/images/reportavideoreported.png" /></a>
';
				}
				return $videoreport;
			}

			#------------------------------------------------------------
			# Function hideInformation
			# Checks to see if a MORE is needed on submissions to the
			# Video reports console
			#------------------------------------------------------------

			function hideInformation($id,$information)
			{
				$spanbefore = '<span id="'.$id.'" style="display: none">';
				$properamoutn = strlen($information)-56;
				$information2 = substr($information,0,56). $spanbefore . substr($information,-$properamoutn) .'</span> <a id="morelink'.$id.'" href="javascript:showFullComment('.$id.')" class="storylinks">... MORE &gt;</a>';
				return $information2;
			}

			#-----------------------------------------------------------
			# Function checkItemCat
			# checks category and makes it 'proper'
			#-----------------------------------------------------------

			function checkItemCat($catid) {
			$query = "SELECT name
						FROM store_category
						WHERE id='$catid'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$name = $row['name'];
			return $name;
			}


			#-----------------------------------------------------------
			# Function checkSeoItemCat
			# checks category and makes it 'proper'
			#-----------------------------------------------------------

			function checkSeoItemCat($catid) {
			$query = "SELECT name
						FROM store_category
						WHERE id='$catid'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$name = $row['name'];
			$name = strtolower(name);
			return $name;
			}

			#-----------------------------------------------------------
			# Function errorReportStatus
			# returns the column for the status of a submission
			#-----------------------------------------------------------

			function errorReportStatus($status) {
				if($status == 0)
				{
					$finalStatus = '<td align="center" style="color:#009933;">Pending</td>';
				}
				else if($status == 1)
				{
					$finalStatus = '<td align="center" style="color:#CC0033;">Closed</td>';
				}
				else {
					$finalStatus = '<td align="center" style="color:#FFFF00;">Working On</td>';
				}
			return $finalStatus;
			}

			#-----------------------------------------------------------
			# Function reviewStatus
			# returns the column for the status of a submission
			#-----------------------------------------------------------

			function reviewStatus($status,$rid,$sid) {
				if($status == 0)
				{
					$finalStatus = '<td align="center" style="color:#FFFFFF;background:#FFFF00;">
					<form name="f1'.$rid.'" action="" method="post">
					<input type="hidden" name="rid" value="'.$rid.'" />
					<input type="hidden" name="sid" value="'.$sid.'" />
					<input type="hidden" name="auth" value="989534" />
					<input type="hidden" name="adsubvar" value="0" />
						<table>
							<tr>
								<td align="center" style="background:#FFFF00;"><input type="radio" name="status" value="0" checked="checked" /></td>
								<td style="background:#009933;"><input type="radio" name="status" value="1" onClick="f1'.$rid.'.submit()"; /></td>
								<td style="background:#CC0033;"><input type="radio" name="status" value="2" onClick="f1'.$rid.'.submit()"; /></td>
							</tr>
						</table>
					</form>
					</td>';
				}
				else if($status == 1)
				{
					$finalStatus = '<td align="center" style="color:#FFFFFF;background:#009933;">
						<form name="f1'.$rid.'" action="" method="post">
						<input type="hidden" name="rid" value="'.$rid.'" />
						<input type="hidden" name="sid" value="'.$sid.'" />
						<input type="hidden" name="adsubvar" value="1" />
						<input type="hidden" name="auth" value="989534" />
							<table>
								<tr>
									<td align="center" style="background:#FFFF00;"><input type="radio" name="status" value="0" disabled="disabled" /></td>
									<td style="background:#009933;"><input type="radio" name="status" value="1" checked="checked" /></td>
									<td style="background:#CC0033;"><input type="radio" name="status" value="2" onClick="f1'.$rid.'.submit()"; /></td>
								</tr>
							</table>
						</form>
					</td>';
				}
				else {
					$finalStatus = '<td align="center" style="color:#FFFFFF;background:#CC0033;">
					<form name="f1'.$rid.'" action="" method="post">
					<input type="hidden" name="rid" value="'.$rid.'" />
					<input type="hidden" name="sid" value="'.$sid.'" />
					<input type="hidden" name="auth" value="989534" />
					<input type="hidden" name="adsubvar" value="1" />
						<table>
							<tr>
								<td align="center" style="background:#FFFF00;"><input type="radio" name="status" value="0" disabled="disabled" /></td>
								<td style="background:#009933;"><input type="radio" name="status" value="1" onClick="f1'.$rid.'.submit()"; /></td>
								<td style="background:#CC0033;"><input type="radio" name="status" value="2" checked="checked" /></td>
							</tr>
						</table>
					</form>
					</td>';
				}
			return $finalStatus;
			}

			#-----------------------------------------------------------
			# Function checkKanji
			# take a series name and check the kanji
			#-----------------------------------------------------------

			function checkKanji($seriesName) {
				mysqli_query($conn, "SET NAMES 'utf8'");
				$query = "SELECT kanji
						FROM series
						WHERE seriesName='$seriesName';";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$kanji = $row['kanji'];
			return $kanji;
			}

			#-----------------------------------------------------------
			# Function checkRomaji
			# take a series name and check the romaji
			#-----------------------------------------------------------

			function checkRomaji($seriesName) {
				$query = "SELECT romaji
						FROM series
						WHERE seriesName='$seriesName';
						";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result);
			$romaji = $row['romaji'];
			return $romaji;
			}


			#-----------------------------------------------------------
			# Function checkForSpecialChars
			# Checks inputs and makes sure the special chars are taken care of..
			#-----------------------------------------------------------

			function checkForSpecialChars($input) {
				$html_entities = array (
					"&" =>  "&amp;",     #ampersand
					"á" =>  "&aacute;",     #latin small letter a
					"Â" =>  "&Acirc;",     #latin capital letter A
					"â" =>  "&acirc;",     #latin small letter a
					"Æ" =>  "&AElig;",     #latin capital letter AE
					"æ" =>  "&aelig;",     #latin small letter ae
					"À" =>  "&Agrave;",     #latin capital letter A
					"à" =>  "&agrave;",     #latin small letter a
					"Å" =>  "&Aring;",     #latin capital letter A
					"å" =>  "&aring;",     #latin small letter a
					"Ã" =>  "&Atilde;",     #latin capital letter A
					"ã" =>  "&atilde;",     #latin small letter a
					"Ä" =>  "&Auml;",     #latin capital letter A
					"ä" =>  "&auml;",     #latin small letter a
					"Ç" =>  "&Ccedil;",     #latin capital letter C
					"ç" =>  "&ccedil;",     #latin small letter c
					"É" =>  "&Eacute;",     #latin capital letter E
					"é" =>  "&eacute;",     #latin small letter e
					"Ê" =>  "&Ecirc;",     #latin capital letter E
					"ê" =>  "&ecirc;",     #latin small letter e
					"È" =>  "&Egrave;",     #latin capital letter E
					"û" =>  "&ucirc;",     #latin small letter u
					"Ù" =>  "&Ugrave;",     #latin capital letter U
					"ù" =>  "&ugrave;",     #latin small letter u
					"Ü" =>  "&Uuml;",     #latin capital letter U
					"ü" =>  "&uuml;",     #latin small letter u
					"Ý" =>  "&Yacute;",     #latin capital letter Y
					"ý" =>  "&yacute;",     #latin small letter y
					"ÿ" =>  "&yuml;",     #latin small letter y
					"Ÿ" =>  "&Yuml;",     #latin capital letter Y
					// Money symbols :D
					"¤" => "&#164;",
					"$" => "&#36;",
					"¢" => "&#162;",
					"£" => "&#163;",
					"¥" => "&#165;",
					"?" => "&#8355;",
					"£" => "&#8356;",
					"P" => "&#8359;",
					"€" => "&#128;",
					"%" => "&#37;",
					"‰" => "&#137;",
					"-" => "&#45;",
					"#" => "&#35;",
					/*"" => "",
					"" => "",
					"" => "",
					"" => "",*/
				);

				foreach ($html_entities as $key => $value) {
					$str = str_replace($key, $value, $str);
				}
				return $str;
			}


			#-----------------------------------------------------------
			# Function checkFakeEmail
			# checks domain against list and says if its spam or not
			#-----------------------------------------------------------

			function checkFakeEmail($subject) {
				$fakes = array("10minutemail.com","20minutemail.com","anonymbox.com","beefmilk.com","bsnow.net","bugmenot.com","deadaddress.com","despam.it","disposeamail.com","dodgeit.com","dodgit.com","dontreg.com","e4ward.com","emailias.com","emailwarden.com","enterto.com","gishpuppy.com","goemailgo.com","greensloth.com","guerrillamail.com","guerrillamailblock.com","hidzz.com","incognitomail.net","jetable.org","kasmail.com","lifebyfood.com","lookugly.com","mailcatch.com","maileater.com","mailexpire.com","mailin8r.com","mailinator.com","mailinator.net","mailinator2.com","mailmoat.com","mailnull.com","meltmail.com","mintemail.com","mt2009.com","myspamless.com","mytempemail.com","mytrashmail.com","netmails.net","odaymail.com","pookmail.com","shieldedmail.com","smellfear.com","sneakemail.com","sogetthis.com","soodonims.com","spam.la","spamavert.com","spambox.us","spamcero.com","spamex.com","spamfree24.com","spamfree24.de","spamfree24.eu","spamfree24.info","spamfree24.net","spamfree24.org","spamgourmet.com","spamherelots.com","spamhole.com","spaml.com","spammotel.com","spamobox.com","spamspot.com","tempemail.net","tempinbox.com","tempomail.fr","temporaryinbox.com","tempymail.com","thisisnotmyrealemail.com","trash2009.com","trashmail.net","trashymail.com","tyldd.com","yopmail.com","zoemail.com","tradermail.info","zippymail.info","suremail.info","safetymail.info","binkmail.com","tradermail.info","zippymail.info","suremail.info","safetymail.info","PutThisInYourSpamDatabase.com","SpamHerePlease.com","SendSpamHere.com","chogmail.com","SpamThisPlease.com","frapmail.com","obobbo.com","devnullmail.com","bobmail.info","slopsbox.com");

				$num_bots=0;
				foreach($fakes as $num=>$fakes){
				preg_match("/".$fakes."/i", $subject, $matches);
					if(count($matches) >0){
					$num_bots++;
					}
					else{
					}
				}
				return $num_bots;
			}

			#-----------------------------------------------------------
			# Function trackerShowEpisode
			# ep: <epnumber> of <seriesName> on Date
			#-----------------------------------------------------------

			function trackerShowEpisode($uid,$elimit) {

				$query2 = "SELECT eid, dateViewed FROM episode_tracker WHERE uid='".$uid."' ORDER BY id DESC LIMIT $elimit, 1";
				$result2 = mysqli_query($conn, $query2) or die('Error : Such a Username does not exist!');
				$row2 = mysqli_fetch_array($result2);
				$eid = $row2['eid'];
				$dateViewed = $row2['dateViewed'];

				$query3 = "SELECT seriesName FROM episode WHERE id='".$eid."'";
				$result3 = mysqli_query($conn, $query3) or die('Error ');
				$row3 = mysqli_fetch_array($result3);
				$seriesName = $row3['seriesName'];
				$fullSeriesName = checkSeriesNoLink($seriesName);
						$Variable02 = substr($fullSeriesName, 0, 15);
						$Variable02.= "...";
				$dateViewed2 = date("M jS",$dateViewed);
				return 'ep: '.checkEpNoLink($eid,'epnum').' of '.$Variable02.'on '.$dateViewed2;

			}

			#-----------------------------------------------------------
			# Function trackerShowEpisode
			# ep: <epnumber> of <seriesName> on Date
			#-----------------------------------------------------------

			/*function trackerShowEpisode($uid,$elimit) {

				$query2 = "SELECT eid, dateViewed FROM episode_tracker WHERE uid='".$uid."' ORDER BY id DESC LIMIT $elimit, 1";
				$result2 = mysqli_query($conn, $query2) or die('Error : Such a Username does not exist!');
				$row2 = mysqli_fetch_array($result2);
				$eid = $row2['eid'];
				$dateViewed = $row2['dateViewed'];

				$query3 = "SELECT seriesName FROM episode WHERE id='".$eid."'";
				$result3 = mysqli_query($conn, $query3) or die('Error ');
				$row3 = mysqli_fetch_array($result3);
				$seriesName = $row3['seriesName'];
				$fullSeriesName = checkSeriesNoLink($seriesName);
						$Variable02 = substr($fullSeriesName, 0, 15);
						$Variable02.= "...";
				$dateViewed2 = date("M jS",$dateViewed);
				return 'ep: '.checkEpNoLink($eid,'epnum').' of '.$Variable02.'on '.$dateViewed2;

			}*/

			#-----------------------------------------------------------
			# Function showAniDBLink
			# Takes an AniDB ID and makes it into a link
			#-----------------------------------------------------------

			function showAniDBLink($aid){
				if($aid == 0)
				{
					$finalproduct = '<div class="editcol" align="center">[&nbsp;&nbsp;]<div>';
				}
				else {
					$rawlink = 'http://anidb.net/perl-bin/animedb.pl?show=anime&amp;aid=';
					$finalproduct = '<div class="editcol" align="center">[<a href="'.$rawlink.$aid.'" target="_blank">X</a>]<div>';
				}
				return $finalproduct;
			}

			#-----------------------------------------------------------
			# Function userReviewedSeries
			# Take a userid and spit back all possible
			# series they have reviewed
			#-----------------------------------------------------------

			function userReviewedSeries($uid,$approval){
				$query = "SELECT s.id, s.fullSeriesName, r.sid, r.uid, r.approved FROM series s, reviews r WHERE r.uid='$uid' AND s.id = r.sid AND r.approved = '$approval' ORDER BY s.fullSeriesName ASC";
				$result = mysqli_query($conn, $query);
				$approved = mysqli_num_rows($result);
				if($approved == 0)
				{
					$showReturn = 'No approved reviews!';
				}
				else {
					while(list($id) = mysqli_fetch_array($result, MYSQL_NUM))
					{
						$showReturn = checkSeriesSid($id)."<br />\n";
					}
				}
				return $showReturn;
			}

			#-----------------------------------------------------------
			# Function checkLoginStatus
			# Takes the login info and checks, if the user is logged in
			# spits back login information that is returned by the browser
			#-----------------------------------------------------------

			function checkLoginStatus($globalnonid,$remoteAddr,$userAgent){
				if(isset($globalnonid))
				{
					$query = "SELECT * FROM users WHERE ID='$globalnonid'";
					$result = mysqli_query($conn, $query);
					$row = mysqli_fetch_array($result);
					if(isset($_COOKIE['authenticate']) && $_COOKIE['authenticate'] == md5 ( $remoteAddr . $row['Password'] . $userAgent ) ) {
						//they clear the authentication process...
						$Logged = 1;
						$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $globalnonid . '\'';
						mysqli_query($conn, $query);
						$PermissionLevelAdvanced = $row['Level_access'];
						$timeZone = $row['timeZone'];
						$bannedornot = $row['Active'];
						$name = $row['Username'];
						$canDownload = $row['canDownload'];
						$postBan = $row['postBan'];
						$siteTheme = $row['theme'];
						$forumBan = $row['forumBan'];
						$messageBan = $row['messageBan'];
						$viewNotifications = $row['viewNotifications'];
					}
					else {
						if(isset($_SESSION['user_id']))
						{
							$Logged = 1;
							$PermissionLevelAdvanced = $row['Level_access'];
							$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $globalnonid . '\'';
							mysqli_query($conn, $query);
							$timeZone = $row['timeZone'];
							$bannedornot = $row['Active'];
							$name = $row['Username'];
							$canDownload = $row['canDownload'];
							$postBan = $row['postBan'];
							$siteTheme = $row['theme'];
							$ftwsub = $row['ftwsub'];
							$forumBan = $row['forumBan'];
							$messageBan = $row['messageBan'];
							$viewNotifications = $row['viewNotifications'];
						}
						else {
							$Logged = 0;
							$PermissionLevelAdvanced = 0;
							$timeZone = '-6';
							$canDownload = 0;
							$siteTheme = 0;
							$postBan = 0;
							$name = '';
							$bannedornot = 0;
							$globalnonid = 0;
							$forumBan = 0;
							$messageBan = 0;
							$viewNotifications = 0;
						}
					}
				}
				else {
					$Logged = 0;
					$PermissionLevelAdvanced = 0;
					$timeZone = '-6';
					$canDownload = 0;
					$siteTheme = 0;
					$postBan = 0;
					$name = '';
					$bannedornot = 0;
					$globalnonid = 0;
					$forumBan = 0;
					$messageBan = 0;
					$viewNotifications = 0;
				}
				$returnArray = array($Logged,$globalnonid,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme,$forumBan,$messageBan,0,$viewNotifications);
				return $returnArray;
			}

			#-----------------------------------------------------------
			# Function newMessages
			# checks messages for a user and
			# returns if they have any or not.
			#-----------------------------------------------------------

			function newMessages($uid){
				$query   = "SELECT COUNT(id) AS unreadMsgs FROM messages WHERE rid='".$uid."' AND viewed='1'";
				$result  = mysqli_query($conn, $query) or die('Error, query failed');
				$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
				$unreadMsgs = $row['unreadMsgs'];
				return $unreadMsgs;
			}

			#-----------------------------------------------------------
			# Function showAvailableForums
			# Take the Level_access and show certain forums to people.
			# one var controls full function
			#-----------------------------------------------------------

			function showAvailableForums($pud){
				$query = "SELECT cid, ctitle FROM forums_categories WHERE cpermission LIKE '%".$pud."%'";
				$result = mysqli_query($conn, $query);
				while(list($cid,$ctitle) = mysqli_fetch_array($result, MYSQL_NUM))
				{
					$showReturn1 = '';
					$showReturnBegin = '<li><a href="/forums">'.$ctitle.'</a><ul>';
					$query1 = "SELECT fid, ftitle FROM forums_forum WHERE fpermission LIKE '%".$pud."%' AND fcid='".$cid."' ORDER BY forder";
					$result1 = mysqli_query($conn, $query1);
					while(list($fid,$ftitle) = mysqli_fetch_array($result1, MYSQL_NUM))
					{
						$showReturn1 .= '<li><a href="/forums/index.php?forum='.$fid.'">'.$ftitle.'</a></li>';
					}
					$showReturnFinal .= $showReturnBegin.$showReturn1.'</ul></li>';
				}
				return $showReturnFinal.'<li><A href="/forums/view-active-topics">Today\'s Active Topics</a></li>';
			}

			#-----------------------------------------------------------
			# Function showAvailableVideos
			# Takes the level access and returns
			# available videos to that user.
			#-----------------------------------------------------------

			function showAvailableVideos($accesslevel) {
				$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, seriesId, fullSeriesName FROM series WHERE active='yes' ORDER BY seriesName";
				$query = mysqli_query ($sql) or die (mysqli_error());
				$total_rows = mysqli_num_rows($query) or die("Error: 1". mysqli_error(). " with query ". $query);
				while ($records = @mysqli_fetch_array($query)) {
					$alpha[$records['letter']] += 1;
					${$records['letter']}[$records['seriesId']] = $records['fullSeriesName'];
				}
				$countup = '';

				$returnFirstPart = '';
				$videoList = '';
				foreach(range('A','Z') as $i) {
					if (array_key_exists ("$i", $alpha)) {
						$videoList .= '<li><a href="/anime"> Series '.$i."</a><ul>\n";
						foreach ($$i as $key=>$value) {
							$videoList .=  "<li>".checkSeriesWIcons($key)."</li>\n";
							$returnFirstPart++;
						}
						$videoList .= "</ul></li>";
					}
				}
				return $videoList;
			}

			#-----------------------------------------------------------
			# Function seriesStatistics
			# Takes a given series id number
			# and returns total episodes and movies.
			#-----------------------------------------------------------

			function seriesStatistics($id) {
				mysqli_query($conn, "SET NAMES 'utf8'");
				$query = "SELECT kanji, romaji
						FROM series
						WHERE id='$id';";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$kanji = $row['kanji'];
				$romaji = $row['romaji'];

				$returnInfo = limitCharacters($romaji,40).'<br />'.limitCharacters($kanji,40);
				return $returnInfo;
			}

			#------------------------------------------------------------
			# Function maxOf32Chars
			# measures the length and makes sure that the length is
			# limited to 32 CHARS
			#------------------------------------------------------------

			function maxOf32Chars($id,$information)
			{
				$spanbefore = '<span id="'.$id.'" style="display: none">';
				$properamoutn = strlen($information)-30;
				$information2 = substr($information,0,30). $spanbefore . substr($information,-$properamoutn) .'</span> <a id="morelink'.$id.'" href="javascript:showFullComment('.$id.')" class="storylinks">...</a>';
				return $information2;
			}

			#------------------------------------------------------------
			# Function getImageUrl
			# takes an ID number and gives back a full
			# IMG tag for an avatar
			#------------------------------------------------------------

			function getImageUrl($size,$uid,$type)
			{
				if($type == 'anime')
				{
					$returnUrl = '//animeftw.tv/images/resize/anime/s-small/'.$uid.'.jpg';
				}
				else {
					$query   = "SELECT avatarActivate, avatarExtension FROM users WHERE ID='".$uid."'";
					$result  = mysqli_query($conn, $query) or die('Error, query failed');
					$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
					$avatarActivate = $row['avatarActivate'];
					if($type == 'profile')
					{
						if($avatarActivate == 'no')
						{
							$returnUrl = '//animeftw.tv/images/avatars/default.gif';
						}
						else {
							$returnUrl = '//animeftw.tv/images/avatars/user'.$uid.'.'.$row['avatarExtension'];
						}
					}
					else {
						if($avatarActivate == 'no')
						{
							$returnUrl = '//animeftw.tv/images/resize/user/'.$size.'/default.gif';
						}
						else {
							$returnUrl = '//animeftw.tv/images/resize/user/'.$size.'/user'.$uid.'.'.$row['avatarExtension'];
						}
					}
				}
				return $returnUrl;
			}

			#------------------------------------------------------------
			# Function showEpisodeInfo
			# Give an episode
			# @Param: $seriesname, $epnumber
			# IMG tag for an avatar
			#------------------------------------------------------------

			function showEpisodeInfo($seriesname,$epnum,$mov)
			{
				if($mov == 'ep'){$movvar = "AND Movie='0' AND ova='0'";}
				else if($mov == 'movie'){$movvar = "AND Movie='1' AND ova='0'";}
				else if($mov == 'ova'){$movvar = "AND Movie='0' AND ova='1'";}
				$query   = "SELECT id, epnumber, epname, vidheight, vidwidth, epprefix, subGroup, date, uid, report, videotype FROM episode WHERE seriesname='".$seriesname."' AND epnumber='".$epnum."' ".$movvar;
				$result  = mysqli_query($conn, $query);
				$numEpisodes = mysqli_num_rows($result);
				if($numEpisodes == 0){
					$episodeArray = array($epnum,0,0,0,0,0,0,0,0,0,0,0);
					}
				else {
					$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
					$episodeArray = array($row['epnumber'],$row['epname'],$row['vidheight'],$row['vidwidth'],$row['epprefix'],$row['subGroup'],$row['date'],$row['uid'],$row['report'],$row['videotype'],$row['id'],1);
				}
				return $episodeArray;
			}

			#------------------------------------------------------------
			# Function showSeriesInfo
			# Give a seoname and it will give info on the series
			# @Param: $seoname
			#------------------------------------------------------------

			function showSeriesInfo($seoname)
			{
				$query   = "SELECT id, seriesName, seoname, fullSeriesName, videoServer, maxEps, description, ratingLink, seriesId, noteActivate, noteReason, aonly, prequelto, sequelto, category, total_reviews FROM series WHERE seoname='".$seoname."'";
				$result  = mysqli_query($conn, $query);
				$row     = mysqli_fetch_array($result, MYSQL_ASSOC);

				$seriesArray = array($row['id'],$row['seriesName'],$row['seoname'],$row['fullSeriesName'],$row['videoServer'],$row['maxEps'],$row['description'],$row['ratingLink'],$row['seriesId'],$row['noteActivate'],$row['noteReason'],$row['aonly'],$row['prequelto'],$row['sequelto'],$row['category'],$row['total_reviews']);
				return $seriesArray;
			}

			#-------------------------------------------------------------
			# Function shareOptionsUpdated
			# Shows the options accessable to users based on
			# sharing of current episodes
			#-------------------------------------------------------------
			function shareOptionsUpdated($ep,$seriesName) {

				#series query
				$query1 = "SELECT seoname, fullSeriesName, moviesonly, OVA, seriesList FROM series WHERE seriesName='$seriesName'";
				$result1 = mysqli_query($conn, $query1);
				$row = mysqli_fetch_array($result1);
				$seoname = $row['seoname'];
				$fullSeriesName = $row['fullSeriesName'];
				$moviesOnly = $row['moviesonly'];
				$OVA = $row['OVA'];
				$seriesList = $row['seriesList'];
				//$fullSeriesName = addPluses($fullSeriesName);
$fullOutput = '<div class="objects"><a id="facebooklink" title="Share this Episode on Facebook" href="http://www.facebook.com/share.php?u=http%3A%2F%2Fwww.animeftw.tv%2Fanime%2F'.$seoname.'%2Fep-'.$ep.'" target="_blank"><span>facebook share</span></a>
<a id="myspacelink" title="Share this Episode on MySpace" href="http://www.myspace.com/Modules/PostTo/Pages/?t='.$fullSeriesName.'+'.$ep.'+at+AnimeFTW.tv%21&amp;c=%3Ca+href%3D%22http%3A%2F%2Fwww.animeftw.tv%2Fanime%2F'.$seoname.'%2Fep-'.$ep.'%22+title%3D%22http%3A%2F%2Fwww.animeftw.tv%2F%2Fanime%2F'.$seoname.'%2Fep-'.$ep.'%22%3Ehttp%3A%2F%2Fwww.animeftw.tv%2F%2Fanime%2F'.$seoname.'%2Fep-'.$ep.'%3C%2Fa%3E%3Cbr+%2F%3E%3Cbr+%2F%3E%3Ca+href%3D%22http%3A%2F%2Fwww.animeftw.tv%2F%2Fanime%2F'.$seoname.'%2Fep-'.$ep.'" target="_blank"><span>myspace share</span></a>
<a id="twitterlink" title="Share this Episode on Twitter" href="http://www.twitter.com/home?status=Watching+'.urlencode($fullSeriesName).'+episode+'.$ep.'+at+AnimeFTW.tv+http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>twitter share</span></a>
<a id="blinklink" title="Share this Episode on BlinkList" href="http://blinklist.com/blink?t=Watching+'.urlencode($fullSeriesName).'+episode+'.$ep.'+at+AnimeFTW.tv&u=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>blinklist share</span></a>
<a id="deliciouslink" title="Share this Episode on Del.ico.us" href="http://del.icio.us/post?v=2&amp;url=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>delicious share</span></a>
<a id="digglink" title="Share this Episode on Digg" href="http://digg.com/submit?phase=2&amp;url=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>digg share</span></a>
<a id="redditlink" title="Share this Episode on Reddit" href="http://reddit.com/submit?url=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>reddit share</span></a>
<a id="stumbleuponlink" title="Share this Episode on StumbleUpon" href="http://www.stumbleupon.com/submit?url=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>stumble share</span></a>
<a id="newsvinelink" title="Share this Episode on Newsvine" href="http://www.newsvine.com/_tools/seed&save?u=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'&h=Watching+'.urlencode($fullSeriesName).'+episode+'.$ep.'+at+AnimeFTW.tv" target="_blank"><span>newsvine share</span></a>
<a id="technoratilink" title="Share this Episode on Technorati" href="http://technorati.com/signup/?f=favorites&amp;Url=http://www.animeftw.tv/anime/'.$seoname.'/ep-'.$ep.'" target="_blank"><span>technorati share</span></a></div>';

					return $fullOutput;
			}

			#-------------------------------------------------------------
			# Function showListing
			# Shows in 3 cloumn tier of series listings
			# --
			#-------------------------------------------------------------

			function showListing ($listType,$sort,$alevel,$stype){
				if($alevel == 0){$aonly = "AND aonly='0'";}
				else if ($alevel == 3){$aonly = "AND aonly<='1'";}
				else{$aonly = '';}
				if($stype == 0){
					if($sort == NULL){
						echo '<div align="center"><a href="/anime/age/e"><img src="//animeftw.tv/images/ratings/e.jpg" alt="" /></a>&nbsp;<a href="/anime/age/12"><img src="//animeftw.tv/images/ratings/12+.jpg" alt="" /></a>&nbsp;<a href="/anime/age/15"><img src="//animeftw.tv/images/ratings/15+.jpg" alt="" /></a>&nbsp;<a href="/anime/age/18"><img src="//animeftw.tv/images/ratings/18+.jpg" alt="" /></a></div><br />';
						$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' ".$aonly."ORDER BY fullSeriesName";
					}
					else {
						$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' ".$aonly."AND category LIKE '%".mysqli_real_escape_string($conn, $sort)."%' ORDER BY seriesName";
					}
				}
				else {
					echo '<div align="center"><a href="/anime/age/e"><img src="//animeftw.tv/images/ratings/e.jpg" alt="" /></a>&nbsp;<a href="/anime/age/12"><img src="//animeftw.tv/images/ratings/12+.jpg" alt="" /></a>&nbsp;<a href="/anime/age/15"><img src="//animeftw.tv/images/ratings/15+.jpg" alt="" /></a>&nbsp;<a href="/anime/age/18"><img src="//animeftw.tv/images/ratings/18+.jpg" alt="" /></a></div><br />';
					if($sort == NULL){
						$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' ".$aonly."ORDER BY fullSeriesName";
					}
					else {
						$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' ".$aonly."AND ratingLink LIKE '%".mysqli_real_escape_string($conn, $sort)."%' ORDER BY seriesName";
					}
				}
				$query = mysqli_query ($sql) or die (mysqli_error());
				$total_rows = mysqli_num_rows($query) or die("<br />Error: No results found");
				while ($records = @mysqli_fetch_array ($query)) {
					$alpha[$records['letter']] += 1;
					${$records['letter']}[$records['id']] = $records['fullSeriesName'];
				}
				echo '<div align="center">';
				foreach(range('A','Z') as $i) {
					echo (array_key_exists ("$i", $alpha)) ? '<a href="#'.$i.'" title="'.$alpha["$i"].' results">'.$i.'</a>' : "$i";
					echo ($i != 'Z') ? ' | ':'';
				}
				echo '</div><br />';
				// Create Data Listing
				$countup = 1;
				$columncount = 1;
				$col = 2;
				echo "<div id=\"col1\">\n";

				floor($total_rows/3);
				foreach(range('A','Z') as $i) {
					if (array_key_exists ("$i", $alpha)) {

						echo '		<a name="'.$i.'"></a><h2>'.$i."</h2>\n";
						foreach ($$i as $key=>$value) {
							echo "		<div>".checkSeriesWIconsV3($key)."</div>\n";
							$countup++;
							if($countup == (floor($total_rows/3)) || $countup == (floor(($total_rows/3)*2)) || $countup == (floor(($total_rows/3)*3)) || $countup == floor(($total_rows/3)*4))
							{
								if($columncount == 3)
								{
								echo "		\n";
								}
								else {
								echo "		</div>\n";
									if($columncount == 1)
									{
										echo "		<div id=\"col2outer\">\n";
									}
								echo "		<div id=\"col$col\">\n";
								$columncount++;
								$col++;
								}
							}
						}
						echo "		<br />\n";
					}

				}
				echo '</div></div></div>';
				echo '<script type="text/javascript">
						$(document).ready(function(){
							$(".formInfo").tooltip({tooltipcontentclass:"animetip"})
						});;
					</script>';
			}

			#-------------------------------------------------------------
			# Function checkBan
			# Takes a given IP and checks to see if it is banned
			# @param $ip
			#-------------------------------------------------------------

			function checkBan($ip){
				//$ip = $_SERVER['REMOTE_ADDR']; #get the users ip address
				$getip = mysqli_query($conn, "SELECT * FROM `banned` WHERE `ip` = '$ip'"); #select the IP from the database
				$results = mysqli_query($conn, "SELECT * FROM `banned` WHERE `ip` = '$ip'"); #select the IP from the database
				$row = mysqli_fetch_array($results);
									$ip = $row['ip'];
									$reason = $row['reason'];
				if(mysqli_num_rows($getip) > 0)
				{
					die("You are currently banned from viewing this site!<br />Reason: $reason");

				} #if the user's ip address is in the database, then kill the script, and tell them that they are banned
			}

			#-------------------------------------------------------------
			# Function tagCloud
			# Place function on any page to get a tag cloud.
			# @param NULL
			#-------------------------------------------------------------

			function tagCloud($list){
				include('wordcloud.class.php');
				$cloud = new wordcloud();
				$getBooks = mysqli_query($conn, "SELECT name FROM categories ORDER BY name DESC");
				if ($getBooks)
				{
					while ($rowBooks = mysqli_fetch_assoc($getBooks))
					{
					//$getTags = explode(' ', $rowBooks['category']);
					$getTags = split(", ", $rowBooks['name']);
						foreach ($getTags as $key => $value)
						{
							$value = trim($value);
							$cloud->addWord($value);
						}
					}
				}
				$cloud->orderBy('word','ASC');
				$myCloud = $cloud->showCloud('array');
				if (is_array($myCloud))
				{
					//$myCloud = natcasesort($myCloud);
					foreach ($myCloud as $key => $value)
					{
						echo ' <a href="/'.$list.'/sort/'.$value['word'].'" class="size'.$value['range'].'">'.$value['word'].'</a> &nbsp;';
					}
				}
			}

			#------------------------------------------------------------
			# Function limitCharacters
			# Takes @param chars and reports back a condensed version
			# @param characters
			#------------------------------------------------------------

			function limitCharacters($input,$chars)
			{
				$counted = strlen($input);
				$properamoutn = strlen($input)-$chars;
				if($counted > $chars){$information2 = '<span title="'.$input.'">'.substr($input,-$counted,$chars).'..</span>';}
				else {$information2 = $input;}
				return $information2;
			}


		#------------------------------------------------------------
		# Function forumThreadList
		# Shows the given forum list
		#------------------------------------------------------------

		function forumThreadList ($fid,$fseo,$requestedLimit,$PermissionLevelAdvanced){
		echo forumPinnedList($fid,$fseo,$requestedLimit,$PermissionLevelAdvanced);
		$query4  = "SELECT tid, ttitle, tpid, tfid, tclosed, tviews
		            FROM forums_threads
					WHERE tfid='$fid' AND tstickied='0' ORDER BY tclosed ASC, tupdated DESC LIMIT ".$requestedLimit.", 30";

		$result4 = mysqli_query($conn, $query4);

		echo "<tr>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'>&nbsp;</td>\n";
		echo "<td class='tbl2 forum-cap'><strong>Forum Topics</strong></td>\n";
		echo "</tr>\n";

		while(list($tid,$ttitle,$tpid,$tfid,$tclosed,$tviews) = mysqli_fetch_array($result4)) {
				   $ttitle = stripslashes($ttitle);

				   //HTML exploit fix
				   //Zigbigidorlu was here =D
				   $ttitle = htmlentities($ttitle);
		echo "<tr>\n";
		if ($tclosed == 1) {
		    $thread_image = "<img src='//animeftw.tv/images/forumimages/f_closed.gif' border='0' alt='Closed Topic' />";
		  } else {
		    $thread_image = "<img src='//animeftw.tv/images/forumimages/f_norm_no_dot.gif' border='0' alt='Open Topic' />";
		  }
            $thread_subject = "<a id='topic-".$tid."' href='/forums/$fseo/topic-".$tid."/s-0' >".$ttitle."</a>";


		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$thread_image."</td>\n";
		echo "<td width='100%' class='tbl1'>".$thread_subject."</td>\n";

			$query3 = mysqli_query($conn, "SELECT COUNT(pid) FROM forums_post WHERE ptid='$tid'");
			$total_thread_posts = mysqli_result($query3, 0);
			$total_thread_posts2 = $total_thread_posts-1;

		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$total_thread_posts2."</td>\n";
		echo "<td align='left' width='1%' class='tbl1' style='white-space:nowrap'>".checkUserNameNumber($tpid)."</td>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$tviews."</td>\n";

			//this would be a good time to make a mysql update for this topic.. for whever a person looks at it...
			$query02 = "SELECT pid, puid, pdate FROM forums_post WHERE ptid='$tid' ORDER BY pid DESC LIMIT 1";
			$result02 = mysqli_query($conn, $query02);
			$row02 = mysqli_fetch_array($result02);
			$pid = $row02['pid'];
			$puid = $row02['puid'];
			$pdate3 = $row02['pdate'];
			$pdate3 = timeZoneChange($pdate3,$timeZone);
			$pdate4 = date("M j Y, h:i A",$pdate3);
			$last_post_by = "<a href='/forums/".$fseo."/topic-".$tid."/showlastpost'>Last post by:</a>&nbsp;".checkUserNameNumber($puid);

		echo "<td width='1%' class='tbl1' style='white-space:nowrap'>".$pdate4."<br />".$last_post_by."</td>\n";

		if ($PermissionLevelAdvanced == 1 || $PermissionLevelAdvanced == 2) {
		    $input_checkbox = "<input class='modcheck' type='checkbox' name='modcheck' value='".$tid."' />";

		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$input_checkbox."</td>\n";
	  } else {
	  }
		echo "</tr>\n";
	  }
		echo "</table><!--sub_forum_table-->\n";
	  }

		#------------------------------------------------------------
		# Function showAvailableEpisodes
		# Use the seriesname and return a list of episodes
		#------------------------------------------------------------

		function showAvailableEpisodes($seriesname,$agent,$accesslevel,$videoServer,$fullSeriesName,$seoname,$canDownload,$type){
			$query = mysqli_query($conn, "SELECT id FROM episode WHERE seriesname='$seriesname' AND Movie='0' AND ova='0'");
			$total_episodes = mysqli_num_rows($query);
			if($total_episodes == 0){}
			else {
				$query   = "SELECT id, sid, epnumber, epname, epprefix, videotype, image FROM episode WHERE seriesname='$seriesname' AND Movie='0' AND ova='0' ORDER BY epnumber";
				$result  = mysqli_query($conn, $query);
				echo '<div><b>Episodes:</b></div>';
				//echo '<div id="tooltipdiv">';
				while(list($id,$sid,$epnumber,$epname,$epPrefix,$videotype,$image) = mysqli_fetch_array($result))
				{
					if($image == 0){
						$imgUrl = '' . $CDNHost . '/video-images/noimage.png';
					}
					else {
						$imgUrl = "{$CDNHost}/video-images/{$sid}/{$id}_screen.jpeg";
					}
					$epname    = stripslashes($epname);
					if ($accesslevel == 7 || $canDownload == 1){
						if($type == 1 && ($accesslevel == 7 || $canDownload == 1)){
							$imgurl = '<a href="http://'.$videoServer.'.animeftw.tv/'.$seriesname.'/'.$epPrefix.'_' . $epnumber . '_ns.'.$videotype.'"><img src="//animeftw.tv/images/disk.png" alt="Advanced Download" title="Click To download '.$fullSeriesName.' Episode ' . $epnumber . '" style="float:left;padding-top:2px;padding-right:3px;" border="0" /></a>';
							//$imgurl = '<a href="http://'.$videoServer.'.animeftw.tv/'.$seriesname.'/'.$epPrefix.'_' . $epnumber . '_ns.'.$videotype.'"><img src="//animeftw.tv/images/disk.png" alt="Advanced Download" title="Click To download '.$fullSeriesName.' Episode ' . $epnumber . '" style="float:right;padding-top:8px;" border="0" /></a>';
						}
						else {$imgurl = '';}

					}
					else {$imgurl = '';}
					if($accesslevel == 0)
					{
						echo "<div style=\"padding-top:5px;\">Episode #".$epnumber.": ".$epname."</div>";
					}
					else
					{
						if($type == 0){
							echo "<a class=\"feature01\" href=\"/anime/".$seoname."/ep-".$epnumber."\" title=\"Titled: ".$epname."\">";
							echo "	<span class=\"overlay01\">";
							echo "		<span class=\"caption01\">Episode: ".$epnumber."<br />Titled: ".$epname."</span>";
							echo "	</span>";
							echo "	<img src=\"$imgUrl\" alt=\"Episode: ".$epnumber."\" height=\"90\" />";
							echo "</a>";
						}
						else {
							echo $imgurl."<div style=\"padding-top:5px;\">Episode #".$epnumber.": <a href=\"/anime/".$seoname."/ep-".$epnumber."\" onmouseover=\"ajax_showTooltip(window.event,'/scripts.php?view=profiles&amp;show=eptips&amp;id=".$id."',this);return false;\" onmouseout=\"ajax_hideTooltip()\">".$epname."</a></div>";
						}
					}
				}
				//echo '</div>';
			}
		}

		#------------------------------------------------------------
		# Function showAvailableMovies
		# Use the seriesname and return a list of Movies
		#------------------------------------------------------------

		function showAvailableMovies($seriesname,$agent,$accesslevel,$videoServer,$fullSeriesName,$seoname,$canDownload){
			$query = mysqli_query($conn, "SELECT id FROM episode WHERE seriesname='$seriesname' AND Movie='1' AND ova='0'");
			$total_episodes = mysqli_num_rows($query);
			if($total_episodes == 0){}
			else {
				if($agent == 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13' && $accesslevel == '3')
				{
					$query   = "SELECT epnumber, epname, epprefix, videotype FROM episode WHERE seriesname='$seriesname' AND Movie='1' AND ova='0' ORDER BY epnumber LIMIT 0, 2";
					$result  = mysqli_query($conn, $query);
					$xbmcplus = '0';
				}
				else {
					$query   = "SELECT epnumber, epname, epprefix, videotype FROM episode WHERE seriesname='$seriesname' AND Movie='1' AND ova='0' ORDER BY epnumber";
					$result  = mysqli_query($conn, $query);
					$xbmcplus = '1';
				}
				echo '<br /><div><b>Movies:</b></div>';
				while(list($epnumber,$epname,$epPrefix,$videotype) = mysqli_fetch_array($result))
				{
					$epname    = stripslashes($epname);
					if($accesslevel == 0)
					{
						echo "<div style=\"padding-top:5px;\">Movie #".$epnumber.": ".$epname."</div>";
					}
					else
					{
						if ($accesslevel == 7 || $canDownload == 1)
						{
							echo '<a href="http://'.$videoServer.'.animeftw.tv/movies/'.$epPrefix.'_' . $epnumber . '_ns.'.$videotype.'"><img src="//animeftw.tv/images/disk.png" alt="Advanced Download" title="Click To download '.$fullSeriesName.' Episode ' . $epnumber . '" style="float:left;padding-top:2px;padding-right:3px;" border="0" /></a>';
						}
						else {
						}
						echo "<div style=\"padding-top:5px;\">Movie #".$epnumber.": <a href=\"/anime/".$seoname."/movie-".$epnumber."\">".$epname."</a>";
						echo "</div>";
					}
				}
				if($xbmcplus == 0)
				{
					echo '<div style="padding-top:5px;">Episode #0: <a href="/anime/">ERROR: Basic Members are only allowed to watch the first 2 episodes of a series, please signup for Advanced Membership to watch more.</a></div>';
				}
			}
		}

		#------------------------------------------------------------
		# Function showAvailableOvas
		# Use the seriesname and return a list of Movies
		#------------------------------------------------------------

		function showAvailableOvas($seriesname){
			$query = mysqli_query($conn, "SELECT id FROM episode WHERE seriesname='$seriesname' AND Movie='0' AND ova='1'");
			$total_episodes = mysqli_num_rows($query);
			if($total_episodes == 0){}
			else {
				if($agent == 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13' && $accesslevel == '3')
				{
					$query   = "SELECT epnumber, epname, epprefix, videotype FROM episode WHERE seriesname='$seriesname' AND Movie='0' AND ova='1' ORDER BY epnumber LIMIT 0, 2";
					$result  = mysqli_query($conn, $query);
					$xbmcplus = '0';
				}
				else {
					$query   = "SELECT epnumber, epname, epprefix, videotype FROM episode WHERE seriesname='$seriesname' AND Movie='0' AND ova='1' ORDER BY epnumber";
					$result  = mysqli_query($conn, $query);
					$xbmcplus = '1';
				}
				echo '<br /><div><b>Movies:</b></div>';
				while(list($epnumber,$epname,$epPrefix,$videotype) = mysqli_fetch_array($result))
				{
					$epname    = stripslashes($epname);
					if($accesslevel == 0)
					{
						echo "<div style=\"padding-top:5px;\">Ova #".$epnumber.": ".$epname."</div>";
					}
					else
					{
						if ($accesslevel == 7 || $canDownload == 1)
						{
							echo '<a href="http://'.$videoServer.'.animeftw.tv/'.$seriesname.'/'.$epPrefix.'_' . $epnumber . '_ns.'.$videotype.'"><img src="//animeftw.tv/images/disk.png" alt="Advanced Download" title="Click To download '.$fullSeriesName.' Episode ' . $epnumber . '" style="float:left;padding-top:2px;padding-right:3px;" border="0" /></a>';
						}
						else {
						}
						echo "<div style=\"padding-top:5px;\">Ova #".$epnumber.": <a href=\"/anime/".$seoname."/movie-".$epnumber."\">".$epname."</a>";
						echo "</div>";
					}
				}
				if($xbmcplus == 0)
				{
					echo '<div style="padding-top:5px;">Episode #0: <a href="/anime/">ERROR: Basic Members are only allowed to watch the first 2 episodes of a series, please signup for Advanced Membership to watch more.</a></div>';
				}
			}
		}

		#------------------------------------------------------------
		# Function searchSeries
		# Searches through Series and gives back information.
		#------------------------------------------------------------

		function searchSeries($input,$userlevel){
			$input = mysqli_real_escape_string($conn, $input);
			if($userlevel == 0){$aonly = "aonly='0' AND ";}
			else if ($userlevel == 3){$aonly = "aonly<='1' AND ";}
			else{$aonly = '';}
			$query   = "SELECT id, seriesName, fullSeriesName, seoname, ratingLink, category, total_reviews FROM series WHERE".$aonly." fullSeriesName LIKE '%".$input."%' OR romaji LIKE '%".$input."%' OR kanji LIKE '%".$input."%' OR category LIKE '%".$input."%' ORDER BY seriesName ASC LIMIT 10";
			$result  = mysqli_query($conn, $query);
			$ts = mysqli_num_rows($result);
			if($ts > 0)
			{
				echo '<h2><b>Series Returned</b></h2>';
				while(list($id,$seriesName,$fullSeriesName,$seoname,$ratingLink,$category,$total_reviews) = mysqli_fetch_array($result))
				{
					$fullSeriesName = stripslashes($fullSeriesName);
					echo '<div class="searchdiv">';
					echo '<div style="float:left;width:100px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/"><img src="//animeftw.tv/images/resize/anime/medium/'.$id.'.jpg" alt="'.$fullSeriesName.'" border="0" /></a></div>';
					echo '<div class="searchinfo"><span style="font-size:16px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/">'.$fullSeriesName.'</a></span><br />Romaji: '.checkRomaji($seriesName).'<br />Kanji: '.checkKanji($seriesName).'<br />Categories: ';
					$episodes = split(", ",$category);
					foreach ($episodes as $value) {echo "<a href=\"http://".$_SERVER['HTTP_HOST']."/anime/sort/$value\">$value</a>, ";}
					echo '<br />Reviews: <a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/#reviews">'.$total_reviews.'</a> | <a href="/reviews">Write a review!</a></div>';
					echo '<div align="right">Favorite Series';
					if($userlevel == 1 || $userlevel == 2){echo '<br /><a href="http://dev.animaftw.tv/management/manage-series?id='.$id.'">Edit this Series</a><br /><a href="http://'.$_SERVER['HTTP_HOST'].'/management/manage-episodes?add=episode&amp;sid='.$id.'">Add a Episode</a>';}
					echo '</div></div><hr color="#E4E4E4" />';
				}
			}
		}
		/*
		<div class="items">
		<div>
			<div class="item">
		*/

		#------------------------------------------------------------
		# Function searchSeries2
		# Searches through Series and gives back information.
		#------------------------------------------------------------

		function searchSeries2($input,$userlevel){
			$input = mysqli_real_escape_string($conn, $input);
			$dualarray = array('2','5','8','11','14','17','20','23','26','29','32','35','38','41','44','47','50','53','56','59','62','65','68','71','74','77','80','83','86','89','92','95','98','101');
			if($userlevel == 0){$aonly = " AND aonly='0'";}
			else if ($userlevel == 3){$aonly = " AND aonly<='1'";}
			else{$aonly = '';}

			$ExplodedInput = explode(',',str_replace(' ', '', $input));

			if(count($ExplodedInput) > 1)
			{
				$subsearch = "";
				$i = 1;
				foreach($ExplodedInput as $value)
				{
					$subsearch .= "category LIKE '%".$value."%'";
					if($i < count($ExplodedInput))
					{
						$subsearch .= " AND ";
					}
					$i++;
				}
			}
			else
			{
				$subsearch = "fullSeriesName LIKE '%".$input."%' OR romaji LIKE '%".$input."%' OR kanji LIKE '%".$input."%' OR category LIKE '%".$input."%'";
			}

			$query   = "SELECT id, seriesName, fullSeriesName, seoname, ratingLink, category, total_reviews FROM series WHERE active='yes'".$aonly." AND ( " . $subsearch . " ) ORDER BY seriesName ASC LIMIT 100";
			$result  = mysqli_query($conn, $query);
			$ts = mysqli_num_rows($result);
			if($ts > 0)
			{
				$i=0;
				while(list($id,$seriesName,$fullSeriesName,$seoname,$ratingLink,$category,$total_reviews) = mysqli_fetch_array($result))
				{
					$fullSeriesName = stripslashes($fullSeriesName);
					echo '<div class="item">'."\n";
					echo '	<div class="searchdiv">'."\n";
					echo '		<div style="float:left;width:100px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/"><img src="//animeftw.tv/images/resize/anime/medium/'.$id.'.jpg" alt="'.$fullSeriesName.'" border="0" /></a></div>'."\n";
					echo '		<div class="searchinfo"><span style="font-size:16px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/">'.$fullSeriesName.'</a></span><br />Romaji: '.checkRomaji($seriesName).'<br />Kanji: '.checkKanji($seriesName).'<br />Categories: '."\n";
					$episodes = split(", ",$category);
					foreach ($episodes as $value) {echo "<a href=\"http://".$_SERVER['HTTP_HOST']."/anime/sort/$value\">$value</a>, ";}
					echo '<br />Reviews: <a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/#reviews">'.$total_reviews.'</a> | <a href="/reviews">Write a review!</a></div>'."\n";
					echo '		<div align="right">Favorite Series'."\n";
					if($userlevel == 1 || $userlevel == 2){echo '<br /><a href="http://dev.animaftw.tv/management/manage-series?id='.$id.'">Edit this Series</a><br /><a href="http://'.$_SERVER['HTTP_HOST'].'/management/manage-episodes?add=episode&amp;sid='.$id.'">Add a Episode</a>';}
					echo '		</div>
							</div><hr color="#E4E4E4" />'."\n";
					echo '</div>'."\n";
					if(in_array($i, $dualarray)){echo '</div><div>'."\n";}
					$i++;

				}
			}
		}




		#------------------------------------------------------------
		# Function searchEpisodes
		# Searches through Episodes and gives back information.
		#------------------------------------------------------------

		function searchEpisodes($input,$userlevel){
		}

		#------------------------------------------------------------
		# Function searchFriends
		# Searches through Friends and gives back information.
		#------------------------------------------------------------

		function searchFriends($input,$userlevel){
		}

		#------------------------------------------------------------
		# Function adv_count_words
		# Takes a string and gives word count back.
		#------------------------------------------------------------

		function adv_count_words($str){
			$words = 0;
			$str = eregi_replace(" +", " ", $str);
			$array = explode(" ", $str);
			for($i=0;$i < count($array);$i++)
			{
		 		if (eregi("[0-9A-Za-zÀ-ÖØ-öø-ÿ]", $array[$i]))
			 	$words++;
			}
			return $words;
	 	}

		#------------------------------------------------------------
		# Function NextEpisodes
		# Grabs a set amount of episodes before and after
		#------------------------------------------------------------

		function NextEpisodes($count,$seriesname,$epnumber,$epprefix,$moe,$seoname){
			// simple vars
			$nextep = $epnumber+1;
			$prevep = $epnumber-1;
			$currep = $epnumber;
			// Movie Ep OVA options
			if($moe == 'ep'){$moevar = "AND Movie = '0' AND ova = '0'";}
			else if($moe == 'movie'){$moevar = "AND Movie = '1' AND ova = '0'";}
			else if($moe == 'ova'){$moevar = "AND Movie = '0' AND ova = '1'";}
			// select all episodes for this series..
			$query = "SELECT COUNT(id) as numrows FROM episode WHERE seriesname='".$seriesname."' ".$moevar;
			$result = mysqli_query($conn, $query);
			$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
			$MaxEps = $row['numrows'];
			//key shortcuts... W00t!
			echo '<script type="text/javascript">
				document.onkeydown = function(e) {
					e = e || window.event;
					switch (e.keyCode) {';
			if($epnumber != 1 && $currep <= $MaxEps){
				echo '				case 37:
							document.location.href = "/anime/'.$seoname.'/'.$moe.'-'.$prevep.'";
							break;';
			}
			if($currep != $MaxEps){
				echo '				case 39:
							document.location.href = "/anime/'.$seoname.'/'.$moe.'-'.$nextep.'";
							break;';
			}
			echo '		}
				};
				</script>';
			// start real code.
			echo '<table><tr>';
			//Prev Ep code
			if($epnumber != 1){
				echo '<td valign="top"><a id="PrevEp" href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/'.$moe.'-'.$prevep.'" title="Previous Episode"><span>previous</span></a></td>';
			}
			else {
				//image with no link for previous ep here
			}
			//Previous Ep Image code...
			$query = "SELECT id, sid, epnumber, epname, epprefix, image FROM episode WHERE seriesName='".$seriesname."' AND epnumber < $epnumber ".$moevar." ORDER BY epnumber DESC LIMIT 0, 1";
			$result = mysqli_query($conn, $query);
			$rowb = mysqli_fetch_array($result, MYSQL_ASSOC);
			$br = mysqli_num_rows($result);
			if($br != 0){
				if($rowb['image'] == 0){$imvarb = '' . $CDNHost . '/video-images/noimage.png';}
				else {$imvarb = "{$CDNHost}/video-images/{$rowb['sid']}/{$rowb['id']}_screen.jpeg";}
					echo '<td valign="top"><div align="center"><a class="linkopacity" href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/'.$moe.'-'.$rowb['epnumber'].'" title="Episode #'.$rowb['epnumber'].'" >
						<img src="'.$imvarb.'" border="0" style="border:1px solid black;"  width="175" alt="Episode '.$rowb['epnumber'].'"></a><br />Previous Episode, #'.$rowb['epnumber'].'</div></td>';
			}
			//Current Ep Image code
			$query = "SELECT image, id FROM episode WHERE seriesName='".$seriesname."' AND epnumber = '".$epnumber."' ".$moevar;
			$result = mysqli_query($conn, $query);
			$rowc = mysqli_fetch_array($result, MYSQL_ASSOC);
			if($rowc['image'] == 0){$imvarc = '' . $CDNHost . '/video-images/noimage.png';}
			else {$imvarc = "{$CDNHost}/video-images/{$rowb['sid']}/{$rowc['id']}_screen.jpeg";}
			echo '<td valign="top"><div align="center"><a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" title="Current Episode, #'.$epnumber.'" >
					<img src="'.$imvarc.'" border="0" style="border:1px solid black;"  width="175" alt="Current Episode, '.$epnumber.'"></a><br />Current Episode, #'.$epnumber.'</div></td>';
			//Next Ep Image code...
			$query = "SELECT id, epnumber, epname, epprefix, image FROM episode WHERE seriesName='".$seriesname."' AND epnumber > $epnumber ".$moevar." ORDER BY epnumber ASC LIMIT 0, 1";
			$result = mysqli_query($conn, $query);
			$rowa = mysqli_fetch_array($result, MYSQL_ASSOC);
			$ar = mysqli_num_rows($result);
			if($ar != 0){
				if($rowa['image'] == 0){$imvara = '' . $CDNHost . '/video-images/noimage.png';}
				else {$imvara = "{$CDNHost}/video-images/{$rowb['sid']}/{$rowa['id']}_screen.jpeg";}
				echo '<td valign="top"><div align="center"><a class="linkopacity" href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/'.$moe.'-'.$rowa['epnumber'].'" title="Episode #'.$rowa['epnumber'].'" >
					<img src="'.$imvara.'" border="0" style="border:1px solid black;"  width="175" alt="Episode '.$rowa['epnumber'].'"></a><br />Next Episode, #'.$rowa['epnumber'].'</div></td>';
			}
			//Next Ep code
			if($currep != $MaxEps){
				echo '<td valign="top"><a id="NextEp" href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/'.$moe.'-'.$nextep.'" title="Next Episode"><span>previous</span></a></td>';
			}
			else {
				//image with no link for next ep here
			}
			echo '</tr></table>';
		}

		#--------------------------------------------------------
		# Function ListGroups
		#
		#--------------------------------------------------------

		//function ListGroups



		#--------------------------------------------------------
		# Function ShowListChange
		#
		#


