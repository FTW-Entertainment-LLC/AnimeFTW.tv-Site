<?php
/****************************************************************\
## FileName: search.class.php									 
## Author: Brad Riemann										 
## Usage: Search Class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Search extends Config {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	private function array_searchSite($input)
	{
		
				
		
	
	private function array_searchSeries($input)
	{
		$input = mysql_real_escape_string($input);
		if($this->UserArray[2] == 0)
		{
			// the user is an unregistered user, give them limited information
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews` FROM `series` WHERE `active` = 'yes' AND `aonly` = '0' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' OR `category` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC";
		}
		else if($this->UserArray[2] == 3)
		{
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews` FROM `series` WHERE `active` = 'yes' AND `aonly` <= '1' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' OR `category` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC";
		}
		else
		{
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews` FROM `series` WHERE `active` = 'yes' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' OR `category` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC";		
		}
		$result  = mysql_query($query) or die('Error : ' . mysql_error());
		$numrows = mysql_num_rows($result);
		$data = array();
		if($numrows > 0)
		{
			$i=0;
			while(list($id,$seriesName,$fullSeriesName,$seoname,$ratingLink,$category,$total_reviews) = mysql_fetch_array($result))
			{
				$fullSeriesName = stripslashes($fullSeriesName);
				$data['id'] = $id;
				$data['type'] = 1;
				$data['seriesname'] = $seriesName;
				$data['fullSeriesName'] = $fullSeriesName;
				$data['seoname'] = $seoname;
				$data['ratingLink'] = $ratingLink;
				$data['category'] = $category;
				$data['totalReviews'] = $total_reviews;
				$i++;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
	}

	
	private function array_searchEpisodes($input)
	{
	}
	
	private function srray_searchUsers($input)
	{
	}
	
	private function array_searchComments($input)
	{
	}
}