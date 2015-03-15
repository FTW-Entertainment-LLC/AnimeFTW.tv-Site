<?php
/****************************************************************\
## FileName: apiv2.class.php								 
## Author: Brad Riemann									 
## Usage: Central API management Class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
## Modified: 09/05/2013
## Version: 1.0.1
\****************************************************************/

Class EpisodeAPI Extends Config {

	var $Data, $UserID, $DevArray;

	// class constructor method
	public function __construct($Data,$UserID,$DevArray)
	{
		// Grab everything we need.
		$this->Data 	= $Data;
		$this->UserID 	= $UserID;
		$this->DevArray = $DevArray;
		$this->MessageCodes = array(
			'410' => array(
				'Status' => '410',
				'Message' => 'No Series was Given, therefore no episodes are available.',
				'Explanation' => 'When requesting episodes, if no seo is given, then an error will be returned.'
			)
		);
		
		// import the functions from the parent class.
		parent::__construct();
		
		print_r($this->Data);
	}
	
	public function displayEpisodes()
	{
		if(!isset($this->Data["title"]))
		{
			$this->reportResult(410)
		}
		else
		{
			$
		}
	}
	
	public function displaySingleEpisode()
	{
	}
	
	public function displayMovies()
	{
	}
	
	public function displayLatestEpisodes()
	{
	}
	
	// Part of the API includes error reporting to the developers, this will need to output formats that 
	// are known by the Devs.
	private function reportResult($ResultCode = 401,$Message = NULL)
	{
		if($Message == NULL)
		{
			// Message is null, which means we can take it from the array
			$Message = $this->MessageCodes["Result Codes"][$ResultCode]["Message"];
		}
		else
		{
			// Message was given, so we need to use THAT.
			$Message = $Message;
		}
		$Result = array('status' => $this->MessageCodes["Result Codes"][$ResultCode]["Status"], 'message' => $Message); // we put the error and the message together for the output..
		return $Result;
	}	
	
}