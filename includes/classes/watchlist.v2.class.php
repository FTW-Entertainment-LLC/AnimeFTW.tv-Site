<?php
/****************************************************************\
## FileName: watch.v2.class.php								 
## Author: Brad Riemann									 
## Usage: My WatchList scripts
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
## Modified: 11/15/2015
## Version: 1.0.0
\****************************************************************/

class watchlist extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes, $UserArray;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
	}
	
	public function connectProfile($input){
		$this->UserArray = $input;
	}
	
	public function array_displayWatchList(){
		// variables
		$columns = "`id`, `date`, `update`, `sid`, `status`, `email`, `currentep`, `tracker`, `tracker_latest`, `comment`";
		$where = "`uid` = " . $this->UserID;
		$orderby = "";
		
		// options that could potentially be set.
		if(isset($this->Data['id'])) {
			// If the ID is set, we are narrowing down on a specific entry.
			$where .= " AND `id` = '" . $this->mysqli->real_escape_string($this->Data['id']) . "'";
		}
		
		if(isset($this->Data['sid'])) {
			// If the ID is set, we are narrowing down on a specific entry.
			$where .= " AND `sid` = '" . $this->mysqli->real_escape_string($this->Data['sid']) . "'";
		}
		
		if(isset($this->Data['sort'])) {
			// we will give them the ability to sort on a variety of factors and fields.
			// for example the sort string can be: &sort=date|desc,sid|asc
			// This will cause the sort to look like: ORDER BY `date` DESC, `sid` ASC
			// we must break up the value as it will be in CSV then pipe delimited.
			$sort = explode(',',$this->Data['sort']);
			$arraycount = count($sort);
			if($arraycount > 0){
				$orderby = "ORDER BY ";
				$i=1;
				foreach($sort as $key => $value){
					// we need to break this up by pipe now to get the full command.
					$sortby = explode("|",$value);
					
					if(!isset($sortby[1]) || (strtolower($sortby[1]) != 'asc' && strtolower($sortby[1]) != 'desc')){
						// we do nothing.. they didn't fill it our right.
					}
					else {
						$orderby .= " `${sortby[0]}` ${sortby[1]}";
						if($i < $arraycount){
							$orderby .= ",";
						}
						$i++;
					}
				}
			}
		}
		else{
		}
		if(isset($this->Data['start'])) {
			if(!is_numeric($this->Data['start'])) {
				$start = "0,";
			}
			else {
				$start = $this->Data['start'] . ",";
			}
		}
		else
		{
			$start = "0,";
		}
		if(isset($this->Data['count']))
		{
			if(!is_numeric($this->Data['count'])) {
				$count = 10;
			}
			else {
				$count = $this->Data['count'];
			}
		}
		else
		{
			$count = 10;
		}
		// Form the query.
		$query = "SELECT ${columns} FROM `watchlist` WHERE ${where} ${orderby}";
		
		// make sure we are using UTF-8 chars
		$this->mysqli->set_charset("utf8");
		
		//execute the query
		$result = $this->mysqli->query($query);
		
		$returneddata = array('status' => '200', 'message' => "Request Successful.");
		$returneddata['total'] = $this->bool_totalWatchListEntries();
		$returneddata['start'] = rtrim($start, ',');
		$returneddata['count'] = $count;
		$returneddata['sort'] = $this->Data['sort'];
		$i = 0;
		
		while($row = $result->fetch_assoc()) {
			// a result was found, build the array for return.
			foreach($row AS $key => &$value) {
				$returneddata['results'][$i][$key] = $value;
			}	
			$i++;
		}
		return $returneddata;
	}
	
	public function array_addWatchListEntry(){
	}
	
	public function array_deleteWatchListEntry(){
	}
	
	private function bool_totalWatchListEntries(){
		$query = "SELECT COUNT(id) as count FROM `watchlist` WHERE `uid` = " . $this->UserID . "";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		return $row['count'];
	}
}