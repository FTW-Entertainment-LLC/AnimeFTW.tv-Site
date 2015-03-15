<?php
include('../includes/siteroot.php');
include('blast.functions.php');
$daysago = time()-"2592000";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(!isset($_POST['level'])){$level = 0;}
	else {$level = $_POST['level'];}
	if($_POST['level'] == 10){
		$query   = "SELECT Username, Email FROM users WHERE Active='1' AND notifications='1' AND (lastActivity <= '$30daysago' AND lastActivity > '0') ORDER BY ID";
	}
	else if($_POST['level'] == 11){
		$query   = "SELECT Username, Email FROM users WHERE Active='1' AND notifications='1' ORDER BY ID";
	}
	else {
		$query   = "SELECT Username, Email FROM users WHERE Active='1' AND notifications='1' AND Level_access='".$level."' ORDER BY ID";
	}
	$result  = mysql_query($query) or die('Error : ' . mysql_error());
	$total_rows = mysql_num_rows($result);
}

//check to see what part we are on..
if(!isset($_GET['start'])){$start = 0;}
else {$start = $_GET['start'];}

if(isset($_POST)){$remember = TRUE;}
else{$remember = FALSE;}

if($_SERVER["REQUEST_METHOD"] == "POST"){
	//$to = $_POST['to'];
	$fromUser = $_POST['from'];
	$subject = $_POST['sub'];
	$level = $_POST['level'];
	$msgBody = $_POST['message'];
	$Epupdate = $_POST['Epupdate'];
	if($Epupdate == 'episode'){$typstring = 'episode';}
	else if($Epupdate == 'update'){$typstring = 'update';}
	include('SMTPconfig.php');
	include('SMTPClass.php');

	$fromUsername = "Robotman321";
					
	if($_POST['level'] == 10){
		$query2 = "SELECT Username, Email FROM users WHERE Active='1' AND notifications='1' AND (lastActivity <= '$daysago' AND lastActivity > '0') ORDER BY ID";
	}
	else {
		$query2 = "SELECT Username, Email FROM users WHERE Active='1' AND notifications='1' AND Level_access='".$level."' ORDER BY ID LIMIT ".$_POST['start'].", 100";
	}
	$result2  = mysql_query($query2) or die('Error : ' . mysql_error());
	$i = 0;	
	while(list($Username, $Email) = mysql_fetch_array($result2)) {
		$toUsername = $Username;
		$toEmail = $Email;
		//begin email buildup
		$mime_boundary = "----FTW_ENTERTAINMENT_LLC----".md5(time());
		$from = "notifications@animeftw.tv";
		$subject = $subject;
		$to = $toEmail;    //  their email
		$headers = "";
			
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n"; 
		$headers .= "Date: ".date(DATE_RFC2822,$_SERVER['REQUEST_TIME'])."\r\n";
		$headers .= "To: $toEmail\r\n";
		$headers .= "From: AnimeFTW.tv Notifications <notifications@animeftw.tv>\r\n";
		$headers .= "Reply-To: AnimeFTW.tv Notifications <notifications@animeftw.tv>\r\n";
		if($_POST['Epupdate'] == 'update')
					{
						# -=-=-=- TEXT EMAIL PART
						$body .= "--$mime_boundary\r\n";
						$body .= "Content-Type: text/plain; charset=UTF-8\n";
						$body .= "Content-Transfer-Encoding: 8bit\n\n";
						$body .=  "AnimeFTW.tv News!\n\n";
						$body .=  strip_tags(stripslashes($msgBody))."\n\n";
						$body .= "--$mime_boundary\r\n";
						# -=-=-=- HTML EMAIL PART
						$body .= "Content-Type: text/html; charset=UTF-8\n";
						$body .= "Content-Transfer-Encoding: 8bit\n\n";
						$body .= " <html lang=\"en\">\n";
						$body .= " <head>\n";
						$body .= " <meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
						$body .= " <title>AnimeFTW Announcements</title>\n";
						$body .= " 			<style type=\"text/css\">\n";
						$body .= " 			a:hover { text-decoration: none !important; }\n";
						$body .= " 			.header h1 {color: #47c8db; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;}\n";
						$body .= " 			.header p {color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;}\n";
						$body .= " 			.content h2 {color:#646464; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif;  }\n";
						$body .= " 			.content p {color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif;}\n";
						$body .= " 			.content a {color: #0eb6ce; text-decoration: none;}\n";
						$body .= " 			.footer p {font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;}\n";
						$body .= " 			.footer a {color: #0eb6ce; text-decoration: none;}\n";
						$body .= " 			</style>\n";
						$body .= " 		  </head>\n";
						$body .= " 		  <body style=\"margin: 0; padding: 0; background: #4b4b4b url('http://eblasts.animeftw.tv/images/bg_email.png');\" bgcolor=\"#4b4b4b\">\n";
						$body .= " 				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"padding: 35px 0; background: #4b4b4b url('http://eblasts.animeftw.tv/images/bg_email.png');\">\n";
						$body .= " 				  <tr>\n";
						$body .= " 					<td align=\"center\" style=\"margin: 0; padding: 0; background: url('http://eblasts.animeftw.tv/images/bg_email.png') ;\" >\n";
						$body .= " 						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"600\" style=\"font-family: Helvetica, Arial, sans-serif; background:#2a2a2a;\" class=\"header\">\n";
						$body .= " 							<tr>\n";
						$body .= " 								<td width=\"600\" align=\"left\" style=\"padding: font-size: 0; line-height: 0; height: 7px;\" height=\"7\" colspan=\"2\"><img src=\"http://eblasts.animeftw.tv/images/bg_header.png\" alt=\"header bg\"></td>\n";
						$body .= " 							  </tr>\n";
						$body .= " 							<tr>\n";
						$body .= " 							<td width=\"20\"style=\"font-size: 0px;\">&nbsp;</td>\n";
						$body .= " 							<td width=\"580\" align=\"left\" style=\"padding: 18px 0 10px;\">\n";
						$body .= " 								<h1 style=\"color: #47c8db; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;\"><a href=\"http://eblasts.animeftw.tv/link/VQ9VkVPPpXT5jgmpkS7EhNIFsGgM1861AuBVP7Aq4oFJFgxKzWM19X8ScCzxIAOkJ7GeNTWbgvWcXSc2y8Ef8ur2uMv4sCub8fO08FAMgUpoScWhyBrtVPrJ8LxPJF3HBdgNj16ATFjtPUgoQvAbVa\" style=\"color: #0eb6ce; text-decoration: none;\">AnimeFTW.tv</a></h1>\n";
						$body .= " 								<p style=\"color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;\">Only the best for the best Members...</p>\n";
						$body .= " 							</td>\n";
						$body .= " 						  </tr>\n";
						$body .= " 						</table><!-- header-->\n";
						$body .= " 						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"600\" style=\"font-family: Helvetica, Arial, sans-serif; background: #fff;\" bgcolor=\"#fff\">\n";
						$body .= " 							<tr>\n";
						$body .= " 							<td width=\"600\" valign=\"top\" align=\"left\" style=\"font-family: Helvetica, Arial, sans-serif; padding: 20px 0 0;\" class=\"content\">\n";
						$body .= " 								<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"  style=\"color: #717171; font: normal 11px Helvetica, Arial, sans-serif; margin: 0; padding: 0;\" width=\"600\">\n";
						$body .= " 								<tr>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 									<td style=\"padding: 0;  font-family: Helvetica, Arial, sans-serif; background: url('http://eblasts.animeftw.tv/images/bg_date_wide.png') no-repeat left top; height:20px; line-height: 20px;\"  align=\"center\" width=\"558\" height=\"20\">\n";
						$body .= " 										<h3 style=\"color:#666; font-weight: bold; text-transform: uppercase; margin: 0; padding: 0; line-height: 10px; font-size: 10px;\"></h3>\n";
						$body .= " 									</td>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 								</tr>\n";
						$body .= " 								<tr>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 									<td style=\"padding: 20px 0 0;\" align=\"left\">\n";			
						$body .= " 										<h2 style=\"color:#646464; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif; \">Update: ".$subject."</h2>\n";
						$body .= " 									</td>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 								</tr>\n";
						$body .= " 								<tr>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 									<td style=\"padding: 15px 0 15px;\"  valign=\"top\">\n";
						
						// Begin main body
						
						$body .= stripslashes($msgBody);
						//end body
						
						$body .= " 									</td><td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 								</tr>\n";
						$body .= " 						</table>	\n";
						$body .= " 							</td>\n";
													
						$body .= " 						  </tr>\n";
						$body .= " 							<tr>\n";
						$body .= " 								<td width=\"600\" align=\"left\" style=\"padding: font-size: 0; line-height: 0; height: 3px;\" height=\"3\" colspan=\"2\"><img src=\"http://eblasts.animeftw.tv/images/bg_bottom.png\" alt=\"header bg\"></td>\n";
						$body .= " 							  </tr>	\n";
						$body .= " 						</table><!-- body -->\n";
						$body .= " 						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"600\" style=\"font-family: Helvetica, Arial, sans-serif; line-height: 10px;\" class=\"footer\"> \n";
						$body .= " 						<tr>\n";
						$body .= " 							<td align=\"center\" style=\"padding: 5px 0 10px; font-size: 11px; color:#7d7a7a; margin: 0; line-height: 1.2;font-family: Helvetica, Arial, sans-serif;\" valign=\"top\">\n";
						$body .= " 								<br>\n";
						$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\">You're receiving this email blast because you did not opt out of Admin Emails.</p>\n";
						$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\"> Not interested? <a href=\"http://eblasts.animeftw.tv/link/CwAoXD1xfaBlJkzBEodr0pw7nkVAkDnEGgLjg6DjunuVV91eJXTVIMNrsJWUMmAbMwXFSErHGiaxEdHnViw5o330VngkXrDHkrblg6Qg8KV2iJ5EevdUN3l49QCQrtee2JQxaHjq6ckDDlFyp3DUjD\" style=\"color: #0eb6ce; text-decoration: none;\">Opt out</a> of Future Messages.</p>\n";
						$body .= " 							</td>\n";
						$body .= " 						  </tr>\n";
						$body .= " 						</table><!-- footer-->\n";
						$body .= " 					</td>\n";
						$body .= " 					</td>\n";
						$body .= " 				</tr>\n";
						$body .= " 			</table>\n";
						$body .= " 		  </body>\n";
						$body .= " 		</html>\n";
						
						
						$body .= "--$mime_boundary--\n\n"; 
						$body = wordwrap($body,70);
					}
		//away we go!
		//mail($to, $subject, $body, $headers);
		$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $headers, $body);
		$SMTPChat = $SMTPMail->SendMail();
		$body = "";
		//echo 'Sent to '.$toUsername.'&nbsp;'.$i.'<br />';
		$i++;
		if($i == 100){
			$redirect = '<script>
			setTimeout(function() {
				document.myform.submit();
			}, 15000);
			</script>';
		}
	}
}
else {
	//when nothing is set!
	$to = "";
	$fromUser = "";
	$subject = "";
	$level = "";
	$msgBody = "";
	$Epupdate = "";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?
if(isset($redirect))
{
	echo $redirect;
}
?>
<title>eBLAST for AnimeFTW.tv</title>
</head>
<body>
<form id="myform" name="myform" method="post" action="?start=<?=($start+100);?>">
<input type="hidden" name="start" value="<?=$start;?>" />
<input type="hidden" name="auto" value="on" />
<table width="500px">
<tr><td>From :</td><td><input type="text" name="from"<? if($remember = TRUE){echo ' value="'.$fromUser.'"';}?> /></td></tr>
<tr><td>Subject :</td><td><input type="text" name="sub"<? if($remember = TRUE){echo ' value="'.$subject.'"';}?> /></td></tr>
<tr><td>Update Type?</td><td>
<select name="Epupdate">
	<option value="update"<? if($Epupdate == 'update'){echo ' selected="selected"';}?>>Update</option>
	<option value="episode"<? if($Epupdate == 'episode'){echo ' selected="selected"';}?>>Episode</option>
</select></td></tr>
<tr><td>Sent to</td><td> 
<select name="level">
	<option value="1"<? if($level == 1){echo ' selected="selected"';}?>>Administrators</option>
	<option value="2"<? if($level == 2){echo ' selected="selected"';}?>>Managers</option>
	<option value="3"<? if($level == 3){echo ' selected="selected"';}?>>Regular Members</option>
	<option value="4"<? if($level == 4){echo ' selected="selected"';}?>>Forum Mod</option>
	<option value="5"<? if($level == 5){echo ' selected="selected"';}?>>Encoders</option>
	<option value="6"<? if($level == 6){echo ' selected="selected"';}?>>Report Mods</option>
	<option value="7"<? if($level == 7){echo ' selected="selected"';}?>>Advanced Members</option>
	<option value="10"<? if($level == 10){echo ' selected="selected"';}?>>Inactive Members</option>
	<option value="11"<? if($level == 11){echo ' selected="selected"';}?>>Notifiable Members</option>
</select></td></tr>
<tr><td>Message :</td><td><textarea name="message" rows="8" cols="50"><? if(isset($msgBody)){echo $msgBody;}?></textarea></td></tr>
<tr><td></td><td><input type="submit" value=" Send " /></td></tr>
</table>
</form>
</body>
</html>
