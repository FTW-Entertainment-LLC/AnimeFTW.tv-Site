<?php
			include 'config.php';
			include 'newsOpenDb.php';
error_reporting(E_ALL);
if(@$_GET['link'])
{
	//Lets get the link's details...
	$query  = "SELECT id, normalLink FROM elinks WHERE enqryptLink='".$_GET['link']."'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	$eLinkID = $row['id'];
	$normalLink = $row['normalLink'];
	
	//Alright, insert a new row for the referrer
	/*$query = sprintf("INSERT INTO ereferals (elinkId, date, ip) VALUES ('%s', '%s', '%s')",
		mysql_real_escape_string($eLinkID, $conn),
		mysql_real_escape_string(time(), $conn),
		mysql_real_escape_string($_SERVER['REMOTE_ADDR'], $conn));
	mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
	*/
	//Send them to their link!!!!
	if($normalLink == ''){
		//echo $query;
	}
	else {
		header( "location: ".$normalLink);
		exit;
	}
}
else if(@$_GET['form']){
	if($_POST){
	$comment = mysql_real_escape_string($_POST['comment']);
	$query = sprintf("INSERT INTO submissions (ip, browser, submission) VALUES ('%s', '%s', '%s')",
		mysql_real_escape_string($_SERVER['REMOTE_ADDR'], $conn),
		mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'], $conn),
		mysql_real_escape_string($comment, $conn));
	mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
		echo '<html lang="en">
 <head>
 <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
 <title>AnimeFTW Announcements</title>
 			<style type="text/css">
 			a:hover { text-decoration: none !important; }
 			.header h1 {color: #47c8db; font: bold 32px Helvetica, Arial,
sans-serif; margin: 0; padding: 0; line-height: 40px;}
 			.header p {color: #c6c6c6; font: normal 12px Helvetica, Arial,
sans-serif; margin: 0; padding: 0; line-height: 18px;}
 			.content h2 {color:#646464; font-weight: bold; margin: 0; padding:
0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial,
sans-serif;  }
 			.content p {color:#767676; font-weight: normal; margin: 0;
padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica,
Arial, sans-serif;}
 			.content a {color: #0eb6ce; text-decoration: none;}
 			.footer p {font-size: 11px; color:#7d7a7a; margin: 0; padding: 0;
font-family: Helvetica, Arial, sans-serif;}
 			.footer a {color: #0eb6ce; text-decoration: none;}
 			</style>
			<meta http-equiv="refresh" content="5;url=http://www.animeftw.tv/" /> 
 		  </head>
 		  <body style="margin: 0; padding: 0; background: #4b4b4b
url(\'http://eblasts.animeftw.tv/images/bg_email.png\');"
bgcolor="#4b4b4b">
<br />Thank you for your submission, we are forwarding you to the index page. If you are not redirected in 5 seconds please <a href="http://www.animeftw.tv/">Click Here</a><br />
</body>
</html>';
	}
	else {
	echo ' <html lang="en">
 <head>
 <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
 <title>AnimeFTW Announcements</title>
 			<style type="text/css">
 			a:hover { text-decoration: none !important; }
 			.header h1 {color: #47c8db; font: bold 32px Helvetica, Arial,
sans-serif; margin: 0; padding: 0; line-height: 40px;}
 			.header p {color: #c6c6c6; font: normal 12px Helvetica, Arial,
sans-serif; margin: 0; padding: 0; line-height: 18px;}
 			.content h2 {color:#646464; font-weight: bold; margin: 0; padding:
0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial,
sans-serif;  }
 			.content p {color:#767676; font-weight: normal; margin: 0;
padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica,
Arial, sans-serif;}
 			.content a {color: #0eb6ce; text-decoration: none;}
 			.footer p {font-size: 11px; color:#7d7a7a; margin: 0; padding: 0;
font-family: Helvetica, Arial, sans-serif;}
 			.footer a {color: #0eb6ce; text-decoration: none;}
 			</style>
 		  </head>
 		  <body style="margin: 0; padding: 0; background: #4b4b4b
url(\'http://eblasts.animeftw.tv/images/bg_email.png\');"
bgcolor="#4b4b4b">
 				<table cellpadding="0" cellspacing="0" border="0" align="center"
width="100%" style="padding: 35px 0; background: #4b4b4b
url(\'http://eblasts.animeftw.tv/images/bg_email.png\');">
 				  <tr>
 					<td align="center" style="margin: 0; padding: 0; background:
url(\'http://eblasts.animeftw.tv/images/bg_email.png\') ;" >
 						<table cellpadding="0" cellspacing="0" border="0"
align="center" width="600" style="font-family: Helvetica, Arial,
sans-serif; background:#2a2a2a;" class="header">
 							<tr>
 								<td width="600" align="left" style="padding: font-size: 0;
line-height: 0; height: 7px;" height="7" colspan="2"><img
src="http://eblasts.animeftw.tv/images/bg_header.png" alt="header
bg"></td>
 							  </tr>
 							<tr>
 							<td width="20"style="font-size: 0px;">&nbsp;</td>
 							<td width="580" align="left" style="padding: 18px 0 10px;">
 								<h1 style="color: #47c8db; font: bold 32px Helvetica, Arial,
sans-serif; margin: 0; padding: 0; line-height: 40px;"><a
href="http://eblasts.animeftw.tv/link/VQ9VkVPPpXT5jgmpkS7EhNIFsGgM1861AuBVP7Aq4oFJFgxKzWM19X8ScCzxIAOkJ7GeNTWbgvWcXSc2y8Ef8ur2uMv4sCub8fO08FAMgUpoScWhyBrtVPrJ8LxPJF3HBdgNj16ATFjtPUgoQvAbVa"
style="color: #0eb6ce; text-decoration: none;">AnimeFTW.tv</a></h1>
 								<p style="color: #c6c6c6; font: normal 12px Helvetica, Arial,
sans-serif; margin: 0; padding: 0; line-height: 18px;">Only the best
for the best Members...</p>
 							</td>
 						  </tr>
 						</table><!-- header-->
 						<table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; background: #fff;" bgcolor="#fff">
 							<tr>
 							<td width="600" valign="top" align="left" style="font-family: Helvetica, Arial, sans-serif; padding: 20px 0 0;" class="content">
 								<table cellpadding="0" cellspacing="0" border="0" style="color: #717171; font: normal 11px Helvetica, Arial, sans-serif;margin: 0; padding: 0;" width="600">
 								<tr>
 									<td width="21" style="font-size: 1px; line-height:1px;"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 									<td style="padding: 0;  font-family: Helvetica, Arial, sans-serif; background:url(\'http://eblasts.animeftw.tv/images/bg_date_wide.png\') no-repeatleft top; height:20px; line-height: 20px;"  align="center" width="558"height="20">
 										<h3 style="color:#666; font-weight: bold; text-transform:uppercase; margin: 0; padding: 0; line-height: 10px; font-size:10px;"></h3>
 									</td>
 									<td width="21" style="font-size: 1px; line-height:1px;"><img src="http://eblasts.animeftw.tv/images/spacer.gif"alt="space" width="20"></td>
 								</tr>
 								<tr>
 									<td width="21" style="font-size: 1px; line-height:1px;"><img src="http://eblasts.animeftw.tv/images/spacer.gif"alt="space" width="20"></td>
 									<td style="padding: 20px 0 0;" align="left">
 										<h2 style="color:#646464; font-weight: bold; margin: 0;padding: 0; line-height: 26px; font-size: 18px; font-family:Helvetica, Arial, sans-serif; ">Come back to us, we have missed you!!!</h2>
 									</td>
 									<td width="21" style="font-size: 1px; line-height:1px;"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 								</tr>
 								<tr>
 									<td width="21" style="font-size: 1px; line-height:
1px;"><img src="http://eblasts.animeftw.tv/images/spacer.gif"
alt="space" width="20"></td>
 									<td style="padding: 15px 0 15px;"  valign="top">
										We\'ve missed you!!! <br /><br />Our Records show that you have been inactive on the site?!<br /><br />We are constantly making additions to the video library and want your opinion! Since you haven\'t been active we want to know why. Was it our Hair? Was it our Clothes? What about our content, did we not have what you wanted??? Here is your chance. By filling out the following form you will be able to tell us why you have not been active on the site.<br /><br />
										<form action="#" method="post">
											<textarea id="comment" name="comment" style="width:550px;height:120px;"></textarea><br />
											</select><input type="submit" class="submit" value=" Submit Comment " />
										</form>
									</td><td width="21" style="font-size:1px; line-height: 1px;"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 								</tr>
 						</table>	
 							</td>
 						  </tr>
 							<tr>
 								<td width="600" align="left" style="padding: font-size: 0;
line-height: 0; height: 3px;" height="3" colspan="2"><img
src="http://eblasts.animeftw.tv/images/bg_bottom.png" alt="header
bg"></td>
 							  </tr>	
 						</table><!-- body -->
 						<table cellpadding="0" cellspacing="0" border="0"
align="center" width="600" style="font-family: Helvetica, Arial,
sans-serif; line-height: 10px;" class="footer"> 
 						<tr>
 							<td align="center" style="padding: 5px 0 10px; font-size:
11px; color:#7d7a7a; margin: 0; line-height: 1.2;font-family:
Helvetica, Arial, sans-serif;" valign="top">
 								<br>
 								<p style="font-size: 11px; color:#7d7a7a; margin: 0; padding:
0; font-family: Helvetica, Arial, sans-serif;">You are receiving this email because we missed you and want you back!</p>
 								<p style="font-size: 11px; color:#7d7a7a; margin: 0; padding:
0; font-family: Helvetica, Arial, sans-serif;"> Not interested? <a
href="http://eblasts.animeftw.tv/link/CwAoXD1xfaBlJkzBEodr0pw7nkVAkDnEGgLjg6DjunuVV91eJXTVIMNrsJWUMmAbMwXFSErHGiaxEdHnViw5o330VngkXrDHkrblg6Qg8KV2iJ5EevdUN3l49QCQrtee2JQxaHjq6ckDDlFyp3DUjD"
style="color: #0eb6ce; text-decoration: none;">Unsubscribe from future pleas.</a>
instantly.</p>
 							</td>
 						  </tr>
 						</table><!-- footer-->
 					</td>
 					</td>
 				</tr>
 			</table>
 		  </body>
 		</html>
';
	}
}
else {
	//for everyone else, you suck now go to the regular site..
	header( "location: http://www.animeftw.tv/");
}
?>