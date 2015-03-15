<?php
$AppsOpen = 0;
define("THEME_WIDTH", "990");
if(!isset($PageTitle)){$PageTitle = '';}

if($profileArray[8] == 1){
	$logo = "<a href=\"/\" title=\"".$PageTitle."\"><img src=\"/images/holiday/christmas/new-logo.png\" alt=\"".$PageTitle."\" style=\"border:0px;\" title=\"From all the Staff of AnimeFTW.tv, we wish you a very safe and Happy Holidays!\" /></a>";
}
else {
	$logo = "<a href=\"/\" title=\"".$PageTitle."\"><img src=\"/images/new-logo.png\" alt=\"".$PageTitle."\" style=\"border:0px;\" title=\"AnimeFTW.tv, your home for High Quality Streaming Anime\" /></a>";
}
//$logo = "<a href=\"http://www.animeftw.tv/forums/global-announcements/topic-2537/s-0\" title=\"".$PageTitle."\"><img src=\"/images/birthday/AnimeFTW_Birthday_Logo.png\" alt=\"".$PageTitle."\" style=\"border:0px;\" title=\"AnimeFTW.tv is 4 Years old this Week!\" /></a>";
	echo "	<table cellpadding='0' cellspacing='0' width='100%'>\n	<tr>\n";
	echo "	<td class='header-bg'>\n";
	echo "		<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n	<tr>\n";
	echo "			<td class='header-logo' style='padding-top: 9px;'>\n".$logo."</td>\n";
	echo "			<td align='right'>\n";
	echo "				<div class='header-log'>";	
	include_once("includes/classes/user_nav.class.php");
	$uin = new AFTWUserNav();
	$uin->Output();
	echo "				</div>\n";
	//echo "				<div class='header-sort'></div>\n";
	echo "				<div class='header-dir'></div>\n";
	echo "			</td>\n";
	echo "		</tr>\n		</table>\n";
	echo "	</td>\n";
	echo " 	</tr>\n";
	echo "	<tr>\n";
	echo "	<td class='header-nav'>\n";
	echo "		<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n	<tr>\n";
	echo "			<td class='header-nav-left'>\n";
	echo "<a id=\"tab-1\" class=\"aftw-main-nav"; if(($_SERVER['PHP_SELF'] == '/index.php' || $_SERVER['PHP_SELF'] == '/projects.php' || $_SERVER['PHP_SELF'] == '/register.php' || $_SERVER['PHP_SELF'] == '/secure.php') && (!strstr($_SERVER['REQUEST_URI'],'/staff/applications') && !strstr($_SERVER['REQUEST_URI'],'store'))){echo ' current';} echo "\" href='/'>Home</a>
