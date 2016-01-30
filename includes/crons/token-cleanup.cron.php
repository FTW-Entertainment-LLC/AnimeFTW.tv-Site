#!/usr/bin/php
<?php
/****************************************************************\
## FileName: token-cleanup.cron.php								 
## Author: Brad Riemann								 
## Usage: Primary usage is for cleaning up the API tokens after they expire
## Copywrite 2013 Techpro, Inc, All Rights Reserved
## Version: 1.0.0
## Updated: 10-31-2013 @ 00:00:00
\****************************************************************/

include("/home/mainaftw/public_html/includes/classes/config.v2.class.php");

class api_cleanup extends Config {

	var $Tokens, $StickyTokens, $TokenTable, $CronID;

	public function __construct()
	{
		// import the functions from the parent class.
		parent::__construct();
		
		// variable declaration
		$this->Tokens 			= 3600; 		// Basic tokens, older than X seconds ago, should be removed from the system
		$this->StickyTokens 	= 2592000; 		// Sticky tokens, should last a week before they are removed, this has the potential to be infinite as each request does an update.
		$this->TokenTable 		= 'developers_api_sessions'; 	// API tokens Table
		$this->CronID 			= 8;
		
		// essentially initialize the script.
		$this->processTokens();
	}
	
	private function processTokens()
	{
		// Select all of the tokens that are invalid now, we will need to go through everything later.
		$query = "SELECT `id` FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "` WHERE (`date` < '" . (time()-$this->Tokens) . "' AND `sticky` = 0) OR (`date` < '" . (time()-$this->StickyTokens) . "' AND `sticky` = 1)";
		$result = $this->mysqli->query($query);
		
		if(!$result)
		{
			echo 'Nothing to see here, nothing to do.';
			exit;
		}
		
		while($row = $result->fetch_assoc())
		{			
			// Query removes the API Token making the user log back in to their session.
			$TokenResults 	= $this->mysqli->query("DELETE FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "` WHERE `id` = " . $row['id']); 
		}
		$this->mysqli->query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
	}
}

$cleanup = new api_cleanup(); //setup the class!