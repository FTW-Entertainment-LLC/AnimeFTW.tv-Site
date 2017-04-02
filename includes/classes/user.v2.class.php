<?php
/****************************************************************\
## FileName: user.v2.class.php                                     
## Author: Brad Riemann                                         
## Usage: User Class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class User extends Config {

    public $Data, $DevArray, $MessageCodes, $UserArray, $permissionArray;
    
    public function __construct($Data = NULL,$UserArray = NULL,$DevArray = NULL,$permissionArray = NULL,$DirectUserArray = NULL)
    {
        parent::__construct(TRUE);
        $this->Data = $Data;
        $this->UserArray = $UserArray;
        $this->DevArray = $DevArray;
        $this->permissionArray = $permissionArray;
        $this->DevArray = $DevArray;
        $this->array_buildAPICodes(); // establish the status codes to be returned to the api.
    }
    
    public function array_dispayUserProfile(){
        if(isset($this->Data['lite'])){
            // This indicates that we are building the lite version of the profile, with specific options only.
            $query = "SELECT `ID`, `Username`, `display_name`, `Level_access`, `lastActivity`, `Email`, `views`, `firstName`, `lastName`, `gender`, `ageDate`, `ageYear`, `ageMonth`, `country`, `avatarExtension`, `avatarActivate`, `personalMsg`, `advanceActive`, `timeZone` FROM `users` WHERE `ID` = " . $this->UserArray['ID'];
        }
        else {
            // for everything else we give the full output.
            $query = "SELECT `ID`, `Username`, `display_name`, `Level_access`, `lastActivity`, `Email`, `views`, `firstName`, `lastName`, `gender`, `ageDate`, `ageYear`, `ageMonth`, `country`, `avatarExtension`, `avatarActivate`, `personalMsg`, `advanceActive`, `timeZone`, (SELECT COUNT(id) FROM `messages` WHERE `rid` = " . $this->UserArray['ID'] . " AND `viewed` = 1) as `newmessages` FROM `users` WHERE `ID` = " . $this->UserArray['ID'];
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
        # we must first check to ensure that all data is correctly inserted into the request.
        # email, firstname, lastname, gender, birthday, country, personalmsg, timezone
        if(!isset($this->Data['email']) || !isset($this->Data['firstname']) || !isset($this->Data['lastname']) || !isset($this->Data['gender']) || !isset($this->Data['birthday']) || !isset($this->Data['country']) || !isset($this->Data['personalmsg']) || !isset($this->Data['timezone'])) {
            # if one of them are not set then we need to alert them to this injustice.
            return array('status' => '404', 'message' => "Data was missing, please ensure all profile fields are submitted.");
        }
        else {
            $query = "UPDATE `users` SET `Email` = '" . $this->mysqli->real_escape_string($this->Data['email']) . "', `firstName` = '" . $this->mysqli->real_escape_string($this->Data['firstname']) . "', `lastName` = '" . $this->mysqli->real_escape_string($this->Data['lastname']) . "', `gender` = '" . $this->mysqli->real_escape_string($this->Data['gender']) . "', `ageDate` = '" . $this->mysqli->real_escape_string($this->Data['']) . "', `ageYear` = '" . $this->mysqli->real_escape_string($this->Data['']) . "', `ageMonth` = '" . $this->mysqli->real_escape_string($this->Data['']) . "', `country` = '" . $this->mysqli->real_escape_string($this->Data['country']) . "', `personalMsg` = '" . $this->mysqli->real_escape_string($this->Data['personalmsg']) . "', `timeZone` = '" . $this->mysqli->real_escape_string($this->Data['timezone']) . "' WHERE `ID` = " . $this->UserArray['ID'];
            $result = $this->mysqli->query($query);
            if(!$result){
                return array('status' => '500', 'message' => "There was an unknown error recording the User details.");
            }
            else {
                return array('status' => '200', 'message' => "Profile Update completed successfully.");
            }
        }
    }
}