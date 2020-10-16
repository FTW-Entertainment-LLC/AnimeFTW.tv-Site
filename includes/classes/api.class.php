<?php
/****************************************************************\
## FileName: api.class.php
## Author: Brad Riemann
## Usage: API Implementation Script.
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

#-----------------------------------------
#* Class AFTWDev
#* @bool: width, ssl, gmessage
#* class for Dev functions
#-----------------------------------------

class AFTWDev{
	var $did, $ssl, $series, $eid, $ads, $UserName, $Categories;

	var $Host = 'https://d206m0dw9i4jjv.cloudfront.net';

	public function __construct()
	{
		$this->buildCategories();
	}

	//grab the did
	function get_did($did){
		$this->did = $did;
	}
	//are we SSL?
	function get_ssl($ssl){
		$this->ssl = $ssl;
	}

	public function RecordUsername($username){
		$this->UserName = $username;
	}

	//get Validate DID info
	function ValDID(){
		$query = "SELECT id FROM developers WHERE devkey='".$this->did."'";
		$result = mysqli_query($query);
		$didreturn = mysqli_num_rows($result);
		return $didreturn;
	}
	//get Validate DID info
	function ShowAds(){
		$query = "SELECT ads FROM developers WHERE devkey='".$this->did."'";
		$result = mysqli_query($query);
		$row = mysqli_fetch_array($result);
		if($row['ads'] == 1){$ads = TRUE;}
		else{$ads = FALSE;}
		return $ads;
	}
	//show <results>
	function showtopresult(){
		echo "<results>\n";
	}
	//show </results>
	function showbtmresult(){
		echo "</results>\n";
	}
	private function Query($q){
		$query = mysqli_query($q);
		$query = mysqli_result($query, 0);
		return $query;
	}

	private function ArrQuery($query){
		$query = mysqli_query($query);
		$query = mysqli_fetch_array($query);
		return $query;
	}

	private function buildCategories()
	{
		$query = "SELECT * FROM `categories`";
		$result = mysqli_query($query);
		while($row = mysqli_fetch_assoc($result))
		{
			$this->Categories[$row['id']]['id'] = $row['id'];
			$this->Categories[$row['id']]['name'] = $row['name'];
			$this->Categories[$row['id']]['description'] = $row['description'];
		}
	}

	private function parseNestedArray($products, $field, $value)
	{
	   foreach($products as $key => $product)
	   {
			if ( $product[$field] === $value )
			{
				return $key;
			}
		}
		return false;
	}

	//show anime, paged
	function ShowAnime($sort,$count,$start,$username,$password,$gsort,$alpha = NULL,$SortNum = 0){
		mysqli_query("SET NAMES 'utf8'");

		$client = (isset($_GET['client'])) ? $_GET['client'] : "browser";
		$version = (isset($_GET['version'])) ? $_GET['version'] : 0;

		$UserArr = $this->ArrQuery("SELECT Level_access FROM users WHERE Username = '" . $username . "'");

		//Alphabetical limitation
		//Zigbigidorlu was here :B
		$alphalimit = "";
		if(!is_null($alpha)) {
			if($alpha == "1") {
				$alphalimit = "AND `fullSeriesName` NOT REGEXP '^[a-zA-Z]'";
			} elseif(ctype_alpha($alpha)) {
				$alpha = substr($alpha,0,1);
				$alphalimit = "AND `fullSeriesName` LIKE '$alpha%'";
			}
		}
		if($UserArr[0] == 3) {
			$aonly = 'AND `aonly` <= 1 AND Movies = 0 AND (id != 6 OR id != 35 OR id != 138 OR id != 194 OR id != 238 OR id != 364 OR id != 403 OR id != 446 OR id != 456 OR id != 735 OR id != 818 OR id != 1006)';
		}
		else {
			$aonly = '';
		}

		if($SortNum == 1) {
			$query = "SELECT id, seriesName, fullSeriesName, romaji, kanji, seoname, description, ratingLink, stillRelease, Movies, OVA, category, total_reviews FROM series WHERE active = 'yes' $aonly ORDER BY `id` ASC LIMIT 25";
		}
		elseif($gsort != NULL){
			if(strlen($gsort) > 1)
			{
				$catsort = $this->parseNestedArray($this->Categories, 'name', ucfirst($gsort));
				$query = "SELECT id, seriesName, fullSeriesName, romaji, kanji, seoname, description, ratingLink, stillRelease, Movies, OVA, category, total_reviews FROM series WHERE active='yes' AND category LIKE '% ".$catsort." %' $aonly $alphalimit ORDER BY fullSeriesName ".$sort." LIMIT ".$start.", ".$count;
				$g_s = '&amp;filter='.$catsort;
			}
			else {
				$query = "SELECT id, seriesName, fullSeriesName, romaji, kanji, seoname, description, ratingLink, stillRelease, Movies, OVA, category, total_reviews FROM series WHERE active='yes' AND seriesName LIKE '".$gsort."%' $aonly $alphalimit ORDER BY fullSeriesName ".$sort." LIMIT ".$start.", ".$count;
				$g_s = '&amp;filter='.$gsort;
			}
		}
		else {
			$query = "SELECT id, seriesName, fullSeriesName, romaji, kanji, seoname, description, ratingLink, stillRelease, Movies, OVA, category, total_reviews FROM series WHERE active='yes' $aonly $alphalimit ORDER BY fullSeriesName ".$sort." LIMIT ".$start.", ".$count;
				$g_s = '';
		}
		mysqli_query("SET NAMES 'utf8'");
		$result = mysqli_query($query);
		echo '<results start="'.$start.'" count="'.$count.'" next="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;client='.$client.'&amp;version='.$version.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=anime'.$g_s.'&amp;start='.($start+$count).'&amp;count='.$count.'"';
		if($start == 0){echo "\n";}
		else {
			echo ' previous="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;client='.$client.'&amp;version='.$version.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=anime'.$g_s.'&amp;start='.($start-$count).'&amp;count='.$count.'"';
		}
		echo '>'."\n";
		while(list($id,$seriesName,$fullSeriesName,$romaji,$kanji,$seoname,$description,$ratingLink,$stillRelease,$Movies,$OVA,$category,$total_reviews) = mysqli_fetch_array($result))
		{
			$fullSeriesName = stripslashes($fullSeriesName);
			$description = stripslashes($description);
			//$description = preg_replace("/\`/"," ",$description);
			$description = preg_replace('/[^0-9a-z?-????\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', ' ',$description);
			//$description = htmlspecialchars($description);
  echo '	<series href="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;client='.$client.'&amp;version='.$version.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=series&amp;title='.$seriesName.'">
		<seriesName><![CDATA['.$fullSeriesName.']]></seriesName>
		<id>'.$id.'</id>
		<romaji><![CDATA['.$romaji.']]></romaji>
		<kanji><![CDATA['.$kanji.']]></kanji>'."\n";
		//<kanji><![CDATA['.utf8_encode($kanji).']]></kanji>'."\n";
		if($OVA == 1){echo '	<ova>yes</ova>'."\n";}
		else{ echo '	<ova>no</ova>'."\n";}
  echo '		<airing>'.$stillRelease.'</airing>
		<episodes>'.$this->Query("SELECT COUNT(id) FROM episode WHERE sid='$id'").'</episodes>
		<movies>'.$Movies.'</movies>';

		$exploded = explode(" , ",$category);
		$category = '';
		$i = 0;
		$count = count($exploded);
		foreach($exploded as $value)
		{
			$category .= $this->Categories[$value]['name'];
			$i++;
			if($i < $count)
			{
				$category .= ', ';
			}
		}

		echo '
		<category>' . $category.'</category>';
		echo '
		<rating>'.substr($ratingLink, 0, -4).'</rating>
		<description><![CDATA['.$description.']]></description>
		<reviews>'.$total_reviews.'</reviews>
		<image><![CDATA[' . $this->Host . '/seriesimages/'.$id.'.jpg]]></image>
	</series>'."\n";
		}
		echo "</results>\n";
	}
	//get our seriesName var
	function get_seriesname($seriesname)
	{
		$query = "SELECT `id` FROM `series` WHERE `seriesName` = '" . mysqli_real_escape_string($seriesname) . "'";
		$result = mysqli_query($query);
		$row = mysqli_fetch_assoc($result);
		$this->series = $row['id'];
	}
	//show episode list of single anime series
	function ShowAnimeEpisodes($sort,$username,$password,$limit,$showad,$appkey=array()){
		$appver = (isset($appkey[1])) ? $appkey[1] : 0;
		if($limit == TRUE && ($showad == FALSE || $appver < 3)){$limitfunc = " LIMIT 0, 2";}
		//Manual limit
		//Zigbigidorlu was here =D
		elseif(isset($_GET['limit']) && is_numeric($_GET['limit']) && $appver < 3) {
			$limitfunc = " LIMIT 0,".$_GET['limit'];
		} else {$limitfunc = "";}
		$query = "SELECT id, sid, epnumber, epname, seriesname, vidheight, vidwidth, epprefix, subGroup, Movie, doubleEp, date, videotype, image FROM episode WHERE sid='".$this->series."' AND Movie='0' ORDER BY epnumber ".$sort.$limitfunc;
		mysqli_query("SET NAMES 'utf8'");
		$result = mysqli_query($query);
		echo '<episodes>'."\n";
		if($limit == TRUE && ($showad == FALSE || $appver < 3)){
			echo "		<episode href=\"https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=episode&amp;id=0\">\n";
			echo "			<id>0</id>\n";
			echo "			<epnumber>0</epnumber>\n";
			echo "			<name><![CDATA[WARNING: AnimeFTW.tv will be closing November 30th, 2017, see site for details.]]></name>\n";
			echo "			<height>0</height>\n";
			echo "			<fansub><![CDATA[Unknown]]></fansub>\n";
			echo "			<added format=\"gmt -6\">0</added>\n";
			echo "			<type>divx</type>\n";
			echo "			<image><![CDATA[" . $this->Host . "/video-images/noimage.png]]></image>\n";
			echo "			<videolink><![CDATA[https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=series]]></videolink>\n";
			echo "		</episode>\n";
		}
		while(list($id,$sid,$epnumber,$epname,$seriesname,$vidheight,$vidwidth,$epprefix,$subGroup,$Movie,$doubleEp,$date,$videotype,$image) = mysqli_fetch_array($result))
		{
			if($image == 0){$imvarb = $this->Host . '/video-images/noimage.png';}
			else {$imvarb = "{$this->Host}/video-images/{$sid}/{$id}_screen.jpeg";}
			$epname = stripslashes($epname);
			$epname = preg_replace('/[^0-9a-z?-????\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', ' ',$epname);
			echo "		<episode href=\"https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=episode&amp;id=".$id."\">\n";
			echo "			<id>".$id."</id>\n";
			echo "			<epnumber>".$epnumber."</epnumber>\n";
			echo "			<name><![CDATA[".$epname."]]></name>\n";
			echo "			<height>".$vidheight."</height>\n";
			echo "			<fansub><![CDATA[".$vidwidth."]]></fansub>\n";
			echo "			<added format=\"gmt -6\">".$date."</added>\n";
			echo "			<type>".$videotype."</type>\n";
			echo "			<image><![CDATA[".$imvarb."]]></image>\n";
			echo "			<videolink><![CDATA[".$this->VideoLink($sid,$epprefix,$epnumber,$videotype,$Movie)."]]></videolink>\n";
			echo "		</episode>\n";
		}
		echo "</episodes>\n";
	}
	//get our seriesName var
	function get_episodeid($eid){
		$this->eid = $eid;
	}
	//show episode list of single anime
	function ShowEpisode(){
		$query = "SELECT id, sid, epnumber, epname, seriesname, vidheight, vidwidth, epprefix, subGroup, Movie, doubleEp, date, videotype, image FROM episode WHERE id='".$this->eid."'";
		mysqli_query("SET NAMES 'utf8'");
		$result = mysqli_query($query);
		$row = mysqli_fetch_array($result);
		if($row['image'] == 0){$imvarb = $this->Host . '/video-images/noimage.png';}
		else {$imvarb = "{$this->Host}/video-images/{$row['sid']}/{$row['id']}_screen.jpeg";}
		echo "		<episode>\n";
		echo "			<id>".$row['id']."</id>\n";
		echo "			<name><![CDATA[".stripslashes($row['epname'])."]]></name>\n";
		echo "			<epnumber>".$row['epnumber']."</epnumber>\n";
		echo "			<height>".$row['vidheight']."</height>\n";
		echo "			<width>".$row['vidwidth']."</width>\n";
		echo "			<fansub><![CDATA[".$row['subGroup']."]]></fansub>\n";
		echo "			<movie>";
		if($row['Movie'] == 0){echo 'No';}
		else{echo 'Yes';}
		echo "</movie>\n";
		echo "			<type>".$row['videotype']."</type>\n";
		echo "			<added format=\"gmt -6\">".$row['date']."</added>\n";
		echo "			<image><![CDATA[".$imvarb."]]></image>\n";
		echo "			<videolink><![CDATA[".$this->VideoLink($row['sid'],$row['epprefix'],$row['epnumber'],$row['videotype'],$row['Movie'])."]]></videolink>\n";
		echo "		</episode>\n";
	}
	//get basic FALSE or TRUE for username
	function CheckUser($username){
		$query = "SELECT Level_access FROM users WHERE Username='".$username."'";
		$result = mysqli_query($query);
		$row = mysqli_fetch_array($result);
		if($row['Level_access'] == 3){$rv = TRUE;}
		else {$rv = FALSE;}
		return $rv;
	}
	//show movie listings for a series
	function ShowAnimeMovies($sort,$username,$password,$limit,$showad,$appkey=array()){
		$appver = (isset($appkey[1])) ? $appkey[1] : 0;
		if($limit == TRUE && $showad == TRUE && $appver < 3){$limitfunc = " LIMIT 0, 2";} else{$limitfunc = "";}
		$query = "SELECT id, sid, epnumber, epname, seriesname, vidheight, vidwidth, epprefix, subGroup, Movie, doubleEp, date, videotype, image FROM episode WHERE sid='".$this->series."' AND Movie='1'".$limitfunc." ORDER BY epnumber ".$sort;
		mysqli_query("SET NAMES 'utf8'");
		$result = mysqli_query($query);
		echo '<movies>'."\n";
		if($limit == TRUE && $showad == TRUE && $appver < 3){
			echo "		<movie href=\"https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=episode&amp;id=0\">\n";
			echo "			<id>0</id>\n";
			echo "			<epnumber>0</epnumber>\n";
			echo "			<name><![CDATA[WARNING: AnimeFTW.tv will be closing November 30th, 2017, see site for details.]]></name>\n";
			echo "			<height>0</height>\n";
			echo "			<fansub><![CDATA[Unknown]]></fansub>\n";
			echo "			<type>mkv</type>\n";
			echo "			<added format=\"gmt -6\">0</added>\n";
			echo "			<image><![CDATA[" . $this->Host . "/video-images/noimage.png]]></image>\n";
			echo "			<videolink><![CDATA[https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=series]]></videolink>\n";
			echo "		</movie>\n";
		}
		else {
			while(list($id,$sid,$epnumber,$epname,$seriesname,$vidheight,$vidwidth,$epprefix,$subGroup,$Movie,$doubleEp,$date,$videotype,$image) = mysqli_fetch_array($result))
			{
				$epname = stripslashes($epname);
				$epname = preg_replace('/[^0-9a-z\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', ' ',$epname);
				if($image == 0){$imvarb = $this->Host . '/video-images/noimage.png';}
				else {$imvarb = "{$this->Host}/video-images/{$sid}/{$id}_screen.jpeg";}
				echo "		<movie href=\"https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=episode&amp;id=".$id."\">\n";
				echo "			<id>".$id."</id>\n";
				echo "			<epnumber>".$epnumber."</epnumber>\n";
				echo "			<name><![CDATA[".stripslashes($epname)."]]></name>\n";
				echo "			<height>".$vidheight."</height>\n";
				echo "			<width>".$vidwidth."</width>\n";
				echo "			<fansub><![CDATA[".$subGroup."]]></fansub>\n";
				echo "			<type>".$videotype."</type>\n";
				echo "			<added format=\"gmt -6\">".$date."</added>\n";
				echo "			<image><![CDATA[".$imvarb."]]></image>\n";
				echo "			<videolink><![CDATA[".$this->VideoLink($sid,$epprefix,$epnumber,$videotype,$Movie)."]]></videolink>\n";
				echo "		</movie>\n";
			}
		}
		echo "</movies>\n";
	}
	//show latest episodes
	function ShowLatestEpisodes($sort,$count,$start,$username,$password,$limit,$showad){
		if($limit == TRUE && $showad == TRUE){
			echo "	<results>\n";
			echo "		<episode href=\"https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=episode&amp;id=0\">\n";
			echo "			<id>0</id>\n";
			echo "			<name><![CDATA[ERROR: Regular Members are not allowed to view the latest episodes listing.]]></name>\n";
			echo "			<epnumber>0</epnumber>\n";
			echo "			<series><![CDATA[NONE]]></series>\n";
			echo "			<height>0</height>\n";
			echo "			<width>0</width>\n";
			echo "			<fansub><![CDATA[Unknown]]></fansub>\n";
			echo "			<movie>No</movie>\n";
			echo "			<type><![CDATA[divx]]></type>\n";
			echo "			<added format=\"gmt -6\">0</added>\n";
			echo "			<image><![CDATA[" . $this->Host . "/site-images/video-images/noimage.png]]></image>\n";
			echo "			<videolink><![CDATA[https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=series]]></videolink>\n";
			echo "		</episode>\n";
		}
		else {
			$query = "SELECT id, sid, epnumber, epname, seriesname, vidheight, vidwidth, epprefix, subGroup, Movie, doubleEp, date, videotype, image FROM episode ORDER BY id ".$sort." LIMIT ".$start.", ".$count;
			mysqli_query("SET NAMES 'utf8'");
			$result = mysqli_query($query);
			$TheResults = '<results start="'.$start.'" count="'.$count.'" next="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=latest&amp;start='.($start+$count).'&amp;count='.$count.'"';
			if($start == 0){echo "\n";}
			else
			{
				$TheResults .= ' previous="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=latest&amp;start='.($start-$count).'&amp;count='.$count.'"';
			}
			$TheResults .= ">\n";
			echo $TheResults;
			while(list($id,$sid,$epnumber,$epname,$seriesname,$vidheight,$vidwidth,$epprefix,$subGroup,$Movie,$doubleEp,$date,$videotype,$image) = mysqli_fetch_array($result))
			{
				if($image == 0){$imvarb = $this->Host . '/video-images/noimage.png';}
				else {$imvarb = "{$this->Host}/video-images/{$sid}/{$id}_screen.jpeg";}
				echo "		<episode>\n";
				echo "			<id>".$id."</id>\n";
				echo "			<name><![CDATA[".stripslashes($epname)."]]></name>\n";
				echo "			<epnumber>".$epnumber."</epnumber>\n";
				echo "			<series><![CDATA[".QuickSeriesCheck($sid)."]]></series>\n";
				echo "			<height>".$vidheight."</height>\n";
				echo "			<width>".$vidwidth."</width>\n";
				echo "			<fansub><![CDATA[".$subGroup."]]></fansub>\n";
				echo "			<movie>";
				if($Movie == 0){echo 'No';}
				else{echo 'Yes';}
				echo "</movie>\n";
				echo "			<type><![CDATA[".$videotype."]]></type>\n";
				echo "			<added format=\"gmt -6\"><![CDATA[".$date."]]></added>\n";
				echo "			<image><![CDATA[".$imvarb."]]></image>\n";
				echo "			<videolink><![CDATA[".$this->VideoLink($sid,$epprefix,$epnumber,$videotype,$Movie)."]]></videolink>\n";
				echo "		</episode>\n";
			}
		}
	}
	//Tag cloud modified
	function ShowTagCloud($username,$password){
		include('wordcloud.class.php');
		$cloud = new wordcloud();
		mysqli_query("SET NAMES 'utf8'");
		$query = mysqli_query("SELECT name FROM categories ORDER BY name DESC");
		if ($query)
		{
			while ($row = mysqli_fetch_assoc($query))
			{
				//$getTags = explode(' ', $row['category']);
				$getTags = split(", ", $row['name']);
				foreach ($getTags as $key => $value)
				{
					$value = trim($value);
					$cloud->addWord($value);
				}
			}
		}
		//$cloud->orderBy('ASC');
		$myCloud = $cloud->showCloud('array');
		if (is_array($myCloud))
		{
			foreach ($myCloud as $key => $value)
			{
				  echo "	<tag href=\"https://".$_SERVER['HTTP_HOST']."/api/v1/show?did=".$this->did."&amp;username=".$username."&amp;password=".$password."&amp;show=anime&amp;filter=".$key."\">".$key."</tag>\n";
			}
		}
	}

	function checkdbversion() {
		$alphalist = array("1","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$returnlist = "";

		$returnlist .= "<signatures>";
		foreach($alphalist as $alpha) {
			if($alpha == "1") {
				$alphalimit = "AND `fullSeriesName` NOT REGEXP '^[a-zA-Z]'";
			} elseif(ctype_alpha($alpha)) {
				$alpha = substr($alpha,0,1);
				$alphalimit = "AND `fullSeriesName` LIKE '$alpha%'";
			}
			mysqli_query("SET NAMES 'utf8'");
			if($query = mysqli_query("SELECT `fullSeriesName` FROM series WHERE active='yes' $alphalimit")) {
				if(mysqli_num_rows($query)) {
					$temp = array();
					while($row = mysqli_fetch_assoc($query)) {
						$signature = sha1($row['fullSeriesName']);
						$temp[] = $signature;
					}
					$temp = implode("|",$temp);
					$signature = sha1($temp);
					$returnlist .= "<signature alpha=\"$alpha\" value=\"$signature\" />\n";
				} else {
					$signature = sha1("NULL");
					$returnlist .= "<signature alpha=\"$alpha\" value=\"$signature\" />\n";
				}
			} else {
				$error = 1;
			}
		}
		$returnlist .= "</signatures>";

		if(isset($error)) {
			echo '<result code="301" title="Erronious API GET" />';
		} else {
			echo trim($returnlist);
		}
	}

	//record our logs
	function RecordDevLogs($username,$url,$agent,$ip){
		$query = "SELECT id FROM developers WHERE devkey='".$this->did."'";
		$result = mysqli_query($query);
		$row = mysqli_fetch_array($result);
		$query = "INSERT INTO developers_logs (date, did, uid, agent, ip, url)
VALUES ('".time()."', '".$row['id']."', '".GetUID($username)."', '".$agent."', '".$ip."', '".$url."')";
		mysqli_query($query) or die('Could not connect, way to go retard:' . mysqli_error());
		$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\''.GetUID($username).'\'';
		mysqli_query($query) or die('Error : ' . mysqli_error());
	}

	public function Search($sort,$count,$start,$username,$password,$SearchInput,$alpha = NULL){
		mysqli_query("SET NAMES 'utf8'");

		$client = (isset($_GET['client'])) ? $_GET['client'] : "browser";
		$version = (isset($_GET['version'])) ? $_GET['version'] : 0;

		$UserArr = $this->ArrQuery("SELECT Level_access FROM users WHERE Username = '" . $username . "'");

		if($UserArr[0] == 3) {
			$aonly = 'AND `aonly` <= 1 AND Movies = 0 AND (id != 6 OR id != 35 OR id != 138 OR id != 194 OR id != 238 OR id != 364 OR id != 403 OR id != 446 OR id != 456 OR id != 735 OR id != 818 OR id != 1006) ';
		}
		else {
			$aonly = '';
		}


		//Alphabetical limitation
		//Zigbigidorlu was here :B
		$alphalimit = "";
		if(!is_null($alpha)) {
			if($alpha == "1") {
				$alphalimit = "AND `fullSeriesName` NOT REGEXP '^[a-zA-Z]'";
			} elseif(ctype_alpha($alpha)) {
				$alpha = substr($alpha,0,1);
				$alphalimit = "AND `fullSeriesName` LIKE '$alpha%'";
			}
		}
		//santize!
		$SearchInput = htmlentities($SearchInput);
		$SearchInput = mysqli_real_escape_string($SearchInput);

		$query = "SELECT id, seriesName, fullSeriesName, romaji, kanji, seoname,description, ratingLink, stillRelease, Movies, OVA, category, total_reviews FROM series WHERE active='yes' AND ( fullSeriesName LIKE '%".$SearchInput."%' OR romaji LIKE '%".$SearchInput."%' OR kanji LIKE '%".$SearchInput."%' ) " . $aonly . "ORDER BY fullSeriesName LIMIT ".$start.",".$count;
		mysqli_query("SET NAMES 'utf8'");
		$result = mysqli_query($query);
		echo '<results start="'.$start.'" count="'.$count.'" next="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;client='.$client.'&amp;version='.$version.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=search&amp;for='.$SearchInput.'&amp;start='.($start+$count).'&amp;count='.$count.'"';
		if($start == 0){echo "\n";}
		else {
			echo ' previous="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;client='.$client.'&amp;version='.$version.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=search&amp;for='.$SearchInput.'&amp;start='.($start-$count).'&amp;count='.$count.'"';
		}
		echo '>'."\n";
		while(list($id,$seriesName,$fullSeriesName,$romaji,$kanji,$seoname,$description,$ratingLink,$stillRelease,$Movies,$OVA,$category,$total_reviews) = mysqli_fetch_array($result))
		{
			$fullSeriesName = stripslashes($fullSeriesName);
			$description = stripslashes($description);
			//$description = preg_replace("/\`/"," ",$description);
			$description = preg_replace('/[^0-9a-z?-????\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', ' ',$description);
			//$description = htmlspecialchars($description);
  echo '	<series href="https://'.$_SERVER['HTTP_HOST'].'/api/v1/show?did='.$this->did.'&amp;client='.$client.'&amp;version='.$version.'&amp;username='.$username.'&amp;password='.$password.'&amp;show=series&amp;title='.$seriesName.'">
		<seriesName><![CDATA['.$fullSeriesName.']]></seriesName>
		<id>'.$id.'</id>
		<romaji><![CDATA['.$romaji.']]></romaji>
		<kanji><![CDATA['.utf8_encode($kanji).']]></kanji>'."\n";
		if($OVA == 1){echo '	<ova>yes</ova>'."\n";}
		else{ echo '	<ova>no</ova>'."\n";}
  echo '		<airing>'.$stillRelease.'</airing>
		<episodes>'.$this->Query("SELECT COUNT(id) FROM episode WHERE sid='$id'").'</episodes>
		<movies>'.$Movies.'</movies>';

		$exploded = explode(" , ",$category);
		$category = '';
		$i = 0;
		$count = count($exploded);
		foreach($exploded as $value)
		{
			$category .= $this->Categories[$value]['name'];
			$i++;
			if($i < $count)
			{
				$category .= ', ';
			}
		}

		echo '
		<category>'.$category.'</category> ';
		echo '
		<rating>'.substr($ratingLink, 0, -4).'</rating>
		<description><![CDATA['.$description.']]></description>
		<reviews>'.$total_reviews.'</reviews>
		<image><![CDATA[' . $this->Host . '/seriesimages/'.$id.'.jpg]]></image>
	</series>'."\n";
		}
		echo "</results>\n";
	}

	/*private function GrabEpisodeData($epid,$type){
		if($type == 1){ //1 = total rating votes
			$query = "SELECT COUNT(rating_num), rating_num FROM ratings WHERE rating_id = 'v".$epid."' GROUP BY rating_num";
		}
		else if($type == 2){ //2 = Average rating
		}
		else if($type == 3){ //3 = screenshot
		}
		else {
		}
	}*/

	public function RecordAnalytics(){
		$ga_uid = 'UA-6243691-1'; // Enter your unique GA Urchin ID (utmac)
		$ga_domain = 'animeftw.tv'; // Enter your domain name/host name (utmhn)
		$ga_randNum = rand(1000000000,9999999999);// Creates a random request number (utmn)
		$ga_cookie = rand(10000000,99999999);// Creates a random cookie number (cookie)
		$ga_rand = rand(1000000000,2147483647); // Creates a random number below 2147483647 (random)
		$ga_today = time(); // Current Timestamp
		$ga_referrer = @$_SERVER['HTTP_REFERER']; // Referrer url

		$ga_userVar=''; // Enter any variable data you want to pass to GA or leave blank
		$ga_hitPage = @$_SERVER['REQUEST_URI']; // Enter the page address you want to track

		$gaURL = 'http://www.google-analytics.com/__utm.gif?utmwv=1&utmn='.$ga_randNum.'&utmsr=-&utmsc=-&utmul=-&utmje=0&utmfl=-&utmdt=-&utmhn='.$ga_domain.'&utmr='.$ga_referrer.'&utmp='.$ga_hitPage.'&utmac='.$ga_uid.'&utmcc=__utma%3D'.$ga_cookie.'.'.$ga_rand.'.'.$ga_today.'.'.$ga_today.'.'.$ga_today.'.2%3B%2B__utmb%3D'.$ga_cookie.'%3B%2B__utmc%3D'.$ga_cookie.'%3B%2B__utmz%3D'.$ga_cookie.'.'.$ga_today.'.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B__utmv%3D'.$ga_cookie.'.'.$ga_userVar.'%3B';

		$handle = @fopen($gaURL, "r"); // open the xml file
		$fget = @fgets($handle); // get the XML data
		@fclose($handle); // close the xml file
	}
	//Video Link get request
