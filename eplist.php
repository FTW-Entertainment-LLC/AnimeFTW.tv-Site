<?php
  #################################################
 #
 # >> Episode listing
 #    Author: Adam Treadway (Zigbigidorlu)
 #   Purpose: Listing of episodes in a series
 # Copyright: Copyright (c) 2011/2012 AnimeFTW.tv
 #    Rights: All rights reserved.
 #     Notes: None.
 #
  #################################################

 session_start();
 require "includes/siteroot.php";
 require "aftw-commonf.php";
 require "config-parse.php";
  
 Class Eplisting Extends Common {
	public function Eplisting() {
		$this::prepare();
		if(isset($_GET['series'])) {
			print_r($this::db_query("SELECT * FROM *"));
		} else {
			header("location:http://www.animeftw.tv");
		}
	}
 }
 
 new Eplisting();
?>