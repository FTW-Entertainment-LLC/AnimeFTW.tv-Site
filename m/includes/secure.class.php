<?php
/****************************************************************\
## FileName: secure.class.php									 
## Author: Brad Riemann										 
## Usage: Secure Pages Constructor class for the mobile website.
## Copywrite 2013-2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Secure extends Config{
	var $node;
	public function __construct($node)
	{
		parent::__construct();
		if(isset($_POST['method']))
		{
			// We want to process the posted data from here.
			$this->processSecureData($_POST);
		}
		else
		{
			$this->node = $node;
		}
	}	
	
	public function Init()
	{
		if($this->node=="login")
		{
			echo '<div id="secure-wrapper">';
			if(stristr($_SERVER['HTTP_USER_AGENT'],'tv.animeftw.android/3.0'))
			{
				echo '<div class="secure-header" style="font-size:18px;">AnimeFTW.tv Login</div>';
			}
			else
			{
				echo '<div class="secure-header" style="font-size:20px;margin-bottom:5px;border-bottom:1px solid #abadb3;">AnimeFTW.tv Mobile Login</div>';
			}
			echo '<div align="center" style="padding:5px;border:1px solid #ccc;background-color:#007fc8;color:#fff;">Welcome to AnimeFTW.tv, we are the world leader in streaming anime.<br /> To access <b>ANY</b> of our content, you must login below.<br />If you do not have an account, <a href="#" style="color:#fff;" onClick="$(\'#content\').load(\'ajax.php?page=register\'); return false;">please sign up here</a>.</div>';
			echo '<div id="login-form" style="padding-top:10px;">
				<form id="form-login" action="ajax.php" method="POST">
				<input name="_submit_check" value="1" type="hidden">
				<input name="issubmit" value="1" type="hidden">
				<input type="hidden" name="method" value="loginform" />
				<input type="hidden" name="FailedLogins" id="FailedLogins" value="1" />
				<div class="form-row" style="width:100%;">
					<div class="form-left-column" style="display:inline-block;width:25%;text-align:right;">
						<label class="left" for="Username" style="margin: 0px 0px 0px 0px;color:#555555;">Username:</label>
					</div>
					<div class="form-right-column" style="display:inline-block;width:70%;">
						<input name="Username" id="Username" class="loginForm" style="width: 175px" type="text" />
					</div>
				</div>
				<div class="form-row" style="width:100%;">
					<div class="form-left-column" style="display:inline-block;width:25%;text-align:right;vertical-align:top;" align="right">
						<label class="left" for="Password" style="margin: 0px 0px 0px 0px;color:#555555;">Password:</label>
					</div>
					<div class="form-right-column" style="display:inline-block;width:70%;">
						<input name="Password" id="Password" class="loginForm" style="width:105px;" type="password" />
						<input name="submit" class="button_2" value="Sign In" type="submit" id="submitButton" />
					</div>
				</div>
				<div id="form-error" style="min-height:20px;padding:3px;" align="center"></div>
				<div class="form-row" style="width:100%;">
					<div align="center">
						<div style="margin-left:50px;"><input name="remember" id="remember" type="checkbox"><label class="left" for="remember">Keep me logged in</label></div>
						<div style="font-size: 9px;">(Not recommended for public or shared computers)</div>
					</div>
				</div>
				<div align="center" style="padding-top:10px;">
					<div style="font-size:16px;" align="left">About AnimeFTW.tv:</div>
					<i>AnimeFTW.tv Members Enjoy many perks over the average Anime Streaming site. By logging in with your AnimeFTW.tv Account, you are given access to the net\'s Largest library of on Demand Streaming Anime in HD Quality. <br><br>Along with the perks that come with being a basic member, users can upgrade their account, "FTW Subscribers" are allowed to enhance their AnimeFTW.tv Account by making them Advanced Members. AMs for short, are allowed to download all our videos and have direct access to the CDN for the fastest download speeds anywhere in the world.</i>
				</div>
				</div>
				</form>
				<script>
					$(document).ready(function() {
						$("#submitButton").on("click", function() {
							if(parseInt($("#FailedLogins").val()) == 5)
							{
								// restrict the login
								$("#login-form").find(\':input:not(:disabled)\').prop(\'disabled\',true);
								$("#form-error").html(\'<div style="width:100%;height:100%;background-color:#F25C5C;color:white;padding:1px;border:1px solid #FF1919">Login Attempts Exhausted, please try again later.</div>\');
								return false;
							}
							$.ajax({
								type: "POST",
								url: "ajax.php",
								data: $(\'#form-login\').serialize(),
								success: function(html) {
									if(html.indexOf("Success") >= 0){
										// Login successful message.
										$("#form-error").html(html);
										var url = "http://www.animeftw.tv/m/?login=success";    
										$(location).delay(10000).attr(\'href\',url);
									}
									else{
										// print out the error coming from the login script.
										var login_val = parseInt($("#FailedLogins").val());
										$("#form-error").html(\'<div style="width:100%;height:100%;background-color:#F25C5C;color:white;padding:1px;border:1px solid #FF1919;">\' + html + " " + $("#FailedLogins").val() + " of 5 login attempts used.</div>");
										$("#FailedLogins").val(login_val + 1);
									}
								}
							});
							return false;
						});
					});
				</script>';
		}
		else if($this->node == "register")
		{
		}
		else if($this->node == "logout")
		{
			include("../includes/classes/account.v2.class.php");
			$A = new Account();
			$A->removeLoginSessions(0);
		}
		else
		{
		}
	}
	
	private function processSecureData($PostData)
	{
		if($PostData['method'] == 'loginform')
		{
			// it was sent from the login form, we need to parse as such
			include("../includes/classes/account.v2.class.php");
			$A = new Account();
			$login = $A->array_validateLogin($Type,$PostData);
			if($login["failed"] == FALSE)
			{
				// login was successful, we let them know.
				echo '<div style="width:100%;height:100%;background-color:#31DB1A;color:white;padding:1px;border:1px solid #2ACC14;">Login Successful Redirecting...</div>';
			}
			else
			{
				echo $login["message"];
			}
		}
		//echo '<!-- Success --><div style="width:100%;height:100%;background-color:#31DB1A;color:white;padding:1px;border:1px solid #2ACC14;">Login Successful Redirecting...</div>';
		//echo '<script>window.location = "http://www.animeftw.tv/m/";</script>';
	}
}

?>