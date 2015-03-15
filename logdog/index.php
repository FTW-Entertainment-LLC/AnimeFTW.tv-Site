<?php
 ###################################################
 # LogDog 1.0
 # Author..... Adam Treadway, FTW Entertainment, LLC
 # Purpose:... API log tool
 # Copyright.. Copyright (c) 2012 FTW Entertainment, LLC
 # Notes...... Do not distribute.
 ###################################################
 
	//Database connection
	mysql_connect("localhost","mainaftw_anime","26V)YPh:|IJG");
	mysql_select_db("mainaftw_anime");
	include("engine.php");
	$goodBoy = new LogDog();
	if(isset($_GET['q'])) {
		$q = $_GET['q'];
		$results = $goodBoy->goGet($q);
		$safeq = str_replace("\"","&quot;",$q);
		$out = fetch("query",array("query"=>$safeq,"results"=>$results));
		echo $out;
	} else {
		$out = fetch("main");
		echo $out;
	}
	
	function fetch($io,array $rpl = array()) {
		$io = file_get_contents("tpl/$io.tpl");
		foreach($rpl as $key=>$value) {
			$io = str_replace("%$key%",$value,$io);
		}
		return $io;
	}
?>