<?php
/****************************************************************\
## FileName: user.v2.class.php									 
## Author: Brad Riemann										 
## Usage: User Class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class User extends Config {

	public $Data, $UserID, $DevArray, $MessageCodes, $UserArray;
	
	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL,$DirectUserArray = NULL)
	{
		parent::__construct(TRUE);
		$this->Data = $Data;
		// check if the data is null, we will override settings that would normally be reserved for the API.
		if($UserID == NULL)
		{
			$this->UserID = $this->UserArray['ID'];
		}
		else
		{
			$this->UserID = $UserID;
		}
		$this->DevArray = $DevArray;
		// again, for non-api driven model, we want to use the same information, per-say, so let's override this.
		if($AccessLevel == NULL)
		{
			$this->AccessLevel = $this->UserArray['Level_access'];
		}
		else
		{	
			$this->AccessLevel = $AccessLevel;
		}
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
	}
	
	public function array_dispayUserProfile(){
		if(isset($this->Data['lite'])){
			// This indicates that we are building the lite version of the profile, with specific options only.
			$query = "SELECT `ID`, `Username`, `display_name`, `Level_access`, `lastActivity`, `Email`, `views`, `firstName`, `lastName`, `gender`, `ageDate`, `ageYear`, `ageMonth`, `country`, `avatarExtension`, `avatarActivate`, `personalMsg`, `advanceActive`, `timeZone` FROM `users` WHERE `ID` = " . $this->UserID;
		}
		else {
			// for everything else we give the full output.
			$query = "SELECT `ID`, `Username`, `display_name`, `Level_access`, `lastActivity`, `Email`, `views`, `firstName`, `lastName`, `gender`, `ageDate`, `ageYear`, `ageMonth`, `country`, `avatarExtension`, `avatarActivate`, `personalMsg`, `advanceActive`, `timeZone`, (SELECT COUNT(id) FROM `messages` WHERE `rid` = " . $this->UserID . " AND `viewed` = 1) as `newmessages` FROM `users` WHERE `ID` = " . $this->UserID;
		}	
		// grab the results.
		$result = $this->mysqli->query($query);
		if(!$result){
			return array('status' => '500', 'message' => "There was an unknown error generating the User details.");
		}
		else {
			// a result was found, build the array for return.
			$returneddata = array('status' => '200', 'message' => "Request Successful.");
			$row = $result->fetch_assoc();
			foreach($row AS $key => &$value){
				$record = TRUE;
				if($key == "ageDate" || $key == "ageYear" || $key == "ageMonth"){
					// we need to make this a linux timestamp.
					if($key == "ageDate"){
						$key = 'birthday';
						$value = $row['ageYear'] . "-" . $row['ageMonth'] . "-" . $row['ageDate'];
					}
					else {
						$record = FALSE;
					}
				}
				if($key == 'Username' || $key == 'display_name'){
					if($key == 'Username'){
						if($row['display_name'] != NULL && $row['Level_access'] != 3){
							// if the display name is not null, then we use that. (as long as they are not basic menbers.
							$value = $row['display_name'];
						}
						else {
							$value = $row['Username'];
						}
						$key = 'username';
					}
					else {
						$record = FALSE;
					}
				}
				if($key == 'avatarExtension' || $key == 'avatarActivate'){
					if($key == 'avatarExtension'){
						$key = 'avatar';
						$value = $this->ImageHost . '/avatars/user' . $row['ID'] . '.' . $row['avatarExtension'];
					}
					else {
						$record = FALSE;
					}
				}
				if($key == 'advanceActive'){
					if($row['Level_access'] == 7){
						$key = 'advancemember';
						$value = '1';
					}
					else {
						$key = 'advancemember';
						$value = '0';
					}
				}
				if($key == 'Level_access'){
					// we dont give up access levels..
					$record = FALSE;
				}
				if($record == TRUE){
					$returneddata['results'][strtolower($key)] = $value;
				}
			}
			return $returneddata;
		}
	}
	
	public function array_editUserProfile(){
	}
}