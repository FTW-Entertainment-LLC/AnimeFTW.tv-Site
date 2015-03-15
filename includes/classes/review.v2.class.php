<?php
/****************************************************************\
## FileName: review.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Review Class and Functions
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
## Updated: 02/21/2014 by Robotman321
## Version: 1.0
\****************************************************************/

class Review extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
	}
	
	public function bool_totalReviews($id)
	{
		$query = "SELECT COUNT(id) as numrows FROM `reviews` WHERE `sid` = " . $this->mysqli->real_escape_string($id) . " AND `approved` = 1";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		return $row['numrows'];
	}
	
	public function array_reviewsInformation($id,$UserID=0)
	{
		$query = "SELECT `id`, `uid`, `date`, `review`, `stars`, `approved` FROM `reviews` WHERE `sid` = " . $this->mysqli->real_escape_string($id);
		$result = $this->mysqli->query($query);
		$count = mysqli_num_rows($result);
		// some vars
		$reviewed = -1; // we set to 0 by default, assuming no one has reviewed anything before.
		$returnarray = array(); // the array we will be returning.
		$ratingstotal = 0; // we will add each review rating up then average it for our average rating.
		$i = 1;
		if($count > 0)
		{
			while($row = $result->fetch_assoc())
			{
				if($UserID == $row['uid'])
				{
					// This person rated this episode.
					$reviewed = $row['stars'];
				}
				$returnarray['reviews'][] = $row;
				$ratingstotal = $ratingstotal+$row['stars'];
				$i++;
			}
			// we give back a rounded number so they can see an average rating to the tenth
			$returnarray['average-stars'] = round($ratingstotal/$i,1);
			$returnarray['total-reviews'] = $count;
		}
		else
		{
			$returnarray['average-stars'] = 0;
			$returnarray['total-reviews'] = 0;
		}
		// the user rated on this episode already.
		$returnarray['user-reviewed'] = $reviewed;
		return $returnarray;
	}
	
	public function array_displayReviews()
	{
	}
}