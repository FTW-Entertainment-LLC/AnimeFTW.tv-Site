<?php
/****************************************************************\
## FileName: eblast.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Eblast Automation class
## Copyright 2017 FTW Entertainment LLC, All Rights Reserved
## Updated: 02/08/2017 by Brad Riemann
## Version: 1.0
\****************************************************************/

class Eblast extends Config
{
    private $eblastTypes;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    # public function sendEblasts
    # finds the current eblast campaign (if any) and sends out based on the current settings.
    public function sendEblasts()
    {
        // Find if there are any campaigns to check.
        // The query looks for a status of 1, which means that it's an active campaign. But then
        // look at the starts flag to ensure that the oldest one starts right away.
        $query = "SELECT * FROM `eblast_campaigns` WHERE `status` = 1 ORDER BY `starts` ASC LIMIT 1";
        
        $result  = $this->mysqli->query($query);
		$numrows = $result->num_rows;
        
        if ($numrows == 1) {
            $row = $result->fetch_assoc()
            // There was one row found.
            // First check when the last run was, we should not be sending an eblast sooner than a minute since the last run.
            // This is a failsafe since the script shouldnt run faster than a minute anyway.
            if ($row['update'] >= (time()-59)) {
                // The script cannot run.
            } else {
                // The time since the last run was greater than a minute so we continue.
                
                // Build the email template out
                include_once("template.class.php")
                $blastLayout = new Template("../../template/eblast/default.tpl");
                $blastLayout->set('title',$this->eblastTypes[0]['title']);
                $blastLayout->set('preheader',$this->eblastTypes[0]['header']);
                $blastLayout->set('content',$this->eblastTypes[0]['content']);
                
                // Query to see what users we should be sending to.
                $query = "SELECT `users`.`ID`, `users`.`Username`, `users`.`Email` FROM `users` WHERE `Active` = '1' AND NOT EXISTS
(SELECT `id` FROM `user_setting` WHERE `user_setting`.`option_id` = 6 AND `user_setting`.`value` != 11 AND `users`.`ID`=`user_setting`.`uid`)
LIMIT ".$_POST['start'].", 100";
                
                foreach ($this->generateUserInformation($row['type'],$count) as $userRow) {
                    $blastLayout->set('eblast-opt-out-link', 'https://www.animeftw.tv/eblast/settings/' . $this->base64url_encode($row['Email']) . '/' . $this->base64url_encode('user' . $row['ID']) . '/');
                }
                // $blastLayout->output(); // return the eblast content.
            }
        }
    }
    
    # private function collectEblastTypes
    # Brings together all of the potential eblast types that were configured, this way we can properly populate the right email template.
    private function collectEblastTypes()
    {
        $query = "SELECT * FROM `eblast_type` ";
        $result = $this->mysqli->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $this->eblastTypes[$row['id']] = $row;
        }
    }
    
    # private function formatEmailHeaders
    # header information is built in this function.
    private function formatEmailHeaders()
    {
        
    }
    
    # private function generateUserInformation
    # Returns an array of username, email and id which is used as part of the sending process.
    private function generateUserInformation($campaignType,$count)
    {
        $query = "SELECT `users`.`ID`, `users`.`Username`, `users`.`Email` FROM `user_setting` INNER JOIN `users` ON `users`.`ID`=`user_setting`.`uid` WHERE `user_setting`.`option_id` = " . $this->eblastTypes[$campaignType]['user_setting_id'] . " LIMIT " . $count . ", 100";
        $result = $this->mysqli->query($query);
        return $result->fetch_assoc();
    }
}