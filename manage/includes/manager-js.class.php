<?php
/****************************************************************\
## FileName: manager-js.class.php									 
## Author: Brad Riemann										 
## Usage: Provides all Functionality for the Uploads Board
## Copywrite 2011-2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

include("/home/mainaftw/public_html/includes/classes/config.class.php");

class ManagerJS extends Config {

	public function __construct()
	{
		parent::__construct();
		$this->displayJS();
	}
	
	private function displayJS()
	{		
		header("Content-type: text/javascript");
		echo '//default tab on page refresh
		$(document).ready(function () {
		' . $this->defaultTab() . '
		});';
	}
	
	private function defaultTab()
	{
		// first we check if the manager-tab cooke was set, if it is, we need to set that as the default
		if(isset($_COOKIE['manage-tab']))
		{
			return '$("#nav-' . $_COOKIE['manage-tab'] . '").children("div").addClass("nav-item-active");
			$("#right-column").load("ajax.php?node=' . $_COOKIE['manage-tab'] . '");';
		}
		else
		{
			if($this->UserArray[2] == 5)
			{
				return '$("#nav-uploads").children("div").addClass("nav-item-active");
				$("#right-column").load("ajax.php?node=uploads");';
			}
			else
			{
				return '$("#nav-uploads").children("div").addClass("nav-item-active");
				$("#right-column").load("ajax.php?node=uploads");';
				
			}
		}
	}
}

$ManagerJS = new ManagerJS();