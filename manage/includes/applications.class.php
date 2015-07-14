<?php
/****************************************************************\
## FileName: applications.class.php								 
## Author: Brad Riemann								 
## Usage: Applications sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Applications extends Config {

	private $AppSettings = array();

	public function __construct()
	{
		parent::__construct(TRUE);
		$this->buildAppData(); // pulls together all of the data for applications.. not much.. but usable later in life
		if(isset($_GET['subnode']))
		{
			$this->buildSubNode();
		}
		else
		{
			echo '<div  class="body-container srow">';
			echo $this->applicationInterface();
			echo '</div>';
			echo '
			<script>
				$(document).ready(function () {
					$(".application-nav").on("click", function(){
						$(".application-nav").css("font-weight", "");
						$("#" + this.id).css("font-weight","bold");
						$("#application-content").load("ajax.php?node=applications&subnode=" + this.id);
						return false;
					});
				});
			</script>';
		}
	}
	
	private function buildAppData()
	{
		$query = "SELECT `id`, `name`, `value` FROM `" . $this->MainDB . "`.`settings` WHERE `id` = 7 OR `id` = 8";
		$result = mysql_query($query);
		while($row = mysql_fetch_assoc($result))
		{
			$this->AppSettings[$row['id']]['name'] = $row['name'];
			$this->AppSettings[$row['id']]['value'] = $row['value'];
		}
	}
		
	private function applicationInterface()
	{
		echo '<div align="center">Status: ' . $this->applicationStatus() . ' | <a href="#" class="application-nav" id="current-applications">Current Applications</a> | <a href="#" class="application-nav" id="past-applications">Past Applications</a></div>
		<div id="application-content">&nbsp;</div>';
	}
	
	private function applicationStatus()
	{
		if($this->AppSettings[7]['value'] == 1)
		{
			// Staff apps are active
			return '<span style="color:green">Active</span>';
		}
		else
		{
			return '<span style="color:red">In-active</span>';
		}
	}
	
	private function buildSubNode()
	{
		if($_GET['subnode'] == 'current-applications')
		{
			$this->showCurrentApplications();
		}
		else if($_GET['subnode'] == 'past-applications')
		{
			$this->showPastApplications();
		}
		else
		{
		}
	}
	
	private function showCurrentApplications()
	{
		$query = "SELECT `applications_submissions`.`id`, `applications_submissions`.`positionID`, `applications_submissions`.`company`, `applications_submissions`.`Age`, `applications_submissions`.`Status`, `applications_submissions`.`reqInformation`, `users`.`ID` FROM `" . $this->MainDB . "`.`applications_submissions`, `" . $this->MainDB . "`.`users` WHERE `users`.`Username`= `applications_submissions`.`username` AND `applications_submissions`.`appRound` = " . $this->AppSettings[8]['value'] . " ORDER BY `applications_submissions`.`id`";
		$result = mysql_query($query);
		
		echo '<div>
			<div class="table-row">
				<div style="font-weight:bold;">
					<div style="min-width:25px;display:inline-block;">&nbsp;</div>
					<div style="min-width:150px;display:inline-block;">Position</div>
					<div style="min-width:170px;display:inline-block;">Username</div>
					<div style="min-width:120px;display:inline-block;">Place</div>
					<div style="min-width:100px;display:inline-block;">Age</div>
					<div style="min-width:120px;display:inline-block;">Security Test</div>
					<div style="min-width:150px;display:inline-block;">Options</div>
				</div>';
		$i = 0;
		while($row = mysql_fetch_assoc($result))
		{	
			if($i % 2)
			{
				$style = ' style="padding:2px;background-color:#B8EAFA;"';
			}
			else
			{
				$style = ' style="padding:2px;"';
			}
			echo '
			<div class="table-row"' . $style . '>
				<div>
					<div style="min-width:25px;display:inline-block;"><a href="#" onClick="$(\'.user-submission\').hide();$(\'#' . $row['id'] . '-description\').show();return false;">+</a></div>
					<div style="min-width:150px;display:inline-block;">' . $row['positionID'] . '</div>
					<div style="min-width:170px;display:inline-block;">' . $this->formatUsername($row['ID']) . '</div>
					<div style="min-width:120px;display:inline-block;">' . $row['company'] . '</div>
					<div style="min-width:100px;display:inline-block;">' . $row['Age'] . '</div>
					<div style="min-width:120px;display:inline-block;">' . $this->displaySecurityTest($row['ID']) . '</div>
					<div style="min-width:150px;display:inline-block;">' . $this->applicationOptions($row['id'],$row['Status']) . '</div>
				</div>
				<div id="' . $row['id'] . '-description" class="user-submission" style="display:none;">
				<div style="padding:5px;">' . nl2br($row['reqInformation']) . '</div>
				</div>
			</div>';
			$i++;
		}
		echo '</div>';
	}
	
	private function showPastApplications()
	{
	}
	
	private function applicationOptions($app_id,$status)
	{
		$output = '
		<select class="application_status" id="app-status-' . $app_id . '">
			<option value="Pending"';
			if($status == 'Pending')
			{
				$output .= ' selected="selected"';
			}
			$output .= '>Pending</option>
			<option value="Under Review"';
			if($status == 'Under Review')
			{
				$output .= ' selected="selected"';
			}
			$output .= '>Under Review</option>
			<option value="Accepted"';
			if($status == 'Accepted')
			{
				$output .= ' selected="selected"';
			}
			$output .= '>Accepted</option>
			<option value="Denied"';
			if($status == 'Denied')
			{
				$output .= ' selected="selected"';
			}
			$output .= '>Denied</option>
		</select>';
		return $output;
	}
	
	private function displaySecurityTest($uid)
	{
		$query = "SELECT `id`, `date` FROM `" . $this->MainDB . "`.`applications_sectests` WHERE `uid` = " . $uid;
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		if($count > 0)
		{
			$row = mysql_fetch_assoc($result);
			return '<span title="Test Uploaded on ' . date('F j, Y g:i',$row['date']) . '">Security Test</span>';
		}
		else
		{
			return '<span style="color:gray;">Security Test</span>';
		}
	}
}