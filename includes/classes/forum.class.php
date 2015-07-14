<?php
/****************************************************************\
## FileName: forum.class.php									 
## Author: Brad Riemann										 
## Usage: Forum Class
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Forum extends Config {

	var $action, $forum, $thread, $s;

	public function __construct()
	{
		parent::__construct();
	}
	
	// small constructor for my vars..
	public function buildVars($action,$UserArray){
		$this->action = $action;
		$this->profileArray = $UserArray;
	}
	
	// Small Copyright function, cause i can..	
	public function Copyright(){
		return 'For The Win forums version 2.5 <br /> copyright &copy; FTW Entertainment LLC, 2008-'.date("Y").', all rights reserved.';
	}
	// output function
	public function Output(){
		if($this->profileArray[9] == 1){
			echo "<h3>ERROR: Your forum privileges have been revoked and you will have no access to the forums.</h3>";
		}
		else {
			if($this->action == 'activetopics'){
				echo $this->showActiveTopics($this->profileArray[2],$this->profileArray[3]); 
			}
			else if($this->action == 'post'){
				include_once('includes/postview.function.php'); 
			}
			else if($this->action == 'modaction'){
				//echo $_POST['modcheck'];
					foreach($_POST as $key => $value) {
						echo $value.', ';
					// Process here using $value for the content of the field.
					}
			}
			else {
				if(isset($_GET['thread'])){
					//include('includes/threadview.function.php'); 
					$tv = new AFTWThreadView();
					$tv->Con(@$_GET['s'],@$_GET['thread'],@$_GET['forum'],$this->profileArray); //$start,$tid,$fseo,$profileArray
					$tv->DisplayThread();
				}
				if(isset($_GET['forum']) && !isset($_GET['thread'])) {
					$fv = new AFTWForumView();
					$fv->Con($_GET['forum'],$this->profileArray[2],@$_GET['s'],$this->profileArray[0],$this->profileArray[3]);
					$fv->ForumDisplay();
				}
				if(!isset($_REQUEST['forum']) && !isset($_REQUEST['action']) && !isset($_GET['s'])) {
					$fm = new AFTWForumMain();
					$fm->Con($this->profileArray[2],$this->profileArray[3]);
					$fm->MainDisplay();
				}
			}
		}
		//array($Logged,$globalnonid,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme)
		/*if(!isset($_REQUEST['forum']) && !isset($_REQUEST['action']) && !isset($_GET['s'])) {
		echo  forumMainView($profileArray[2],$profileArray[3]);
		}
		
		if(isset($_REQUEST['forum']) && !isset($_REQUEST['thread'])) {
			echo forumForumView ($_REQUEST['forum'],$profileArray[2],$_GET['s'],$profileArray[0]);
		}
		if(isset($_REQUEST['thread'])) {
		///echo 'Threads View.';
			include('includes/threadview.function.php'); 
		}
		if(@$_REQUEST['action'] == 'post') {
		//echo 'Post View';
		}
		if(@$_REQUEST['action'] == 'activetopics') {
			echo showActiveTopics($profileArray[2],$profileArray[3]); 
		}	*/
	}

	private function showActiveTopics ($PLA,$timeZone){
		echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;Today's Active Topics</div>
<!-- Bgin subforums?! -->";
		//END TOPIC START BUTTON
		//START FORUM STUFFS! (header)
		echo "<div class=\"borderwrap\"><br /><br /> <table class='ipbtable' cellspacing=\"0\"><tr><td colspan='8' class='darkrow1'><div><div style='float: left;'>Active Topics for the past 24 Hours</div></div></td></tr><tr class='foruminfo3'><th width=\"37%\" nowrap=\"nowrap\">&nbsp;&nbsp;Thread Title</th><th width=\"13%\" style=\"text-align:center\" nowrap=\"nowrap\">Forum</th><th width=\"13%\" style=\"text-align:center\" nowrap=\"nowrap\">Thread Starter</th><th width=\"4%\" style=\"text-align:center\" nowrap=\"nowrap\">Replies</th><th width=\"4%\" style=\"text-align:center\" nowrap=\"nowrap\">Views</th><th width=\"19%\" style=\"text-align:center\" nowrap=\"nowrap\">Last Action</th></tr>";
					
		$FinalDate3 = time()-86400;
		// ADDED 27/03/15 by robotman321
                $hiddenLimit = "";
                if($this->UserArray[2] != 1 && $this->UserArray[2] != 2)
                {
                        $hiddenLimit = " AND `t1`.`hidden` = 0";
                }
		$query4 = "SELECT t1.tid, t1.ttitle, t1.tpid, t1.tfid, `t1`.`hidden`, t1.tviews, t2.ftitle, t2.fseo FROM forums_threads AS t1 LEFT JOIN forums_forum AS t2 ON t1.tfid = t2.fid WHERE (t2.fpermission LIKE '%".$PLA."%' AND t1.tupdated>='".$FinalDate3."' AND t1.tclosed=0" . $hiddenLimit  . ") ORDER BY t1.tupdated DESC";
		mysql_query("SET NAMES 'utf8'"); 
		$result4 = mysql_query($query4) or die('Error : ' . mysql_error());
		$numcount = mysql_num_rows($result4);
		if($numcount == 0){
			echo "<tr><td class='row1' colspan=\"5\"><div style=\"padding-left:30px;\"><h3>There have been no replies in the last 24 hours.</td></tr>";
		}
		while(list($tid,$ttitle,$tpid,$tfid,$thidden,$tviews,$ftitle,$fseo) = mysql_fetch_array($result4)){
			$ttitle = stripslashes($ttitle);
							
			//HTML exploit fix
			//Zigbigidorlu was here =D
			$ttitle = htmlentities($ttitle);
							
			$query3 = mysql_query("SELECT COUNT(pid) FROM forums_post WHERE ptid='$tid' AND pistopic='0'"); 
			$total_thread_posts = mysql_result($query3, 0);
			$query02 = "SELECT pid, puid, pdate FROM forums_post WHERE ptid='$tid' ORDER BY pid DESC LIMIT 0, 1";
			$result02 = mysql_query($query02) or die('Error : ' . mysql_error());
			$row02 = mysql_fetch_array($result02);
			$pid = $row02['pid'];
			$puid = $row02['puid'];
			$pdate3 = $row02['pdate'];
			$pdate3 = timeZoneChange($pdate3,$timeZone);
			$pdate4 = date("M j Y, h:i A",$pdate3);
			// hidden..
			$ThreadHidden = "";
			if($thidden == 1)
			{
				$ThreadHidden = "<span style='color:gray;'>Hidden:</span> ";
			}
			echo "<tr><td class='row1'>" . $ThreadHidden . "<a href='/forums/$fseo/topic-$tid/s-0'>$ttitle</a></td><td class='row1' align='center'><a href='/forums/$fseo/'>$ftitle</a></td><td class='row1' align='center'>".$this->formatUsername($tpid)."</td><td class='row1' align='center'>$total_thread_posts</td><td class='row1' align='center'>$tviews</td><td class='row1' align='center'>$pdate4<br /><a href='/forums/$fseo/topic-$tid/showlastpost'>Last post by:</a><b>".checkUserNameNumber($puid)."</b></td></tr>";
		}
		echo "</table></td></tr>";
		echo "</table></div><br /><br /><br />";
	}
	
	public function addTopicView($tid) {
		$query = "UPDATE forums_threads SET tviews = tviews+1 WHERE tid = $tid";
		$result = mysql_query($query);		
	}
}


?>
