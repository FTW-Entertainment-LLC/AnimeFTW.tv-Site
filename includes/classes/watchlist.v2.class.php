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

    public $Data, $UserArray, $DevArray, $permissionArray, $MessageCodes, $UserArray;

    public function __construct($Data = NULL,$UserArray = NULL,$DevArray = NULL,$permissionArray = NULL)
    {
		parent::__construct();
		$this->Data = $Data;
		$this->UserArray = $UserArray;
		$this->DevArray = $DevArray;
		$this->permissionArray = $permissionArray;
    }
    
    public function connectProfile($input){
        $this->UserArray = $input;
    }
    
    public function array_displayWatchList($allEntries = FALSE){
        // variables
        $columns = "`watchlist`.`id`, `watchlist`.`date`, `watchlist`.`update`, `watchlist`.`sid`, `watchlist`.`status`, `watchlist`.`email`, `watchlist`.`currentep`, `watchlist`.`tracker`, `watchlist`.`tracker_latest`, `watchlist`.`comment`, `series`.`fullSeriesName`, `series`.`description`";
        $where = "`uid` = " . $this->UserArray['ID'];
        $orderby = "";
        
        // we want all of the user's entries.. 
        if ($allEntries == TRUE){
            $columns = "`watchlist`.`id`, `watchlist`.`sid`";
        } else {
            // options that could potentially be set.
            if (isset($this->Data['id'])) {
                // If the ID is set, we are narrowing down on a specific entry.
                $where .= " AND `watchlist`.`id` = '" . $this->mysqli->real_escape_string($this->Data['id']) . "'";
            }
            if (isset($this->Data['sid'])) {
                // If the ID is set, we are narrowing down on a specific entry.
                $where .= " AND `watchlist`.`sid` = '" . $this->mysqli->real_escape_string($this->Data['sid']) . "'";
            }
            if (isset($this->Data['sort'])) {
                // we will give them the ability to sort on a variety of factors and fields.
                // for example the sort string can be: &sort=date|desc,sid|asc
                // This will cause the sort to look like: ORDER BY `date` DESC, `sid` ASC
                // we must break up the value as it will be in CSV then pipe delimited.
                $sort = explode(',',$this->Data['sort']);
                $arraycount = count($sort);
                if ($arraycount > 0) {
                    $orderby = "ORDER BY ";
                    $i=1;
                    foreach ($sort as $key => $value) {
                        // we need to break this up by pipe now to get the full command.
                        $sortby = explode("|",$value);
                        
                        if (!isset($sortby[1]) || (strtolower($sortby[1]) != 'asc' && strtolower($sortby[1]) != 'desc')) {
                            // we do nothing.. they didn't fill it our right.
                        } else {
                            $orderby .= " `${sortby[0]}` ${sortby[1]}";
                            if($i < $arraycount){
                                $orderby .= ",";
                            }
                            $i++;
                        }
                    }
                }
            }
            if (isset($this->Data['start'])) {
                if (!is_numeric($this->Data['start'])) {
                    $start = "0,";
                } else {
                    $start = $this->Data['start'] . ",";
                }
            } else {
                $start = "0,";
            }
            if (isset($this->Data['count'])) {
                if (!is_numeric($this->Data['count'])) {
                    $count = 10;
                } else {
                    $count = $this->Data['count'];
                }
            } else {
                $count = 10;
            }
        }
        // Form the query.
        $query = "SELECT ${columns} FROM `watchlist` INNER JOIN `series` ON `series`.`id`=`watchlist`.`sid` WHERE ${where} ${orderby}";
        
        // make sure we are using UTF-8 chars
        $this->mysqli->set_charset("utf8");
        
        //execute the query
        $result = $this->mysqli->query($query);
        
        // Add options in to make the data easier to read for less detail orianted functions.
        if ($allEntries == TRUE) {
            
            $returneddata = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // We only need to show that the id for the series exists.
                    $returneddata[$row['sid']] = $row['id'];
                }
            }
        } else {
            $returneddata = array('status' => '200', 'message' => "Request Successful.");
            $returneddata['total'] = $this->bool_totalWatchListEntries();
            $returneddata['start'] = rtrim($start, ',');
            $returneddata['count'] = $count;
            if (isset($this->Data['sort'])) {
                $returneddata['sort'] = $this->Data['sort'];
            }
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                // a result was found, build the array for return.
                foreach ($row AS $key => &$value) {
                    if (isset($this->Data['id']) && $row['tracker'] == 1 && $key == 'current_episode') {
                        // we ommit the current_episode option since the tracker is in play.
                    } else {
                        if ($key == 'currentep') {
                            $returneddata['results'][$i]['current_episode'] = $value;
                        } else {
                            $returneddata['results'][$i][$key] = $value;
                        }
                    }
                }
                $returneddata['results'][$i]['image'] = $this->ImageHost . '/seriesimages/' . $row['sid'] . '.jpg';
                // If the request is for a specific My WatchList entry, we will pull down the proper layout for the various types.
                if ((isset($this->Data['id']) || isset($this->Data['sid'])) && $row['tracker'] == 1) {
                    // tracker was set, we send them
                    $returneddata['results'][$i]['tracker_information'] = $this->array_returnTrackerInformation($row['sid'],$row['tracker_latest']);
                }
                $i++;
            }
        }
        return $returneddata;
    }
    
    public function array_addWatchListEntry(){
        if (isset($this->Data['id']) && is_numeric($this->Data['id'])) {
            // the ID was supplied, moving forward.
            $query = "INSERT INTO `watchlist` (`id`, `uid`, `date`, `update`, `sid`, `status`, `email`, `currentep`, `tracker`, `tracker_latest`, `comment`) VALUES (NULL, '" . $this->UserArray['ID'] . "', '" . time() . "', '" . time() . "', '" . $this->mysqli->real_escape_string($this->Data['id']) . "', '1', '1', '0', '0', '0', '')";
            $result = $this->mysqli->query($query);
            if ($result) {
                $results = array(
                    'id' => $this->mysqli->insert_id,
                    'date' => time(),
                    'updated' => time(),
                    'sid' => $this->Data['id'],
                    'status' => '1',
                    'email' => '1',
                    'current_episode' => '0',
                    'tracker' => '0',
                    'tracker_latest' => '0',
                    'comment' => '',
                );
                return array('status' => "200", 'message' => "Request successful.", 'results' => $results);
            } else {
                return array('status' => "500", 'message' => "An issue appeared when attempting to run that request.");
            }
        } else {
            return array('status' => "501", 'message' => "There was a configuration issue with the request, ensure all requirements are met.");
        }
    }
    
    public function array_editWatchListEntry(){
        // we start off by checking to make sure the id of the WatchList entry was given, if it wasn't send them back.
        if (isset($this->Data['id']) && is_numeric($this->Data['id'])) {
            // first we will query to get all of the existing details.
            $query = "SELECT `status`, `email`, `currentep`, `tracker`, `tracker_latest`, `comment` FROM `watchlist` WHERE `id` = " . $this->mysqli->real_escape_string($this->Data['id']) . " AND `uid` = " . $this->UserArray['ID'];
            $result = $this->mysqli->query($query);
            $existingRow = $result->fetch_assoc();
            
            // we will now go through the potential options that could be supplied to the system.
            // check to see if the status changed. 
            $querySetOptions = '';
            $i = 0;
            if (isset($this->Data['status'])) {
                if ($this->Data['status'] != $existingRow['status']) {
                    // the value doesn't match, so we need to update it.
                    $querySetOptions .= "`status` = '" . $this->mysqli->real_escape_string($this->Data['status']) . "', ";
                    $i++;
                }
            }
            // check for email settings change.
            if (isset($this->Data['email'])) {
                if ($this->Data['email'] != $existingRow['email']) {
                    $querySetOptions .= "`email` = '" . $this->mysqli->real_escape_string($this->Data['email']) . "', ";
                    $i++;
                }
            }            
            // check for currentep updates
            if (isset($this->Data['current_episode'])) {
                if ($this->Data['current_episode'] != $existingRow['currentep']) {
                    $querySetOptions .= "`currentep` = '" . $this->mysqli->real_escape_string($this->Data['current_episode']) . "', ";
                    $i++;
                }
            }            
            // check for tracker options update
            if (isset($this->Data['tracker'])) {
                if ($this->Data['tracker'] != $existingRow['tracker']) {
                    $querySetOptions .= "`tracker` = '" . $this->mysqli->real_escape_string($this->Data['tracker']) . "', ";
                    $i++;
                }
            }
            // check for the tracker_latest option
            if (isset($this->Data['tracker_latest'])) {
                if ($this->Data['tracker_latest'] != $existingRow['tracker_latest']) {
                    $querySetOptions .= "`tracker_latest` = '" . $this->mysqli->real_escape_string($this->Data['tracker']) . "', ";
                    $i++;
                }
            }
            // Check if the comment was sent.
            if (isset($this->Data['comment'])) {
                if ($this->Data['comment'] != $existingRow['comment']) {
                    $querySetOptions .= "`comment` = '" . $this->mysqli->real_escape_string($this->Data['comment']) . "', ";
                    $i++;
                }
            }
            
            // check to see if the returned values are the same as the stored ones.. it saves on the responses by the sql server..
            if ($i == 0) {
                // nothing to do.. no updates were made.
                return array('status' => "200", 'message' => "Request successful. No Updates were made.");
            } else {
                $querySetOptions = rtrim(rtrim($querySetOptions), ',');
                // initiate the new query, there was an update.
                $query = "UPDATE `watchlist` SET ${querySetOptions} WHERE `id` = " . $this->mysqli->real_escape_string($this->Data['id']) . " AND `uid` = " . $this->UserArray['ID'];
                $result = $this->mysqli->query($query);
                
                if (!$result) {
                    return array('status' => "500", 'message' => "An issue appeared when attempting to run that request.");
                } else {
                    return array('status' => "200", 'message' => "Request successful.");
                }
            }
        } else {
            return array('status' => "501", 'message' => "There was a configuration issue with the request, ensure all requirements are met.");
        }
    }
    
    public function array_deleteWatchListEntry(){
        if (!isset($this->Data['id'])) {
            // The ID was not set, so there is no way for them to pass.
            return array('status' => "501", 'message' => "There was a configuration issue with the request, ensure all requirements are met.");
        } else {
            $query = "DELETE FROM `watchlist` WHERE `sid` = " . $this->mysqli->real_escape_string($this->Data['id']) . " AND `uid` = " . $this->UserArray['ID'];
            // we want to get affected rows back, so that we can safely let them know if it was deleted or not.
            if ($stmt = $this->mysqli->prepare($query)) {
                $stmt->execute();
                $count = $stmt->affected_rows; // This tells us how many rows were impacted.. ideally only one would be removed.
                if ($count > 0) {
                    return array('status' => "200", 'message' => "Request successful.");
                } else {
                    return array('status' => "404", 'message' => "No Entry was available to delete at that id.");
                }
            }
        }
    }
    
    private function bool_totalWatchListEntries(){
        $query = "SELECT COUNT(id) as count FROM `watchlist` WHERE `uid` = " . $this->UserArray['ID'] . "";
        $result = $this->mysqli->query($query);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function array_returnTrackerInformation($sid,$tracker_latest) {
        if ($tracker_latest == '1') {
            // latest episode watched.
            $query = "SELECT `episode`.`epnumber` as `numrows` FROM `episode_tracker`, `episode` WHERE `episode_tracker`.`seriesName` = " . $sid . " AND `episode_tracker`.`uid` = " . $this->UserArray['ID'] . " AND `episode`.`id`=`episode_tracker`.`eid` ORDER BY `eid` DESC LIMIT 0, 1";
        } else {
            // cumulative
            $query = "SELECT COUNT(id) as `numrows` FROM `episode_tracker` WHERE `seriesName` = " . $sid . " AND `uid` = " . $this->UserArray['ID'];
        }
        
        $result = $this->mysqli->query($query);
        $row = $result->fetch_assoc();
        
        return $row['numrows'];
    }
}