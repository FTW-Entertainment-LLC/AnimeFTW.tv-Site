<?php
/****************************************************************\
## FileName: anime.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Builds all of the series and episode information for the anime
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Anime extends Config {

	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	# @usage: will build an array of data formed around the Series Table
	# @output: Array
	*/
	public function array_constructSeriesListing($Type = 0,$Options = NULL)
	{
		// We will use different `Types` of queries, from one array queries to the full database.
		
		// Type 0 is for all series, just exported out.
		if($Type == 0)
		{
			if($Options)
			{
				// if the options are set here, then we are going to sift through differently, it will be plugged in to the statement remotely..
				$QueryAddon = "$Options";
			}
			else
			{
				$QueryAddon = "";
			}
			$query = "SELECT * FROM `" . $this->MainDB . "`.`series`" . $QueryAddon;
			$results = $this->mysqli->query($query);
			
			$SeriesArray = array();
			$i = 0;
			while($row = $results->fetch_assoc())
			{
				$SeriesArray[] = $row;
				$i++;
			}
		}
		else
		{
		}
		return $SeriesArray;
	]
	
	public function __destruct()
	{
	}
}