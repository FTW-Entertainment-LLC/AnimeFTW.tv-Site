<?php
/****************************************************************\
## FileName: series.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Series Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Series extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes;
	private $AdvanceRestrictions;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
		if($this->AccessLevel == 0 || $this->AccessLevel == 3)
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
			// We consider this a valid single series, an ID needs to be supplied, and nothing else to ensure system level continuity.
			$this->mysqli->query("SET NAMES 'utf8'");
			$query = "SELECT `id`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesonly`, `noteReason`, `category`, `prequelto`, `sequelto`, `hd` FROM `" . $this->MainDB . "`.`series` WHERE `id` = " . $this->mysqli->real_escape_string($this->Data['id']) . $this->AdvanceRestrictions;
			$result = $this->mysqli->query($query);
			
			$count = $result->num_rows;
			if($count > 0)
			{
				// include review information
				include_once("review.v2.class.php");
				$Review = new Review();
				$row = $result->fetch_assoc();
				$Reviews = $Review->array_reviewsInformation($row['id'],$this->UserID);
				// a result was found, build the array for return.
				$results = array('status' => $this->MessageCodes["Result Codes"]["02-200"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["02-200"]["Message"]);
				
				foreach($row AS $key => &$value)
				{
					if($key == 'ratingLink')
					{
						$results['results']['rating'] = substr($value,0,-4);
						$results['results'][$key] = $this->ImageHost . '/ratings/' . $value;
					}
					else
					{
						$results['results'][$key] = $value;
					}
				}
				//$results[] = $row;
				// add the seriesimage to the array
				$results['image'] = $this->ImageHost . '/seriesimages/' . $row['id'] . '.jpg';
				$results['total-reviews'] = $Reviews['total-reviews'];
				$results['user-reviewed'] = $Reviews['user-reviewed'];
				$results['reviews-average-stars'] = $Reviews['average-stars'];
				return $results;
			}
			else
			{
				return array('status' => $this->MessageCodes["Result Codes"]["02-400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["02-400"]["Message"]);
			}
		}
		else
		{
			// Nothing matched the information give, send back to them.
			return array('status' => $this->MessageCodes["Result Codes"]["02-400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["02-400"]["Message"]);
		}
	}
	
	public function array_displaySeries()
	{
		// these will be the details returned for the series, we don't want them to have the whole cake and eat it too
		$columns = "`id`, `fullSeriesName`, `romaji`, `kanji`, `synonym`, `description`, `ratingLink`, `stillRelease`, `Movies`, `moviesonly`, `noteReason`, `category`, `prequelto`, `sequelto`, `hd` ";
		// render the options that we will accept, includes amount of series, alphanumeric sorting
		if(isset($this->Data['sort']))
		{
			// we will have options to sort by first letter of the seriesname, 
		}
		else
		{
		}
		if(isset($this->Data['start']))
		{
			$start = $this->Data['start'] . ",";
		}
		else
		{
			$start = "0,";
		}
		if(isset($this->Data['count']))
		{
			$count = $this->Data['count'];
		}
		else
		{
			$count = 10;
		}
	
		
		//Alphabetical limitation
		//Zigbigidorlu was here :B
		$alphalimit = "";
		if(!is_null($alpha))
		{
			if($alpha == "1")
			{
				$alphalimit = "AND `fullSeriesName` NOT REGEXP '^[a-zA-Z]'";
			}
			elseif(ctype_alpha($alpha))
			{
				$alpha = substr($alpha,0,1);
				$alphalimit = "AND `fullSeriesName` LIKE '$alpha%'";
			}
		}
		
		// we need to allow for randomization in a series view
		
		if($this->Data['action'] == 'random-series')
		{
			$orderby = " ORDER BY RAND() ";
			$start = "";
		}
		else
		{
			$orderby = " ORDER BY fullSeriesName ";
		}
		
		if($SortNum == 1)
		{
			$query = "SELECT $columns FROM `series` WHERE `active` = 'yes' $aonly ORDER BY `id` ASC LIMIT 25";
		}
		else if($gsort != NULL)
		{
			if(strlen($gsort) > 1)
			{
				$catsort = $this->parseNestedArray($this->Categories, 'name', ucfirst($gsort));
				$query = "SELECT $columns FROM series WHERE active='yes' AND category LIKE '% ".$catsort." %' " . $this->AdvanceRestrictions . " $alphalimit ORDER BY fullSeriesName " . $sort . " LIMIT " . $start . " " . $count;
			}
			else 
			{
				$query = "SELECT $columns FROM series WHERE active='yes' AND seriesName LIKE '".$gsort."%' " . $this->AdvanceRestrictions . " $alphalimit ORDER BY fullSeriesName ".$sort." LIMIT ".$start." ".$count;
			}
		}
		else 
		{
			$query = "SELECT $columns FROM series WHERE active='yes' " . $this->AdvanceRestrictions . " $alphalimit " . $orderby . " ".$sort." LIMIT ".$start." ".$count;
		}
		// make sure we are using UTF-8 chars
		$this->mysqli->set_charset("utf8");
		
		//execute the query
		$result = $this->mysqli->query($query);
		$this->mysqli->set_charset("utf8");
		
		$returneddata = array('status' => $this->MessageCodes["Result Codes"]["02-200"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["02-200"]["Message"]);
		$returneddata['total-series'] = $this->bool_totalSeriesAvailable();
		$returneddata['start'] = $start;
		$returneddata['count'] = $count;
		// include review information
		include_once("review.v2.class.php");
		$Review = new Review();
		$i = 0;
		while($row = $result->fetch_assoc())
		{
			$Reviews = $Review->array_reviewsInformation($row['id'],$this->UserID);
			// a result was found, build the array for return.
			foreach($row AS $key => &$value)
			{
				if($key == 'ratingLink')
				{
					$returneddata['results'][$i]['rating'] = substr($value,0,-4);
					$returneddata['results'][$i][$key] = $this->ImageHost . '/ratings/' . $value;
				}
				else
				{
					$returneddata['results'][$i][$key] = $value;
				}
			}
			$returneddata['results'][$i]['image'] = $this->ImageHost . '/seriesimages/' . $row['id'] . '.jpg';
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
}