<a id=\"tab-2\" class=\"aftw-main-nav"; if($_SERVER['PHP_SELF'] == '/videos.php' || $_SERVER['PHP_SELF'] == '/videos2.php' || $_SERVER['PHP_SELF'] == '/search.php'){echo ' current';} echo "\" href=\"/anime\">Anime</a>
<a id=\"tab-3\" class=\"aftw-main-nav"; if($_SERVER['PHP_SELF'] == '/users.php' || $_SERVER['PHP_SELF'] == '/messages.php'){echo ' current';} echo "\" href=\"/user\">Profile</a>
<a id=\"tab-4\" class=\"aftw-main-nav"; if(strpos($_SERVER['REQUEST_URI'], 'store')){echo ' current';} echo "\" href=\"/store\">Store</a>
<a id=\"tab-5\" class=\"aftw-main-nav"; if($_SERVER['PHP_SELF'] == '/forums.php'){echo ' current';} echo "\" href=\"/forums\">Forum</a>";
	if($profileArray[2] == 3)
	{
		echo "<a id=\"tab-6\" class=\"aftw-main-nav"; if(strpos($_SERVER['REQUEST_URI'], 'advanced-signup')){echo ' current';} echo "\" href=\"/advanced-signup\">Adv.</a>";
	}
	if($AppsOpen == 1 && $profileArray[0] == 1 && ($profileArray[2] == 3 || $profileArray[2] == 7 || $profileArray[2] == 1)){
		echo "<a id=\"tab-8\" class=\"aftw-main-nav\" href='/staff/applications'"; if(strstr($_SERVER['REQUEST_URI'],'/staff/applications')){echo ' class="current"';$currentTab=6;} echo ">Staff</a>\n";
	
		if($profileArray[2] == 1)
		{
			echo "<a id=\"tab-7\" class=\"aftw-main-nav\" href=\"#\" onClick=\"window.open('/irc','winname','directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=650,height=550'); return false;\">Chat</a>";
		}
	}
	else
	{
		if($profileArray[0] == 1)
		{
			echo "<a id=\"tab-7\" class=\"aftw-main-nav\" href=\"#\" onClick=\"window.open('/irc','winname','directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=650,height=550'); return false;\">Chat</a>";
		}
	}
	echo '<script>
		$(document).ready(function(){
			var active_tab = $(".header-nav-left").find(".current").attr("id"); 
			$("#hover-" + active_tab).show();
			$(".aftw-main-nav").mouseenter(function() {
				var menuid = $(this).attr("id");
				$(".pane-subnav").hide();
				$(".aftw-main-nav").removeClass("current");
				$("#" + menuid).addClass("current");
				$("#hover-" + menuid).show();
			});
		});
	</script>';
	echo "			</td>";
	echo "			<td align='right'>\n";
	echo "			<div class='header-search' style='float:right;'>";
	echo "			<div class='header-search-nav' style='float:right;'>";
	// /search
	echo "				<form name='slimsearch' method='get' action='/search'>
							<input type='hidden' name='sid' value='".md5(time())."' />
							<span class='search'>
									<input type='text' name='q' id='q' class='search-box' value='AnimeFTW.tv Site Search' autocomplete='off' />
									<img src='/images/search-button.png' alt='' onClick='document.slimsearch.submit()' class='search-button' />
							</span>
						</form>";
	echo "				<div class='search-text'>";
	$pt = new AFTWpage();
	$pt->LatestNews();
	echo "				</div><div id='SearchResults'></div>";
	echo "			</div></div>";
	echo "			</td>\n";
	echo "		</tr>\n		</table>\n";
	echo "	</td>\n";
	echo "	</tr>\n		</table>\n";
	echo "			<div id=\"hover-panes\" class=\"panes\" align=\"center\">
						<div id=\"hover-tab-1\" class=\"pane-subnav\" align=\"center\"><a href='/stats'>Site Stats</a> | <a href='/rules'>Site Rules</a> | <a href='/server-stats'>Server Stats</a> | <a href='/faq'>FAQs</a> | <a href='/tos'>TOS</a> | <a href='/copyright'>Copyright</a> | <a href='/top-series'>Top Series</a></div>
						<div id=\"hover-tab-2\" class=\"pane-subnav\" align=\"center\">View all of AnimeFTW.tv's Diverse Anime Collection!</div>
						<div id=\"hover-tab-3\" class=\"pane-subnav\" align=\"center\">";
						if(($_SERVER['PHP_SELF'] == '/users.php' && ($profileArray[1] == $u->UserArray['ID'] || ($profileArray[2] == 1 || $profileArray[2] == 2 && $u->UserArray['ID'] != 1)))){
							echo '
							<div style="display:inline-block;" class="pane-subnav-subdiv">
								<div class="popbox" style="width:150px;">
									<a href="#" class="open" onClick="$(\'#change-password-box\').load(\'/scripts.php?view=settings&id=' . $u->UserArray['ID'] . '&go=password\');">Change your Password</a> |
									<div class="collapse">
										<div class="box2">
											<div class="arrow"></div>
											<div class="arrow-border"></div>

											<div id="change-password-box">Loading Content..</div>
										</div>
									</div>
								</div>
							</div>
							<div style="display:inline-block;" class="pane-subnav-subdiv">
								<div class="popbox" style="width:150px;">
									<a href="#" class="open" onClick="$(\'#change-email-box\').load(\'/scripts.php?view=settings&id=' . @$u->UserArray['ID'].'&go=email\'); return false;">Change your Email</a> |
									<div class="collapse">
										<div class="box2">
											<div class="arrow"></div>
											<div class="arrow-border"></div>

											<div id="change-email-box">Loading Content..</div>
										</div>
									</div>
								</div>
								
							</div>';
							echo "
							<div style=\"display:inline-block;\" class=\"pane-subnav-subdiv\">
								<a href=\"#\" onclick=\"loadEditProfile(".@$u->UserArray['ID']."); return false;\"  title=\"Click to Edit your Settings\">Edit your Settings</a>
							</div>";
						}
						else {
							if($profileArray[0] == 0){
								echo 'Sign in to gain access to Member\'s Only Features!';
							}
							else {
								echo 'Click to Proceed to your Profile.';
							}
							echo '';
						}
						echo "</div>
						<div id=\"hover-tab-4\" class=\"pane-subnav\" align=\"center\">Love AnimeFTW.tv? Check out the Store for awesome Goodies, AFTW Style.</div>
						<div id=\"hover-tab-5\" class=\"pane-subnav\" align=\"center\"><a href='/forums/global-announcements/'>Announcements</a> | <a href='/forums/releases/'>Releases</a> | <a href='/forums/anime-requests/'>Anime Requests</a> | <a href='/forums/bug-reports/'>Bug Reports</a> | <a href='/forums/active-topics'>Active Topics</a></div>
						<div id=\"hover-tab-6\" class=\"pane-subnav\" align=\"center\">Get the Perks of Advanced Membership Today!</div> 
						<div id=\"hover-tab-7\" class=\"pane-subnav\" align=\"center\">Join the Staff and other Members in Chat!</div> 
						<div id=\"hover-tab-8\" class=\"pane-subnav\" align=\"center\">Want to help AnimeFTW.tv Spread Anime Love? Apply Within!</div> 
						<div></div>
					</div>";
	if($profileArray[2] == 1 || $profileArray[2] == 2 || $profileArray[2] == 4 || $profileArray[2] == 5 || $profileArray[2] == 6){
		
		echo '<div class="apple_overlay" id="manage">';
		echo '<h2 style="margin:0px">Site Management</h2>';
		echo '<div class="comments" id="manageedit">Loading. Please Wait...</div>';
		echo '</div>';
		echo '<div class="apple_overlay" id="uploads">';
		echo '<h2 style="margin:0px">AnimeFTW.tv Uploads Tracker</h2>';
		echo '<div class="comments" id="uploadstracker">Loading. Please Wait...</div>';
		echo '</div>';
	}
	if($_SERVER['PHP_SELF'] == '/videos.php' || $_SERVER['PHP_SELF'] == '/videos2.php'){}
	else {echo "<br /><br /><br /><br /><br /><br />\n";}
	

		
?>