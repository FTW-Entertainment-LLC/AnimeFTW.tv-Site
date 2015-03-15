<? ob_start("ob_gzhandler"); ?>
<?php
$cdnroot = 'aftw.static.ftw-cdn.com';
if(@$_SERVER['SERVER_PORT'] == '443')
{
	$sslornot = 'https';
	$IsSecure = 'https';
}
else {
	$sslornot = 'http';
	$IsSecure = 'http';
}
include('aftw.class.php');
include('aftw.functions.php');
include('global_functions.php');
?>