private function VideoLink($sid,$epprefix,$ep,$videotype,$Movie){
	$style = 0;
	$query = "SELECT seriesName, videoServer FROM series WHERE id='".$sid."'";
	$result = mysqli_query($query);
	$row = mysqli_fetch_array($result);
	// sub query, cause im lazy now..
	$squery = "SELECT Level_access FROM users WHERE Username = '".$this->UserName."'";
	$subquery = mysqli_query($squery);
	$sr = mysqli_fetch_array($subquery);
	$sr = $sr['Level_access'];
	$CDNDown = 1; //if the CDN is down, changing this to 1 moves things to zeus.
	if($ep == 1 && $Movie == 0){
		$sr = 7;
	}
	if($style == 1){
		if($sr == 3 || $CDNDown == 1){
			$final = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $epprefix . "_" . $ep . '_ns.'.$videotype;
		}
		else {
			$final = 'http://static.ftw-cdn.com/'.$videos.'/' . $row['seriesName'] . '/' . $epprefix . "_" . $ep . '_ns.'.$videotype;
		}
	}
	else {
		if($row['videoServer'] == 'videos'){$videos = 'videos1';}
		else {$videos = $row['videoServer'];}
		if($sr == 3 || $CDNDown == 1)
		{
			$final = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $epprefix . "_" . $ep . '_ns.'.$videotype;
		}
		else
		{
			$final = 'http://static.ftw-cdn.com/'.$videos.'/' . $row['seriesName'] . '/' . $epprefix . "_" . $ep . '_ns.'.$videotype;
		}
	}
	return $final;
}
}

//quick series Check
function QuickSeriesCheck($sid){
	$query = "SELECT fullSeriesName FROM series WHERE id='".$sid."'";
	$result = mysqli_query($query);
	$row = mysqli_fetch_array($result);
	$fullSeriesName = stripslashes($row['fullSeriesName']);
	return $fullSeriesName;
}
//quick series Check
function GetUID($username){
	$query = "SELECT ID FROM users WHERE Username='".$username."'";
	$result = mysqli_query($query);
	$row = mysqli_fetch_array($result);
	$ID = stripslashes($row['ID']);
	return $ID;
}
?>
