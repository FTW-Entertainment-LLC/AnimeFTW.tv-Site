<?php
#***********************************************************
#* global_functions.php, Version 2.0
#* Written by Brad Riemann
#* Copywrite 2008-2011 FTW Entertainment LLC
#* Distrobution of this is stricty forbidden
#***********************************************************

include 'config.php';
include 'newsOpenDb.php';
$siteroot = 'dev.animeftw.tv';
//Functions
	
//clean function
function CleanFileName($Raw){
	$Raw = trim($Raw);
	$RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" );
	$ReplaceWith = array("-", "", "-");
	return preg_replace($RemoveChars, $ReplaceWith, $Raw);
}


#-----------------------------------------
#* Class AFTWUser
#* @bool: id
#* class for user account details.
#-----------------------------------------

class AFTWUser{
	var $id;
	var $ssl;
	var $username;
	
	//grab our username
	function get_username($username){
		$this->username = $username;
	}	
	// Let's set our ID
	function get_id($user_id){
		$this->id = $user_id;
	}
	// Using a SSL?
	function get_ssl($ssl_port){
		if($ssl_port == 80){
			$this->ssl = 'http';
		}
		else {
			$this->ssl = 'https';
		}
	}
	
	// Get the basic User information
	function checkUserName($type) {
		//0=full-link,1=full-no link,2=Username-no link
		if($type == 2){$query = "SELECT Username FROM users WHERE ID='".$this->id."'";}
		else {$query = "SELECT Username, Level_access, advanceImage, Active FROM users WHERE ID='".$this->id."'";}
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		$row = mysqli_fetch_array($result);
		if($type == 0){$frontUser = '<a href="'.$this->ssl.'://dev.animeftw.tv/profile/' . $row['Username'] . '">';$endUser = '</a>';}
		else {$frontUser = '';$endUser = '';}
		if($type == 2){
			$fixedUsername = $row['Username'];
		}
		else {
			if($row['Active'] == 1){
				if($row['Level_access'] == 1){$fixedUsername = '<img src="'.$this->ssl.'://static.ftw-cdn.com/site-images/adminbadge.gif" alt="Admin of Animeftw" style="vertical-align:middle;" border="0" />' . $frontUser . $row['Username'] . $endUser;}
				else if($row['Level_access'] == 2){$fixedUsername = '<img src="'.$this->ssl.'://static.ftw-cdn.com/site-images/manager.gif" alt="Group manager of Animeftw" style="vertical-align:middle;" border="0" />'.$frontUser.$row['Username'].$endUser;}
				else if($row['Level_access'] == 7){$fixedUsername = '<img src="'.$this->ssl.'://static.ftw-cdn.com/site-images/advancedimages/'.$row['advanceImage'].'.gif" alt="Advanced User Title" style="vertical-align:middle;" border="0" />'.$frontUser.$row['Username'].$endUser;}
				else{$fixedUsername = $frontUser.$row['Username'].'</a>';}
			}
			else {$fixedUsername = $frontUser.'<s>'.$row['Username'].'</s>'.$endUser;}
		}
		return $fixedUsername;
	}
	//return variable from the Username
	function returnVarName($var){
		$query = "SELECT $var FROM users WHERE username='".$this->username."'";
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		$row = mysqli_fetch_array($result);
		return $row[$var];
	}
	//return variable from the Username
	function returnVarId($var){
		$query = "SELECT $var FROM users WHERE username='".$this->id."'";
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		$row = mysqli_fetch_array($result);
		return $row[$var];
	}
}

#-----------------------------------------
#* Class AFTWEps
#* @bool: id
#* class for series and the episodes
#-----------------------------------------

class AFTWVideos{
	var $id;
	var $ssl;
	
