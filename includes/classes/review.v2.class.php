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
            $returnarray['average-stars'] = round($ratingstotal/$count,1);
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
        //vars
        $orderby = " `date` DESC";
        $where = " `approved` = 1";
        
        // we set the count of how many comments to display, default is 20
        if(isset($this->Data['count']))
        {
            $count = $this->Data['count'];
        }
        else
        {
            $count = 20;
        }
        // get the current page, we'll need to multiply it by the count.
        if(isset($this->Data['page']))
        {
            $page = $this->Data['page']*$count;
        }
        else
        {
            $page = 0;
        }
        // if the series id is set, we will give them the reviews for that series.
        if(isset($this->Data['id'])){
            $where .= " AND `sid` = " . $this->mysqli->real_escape_string($this->Data['id']);
        }
        else {
            // no sid, means we just show them everything..
        }
        
        $query = "SELECT `id`, `sid`, `uid`, `date`, `review`, `stars` FROM `reviews` WHERE $where ORDER BY $orderby LIMIT $page, $count";
        $result = $this->mysqli->query($query);
        $count = mysqli_num_rows($result);
        $returnarray = array();
        $returnarray['status'] = "200";
        if($count > 0)
        {
            $i = 0;
            while($row = $result->fetch_assoc())
            {
                $returnarray['results'][$i]['id'] = $row['id'];
                $returnarray['results'][$i]['review'] = stripslashes($row['review']);
                $returnarray['results'][$i]['sid'] = $row['sid'];
                $returnarray['results'][$i]['date'] = $row['date'];
                $returnarray['results'][$i]['user'] = $this->string_fancyUsername($row['uid'],NULL,NULL,NULL,NULL,NULL,1);
                $returnarray['results'][$i]['stars'] = $row['stars'];
                $i++;
            }
        }
        else
        {
            $returnarray = array('status' => '404', 'message' => "No Reviews were found");
        }
        return $returnarray;
    }
    
    # array_addReview - Add a review to a series.
    public function array_addReview() {
        // first, ensure that the id is set, we need this as the series id.
        if(isset($this->Data['id']) && isset($this->Data['review']) && (isset($this->Data['stars']) && is_numeric($this->Data['stars']))) {
            // id is set, now we check to see if they have made a review before or not.
            $query = "SELECT COUNT(*) AS `numrows`, `fullSeriesName` FROM `reviews` INNER JOIN `series` ON `series`.`id`='" . $this->mysqli->real_escape_string($this->Data['id']) . "' WHERE `sid` = '" . $this->mysqli->real_escape_string($this->Data['id']) . "' AND uid = " . $this->UserID;
            $result = $this->mysqli->query($query);
            $row = $result->fetch_assoc();
            if($row['fullSeriesName'] == NULL) {
                // if the series really doesnt exist.. thats the start of a problem.
                return array('status' => '400', 'message' => "Required data is missing or incorrect, in the request. Please try again.");
            } else {
                if($row['numrows'] > 0) {
                    // There is a review already for this series, from this user.
                    return array('status' => '409', 'message' => "There is already a review from this user, for this series.");
                } else {
                    // No review exists, let's go through adding one.
                    $query = "INSERT INTO `reviews` (`id`, `sid`, `uid`, `date`, `review`, `stars`) VALUES (NULL, '" . $this->mysqli->real_escape_string($this->Data['id']) . "', '" . $this->mysqli->real_escape_string($this->UserID) . "', '" . time() . "', '" . $this->mysqli->real_escape_string($this->Data['review']) . "', '" . $this->mysqli->real_escape_string($this->Data['stars']) . "');";
                    $result = $this->mysqli->query($query);
                    
                    if(!$result) {
                        $SubmittedData = 'Data: ';
                        foreach($this->Data as $key => $value){
                            if($key == 'devkey') {
                                // do nothing..
                            } else {
                                $SubmittedData .= "${key} => ${value},";
                            }
                        }
                        $slackData = "ERROR: A review was not added, the data is as follows. " . $SubmittedData;
                        $slack = $this->postToSlack($slackData);
                        return array('status' => '500', 'message' => "An error processing the request was logged, please try again.");
                    } else {
                        $slackData = "*New Review Posted to " . stripslashes($row['fullSeriesName']) . "*: \n ```" . $this->Data['review'] . "``` <https://www.animeftw.tv/manage/#reviews>";
                        $slack = $this->postToSlack($slackData);
                        return array('status' => '200', 'message' => "Request Successful.");
                    }
                }
            }
        } else {
            return array('status' => '400', 'message' => "Required data is missing or incorrect, in the request. Please try again.");
        }
    }
}