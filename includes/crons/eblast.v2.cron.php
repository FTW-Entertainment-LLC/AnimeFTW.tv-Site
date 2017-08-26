<?php
/****************************************************************\
## FileName: eblast.v2.cron.php									 
## Author: Brad Riemann										 
## Usage: Processes Active Eblasts and sends them.
## Copyright 2017 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/
$includePath = ($_SERVER['HTTP_HOST'] === "v4.aftw.ftwdevs.com" ||
                $_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com'||
				$_SERVER['HTTP_HOST'] === "phpdev") ? "/home/devsftw9/public_html/projects/aftw/v4" : "/home/mainaftw/public_html";
                
require_once("{$includePath}/includes/classes/config.v2.class.php");
require_once("{$includePath}/includes/classes/eblasts.v2.class.php");

$Eblast = new Eblast();
$Eblast->sendEblasts();