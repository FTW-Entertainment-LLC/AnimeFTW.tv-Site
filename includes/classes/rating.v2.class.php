<?php
/****************************************************************\
## FileName: rating.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Rating Class and Functions
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
## Updated: 02/21/2014 by Robotman321
## Version: 1.0
\****************************************************************/

class Rating extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
	}
	
	public function array_ratingsInformation($id, $UserID=0,$type=0)
	{
		// The object of this is to give all of the rating information about a specific item.
		// we need to figure out what the rating type can be..
		if($type == 1)
		{
			// this is for comment ratings
			$rating_id = "c$id";
		}
		else
		{
			// we will default to video ratings first..
			$rating_id = "v$id";
		}
		$query = "SELECT `rating_num`, `IP` FROM `ratings` WHERE `rating_id` = '$rating_id'";
		$result = $this->mysqli->query($query);
		$count = mysqli_num_rows($result);
		// some vars
		$rated = -1; // we set to 0 by default, assuming no one has rated anything before.
		$returnarray = array(); // the array we will be returning.
		$ratingstotal = 0; // we will add each rating up then average it for our average rating.
		$i = 1;
		if($count > 0)
		{
			while($row = $result->fetch_assoc())
			{
				if($type == 0 && $UserID == $row['IP'])
				{
					// This person rated this episode.
					$rated = $row['rating_num'];
				}
				$returnarray['ratings'][$row['IP']] = $row['rating_num'];
				$ratingstotal = $ratingstotal+$row['rating_num'];
				// if not, then they have not rated the episode.
				$i++;
			}
			// we give back a rounded number so they can see an average rating to the tenth
			$returnarray['average-rating'] = round($ratingstotal/$i,1);
		}
		else
		{
			$returnarray['average-rating'] = 0;
		}
		// the user rated on this episode already.
		$returnarray['user-rated'] = $rated;
		return $returnarray;
	}
	
	public function bool_submitEpisodeRating()
	{
		if(!isset($this->Data['id']) || !isset($this->Data['star']))
		{
			// there was an issue with the data..
			return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["06-401"]["Message"]);
		}
		else
		{
			// the data is valid.. let's add it to the site.
			$query = "INSERT INTO `ratings` (`id`, `rating_id`, `rating_num`, `IP`, `v1`) VALUES (NULL, 'v" . $this->Data['id'] . "', '" . $this->Data['star'] . "', '" . $this->UserID . "', NULL)";
			$result = $this->mysqli->query($query);
			if(!$result)
			{
				return array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);
			}
			else
			{
				return array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["200"]["Message"]);
			}
		}
	}
	
	public function bool_averageSeriesRating($id)
	{
		$query = "SELECT `stars` FROM `reviews` WHERE `sid` = " . $this->mysqli->real_escape_string($id) . " AND `approved` = 1";
		$result = $this->mysqli->query($query);
		$count = mysqli_num_rows($result);
		if($count > 0)
		{
			$a = 0;
			$row = $result->fetch_assoc();
			foreach($row as $value)
			{
				$a = $a+$value;
			}
			$average = $a/$count; // this is the average ratings.
			return $average;
		}
		else
		{
			return 0;
		}
	}
}
