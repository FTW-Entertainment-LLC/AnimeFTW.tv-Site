<?php

include("includes/classes/config.class.php");

$Config = new Config();

echo '<html>
<head>
	<title>AnimeFTW.tv Chat</title>
	<style type="text/css">
	a {
		text-decoration:none;
		color:white;
	}
	a.active {
		color:black;
	}
	.tab {
		background-color:#787878;
		padding:3px;
		display:inline-block;
		border-top-left-radius: 4px;
		border-top-right-radius: 4px;
		-moz-border-radius-topleft: 4px;
		-moz-border-radius-topright: 4px;
		-webkit-border-top-left-radius: 4px;
		-webkit-border-top-right-radius: 4px;
	}
	.active-tab {
		background-color:white;
		color:black;
	}
	#nav {
		border-bottom:1px solid white;
		font-size:18px;
	}
	#header {
		height:140px;
		padding:5px;
		color:white;
	}
	.hidden {
		display:none;
	}
	.body-content {
		font-size:16px;
	}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body style="background:#444444;">
<div style="margin:-10px 0 0 -10px;">
	<div id="header">
		<div id="nav">
			<div id="tab-home" class="tab active-tab"><a href="#" class="active">Home</a></div>
			<div id="tab-register" class="tab"><a href="#">Register</a></div>
			<div id="tab-login" class="tab"><a href="#">Login</a></div>
			<div id="tab-joining" class="tab"><a href="#">Joining Channels</a></div>
		</div>
		<div id="body-content">
			<div id="home-tab-content" class="body-content">
				Welcome to our IRC Chat room, we can be found on Rizon IRC at #AnimeFTW.tv. Feel free to connect your IRC client to irc.rizon.io and join #Animeftw.tv. If you have any questions about this please contact us on the forums or hop in the chat.
			</div>
			<div id="register-tab-content" class="body-content hidden">
				Since Rizon is not an AnimeFTW.tv or FTW Entertainment Property, it is up to you to register and manage your account. To do so, use the following command
				to register your account:<br />
				<code>/ns register PASSWORD EMAIL</code>
			</div>
			<div id="login-tab-content" class="body-content hidden">
				To log in to your Rizon account use the following command:<br /><br />
				<code>/ns identify PASSWORD</code>
			</div>
			<div id="joining-tab-content" class="body-content hidden">
				To join other channels, type the following command:<br /><br />
				<code>/join #CHANNEL</code><br />
				Popular Fansub channels: #Hadena, #UTW, #evetaku
			</div>
		</div>
		<script>
		$(document).ready(function(){
			$(".tab").on("click", function(){
				var tab_id = $(this).attr("id").substring(4);
				$(".tab").removeClass("active-tab");
				$(".tab").children("a").removeClass("active");
				$(this).addClass("active-tab");
				$(this).children("a").addClass("active");
				$(".body-content").slideUp().addClass("hidden");
				$("#" + tab_id + "-tab-content").removeClass("hidden").slideDown();
				return false;
			});
		});
		</script>
	</div>
	<div>';
if($Config->UserArray[0] == 1)
{
	echo '<iframe src="https://qchat2.rizon.net/?nick=' . $Config->UserArray[5] . '&channels=AnimeFTW.tv&uio=d4" width="650" height="400"></iframe>';
}
else
{
	echo '<iframe src="http://qchat2.rizon.net/?nick=aftwguest' . rand(3,3) . '&channels=AnimeFTW.tv&uio=d4" width="650" height="400"></iframe>';
}
echo '
	</div>
</div>
</body>
</html>';