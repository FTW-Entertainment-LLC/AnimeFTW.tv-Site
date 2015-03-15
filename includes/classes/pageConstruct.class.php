<?php
########################################################
# Content Constructor Class for the AnimeFTW.tv Website
# Copyright 2008-2014, FTW Entertainment LLC
# 	~Written by Brad Riemann~
# These sets of classes were developed for the
# the specific page setup for the AnimeFTW.tv LLC Website
########################################################

class constructPage extends Config {
	var $page, $content, $ptype, $pname, $pseoname, $mysqli, $currentversion, $rootdir, $PageColumns, $PageArray;
	
	
	#----------------------------------------------------------------
	# function __construct
	# Initial function that calls when the Class is called.
	# @public
	#----------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
		
		$this->PageColumns = array("id", "name", "page_title", "seoname", "type", "template");
		
		// Parse the bad out of the URl.
		$this->GetPageDetails();
		
		// We construct an array so that everyone gets what they want.
		$this->BuildPages();
	}
	
	#----------------------------------------------------------------
	# function GetPageDetails
	# Built to be called after the DB_Con function, to build details about the page.
	# @private
	#----------------------------------------------------------------
	
	private function GetPageDetails()
	{
		$ReqURL = $_SERVER['REQUEST_URI']; // Requested URL we have to Parse
		$trimmed = trim($ReqURL,"/");
		$this->page = $trimmed;
	}
	
	#----------------------------------------------------------------
	# function BuildPage
	# Puts together a multi dimensional array for all of the pages.
	# @private
	#----------------------------------------------------------------
	
	private function BuildPages()
	{
		// SQL Query
		$sql = "SELECT " . implode('`, `', $this->PageColumns) . " FROM `content_page` ORDER BY id";
		
		// if anything happens, we bail.
		if(!$result = $this->mysqli->query($sql))
		{
			die('There was an error running the query [' . $this->mysqli->error . ']');
		}
		
		$pagearray = array();
		$i = 0;
		while($row = $result->fetch_assoc())
		{
			for($r=0; $r < 6; $r++)
			{
				$pagearray[$row['seoname']][$this->PageColumns[$r]] = $row[$this->PageColumns[$r]];				
			}
			$i++;
		}
		$this->PageArray = $pagearray;
	}
	
	#----------------------------------------------------------------
	# function initializeSite
	# Initializes the full site and will build the current page
	# @public
	#----------------------------------------------------------------
	
	public function initializeSite()
	{
		if($this->page == '')
		{
			$this->page = 'home';
		}
		// we need to verify that there is data to give, if there is not then they need to get a 404 error.
		$query = "SELECT `content`.`id`, `content`.`full_page_name`, `content`.`body` FROM `content`, `content_page` WHERE `content`.`page_id`=`pages`.`id` AND `pages`.`seoname` = '" . $this->mysqli->real_escape_string($this->page) . "'";
		
		$result = $this->mysqli->query($query) or die('Error : ' . $this->mysqli->error);
		
		if($result->num_rows < 1)
		{
			include_once("template.class.php");
			// there are no rows for this page, we need to give a 404 error
			$page  = new Template("template/default/404-error.tpl");
			echo '404';
		}
		else
		{
			$requestedpage = explode("/",$this->page);
			if($requestedpage[0] == 'subscribers')
			{
				$rows = array();
				while($row = $result->fetch_assoc())
				{
					$rows[$row['name']]['id'] .= $row['id'];
					$rows[$row['name']]['name'] .= $row['name'];
					$rows[$row['name']]['content'] .= $row['content'];
				}
				
				// spin up the login, as we will need to validate the session.
				include_once("login.class.php");
				$Login = new Login();
				$profileArray = $Login->checkSession(); // put the array into a variable to use later..
				
				include_once("template.class.php");
				$pageOutput = new Template("templates/default/" . $this->PageArray[$this->page]['template']);
				// The subscribers pages need to be built a little differently.
				$pageOutput->set('navigation',$this->BuildNav());
				$pageOutput->set('page-title',$rows['page-title']['content']);
				
				if(isset($_POST['_submit_check']))
				{
					// a form was submitted.. usually means someone is trying to log in.
					$pageOutput->set('page-content',$Login->processLogin());
				}
				else
				{
					if(isset($requestedpage[1]))
					{
						if($requestedpage[1] == 'account')
						{
							//($Logged,$globalnonid,$Level_access,$name,$ftwsub,$advanceActive,$advanceLevel,$advanceDate)
							if($profileArray[4] == 1)
							{
								// if the user is active, we need to let them know
								$customUserPage = new Template("templates/active_user_layout.tpl");
								//$customUserPage->set('page-content',);
								$customUserPage->set('username',$profileArray[3]);
								// calculate the period of their FTW Subscriber status
								if($profileArray[6] == 1)
								{
									$advanceDate = date("l, F jS, Y, h:i a",$profileArray[7]);
									$blahdate = strtotime($advanceDate." +1 month");
									$testfuture = date("l, F jS, Y, h:i a", $blahdate);
								}
								else
								{
									$advanceDate = date("l, F jS, Y, h:i a", $profileArray[7]);
									$blahdate = strtotime($advanceDate." +".$profileArray[6]." months");
									$testfuture = date("l, F jS, Y, h:i a", $blahdate);
								}
								// background:green;color:white;padding-left:5px;
								if($profileArray[5] == 'yes')
								{
									$customUserPage->set('sub-style','padding:10px;border:1px solid #008A05;background:#38D63D;color:white;font-size:18px;');
									$customUserPage->set('status-sub-header',$rows['active-status-sub-header']['content']);
									$customUserPage->set('account-activity',$rows['active-account-activity']['content']);
								}
								else
								{
									$customUserPage->set('sub-style','padding:10px;border:1px solid #E80C00;background:#FF190D;color:white;font-size:18px;');
									$customUserPage->set('status-sub-header',$rows['inactive-status-sub-header']['content']);
									$customUserPage->set('account-activity',$rows['inactive-account-activity']['content']);
								}
								$customUserPage->set('advanced-period',$advanceDate.' till '.$testfuture);
								$pageOutput->set('page-content',$customUserPage->output());
							}
							else
							{
								// They do not have an active subscription so we need to give them the whole speel
								$customUserPage = new Template("templates/default/inactive_user_layout.tpl");
								$customUserPage->set('username',$profileArray[3]);
								$customUserPage->set('user-id',$profileArray[1]);
								$customUserPage->set('root-dir',$this->rootdir);
								$customUserPage->set('cc-month',$this->creditCardMonth());
								$customUserPage->set('cc-year',$this->creditCardYear());
								$pageOutput->set('page-content',$customUserPage->output());
							}
							//$pageOutput->set('page-content','<br /><br /><br /><br /><a href="/v2/subscribers/logout">Logout</a>');
						}
						else if($requestedpage[1] == 'logout')
						{
							// if the second option is set, its a sub page..
							$pageOutput->set('page-content',$Login->logoutAction());
						}
						else
						{
						}
					}
					else
					{
						// no form was submitted. We need to check if they are logged in or trying to log in..
						if($profileArray[0] == 1)
						{
							// they are logged in, we need to push them to the account overview window.
							$pageOutput->set('page-content','<script>window.location.href = "' . $this->rootdir . '/subscribers/account";</script>');
						}
						else
						{
							// are not logged in, and should get the login form
							$pageOutput->set('page-content',$rows['login-form']['content']);
							//$pageOutput->set('page-content',print_r($profileArray));
						}
					}
				}
			}
			else
			{
				include_once("includes/template.class.php");
				$pageOutput = new Template("templates/" . $this->PageArray[$this->page]['template']);
				while($row = $result->fetch_assoc())
				{
					if($row['name'] == 'navigation')
					{
						$pageOutput->set($row['name'],$this->BuildNav());
					}
					else
					{
						$pageOutput->set($row['name'],$row['content']);
					}
				}
			}
			$pageOutput->set('date',date("Y"));
			$pageOutput->set('rootdir',$this->rootdir);			
			echo $pageOutput->output();
		}
	}
	
	#----------------------------------------------------------------
	# function BuildNav
	# Takes the List of pages, and outputs them in order based on the script.
	# @private
	#----------------------------------------------------------------
	
	private function BuildNav()
	{
		$data = '
		<div class="header pure-u-1">
			<div id="full-menu" class="pure-menu pure-menu-open pure-menu-fixed pure-menu-horizontal">
				<a class="pure-menu-heading" href="">FTW Entertainment LLC</a>	
				<ul>'."\n";
		$i = 0;
		foreach($this->PageArray as $Page)
		{
			if($Page['type'] == 0)
			{
			}
			else
			{
				$rootpage = explode("/",$this->page);
				//if($Page['seoname'] == ($this->page) || ($this->page == "" && $Page['seoname'] == 'home'))				
				if($Page['seoname'] == ($rootpage[0]) || ($this->page == "" && $Page['seoname'] == 'home'))
				{
					$selected = ' class="pure-menu-selected"';
				}
				else
				{
					$selected = '';
				}
				$data .= '					<li' . $selected . '><a href="' . $this->rootdir . '/' . $Page['seoname'] . '">' . $Page['name'] . '</a></li>'."\n";
				$i++;
			}
		}
		$data .= '
				</ul>
			</div>
		</div>';
		return $data;
	}
	
	#----------------------------------------------------------------
	# function loginProcess
	# If someone logs in, we need to point them in the right direction
	# @private
	#----------------------------------------------------------------
	
	private function loginProcess()
	{
		include_once("login.class.php");
		$Login = new Login();
		return $Login->processLogin();
	}
}