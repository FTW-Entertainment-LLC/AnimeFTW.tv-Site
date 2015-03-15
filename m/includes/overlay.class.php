<?php
/****************************************************************\
## FileName: overlay.class.php									 
## Author: Brad Riemann										 
## Usage: Overlay class for popups.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Overlay extends Config {
		
	public function __construct()
	{
		parent::__construct();
	}
	
	public function Init()
	{
		if(isset($_GET['type']) && $_GET['type'] == 'ad')
		{
			$this->adsOverlay();
		}
		else if(isset($_GET['type']) && $_GET['type'] == 'episode')
		{
			$this->episodeOverlay();
		}
		else
		{
			echo '<div align="center">The requested overlay action is not available. Please try again.</div>';
		}
	}
	
	private function episodeOverlay()
	{
		require_once("anime.class.php");
		
		$Anime = new Anime();
		$Anime->displayEpisode($_GET['id']);
	}
	
	private function adsOverlay()
	{
		echo '<div id="ad-overlay">';
		echo '<div align="center" style="padding:5px;font-weight:bold;">The Servers for AnimeFTW.tv are paid for by viewers like you. If you want to stop ads, <a href="#">Sign Up</a> for AnimeFTW.tv Advanced Membership Today!</div>';
		echo '<div style="padding-bottom:5px;"><form name="counter"><span>Your Video will be available in: <br /><input type="text" name="d2" style="border:none;width:20px;text-align:center;">seconds</span></form></div>
			<div style="padding-bottom:10px;"><input type="button" id="launch-button" value=" Your Video is not yet Available! " disabled="disabled" /></div>
			<div align="center">';
			echo ' <!-- Start J-List Affiliate Code -->
<div style="text-align: center; font-size: 12px;">
<a href="http://anime.jlist.com/click/3638/129" target="_blank" onmouseover="window.status=\'Click for Japanese study aids and more\'; return true;" onmouseout="window.status=\'\'; return true;" title="Click for Japanese study aids and more">
<img src="https://affiliates.jlist.com/media/3638/129" style="width:60%;" alt="Click for Japanese study aids and more" border="0"><br />Japanese study aids and more at J-List</a>
</div>
<!-- End J-List Affiliate Code -->';
			echo '</div>
			<div align="left" style="padding:5px;">
				<div style="padding-left:20px;font-weight:bold;">About AnimeFTW.tv:</div>
				<div style="font-size:10px;">AnimeFTW.tv is a Free Streaming Anime Website, dedicated to spreading Anime love through High Quality Video Streams. The AnimeFTW.tv Staff are 100% Volunteers, we rely on member donations and subscriptions to keep our servers running!</div>
			</div>
			<script> 
			<!-- 
			//
			var milisec=0 
            var seconds=31
            document.counter.d2.value=\'31\'      
            function display(){ 
            	if (milisec<=0){ 
					milisec=9; 
					seconds-=1;
				} 
				if(seconds === 0)
				{
					$("#launch-button").removeAttr("disabled").attr("value"," Your Video is Available! Click to Launch.");
					$("#launch-button").attr("onClick","ftwentertainment.vidlaunch(\'' . urldecode($_GET['title']) . '\',\'' . urldecode($_GET['url']) . '\'); return false;");
				}
				if (seconds<=-1){ 
					milisec=0;
					seconds+=1;
				} 
				else {
					milisec-=1 
					document.counter.d2.value=seconds
					setTimeout("display()",100)
				}
			} 
			display()
			--> 
			$(document).keyup(function(e) {

				if (e.keyCode == 27) { $("#overlay").hide();$("#overlay-content").html(""); }   // esc
			});
			</script>
			<script>
			$(document).mouseup(function (e)
			{
				var container = $("#ad-overlay");

				if (!container.is(e.target) // if the target of the click isn\'t the container...
					&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					$("#overlay").hide();
					$("#overlay-content").html("");				
				}
			});
			</script>';
		echo '</div>';
	}
}
?>