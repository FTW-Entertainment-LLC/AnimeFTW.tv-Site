<?php
/****************************************************************\
## FileName: emails.class.php								 
## Author: Brad Riemann								 
## Usage: Emails sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Emails extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		$link = 'ajax.php?node=emails'; // base url
		echo '<div  class="body-container">';	
		echo '
			<div>
				<div class="email-container"';
				if(isset($_GET['stage'])){
					echo ' style="border-bottom:1px solid #dbdbdb;"';
				}
				echo '>
					<div style="padding:5px;display:inline-block;vertical-align:top;">
						<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Campaigns</div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'all-campaigns'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=all-campaigns\'); return false;">All Campaigns</a></div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'active-campaigns'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=active-campaigns\'); return false;">Active Campaigns</a></div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'finished-campaigns'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=finished-campaigns\'); return false;">Finished Campaigns</a></div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'add-campaigns'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=add-campaigns\'); return false;">Add Campaigns</a></div>
					</div>
					<div style="padding:5px;display:inline-block;vertical-align:top;">
						<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Settings</div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'campaign-types'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=campaign-types\'); return false;">Campaign Types</a></div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'subscriber-management'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=subscriber-management\'); return false;">Subscriber Management</a></div>
					</div>
					<div style="padding:5px;display:inline-block;vertical-align:top;">
						<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Logs</div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'email-logs'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=email-logs\'); return false;">Email Logs</a></div>
						<div style="padding:2px;'; if(isset($_GET['stage']) && $_GET['stage'] == 'error-logs'){echo 'font-weight:bold;';} echo '"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=error-logs\'); return false;">Error Logs</a></div>
					</div>
				</div>
			</div>';
		if(isset($_GET['stage']) && $_GET['stage'] == 'all-campaigns') {
			echo '<div id="stage-container">';
			$this->resultsOutput("`id`, `added`, `updated`, `starts`, `status`, `title`, `contents`, `type`, `count`",NULL);
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'active-campaigns') {
			echo '<div id="stage-container">';
			$this->resultsOutput("`id`, `added`, `updated`, `starts`, `status`, `title`, `contents`, `type`, `count`","`status` = 1");
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'finished-campaigns') {
			echo '<div id="stage-container">';
			$this->resultsOutput("`id`, `added`, `updated`, `starts`, `status`, `title`, `contents`, `type`, `count`","`status` = 2");
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'add-campaigns') {
			echo '<div id="stage-container">';
			$this->emailsForm('addCampaign');
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'edit-campaign'){
			echo '<div id="stage-container">';
			$this->emailsForm('editCampaign');
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'campaign-types') {
			echo '<div id="stage-container">';
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'subscriber-management') {
			echo '<div id="stage-container">';
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'email-logs') {
			echo '<div id="stage-container">';
			echo '</div>';
		}
		else if(isset($_GET['stage']) && $_GET['stage'] == 'error-logs') {
			echo '<div id="stage-container">';
			echo '</div>';
		}
		else {
			if(isset($_GET['stage'])){
				echo '<div id="stage-container"> Your request was invalid, please.. stop that..</div>';
			}
		}
		echo '</div>';
	}
	
	private function resultsOutput($columns,$whereClause = NULL){
		// Set an order by clause up.
		$orderBy = " ORDER BY `updated` DESC";
		if(isset($_GET['order'])){
			$by = explode('|',$_GET['order']);
			$orderBy = " ORDER BY `" . $by[0] . "` " . $by[1];
		}
			
		// Set the count limit clause up
		$count = 30;
		if(isset($_GET['count']) && is_numeric($_GET['count'])){
			$count = $_GET['count'];
		}
			
		// set up the position up
		$start = 0;
		if(isset($_GET['start']) && is_numeric($_GET['start'])){
			$start = $_GET['start'];
		}
		
		// Where clause manipulation
		$where = "";
		if($whereClause != NULL){
			$where = " WHERE " . $whereClause;
		}
		// `added`, `updated`, `starts`, `status`, `title`, `contents`, `type`, `count`
		$query = "SELECT ${columns} FROM `eblast_campaigns`" . $where . $orderBy . " LIMIT " . $start . "," . $count;
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		echo '
			<div class="table-wrapper">';
		echo '
				<div class="table-row table-header" style="width:100%;">
					<div class="table-column-2 column-header" style="display:inline-block;width:15.66666666666667%;font-size:14px;">Title</div>
					<div class="table-column-2 column-header" style="display:inline-block;width:15.66666666666667%;font-size:14px;">Status</div>
					<div class="table-column-2 column-header" style="display:inline-block;width:15.66666666666667%;font-size:14px;">Added</div>
					<div class="table-column-2 column-header" style="display:inline-block;width:15.66666666666667%;font-size:14px;">Updated</div>
					<div class="table-column-2 column-header" style="display:inline-block;width:15.66666666666667%;font-size:14px;">Count</div>
					<div class="table-column-2 column-header" style="display:inline-block;width:15.66666666666667%;font-size:14px;">edit</div>
				</div>';
		if($count < 1){
			echo '<div align="center">No rows were available for this request.</div>';
		}
		else {
			while($row = mysql_fetch_assoc($result)){
				echo '
				<div class="table-row" style="padding:5px 0 5px 0;">
					<div class="table-column-2" style="display:inline-block;width:15.66666666666666%">' . $row['title'] . '</div>
					<div class="table-column-2" style="display:inline-block;width:15.66666666666666%">' . $row['status'] . '</div>
					<div class="table-column-2" style="display:inline-block;width:15.66666666666666%">' . $row['added'] . '</div>
					<div class="table-column-2" style="display:inline-block;width:15.66666666666666%">' . $row['updated'] . '</div>
					<div class="table-column-2" style="display:inline-block;width:15.66666666666666%">' . $row['count'] . '</div>
					<div class="table-column-2" style="display:inline-block;width:15.66666666666666%"><a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=emails&stage=edit-campaign&id=' . $row['id'] . '\'); return false;">edit</a></div>
				</div>';
			}
		}
		echo '
			</div>';
	}
	
	private function emailsForm($formType){
		
		// Change selection by formType
		// editCampaign, addCampaign
		echo '
			<div class="table-wrapper">
				<form>';
		if($formType == 'editCampaign'){
			if(!isset($_GET['id'])){
				echo '<div align="center">There was an issue with the request.</div>';
				exit;
			}
			$query = "SELECT * FROM `eblast_campaigns` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'";
			$result = mysql_query($query);
			
			if(!$result){
				echo 'There was an issue with the query.';
				exit;
			}
			$row = mysql_fetch_assoc($result);
			
			$id = $row['id'];
			$added = date("F j, Y, g:i a",$row['added']);
			$updated = date("F j, Y, g:i a",$row['updated']);
			$starts = date("F j, Y, g:i a",$row['starts']);
			$title = $row['title'];
			$contents = $row['contents'];
			echo '
			<input type="hidden" name="id" value="' . $id . '" />
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-5 column-right" style="display:inline-block;width:8.999999%;text-align:right;vertical-align:top;">Added</div>
				<div class="table-column-5 column-left" style="display:inline-block;width:89.999999%;text-align:left;vertical-align:top;">' . $added . '</div>
			<div>
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-5 column-right" style="display:inline-block;width:8.999999%;text-align:right;vertical-align:top;">Updated</div>
				<div class="table-column-5 column-left" style="display:inline-block;width:89.999999%;text-align:left;vertical-align:top;">' . $updated . '</div>
			<div>
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-5 column-right" style="display:inline-block;width:8.999999%;text-align:right;vertical-align:top;">Starts</div>
				<div class="table-column-5 column-left" style="display:inline-block;width:89.999999%;text-align:left;vertical-align:top;">' . $starts . '</div>
			<div>';
		}
		else if($formType == 'addCampaign'){
			$contents = ''; $title = '';
			echo '
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-1 column-right" style="display:inline-block;width:9.999997%;text-align:right;vertical-align:top;">Schedule<br /></div>
				<div class="table-column-5 column-left" style="display:inline-block;width:49.999985%;text-align:left;vertical-align:top;">
					<input type="text" name="starts" value="" /> (use: MM/DD/YYYY HH:MM formatting)
				</div>
			<div>';
		}
		else {
		}
		echo '
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-1 column-right" style="display:inline-block;width:9.999997%;text-align:right;vertical-align:top;">Title</div>
				<div class="table-column-9 column-left" style="display:inline-block;width:79.999976%;text-align:left;vertical-align:top;">
					<input type="text" name="title" value="' . $title . '" style="width:300px;" />
				</div>
			</div>
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-1 column-right" style="display:inline-block;width:9.999997%;text-align:right;vertical-align:top;">Contents</div>
				<div class="table-column-9 column-left" style="display:inline-block;width:79.999976%;text-align:left;vertical-align:top;">
					<textarea name="contents" style="width:500px;height:150px;" id="contents-textarea">' . $contents . '</textarea>
				</div>
			</div>';
			if($formType == 'editCampaign'){
				 echo '
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-1 column-right" style="display:inline-block;width:9.999997%;text-align:right;vertical-align:top;">Preview:</div>
				<div class="table-column-9 column-left" style="display:inline-block;width:79.999976%;text-align:left;vertical-align:top;height:500px;overflow:scroll;;">
					' . $this->emailPreview($contents) . '
				</div>
			</div>';
			}
			echo '
			<input type="submit" name="submit" value="Submit" />
		</form>
		</div>
		<script type="text/javascript">
			$(function()
			{
				$(\'#contents-textarea\').redactor({
					focus: true,
					minHeight: 300
				});
			});
		</script>';
	}
	
	private function emailPreview($msgBody){
		
		$mime_boundary = "----FTW_ENTERTAINMENT_LLC----".md5(time());
		# -=-=-=- HTML EMAIL PART
		$body = '';
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
		$body = wordwrap($body,70);
		return $body;
	}
}