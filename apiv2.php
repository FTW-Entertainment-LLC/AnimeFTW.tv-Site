<?php
header('Content-Type: text/html; charset=utf-8');
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
	header("location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}
#***********************************************************
#* apiv2.php, API V2 Token Based scripts for AnimeFTW.tv
#* Written by Brad Riemann
#* Copywrite 2014. FTW Entertainment LLC
#* Distribution of this is strictly forbidden
#***********************************************************

include("includes/classes/config.v2.class.php");
include("includes/classes/apiv2.class.php");

$Api = new api();