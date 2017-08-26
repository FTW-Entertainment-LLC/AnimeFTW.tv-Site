<?php
/****************************************************************\
## FileName: eblast.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Eblast Automation class
## Copyright 2017 FTW Entertainment LLC, All Rights Reserved
## Updated: 02/08/2017 by Brad Riemann
## Version: 1.0
\****************************************************************/

include_once('smtp.v2.class.php');

class Eblast extends Config
{
    private $eblastTypes, $smtpSettings=array(), $mimeBoundary, $cronId;
    
    public function __construct()
    {
        parent::__construct();
        $this->cronId = 16;
        $this->smtpSettings['SmtpServer']="localhost";
        $this->smtpSettings['SmtpPort']="25";
        $this->smtpSettings['SmtpUser']="notifications+animeftw.tv";
        $this->smtpSettings['SmtpPass']="Ftw3nt3rtainm3nt";
        $this->mimeBoundary = "----FTW_ENTERTAINMENT_LLC----".md5(time());
        $this->collectEblastTypes();
    }
    
    public function __destruct()
    {
        $this->mysqli->query("INSERT INTO `crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '" . $this->cronId . "', '" . time() . "', '" . time() . "');");
		$this->mysqli->query("UPDATE `crons` SET last_run = '" . time() . "', status = 0 WHERE id = " . $this->cronId);
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
            $row = $result->fetch_assoc();
            // There was one row found.
            // First check when the last run was, we should not be sending an eblast sooner than a minute since the last run.
            // This is a failsafe since the script shouldnt run faster than a minute anyway.
            if ($row['update'] >= (time()-59)) {
                // The script cannot run.
            } else {
                // The time since the last run was greater than a minute so we continue.
                
                // Build the email template out
                include_once("template.class.php");
                
                foreach ($this->generateUserInformation($row) as $userRow) {
                    $blastLayout = new Template("../../template/eblast/default.tpl");
                    $blastLayout->set('title',$row['title']);
                    $blastLayout->set('preheader',$row['header']);
                    $blastLayout->set('email-type',$this->eblastTypes[$row['type']]['name']);
                    $blastLayout->set('content',$row['contents']);
                    $blastLayout->set('email',$userRow['Email']);
                    $blastLayout->set('eblast-opt-out-link', 'https://www.animeftw.tv/eblast/settings/' . $this->base64url_encode($userRow['Email']) . '/' . $this->base64url_encode('user' . $userRow['ID']) . '/');
                    $this->sendEmail('notifications@animeftw.tv', $userRow['Email'], $row['title'], $blastLayout->output());
                }
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
    private function formatEmailHeaders($toEmail)
    {
        $headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: multipart/alternative; boundary=\"" . $this->mimeBoundary . "\"\r\n"; 
		$headers .= "Date: ".date(DATE_RFC2822,$_SERVER['REQUEST_TIME'])."\r\n";
		$headers .= "To: $toEmail\r\n";
		$headers .= "From: AnimeFTW.tv Notifications <notifications@animeftw.tv>\r\n";
		$headers .= "Reply-To: AnimeFTW.tv Notifications <notifications@animeftw.tv>\r\n";
        return $headers;
    }
    
    # private function generateUserInformation
    # Returns an array of username, email and id which is used as part of the sending process.
    private function generateUserInformation($campaignInfo)
    {
        $query = "SELECT `users`.`ID`, `users`.`Username`, `users`.`Email` FROM `user_setting` INNER JOIN `users` ON `users`.`ID`=`user_setting`.`uid` WHERE `user_setting`.`option_id` = " . $this->eblastTypes[$campaignInfo['type']]['user_setting_id'] . " LIMIT " . $campaignInfo['count'] . ", 100";
       
        $result = $this->mysqli->query($query);
        $returnResults = [];
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $returnResults[$i]['ID'] = $row['ID'];
            $returnResults[$i]['Username'] = $row['Username'];
            $returnResults[$i]['Email'] = $row['Email'];
            $i++;
        }
        
        $newCount = $campaignInfo['count']+100;
        if ($result->num_rows < 100) {
            // results were less than 100, we need to update this job letting it know that after this 
            // run it should be turned off.
            $query = "UPDATE `eblast_campaigns` SET `status` = 2, `count` = ${newCount} WHERE `id` = " . $campaignInfo['id'];
            $result = $this->mysqli->query($query);
        } else {
            $query = "UPDATE `eblast_campaigns` SET `count` = ${newCount} WHERE `id` = " . $campaignInfo['id'];
            $result = $this->mysqli->query($query);
        }
        return $returnResults;
    }
    
    private function sendEmail($from, $to, $subject, $htmlContent)
    {
        $body = "--" . $this->mimeBoundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\n";
        $body .= "Content-Transfer-Encoding: 8bit\n\n";
        $body .=  "AnimeFTW.tv News!\n\n";
        $body .=  strip_tags(stripslashes("This was an HTML Message, please view in an HTML browser. Thank you."))."\n\n";
        $body .= "--" . $this->mimeBoundary . "\r\n";
        # -=-=-=- HTML EMAIL PART
        $body .= "Content-Type: text/html; charset=UTF-8\n";
        $body .= "Content-Transfer-Encoding: 8bit\n\n";
        $body .= $htmlContent;
        $SMTPMail = new SMTPClient ($this->smtpSettings['SmtpServer'], $this->smtpSettings['SmtpPort'], $this->smtpSettings['SmtpUser'], $this->smtpSettings['SmtpPass'], $from, $to, $subject, $this->formatEmailHeaders($to), $body);
        $SMTPChat = $SMTPMail->SendMail();
    }   
}