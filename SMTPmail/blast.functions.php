<?php
//function script for the 


function email_type($subject,$msgBody,$type){
	$body = "";
	if($type == 'announcement'){
		# -=-=-=- TEXT EMAIL PART
						$body .= "--$mime_boundary\r\n";
						$body .= "Content-Type: text/plain; charset=UTF-8\n";
						$body .= "Content-Transfer-Encoding: 8bit\n\n";
						$body .=  "AnimeFTW.tv News!\n\n";
						$body .=  "Read this Episode announcment in your browser http://www.animeftw.tv/forums/index.php?forum=9&thread=1861&s=0\n\n";
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
						$body .= " 										<h2 style=\"color:#646464; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif; \">This weeks, Mid-Week Episode update!</h2>\n";
						$body .= " 									</td>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 								</tr>\n";
						$body .= " 								<tr>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
						$body .= " 									<td style=\"padding: 15px 0 15px;\"  valign=\"top\">\n";
						$body .= " 										<p style=\"color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif; \">Hello everyone! We've done some updates to the ongoing series! Here they are!!!</p><br>\n";
						$body .= " 							<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"558\">\n";
						$body .= " 										<tr>\n";
						$body .= " 											<td valign=\"top\">\n";		
						$body .= " 												<p style=\"color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif; \">\n";
						$body .= " 													<a href=\"http://eblasts.animeftw.tv/link/dJ9VyiGlCr01ySCsGF1n3Br2qDfyOX8ebMmJ7xuaagVzlLuMehG2toC0Ia4AQhh0wA00mjOsH9krg232y80B7EmymjpfeT6LEpNxk1WeqNcK5iTP51XJvBffdNsV7w6rCAkF1SE40uxmBVkUmOh0Df\" style=\"color: #0eb6ce; text-decoration: none;\"><strong>Bleach</strong></a><strong>, Episode 295.<br /> Titled: It`s All A Trap... Engineered Bonds! </strong>i <br />\n";
						$body .= " 													Ichigo succeeds in wounding Aizen with his Hollowfied Getsugatensho attack. But to his surprise, the Hogyoku instantly heals Aizen’s wound. Aizen then reveals an even more shocking truth to Ichigo. <br /><a href=\"http://eblasts.animeftw.tv/link/OPpsi8oEC81Abk9F9QVu9jB6cH7evETkNB5DUHWM0TvVkfFumxhlcSIJM8Sh2WN0g99DXrtBX6fhx4k4JET3mHe9npjaxBoKwHLji8Eb4ebTMIeGuFT4mFe9kdloyHrNn5TxmCqQSBCPPuBsdOEeVp\" style=\"color: #0eb6ce; text-decoration: none;\">Watch Episode 295</a>\n";
						$body .= " 											  </p>\n";
						$body .= " 											</td>\n";
						$body .= " 											<td valign=\"top\" style=\"padding: 0 0 20px 20px\">\n";
						$body .= " 												<img src=\"http://eblasts.animeftw.tv/images/bleach295.png\" style=\"border: 1px solid #e9e9e9\" alt=\"\"><br>\n";
						$body .= " 												<p style=\"font-family: Helvetica, Arial, sans-serif; background: #e9e9e9; color:#8f8f8f; margin: 0; padding:0; font-size: 11px; text-align: center; height: 25px; line-height: 25px\">Fireball......</p>\n";
						$body .= " 											</td>\n";
						$body .= " 										</tr>\n";
						$body .= " 										</table>\n";
						$body .= " 										<img src=\"http://eblasts.animeftw.tv/images/divider_wide.png\" alt=\"\" style=\"border-top: 10px solid #fff;width: 558px\">We are currently working to finish the rest of the series that are airing, once they are finalized this weekend we will release another email linking to everything!</td>\n";
						$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
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
						$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\"> Not interested? <a href=\"http://eblasts.animeftw.tv/link/CwAoXD1xfaBlJkzBEodr0pw7nkVAkDnEGgLjg6DjunuVV91eJXTVIMNrsJWUMmAbMwXFSErHGiaxEdHnViw5o330VngkXrDHkrblg6Qg8KV2iJ5EevdUN3l49QCQrtee2JQxaHjq6ckDDlFyp3DUjD\" style=\"color: #0eb6ce; text-decoration: none;\">Unsubscribe</a> instantly.</p>\n";
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
	if($type == 'update'){
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
						/*$body .= " 										<p style=\"color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif; \">Hello everyone! We've done some updates to the ongoing series! Here they are!!!</p><br>\n";
						$body .= " 							<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"558\">\n";
						$body .= " 										<tr>\n";
						$body .= " 											<td valign=\"top\">\n";		
						$body .= " 												<p style=\"color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif; \">\n";
						$body .= " 													<a href=\"http://eblasts.animeftw.tv/link/dJ9VyiGlCr01ySCsGF1n3Br2qDfyOX8ebMmJ7xuaagVzlLuMehG2toC0Ia4AQhh0wA00mjOsH9krg232y80B7EmymjpfeT6LEpNxk1WeqNcK5iTP51XJvBffdNsV7w6rCAkF1SE40uxmBVkUmOh0Df\" style=\"color: #0eb6ce; text-decoration: none;\"><strong>Bleach</strong></a><strong>, Episode 295.<br /> Titled: It`s All A Trap... Engineered Bonds! </strong>i <br />\n";
						$body .= " 													Ichigo succeeds in wounding Aizen with his Hollowfied Getsugatensho attack. But to his surprise, the Hogyoku instantly heals Aizen’s wound. Aizen then reveals an even more shocking truth to Ichigo. <br /><a href=\"http://eblasts.animeftw.tv/link/OPpsi8oEC81Abk9F9QVu9jB6cH7evETkNB5DUHWM0TvVkfFumxhlcSIJM8Sh2WN0g99DXrtBX6fhx4k4JET3mHe9npjaxBoKwHLji8Eb4ebTMIeGuFT4mFe9kdloyHrNn5TxmCqQSBCPPuBsdOEeVp\" style=\"color: #0eb6ce; text-decoration: none;\">Watch Episode 295</a>\n";
						$body .= " 											  </p>\n";
						$body .= " 											</td>\n";
						$body .= " 											<td valign=\"top\" style=\"padding: 0 0 20px 20px\">\n";
						$body .= " 												<img src=\"http://eblasts.animeftw.tv/images/bleach295.png\" style=\"border: 1px solid #e9e9e9\" alt=\"\"><br>\n";
						$body .= " 												<p style=\"font-family: Helvetica, Arial, sans-serif; background: #e9e9e9; color:#8f8f8f; margin: 0; padding:0; font-size: 11px; text-align: center; height: 25px; line-height: 25px\">Fireball......</p>\n";
						$body .= " 											</td>\n";
						$body .= " 										</tr>\n";
						$body .= " 										</table>\n";
						$body .= " 										<img src=\"http://eblasts.animeftw.tv/images/divider_wide.png\" alt=\"\" style=\"border-top: 10px solid #fff;width: 558px\">We are currently working to finish the rest of the series that are airing, once they are finalized this weekend we will release another email linking to everything!\n";*/
						
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
						$body .= " 								<p style=\"font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;\"> Not interested? <a href=\"http://eblasts.animeftw.tv/link/CwAoXD1xfaBlJkzBEodr0pw7nkVAkDnEGgLjg6DjunuVV91eJXTVIMNrsJWUMmAbMwXFSErHGiaxEdHnViw5o330VngkXrDHkrblg6Qg8KV2iJ5EevdUN3l49QCQrtee2JQxaHjq6ckDDlFyp3DUjD\" style=\"color: #0eb6ce; text-decoration: none;\">Unsubscribe</a> instantly.</p>\n";
						$body .= " 							</td>\n";
						$body .= " 						  </tr>\n";
						$body .= " 						</table><!-- footer-->\n";
						$body .= " 					</td>\n";
						$body .= " 					</td>\n";
						$body .= " 				</tr>\n";
						$body .= " 			</table>\n";
						$body .= " 		  </body>\n";
						$body .= " 		</html>\n";
						
						# -=-=-=- FINAL BOUNDARY 	
						$body .= "--$mime_boundary--\n\n"; 
						$body = wordwrap($body,70);
	}	
	if($type == 'pm'){
		/*$body =  "Hi $toUsername,<br /><br />\n";
		$body .= "$fromUsername has sent you a site PM entitled: $msgSubject <br />\n"; 
		$body .= "--------------------------------------------------------------------------------<br />\n";
		$body .= "$msgBody <br />\n";
		$body .= "--------------------------------------------------------------------------------<br />\n";
		$body .= "You can view this message here: <br />\n";
		$body .= "<a href='http://www.animeftw.tv/messages/view/$msgId'>http://www.animeftw.tv/messages/view/$msgId</a>\n";*/
						
		//$body .= "<br /><br /><i>If you wish to opt out of PM notifications please <a href='http://www.animeftw.tv/edit/notifications'>edit</a> your notification methods</i><br />\n";
	}
	return $body;
}
?>