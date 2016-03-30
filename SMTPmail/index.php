<?php
header('Content-Type: text/html; charset=utf-8');
include('../includes/classes/config.class.php');
include('blast.functions.php');
$daysago = time()-"2592000";
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

//check to see what part we are on.
if(!isset($_GET['start'])){$start = 0;}
else {$start = $_GET['start'];}

if(isset($_POST)){$remember = TRUE;}
else{$remember = FALSE;}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(!isset($_POST['update-type'])){$level = 0;}
	else {$level = $_POST['update-type'];}
	//$to = $_POST['to'];
	$fromUser = $_POST['from'];
	$subject = $_POST['sub'];
	$msgBody = $_POST['message'];
	$Epupdate = $_POST['Epupdate'];
	if($Epupdate == 'episode'){$typstring = 'episode';}
	else if($Epupdate == 'update'){$typstring = 'update';}
	include('SMTPconfig.php');
	include('SMTPClass.php');

	$fromUsername = "Robotman321";
	
	if($_POST['update-type'] == 1)
	{
		$query2 = "SELECT `users`.`Username`, `users`.`Email` 
FROM `users` WHERE `Active` = '1' AND NOT EXISTS
(SELECT `id` FROM `user_setting` WHERE `user_setting`.`option_id` = 6 AND `user_setting`.`value` != 11 AND `users`.`ID`=`user_setting`.`uid`)
LIMIT ".$_POST['start'].", 100";
	}
	else if($_POST['update-type'] == 2)
	{
		$query2 = "SELECT `users`.`Username`, `users`.`Email` 
FROM `users` WHERE `Active` = '1' AND NOT EXISTS
(SELECT `id` FROM `user_setting` WHERE `user_setting`.`option_id` = 7 AND `user_setting`.`value` != 14 AND `users`.`ID`=`user_setting`.`uid`)
LIMIT ".$_POST['start'].", 100";
	}
	else if($_POST['update-type'] == 4)
	{
		$query2 = "SELECT `users`.`Username`, `users`.`Email` FROM `developers_api_sessions`, `users` WHERE `developers_api_sessions`.`did` = 3 AND `users`.`ID`=`developers_api_sessions`.`uid` LIMIT ".$_POST['start'].", 100 GROUP BY `developers_api_sessions`.`uid`";
	}
	else if($_POST['update-type'] == 5)
	{
        $twoWeeksAgo = time()-(14*24*60*60);
		$query2 = "SELECT `Username`, `Email` FROM `users` WHERE `Active` = 'yes' AND `lastActivity` >= ${twoWeeksAgo} LIMIT ".$_POST['start'].", 100";
	}
	else {
		//$query2 = "SELECT Username, Email FROM users WHERE Active='1' AND notifications='1' AND Level_access='".$level."' ORDER BY ID LIMIT ".$_POST['start'].", 100";
	}
	$result2  = mysql_query($query2) or die('Error : ' . mysql_error());
	$count = mysql_num_rows($result2);
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
		if($_POST['update-type'] == '1' || $_POST['update-type'] == '2' || $_POST['update-type'] == '4')
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
			$body .= " 								<p style=\"color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;\">Only the best for the best Members..</p>\n";
			$body .= " 							</td>\n";
			$body .= " 						  </tr>\n";
			$body .= " 						</table><!-- header-->\n";
			$body .= " 						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"600\" style=\"font-family: Helvetica, Arial, sans-serif; background: #fff;\" bgcolor=\"#fff\">\n";
			$body .= " 							<tr>\n";
			$body .= " 							<td width=\"600\" valign=\"top\" align=\"left\" style=\"font-family: Helvetica, Arial, sans-serif; padding: 20px 0 0;\" class=\"content\">\n";
			$body .= " 								<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"  style=\"color: #717171; font: normal 11px Helvetica, Arial, sans-serif; margin: 0; padding: 0;\" width=\"600\">\n";
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
		else if($_POST['update-type'] == '3')
		{
			# -=-=-=- TEXT EMAIL PART
			$body .= "--$mime_boundary\r\n";
			$body .= "Content-Type: text/plain; charset=UTF-8\n";
			$body .= "Content-Transfer-Encoding: 8bit\n\n";
			$body .=  "AnimeFTW.tv News!\n\n";
			$body .=  strip_tags(stripslashes("This was an HTML Message, please view in an HTML browser. Thank you."))."\n\n";
			$body .= "--$mime_boundary\r\n";
			# -=-=-=- HTML EMAIL PART
			# -=-=-=- HTML EMAIL PART			
			$body .= "Content-Type: text/html; charset=UTF-8\n";
			$body .= "Content-Transfer-Encoding: 8bit\n\n";
			$body .= " <html lang=\"en\">\n";
			$body .= " <head>\n";
			$body .= " <meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
			$body .= " <title>AnimeFTW Announcements</title>\n";
			$body .= " 			<style type=\"text/css\">\n";
			$body .= "				body {margin-top: 0px;margin-left: 0px;}\n";
			$body .= "				#page_1 {position:relative; overflow: hidden;margin: 0 auto;padding: 0px;border: none;width: 816px;height: 1012px;}\n";
			$body .= "				#page_1 #id_1 {border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 816px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_2 {border:none;margin: 14px 0px 0px 46px;padding: 0px;border:none;width: 675px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_2 #id_2_1 {float:left;border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 326px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_2 #id_2_2 {float:left;border:none;margin: 0px 0px 0px 58px;padding: 0px;border:none;width: 291px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_3 {border:none;margin: 129px 0px 0px 46px;padding: 0px;border:none;width: 770px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_3 #id_3_1 {float:left;border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 408px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_3 #id_3_2 {float:left;border:none;margin: 125px 0px 0px 0px;padding: 0px;border:none;width: 362px;overflow: hidden;}\n";
			$body .= "				#page_1 #id_4 {border:none;margin: 33px 0px 0px 171px;padding: 0px;border:none;width: 645px;overflow: hidden;}\n";
			$body .= "				#page_1 #dimg1 {position:absolute;top:80px;left:23px;z-index:-1;width:769px;height:888px;}\n";
			$body .= "				#page_1 #dimg1 #img1 {width:769px;height:888px;}\n";
			$body .= "				#page_2 {position:relative; overflow: hidden;margin: 0 auto;padding: 0px;border: none;width: 816px;height: 996px;}\n";
			$body .= "				#page_2 #id_1 {border:none;margin: 4px 0px 0px 34px;padding: 0px;border:none;width: 782px;overflow: hidden;}\n";
			$body .= "				#page_2 #id_1 #id_1_1 {float:left;border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 456px;overflow: hidden;}\n";
			$body .= "				#page_2 #id_1 #id_1_2 {float:left;border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 326px;overflow: hidden;}\n";
			$body .= "				#page_2 #id_2 {border:none;margin: 67px 0px 0px 0px;padding: 0px;border:none;width: 816px;overflow: hidden;}\n";
			$body .= "				#page_2 #id_3 {border:none;margin: 34px 0px 0px 187px;padding: 0px;border:none;width: 629px;overflow: hidden;}\n";
			$body .= "				#page_2 #dimg1 {position:absolute;top:0px;left:24px;z-index:-1;width:769px;height:996px;}\n";
			$body .= "				#page_2 #dimg1 #img1 {width:769px;height:996px;}\n";
			$body .= "				.dclr {clear:both;float:none;height:1px;margin:0px;padding:0px;overflow:hidden;}\n";
			$body .= "				.ft0{font: bold 16px 'Century Gothic';color: #3c9e46;line-height: 19px;position: relative; bottom: 41px;}\n";
			$body .= "				.ft1{font: 64px 'Century Gothic';color: #3c9e46;line-height: 75px;}\n";
			$body .= "				.ft2{font: bold 16px 'Century Gothic';color: #3c9e46;line-height: 19px;}\n";
			$body .= "				.ft3{font: bold 9px 'Century Gothic';color: #3c9e46;line-height: 13px;position: relative; bottom: 4px;}\n";
			$body .= "				.ft4{font: bold 15px 'Century Gothic';color: #3c9e46;line-height: 18px;}\n";
			$body .= "				.ft5{font: bold 19px 'Century Gothic';color: #ffffff;line-height: 23px;}\n";
			$body .= "				.ft6{font: 13px 'Century Gothic';color: #ffffff;line-height: 17px;}\n";
			$body .= "				.ft7{font: bold 13px 'Century Gothic';color: #ffffff;line-height: 16px;}\n";
			$body .= "				.ft8{font: 12px 'Century Gothic';color: #ffffff;line-height: 15px;}\n";
			$body .= "				.ft9{font: 12px 'Century Gothic';color: #ffffff;line-height: 14px;}\n";
			$body .= "				.ft10{font: 12px 'Century Gothic';color: #ffffff;line-height: 17px;}\n";
			$body .= "				.ft11{font: 19px 'Century Gothic';color: #3c9e46;line-height: 22px;}\n";
			$body .= "				.ft12{font: 13px 'Century Gothic';line-height: 16px;}\n";
			$body .= "				.ft13{font: italic 13px 'Century Gothic';line-height: 16px;}\n";
			$body .= "				.ft14{font: 13px 'Century Gothic';line-height: 17px;}\n";
			$body .= "				.ft15{font: 13px 'Century Gothic';line-height: 15px;}\n";
			$body .= "				.ft16{font: bold 13px 'Century Gothic';line-height: 16px;}\n";
			$body .= "				.ft17{font: bold 21px 'Century Gothic';color: #ffffff;line-height: 25px;}\n";
			$body .= "				.ft18{font: bold 19px 'Century Gothic';color: #d32125;line-height: 23px;}\n";
			$body .= "				.ft19{font: 13px 'Century Gothic';color: #d32125;line-height: 17px;}\n";
			$body .= "				.ft20{font: 13px 'Century Gothic';text-decoration: underline;color: #000080;line-height: 17px;}\n";
			$body .= "				.ft21{font: 12px 'Century Gothic';color: #ffffff;line-height: 20px;}\n";
			$body .= "				.ft22{font: 12px 'Century Gothic';color: #ffffff;line-height: 18px;}\n";
			$body .= "				.ft23{font: 12px 'Century Gothic';color: #ffffff;line-height: 21px;}\n";
			$body .= "				.ft24{font: 35px 'Century Gothic';color: #3c9e46;line-height: 42px;}\n";
			$body .= "				.ft25{font: bold 16px 'Century Gothic';color: #ffffff;line-height: 19px;}\n";
			$body .= "				.p0{text-align: left;padding-left: 22px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p1{text-align: left;padding-left: 651px;margin-top: -40px;margin-bottom: 0px;}\n";
			$body .= "				.p2{text-align: right;padding-right: 47px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p3{text-align: left;padding-left: 46px;margin-top: 14px;margin-bottom: 0px;}\n";
			$body .= "				.p4{text-align: left;padding-right: 4px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p5{text-align: left;margin-top: 14px;margin-bottom: 0px;}\n";
			$body .= "				.p6{text-align: left;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p7{text-align: left;padding-right: 78px;margin-top: 17px;margin-bottom: 0px;}\n";
			$body .= "				.p8{text-align: left;margin-top: 15px;margin-bottom: 0px;}\n";
			$body .= "				.p9{text-align: left;padding-right: 90px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p10{text-align: left;padding-right: 75px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p11{text-align: left;padding-left: 207px;margin-top: 34px;margin-bottom: 0px;}\n";
			$body .= "				.p12{text-align: left;padding-left: 73px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p13{text-align: left;padding-left: 78px;margin-top: 12px;margin-bottom: 0px;}\n";
			$body .= "				.p14{text-align: left;padding-left: 45px;padding-right: 62px;margin-top: 1px;margin-bottom: 0px;text-indent: -40px;}\n";
			$body .= "				.p15{text-align: left;padding-left: 102px;margin-top: 1px;margin-bottom: 0px;}\n";
			$body .= "				.p16{text-align: left;margin-top: 220px;margin-bottom: 0px;}\n";
			$body .= "				.p17{text-align: left;margin-top: 2px;margin-bottom: 0px;}\n";
			$body .= "				.p18{text-align: left;padding-right: 55px;margin-top: 24px;margin-bottom: 0px;}\n";
			$body .= "				.p19{text-align: left;padding-right: 391px;margin-top: 18px;margin-bottom: 0px;}\n";
			$body .= "				.p20{text-align: left;padding-right: 390px;margin-top: 17px;margin-bottom: 0px;}\n";
			$body .= "				.p21{text-align: left;padding-right: 386px;margin-top: 17px;margin-bottom: 0px;}\n";
			$body .= "				.p22{text-align: left;padding-right: 378px;margin-top: 17px;margin-bottom: 0px;}\n";
			$body .= "				.p23{text-align: left;padding-right: 69px;margin-top: 15px;margin-bottom: 0px;}\n";
			$body .= "				.p24{text-align: left;padding-right: 58px;margin-top: 15px;margin-bottom: 0px;}\n";
			$body .= "				.p25{text-align: left;margin-top: 99px;margin-bottom: 0px;}\n";
			$body .= "				.p26{text-align: left;padding-right: 36px;margin-top: 9px;margin-bottom: 0px;}\n";
			$body .= "				.p27{text-align: left;margin-top: 6px;margin-bottom: 0px;}\n";
			$body .= "				.p28{text-align: left;padding-right: 196px;margin-top: 5px;margin-bottom: 0px;}\n";
			$body .= "				.p29{text-align: left;margin-top: 8px;margin-bottom: 0px;}\n";
			$body .= "				.p30{text-align: left;margin-top: 5px;margin-bottom: 0px;}\n";
			$body .= "				.p31{text-align: left;padding-right: 48px;margin-top: 9px;margin-bottom: 0px;}\n";
			$body .= "				.p32{text-align: left;padding-right: 35px;margin-top: 8px;margin-bottom: 0px;}\n";
			$body .= "				.p33{text-align: left;padding-left: 52px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p34{text-align: left;padding-left: 134px;margin-top: 0px;margin-bottom: 0px;}\n";
			$body .= "				.p35{text-align: left;padding-left: 94px;margin-top: 1px;margin-bottom: 0px;}\n";
			$body .= "				.p36{text-align: right;padding-right: 202px;margin-top: 59px;margin-bottom: 0px;}\n";
			$body .= "				.p37{text-align: left;padding-left: 502px;margin-top: 2px;margin-bottom: 0px;}\n";
			$body .= " 			</style>\n";
			$body .= " 		  </head>\n";
			$body .= "<body>\n";
			$body .= "<div style=\"margin:20px;\">AnimeFTW.tv Holiday Card not showing correctly? <a href=\"http://eblasts.animeftw.tv/christmas-2013.html\">View it in your browser!</a></div>";
			$body .= "<div id=\"page_1\">\n";
			$body .= "<div id=\"dimg1\">\n";
			$body .= "<IMG src=\"http://eblasts.animeftw.tv/images/christmas2013/christmas-newsletter1x1.jpg\" id=\"img1\" />\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_1\">\n";
			$body .= "<p class=\"p0 ft1\">AnimeFTW.tv News <SPAN class=\"ft0\">Newsletter:</SPAN></p>\n";
			$body .= "<p class=\"p1 ft2\"><nobr>2013-12-01.</nobr></p>\n";
			$body .= "<p class=\"p2 ft4\">December 24<SPAN class=\"ft3\">th</SPAN>, 2013</p>\n";
			$body .= "<p class=\"p3 ft5\">$toUsername, Merry Christmas and Season’s Greetings from AnimeFTW.tv!</p>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_2\">\n";
			$body .= "<div id=\"id_2_1\">\n";
			$body .= "<p class=\"p4 ft6\">With Christmas tomorrow, the staff of AnimeFTW.tv wanted to wish you and your family a very Merry Christmas and a Happy New year!</p>\n";
			$body .= "<p class=\"p5 ft6\">As we look back on 2013, we are thankful for members that continue to delight and make our jobs that much more enjoyable. Our passion is anime, as is yours, and being able to share and grow that passion with a community of members is special for which we are extremely thankful for.</p>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_2_2\">\n";
			$body .= "<p class=\"p6 ft6\">From the staff of AnimeFTW.tv and FTW Entertainment LLC, we hope you have a safe holiday season!</p>\n";
			$body .= "</div>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_3\">\n";
			$body .= "<div id=\"id_3_1\">\n";
			$body .= "<p class=\"p6 ft5\">Notes from Staff</p>\n";
			$body .= "<p class=\"p7 ft6\">This past year has been special, and here are some thoughts from our staff:</p>\n";
			$body .= "<p class=\"p8 ft7\">Mimby writes:</p>\n";
			$body .= "<p class=\"p9 ft6\">I hope you have a Merry Christmas & an amazing New Year! Thanks for being such great members!</p>\n";
			$body .= "<p class=\"p8 ft7\">Toki writes:</p>\n";
			$body .= "<p class=\"p6 ft8\">Amor Entrada Masterspark!</p>\n";
			$body .= "<p class=\"p6 ft8\">Up comes god ol Marisa</p>\n";
			$body .= "<p class=\"p6 ft9\">With a big sack full of magic tomes</p>\n";
			$body .= "<p class=\"p6 ft8\">Fire and reading back at home</p>\n";
			$body .= "<p class=\"p6 ft8\">Ze Ze Ze who would had known</p>\n";
			$body .= "<p class=\"p6 ft8\">Ze Ze Ze who would had known</p>\n";
			$body .= "<p class=\"p6 ft9\">Up on a broomstick graze graze graze</p>\n";
			$body .= "<p class=\"p6 ft10\">Marisa stole your s**t <nobr>Da-Ze!</nobr></p>\n";
			$body .= "<p class=\"p5 ft7\">Gameoffuture writes:</p>\n";
			$body .= "<p class=\"p10 ft8\">Hello AnimeFTW members and fans. I am delighted to have been given the opportunity to wish you a wonderful holidays and a happy new year. I thank you for your continued support and looking forward to serving you by bringing more of your favourite anime to the site.</p>\n";
			$body .= "<p class=\"p5 ft10\">May your future always shine brighter than your present.</p>\n";
			$body .= "<p class=\"p11 ft6\">www.animeftw.tv</p>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_3_2\">\n";
			$body .= "<p class=\"p12 ft11\">www.animeftw.tv</p>\n";
			$body .= "<p class=\"p13 ft12\">QUOTE OF THE MONTH:</p>\n";
			$body .= "<p class=\"p14 ft13\">The main reason Santa is so jolly is because he knows where all the bad girls live.</p>\n";
			$body .= "<p class=\"p15 ft14\">- George Carlin</p>\n";
			$body .= "<p class=\"p16 ft15\">PHOTO CAPTION:</p>\n";
			$body .= "<p class=\"p17 ft16\">Snowmen gathered round the tree</p>\n";
			$body .= "</div>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_4\">\n";
			$body .= "<p class=\"p6 ft17\">Have a Merry Christmas & a Happy New Year!</p>\n";
			$body .= "</div>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"page_2\">\n";
			$body .= "<div id=\"dimg1\">\n";
			$body .= "<IMG src=\"http://eblasts.animeftw.tv/images/christmas2013/christmas-newsletter2x1.jpg\" id=\"img1\">\n";
			$body .= "</div>\n";
			$body .= "<div class=\"dclr\"></div>\n";
			$body .= "<div id=\"id_1\">\n";
			$body .= "<div id=\"id_1_1\">\n";
			$body .= "<p class=\"p6 ft18\">AnimeFTW.tv Season of Giving Winners</p>\n";
			$body .= "<p class=\"p6 ft19\">By robotman321</p>\n";
			$body .= "<p class=\"p18 ft14\">As many of you are aware, we have had an ongoing even on the site the month of December. If you are active in a week period AND have used the site’s features you are signed up to win a free month of advanced membership or Goodies!</p>\n";
			$body .= "<p class=\"p5 ft14\">The winners to this point are as follow:</p>\n";
			$body .= "<p class=\"p8 ft16\">Week 1:</p>\n";
			$body .= "<p class=\"p19 ft14\">jomega87 pslocks</p>\n";
			$body .= "<p class=\"p5 ft16\">Week 2:</p>\n";
			$body .= "<p class=\"p20 ft14\">Frocoz huysamen</p>\n";
			$body .= "<p class=\"p8 ft16\">Week 3:</p>\n";
			$body .= "<p class=\"p21 ft14\">Animejose dreanime2</p>\n";
			$body .= "<p class=\"p8 ft16\">Week 4:</p>\n";
			$body .= "<p class=\"p22 ft14\">Julz jollyrogers74</p>\n";
			$body .= "<p class=\"p23 ft14\">Make sure you Email <A href=\"mailto:support@animeftw.tv\"><SPAN class=\"ft20\">support@animeftw.tv </SPAN></A>today to confirm your account and get your prizes!.</p>\n";
			$body .= "<p class=\"p24 ft14\">As always, thank you THANK YOU for being part of the site, we do &lt;3 you all!</p>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_1_2\">\n";
			$body .= "<p class=\"p6 ft5\">Notes from Staff Continued..</p>\n";
			$body .= "<p class=\"p25 ft7\">Kinsfolk writes:</p>\n";
			$body .= "<p class=\"p26 ft21\">Hey guys its been a lot of fun working for all of you awesome fans and I hope that you'll continue to take care of me in the future.</p>\n";
			$body .= "<p class=\"p27 ft10\">We love Anime</p>\n";
			$body .= "<p class=\"p28 ft22\">I think a line is missing. Happy Holidays!</p>\n";
			$body .= "<p class=\"p29 ft7\">Topseli writes:</p>\n";
			$body .= "<p class=\"p26 ft23\">Many thanks to all site users for all the support and I hope it's been a good year for you! Now it's time to prepare for the future, so make your promises, set your goals and remember to aim high. Best luck for the year 2014!</p>\n";
			$body .= "<p class=\"p30 ft7\">Enic writes:</p>\n";
			$body .= "<p class=\"p31 ft21\">I'm looking forward to spend another great year here on AnimeFTW.tv and wish you all a Merry Christmas and a Happy New Year!</p>\n";
			$body .= "<p class=\"p27 ft7\">Issei302 writes:</p>\n";
			$body .= "<p class=\"p32 ft23\">Merry Christmas from Nathan (Issei302)!!! It's a pleasure being a contribute to FTW, working probably the best staff I've ever known. Have a great Christmas, and if anyone gets a death note, please feel free to send me a few pages.</p>\n";
			$body .= "</div>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_2\">";
			$body .= "<p class=\"p33 ft24\">FTW Entertainment LLC</p>\n";
			$body .= "<p class=\"p34 ft24\">Wishes you a</p>\n";
			$body .= "<p class=\"p35 ft24\">HAPPY HOLIDAYS!</p>\n";
			$body .= "<p class=\"p36 ft15\">PHOTO CAPTION:</p>\n";
			$body .= "<p class=\"p37 ft16\">Group of Santas</p>\n";
			$body .= "</div>\n";
			$body .= "<div id=\"id_3\">\n";
			$body .= "<p class=\"p6 ft25\">www.animeftw.tv | support@animeftw.tv | (312) <nobr>465-1161</nobr></p>\n";
			$body .= "</div>\n";
		}
		else
		{
		}
		if($_POST['update-type'] == '1')
		{
			// Anime Update
			$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\">You're receiving this email blast because you did not opt out of Anime Updates.</p>\n";
		}
		else if($_POST['update-type'] == '2')
		{
			// Admin notification
			$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\">You're receiving this email blast because you did not opt out of Admin Notifications.</p>\n";
		}
		else
		{
		}
		$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\"> Not interested? <a href=\"https://www.animeftw.tv/user/" . $Username . "/\" style=\"color: #0eb6ce; text-decoration: none;\">Opt out</a> of Notifications.</p>\n";
		$body .= "</div>\n";
		$body .= "</html>\n";
						
						
		$body .= "--$mime_boundary--\n\n"; 
		//$body = wordwrap($body,70);
		//away we go!
		//mail($to, $subject, $body, $headers);
		$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $headers, $body);
		//$SMTPChat = $SMTPMail->SendMail();
		$body = "";
		if($count == 100){
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
<?php
$now = date("r");
echo "Script Start: {$now} <br />\n";
if(isset($count)){
	echo "Found: {$count} results.";
}
echo $count;
?>
<table width="500px">
<tr><td>From :</td><td><input type="text" name="from"<? if($remember = TRUE){echo ' value="'.$fromUser.'"';}?> /></td></tr>
<tr><td>Subject :</td><td><input type="text" name="sub"<? if($remember = TRUE){echo ' value="'.$subject.'"';}?> /></td></tr>
<tr><td>Update Type</td><td> 
<select name="update-type" id="update-type">
	<option value="1"<? if($level == 1){echo ' selected="selected"';}?>>Anime Update</option>
	<option value="2"<? if($level == 2){echo ' selected="selected"';}?>>Admin Notification</option>
	<option value="3"<? if($level == 3){echo ' selected="selected"';}?>>Christmas Update</option>
	<option value="4"<? if($level == 4){echo ' selected="selected"';}?>>Kodi Addon Update</option>
	<option value="5"<? if($level == 5){echo ' selected="selected"';}?>>Active Users (2 weeks)</option>
</select></td></tr>`
<tr><td>Message :</td><td><textarea name="message" rows="8" cols="50" id="message"><? if(isset($msgBody)){echo $msgBody;}?></textarea></td></tr>
<tr><td></td><td><input type="submit" value=" Send " /></td></tr>
</table>
</form>
</body>
</html>
