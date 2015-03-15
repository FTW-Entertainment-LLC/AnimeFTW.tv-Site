<?php
header('Content-Type: text/html; charset=utf-8');
if($_SERVER['SERVER_PORT'] == '80' && $_SERVER['HTTP_HOST'] == 'www.animeftw.tv')
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