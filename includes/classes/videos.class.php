<?php
/****************************************************************\
## FileName: videos.class.php									 
## Author: Brad Riemann										 
## Usage: Displays Series information and Episode information.
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWVideos extends Config{
	var $id;
	var $ssl;
	var $UserArray; //added 8/4/2014 by robotman321
	var $RecentEps; //added 3/21/2015 by robotman321
	var $watchListSelection;
	var $Categories = array(); // added 10/10/2014 by robotman321
	
	public function __construct()
	{
		parent::__construct();
		$this->buildCategories();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
		$this->array_buildSiteSettings(); // ADDED 8/23/2015 by Robotman321, connects the settings so we can use them.
		
		$this->array_buildRecentlyWatchedEpisodes();
	}
	
	//Lets get the ID number.. Whatever it is..
	public function get_id($id){
		$this->id = $id;
	}
	
	// Using a SSL? We need to know!
	public function get_ssl($ssl_port){
		if($ssl_port == 80){
			$this->ssl = 'http';
		}
		else {
			$this->ssl = 'https';
		}
	}
	
	public function PageTitle($seo = NULL,$eid = NULL,$oid = NULL,$mid = NULL,$type){
		if($seo == NULL){
			if($type == 'anime')
			{
				$pt = "Anime Library - AnimeFTW.TV";
			}
			else 
			{
				$pt = "Video Library - AnimeFTW.TV";
			}
		}
		else {
			$SeriesArray = $this->showSeriesInfo($seo);
			if($SeriesArray[16] == 0){
				$pt = "404 Series Not Found - AnimeFTW.TV2";
			}
			else {
				if($eid != '' || $oid != '' || $mid != ''){
					$fsn = stripslashes($SeriesArray[3]);
					if($eid != ''){
						$pt = "Episode ".$eid." of ".$fsn." - AnimeFTW.TV";
					}
					else if($oid != ''){
						$pt = "OVA ".$oid." of ".$fsn." - AnimeFTW.TV";
					}
					else if($mid != ''){
						$pt = "Movie ".$mid." of ".$fsn." - AnimeFTW.TV";
					}
					else {
						$pt = "No one knew what to put here.. - AnimeFTW.TV";
					}
				}
				else {
					$pt = $SeriesArray[3]." - AnimeFTW.TV"; //Series name goes here..
				}
			}
		}
		return $pt;
	}
	
	public function tagCloud($list){
		include_once('wordcloud.class.php');
		$cloud = new wordcloud();
		$getBooks = mysql_query("SELECT name FROM categories ORDER BY name DESC");
		if ($getBooks)
		{
			while ($rowBooks = mysql_fetch_assoc($getBooks))
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
	
	#-------------------------------------------------------------
	# Function showListing
	# Shows in 3 cloumn tier of series listings
	# --
	#-------------------------------------------------------------
	
	public function showListing ($listType,$sort,$alevel,$stype,$url = NULL){
		if($alevel == 0){$aonly = "AND aonly='0'";}
		else if ($alevel == 3){$aonly = "AND aonly<='1'";}
		else{$aonly = '';}
		$catsort = $this->parseNestedArray($this->Categories, 'name', ucfirst($sort));
		if($stype == 0)
		{
			if($sort == NULL)
			{
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' ".$aonly."ORDER BY fullSeriesName";
			}
			else
			{
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' ".$aonly."AND category LIKE '% ".$catsort." %' ORDER BY seriesName";
			}
			
		}
		else if($stype == 1)
		{
			//for movies, airing and various other crap
			if($sort == 'airing'){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' AND stillRelease = 'yes' ".$aonly."ORDER BY fullSeriesName";
			}
			else if($sort == 'completed'){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' AND stillRelease = 'no' ".$aonly."ORDER BY fullSeriesName";
			}
			else if($sort == 'movies'){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' AND Movies > 0 ".$aonly."ORDER BY fullSeriesName";
			}
			else if($sort == 'mkv'){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' AND seriesType = 1 ".$aonly."ORDER BY fullSeriesName";
			}
			else if($sort == 'divx'){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' AND seriesType = 0 ".$aonly."ORDER BY fullSeriesName";
			}
			else if($sort == 'mp4'){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' AND seriesType = 2 ".$aonly."ORDER BY fullSeriesName";
			}
			else {
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' ".$aonly."AND category LIKE '% ".$catsort." %' ORDER BY seriesName";
			}
		}
		else {
			if($sort == NULL){
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' ".$aonly."ORDER BY fullSeriesName";
			}
			else {
				$sql = "SELECT UPPER(SUBSTRING(fullSeriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' AND active='yes' ".$aonly."AND ratingLink LIKE '%".mysql_real_escape_string($sort)."%' ORDER BY seriesName";
			}
		}
		
		if($url == NULL){
				echo '<div align="center"><a href="/anime/age/e"><img src="/images/ratings/e.jpg" alt="" /></a>&nbsp;<a href="/anime/age/12"><img src="/images/ratings/12+.jpg" alt="" /></a>&nbsp;<a href="/anime/age/15"><img src="/images/ratings/15+.jpg" alt="" /></a>&nbsp;<a href="/anime/age/18"><img src="/images/ratings/18+.jpg" alt="" /></a></div><br />';
		}
		error_reporting(E_ALL & ~E_NOTICE);
		$query = mysql_query ($sql) or die (mysql_error());
		$total_rows = mysql_num_rows($query) or die("<br />Error: No results found");
		while ($records = @mysql_fetch_array ($query))
		{
			$alpha[$records['letter']] += 1;
			${$records['letter']}[$records['id']] = $records['fullSeriesName'];
		}
		echo '
		<div align="center">
			Series Types: 
			<a href="/anime/type/airing">Airing Series <img src="' . $this->Host . '/airing_icon.gif" alt="Airing" style="vertical-align:middle;" border="0" /></a> | 
			<a href="/anime/type/completed">Completed Series</a> | 
			<a href="/anime/type/movies">Series with Movies <img src="' . $this->Host . '/movie_blue.png" alt="Movie" style="vertical-align:middle;" border="0" /></a> | 
			<a href="/anime/type/divx">DivX Based</a>
		</div>
		<div align="center">
			My WatchList Legend:
			<img src="' . $this->Host . '/flag_red.png" alt="planning to watch icon" style="vertical-align:middle;" border="0" /> Planning to Watch | 
			<img src="' . $this->Host . '/flag_yellow.png" alt="currently watching" style="vertical-align:middle;" border="0" /> Currently Watching | 
			<img src="' . $this->Host . '/flag_green.png" alt="currently watching" style="vertical-align:middle;" border="0" /> Finished Watching
		</div>
		<br />';
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
		$itemCount = 0;
		$letterArray = array('0','1','2','3','4','5','6','7','8','9','.','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

		echo '<div style="width:310px;display:inline-block;vertical-align:top;">';
		floor($total_rows/3);
		if($this->UserArray[0] == 1)
		{
			// tell the system to build the current users watchlist if they are logged in.
			$this->array_buildWatchListEntries();
		}
		else
		{
		}		
		
		foreach($letterArray as $i)
		{
			if (array_key_exists ("$i", $alpha))
			{	
				echo '		<a name="'.$i.'"></a><h2>'.$i."</h2>\n";
				foreach ($$i as $key=>$value)
				{
					if($url != NULL)
					{
						echo "		<div>".$this->DisplayLinks($key,1,$alevel)."</div>\n";
					}
					else 
					{
						echo "		<div>".$this->DisplayLinks($key,0,$alevel)."</div>\n";
					}
					$countup++;
					if($countup == (floor($total_rows/3)) || $countup == (floor(($total_rows/3)*2)) || $countup == (floor(($total_rows/3)*3)) || $countup == floor(($total_rows/3)*4))
					{
						if($columncount == 3){echo "		\n";}
						else {
						echo "		</div>\n";
						echo "		<div style=\"width:310px;display:inline-block;vertical-align:top;\">\n";
						$columncount++;
						$col++;
						}
					}
					$itemCount++;
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
	
	#------------------------------------------------------------
	# Function showEpisodeInfo
	# Give an episode
	# @Param: $seriesname, $epnumber
	# IMG tag for an avatar
	#------------------------------------------------------------
				
	private function showEpisodeInfo($sid,$epnum,$mov){
		if($mov == 'ep'){$movvar = "AND Movie='0' AND ova='0'";}
		else if($mov == 'movie'){$movvar = "AND Movie='1' AND ova='0'";}
		else if($mov == 'ova'){$movvar = "AND Movie='0' AND ova='1'";}
		else {$movvar = NULL;}
		$query   = "SELECT `id`, `sid`, `spriteId`, `epnumber`, `epname`, `vidheight`, `vidwidth`, `epprefix`, `subGroup`, `date`, `uid`, `report`, `videotype`, `hd`, `views`, `Movie` FROM episode WHERE sid='".$sid."' AND epnumber='".$epnum."' ".$movvar;
		$result  = mysql_query($query) or die('Error : ' . mysql_error());
		$numEpisodes = mysql_num_rows($result);
		if($numEpisodes == 0){
			$episodeArray = array($epnum,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,null);
		}
		else {
			$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			$episodeArray = array($row['epnumber'],$row['epname'],$row['vidheight'],$row['vidwidth'],$row['epprefix'],$row['subGroup'],$row['date'],$row['uid'],$row['report'],$row['videotype'],$row['id'],1,$row['hd'],1,$row['sid'],$row['id'],$row['views'],$row['Movie'],$row['spriteId']);
		}
		return $episodeArray;
	}
				
	#------------------------------------------------------------
	# Function showSeriesInfo
	# Give a seoname and it will give info on the series
	# @Param: $seoname
	#------------------------------------------------------------
				
	private function showSeriesInfo($seoname)
	{
		mysql_query("SET NAMES 'utf8'");
		$query   = "SELECT id, seriesName, synonym, seoname, fullSeriesName, videoServer, description, ratingLink, noteReason, aonly, prequelto, sequelto, category, total_reviews, hd, kanji, romaji FROM series WHERE seoname='".$seoname."'";
		$result  = mysql_query($query) or die('Error : ' . mysql_error()); 
		$numSeries = mysql_num_rows($result);
		
		if($numSeries == 0)
		{
			$seriesArray = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		}
		else {
			$row     = mysql_fetch_array($result, MYSQL_ASSOC);				
			$seriesArray = array($row['id'],$row['seriesName'],$row['seoname'],stripslashes($row['fullSeriesName']),$row['videoServer'],0,$row['description'],$row['ratingLink'],0,0,$row['noteReason'],$row['aonly'],$row['prequelto'],$row['sequelto'],$row['category'],$row['total_reviews'],1,$row['synonym'],1,$row['hd'],$row['kanji'],$row['romaji']);
		}
		return $seriesArray;
	}

	#-------------------------------------------------------------
	# Function showSpriteInfo
	# Give an Episode Id and it will return sprite info
	# @Param: $EpisodeArray[18]
	#-------------------------------------------------------------

	private function showSpriteInfo($spriteId) {
		if ($spriteId == null)
			return false;

		$sql	= "SELECT width, height, totalWidth, rate, count FROM sprites WHERE id='{$spriteId}'";
		$query	= mysql_query($sql) or die('Error : ' . mysql_error());
		$count	= mysql_num_rows($query);

		if ($count == 0)
			return false;

		return mysql_fetch_array($query); // TODO: Switch to mysql_fetch_assoc and use Integer based indexing? -Nikey
	}
	
	#-------------------------------------------------------------
	# Function recordEpisodeTopseries
	# Each page refresh makes a new pageview for a given topic
	# if the user's ip is not in the database already
	#-------------------------------------------------------------
	
	private function recordEpisodeTopseries($epid,$seriesId,$ip,$epNumber) {
		//Get the Date for today, all 24 hours
		$currentDay = date('d-m-Y',time());
		$midnight = strtotime($currentDay);
		$elevenfiftynine = $midnight+86399;
		//check for any rows that were done today...
		$query20  = mysql_query("SELECT * FROM episodestats WHERE ip='".$ip."' AND epSeriesId='".$seriesId."' AND epNumber='".$epNumber."' AND date>='".$midnight."'");
		$Countrows = mysql_num_rows($query20);
		if($Countrows == 0){
			$query = "INSERT INTO episodestats (`eid`, `epSeriesId`, `ip`, `date`, `epnumber`)
	VALUES ('$epid', '$seriesId', '$ip', '".time()."', '$epNumber')";
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
		}
	}
	
	private function DisplayLinks($SeriesId,$type,$alevel)
	{
		$query = "SELECT id, fullSeriesName, seoname, description, stillRelease, seriesType, seriesList, moviesOnly FROM series WHERE id='$SeriesId'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		$fullSeriesName = $row['fullSeriesName']; 
		$fullSeriesName = stripslashes($fullSeriesName);
		$seoname = $row['seoname'];
		$stillRelease = $row['stillRelease'];
		$seriesList = $row['seriesList'];
		$moviesOnly = $row['moviesOnly'];
		$description = $row['description'];
		$html5 = 1;
		$description = stripslashes($description);
		if($seriesList == 0){
			$seriesList = 'anime';
		}
		else if($seriesList == 1){
			$seriesList = 'drama';
		}
		else {
			$seriesList = 'amv';
		}
		
		if($this->UserArray[0] == 1)
		{
			//  user is logged in
			if(array_key_exists($row['id'], $this->watchListSelection))
			{
				// it exists..
				if($this->watchListSelection[$row['id']]['status'] == 1)
				{
					$status = '&nbsp;<img src="' . $this->Host . '/flag_red.png" alt="" title="In your WatchList Planning to Watch" style="height:14px;display:inline;" />';
				}
				else if($this->watchListSelection[$row['id']]['status'] == 2)
				{
					$status = '&nbsp;<img src="' . $this->Host . '/flag_yellow.png" alt="" title="In your WatchList under Watching" style="height:14px;display:inline;" />';
				}
				else if($this->watchListSelection[$row['id']]['status'] == 3)
				{
					$status = '&nbsp;<img src="' . $this->Host . '/flag_green.png" alt="" title="In your WatchList as Finished" style="height:14px;display:inline;" />';
				}
				else if($this->watchListSelection[$row['id']]['status'] == 4)
				{
					$status = '&nbsp;<img src="' . $this->Host . '/flag_orange.png" alt="" title="In your WatchList as Someday Maybe" style="height:14px;display:inline;" />';
				}
				else
				{
					$status = '';
				}
			}
			else
			{
				// nothing to see here.
				$status = '';
			}
		}
		else
		{
			$status = '';
		}
				
		if($row['seriesType'] == 0){
			$Type = '';
		}
		else if($row['seriesType'] == 1){
			$Type = '&nbsp;<img src="' . $this->Host . '/mkv-series.png" alt="MKV series" title="This series is in DivX Web 2.0 Format" style="vertical-align:middle;" border="0" />';
		}
		else {
			$Type = '';
		}
		
		if($stillRelease == 'yes'){
			$airing = '&nbsp;<img src="' . $this->Host . '/airing_icon.gif" alt="Airing" title="This Series is Airing" style="vertical-align:middle;" border="0" />';
		}
		else {
			$airing = '&nbsp;';
		}
		
		if($moviesOnly == 1){
			$Type .= '&nbsp;<img src="' . $this->Host . '/movie_blue.png" alt="Movie" title="This is a Movie"  style="vertical-align:middle;" border="0" />';
		}
		if($type == 1){
			$url = '/reviews/series-'.$row['id'];
		}
		else {
			$url = '/'.$seriesList.'/'.$seoname.'/';
		}
			$FinalLink = '<a href="'.$url.'" class="tooltip-overlay" data-node="/scripts.php?view=profiles&show=tooltips&id=' . $row['id'] . '">'.$fullSeriesName.'</a>' . $status . $airing . $Type;
			return $FinalLink;
	}
	
	private function array_buildWatchListEntries()
	{
		$query = 'SELECT `id`, `sid`, `status` FROM `watchlist` WHERE `uid` = ' . $this->UserArray[1];
		$result = mysql_query($query);
		
		$dataarray = array();
		while($row = mysql_fetch_assoc($result))
		{
			$dataarray[$row['sid']]['id'] = $row['id'];
			$dataarray[$row['sid']]['sid'] = $row['sid'];
			$dataarray[$row['sid']]['status'] = $row['status'];
		}
		
		$this->watchListSelection = $dataarray;
	}
	
	#-------------------------------------------------------------
	# Function Episode_Display
	# Builds the viewer.
	# if the user's ip is not in the database already
	#-------------------------------------------------------------
	
	private function Episode_Display($SeriesArray,$EpisodeArray,$SpriteArray,$moevar)
	{
		function getUrl()
		{
            if(isset($_SERVER['HTTP_CF_VISITOR'])){
                $decoded = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
                if($decoded['scheme'] == 'http'){
                    // http requests
                    $port = 80;
                } else {
                    $port = 443;
                }
            } else {
                $port = $_SERVER['SERVER_PORT'];
            }
			$url  = @( $port != '443' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
			$url .= ( $port !== 80 ) ? ":".$port : "";
			$url .= $_SERVER["REQUEST_URI"];
			return $url;
		}
		
		$FinalSerisName = $SeriesArray[1];

		// ADDED 08/31/14 - Robotman321
		// The following will enable us to directly allow for selective links to a video.
		// This will allow members to link directly to good parts of a video.
		$parsedURL = parse_url(getUrl());
			
		if(isset($parsedURL['query']) && stristr($parsedURL['query'], 't='))
		{
			// The Anchor tag is set
			$timed = explode('=', $parsedURL['query']);
			$vidPosition = '#t=' . gmdate("H:i:s", $timed[1]);
		}
		else
		{
			// we first check to see if the user has watched this, to be in the timer array.
			if(@array_key_exists($EpisodeArray[15],$this->RecentEps))
			{
				echo '<!-- key exists -->';
				// success. However, we need to check the timestamp against our current time, if its > 4 weeks or is more than 95% of the way through the video, let's just let it go.
				if($this->RecentEps[$EpisodeArray[15]]['updated'] < (time()-2419200) || (is_numeric($this->RecentEps[$EpisodeArray[15]]['max']) && ($this->RecentEps[$EpisodeArray[15]]['time']/$this->RecentEps[$EpisodeArray[15]]['max'])*100 > 95))
				{
					// sorry.. your time was too far off, or you already watched through this video before.. so we will just let it ride out without a stamp.
					$vidPosition = '';
				}
				else
				{
					$vidPosition = '#t=' . $this->RecentEps[$EpisodeArray[15]]['time'];
				}
			}
			else
			{
				$vidPosition = '';
			}
		}
		if($this->UserArray[2] == 3)
		{
			$randomValue = rand(0,3);
			if($randomValue == 0 || $randomValue == 1 || $randomValue == 2){
				$ad = '
						<!-- Start J-List Affiliate Code -->
						<a href="https://anime.jlist.com/click/3638/129" target="_blank" onmouseover="window.status=\'Click for Japanese study aids and more\'; return true;" onmouseout="window.status=\'\'; return true;" title="Click for Japanese study aids and more">
							<img src="https://affiliates.jlist.com/media/3638/129" width="300" height="250" alt="Click for Japanese study aids and more" border="0"><br />
							Japanese study aids and more at J-List
						</a>
						<!-- End J-List Affiliate Code -->';
			}
			else {
				$ad = '
						<!-- Start J-List Affiliate Code -->
						<a href="https://anime.jlist.com/click/3638/129" target="_blank" onmouseover="window.status=\'Click for Japanese study aids and more\'; return true;" onmouseout="window.status=\'\'; return true;" title="Click for Japanese study aids and more">
							<img src="https://affiliates.jlist.com/media/3638/129" width="300" height="250" alt="Click for Japanese study aids and more" border="0"><br />
							Japanese study aids and more at J-List
						</a>
						<!-- End J-List Affiliate Code -->';
			}
			echo '
			<div id="am-container">
				<div align="center" style="padding:5px;">Please note, AnimeFTW.tv only streams using a custom build HTML5 Video player, all other players are NOT Supported.</div>
				<div align="center"><form name="counter"><span>Your video will start in:<input type="text" name="d2" style="background:#F7F7F7;border:none;width:16px;"> seconds</span></form></div>
				<div align="center">
					<div style="text-align: center; font-size: 12px;" align="center">
						' . $ad . '
					</div>
					<br />And now a Word from one of our Partners.
					<br />
					<a href="/advanced-signup">Sick of waiting for episodes to start? Signup for advanced membership <br />and help out the site with server costs while having no ads at all!</a>
					<br /><br />
				</div>
			</div>';
			$hiddenstyle = ' style="display:none;"';
		}
		else
		{
			$hiddenstyle = '';
		}
		
		// check the image.
		if($EpisodeArray[15] == 0)
		{
			$epimage = $this->Host . '/video-images/noimage.png';
		}
		else
		{
			$epimage = "{$this->Host}/video-images/{$EpisodeArray[14]}/{$EpisodeArray[10]}_screen.jpeg";
		}
		// Autoplay functionality.
		$autoplay = "";
		if(isset($this->SettingsArray[16]) && $this->SettingsArray[16]['disabled'] != 1)
		{
			if($this->SettingsArray[16]['value'] == 33)
			{
				$autoplay = " autoplay";
			}
		}
			
		// All of the code for the HTML5 player is here.
		echo '
			<div id="aftw-video-wrapper"' . $hiddenstyle . ' align="center">
				<video id="aftw-player" class="video-js vjs-sublime-skin" controls preload="none" width="' . $EpisodeArray[3] . '" height="' . $EpisodeArray[2] . '" poster="' . $epimage . '"' . $autoplay . '>';
				
		// ADDED 08/31/14 - Robotman321
		// With native support in the HTML5 player for different resolutions, we can support higher resolutions inline.				
		if($EpisodeArray[12] == 1 && $this->UserArray[2] != 3)
		{
			// it's equal to 1, which means its just 720p
			echo '					<source src="//videos.animeftw.tv/' . $FinalSerisName . '/' . $EpisodeArray[4] . '_' . $EpisodeArray[0] . '_ns.mp4' . $vidPosition . '" type="video/mp4" data-res="480" />';
			echo '					<source src="//videos2.animeftw.tv/' . $FinalSerisName . '/' . $EpisodeArray[4] . '_720p_' . $EpisodeArray[0] . '_ns.mp4' . $vidPosition . '" type="video/mp4" data-res="720" />';
		}																						 // swordartonline_720p_10_ns.mkv
		else if($EpisodeArray[12] == 2 && $this->UserArray[2] != 3)
		{
			// its equal to 2, which means its 1080p
			echo '					<source src="//videos.animeftw.tv/' . $FinalSerisName . '/' . $EpisodeArray[4] . '_' . $EpisodeArray[0] . '_ns.mp4' . $vidPosition . '" type="video/mp4" data-res="480" />';
			echo '					<source src="//videos2.animeftw.tv/' . $FinalSerisName . '/' . $EpisodeArray[4] . '_720p_' . $EpisodeArray[0] . '_ns.mp4' . $vidPosition . '" type="video/mp4" data-res="720" />';
			echo '					<source src="//videos2.animeftw.tv/' . $FinalSerisName . '/' . $EpisodeArray[4] . '_1080p_' . $EpisodeArray[0] . '_ns.mp4' . $vidPosition . '" type="video/mp4" data-res="1080" data-default="true" />';
		}
		else
		{
			// nothing else to see here.
			echo '					<source src="//videos.animeftw.tv/' . $FinalSerisName . '/' . $EpisodeArray[4] . '_' . $EpisodeArray[0] . '_ns.mp4' . $vidPosition . '" type="video/mp4" />';
		}
		echo '
			</video>
		</div>';
		// ADDED 09/19/14 - Robotman321
		// The following will automagically add an entry to the toplist based on watching 65% of the video
		// or getting past the 65% point..
		echo '
		<script>
			var int_val = 0;
			var submit_check = "FALSE";
			var timerCheck = setInterval(function(){
				var current_time = $(\'#aftw-player\').find(\'video\').get(0).currentTime;
				var durration = $(\'#aftw-player\').find(\'video\').get(0).duration;
				
				if((Math.round(current_time) % 60 == 0 && Math.round(current_time) != 0 && int_val != Math.round(current_time)) || Math.round(current_time) == Math.round(durration-1))
				{
					$.ajax({
						url: "/scripts.php?view=check-episode&id=' . $EpisodeArray[15] . '&time=" + Math.round(current_time) + "&max=" + Math.round(durration),
						cache: false
					});
					int_val = Math.round(current_time);
				}
				
				if(((current_time/durration)*100) >= 65 && submit_check == "FALSE")
				{
					submit_check = "TRUE";
					$.ajax({
						url: "/scripts.php?view=toplist&action=record&epid=' . $EpisodeArray[15] . '",
						cache: false
					});';
		if(isset($this->SettingsArray[9]) && $this->SettingsArray[9]['disabled'] != 1)
		{
			if($this->SettingsArray[9]['value'] == 17)
			{
				echo '
					$.ajax({
						url: "/scripts.php?view=tracker&subview=add-entry&id=' . $EpisodeArray[15] . '",
						cache: false,
						success: function(response) {
							if(response.indexOf("Success") >= 0){
								$(".tracker-button").html(\'<img src="' . $this->Host . '/added_tracker.png" alt="" title="Auto Addition was Successful!" style="float:left;padding-top:1px;padding-right:3px;" />&nbsp;<span>Auto-Added!</span>\');
								$(".tracker-added-date").html(response);
							}
							else
							{
								alert("There was an error trying to process that request.");
							}
						}
					});
				';
			}
		}
		echo '
					return;
				}
				';				
		// Auto Play feature
		// Will auto move to the next page at the completion of video. We will want to
		// add some sort of pause option down the line, technically pausing a video would 
		// do what we need, but it could be useful.
		if(isset($this->SettingsArray[16]) && $this->SettingsArray[16]['disabled'] != 1)
		{
			if($this->SettingsArray[16]['value'] == 33)
			{
				$query = "SELECT `id` FROM `episode` WHERE `sid` = " . $SeriesArray[0] . " AND `epnumber` > " . $EpisodeArray[0] . " LIMIT 0, 1";
				$result = mysql_query($query);
				$count = mysql_num_rows($result);
				if($count > 0)
				{
					if($EpisodeArray[17] == 0)
					{
						// Episode
						$mov = "ep";
					}
					else
					{
						// movie
						$mov = "movie";
					}
					// we make sure that the current time equals the durration, then we redirect them.
					echo '
				if(current_time == durration)
				{
					window.location.href = "/anime/' . $SeriesArray[2] . '/' . $mov . '-' . ($EpisodeArray[0]+1) . '";
				}
					';
				}
				else
				{
				}
			}
		}
		echo '
			},1000);
		</script>
		<script>
		$(document).ready(function(){
			$(\'.vjs-loading-spinner\').html(\'<img src="' . $this->Host . '/fay-loading-image.gif" alt="Loading...">\');
		});
		</script>';
		if($this->UserArray[2] == 3)
		{
			echo '
			<script> 
			<!-- 
			//
			var milisec=0 
			var seconds=31
			document.counter.d2.value=\'31\'      
			function display(){ 
				if (milisec<=0){ 
					milisec=9 
					seconds-=1 
				} 
				if (seconds<=-1){ 
					milisec=0 
					seconds+=1 
					$("#am-container").hide();
					$("#aftw-video-wrapper").show();
				} 
				else {
					milisec-=1 
					document.counter.d2.value=seconds
					setTimeout("display()",100)
				}
			}
			display()
			--> 
			</script>';
		}
		$videoJsFunction = "";
		if (!$SpriteArray) {
			echo '
			<link href="/css/videojs.progressTips.css" rel="stylesheet" />
			<script src="/scripts/videojs.progressTips.js" type="text/javascript"></script>';
			$videoJsFunction = ", function() {\nthis.progressTips();\n}";
		}
			
		// ADDED 08/31/14 - Robotman321
		// With native support in the HTML5 player for different resolutions, we can support higher resolutions inline.
		if($EpisodeArray[12] > 0)
		{
			if(isset($this->SettingsArray[15]) && $this->SettingsArray[15]['disabled'] != 1)
			{
				if($this->SettingsArray[15]['value'] == '31' && $EpisodeArray[12] == 2)
				{
					// 1080p by default
					$defaultrez = '1080';
				}
				else if($this->SettingsArray[15]['value'] == '30' || ($this->SettingsArray[15]['value'] == '31' && $EpisodeArray[12] == 1))
				{
					// 720p by default
					$defaultrez = '720';
				}
				else
				{
					// 480p by default
					$defaultrez = '480';
				}
			}
			else
			{
				$defaultrez = '480';
			}
			echo '
			<script type="text/javascript">
				// Initialize video.js and activate the resolution selector plugin
				var video = videojs(\'#aftw-player\', {
					plugins : {
						resolutionSelector: {
							// Pass any options here
							default_res : \'' . $defaultrez . '\'
							// Define an on.ready function
						},
						hotkeys: {
							volumeStep: 0.1,
							seekStep: 5,
							enableMute: true,
							enableFullscreen: true
						}
					}
					
				}' . $videoJsFunction . ');
			</script>';
		}
		else {
			echo '
			<script type="text/javascript">
				// Initialize video.js and activate the resolution selector plugin
				var video = videojs( \'#aftw-player\', {
					plugins : {
						hotkeys: {
							volumeStep: 0.1,
							seekStep: 5,
							enableMute: true,
							enableFullscreen: true
						}
					}
				}' . $videoJsFunction . ');
			</script>';
		}

		// Open a style tag for late style modifications
		echo '
			<style>';

		// If user !AdvancedMember || episode.hd = 0...I hope
		if ($this->UserArray[3] === 3 || $EpisodeArray[12] === 0) {
			// Extend the progress bar so that the Resolution Selectors gap is filled
			echo '
				.vjs-sublime-skin .vjs-progress-control {
					right: 90px !important;
				}' . "\n";
		}

		// Fix the JavaScript demon child.
		echo '
				.vjs-volume-level {
					width: 100%;
					position: absolute;
				}
			</style>';

		// Added 9/22/15 by Nikey646, Output for sprite sheets if they exist.
		if ($SpriteArray) {
			// Sprite exists. Lets load the Sprite Data
			// This is soooo messy ;(
			// 5 Tab spaces to help w/ indenting source code in the output.
			$tab5 = "					";

			echo <<<HDOC

			<script type="text/javascript">
				video.thumbnails({\n
HDOC;

			for($i = 0; $i < $SpriteArray['count']; $i++) {
				echo $tab5 . $i * $SpriteArray['rate'] . ": {\n";
				if ($i === 0)
					echo $tab5 . "	src: \"{$this->Host}/video-images/{$EpisodeArray[14]}/{$EpisodeArray[10]}_sprite.jpeg\",\n";
					echo $tab5 . "	style: {\n";
					echo $tab5 . "		left: '-" . (($SpriteArray['width'] / 2) + ($SpriteArray['width'] * $i)) . "px',\n";
				if ($i === 0) {
					echo $tab5 . "		width: '{$SpriteArray['totalWidth']}px',\n";
					echo $tab5 . "		height: '{$SpriteArray['height']}px', \n";
				}
				echo $tab5 . "		clip: 'rect(0, " . ($SpriteArray['width'] * ($i + 1)) . "px, {$SpriteArray['height']}px, " . ($SpriteArray['width'] * $i) . "px)'\n";

				echo $tab5 . "	}\n";

				echo $tab5 . "},\n";
			}

			echo <<<HDOC
				});
			</script>
HDOC;
		}
	}

	
	#------------------------------------------------------------
	# Function DisplaySeries
	# Main function to display content on the videos page
	# @private function
	#------------------------------------------------------------
	
	public function DisplaySeries($SeoN,$seo = NULL,$eid = NULL,$oid = NULL,$mid = NULL) {
		
		if($seo == NULL){ //No seo, NO FOOD FOR YOU!
			echo 'ERROR: No series was chosen. ERROR: NL0001.';
		}
		else { //Ok, your Seo is accepted here... lets check that series..
			include("includes/rating_functions.php"); 
			$SeriesArray = $this->showSeriesInfo($SeoN); //Grab the info for the series
			if($SeriesArray[16] == 0)
			{
				//check series, if not valid, GTFO!
				echo '<br /><br /><br />ERROR: No series by that name.NL0002';
			}
			else { //Valid series, you may pass..
				if ($SeriesArray[10] != ''){
					$index_global_message = "<b>Series Note:</b> ".stripslashes($SeriesArray[10]);
				}
				else {
					$index_global_message = NULL;
				}
				if($eid != '' || $oid != '' || $mid != ''){ //check for episode details, just because..
					if(isset($eid) && is_numeric($eid)){$moe = $eid;$moevar = 'ep';$type1 = 'Episode';}
					else if(isset($oid) && is_numeric($oid)){$moe = $oid;$moevar = 'ova';$type1 = 'OVA';}
					else if(isset($mid) && is_numeric($mid)){$moe = $mid;$moevar = 'movie';$type1 = 'Movie';}
					else {$moe = NULL;$moevar = NULL;$type1 = NULL;}
					if($moe == NULL && $moe != 0){ //it's null, trip them to the black hole error.. cause we dont know how they got here..
						echo 'ERROR: No Episode was chosen. ERROR: NL0003 '.$moe;
					}
					else
					{ //This is an episode, you can move to the next stage..					
						$EpisodeArray = $this->showEpisodeInfo($SeriesArray[0],$moe,$moevar); //build the array, NAOW
						
						if($EpisodeArray[11] == 1)
						{ //Just to be SUPER sure, we need to check to make sure this episode is valid for this series.. 1 = awesomesauce

							if($this->UserArray[0] == 0 || $this->checkBanned() == FALSE || ($this->UserArray[2] == 0 && $SeriesArray[11] > 0) || ($this->UserArray[2] == 3 && $SeriesArray[11] > 1))
							{
								echo '<br /><br /><br /><br /><br /><br /><div align="center" style="font-size:20px;">Error 404: There was no video found, please go back and try again.</div>';
							}
							else
							{
								$SpriteArray = $this->showSpriteInfo($EpisodeArray[18]);

								echo "<div id='va'><br /><br /><br />";
								echo "<span class='head'><a href='/anime/".$SeriesArray[2]."/' style='color:#000;'>".$SeriesArray[3]."</a></span><span class='headend'>, ".$type1." #".$EpisodeArray[0]."</span>";
								echo "</div>";	
								include_once('includes/classes/pages.class.php');
								$p = new AFTWpage();
								echo $p->bodyTopInfo($index_global_message,NULL,$this->UserArray,$this->SettingsArray);
								
								if($this->UserArray[2] == 3)
								{
									$views = '<span title="Become an Advanced Member and see real numbers!">1 Views</span>';
								}
								else
								{
									if($EpisodeArray[16] == 1)
									{
										$views = '1 View';
									}
									else
									{
										$views = $EpisodeArray[16] . ' Views';
									}
								}
								if($EpisodeArray[6] == 0)
								{
									$date = 'Unknown.';
								}
								else
								{
									$date = date('F jS Y', $this->timeZoneChange($EpisodeArray[6],$this->UserArray[3]));
								}
								/* echo '<div id="command" align="left"><a class="lightSwitcher" href="javascript:void(0)">Turn down the lights</a></div>'; */
								echo '
								<div id="video-wrapper">
									<div id="video-left-column">
										<div class="video-player">';
										 echo $this->Episode_Display($SeriesArray,$EpisodeArray,$SpriteArray,$moevar);
										 echo '
										</div>
										<div class="video-information">
											<div class="video-information-top">
												<div class="video-information-image" align="center">
													<img src="' . $this->Host . '/seriesimages/' . $SeriesArray[0] . '.jpg" alt="series-image" style="max-height:120px;max-width:93px;" />
												</div>
												<div class="video-information-details">
													<div class="video-information-title"><span title="This episode is titled..">' . stripslashes($EpisodeArray[1]) . '</span></div>
													<div class="video-information-series"><a href="/anime/' . $SeriesArray[2] . '/" target="_blank" style="color:black;text-decoration:none;">' . stripslashes($SeriesArray[3]) . '</a></div>
													<div class="video-information-views">Added ' . $date . ' </div>
													<div class="video-information-views">' . $views . '</div>
												</div>
												<div class="video-information-ratings">
													<div class="video-information-subrate">
														<div style="margin:0 0 3px -20px;font-size:12px;color:#a8a8a8;">Rate this Video:</div>';
														echo pullRating('v'.$EpisodeArray[10],$this->UserArray[1],true,false,true);
														echo '
														</div>
														<div class="video-information-tracker">
															<div style="margin:0 0 3px -5px;font-size:12px;color:#a8a8a8;">Episode Tracking:</div>';
														include_once("tracker.class.php");
														$Tracker = new AFTWTracker();
														$Tracker->connectProfile($this->UserArray);
														$Tracker->currentEpisodeAvailability($EpisodeArray[10]);
														echo '
													</div>
												</div>
											</div>
											<div class="video-information-bottom">
												<div style="margin-top:3px;">
												Share Via:<br />
												<a title="Share this Episode on Facebook" href="https://www.facebook.com/share.php?u=https%3A%2F%2Fwww.animeftw.tv%2Fanime%2F' . $SeriesArray[2] . '%2Fep-' . $EpisodeArray[0] . '" target="_blank"><img src="' . $this->Host . '/social-networking/icon-facebookv2.png" alt="facebook" /></a>&nbsp;
												<a title="Share this Episode on Twitter" href="http://www.twitter.com/home?status=Watching+'.urlencode($SeriesArray[3]).'+episode+' . $EpisodeArray[0] . '+at+AnimeFTW.tv+'.$_SERVER['REQUEST_URI'].'" target="_blank"><img src="' . $this->Host . '/social-networking/icon-twitterv2.png" alt="twitter" /></a>&nbsp;
												<a title="Share this Episode on Digg" href="http://digg.com/submit?phase=2&amp;url=https://www.animeftw.tv'.$_SERVER['REQUEST_URI'].'" target="_blank"><img src="' . $this->Host . '/social-networking/icon-diggv2.png" alt="Digg" /></a>&nbsp;
												<a title="Share this Episode on StumbleUpon" href="http://www.stumbleupon.com/submit?url=https://www.animeftw.tv'.$_SERVER['REQUEST_URI'].'" target="_blank"><img src="' . $this->Host . '/social-networking/icon-stumbleuponv2.png" alt="StumbleUpon" /></a>&nbsp;
												<a title="Share this Episode on Google+" href="https://plus.google.com/u/0/share?url=https://www.animeftw.tv'.$_SERVER['REQUEST_URI'].'" target="_blank"><img src="' . $this->Host . '/social-networking/icon-google-plusv2.png" alt="Google Plus" /></a>
												</div>
											</div>
										</div>
										<div class="video-comments">
											<div class="video-comments-header" style="font-size:16px;margin:5px 0 0 20px;">Video Comments:</div>
											<div class="comments" id="comments1"><div align="center" style="padding:10px;text-align:center;font-size:16px;"><img src="/images/loading-mini.gif" alt="" />&nbsp;Loading Comments. Please Wait...</div></div>
											';
										if($EpisodeArray[11] == 1)
										{
											echo '
											<script type="text/javascript">
												$(document).ready(function(){
													$("#comments1").load("/scripts.php?view=commentsv2&epid='.$EpisodeArray[10].'");
												});
											</script>';
										}
										echo '
										</div>
									</div>
									<div id="video-right-column">
										<div style="border-bottom:1px solid #D1D1D1;">
										'; 
										
										$autoplaytext = '
											<div class="video-episodes">AutoPlay is <span style="color:red;cursor:pointer;" title="Enable this Advanced Member feature in your Profile Settings!">Disabled</span></div>';
										if(isset($this->SettingsArray[16]) && $this->SettingsArray[16]['disabled'] != 1)
										{
											if($this->SettingsArray[16]['value'] == 33)
											{
										$autoplaytext = '
											<div class="video-episodes">AutoPlay is <span style="color:green;cursor:pointer;" title="The Videos will automatically start and move to the next episode once completed.">Enabled</span></div>';
											}
										}
										echo $autoplaytext;
										echo '
										</div>
										<div>';
											echo $this->NextEpisodesV2($SeriesArray,$EpisodeArray);
											echo '
										</div>
										<div>
											<div class="video-suggested-anime">Suggested Anime:</div>';
											echo $this->suggestedAnime($SeriesArray);
											echo '
										</div>
									</div>
								</div>';
						
							}
						}
						else {
							echo 'ERROR: There is no episode by that number. ERROR: NL0005';
						}
					}						
				}
				else 
				{ //none of the episode stuff was presented, we need to make them see the series page..
					if($SeriesArray[11] == 1 && $this->UserArray[2] == 0)
					{
						echo "<div id='va'><br /><br /><br />";
						echo "<span class='head'>ERROR: No series by that name. NL0004</span>";
						echo "</div>";
					}
					else if($SeriesArray[11] == 2 && ($this->UserArray[2] == 0 || $this->UserArray[2] == 3)){
						echo "<div id='va'><br /><br /><br />";
						echo "<span class='head'>ERROR: No series by that name. NL0005</span>";
						echo "</div>";
					}
					else
					{
						$html5tag = '<a href="/what-is-the-aftw-html5-player"><img src="' . $this->Host . '/html5.png" alt="HTML5 Series" style="vertical-align:middle;height:25px;" border="0" title="This is an AnimeFTW.tv v2.0 HTML5 Player Series!" /></a>';
						if($SeriesArray[19] == 1)
						{
							// 720p only series
							$quality = '&nbsp;<img src="' . $this->Host . '/series-pages/hd-720p-icon.png" alt="720p Videos available" style="vertical-align:middle;" border="0" />&nbsp;';
						}
						else if($SeriesArray[19] == 2)
						{
							// 1080p and 720p series
							$quality = '&nbsp;<img src="' . $this->Host . '/series-pages/hd-720p-icon.png" alt="720p Videos available" style="vertical-align:middle;" border="0" title="Watch ' . stripslashes($SeriesArray[3]) . ' in 720p!" />&nbsp;<img src="' . $this->Host . '/series-pages/hd-1080p-icon.png" alt="1080p Videos available" style="vertical-align:middle;" border="0" title="Watch ' . stripslashes($SeriesArray[3]) . ' in 1080p!" />';
						}
						else
						{
							// SD only series..
							$quality = '';
						}
						echo "<br /><br /><br />";
						//echo $SeriesArray[3];
						include_once('includes/classes/pages.class.php');
						$p = new AFTWpage();
						echo $p->bodyTopInfo($index_global_message,NULL,$this->UserArray,$this->SettingsArray);
						echo '<div style="width:100%;">';
												
						echo '
						<div class="series-top-wrapper" style="border-bottom:1px solid #d8d8d8;padding-bottom:5px;">
							<div class="series-image-column" style="display:inline-block;padding:0 5px 5px 5px;vertical-align:top;">
								<img src="' . $this->Host . '/seriesimages/' . $SeriesArray[0] . '.jpg" alt="Series Image" style="max-width:225px;" />
							</div>
							<div class="series-details-column" style="display:inline-block;vertical-align:top;padding:0 5px 5px 5px;width:70%;border-left:1px solid #d8d8d8;">
								<div style="width:100%;">
									<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Titled:</div>
									<div style="font-size:20px;color:#242424;">' . stripslashes($SeriesArray[3]) . '</div>
								</div>
								<div>
									<div style="display:inline-block;width:48%;">
										<div>
									';
										
									if($html5tag == '' && $quality == '')
									{
										// Don't do anything, nothing to see here.
									}
									else
									{
										echo '
											<div style="width:49%;display:inline-block;vertical-align:top;">
												<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Available in:</div>
												<div style="font-size:16px;">' . $html5tag . $quality . '</div>
											</div>';
									}
									echo '
											<div style="width:49%;display:inline-block;vertical-align:top;">
												<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Rated:</div>
												<a href="https://ftwentertainment.com/ratings" target="_blank"><img src="' . $this->Host . '/ratings/'.$SeriesArray[7].'" border="0" border="0" style="height:25px;" /></a>
											</div>
										</div>';
									if ($SeriesArray[12] != 0 || $SeriesArray[13] != 0)
									{
										if($SeriesArray[12] != 0)
										{
											echo '
											<div style="padding-top:10px;">
												<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Prequel Series:</div>
												<div style="font-size:12px;color:#242424;">' . $this->checkSeriesSid($SeriesArray[12]) . '</div>
											</div>';
										}
										if($SeriesArray[13] != 0)
										{
											echo '
											<div style="padding-top:10px;">
												<div style="font-size:10px;color:#c0c0c0;vertical-align:top;">Sequel Series:</div>
												<div style="font-size:12px;color:#242424;">' . $this->checkSeriesSid($SeriesArray[13]) . '</div>
											</div>';
										}
									}
									echo '
										<div style="padding-top:10px;">
											<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Kanji:</div>
											<div style="font-size:12px;color:#242424;">' . $SeriesArray[20] . '</div>
										</div>
										<div style="padding-top:10px;">
											<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Romaji:</div>
											<div style="font-size:12px;color:#242424;">' . $SeriesArray[21] . '</div>
										</div>
										<div style="padding-top:10px;">
											<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Genres:</div>
											<div style="font-size:12px;color:#242424;">';
											$exploded = explode(" , ",$SeriesArray[14]);
											$category = '';
											$i = 0;
											$count = count($exploded);
											foreach($exploded as $value)
											{
												if($i > 0 && $i < $count)
												{
													$category .= '<a href="/anime/sort/' . $this->Categories[$value]['name'] . '" target="_blank">' . $this->Categories[$value]['name'] . '</a>';
													$i++;
													if($i < ($count-1))
													{
														$category .= ', ';
													}
												}
												if($i == 0)
												{
													$i++;
												}
											}
											echo $category;
											echo '
											</div>
										</div>
										<div style="padding-top:10px;">
											<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Site Rank:</div>
											<div style="font-size:12px;color:#242424;">' . $this->seriesTopSeriesRank($SeriesArray) . '</div>
										</div>
									</div>
									<div style="display:inline-block;width:48%;vertical-align:top;">';
									if($this->UserArray[0] == 1)
									{
										echo '									
										<div>
											<div style="font-size:10px;color:#c0c0c0;vertical-align:top;">My WatchList:</div>
											<div style="font-size:12px;color:#242424;" id="my-watchlist-container">';
												include_once("watchlist.class.php");
												$W = new AFTWWatchlist($this->UserArray);
												$W->checkSeriesEntry($SeriesArray[0]); // Checks the watchlist for this series.
											echo '	
											</div>
										</div>';
									}
									echo '
									</div>
								</div>
								<div style="width:100%;padding-top:10px;">
									<div style="font-size:8px;color:#c0c0c0;vertical-align:top;">Series Synopsis:</div>
									<div style="font-size:12px;color:#242424;">' . stripslashes(nl2br($SeriesArray[6])) . '</div>
								</div>
							</div>
						</div>
						<div class="series-middle-wrapper" style="margin-top:10px;width:100%;">
							<div class="series-episode-wrapper" style="margin-bottom:5px;">';
							if((isset($this->SettingsArray[1]) && $this->SettingsArray[1]['value'] == 2) || $this->UserArray[0] == 0)
							{
								$LayoutType = 2;
							}
							else
							{
								$LayoutType = 1;
							}
							echo $this->showAvailableVideos(0,$SeriesArray[0],$LayoutType);
							echo '
							</div>
							<div class="series-ova-wrapper" style="margin-bottom:5px;">';
							echo $this->showAvailableVideos(2,$SeriesArray[0],$LayoutType);
							echo '
							</div>
							<div class="series-movie-wrapper">';
							echo $this->showAvailableVideos(1,$SeriesArray[0],$LayoutType);
							echo '
							</div>
						</div>
						<div class="series-bottom-wrapper" style="margin-top:10px;width:100%;">
						';
						include_once("reviews.class.php");
						$Review = new Review();
						$Review->connectProfile($this->UserArray);
						$Review->showSeriesReviews($SeriesArray[0]);
						echo '
						</div>';
					}
				}
			}
			
		}
	}
	
	public function NextEpisodesV2($SeriesArray = NULL,$EpisodeArray = NULL)
	{
		$ajax = 0;
		if($SeriesArray == NULL && $EpisodeArray == NULL)
		{
			$AvailableRows = 10;
			// If both of these are null then its going to be an external request for more data
			//if(!isset($_GET['page']) || !is_numeric($_GET['page']) || !isset($_GET['sid']) || !isset($_GET['epnumber']))
			$page = ($_GET['page']*$AvailableRows)+10;
			$ajax = 1;
			$Movie = 0;
			$sid = $_GET['sid'];
			$EpisodesTitle = '
											<div class="video-episodes">Episodes:</div>';
			$query = "SELECT `episode`.`id`, `episode`.`epnumber`, `episode`.`epname`, `episode`.`epprefix`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `episode`.`Movie`, `series`.`seoname` FROM `episode`, `series` WHERE `episode`.`sid` = " . mysql_real_escape_string($_GET['sid']) . " AND `series`.`id` = " . mysql_real_escape_string($_GET['sid']) . " AND `epnumber` >= " . mysql_real_escape_string($_GET['epnumber']) . " AND `Movie` = 0 ORDER BY `epnumber` ASC LIMIT $page, $AvailableRows";
			
		}
		else
		{
			$AvailableRows = 20;
			// properly formatted.. yay..
			$Movie = $EpisodeArray[17];
			if($Movie == 1)
			{
				$EpisodesTitle = '
											<div class="video-episodes">Movies:</div>';
			}
			else
			{
				$EpisodesTitle = '
											<div class="video-episodes">Episodes:</div>';
			}
			$sid = $SeriesArray[0];
			$query = "SELECT `id`, `epnumber`, `epname`, `epprefix`, `image`, `hd`, `views`, `Movie` FROM `episode` WHERE `sid` = " . $SeriesArray[0] . " AND `epnumber` >= " . $EpisodeArray[0] . " AND `Movie` = $Movie ORDER BY `epnumber` ASC LIMIT 0, $AvailableRows";

		}
		
		// 18
		
		$result = mysql_query($query);
		$count = mysql_num_rows($result);
		if($ajax == 0)
		{
			echo $EpisodesTitle;
			if($count > 10 || (isset($_GET['epnumber']) && $_GET['epnumber'] > 20))
			{
				echo '<div style="overflow-y:scroll;height:440px;margin-bottom:10px;" id="available-episodes">';
			}
			else
			{
				echo '<div style="margin-bottom:10px;" id="available-episodes">';
			}
		}
		else
		{
			// it's an ajax function, we need to check to see if there are any sequels to this series.
		}
		
		// ADDED 10/14/2014
		// To ensure that we can see the previous information.. We need to be able to stop it..
		if(isset($_GET['epnumber']) && $_GET['epnumber'] > 20)
		{
			// This is greater than 20 rows, we need to be able to allow them to scroll back up
		}
		else
		{
			// it's not greater than 20 so there shouldn't be a need for previous episodes.
			echo '<div class="end-beginning-dynamic-data"></div>';
		}
		while($row = mysql_fetch_assoc($result))
		{
			if($row['image'] == 0)
			{
				$epimage = $this->Host . '/video-images/noimage.png';
			}
			else
			{
				$epimage = "{$this->Host}/video-images/{$sid}/{$row['id']}_screen.jpeg";
			}
			if($row['epnumber'] == $EpisodeArray[0])
			{
				$currentepstyle = ' next-episode-entry';
				$nextepverage = 'Now Playing:';
			}
			else if($row['epnumber'] == ($EpisodeArray[0]+1))
			{
				$currentepstyle = '';
				$nextepverage = 'Next Video:';
			}
			else
			{
				$currentepstyle = '';
				$nextepverage = '';
			}
			if($this->UserArray[2] == 3)
			{
				$views = 1;
			}
			else
			{
				$views = $row['views'];
			}
			if($ajax == 1)
			{
				$seoname = $row['seoname'];
			}
			else
			{
				$seoname = $SeriesArray[2];
			}
			if($row['Movie'] == 1)
			{
				$Movie = 'movie';
			}
			else
			{
				$Movie = 'ep';
			}
			echo '
			<div class="episode-list-wrapper">
			';
			if($this->UserArray[0] == 1)
			{
				echo '
				<a href="/anime/' . $seoname . '/' . $Movie . '-' . $row['epnumber'] . '" style="text-decoration:none;">';
				$endlink = '</a>';
			}
			else
			{
				$endlink = '';
			}
			echo '
			<div style="color:black;text-decoration:none;margin-bottom:-2px;">' . $nextepverage . '</div>
			<div class="episode-list-entry' . $currentepstyle . '">
				<div class="episode-list-entry-image">
					<img src="' . $epimage . '" alt="ep image" style="height:40px;" />
				</div>
				<div class="episode-list-entry-details">
					<div class="episode-list-entry-epname"><span style="font-size:14px;color:black;">' . stripslashes($row['epname']) . '</span></div>
					<div class="episode-list-entry-subtext">';
					if($row['Movie'] == 1)
					{
						echo 'Movie';
					}
					else
					{
						echo 'Episode';
					}
					echo ' #' . $row['epnumber'] . '</div>
					<div class="episode-list-entry-subtext">' . $views . ' view'; if($views == 1){echo '';}else{echo 's';} echo '</div>
				</div>
			</div>
			' . $endlink . '
			</div>';
		}
		if($count < $AvailableRows)
		{
			// we have to check to see if the available rows from the last action cause us to stop loading items (to prevent derp code from coming up).
			// If the count is less than what we want available, eg, 6 rows return of 10 we could have, then we need to tell the JS to stop processing data.
			echo '<div class="end-dynamic-data"></div>';
		}
		if($ajax == 0 && $count == 20)
		{
			echo '</div>';
			echo '
			<script>
				var nextpage = 1;			
				jQuery(
				  function($)
				  {
					$("#available-episodes").bind("scroll", function()
					{
						if($(".end-dynamic-data").length)
						{
						}
						else
						{
							if($(this).scrollTop() + $(this).innerHeight()>=$(this)[0].scrollHeight)
							{
								var url = \'/scripts.php?view=episodes&page=\' + nextpage + \'&sid=' . $SeriesArray[0] . '&epnumber=' . $EpisodeArray[0] . '\';
								$.post(url, function(data) {
									$(\'#available-episodes\').children().last().after(data);
									nextpage++;
								});
							}
						}
					})
				  }
				);
			</script>';
		}
	}
	
	private function suggestedAnime($SeriesArray)
	{
		$Categories = explode(" , ",$SeriesArray[14]);
		$SQLAddon = '';
		$count = count($Categories);
		$i = 0;
		foreach($Categories as $Category)
		{
			if($i < 4)
			{
				$SQLAddon .= ' `series`.`category` LIKE \'% ' . $Category . ' %\'';
				$i++;
				if($i < 4 && $i < $count)
				{
					$SQLAddon .= ' OR';
				}
			}
		}
		$query = "SELECT `series`.`id`, `series`.`seoname`, `series`.`fullSeriesName`, `series`.`category`, (SELECT COUNT(id) FROM `episode` WHERE `sid`=`series`.`id`) as NumEps, `site_topseries`.`currentPosition` FROM `series`, `site_topseries` WHERE `site_topseries`.`seriesID`=`series`.`id` AND `series`.`active` = 'yes' AND `series`.`id` != " . $SeriesArray[0] . " AND (" . $SQLAddon . ")  ORDER BY `site_topseries`.`currentPosition` ASC LIMIT 10";
		$result = mysql_query($query);
		while($row = mysql_fetch_assoc($result))
		{
			$exploded = explode(" , ",$row['category']);
			$category = '';
			$i = 0;
			$count = count($exploded);
			foreach($exploded as $value)
			{
				if($i > 0 && $i < $count)
				{
					$category .= '<a href="/anime/sort/' . $this->Categories[$value]['name'] . '" target="_blank">' . $this->Categories[$value]['name'] . '</a>';
					$i++;
					if($i < ($count-1))
					{
						$category .= ', ';
					}
				}
				if($i == 0)
				{
					$i++;
				}
			}
			echo '
			<div class="episode-list-wrapper">
			<a href="/anime/' . $row['seoname'] . '/" style="text-decoration:none;">
			<div class="episode-list-entry">
				<div class="episode-list-entry-image">
					<img src="' . $this->Host . '/seriesimages/' . $row['id'] . '.jpg" alt="series image" style="height:80px;" />
				</div>
				<div class="episode-list-entry-details">
					<div class="episode-list-entry-epname"><span style="font-size:14px;color:black;">' . stripslashes($row['fullSeriesName']) . '</span></div>
					<div class="episode-list-entry-subtext">' . $category . '</div>
					<div class="episode-list-entry-subtext">' . $row['NumEps'] . ' video'; if($row['NumEps'] == 1){echo '';}else{echo 's';} echo '</div>
				</div>
			</div>
			</a>
			</div>';
			unset($i);
			unset($count);
			unset($category);
			unset($exploded);
		}
	}
	
	private function checkBanned()
	{
		$query = "SELECT id FROM `banned` WHERE `ip` = '" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "'";
		$result = mysql_query($query);
		$count = mysql_num_rows($result);
		
		if($count > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function showAvailableVideos($listingtype = 0,$sid,$type,$ajaxed = FALSE)
	{
		// variables
		$ajax = 0;
		$AvailableRows = 13;
	
		// listingtype of 0 is for episodes, 1 is for movies, 2 is for ovas
		if($listingtype == 0)
		{
			$VideoTitle = 'Episode';
			$MovieAllowed = 0;
			$OvaAllowed = 0;
		}
		else if($listingtype == 1)
		{
			$VideoTitle = 'Movie';
			$MovieAllowed = 1;
			$OvaAllowed = 0;
		}
		else if($listingtype == 2)
		{
			$VideoTitle = 'OVA';
			$MovieAllowed = 0;
			$OvaAllowed = 1;
		}
		else
		{
			$VideoTitle = '';
			$MovieAllowed = 0;
			$OvaAllowed = 0;
		}		
		if($ajaxed == TRUE)
		{
			$page = ($_GET['page']*$AvailableRows);
			$LimitBy = ' LIMIT ' . $page . ', ' . $AvailableRows;
			$ajax = 1;
		}
		else
		{
			if($type == 2)
			{
				$LimitBy = '';
			}
			else
			{
				$LimitBy = ' LIMIT 0, ' . $AvailableRows;
			}
		}
	
		$query  = "SELECT `episode`.`id`, `episode`.`epnumber`, `episode`.`epname`, `episode`.`epprefix`, `episode`.`videotype`, `episode`.`image`, `episode`.`Movie`, `series`.`seriesname`, `series`.`seoname` FROM `episode`, `series` WHERE  `episode`.`sid`='$sid' AND `episode`.`Movie`='$MovieAllowed' AND `episode`.`ova`='$OvaAllowed' AND `series`.`id`=`episode`.`sid` ORDER BY `episode`.`epnumber` ASC" . $LimitBy;
		
		$result  = mysql_query($query) or die('Error : ' . mysql_error());
		
		$count = mysql_num_rows($result);
		
		if($count > 0)
		{
			if($ajax == 0)
			{
				echo '<div style="font-size:10px;color:#c0c0c0;vertical-align:top;">' . $VideoTitle . 's:</div>';
				if($count == $AvailableRows && $type != 2)
				{
					echo '<div class="series-episode-listing" style="overflow-y:scroll;height:390px;" align="left" id="' . strtolower($VideoTitle) . '-listing">';
				}
				else
				{
					echo '<div class="series-episode-listing" align="left">';
				}
			}
			else
			{
			}
			while(list($id,$epnumber,$epname,$epPrefix,$videotype,$image,$Movie,$seriesname,$seoname) = mysql_fetch_array($result))
			{
				if($image == 0)
				{
						$episodepreview = $this->Host . '/video-images/noimage.png';
				}
				else 
				{
					$episodepreview = "{$this->Host}/video-images/{$sid}/{$id}_screen.jpeg";
				}
				$epname    = stripslashes($epname);
				
				if($this->UserArray[0] == 0 || $this->checkBanned() == FALSE)
				{
					$LoggedInData = '';
				}
				else
				{
					if($type == 2)
					{
						$LoggedInData = ' class="tooltip-overlay" data-node="/scripts.php?view=tooltip&show=episode&image=true&id=' . $id . '"';
					}
					else
					{
						$LoggedInData = ' class="tooltip-overlay" data-node="/scripts.php?view=tooltip&show=episode&id=' . $id . '"';
					}
				}
				if($Movie == 0)
				{
					$videostyle = 'ep';
					$videotype = 'Episode';
				}
				else
				{
					$videostyle = 'movie';
					$videotype = 'Movie';
				}
				
				// There are currently two types, one is the type available to the mobile site, and one available to the main site.
				if($type == 0)
				{
					echo "<a class=\"feature01\" href=\"/anime/".$seoname."/ep-".$epnumber."\" title=\"Titled: ".$epname."\">";
					echo "	<span class=\"overlay01\">";
					echo "		<span class=\"caption01\">' . $VideoTitle . ': ".$epnumber."<br />Titled: ".$epname."</span>";
					echo "	</span>";
					echo "	<img src=\"$episodepreview\" alt=\"' . $VideoTitle . ': ".$epnumber."\" height=\"90\" />";
					echo "</a>";
				}
				else if($type == 2)
				{
					if($this->UserArray[2] == 7 || $this->UserArray[6] == 1)
					{
						$CanDownload = TRUE;
					}
					else
					{
						$CanDownload = FALSE;
					}
		
					if($this->UserArray[0] == 1)
					{
						$beginlink = '<a href="/anime/' . $seoname . '/' . $videostyle . '-' . $epnumber . '"' . $LoggedInData . '>';
						$endlink = '</a>';
					}
					else
					{
						$beginlink = '';
						$endlink = '';
					}
					// This is for Users that want to see the text based listing only..
					echo '
					<div style="height:20px;text-align:middle;">
						<div style="display:inline-block;">
							' . $this->showDownloadOption($id,$CanDownload,TRUE) . '
						</div>
						<div style="display:inline-block;vertical-align:top;">
							' . $videotype . ' #' . $epnumber . ' - ' . $beginlink . stripslashes($epname) . $endlink . '
						</div>
					</div>';
				}
				else
				{					
					if($this->UserArray[0] == 1)
					{
						$beginlink = '<a style="text-decoration:none;color:white;" href="/anime/' . $seoname . '/' . $videostyle . '-' . $epnumber . '" class="image-overlay">';
						$endlink = '</a>';
					}
					else
					{
						$beginlink = '';
						$endlink = '';
					}
					echo '
					<div style="display:inline-block;position:relative;text-decoration:none;color:white;"' . $LoggedInData . '>
						' . $beginlink . '
							<div class="details" style="float: left;width:100%;position:absolute;left:0;bottom:0;height:60px;background:url(\'' . $this->Host .'/series-pages/gradiant-background2.png\') repeat-x;">
								<div class="text-align:right;color: #666666;vertical-align:bottom;text-align:bottom;" align="left">
									<div style="height:100%;margin-top:10px;padding:4px;">' . $VideoTitle . ': ' . $epnumber . '<br />Titled: ' . $epname . '</div>
								</div>
							</div>
							<div class="image-backdrop" style="float: left;">
								<img src="' . $episodepreview . '" alt="' . $VideoTitle . ': ' . $epnumber . '" style="width:225px;" />
							</div>							
						' . $endlink . '
					</div>';
				}
			}
			if($count < $AvailableRows)
			{
				// we have to check to see if the available rows from the last action cause us to stop loading items (to prevent derp code from coming up).
				// If the count is less than what we want available, eg, 6 rows return of 10 we could have, then we need to tell the JS to stop processing data.
				echo '<div class="end-dynamic-data-' . strtolower($VideoTitle) . 's"></div>';
			}
			if($ajax == 0 && $count == $AvailableRows)
			{
				echo '</div>';
				echo '
				<script>
					var nextpage = 1;
					var fail_safe = 0;
					jQuery(
					  function($)
					  {
						$("#episode-listing").bind("scroll", function()
						{
							if($(".end-dynamic-data-' . strtolower($VideoTitle) . 's").length)
							{
							}
							else
							{
								if(fail_safe == 0)
								{
									if($(this).scrollTop() + $(this).innerHeight()>=$(this)[0].scrollHeight)
									{
										fail_safe = 1;
										var url = \'/scripts.php?view=dynamic-load&show=' . strtolower($VideoTitle) . 's&page=\' + nextpage + \'&id=' . $sid . '\';
										$.post(url, function(data) {
											//$(\'#episode-listing\').children().last().after(data);
											$(\'#episode-listing\').append(data);
											nextpage++;
										})
										.always(function() {
											constructAvailableTooltips();
											fail_safe = 0;
										});;
									}
								}
							}
						})
					  }
					);
				</script>';
			}
			echo '
				<script>
					(function($){
						$(document).ready(function() {
							$(\'.download-menu\').dropit();
						});
					})(window.jQuery);
				</script>';
		}
		else 
		{
		}
	}
	#------------------------------------------------------------
	# Function searchSeries2
	# Searches through Series and gives back information.
	#------------------------------------------------------------
	
	public function searchSeries2($input)
	{		
		$input = mysql_real_escape_string($input);
		$dualarray = array('2','5','8','11','14','17','20','23','26','29','32','35','38','41','44','47','50','53','56','59','62','65','68','71','74','77','80','83','86','89','92','95','98','101');
		if($this->UserArray[1] == 0){$aonly = " AND aonly='0'";}
		else if ($this->UserArray[1] == 3){$aonly = " AND aonly<='1'";}
		else{$aonly = '';}
		
		$ExplodedInput = explode(',',str_replace(' ', '', $input));
		
		if(count($ExplodedInput) > 1)
		{
			$subsearch = "";
			$i = 1;
			foreach($ExplodedInput as $value)
			{
				$key = $this->parseNestedArray($this->Categories, 'name', ucfirst($value));
				$subsearch .= "category LIKE '%" . $key . "%'";
				if($i < count($ExplodedInput))
				{
					$subsearch .= " AND ";
				}
				$i++;
			}
		}
		else
		{
			$key = $this->parseNestedArray($this->Categories, 'name', ucfirst($input));
			if($key == '')
			{
				$cat = "";
			}
			else
			{
				$cat = " OR category LIKE '%" . $key . "%'";
			}
			$subsearch = "fullSeriesName LIKE '%".$input."%' OR romaji LIKE '%".$input."%' OR kanji LIKE '%".$input."%'" . $cat;
		}
		
		$query   = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews`, `romaji`, `kanji` FROM series WHERE active='yes'".$aonly." AND ( " . $subsearch . " ) ORDER BY seriesName ASC LIMIT 100";
		
		mysql_query("SET NAMES 'utf8'"); 
		$result  = mysql_query($query) or die('Error : ' . mysql_error());
		$ts = mysql_num_rows($result);
		if($ts > 0)
		{
			$i=0;
			while(list($id,$seriesName,$fullSeriesName,$seoname,$ratingLink,$category,$total_reviews,$romaji,$kanji) = mysql_fetch_array($result))
			{
				$fullSeriesName = stripslashes($fullSeriesName);
				echo '<div class="item">'."\n";
				echo '	<div class="searchdiv">'."\n";
				echo '		<div style="float:left;width:100px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/"><img src="http://'.$_SERVER['HTTP_HOST'].'/images/resize/anime/medium/'.$id.'.jpg" alt="'.$fullSeriesName.'" border="0" /></a></div>'."\n";
				echo '		<div class="searchinfo"><span style="font-size:16px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/anime/'.$seoname.'/">'.$fullSeriesName.'</a></span><br />Romaji: '.$romaji.'<br />Kanji: '.$kanji.'<br />Categories: '."\n";
				//$episodes = split(" , ",$category);
				//foreach ($episodes as $value) {echo "<a href=\"http://".$_SERVER['HTTP_HOST']."/anime/sort/" . $this->Categories[$value]['name'] . "\">" . $this->Categories[$value]['name'] . "</a>, ";}
				
				$exploded = explode(" , ",$category);
				$category = '';
				$i = 0;
				$count = count($exploded);
				foreach($exploded as $value)
				{
					if($i > 0 && $i < $count)
					{
						$category .= '<a href="/anime/sort/' . $this->Categories[$value]['name'] . '" target="_blank">' . $this->Categories[$value]['name'] . '</a>';
						$i++;
						if($i < ($count-1))
						{
							$category .= ', ';
						}
					}
					if($i == 0)
					{
						$i++;
					}
				}
				echo $category;				
				echo '<br />Reviews: <a href="/anime/'.$seoname.'/#series-reviews">'.$total_reviews.'</a> | <a href="/anime/'.$seoname.'/#series-reviews">Write a review!</a></div>'."\n";
				echo '		</div>
						</div><hr color="#E4E4E4" />'."\n";
				echo '</div>'."\n";
				if(in_array($i, $dualarray)){echo '</div><div>'."\n";}
				$i++;
				}
		}
	}
	
	private function parseNestedArray($products, $field, $value)
	{
	   foreach($products as $key => $product)
	   {
			if($product[$field] === $value)
			{
				return $key;
			}
		}
		return false;
	}
	
	private function seriesTopSeriesRank($SeriesArray)
	{
		$query = "SELECT lastPosition, currentPosition FROM site_topseries WHERE seriesId='".$SeriesArray[0]."' ORDER BY currentPosition ASC ";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		$count = mysql_num_rows($result);
		$lastPosition = $row['lastPosition'];
		$currentPosition = $row['currentPosition'];
		$singleRank = '';
		
		if($currentPosition < $lastPosition)
		{
			$Rank = $currentPosition.'&nbsp;<img src="' . $this->Host . '/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: '.$lastPosition.'" />';
		}
		else if ($currentPosition == $lastPosition)
		{
			$Rank = $currentPosition.'&nbsp;<img src="' . $this->Host . '/arrow_none.gif" title="Rank Unchanged, Previous Rank: '.$lastPosition.'" alt="" />';
		}
		else
		{
			$Rank = $currentPosition.'&nbsp;<img src="' . $this->Host . '/arrow_down.gif" alt="" title="Rank Went Down, Previous Rank: '.$lastPosition.'" />';
		}

		if($count < 1)
		{
			$singleRank .= 'This Series is not Ranked on the Top list.';
		}
		else 
		{
			if($currentPosition == '')
			{
				$singleRank .= 'This Series is not Ranked on the Top list';
			}
			else
			{
				$singleRank .= '<a href="/anime/' . $SeriesArray[2] . '/">' . stripslashes($SeriesArray[3]) . '</a> is ranked #<b>'.$Rank."</b> on AnimeFTW.tv\n";
			}
		}		
		return $singleRank;
	}
	
	private function checkSeriesSid($sid)
	{
		$query = "SELECT `seoname`, `fullSeriesName`, `active`, `aonly` FROM `series` WHERE `id` = '$sid'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		$seoname = $row['seoname'];
		$fullSeriesName = $row['fullSeriesName']; 
		$fullSeriesName = stripslashes($fullSeriesName);
		
		// First we check to make sure the series is active and if its not we allow staff to see the series.
		if($row['active'] == 'yes' || ($this->UserArray[2] != 0 && $this->UserArray[2] != 3 && $this->UserArray[2] != 7)){
			if($row['aonly'] == 0) {
				// everyone can see it.
				$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'</a>';
			}
			else if($row['aonly'] == 1 && $this->UserArray[2] != 0) {
				// basic members can see it.
				$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'</a>';
			}
			else if($row['aonly'] == 2 && ($this->UserArray[2] != 0 && $this->UserArray[2] != 3)) {
				// advanced + can see it.
				$FinalLink = '<a href="/anime/'.$seoname.'/">'.$fullSeriesName.'</a>';
			}
			else {
				// otherwise they see nothing.
				$FinalLink = $fullSeriesName;
			}
		}
		else {
			$FinalLink = $fullSeriesName;
		}		
		return $FinalLink;
	}
	
	public function showEpisodeTooltip($id,$type = 0)
	{
		$id = mysql_real_escape_string($id);
		$query = "SELECT `id`, `sid`, `epnumber`, `epprefix`, `epname`, `subGroup`, `hd`, `views`, `Movie`, `image`, (SELECT COUNT(id) FROM `episode_tracker` WHERE `eid` = '$id' AND `uid` = " . $this->UserArray[1] . ") AS `tracker_entry` FROM `episode` WHERE `id` = '$id'";
		$result = mysql_query($query);
		
		$row = mysql_fetch_assoc($result);
		
		if($row['Movie'] == 0)
		{
			$MovieOrNot = 'Episode';
		}
		else
		{
			$MovieOrNot = 'Movie';
		}
		
		if($row['tracker_entry'] == 1)
		{
			$TrackerStatus = $MovieOrNot . ' is in your Episode Tracker.';
		}
		else
		{
			$TrackerStatus = $MovieOrNot . ' is NOT in your Episode Tracker.';
		}
		
		if($row['hd'] == 1)
		{
			// 720p only series
			$quality = '&nbsp;<img src="' . $this->Host . '/series-pages/hd-720p-icon.png" alt="720p Video available" style="vertical-align:middle;height:18px;" border="0" title="Watch this ' . $MovieOrNot . ' in 720p!" />&nbsp;';
		}
		else if($row['hd'] == 2)
		{
			// 1080p and 720p series
			$quality = '&nbsp;<img src="' . $this->Host . '/series-pages/hd-720p-icon.png" alt="720p Video available" style="vertical-align:middle;height:18px;" border="0" title="Watch this ' . $MovieOrNot . ' in 720p!" />&nbsp;<img src="' . $this->Host . '/series-pages/hd-1080p-icon.png" alt="1080p Video available" style="vertical-align:middle;height:18px;" border="0" title="Watch this ' . $MovieOrNot . ' in 1080p!" />';
		}
		else
		{
			// SD only series..
			$quality = '';
		}
		if($this->UserArray[1] == 3 || $this->UserArray[1] == 0)
		{
			$views = '
				<div style="padding-bottom:4px;">
					<div style="display:inline-block;width:60px;">Views:</div>
					<div style="display:inline-block;">0</div>
				</div>';
		}
		else
		{
			$views = '
				<div style="padding-bottom:4px;">
					<div style="display:inline-block;width:60px;">Views:</div>
					<div style="display:inline-block;">' . $row['views'] . '</div>
				</div>';
		}
		if($this->UserArray[2] == 7 || $this->UserArray[6] == 1)
		{
			$CanDownload = TRUE;
		}
		else
		{
			$CanDownload = FALSE;
		}
		
		// ADDED 11/28/2014
		// will add an image to the bottom of the listing so people can see the images
		if($type == 1)
		{
			if($row['image'] == 0)
			{
					$episodepreview = $this->Host . '/video-images/noimage.png';
			}
			else 
			{
				$episodepreview = "{$this->Host}/video-images/{$row['sid']}/{$row['id']}_screen.jpeg";
			}
			$DisplayImage = '
			<div>
				<img src="' . $episodepreview . '" alt="This episode`s image" style="width:365px;" />
			</div>';
		}
		else
		{
			$DisplayImage = '';
		}
		echo '
		<div>
			<div style="display:inline-block;width:69%;vertical-align:top;font-size:12px;">
				<div style="padding-bottom:4px;">
					<div style="display:inline-block;width:60px;vertical-align:top;">Title:</div>
					<div style="display:inline-block;vertical-align:top;word-wrap:break-word;width:185px;">' . stripslashes($row['epname']) . '</div>
				</div>
				<div style="padding-bottom:4px;">
					<div style="display:inline-block;width:60px;">' . $MovieOrNot . ' #:</div>
					<div style="display:inline-block;">' . $row['epnumber'] . '</div>
				</div>
				<div style="padding-bottom:4px;">
					<div style="display:inline-block;width:60px;">Fansub:</div>
					<div style="display:inline-block;">' . $row['subGroup'] . '</div>
				</div>
				' . $views;
				echo '
				<div style="padding-bottom:4px;">
					<div style="display:inline-block;width:60px;vertical-align:top;">Tracker:</div>
					<div style="display:inline-block;word-wrap:break-word;width:185px;">' . $TrackerStatus . '</div>
				</div>
			</div>
			<div style="display:inline-block;width:29%;vertical-align:top;font-size:12px;">
				<div>
					Episode Info:<br />
					<img src="' . $this->Host . '/html5.png" alt="HTML5 Series" style="vertical-align:middle;height:18px;" border="0" title="Watch this video on the AnimeFTW.tv HTML5 Player!" />
					' . $quality . $this->showDownloadOption($row['id'],$CanDownload) . ' 
				</div>
			</div>
			' . $DisplayImage . '
		</div>
				<script>
					(function($){
						$(document).ready(function() {
							$(\'.download-menu\').dropit();
						});
					})(window.jQuery);
				</script>';
	}
	
	private function showDownloadOption($epid,$CanDownload,$disk = FALSE)
	{
		if($CanDownload == TRUE)
		{
			$query = "SELECT `series`.`seriesName`, `series`.`fullSeriesName`, `episode`.`id`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`videotype`, `episode`.`hd` FROM `episode`,`series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id` = $epid";
			$result = mysql_query($query);
			
			$row = mysql_fetch_assoc($result);
			if($disk == TRUE)
			{
				$DLIcon = 'disk.png';
			}
			else
			{
				$DLIcon = 'download-icon.png';
			}
			if($disk == TRUE) {
				$data = '
				<ul class="download-menu" id="dropdown-' . $row['id'] . '">
					<li>
						<a href="#" onClick="return false;"><img src="' . $this->Host . '/' . $DLIcon . '" alt="Advanced Download" title="Click To download ' . $row['fullSeriesName'] . ' Episode ' . $row['epnumber'] . '" style="" border="0" /></a>
						<ul>';
				$i = 0;
				while($i <= $row['hd']){
					if($i == 0){
						$data .= '<li><a href="#" onClick="window.location.href=\'//videos2.animeftw.tv/download.php?series=' . $row['seriesName'] . '&preffix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&hd=480\'; return false;">480p</a></li>';
					}
					else if($i == 1){
						$data .= '<li><a href="#" onClick="window.location.href=\'//videos2.animeftw.tv/download.php?series=' . $row['seriesName'] . '&preffix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&hd=720\'; return false;">720p</a></li>';
					}
					else if($i == 2){
						$data .= '<li><a href="#" onClick="window.location.href=\'//videos2.animeftw.tv/download.php?series=' . $row['seriesName'] . '&preffix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&hd=1080\'; return false;">1080p</a></li>';
					}
					else {
					}
					$i++;					
				}
				$data .= '
						</ul>
					</li>
				</ul>';
			}
			else {
				$data .= '<a href="#" onClick="window.location.href=\'//videos2.animeftw.tv/download.php?series=' . $row['seriesName'] . '&preffix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&hd=480\'; return false;"><img src="' . $this->Host . '/' . $DLIcon . '" alt="Advanced Download" title="Click To download ' . $row['fullSeriesName'] . ' Episode ' . $row['epnumber'] . '" style="" border="0" /></a>';
			}
			return $data;
			//return '<a href="//videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $row['videotype'] . '"><img src="' . $this->Host . '/' . $DLIcon . '" alt="Advanced Download" title="Click To download ' . $row['fullSeriesName'] . ' Episode ' . $row['epnumber'] . '" style="" border="0" /></a>';
		}
	}
}