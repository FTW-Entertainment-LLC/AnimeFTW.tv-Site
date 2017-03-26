<?php
/****************************************************************\
## FileName: series.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Series Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Series extends Config {

	public $Data, $UserArray, $DevArray, $permissionArray, $MessageCodes;
	private $AdvanceRestrictions;
    
	public function __construct($Data = NULL,$UserArray = NULL,$DevArray = NULL,$permissionArray = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserArray = $UserArray;
		$this->DevArray = $DevArray;
		$this->permissionArray = $permissionArray;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
		if($this->UserArray['Level_access'] == 0 || $this->UserArray['Level_access'] == 3)
		{
			// Special restrictions for guests and basic members
			$this->AdvanceRestrictions = ' AND `aonly` <= 1 AND (id != 6 OR id != 35 OR id != 138 OR id != 194 OR id != 238 OR id != 364 OR id != 403 OR id != 446 OR id != 456 OR id != 735 OR id != 818 OR id != 1006)';
		}
		else
		{
			$this->AdvanceRestrictions = '';
		}
	}
	
	public function array_displaySingleSeries()
	{
		// restrict output to a specific ID, if it's not available then we need to let them know.
		if(isset($this->Data['id']) && is_numeric($this->Data['id']))
		{
			$addonQuery = '';
			if($this->DevArray['license'] == 0 && ($this->UserArray['Level_access'] == 0 || $this->UserArray['Level_access'] == 3)) {
				// it means the content we can show is only unlicensed.
				$addonQuery = " AND `license` = 0";
			}
			// We consider this a valid single series, an ID needs to be supplied, and nothing else to ensure system level continuity.
			$this->mysqli->query("SET NAMES 'utf8'");
			$query = "SELECT `id`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesonly`, `noteReason`, `category`, `prequelto`, `sequelto`, `hd`, (SELECT COUNT(id) FROM `" . $this->MainDB . "`.`watchlist` WHERE `sid`=`series`.`id` AND `uid`= " . $this->UserArray['ID'] . ") AS `watchlist` FROM `" . $this->MainDB . "`.`series` WHERE `id` = " . $this->mysqli->real_escape_string($this->Data['id']) . $this->AdvanceRestrictions . $addonQuery;
			$result = $this->mysqli->query($query);
			
			$count = $result->num_rows;
			if($count > 0)
			{
				// include review information
				include_once("review.v2.class.php");
				$Review = new Review();
				// include watchlist class
				include_once("watchlist.v2.class.php");
				$Watchlist = new Watchlist($this->Data,$this->UserArray,$this->DevArray,$this->permissionArray);
				$watchlistEntries = $Watchlist->array_displayWatchList(TRUE);
				$row = $result->fetch_assoc();
				$Reviews = $Review->array_reviewsInformation($row['id'],$this->UserArray['ID']);
				// a result was found, build the array for return.
				$results = array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => "Request Successful.");
				
				// This option will be for database objects that use Yes, no, true or false to define a boolean.
				$booleanSwitch = array('true' => "1", 'false' => "0", 'yes' => "1", 'no' => "0");
				
				foreach($row AS $key => &$value)
				{
					if($key == 'ratingLink')
					{
						$results['results']['rating'] = substr($value,0,-4);
						$results['results'][$key] = $this->ImageHost . '/ratings/' . $value;
					}
					else
					{
						if(isset($booleanSwitch[$value])){
							$results['results'][$key] = $booleanSwitch[$value];
						}
						else {
							$results['results'][$key] = $value;
						}
					}
				}
				$returneddata['results']['watchlist'] = "0";
				if(array_key_exists($row['id'],$watchlistEntries)){
					$returneddata['results']['watchlist'] = "1";
				}
				// add the seriesimage to the array
				$results['results']['image'] = $this->ImageHost . '/seriesimages/' . $row['id'] . '.jpg';
				$results['results']['image-320x280'] = $this->ImageHost . '/seriesimages/320x280/' . $row['id'] . '.jpg';
				$results['results']['total-reviews'] = $Reviews['total-reviews'];
				$results['results']['user-reviewed'] = $Reviews['user-reviewed'];
				$results['results']['reviews-average-stars'] = $Reviews['average-stars'];
				return $results;
			}
			else
			{
				return array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);
			}
		}
		else
		{
			// Nothing matched the information give, send back to them.
			return array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);
		}
	}
	
	public function array_displaySeries()
	{
		// these will be the details returned for the series, we don't want them to have the whole cake and eat it too
		$columns = "`id`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesonly`, `noteReason`, `category`, `prequelto`, `sequelto`, `hd` ";
		// render the options that we will accept, includes amount of series, alphanumeric sorting
		if (isset($this->Data['sort'])) {
			// we will have options to sort by first letter of the seriesname, 
		} else {
		}
		if (isset($this->Data['start'])) {
			if (!is_numeric($this->Data['start'])) {
				$start = "0,";
			} else {
				$start = $this->Data['start'] . ",";
			}
		} else {
			$start = "0,";
		}
		if (isset($this->Data['count'])) {
			if (!is_numeric($this->Data['count'])) {
				$count = 10;
			} else {
				$count = $this->Data['count'];
			}
		} else {
			$count = 10;
		}
	
		
		//Alphabetical limitation
		//Zigbigidorlu was here :B
		$alphalimit = "";
		if (!is_null($alpha)) {
			if ($alpha == "1") {
				$alphalimit = "AND `fullSeriesName` NOT REGEXP '^[a-zA-Z]'";
			} elseif (ctype_alpha($alpha)) {
				$alpha = substr($alpha,0,1);
				$alphalimit = "AND `fullSeriesName` LIKE '$alpha%'";
			}
		}
		
		if (isset($this->Data['filter'])) {
			$filter = " AND `category` LIKE '%" . $this->Data['filter'] . " ,%'";
		} else {
			$filter = "";
		}
		
        // allow sifting of categories.
        $categoryFilter = "";
		if (isset($this->Data['categories']) && $this->Data['categories'] != '') {
            $categories = explode(',', $this->Data['categories']);
            foreach ($categories as $category) {
                $categoryFilter .= " AND `category` LIKE '%" . trim($category, " ") . " ,%'";
            }
		}
		
		// we need to allow for randomization in a series view		
		if ($this->Data['action'] == 'random-series') {
			$orderBy = " ORDER BY RAND() ";
			$start = "0,";
		} else {
			$orderBy = " ORDER BY fullSeriesName ";
		}
        
        // Field to sort by movies only.
		if (isset($this->Data['movies'])) {
			$containsMovies = " AND `Movies` = 1";
		} else {
			$containsMovies = "";
		}
		
		// Add support for viewing the last X series added.
		if (isset($this->Data['latest'])) {
			// latest is set, we will limit them to the latest ## series by default.
			// Unless they use the timeframe flag, which will allow them to specify a time frame from the current time
			// that they wish to pull down series from.
			if (isset($this->Data['timeframe'])) {
				// They can use m, s  or h at the end, this way we can do &timeframe=15m or timeframe=60s
				$timeType = substr($this->Data['timeframe'], -1);
				$timeFrame = substr($this->Data['timeframe'], 0, -1);
				if (strtolower($timeType) == 'm') {
					// Minutes timeframe.
					$finalTime = time()-($timeFrame*60);
				} elseif (strtolower($timeType) == 'h') {
					// hours
					$finalTime = time()-($timeFrame*60*60);
				} else {
					// seconds is the default, we will not accept anything else.
					$finalTime = time()-$timeFrame;
				}
				$where .= " AND `date` >= " . $this->mysqli->real_escape_string($finalTime);
			} else {
			}
			$columns = "`id`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesonly`, `noteReason`, `category`, `prequelto`, `sequelto`, `hd` ";
			$orderBy = " ORDER BY `series`.`id` DESC";
		} else {
			$latest = "";
		}
		
		if ($this->DevArray['license'] == 0 && ($this->UserArray['Level_access'] == 0 || $this->UserArray['Level_access'] == 3)) {
			// it means the content we can show is only unlicensed.
			$this->AdvanceRestrictions .= " AND `license` = 0";
		}
		
		if ($SortNum == 1) {
			$query = "SELECT $columns FROM `series` WHERE `active` = 'yes'${categoryFilter}${containsMovies}" . $this->AdvanceRestrictions . " ORDER BY `id` ASC LIMIT 25";
		} elseif ($gsort != NULL) {
			if (strlen($gsort) > 1) {
				$catsort = $this->parseNestedArray($this->Categories, 'name', ucfirst($gsort));
				$query = "SELECT $columns FROM series WHERE active='yes'${containsMovies} AND category LIKE '% ".$catsort." %' " . $this->AdvanceRestrictions . " $alphalimit " . $orderBy . " ORDER BY fullSeriesName " . $sort . " LIMIT " . $start . " " . $count;
			} else {
				$query = "SELECT $columns FROM series WHERE active='yes'${categoryFilter}${containsMovies} AND seriesName LIKE '".$gsort."%' " . $this->AdvanceRestrictions . " $alphalimit " . $orderBy . " ORDER BY fullSeriesName ".$sort." LIMIT ".$start." ".$count;
			}
		} else {
			$query = "SELECT $columns FROM `series` WHERE active='yes'${categoryFilter}${containsMovies}${filter} " . $this->AdvanceRestrictions . " $alphalimit " . $orderBy . " ".$sort." LIMIT ".$start." ".$count;
		}
        
		// make sure we are using UTF-8 chars
		$this->mysqli->set_charset("utf8");
		
		//execute the query
		$result = $this->mysqli->query($query);
		$this->mysqli->set_charset("utf8");
		
		$returneddata = array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => "Request Successful.");
		$returneddata['total-series'] = $this->bool_totalSeriesAvailable();
		$returneddata['start'] = rtrim($start, ',');
		$returneddata['count'] = $count;
		// include review information
		include_once("review.v2.class.php");
		$Review = new Review();
		// include watchlist class
		include_once("watchlist.v2.class.php");
		$Watchlist = new Watchlist($this->Data,$this->UserArray,$this->DevArray,$this->permissionArray);
		$watchlistEntries = $Watchlist->array_displayWatchList(TRUE);
		$i = 0;
		$booleanSwitch = array('true' => "1", 'false' => "0", 'yes' => "1", 'no' => "0");
		while ($row = $result->fetch_assoc()) {
			$Reviews = $Review->array_reviewsInformation($row['id'],$this->UserArray['ID']);
			// a result was found, build the array for return.
			foreach ($row AS $key => &$value) {
				if ($key == 'ratingLink') {
					$returneddata['results'][$i]['rating'] = substr($value,0,-4);
					$returneddata['results'][$i][$key] = $this->ImageHost . '/ratings/' . $value;
				} else {
					if (isset($booleanSwitch[$value])) {
						$returneddata['results'][$i][$key] = $booleanSwitch[$value];
					} else {
						$returneddata['results'][$i][$key] = $value;
					}
				}
			}
			$returneddata['results'][$i]['watchlist'] = "0";
			if (array_key_exists($row['id'],$watchlistEntries)) {
				$returneddata['results'][$i]['watchlist'] = "1";
			}
			$returneddata['results'][$i]['image'] = $this->ImageHost . '/seriesimages/' . $row['id'] . '.jpg';
			$returneddata['results'][$i]['image-320x280'] = $this->ImageHost . '/seriesimages/320x280/' . $row['id'] . '.jpg';
			$returneddata['results'][$i]['total-reviews'] = $Reviews['total-reviews'];
			$returneddata['results'][$i]['user-reviewed'] = $Reviews['user-reviewed'];
			$returneddata['results'][$i]['reviews-average-stars'] = $Reviews['average-stars'];
			$i++;
		}
		return $returneddata;
	}
	
	private function bool_totalSeriesAvailable()
	{
		$query = "SELECT COUNT(id) as count FROM  series WHERE `active` = 'yes' " . $this->AdvanceRestrictions . "";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		return $row['count'];
	}
	
	public function array_displayCategories(){
		if(isset($this->Data['sort'])){
			$sort = $this->Data['sort'];
		}
		else {
			$sort = "ASC";
		}
		
		if(isset($this->Data['start'])){
			$start = $this->Data['start'];
		}
		else {
			$start = 0;
		}
		
		if(isset($this->Data['count'])){
			$count = $this->Data['count'];
		}
		else {
			$count = 50;
		}
		$query = "SELECT `id`, `name`, `description` FROM `categories` ORDER BY `name` {$sort} LIMIT {$start}, {$count}";
		$this->mysqli->query("SET NAMES 'utf8'");
		$result = $this->mysqli->query($query);
		
		$returneddata = array('status' => '200', 'message' => "Request Successful.", 'sort' => $sort);
		$returneddata['sort'] = $sort;
		$returneddata['count'] = $count;
		$returneddata['start'] = $start;
		$i = 0;
		while($row = $result->fetch_assoc()){
			$returneddata['results'][$i] = $row;
			$i++;
		}
		return $returneddata;
	}
	
	public function array_displayTagCloud(){
	}
}
