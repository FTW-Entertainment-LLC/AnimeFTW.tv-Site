<?php
/****************************************************************\
## FileName: user_nav.class.php									 
## Author: Brad Riemann										 
## Usage: User Nav UI Class
## Copywrite 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWUserNav extends Config {
	/****************************\
	# Variables and constructors #
	\****************************/
	var $UserArray;
	
	public function __construct($UserArray){
		parent::__construct();
		// Because this is not taking the information as it is declared outside of the config class.. we need to manually bridge the two here.
		$this->UserArray = $UserArray;
	}
	
	public function Output(){
		$this->BuildUI();
	}
	
	/*******************\
	# Private Functions #
	\*******************/
	
	private function BuildUI(){
		if($this->UserArray[0] == 1) {
			echo '<div class="newnavbar">';
			echo '<div class="aftwU">'.$this->formatUsername($this->UserArray[1]).'</div>';
			if($this->Messages($this->UserArray[1]) > 0){
				$display = " disBlock";
			}
			else {
				$display = "";
			}
			if($this->UserArray[1] == 7){
				echo '<div class="aftwNot">' . $this->CheckAdvancedStatus() . '</div>';
			}
			echo '<div class="aftwNot"><a href="https://' . $_SERVER['HTTP_HOST'] . '/pm"><img src="/images/new-icons/pm_new.png" alt="" title="View your Personal Messages" /><span class="JewelNotif'.$display.'" id="requestJewelNotif">'.$this->Messages($this->UserArray[1]).'</span></a></div>';
			echo '<div class="dropdown">';
			
			include('notifications.class.php');
			$N = new AFTWNotifications();
            $N->connectProfile($this->UserArray);
			echo '
					   <a href="#" id="linkglobal"><img src="/images/new-icons/notifications_new.png" alt="" title="View your Site Notifications" height="18px" /><span id="notesprite">' . $N->ShowSprite() . '</span></a>
							<ul style="display: none;" id="ulglobal">
								<img src="/images/new-icons/trangle-sprite.png" alt="" style="float:right;margin:-20px 123px 0 0;" />
								<div class="usernotes" id="usernotes"><div style="white-space:nowrap;padding:2px;">Loading your Notifications...</div></div>
						  </ul>';
			echo '</div>';
			if($this->UserArray[2] != 0 && $this->UserArray[2] != 3 && $this->UserArray[2] != 7){
				if($this->UserArray[2] == 1 || $this->UserArray[2] == 2){
					$query = mysql_query("SELECT ID FROM uestatus WHERE `change` = 1"); 
					$CountUpdated = mysql_num_rows($query);
					if($CountUpdated > 0){
						$display2 = " disBlock";
					}
					else {
						$display2 = "";
					}
				}
				else {$display2 = "";$CountUpdated = "0";}
				echo '<div class="aftwNot"><a href="https://' . $_SERVER['HTTP_HOST'] . '/manage/"><img src="/images/new-icons/uploads_new.png" alt="" /><span class="JewelNotif'.$display2.'" id="requestJewelNotif">'.$CountUpdated.'</span></a></div>';
			}
			if($this->ValidatePermission(1) == TRUE){
				echo '<div class="aftwNot"><a href="#" rel="#manage" onClick="javascript:ajax_loadContent(\'manageedit\',\'/scripts.php?view=management&u='.$this->UserArray[1].'&node=users\'); return false;"><img src="/images/new-icons/settings_new.png" alt="" title="AFTW Management interface Launch" /></a></div>';
			}
			echo '<div class="aftwNot"><a href="https://' . $_SERVER['HTTP_HOST'] . '/logout"><img src="/images/new-icons/logout_new.png" alt="" title="Log off your AnimeFTW.tv Account" /></a></div>';	
			/*if($this->UserArray[2] == 3){
				echo '<div align="center"><a href="/advanced-signup" title="Support the site in our conquests! Sign up for FTW Subscriber status today!"><span style="color:#FF0000;">Be Advanced..</span></a></div>';
			}*/
			echo '</div>';	
		}
		else { //User is not logged in, give them the basics
			echo '<div class="newnavbar"><div class="aftwU"><a href="https://' . $_SERVER['HTTP_HOST'] . '/login">Sign In</a> | <a href="https://' . $_SERVER['HTTP_HOST'] . '/register">Register</a> | <a href="https://' . $_SERVER['HTTP_HOST'] . '/email-resend">Email Resend</a> | <a href="/forgot-password">Forgot Password</a></div></div>'."\n";
		}		
	}
	
	#-----------------------------------------------------------	
	# Function Messages
	# checks messages for a user and
	# returns if they have any or not.
	#-----------------------------------------------------------
	
	private function Messages($uid){
		$query   = "SELECT COUNT(id) AS unreadMsgs FROM messages WHERE rid='".$uid."' AND viewed='1' AND sent = '0'";
		$result  = mysql_query($query) or die('Error, query failed:' . mysql_error());
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
		$unreadMsgs = $row['unreadMsgs'];
		return $unreadMsgs;
	}
	
	private function CheckAdvancedStatus(){
		if($this->UserArray[13] == 'yes')
		{
			return '<img src="//www.animeftw.tv/images/green_checkmark_rounded_40x40.png" alt="" title="Your Advanced Membership is Active!" />';
		}
		else
		{
			return '<img src="//www.animeftw.tv/images/red_x_rounded_40x40.png" alt="" title="Your Advanced Membership is Inactive!" />';
		}
	}
		
}
?>