<?php
/****************************************************************\
## FileName: episode-count-harvest.cron.php                                     
## Author: Brad Riemann                                         
## Usage: Runs every two hours, grabs episode data for 
## Analytics and stats.
## Copyright 2014 FTW Entertainment LLC, All Rights Reserved
## Version: 1.0.0
## Updated: 17/09/2014 @ 3:30pm CST by Robotman321
\****************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/classes/config.v2.class.php");
    
class EpisodeStats extends Config {
    private $CronID, $ScriptDay, $CollectionTime, $seriesArray = [];
    
    public function __construct()
    {
        parent::__construct();
        // variable declarations
        $this->CronID            = 14;                // ID of this cron
        $this->ScriptDay        = strtotime(date("j F Y"));             // Used for matching any records created at this time.
        if (isset($_GET['collection-time']) && $_GET['collection-time'] > 7200) {
            $this->CollectionTime = $_GET['collection-time'];
        } else {
            $this->CollectionTime     = 60*60*2;            // the period of time we want to take from, default is 2 hours
        }
        
        // SCript init
        $this->initializeScript();
        $this->updateCronEntry();
    }
    
    private function initializeScript()
    {
        /*
        * The object of the script is to go through the records of the topseries list (before they are processed)
        * and record for each episode via a +1. 
        * Steps:
        * 1) Cycle through the episode_timer table for the last two hours, generate an array of episodes watched.
        *    Compare episodes to ensure that >70% of the video is watched, if it is, add it to the list of episodes counted as "watched"
        * 2) Loop through episodes in array.
        *     1. Update each episode "views" count based on the new episodes.
        *     2. Add entries to the episode_stats table
        *     3. Add entries to the episodestats table for collection during the nightly run.
        */
        $query = "SELECT `eid`, `time`, `max` FROM `" . $this->MainDB . "`.`episode_timer` WHERE `updated` >= " . (time()-$this->CollectionTime);
        $result = $this->mysqli->query($query);
        
        $episodesRecorded=[];
        while ($row = $result->fetch_assoc()) {
            if ((($row['time']/$row['max'])*100) >= 70) {
                // If the key exists we will add +1 to the views listing and continue on.
                if (array_key_exists($row['eid'], $episodesRecorded) === true) {
                    $episodesRecorded[$row['eid']]['views']++;
                } else {
                    // nothing exists so now it must.
                    $episodesRecorded[$row['eid']]['eid'] = $row['eid'];
                    $episodesRecorded[$row['eid']]['views'] = 1;
                }
            }
        }
        
        // Loop through each episode that has been watched in the last hour (condensed array version).
        foreach ($episodesRecorded as $episode) {
            // 1. Update the views of the episode on the episode..
            $this->mysqli->query("UPDATE `" . $this->MainDB . "`.`episode` SET `views` = `views` + " . $episode['views'] . " WHERE `id` = " . $episode['eid']);
            
            // 2. Record Episode Stats information in the stats table.
            $available = $this->checkIfEntryExists($eid);
            if($available == 0)
            {
                // it's not, add an entry.
                $query = "INSERT INTO `" . $this->StatsDB . "`.`episode_stats` (`id`, `date`, `type`, `episode_id`, `value`) VALUES (NULL, '" . $this->ScriptDay . "', 0, '" . $episode['eid'] . "', '" . $episode['views'] . "')";
            }
            else
            {
                // we have something there, let's update it.
                $query = "UPDATE `" . $this->StatsDB . "`.`episode_stats` SET `value` = `value` + " . $episode['views'] . " WHERE `episode_id` = " . $episode['eid'] . " AND `type` = 0 AND `date` = '" . $this->ScriptDay . "'";
            }
            $this->mysqli->query($query);
            
            // 3. Add entries to the episodestats table, which will be used to calculate the overall topseries listings.
            $query = "INSERT INTO `" . $this->MainDB . "`.`episodestats` (`eid`, `epSeriesId`, `ip`, `date`, `epNumber`, `uid`) VALUES ('" . $episode['eid'] . "', '" . $this->seriesIDLookup($episode['eid']) . "', '0', '" . time() . "', '" . $episode['eid'] . "', '0');";
            $result = $this->mysqli->query($query);
        }
    }
    
    private function checkIfEntryExists($eid)
    {
        $query = "SELECT `value` FROM `" . $this->StatsDB . "`.`episode_stats` WHERE `type` = 0 AND `date` = '" . $this->ScriptDay . "' AND `episode_id` = '" . $eid . "'";
        $result = $this->mysqli->query($query);
        $count = mysqli_num_rows($result);
        if($count > 0)
        {
            // The count is larger than 0.. so something is there..
            $row = $result->fetch_assoc();
            return $row['value'];
        }
        else
        {
            return 0;
        }
    }
    
    private function seriesIDLookup($eid)
    {
        if (array_key_exists($eid, $this->seriesArray) === true) {
            return $this->seriesArray[$eid];
        } else {
            // no key exists with this episode, so we query for it.
            $query = "SELECT `sid` FROM `" . $this->MainDB . "`.`episode` WHERE `id` = ${eid}";
            $result = $this->mysqli->query($query);
            $row = $result->fetch_assoc();
            
            // add it to the array for later usage.
            $this->seriesArray[$eid] = $row['sid'];
            
            return $row['sid'];
        }
    }
    
    private function updateCronEntry()
    {
        $this->mysqli->query("INSERT INTO `" . $this->MainDB . "`.`crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->CronID . "', '" . time() . "', '" . time() . "');");
        $this->mysqli->query("UPDATE `" . $this->MainDB . "`.`crons` SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->CronID);
    }
}

$EpisodeStats = new EpisodeStats();