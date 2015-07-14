<?php
/****************************************************************\
## FileName: manager.class.php								 
## Author: Brad Riemann								 
## Usage: Wrapper class for the AnimeFTW.tv Management Interface.
## 		Provides common layouts and functions usable by the 
##		various classes.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Manager extends Config {
	var $UserArray;

	public function __construct($UserArray)
	{
		parent::__construct();
		$this->UserArray = $UserArray;
	}
	
	public function bodyCode()
	{
	
		/*
		This year has been a fantastic year for us, we have had some great staff members. <br />Have a Safe and Happy Holidays and remember, if you will be out for extended time to let a manager know!
		
		*/
		echo '
		<div id="manager-wrapper">
			<div id="body-wrapper">
				<div>
					<div class="body-message">With us switching to MP4 ONLY, please make sure your uploads after January 1st contain ONLY mp4 files. You will be given until January 14th to convert your encoding process to MP4 output. If your uploads are not mp4 output after 1/14, you will be told to redo the upload in mp4 format.
					<div style="float:right;margin-top:-2px;"><a href="?logout" title="Log out"><img src="/images/new-icons/logout_new.png" alt="" style="width:20px;" /></a></div>
					</div>
				</div>
				<div id="left-column">' . $this->headerCode() . '
				</div>
				<div id="right-column">';
		if(isset($_COOKIE['manage-tab']))
		{
			$RequestedNode = $_COOKIE['manage-tab'];
			$RequestedClass = 'includes/' . $RequestedNode . '.class.php';
			include($RequestedClass); //we include the file, since it exists
			$Class = ucwords($RequestedNode); //This will fix the first letter of the class, so it fits with our standards.
			$C = new $Class;
		}
		else
		{
			include_once('uploads.class.php');
			$U = new Uploads();
		}
		echo '
				</div>
			</div>
		</div>';
	}
	
	private function headerCode()
	{
		$header = '<div id="nav-wrapper">
		<div id="sub-nav-wrapper">';
		if($this->ValidatePermission(2) == TRUE)
		{
			$header .= '
			<div style="padding:4px;">
			<a href="#" id="nav-users" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_users.png" height="25px" alt="" title="Manage Users" />
						</div>
						<div class="nav-text">
							Users
						</div>
					</div>
			</a>
			</div>';
		}
		if($this->ValidatePermission(10) == TRUE)
		{
			$header .= '
			<div style="padding:4px;">
			<a href="#" id="nav-comments" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_comments.png" height="25px" alt="" title="Manage Comments" />
						</div>
						<div class="nav-text">
							Comments
						</div>
					</div>
			</a>
			</div>';
		}
		if($this->ValidatePermission(17) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-episodes" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_episodes.png" height="25px" alt="" title="Manage Episodes" />
						</div>
						<div class="nav-text">
							Episodes
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(21) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-series" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_series.png" height="25px" alt="" title="Manage Series" />
						</div>
						<div class="nav-text">
							Series
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(26) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-applications" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_applications.png" height="25px" alt="" title="Manage Applications" />
						</div>
						<div class="nav-text">
							Apps
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(30) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-errors" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_error_reports.png" height="25px" alt="" title="Manage Error Reports" />
						</div>
						<div class="nav-text">
							Errors
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(33) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-reviews" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_reviews.png" height="25px" alt="" title="Manage Reviews" />
						</div>
						<div class="nav-text">
							Reviews
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(38) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-emails" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_mail.png" height="25px" alt="" title="Manage Emails" />
						</div>
						<div class="nav-text">
							Emails
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(41) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-logs" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_logs.png" height="25px" alt="" title="Manage Site Logs" />
						</div>
						<div class="nav-text">
							Logs
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(44) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-watchlist" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_my_watchlist.png" height="25px" alt="" title="Manage My WatchList Entries" />
						</div>
						<div class="nav-text">
							My WatchList
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(48) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-forums" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_forum_objects.png" height="25px" alt="" title="Manage Forum Posts and Threads" />
						</div>
						<div class="nav-text">
						Forums
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(61) == TRUE)
			{
				$header .= '
			<div style="padding:4px;">
				<a href="#" id="nav-settings" class="manager-nav">
					<div class="nav-item">
						<div class="nav-image">
							<img src="/images/management/manage_settings.png" height="25px" alt="" title="Manage Settings" />
						</div>
						<div class="nav-text">
							Settings
						</div>
					</div>
				</a>
			</div>';
			}
			if($this->ValidatePermission(79) == TRUE)
			{
				$header .= '
				<div style="padding:4px;">
					<a href="#" id="nav-store" class="manager-nav">
						<div class="nav-item">
							<div class="nav-image">
								<img src="/images/storeimages/shopping_basket.png" height="25px" alt="" title="Manage Store" />
							</div>
							<div class="nav-text">
								Store
							</div>
						</div>
					</a>
				</div>';
			}
			if($this->ValidatePermission(91) == TRUE)
			{
				$header .= '
				<div style="padding:4px;">
					<a href="#" id="nav-content" class="manager-nav">
						<div class="nav-item">
							<div class="nav-image">
								<img src="/images/management/manage_applications.png" height="25px" alt="" title="Manage Content" />
							</div>
							<div class="nav-text">
								Content
							</div>
						</div>
					</a>
				</div>';
			}
			if($this->ValidatePermission(92) == TRUE)
			{
				$header .= '
				<div style="padding:4px;">
					<a href="#" id="nav-stats" class="manager-nav">
						<div class="nav-item">
							<div class="nav-image">
								<img src="/images/management/management_stats.png" height="25px" alt="" title="Manage Stats" />
							</div>
							<div class="nav-text">
								Stats
							</div>
						</div>
					</a>
				</div>';
			}
			$header .= '
				<div style="padding:4px;">
					<a href="#" id="nav-uploads" class="manager-nav">
						<div class="nav-item">
							<div class="nav-image">
								<img src="/images/new-icons/uploads_new.png" height="25px" alt="" title="Uploads Interface" />
							</div>
							<div class="nav-text">
								Uploads
							</div>
						</div>
					</a>
				</div>
			</div>';
			$header .= '</div>
				<script>
				$(".manager-nav").on("click", function() {
					var nav_id = $(this).attr("id").substring(4);
					$(".nav-item").removeClass("nav-item-active");
					$(this).children("div").addClass("nav-item-active");
					$("#right-column").load("ajax.php?node=" + nav_id);
					var request = $.ajax({
						type: "GET",
						processData: false,
						url: "ajax.php?node=users&cookie=" + nav_id
					});
					$("html, body").animate({ scrollTop: 0 }, "slow");
					return false;
				});
				</script>
				<script>
				$(document).ready(function () {  
					var top = $(\'#sub-nav-wrapper\').offset().top - parseFloat($(\'#sub-nav-wrapper\').css(\'marginTop\').replace(/auto/, 0));
					$(window).scroll(function (event) {
						// what the y position of the scroll is
						var y = $(this).scrollTop();
  
						// whether that\'s below the form
						if (y >= top) {
							// if so, ad the fixed class
							$(\'#sub-nav-wrapper\').addClass(\'fixed\');
						}
						else 
						{
							// otherwise remove it
							$(\'#sub-nav-wrapper\').removeClass(\'fixed\');
						}
					});
				});
				</script>';
		return $header;
	}
}

?>