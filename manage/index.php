<?php
include("../includes/classes/config.class.php");
$C = new Config();
$C->buildUserInformation();

// we need to sift out people..
if($C->UserArray[0] == 0)
{
	header("location: http://" . $_SERVER['HTTP_HOST'] . "/");
	exit;
}
else if($C->UserArray[2] == 3)
{
	header("location: http://" . $_SERVER['HTTP_HOST'] . "/");
	exit;	
}
else
{
}

// make sure that they are on animeftw.tv, that they are using port 443 for ssl
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
if($port == '80' && $_SERVER['HTTP_HOST'] == 'www.animeftw.tv')
{
	header("location: https://" . $_SERVER['HTTP_HOST'] . "/manage/");
}
else {}

include("includes/logins.class.php");
$L = new Logins();

if(isset($_GET['logout']))
{
	$L->removeSessions();
}

if($L->checkSessions() == FALSE)
{
	// they are not logged in, so we need to go through the process.
	//$Body = $L->loginCode();
	$Title = 'Welcome, Friend.';
	$ManagementJS = '';
}
else
{
	include("includes/manager.class.php");
	$M = new Manager($C->UserArray);
	//$Body = $M->bodyCode();
	$Title = 'AnimeFTW.tv Site Manager - Home';
	$ManagementJS = '<script src="assets/management.js"></script>
		<script src="assets/select2.min.js"></script>
		<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="assets/excanvas.js"></script><![endif]-->';
}

echo '<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		
		<title>' . $Title . '</title>

		<!-- Meta tags -->
		<meta name="description" content="A Mythical Site of the Unknown" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="assets/style.css?v=4" />
		<link rel="stylesheet" type="text/css" href="assets/jquery.jqplot.min.css" />
		<link rel="stylesheet" type="text/css" href="assets/select2.css?v=1" />
		
		<!-- JavaScript -->
		<!--[if IE]><![endif]-->
		<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="assets/jquery.min.js"></script>
		<script type="text/javascript" src="assets/jquery.jqplot.min.js"></script>
		<link rel="stylesheet" href="assets/redactor.css?v=1" />
		<script src="assets/redactor.min.js"></script>
		' . $ManagementJS . '
		<script type="text/javascript">//<![CDATA[ 
			$( document ).ajaxStart(function() {
				$( ".modal" ).show();
			}).ajaxStop(function(){
				$( ".modal" ).hide();
			});
		</script>
	</head>
	<body>
	<div id="hov-msg">&nbsp;</div>';
	// Had to put this down here so it went into the right area.
	if($ManagementJS == ''){
		echo $L->loginCode();
	}
	else {
		echo $M->bodyCode();
	}
echo '
	<div class="modal"></div>';
	if($C->UserArray[1] == 1)
	{
	echo '<div class="notification-bar">
		<div class="notification-bubble" id="bubble-1">
			<div class="bubble-header">header</div>
			<div class="bubble-body">
				<a href="#" onClick="$(\'#bubble-1\').hide(\'slow\');return false;">Hide this</a>
			</div>
			<div class="bubble-options">options</div>
		</div>
		<div class="notification-bubble" id="bubble-2">
			<div class="bubble-header">header</div>
			<div class="bubble-body">
				<a href="#" onClick="$(\'#bubble-2\').hide(\'slow\');return false;">Hide this</a>
			</div>
			<div class="bubble-options">options</div>
		</div>
		<div class="notification-bubble" id="bubble-3">
			<div class="bubble-header">header</div>
			<div class="bubble-body">
				<a href="#" onClick="$(\'#bubble-3\').hide(\'slow\');return false;">Hide this</a>
			</div>
			<div class="bubble-options">options</div>
		</div>
		<div class="notification-bubble" id="bubble-4">
			<div class="bubble-header">header</div>
			<div class="bubble-body">
				<a href="#" onClick="$(\'#bubble-4\').hide(\'slow\');return false;">Hide this</a>
			</div>
			<div class="bubble-options">options</div>
		</div>
	</div>';
	}
	echo '
	</body>
</html>';