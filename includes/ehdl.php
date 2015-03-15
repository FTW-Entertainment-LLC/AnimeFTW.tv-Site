<?php
 /********************************************************

   PHP Silent Error Logger (the Mistress)
   Build	12.4
   Author	Adam Treadway
   Copyright	2012 AnimeFTW.tv
   Desc		Silently logs "soft" PHP errors.
		Can not handle "hard" errors, as described below:
		E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
		For more information: http://www.php.net/manual/en/function.set-error-handler.php

   SQL		CREATE TABLE IF NOT EXISTS `error` (
  		`id` int(11) NOT NULL AUTO_INCREMENT,
		`f` varchar(255) NOT NULL,
		`l` int(11) NOT NULL,
		`s` varchar(255) NOT NULL,
		`c` longtext NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

 ********************************************************/

 set_time_limit(10);

 //Reporting level
 $reporting_level	= E_ALL | E_STRICT;

 function EHDL($n,$s,$f,$l,$c) {

	// Configuration
	$use_mysql	= TRUE;
	$use_existcon	= TRUE;
	$flatfile	= "errors.txt";
	$relog_error	= FALSE;

	// Connection Details
	$edb_name	= "mainaftw_anime";
	$edb_user	= "mainaftw_anime";
	$edb_pass	= "26V)YPh:|IJG";
	$edb_table	= "error";

	//Context conversion
	array_shift($c);
	$context = var_export($c,true);
	if($use_mysql == TRUE) {
		if($use_existcon != TRUE) {
			$elink = @mysql_connect("localhost",$edb_user,$edb_pass,true) or EHDL_DIRTY(1,__LINE__);
			@mysql_select_db($edb_name,$elink) or EHDL_DIRTY(1,__LINE__);
		}
		$ip = "";
		$safe_context = mysql_real_escape_string($context);
		$safe_string = mysql_real_escape_string($s);
		$insert = "INSERT INTO `".$edb_table."` VALUES('','$f',$l,'$safe_string','$safe_context','$ip')";
		if($relog_error == FALSE) {
			$query = "SELECT `id` FROM `".$edb_table."` WHERE ".
				 "`s` = \"".$safe_string."\" AND `f` = \"$f\" AND `l` = \"$l\"";
			$resource = @mysql_query($query) or EHDL_DIRTY(1,__LINE__);
			if(@mysql_num_rows($resource) == 0) {
				@mysql_query($insert) or EHDL_DIRTY(1,__LINE__);
			}
		} else {
			@mysql_query($insert) or EHDL_DIRTY(1,__LINE__);
		}

		@mysql_close($elink);

	} else {

		if(@is_writable($flatfile)) {

			$output = "#\n$f, $l\n$s\n$context\n";

			$file = @fopen("$flatfile","a") or EHDL_DIRTY(2,__LINE__);
			@fwrite($file,$output,strlen($output)) or EHDL_DIRTY(2,__LINE__);
			@fclose($file) or EHDL_DIRTY(2,__LINE__);
			
		} else {

			/*
				Last resort. Outputs a physical message and terminates
				the program. Only happens if neither database nor flatfile
				directory are available to write to.
			*/

			echo "<b>Error Logger Critical Error:</b> Could not write error data to database or flatfile! Administrator, ".
			     "please check that your settings are correct in the error handler configuration, and that your specified ".
			     "flatfile has CHMOD permissions of 640 or 644.";
			exit;

		}

	}
 }

 function EHDL_DIRTY($mode,$line) {
	$message = ($mode == 1) ? "Internal query error" : "Internal file permissions error";
	echo "<b>Error Logger Critical Error:</b> $message on line ($line).";
	exit;
 }

 set_error_handler("EHDL",$reporting_level);
?>