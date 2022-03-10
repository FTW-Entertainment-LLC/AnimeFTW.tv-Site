<?php
/****************************************************************\
## FileName: forumview.forum.class.php
## Author: Brad Riemann
## Usage: Single Forum Class
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWForumView {
	var $reqforum,$perms,$reqLimit,$Logged,$timezone;

	public function Con($reqforum,$perms,$reqLimit,$Logged,$timezone){
		$this->reqforum = $reqforum;
		$this->perms = $perms;
		if($reqLimit == ''){$reqLimit = 0;}
		$this->reqLimit = $reqLimit;
		$this->Logged = $Logged;
		$this->timezone = $timezone;
	}

	public function ForumDisplay(){
		$query2 = "SELECT fid, ftitle, fseo FROM forums_forum WHERE fseo='".$this->reqforum."' AND fpermission LIKE '%".$this->perms."%'";
		mysqli_query($conn, "SET NAMES 'utf8'");
		$result2 = mysqli_query($conn, $query2);
		$row2 = mysqli_fetch_array($result2);
		$fid = $row2['fid'];
		$ftitle = $row2['ftitle'];
		$fseo = $row2['fseo'];
		if ($fid == '') {
			echo "<div id=\"navstrip\"><img src='//animeftw.tv/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a></div><br />";
			echo "<div class='errorwrap'><h4>The error returned was:</h4><p>Sorry, the link that brought you to this page seems to be out of date or broken.</p></div><br />";
		}
		else {
			echo "<div id=\"navstrip\"><img src='//animeftw.tv/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;<a href='/forums/".$fseo."/'>$ftitle</a></div><!-- Bgin subforums?! --><table style='width:100%' cellspacing=\"0\"><tr><td style='padding-left:0px' width=\"60%\">";
			//paginate here!
			$requestedLimit = $this->reqLimit;
			$requestedLimit = $requestedLimit+0;
			$requestedLimitUp = $this->reqLimit+30;
			$requestedLimitDown = $this->reqLimit-30;
			$query = mysqli_query($conn, "SELECT COUNT(pid) FROM forums_post WHERE pfid ='$fid'");
			$users = mysqli_result($query, 0);
			$page_count = round(($users/30)+1);
			echo "&nbsp;";
			//New Paging
			$query9 = mysqli_query($conn, "SELECT COUNT(tid) FROM forums_threads WHERE tfid='$fid'");
			$total_threadv2 = mysqli_result($query9, 0);
			include_once('includes/classes/forumpaging.class.php');
			$paging = new AFTWForumPageing();
			$paging->get_icount($total_threadv2);
			$paging->get_start($this->reqLimit);
			$paging->get_setlimit('30');
			$paging->get_url('/forums/'.$fseo.'/');
			echo $paging->ShowPaging();
			echo "</td>";
			//END PAGINATE (TOP)
			//NEW TOPIC POST START (REQUIRES A USER TO BE LOGGED IN)
			echo "<td class='nopad' style='padding:0px 0px 5px 0px' align=\"right\" nowrap=\"nowrap\">";
			if ($this->Logged != 1){}
			else {
				$query = "SELECT fid FROM forums_forum WHERE fseo='".$this->reqforum."' AND fpermpost LIKE '%".$this->perms."%'";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result);
				$allowed = $row['fid'];
				if ($allowed ==''){}
				else {
					echo "<span class='forumbottons'><a href='/forums/post/topic-$fid'><img align='middle' src='//animeftw.tv/images/forumimages/posttopic.jpg' alt='topic start' style='margin-bottom:5px;' /></a></span>&nbsp;";
				}
			}
			echo "</td></tr></table>";
			//END TOPIC START BUTTON
			//START FORUM STUFFS! (header)
		echo "<form id='modform' name='modform' method='post' action='/forums/modaction'>";
		echo "<table cellpadding='0' cellspacing='1' width='100%'>\n<tr>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'>&nbsp;</td>\n";
		echo "<td class='tbl2 forum-cap'>Thread Title</td>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'>Replies</td>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap' align='left' >Thread Starter</td>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap' align='center'>Views</td>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'>Last Action</td>\n";
		if ($this->perms == 1 || $this->perms == 2 || $this->perms == 6) {
			echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'><input type=\"checkbox\" id=\"selectall\" /></td>\n";
	  	}
		else {}
		echo "</tr>\n";
		//END HEADER STUFFS..
		//BEGIN FORUM TOPICS!
		$tl = new AFTWThreads();
		$tl->Con($fid,$fseo,$this->reqLimit,$this->perms,$this->timezone);
		$tl->ThreadListDisplay();
		//END TOPIC RUN!
		//BEGIN FOOTER!
		//echo "</table></td></tr>";
		if ($this->perms == 1 || $this->perms == 2 || $this->perms == 6) {
			echo "<table cellspacing='0' cellpadding='0' width='100%'>\n<tr>\n<td style='padding-top:5px'>";
			echo "<tr><td><div align='right'>\n
			  <input type='hidden' name='act' value='mod' />
			  <input type='hidden' name='CODE' value='topicchoice' />
			  <input type='hidden' name='f' value='$fid' />
			  <input type='hidden' value='' name='selectedtids' />
			  <select name='tact'>
				  <option>- Chose an Action -</option>
				  <option value='close'>Close Topics</option>
				  <option value='open'>Open Topics</option>
				  <option value='pin'>Pin Topics</option>
				  <option value='unpin'>Unpin Topics</option>
				  <option value='move'>Move Topics</option>
				  <option value='merge'>Merge Topics</option>
				  <option value='delete'>Delete Topics</option>
				  <option value='approve'>Set Visible (Approve)</option>
				  <option value='unapprove'>Set Invisible (Unapprove)</option>
			  </select>&nbsp;<a href=\"#\" onClick=\"modform.submit(); return false;\">Go</a>
			  </div></td></tr>";
	   		echo "</tr>\n</table>\n";
	 	}
		else {}
		echo "</table></form></div><br /><br /><br />";
		}
	}
}

?>