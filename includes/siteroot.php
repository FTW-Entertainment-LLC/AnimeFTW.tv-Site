<? ob_start("ob_gzhandler"); ?>
<?php
$cdnroot = 'aftw.static.ftw-cdn.com';
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
if(@$port == '443')
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