	//Lets get the ID number.. Whatever it is..
	function get_id($id){
		$this->id = $id;
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
	
	#-------------------------------------------------------------
	# Function showListing
	# Shows in 3 cloumn tier of series listings
	# --
	#-------------------------------------------------------------
	
	function showListing ($listType,$sort,$alevel){
		if($alevel == 0){$aonly = "AND aonly='0'";}
		else if ($alevel == 3){$aonly = "AND aonly<='1'";}
		else{$aonly = '';}
		if($sort == NULL)
		{
			$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' ".$aonly."ORDER BY fullSeriesName";
		}
		else {
			$sql = "SELECT UPPER(SUBSTRING(seriesName,1,1)) AS letter, id, fullSeriesName FROM series WHERE seriesList='$listType' ".$aonly."AND category LIKE '%".mysqli_real_escape_string($sort)."%' ORDER BY seriesName";
		}
		$query = mysqli_query ($sql) or die (mysqli_error());
		$total_rows = mysqli_num_rows($query) or die("Error: ". mysqli_error(). " with query ". $query);
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
					echo "		<div>".checkSeriesWIconsV2($key)."</div>\n";
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
	}
	
	#-----------------------------------------------------------
	# Function checkSeries
	# checks the series for a given id number and gives it an airing icon or anything else it needs
	# t0=full w/ icons, t1=simple, link w/ full name
	#-----------------------------------------------------------	
			
	function checkSeries($id,$type) {
		if($type=0){$query = "SELECT fullSeriesName, seoname, stillRelease, seriesType, seriesList, moviesOnly FROM series WHERE id='$id'";}
		else {$query = "SELECT fullSeriesName, seoname, seriesList, seriesList FROM series WHERE id='$id'";}
		$row = mysqli_fetch_array($result = mysqli_query($query) or die('Error : ' . mysqli_error()));
		$fullSeriesName = stripslashes($row['fullSeriesName']); 
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
			$Type = '&nbsp;<img src="http://aftw.static.ftw-cdn.com/site-images/mkv-series.png" alt="MKV series" title="This series is in DivX Web 2.0 Format" style="vertical-align:middle;" border="0" />';
		}
		else {
			$Type = '';
		}
		if($stillRelease == 'yes')
		{
			$airing = '&nbsp;<img src="http://aftw.static.ftw-cdn.com/site-images/airing_icon.gif" alt="Airing" title="This Series is Airing" style="vertical-align:middle;" border="0" />';
		}
		else {
			$airing = '&nbsp;';
		}
		if($moviesOnly == 1)
		{
			$Type .= '&nbsp;<img src="http://aftw.static.ftw-cdn.com/site-images/movie_blue.png" alt="Movie" title="This is a Movie"  style="vertical-align:middle;" border="0" />';
		}
		$FinalLink = '<a href="/'.$seriesList.'/'.$seoname.'/">'.$fullSeriesName.'</a>'.$airing.$Type;
		return $FinalLink;
	}
}

#-----------------------------------------
#* Class AFTWBlog
#* @bool: id
#* class for blogs
#-----------------------------------------

class AFTWBlog{
	var $id; //User ID
	var $showLimit; //Limit var
	var $rperm; //Read permission var
	var $cperm; //comment permission var
	var $tzone; //TimeZone of the Reading user
	var $uname; //Username for the poster
	var $bid; //Username for the poster
	//get id function
	function get_vars($id,$showLimit,$rperm,$cperm,$tzone,$uname){
		$this->id = $id;
		$this->showLimit = $showLimit;
		$this->rperm = $rperm;
		$this->cperm = $cperm;
		$this->tzone = $tzone;
		$this->uname = $uname;
	}
	//grab the current blog id
	function get_bid($bid){
		$this->bid = $bid;
	}
	// grabs the latest blog entries
	function LatestBlogs(){
		$query = "SELECT id, title, content, readperm, commentperm, date, category FROM blog_content WHERE uid='".$this->id."' AND readperm LIKE '%".$this->rperm."%' ORDER BY id ASC LIMIT 0, ".$this->showLimit;
		$result = mysqli_query($query);
		$total_entries = mysqli_num_rows($result);
		if($total_entries == 0){
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>".$this->uname." has not made any Blog Posts.</span>\n";
			echo "</div>\n";
		}
		else {
			while(list($id,$title,$content,$data,$category) = mysqli_fetch_array($result))
			{
				$subline = "Posted by ".$this->uname." on mm/dd/yyyy";
				echo "<div class='side-body-bg'>\n";
				echo "<span class='scapmain'><a href='/blogs/".$this->uname."/$id-".stripslashes(CleanFileName($title))."/'>".stripslashes($title)."</a></span>\n";
				echo "<br />\n";
				echo "<span class='poster'>$subline</span>\n";
				echo "</div>\n";
				echo "<div class='tbl'>$content</div>\n";
				echo "<br />\n";
			}
		}
	}
	// Will give the selected Blog post
	function SingleBlog(){
		$query = "SELECT id, title, content, readperm, commentperm, date, category FROM blog_content WHERE id='".$this->bid."' AND uid='".$this->id."'";
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		$result = mysqli_query($query);
		$total_entries = mysqli_num_rows($result);
		if($total_entries == 0){
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>Error: No Blog postings found.</span>\n";
			echo "</div>\n";
		}
		else {
			$row = mysqli_fetch_array($result);
			$subline = "Posted by ".$this->uname." on mm/dd/yyyy";
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'><a href='/blogs/".$this->uname."/".$row['id']."-".stripslashes(CleanFileName($row['title']))."/'>".stripslashes($row['title'])."</a></span>\n";
			echo "<br />\n";
			echo "<span class='poster'>$subline</span>\n";
			echo "</div>\n";
			echo "<div class='tbl'>".$row['content']."</div>\n";
			echo "<br />\n";
		}
	}
}

#-----------------------------------------
#* Class AFTWpage
#* @bool: width, ssl, gmessage
#* class for pages
#-----------------------------------------

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
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>$header</span>\n";
		echo "<br />\n";
		echo "<span class='poster'>$subline</span>\n";
		echo "</div>\n";
		echo "<div class='tbl'>$body</div>\n";
		echo "<br />\n";
	}	
}

?>