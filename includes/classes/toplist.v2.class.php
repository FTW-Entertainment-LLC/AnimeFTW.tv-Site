<?php
/****************************************************************\
## FileName: toplist.v2.class.php                                 
## Author: Brad Riemann                                     
## Usage: TopList scripts
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
## Modified: 09/19/2014
## Version: 1.0.0
\****************************************************************/

class toplist extends Config {

    public $Data, $UserArray, $DevArray, $permissionArray, $MessageCodes, $UserArray;
    var $Categories = array(); // added 10/10/2014 by robotman321

    public function __construct($Data = NULL,$UserArray = NULL,$DevArray = NULL,$permissionArray = NULL)
    {
        parent::__construct();
        $this->Data = $Data;
        if ($UserArray != NULL) {
            $this->UserArray = $UserArray;
        }
        $this->DevArray = $DevArray;
        $this->permissionArray = $permissionArray;
        $this->array_buildAPICodes(); // establish the status codes to be returned to the api.
    }
    
    public function connectProfile($input)
    {
        $this->UserArray = $input;
    }
    
    // Parses through script variables sent via the scripts.php file
    
    public function scriptsFunctions()
    {
        if(isset($_GET['action']) && $_GET['action'] == 'record')
        {
            $this->recordEpisodeView();
        }
        else
        {
            echo 'That was not the function you were looking for..';
        }
    }
    
    public function array_showTopAnime($count = NULL,$start = NULL)
    {
        // build the cateogory listing.
        $this->buildCategories();
        
        $returnarray = array();
        if(isset($this->Data['count'])){
            $count = $this->Data['count'];
        } else {
            if($count == NULL && isset($_GET['count'])) {
                $count = $_GET['count'];
            } else if($count != NULL && !isset($_GET['count'])) {
                $count = $count;
            } else {
                $count = 25;
            }
        }
        if(isset($this->Data['start'])){
            $start = $this->Data['start'];
        } else {
            if($start == NULL && isset($_GET['start'])) {
                $start = $_GET['count'];
            } else if($start != NULL && !isset($_GET['start'])) {
                $start = $start;
            } else {
                $start = 0;
            }
        }
        
        if($this->DevArray['license'] == 0 && ($this->UserArray['Level_access'] == 0 || $this->UserArray['Level_access'] == 3)) {
            // it means the content we can show is only unlicensed.
            $licensed = " AND `license` = 0";
        }        
        $query = "SELECT `seriesId`, `lastPosition`, `currentPosition`, `fullSeriesName`, `description`, `category`, `ratingLink` FROM `site_topseries` INNER JOIN `series` ON `series`.`id`=`site_topseries`.`seriesId`" . $licensed . " ORDER BY `currentPosition` ASC LIMIT " . $this->mysqli->real_escape_string($start) . ", " . $this->mysqli->real_escape_string($count);
        $result = $this->mysqli->query($query);
        
        if($result)
        {
            $returnarray['status'] = $this->MessageCodes["Result Codes"]["200"]["Status"];
            $returnarray['message'] = 'Request Successful.';
            $returnarray['count'] = $count;
            $i=0;
            include_once("rating.v2.class.php");
            $Rating = new Rating();
            while($row = $result->fetch_assoc())
            {
                $returnarray['results'][$i]['id'] = $row['seriesId'];
                $returnarray['results'][$i]['fullSeriesName'] = stripslashes($row['fullSeriesName']);
                $returnarray['results'][$i]['last-position'] = $row['lastPosition'];
                $returnarray['results'][$i]['current-position'] = $row['currentPosition'];
                $returnarray['results'][$i]['reviews-average-stars'] = $Rating->bool_averageSeriesRating($row['seriesId']);
                $returnarray['results'][$i]['rating'] = $Rating->bool_averageSeriesRating($row['seriesId']);
                $returnarray['results'][$i]['ratingLink'] = $this->ImageHost . '/ratings/' . $row['ratingLink'];
                $returnarray['results'][$i]['description'] = stripslashes($row['description']);
                $returnarray['results'][$i]['image'] = $this->ImageHost . '/seriesimages/' . $row['seriesId'] . '.jpg';
                $returnarray['results'][$i]['category'] = $row['category'];
                // add the ratings here.
                $i++;
            }
        }
        else
        {
            $returnarray['status'] = $this->MessageCodes["Result Codes"]["400"]["Status"];
            $returnarray['message'] = $this->MessageCodes["Result Codes"]["400"]["Message"];
        }
        return $returnarray;
    }
    
    // Will take an episode id and record it in the toplist records.
    private function recordEpisodeView()
    {
        // Check if the epid is set.. if it is not throw the book at em!
        if(!isset($_GET['epid']) && !is_numeric($_GET['epid']))
        {
            echo 'Error: The episode ID was invalid or wrong. Please try again.';
        }
        else
        {            
            $query = "SELECT `sid`, `epnumber` FROM `episode` WHERE `id` = '" . $this->mysqli->real_escape_string($_GET['epid']) . "'";
            $result = $this->mysqli->query($query);
            $row = $result->fetch_assoc();
    
            //Get the Date for today, all 24 hours
            $currentDay = date('d-m-Y',time());
            $midnight = strtotime($currentDay);
            $elevenfiftynine = $midnight+86399;
        
            //check for any rows that were done today...
            // we will want to switch out the seriesid and the epnumber for epid later.. just makes it easier..
            $query  = $this->mysqli->query("SELECT `id` FROM `episodestats` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `epSeriesId` = '" . $row['sid'] . "' AND `epNumber` = '" . $row['epnumber'] . "' AND `date` >= '" . $midnight . "'");
            $count = mysqli_num_rows($query);
        
            if($count == 0)
            {
                $query = "INSERT INTO `episodestats` (`eid`, `epSeriesId`, `ip`, `date`, `epnumber`)  VALUES ('" . $this->mysqli->real_escape_string($_GET['epid']) . "', '" . $row['sid'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '".time()."', '" . $row['epnumber'] . "')";
                $result = $this->mysqli->query($query);
            }
        }
    }
}
