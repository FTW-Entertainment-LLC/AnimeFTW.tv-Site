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
				<div class="table-column-5 column-right" style="display:inline-block;width:8.999999%;text-align:right;vertical-align:top;">Schedule<br /></div>
				<div class="table-column-5 column-left" style="display:inline-block;width:89.999999%;text-align:left;vertical-align:top;">
					<input type="text" name="starts" value="" /> (use: MM/DD/YYYY HH:MM formatting)
				</div>
			<div>';
		}
		else {
		}
		echo '
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-1 column-right" style="display:inline-block;width:8.999999%;text-align:right;vertical-align:top;">Title</div>
				<div class="table-column-9 column-left" style="display:inline-block;width:89.999999%;text-align:left;vertical-align:top;">
					<input type="text" name="title" value="' . $title . '" style="width:300px;" />
				</div>
			</div>
			<div class="table-row" style="padding:5px 0 5px 0;">
				<div class="table-column-1 column-right" style="display:inline-block;width:8.999999%;text-align:right;vertical-align:top;">Contents</div>
				<div class="table-column-9 column-left" style="display:inline-block;width:89.999999%;text-align:left;vertical-align:top;">
					<textarea name="contents" style="width:500px;height:150px;">' . $contents . '</textarea>
				</div>
			</div>
			<input type="submit" name="submit" value="Submit" />
		</form>
		</div>';
	}
}