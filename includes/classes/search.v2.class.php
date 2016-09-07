<?php
/****************************************************************\
## FileName: search.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Search Class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
## Modified: 09/11/2015
## Version: 1.0.0
\****************************************************************/

class Search extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes;
	var $Categories = array(); // added 10/10/2014 by robotman321

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	public function array_siteSearch($input = NULL,$count = NULL)
	{
		// build the cateogory listing.
		$this->buildCategories();
		
		if($input == NULL){
			if(!isset($this->Data['for'])){
				// this is not set, so we cannot let them go through.., so we throw an error and exit the script.
				return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => 'Error, missing the for in the search string, please try again.');
				exit;
			}
			else {
				$input = $this->Data['for'];
			}
		}
		
		if(isset($this->Data['start']))
		{
			$start = $this->Data['start'];
		}
		else
		{
			$start = "0";
		}
		if(isset($this->Data['count']))
		{
			$count = $this->Data['count'];
		}
		else
		{
			$count = 10;
		}
        
        // The windows dev is a derp so we must override some settings.
        if ($_SERVER['HTTP_USER_AGENT'] == 'NativeHost') {
            $count = 100;
            $start = 0;
        }
		$input = $this->mysqli->real_escape_string($input);
		if($this->AccessLevel == 0)
		{
			// the user is an unregistered user, give them limited information
            $query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category` FROM `series` WHERE `active` = 'yes' AND `aonly` = '0' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC LIMIT ${start}, ${count}";
		}
		else if($this->AccessLevel== 3)
		{
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category` FROM `series` WHERE `active` = 'yes' AND `aonly` <= '1' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC LIMIT ${start}, ${count}";
		}
		else
		{
            $query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category` FROM `series` WHERE `active` = 'yes' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC LIMIT ${start}, ${count}";		
		}
        
		$result  = $this->mysqli->query($query);
		$numrows = $result->num_rows;
		if($numrows > 0)
		{
			$results = array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => 'Success, results displayed.');			
			$results['start'] = $start;
			$results['count'] = $count;
			$results['numrows'] = $numrows;
			$i = 0;
			while($row = $result->fetch_assoc())
			{
				$fullSeriesName = stripslashes($fullSeriesName);
				$results['results'][$i]['id'] = $row['id'];
				$results['results'][$i]['type'] = 1;
				$results['results'][$i]['seriesname'] = $row['seriesName'];
				$results['results'][$i]['fullSeriesName'] = $row['fullSeriesName'];
				$results['results'][$i]['seoname'] = $row['seoname'];
				$results['results'][$i]['ratingLink'] = $row['ratingLink'];
				$results['results'][$i]['category'] = $row['category'];
				$i++;
			}
			return $results;
		}
		else
		{
			$results = array('status' => $this->MessageCodes["Result Codes"]["402"]["Status"], 'message' => 'No series found.');
			return $results;
		}
	}
}