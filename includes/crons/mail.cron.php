<?php
/****************************************************************\
## FileName: email.cron.php									 
## Author: Brad Riemann										 
## Usage: Checks for emails to be sent out from the Database
## and will send them, intervules are set by the cron job
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/
include('/home/mainaftw/public_html/includes/siteroot.php');
include('/home/mainaftw/public_html/SMTPmail/SMTPconfig.php');
include('/home/mainaftw/public_html/SMTPmail/SMTPClass.php');
	// The object of this script is to check the last X minutes, 
	// if there are rows in the email table, then it needs to be
	// processed, it can be any amount of time.
	// build the query that will tell us how many there were.
	$query = "SELECT id, date, sid, v1, v2 FROM email";
	$results = mysql_query($query);
	$count = mysql_num_rows($results);
	
	if($count > 0)
	{
		while(list($id,$date,$sid,$v1,$v2) = mysql_fetch_array($results))
		{
			$query = "SELECT series.fullSeriesName, series.seoname, episode.epname, episode.epnumber, episode.date, episode.image FROM series, episode WHERE series.id = ".$sid." AND episode.id = ".$v1." AND episode.sid=series.id";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			
			$subquery = "SELECT users.Username, users.Email FROM users, watchlist WHERE users.ID=watchlist.uid AND watchlist.sid = $sid AND watchlist.email = 1";
			$subresult = mysql_query($subquery);
			while(list($Username,$Email) = mysql_fetch_array($subresult))
			{
				//begin email buildup
				$mime_boundary = "----FTW_ENTERTAINMENT_LLC----".md5(time());
				$from = "notifications@animeftw.tv";
				//$toName = 'Robotman321';
				$toName = $Username;
				//$to = 'briemann@techpro.com';    //  their email
				$to = $Email;				
				$Action = "Sending Episode Update Email to ".$Username.", via Email at ".$Email.", for series ".stripslashes($row['fullSeriesName']);
				$AnimeLinkn = "<a href=\"http://www.animeftw.tv/anime/".$row['seoname']."/ep-".$row['epnumber']."\">http://www.animeftw.tv/anime/".$row['seoname']."/ep-".$row['epnumber']."</a>";				
				$headers = "";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n"; 
				//$headers .= "Content-Type: text/html; boundary=\"$mime_boundary\"\n";
				$headers .= "Date: ".date(DATE_RFC2822,$_SERVER['REQUEST_TIME'])."\r\n";
				$headers .= "To: $to\r\n";
				$headers .= "From: AnimeFTW.tv Notifications <notifications@animeftw.tv>\r\n";
				$headers .= "Reply-To: AnimeFTW.tv Notifications <notifications@animeftw.tv>\r\n";
				$subject = 'New Episode added to a Series in your My WatchList!';
				$textbody =  "Hello $toName,<br />\n";
				$textbody .= "This is just a friendly message, letting you know that a series in your My WatchList has had an episode added. <br />\n Details are as follow.<br /><br />\n"; 
				$textbody .= "--------------------------------------------------------------------------------<br />\n";
				$textbody .= "Posted in ".stripslashes($row['fullSeriesName']).", Episode #".$row['epnumber']." <br />\n";
				$textbody .= "Added on ".date("l, F jS, Y, h:i a",$row['date'])."<br />\n";
				$textbody .= "Titled: ".$row['epname']." <br />\n";
				$textbody .= "--------------------------------------------------------------------------------<br /><br />\n";
				$textbody .= "Click this link to view this episode $AnimeLinkn <br /><br />\n\n";
				$textbody .= "If you wish to opt out of these notifications, please visit your profile and update your My WatchList Entry http://www.animeftw.tv/user/$toName<br />\n";
				
				$body = "";			
				$body .= "--$mime_boundary\r\n";
				$body .= "Content-Type: text/plain; charset=UTF-8\n";
				$body .= "Content-Transfer-Encoding: 8bit\n\n";
				$body .= strip_tags($textbody);
				$body .= "--$mime_boundary\r\n";
				# -=-=-=- HTML EMAIL PART
				$body .= "Content-Type: text/html; charset=UTF-8\n";
				$body .= "Content-Transfer-Encoding: 8bit\n\n";
				$body .= " <html lang=\"en\">\n";
				$body .= " <head>\n";
				$body .= " <meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
				$body .= " <title>AnimeFTW Announcements</title>\n";
				$body .= " </head>\n";
				$body .= "<body>\n";
				$body .= $textbody;
				$body .= "</body>\n";
				$body .= "</html>\n";
				$body .= "--$mime_boundary\r\n";
				
				$body = wordwrap($body,70);
				$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $headers, $body);
				$SMTPChat = $SMTPMail->SendMail();
				
				mysql_query("INSERT INTO email_logs (`id`, `date`, `script`, `action`) VALUES (NULL,'".time()."', '".$_SERVER['REQUEST_URI']."', '".$Action."');");
			}
			unset($result);
			unset($row);
			unset($query);
			unset($subresult);
			unset($subquery);
		}
		mysql_query("TRUNCATE email");
	}
	mysql_query("UPDATE `crons` SET `status` = 0, `last_run` = " . time() . " WHERE `id` = 4");