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
			$this->manageCampaignTypes();
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
		$resultStatus = array(0 => "Queued", 1 => "Active", 2 => "Finished");
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
		$firstColumn = "table-column-25";
		if($whereClause != NULL){
			$where = " WHERE " . $whereClause;
			$firstColumn = "table-column-35";
		}
		// `added`, `updated`, `starts`, `status`, `title`, `contents`, `type`, `count`
		$query = "SELECT ${columns} FROM `eblast_campaigns`" . $where . $orderBy . " LIMIT " . $start . "," . $count;
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		echo '
			<div class="table-wrapper">';
		echo '
				<div class="table-row table-header" style="width:100%;">
					<div class="' . $firstColumn .' column-header" style="font-size:14px;">Title</div>';
		if($whereClause == NULL){
		echo '
					<div class="table-column-10 column-header">Status</div>';
		}
		echo '
					<div class="table-column-20 column-header">Added</div>
					<div class="table-column-20 column-header">Updated</div>
					<div class="table-column-10 column-header">Count</div>
					<div class="table-column-5 column-header">edit</div>
				</div>';
		if($count < 1){
			echo '<div align="center">No rows were available for this request.</div>';
		}
		else {
			$i=0;
			while($row = mysql_fetch_assoc($result)){
				echo '
				<div class="table-row'; 
				if($i % 2){
					echo ' row-even';
				}
				else {
					echo ' row-odd';
				}
				echo '">
					<div class="' . $firstColumn .'">' . $row['title'] . '</div>';
		if($whereClause == NULL){
		echo '
					<div class="table-column-10">' . $resultStatus[$row['status']] . '</div>';
		}
		echo '
					<div class="table-column-20">' . date("F j, Y, g:i a",$row['added']) . '</div>
					<div class="table-column-20">' . date("F j, Y, g:i a",$row['updated']) . '</div>
					<div class="table-column-10">' . $row['count'] . '</div>
					<div class="table-column-5"><a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=emails&stage=edit-campaign&id=' . $row['id'] . '\'); return false;">edit</a></div>
				</div>';
				$i++;
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
			$status = $row['status'];
			$type = $row['type'];
			if($status == 1 || $status == 2){
				echo '
					<div class="body-message">NOTICE: This eblast has progressed past the "Queued" state, editting is no longer available.</div>';
			}
			echo '
			<input type="hidden" name="id" value="' . $id . '" />
			<div class="table-row">
				<div class="table-column-10 column-right">Added</div>
				<div class="table-column-80 column-left">' . $added . '</div>
			<div>
			<div class="table-row">
				<div class="table-column-10 column-right">Updated</div>
				<div class="table-column-80 column-left">' . $updated . '</div>
			<div>
			<div class="table-row">
				<div class="table-column-10 column-right">Starts</div>
				<div class="table-column-80 column-left">' . $starts . '</div>
			<div>';
		}
		else if($formType == 'addCampaign'){
			$contents = ''; $title = '';$status = 0;$type = '';
			echo '
			<div class="table-row">
				<div class="table-column-10 column-right">Schedule<br /></div>
				<div class="table-column-50 column-left">
					<input type="text" name="starts" value="" /> (use: MM/DD/YYYY HH:MM formatting)
				</div>
			<div>';
		}
		else {
		}
		echo '
			<div class="table-row">
				<div class="table-column-10 column-right">Type</div>
				<div class="table-column-80 column-left">
					<select id="email-type" name="type">';
						foreach($this->array_eblastTypes() as $campaignTypes){
							$selected = '';
							if($type == $campaignTypes['id']){
								$selected = ' selected="selected"';
							}
							echo '<option value="' . $campaignTypes['id'] . '"' . $selected .'>' . $campaignTypes['name'] . '</option>';
						}
			echo '	
					</select>
				</div>
			</div>
			<div class="table-row">
				<div class="table-column-10 column-right">Title</div>
				<div class="table-column-80 column-left">
					<input type="text" name="title" value="' . $title . '" style="width:300px;" id="email-title" />
				</div>
			</div>
			<div class="table-row">
				<div class="table-column-10 column-right">Contents</div>
				<div class="table-column-80 column-left">
					<textarea name="contents" style="width:500px;height:150px;" id="contents-textarea">' . $contents . '</textarea>
				</div>
			</div>';
			if($formType == 'editCampaign' || $formType == 'addCampaign'){
				 echo '
			<div class="table-row">
				<div class="table-column-10 column-right">
					Preview:<br />';
				if($status == 0) {
					echo '
				<input type="button" name="update-preview" value="Update" id="preview-button-update" />';
				}
				echo '
				</div>
				<div class="table-column-80 column-left" style="height:400px;overflow:scroll;">';
				if($formType == 'editCampaign'){
					echo $this->emailPreview($contents);
				}
				else {
					echo ' <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
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
 				<table style="padding: 35px 0; background: #4b4b4b url(\'http://eblasts.animeftw.tv/images/bg_email.png\');" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
 				  <tbody><tr>
 					<td style="margin: 0; padding: 0; background:url(\'http://eblasts.animeftw.tv/images/bg_email.png\') ;" align="center">
 						<table style="font-family: Helvetica, Arial,sans-serif; background:#2a2a2a;" class="header" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
 							<tbody><tr>
 								<td style="padding: font-size: 0;
line-height: 0; height: 7px;" colspan="2" align="left" height="7" width="600"><img src="http://eblasts.animeftw.tv/images/bg_header.png" alt="headerbg"></td>
 							  </tr>
 							<tr>
 							<td style="font-size: 0px;" width="20">&nbsp;</td>
 							<td style="padding: 18px 0 10px;" align="left" width="580">
 								<h1 style="color: #47c8db; font: bold 32px Helvetica, Arial,sans-serif; margin: 0; padding: 0; line-height: 40px;"><a href="http://eblasts.animeftw.tv/link/VQ9VkVPPpXT5jgmpkS7EhNIFsGgM1861AuBVP7Aq4oFJFgxKzWM19X8ScCzxIAOkJ7GeNTWbgvWcXSc2y8Ef8ur2uMv4sCub8fO08FAMgUpoScWhyBrtVPrJ8LxPJF3HBdgNj16ATFjtPUgoQvAbVa" style="color: #0eb6ce; text-decoration: none;">AnimeFTW.tv</a></h1>
 								<p style="color: #c6c6c6; font: normal 12px Helvetica, Arial,sans-serif; margin: 0; padding: 0; line-height: 18px;">Only the best for the best Members..</p>
 							</td>
 						  </tr>
 						</tbody></table><!-- header-->
 						<table style="font-family: Helvetica, Arial,
sans-serif; background: #fff;" align="center" bgcolor="#fff" border="0" cellpadding="0" cellspacing="0" width="600">
 							<tbody><tr>
 							<td style="font-family:
Helvetica, Arial, sans-serif; padding: 20px 0 0;" class="content" align="left" valign="top" width="600">
 								<table style="color: #717171; font: normal 11px Helvetica, Arial, sans-serif;margin: 0; padding: 0;" border="0" cellpadding="0" cellspacing="0" width="600">
 								<tbody><tr>
 									<td style="font-size: 1px; line-height:1px;" width="21"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 									<td style="padding: 20px 0 0;" align="left">
 										<h2 style="color:#646464; font-weight: bold; margin: 0;padding: 0; line-height: 26px; font-size: 18px; font-family:Helvetica, Arial, sans-serif;" id="title-row-holder"></h2>
 									</td>
 									<td style="font-size: 1px; line-height:1px;" width="21"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 								</tr>
 								<tr>
 									<td style="font-size: 1px; line-height:1px;" width="21"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 									<td style="padding: 15px 0 15px;" valign="top" id="email-template-insert-point">
									
									</td><td style="font-size: 1px; line-height:1px;" width="21"><img src="http://eblasts.animeftw.tv/images/spacer.gif" alt="space" width="20"></td>
 								</tr>
 						</tbody></table>	
 							</td>
 						  </tr>
 							<tr>
 								<td style="padding: font-size: 0;line-height: 0; height: 3px;" colspan="2" align="left" height="3" width="600"><img src="http://eblasts.animeftw.tv/images/bg_bottom.png" alt="headerbg"></td>
 							  </tr>	
 						</tbody></table><!-- body -->
 						<table style="font-family: Helvetica, Arial,sans-serif; line-height: 10px;" class="footer" align="center" border="0" cellpadding="0" cellspacing="0" width="600"> 
 						<tbody><tr>
 							<td style="padding: 5px 0 10px; font-size:11px; color:#7d7a7a; margin: 0; line-height: 1.2;font-family:Helvetica, Arial, sans-serif;" align="center" valign="top">
 								<br>
 								<p style="font-size: 11px; color:#7d7a7a; margin: 0; padding:0; font-family: Helvetica, Arial, sans-serif;">You\'re receiving this email blast because you did not opt out of Admin Emails.</p>
 								<p style="font-size: 11px; color:#7d7a7a; margin: 0; padding:0; font-family: Helvetica, Arial, sans-serif;"> Not interested? <a href="http://eblasts.animeftw.tv/link/CwAoXD1xfaBlJkzBEodr0pw7nkVAkDnEGgLjg6DjunuVV91eJXTVIMNrsJWUMmAbMwXFSErHGiaxEdHnViw5o330VngkXrDHkrblg6Qg8KV2iJ5EevdUN3l49QCQrtee2JQxaHjq6ckDDlFyp3DUjD" style="color: #0eb6ce; text-decoration: none;">Opt out</a> of Future Messages.</p>
 							</td>
 						  </tr>
 						</tbody></table><!-- footer-->
 					</td>
 					
 				</tr>
 			</tbody></table>';
				}
				echo '
				</div>
			</div>';
			}
			echo '
			<input type="submit" name="submit" value="Submit"'; if($status == 2 || $status == 1){echo ' disabled="disabled"';} echo ' />
		</form>
		</div>
		<script type="text/javascript">
			$(function()
			{
				$(\'#contents-textarea\').redactor({
					focus: true,
					minHeight: 300
				});
				$("#preview-button-update").on("click", function(){
					$("#email-template-insert-point").html($("#contents-textarea").val());
					$("#title-row-holder").text($("#email-title").val());
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
		$body .= " 										<h2 style=\"color:#646464; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif; \" id=\"title-row-holder\">Update: ".$subject."</h2>\n";
		$body .= " 									</td>\n";
		$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
		$body .= " 								</tr>\n";
		$body .= " 								<tr>\n";
		$body .= " 									<td width=\"21\" style=\"font-size: 1px; line-height: 1px;\"><img src=\"http://eblasts.animeftw.tv/images/spacer.gif\" alt=\"space\" width=\"20\"></td>\n";
		$body .= " 									<td style=\"padding: 15px 0 15px;\"  valign=\"top\" id=\"email-template-insert-point\">\n";						
		
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
	
	private function array_eblastTypes($id = NULL){
		$query = "SELECT * FROM `eblast_type`";
		if($id != NULL && is_numeric($id)){
			$query .= " WHERE `id` = ${id}";
		}
		$query .= " ORDER BY `name`";
		$result = mysql_query($query);
		
		$returnArray = array();
		$i = 0;
		while($row = mysql_fetch_Assoc($result)){
			$returnArray[$i]['id'] = $row['id'];
			$returnArray[$i]['user_setting_id'] = $row['user_setting_id'];
			$returnArray[$i]['name'] = $row['name'];
			$returnArray[$i]['description'] = $row['description'];
			$i++;
		}
		return $returnArray;
	}
	
	# function manageCampaignTypes
	# used to manage the types of campaigns that can be sent out.
	private function manageCampaignTypes(){
		echo '<div class="body-message">NOTE: Only Manage these settings if you know what you are doing, these types link to permissions settings to properly link all pieces, if you have questions please contact Brad.</div>';
		if(!isset($_GET['edit']) && !isset($_GET['add'])){
			echo '
			<div class="table-wrapper">
				<div class="table-row table-header">
					<div class="table-column-5 column-header">ID</div>
					<div class="table-column-15 column-header">Name</div>
					<div class="table-column-70 column-header">Description</div>
					<div class="table-column-5 column-header">Actions</div>
				</div>';
			$i=0;
			foreach($this->array_eblastTypes() as $types){
				echo '
				<div class="table-row'; 
				if($i % 2){
					echo ' row-even';
				}
				else {
					echo ' row-odd';
				}
				echo '">
					<div class="table-column-5">' . $types['id'] . '</div>
					<div class="table-column-15">' . $types['name'] . '</div>
					<div class="table-column-70">' . $types['description'] . '</div>
					<div class="table-column-5"><a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=emails&stage=campaign-types&edit=' . $types['id'] . '\'); return false;">Edit</a></div>
				</div>';
				$i++;
			}
			echo '
			</div>';
		}
		else {
			// The edit or add actions are set.
			if(isset($_GET['add']) && !isset($_GET['edit'])){
				// adding a new type
				echo $this->campaignTypesForm(0);
			}
			else if (!isset($_GET['add']) && isset($_GET['edit'])){
				// editting an existing entry.
				echo $this->campaignTypesForm(1);
			}
			else {
				// Something somewhere went very wrong, thus they get nothing.
			}
		}
	}
	
	private function campaignTypesForm($type = 0){
		// 0 is the add form, 1 is the edit form.
		echo '<div class="table-wrapper">
			<form id="campaign-form">';
		if($type == 0){
			// add a new type.
			echo '
			<div class="table-row table-header">
				<div class="table-column-100 column-header">Add a new Campaign Type</div>
			</div>
			<input type="hidden" name="action" value="AddCampaignType" />';
		}
		else if($type == 1){
			// edit data..
			echo '
			<div class="table-row table-header">
				<div class="table-column-100 column-header">Edit a Campaign Type</div>
			</div>
			<input type="hidden" name="action" value="EditCampaignType" />
			<input type="hidden" name="id" value="' . $_GET['edit'] . '" />';
			$typeInformation = $this->array_eblastTypes($_GET['edit']);
			$name = $typeInformation[0]['name'];
			$description = $typeInformation[0]['description'];
			$user_setting_id = $typeInformation[0]['user_setting_id'];
		}
		else {
			// no idea.. just the catcher.
		}
		echo '
		<div class="table-row">
			<div class="table-column-10 column-right">Name</div>
			<div class="table-column-80 column-left">
				<input type="text" name="name" value="' . $name . '" />
			</div>
		<div>
		<div class="table-row">
			<div class="table-column-10 column-right">Description</div>
			<div class="table-column-80 column-left">
				<input type="text" name="description" value="' . $description . '" style="width:100%;" />
			</div>
		<div>
		<div class="table-row">
			<div class="table-column-10 column-right">User Setting Link</div>
			<div class="table-column-80 column-left">
			<span style="font-size:8px;">(This is used to map Capaign Types to User setting options, making a more autonomous collection system)</span><br />';
		echo '
			</div>
		<div>';
		echo '</form></div>'; // closes the table.
	}
}