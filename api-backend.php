<?php
if($_SERVER['SERVER_PORT'] == '80' && $_SERVER['HTTP_HOST'] == 'www.animeftw.tv')
{
	header("location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}
#***********************************************************
#* api-backend.php, API REST scripts for AnimeFTW.tv
#* Written by Brad Riemann
#* Copywrite 2011 FTW Entertainment LLC
#* Distribution of this is stricty forbidden
#***********************************************************
include('includes/siteroot.php');
include('includes/classes/config.class.php');
include('includes/classes/users.class.php');
include('includes/classes/api.class.php');

# The idea of this script is to create a working REST API, REST APIs revolve around GET based HTTP authentication, 
# So what better of a way to implement than via the URL!?
# the basic concept is putting forth the username and the Dev id, without either one,
# We can't have a valid API transaction!

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Pragma: no-cache"); // HTTP/1.0
header ("Content-Type: text/xml");

//$ = $_GET[''];
$DevID = @$_GET['did'];
$Dbver = (isset($_GET['dbver'])) ? TRUE : FALSE;
$Username = @$_GET['username'];
$Username = str_replace("%20","",$Username);
$Password = @$_GET['password'];
$android = (isset($_GET['client']) && $_GET['client'] == "FtwAndApp") ? TRUE : FALSE;
$version = (isset($_GET['version'])) ? $_GET['version'] : 0;
$appkey = ($android == TRUE) ? array("FtwAndApp",$version) : array(NULL,NULL);
$alpha = isset($_GET['alpha']) ? mysql_real_escape_string($_GET['alpha']) : NULL;
if(@$_SERVER['HTTP_USER_AGENT'] == ''){
	if(isset($_GET['version'])){
		$agent = $_GET['client'].' running '.$_GET['version'];
	}
	else {
		$agent = $_GET['client'];
	}
}
else {
	$agent = @$_SERVER['HTTP_USER_AGENT'];
} //Agent string
$gsort = @$_GET['filter'];

//Header switch. Android XML reader breaks with standard header.
if($android == true) {
	//Discard the header
} else {
	echo '<?xml version="1.0" standalone="yes"?>'."\n";
}
if(!isset($DevID)){
	echo '<result code="201" title="No Development ID given" />'."\n";
	$devClear = FALSE;
}
else {
	if($_SERVER['REMOTE_ADDR'] == '66.249.72.201'){
		echo '<result code="403" title="Access Denied, API Access revoked." />'."\n";
	}
	else {
		$dev = new AFTWDev();
		$dev->get_did($DevID);
		$dev->RecordAnalytics();
		//Check Dev id
		
		if($dev->ValDID() == 1){
			$devClear = TRUE;
			if($Dbver == TRUE) {
				//Specialized database version check
				//Android app uses this for caching
				$dev->checkdbversion();
				$userClear = FALSE;
			} elseif(!isset($Username)){
				echo '<result code="203" title="No Username Given" />'."\n";
				$devClear = FALSE;
			}
			else {
				$u = new AftwUser;
				$u->get_username($Username);
				$u->get_password($Password);
				if(!isset($Password)){
					echo '<result code="202a" title="Invalid User ID/Password" />'."\n";
					$userClear = FALSE;
				}
				else {
					//Check for valid username..
					if($u->apiUserCheck() == 1){
						if($u->apiActiveCheck() == 1){
							$userClear = TRUE;
						}
						else {
							echo '<result code="403" title="Inactive Account, Access is Denied" />';
							$userClear = FALSE;
						}
					}
					else {
						echo '<result code="202b" title="Invalid User ID/Password" />';
						$userClear = FALSE;
					}	
				}
			}
		}
		else {
			echo '<result code="202" title="Invalid Development ID" />'."\n";
			$devClear = FALSE;
		}
	}		
}
if($devClear == TRUE && $userClear == TRUE){
	$Start = @$_GET['start'];
	$Count = @$_GET['count'];
	if(!isset($Count)){$Count = 15;}
	if(!isset($Start)){$Start = 0;}
	if(!isset($gsort)){$gsort = NULL;}
	$dev->RecordDevLogs($Username,$_SERVER['REQUEST_URI'],$agent,$_SERVER['REMOTE_ADDR']);
	$dev->RecordUsername($Username);
	if(isset($_GET['show']) && $_GET['show'] == 'anime'){
		//ADAM WUZ HAR. This line will allow alpha separation of results.
		//Prevents an overload of data for the Android App.
		$dev->ShowAnime('ASC',$Count,$Start,$Username,$Password,$gsort,$alpha);
	}
	else if(isset($_GET['show']) && $_GET['show'] == 'latest'){
		$dev->ShowLatestEpisodes('DESC',$Count,$Start,$Username,$Password,$dev->CheckUser($Username),$dev->ShowAds());
		$dev->showbtmresult();
	}
	else if(isset($_GET['show']) && $_GET['show'] == 'series' && isset($_GET['title'])){
		$dev->get_seriesname($_GET['title']);
		$dev->showtopresult();
		$dev->ShowAnimeEpisodes('ASC',$Username,$Password,$dev->CheckUser($Username),$dev->ShowAds(),$appkey);
		$dev->ShowAnimeMovies('ASC',$Username,$Password,$dev->CheckUser($Username),$dev->ShowAds(),$appkey);
		$dev->showbtmresult();
	}
	else if(isset($_GET['show']) && $_GET['show'] == 'episode' && isset($_GET['id'])){
		$dev->showtopresult();
		$dev->get_episodeid($_GET['id']);
		$dev->ShowEpisode();
		$dev->showbtmresult();
	}
	else if(isset($_GET['show']) && $_GET['show'] == 'tagcloud'){
		$dev->showtopresult();
		$dev->ShowTagCloud($Username,$Password);
		$dev->showbtmresult();
	}
	else if(isset($_GET['show']) && $_GET['show'] == 'search' && isset($_GET['for'])){
		$dev->showtopresult();
		$dev->Search('DESC',$Count,$Start,$Username,$Password,$_GET['for']);
		$dev->showbtmresult();
	}
	//NEW: User status system. Feeds back advanced status and passed login
	else if(isset($_GET['show']) && $_GET['show'] == "userstatus") {
		$isprem = ($dev->CheckUser($Username) == TRUE) ? "0" : "1";
		echo '<result code="027" premium="'.$isprem.'" />';
	}
	else {
		echo '<result code="301" title="Erronious API GET" />';
	}
}
?>