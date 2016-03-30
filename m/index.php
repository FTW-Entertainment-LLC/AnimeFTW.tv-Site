<?php
include("../includes/classes/config.v2.class.php");
$Config = new Config();

echo '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
		<meta name="author" content="FTW Entertainment LLC" />
		<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />
		<meta name="robots" content="noindex, nofollow" />

		<title>Home - AnimeFTW.tv</title>

		<link type="text/css" rel="stylesheet" href="docs.css?v=4031" />
		<link type="text/css" rel="stylesheet" href="mmenu.css?v=4000" />

		<script type="text/javascript" src="js/jquery.min.js?v=0001"></script>
		<script type="text/javascript" src="js/jquery.mmenu.min.js?v=0001"></script>
		<script type="text/javascript" src="js/jquery.transit.js?v=0001" ></script>
		<script type="text/javascript" src="js/jtLoad.jquery.js?v=0001" ></script>
		<script type="text/javascript">

			$(document).ready(function() {
				$.ytLoad();
			});
			
			//	The menu on the left
			$(function() {
				$(\'nav#menu-left\').mmenu({
					slidingSubmenus: false
				}); 
			});


			//	The menu on the right
			$(function() {

				$("nav#menu-right").mmenu({
					position: \'right\',
					counters: true,
					searchfield: true
				});

				//	Click a menu-item
				var $confirm = $("#confirmation");
				$("#menu-right a").not( ".mmenu-subopen" ).not( ".mmenu-subclose" ).bind(
					\'click.example\',
					function( e )
					{
						e.preventDefault();
						$confirm.show().text( \'You clicked "\' + $(this).text() + \'"\' );
						$(\'#menu-right\').trigger( \'close\' );
					}
				);
			});
			function openOverlay(id) {
				$("#overlay-content").load("ajax.php?page=overlay&type=episode&id=" + id);
				$("#overlay").show();	
			}
		</script>';
		if(stristr($_SERVER['HTTP_USER_AGENT'],'tv.animeftw.android/3.0') || $Config->UserArray['Level_access'] == 1)
		{
			echo '
			<script>
				function playvid(title,link) {
				';
				if($Config->UserArray['Level_access'] == 3 || $Config->UserArray['Level_access'] == 0)
				{
				echo '
					$("#overlay-content").load("ajax.php?page=overlay&type=episode");
					$("#overlay").show();				
				';
				}
				else
				{
					echo '
					//$("#overlay-content").load("ajax.php?page=overlay&type=ad&title=" + title + "&url=" + link);
					$("#overlay-content").load("ajax.php?page=overlay&type=episode");
					$("#overlay").show();
					//ftwentertainment.vidlaunch(title,link);
					';
				}
				echo '	
				}
			</script>';
		}
echo '		
	</head>
	<body>
		<div id="overlay" style="display:none;">
			<div id="overlay-background"></div>
			<div id="overlay-content" align="center"></div>
		</div>
		<div id="page">
			<div id="header">
				<a href="#menu-left"></a>
				AnimeFTW.tv
				<a href="#menu-right" class="friends right"></a>
			</div>
			<div id="content">';
		if(isset($_GET['login']) && $_GET['login'] == 'success')
		{
			echo '<div style="width:100%;height:100%;background-color:#31DB1A;color:white;padding:1px;border:1px solid #2ACC14;margin-bottom:10px;">You have been logged in Successfully!</div>';
		}
		else if(isset($_GET['logout']) && $_GET['logout'] == 'success')
		{
			echo '<div style="width:100%;height:100%;background-color:#31DB1A;color:white;padding:1px;border:1px solid #2ACC14;margin-bottom:10px;">You have been logged out Successfully!</div>';
		}
		else
		{
		}
		include("includes/mobile.class.php");
        if(isset($_SERVER['HTTP_CF_VISITOR'])){
            $decoded = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
            if($decoded['scheme'] == 'http'){
                // http requests
                $port = 80;
            } else {
                $port = 443;
            }
        } else {
            $port = $_SERVER['SERVER_PORT'];
        }
		if($port == 443)
		{
			if(isset($_GET['page']) && $_GET['page'] == 'login')
			{
				// we push them to the login page.
				$Mobile = new Mobile('login');
			}
			else if(isset($_GET['page']) && $_GET['page'] == 'register')
			{
				// push them to the registration page
				$Mobile = new Mobile('register');
			}
			else
			{
				// non secure page..
				$Mobile = new Mobile('home');
			}
		}
		else
		{
			if(!isset($_GET['page']))
			{
				$ReqPage = 'home';
			}
			else
			{
				$ReqPage = $_GET['page'];
			}
			$Mobile = new Mobile($ReqPage);
		}
		$Mobile->DisplayPage();
				
		echo '	</div>
			<nav id="menu-left">
				<ul>
					<li>
						<a href="#" id="link-home" class="ajax-click">Home</a>
						<ul>
							<li><a href="#" id="link-stats" class="ajax-click">Site Stats</a></li>
							<li><a href="#" id="link-rules" class="ajax-click">Site Rules</a></li>
							<li><a href="#" id="link-server-stats" class="ajax-click">Server Stats</a></li>
							<li><a href="#" id="link-faq" class="ajax-click">FAQs</a></li>
							<li><a href="#" id="link-tos" class="ajax-click">TOS</a></li>
							<li><a href="#" id="link-copyright" class="ajax-click">Copyright</a></li>
							<li><a href="#" id="link-privacy" class="ajax-click">Privacy</a></li>
							<li><a href="#" id="link-top-series" class="ajax-click">Top Series</a></li>
							<li><a href="#" id="link-history" class="ajax-click">History</a></li>
						</ul>
					</li>
					<li><a href="#" id="link-anime" class="ajax-click">Anime</a></li>';
					if($Config->UserArray['logged-in'] == 0)
					{
						// not logged in, we need to have a link for them to login.
						echo '<li><a href="#" id="link-login" class="ajax-click">Login</a></li>';
					}
					else
					{
						echo '
						<li>
							<a href="#" id="link-profile" class="ajax-click">My Profile</a>
							<ul>
								<li><a href="#" id="link-edit-settings" class="ajax-click">Edit Settings</a></li>
								<li><a href="#" id="link-edit-password" class="ajax-click">Change Password</a></li>
								<li><a href="#" id="link-edit-email" class="ajax-click">Change Email</a></li>
							</ul>
						</li>';
					}
					echo '
					<li><a href="#" id="link-store" class="ajax-click">Store</a></li>
					<li>
						<a href="#" id="link-forum" class="ajax-click">Forums</a>
						<ul>
							<li><a href="#" id="link-forum-1" class="ajax-click">Announcements</a></li>
							<li><a href="#" id="" class="ajax-click">Releases</a></li>
							<li><a href="#" id="" class="ajax-click">Anime Requests</a></li>
							<li><a href="#" id="" class="ajax-click">Bug Reports</a></li>
							<li><a href="#" id="link-forum-active-topics" class="ajax-click">Active Topics</a></li>
						</ul>
					</li>
					<li><a href="#" id="link-chat" class="ajax-click">Chat</a></li>';
					if($Config->UserArray['Level_access'] == 3)
					{
						echo '<li><a href="#" id="link-advanced" class="ajax-click">Become Advanced</a></li>';
					}
					if($Config->UserArray['logged-in'] == 1)
					{
						echo '<li><a href="#" id="link-logout" class="ajax-click">Logout</a></li>';
					}
				echo '
				</ul>
			</nav>
			<nav id="menu-right">
				<ul>
				';
				if($Config->UserArray['Level_access'] == 0)
				{
					$aonly = " AND aonly = 0";
				}
				else if($Config->UserArray['Level_access'] == 3)
				{
					$aonly = " AND aonly <= 1";
				}
				else
				{
					$aonly = '';
				}
				$results = $Config->mysqli->query("SELECT fullSeriesName, seoname FROM series WHERE active = 'yes'" . $aonly . " ORDER BY fullSeriesName");				
				$lastFoundLetter = '';
				while($row = $results->fetch_assoc()) {
					//get the first letter of the current record
					$firstLetter = substr($row['fullSeriesName'], 0, 1);
					if ($firstLetter != $lastFoundLetter) {
						//you've started a new letter category
						if ($lastFoundLetter != '') {
							//if there's a real value in $lastFoundLetter, we need to close the previous div 
							echo "							</ul>\n
						</li>\n"; 
						}
						echo "						<li>\n
							<span>" . strtoupper($firstLetter) . "</span>\n
							<ul>\n";
						$lastFoundLetter = $firstLetter;
					}
					echo '							<li><a href="#" id="link-anime-' . $row['seoname'] . '" class="ajax-click">' . stripslashes($row['fullSeriesName']) . '</a></li>'."\n";
				}
				//close the last div
				if ($lastFoundLetter != '') {
					echo "</ul>\n
						</li>\n";
				}
			echo '
				</ul>
			</nav>
			<script>
			$(document).ready(function() {
				$(".ajax-click").on("click", function() {
					var request_id = $(this).attr("id").substring(5);
					$("#content").load("ajax.php?page=" + request_id);
					return false;
				});
			});
			</script>
		</div>
	</body>
</html>';
?>