<?php
/****************************************************************\
## FileName: mainview.forum.class.php
## Author: Brad Riemann
## Usage: Forum Mainview Class
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWForumMain extends Config {
	var $perms, $timezone;

	public function __construct()
	{
		parent::__construct();
	}

	public function Con($perms,$timezone)
	{
		$this->perms = $perms;
		$this->timezone = $timezone;
	}

	public function MainDisplay(){
		echo "<div id='navstrip'><img src='//i.animeftw.tv/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums/'>AnimeFTW.TV Forums</a></div><br />";
		$query1 = "SELECT cid, ctitle, cpermission, cseo FROM forums_categories WHERE cpermission LIKE '%".$this->perms."%' ORDER BY corder ";
		mysqli_query($conn, "SET NAMES 'utf8'");
		$result1 = mysqli_query($conn, $query1) or die('Error : ' . mysqli_error());
		while(list($cid,$ctitle,$cpermission,$cseo) = mysqli_fetch_array($result1)) {
			echo "<table cellpadding='0' cellspacing='1' width='100%' class='forum_idx_table'>\n";
			echo "<tr>\n<td colspan='2' class='forum-caption forum_cat_name'>".$ctitle."</td>\n";
			echo "<td align='center' width='1%' class='forum-caption' style='white-space:nowrap'>Threads</td>\n";
			echo "<td align='center' width='1%' class='forum-caption' style='white-space:nowrap'>Replies</td>\n";
			echo "<td width='1%' class='forum-caption' style='white-space:nowrap'>Last Post Info</td>\n";
			echo "</tr>\n";
			$query200 = "SELECT fid, fpermission, ftitle, fdescription, ficon, fcid, fseo FROM forums_forum WHERE fcid='".$cid."' AND fpermission LIKE '%".$this->perms."%' ORDER BY forder ASC";
			$result200 = mysqli_query($conn, $query200) or die('Error : ' . mysqli_error());
			while(list($fid,$fpermission,$ftitle,$fdescription,$ficon,$fcid,$fseo) = mysqli_fetch_array($result200)) {
				$fdescription = stripslashes($fdescription);
				$fim = "<img src='//i.animeftw.tv/forumimages/bf_new.png' border='0'  alt='Posts!' />";
				echo "<tr>\n";
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$fim</td>\n";
				echo "<td class='tbl1 forum_name' valign='top'><!--forum_name--><a href='/forums/".$fseo."/'>".$ftitle."</a><br />\n";
				echo "<span class='small'>".$fdescription."</span>\n";
				$total_forum_threads = mysqli_query($conn, "SELECT COUNT(pid) FROM forums_post WHERE pfid ='$fid' AND pistopic='1'");
				$total_forum_threads  = mysqli_result($total_forum_threads , 0);
				$total_forum_posts = mysqli_query($conn, "SELECT COUNT(pid) FROM forums_post WHERE pfid ='$fid' AND pistopic='0'");
				$total_forum_posts = mysqli_result($total_forum_posts, 0);
				$query5 = mysqli_query($conn, "SELECT p.pdate, p.puid, t.ttitle, t.tid FROM forums_threads AS t, forums_post AS p, users AS u WHERE t.tfid = '$fid' AND p.ptid=t.tid AND u.ID=p.puid ORDER BY p.pid DESC LIMIT 0, 1;");
				$row5 = mysqli_fetch_array($query5);
				$ptid = $row5['tid'];
				$puid = $row5['puid'];
				$ptitle = $row5['ttitle'];
				$pdate = $row5['pdate'];
				$pdate2 = $this->timeZoneChange($pdate,$this->timezone);
				if ($pdate2 == ''){$pdate1 = '';$ptitle1 = '';$GetLastPost = "<img src='//i.animeftw.tv/forumimages/lastpost.gif' border='0'  alt='Last Post' />";$Username = '';
			  	}
				else {
					$pdate1 = date("M j Y, h:i A",$pdate2);
					$ptitle = stripslashes($ptitle);
					if(strlen($ptitle) <= 20){
					}
					else {
						$ptitle = substr($ptitle,0,18).'&hellip;';
					}
					$GetLastPost = "<a href='/forums/$fseo/topic-$ptid/showlastpost' title='Go to the last post'><img src='//i.animeftw.tv/forumimages/lastpost.gif' border='0' alt='Last Post' /></a>";
				}
				$first_unread = "<a href='/forums/$fseo/topic-$ptid/showlastpost' title='Go to the first unread post: $ptitle'>$ptitle</a>";
				echo "</td>\n";
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$total_forum_threads."</td>\n";
				echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".$total_forum_posts."</td>\n";
				echo "<td width='1%' class='tbl2' style='white-space:nowrap'>".$GetLastPost."&nbsp;".$pdate1."<br />\n";
				echo "<span class='small'><b>In:</b>&nbsp;".$first_unread."<br /><b>By:</b> " . $this->formatUsername($puid) . "</span></td>\n";
				echo "</tr>\n";
			}
			echo "<tr><td class='catend' colspan='5'><!-- no content --></td></tr></table></div><br />";
		}
		//Site &amp; Board Statistics
		echo "<table cellpadding='0' cellspacing='1' width='100%'>\n<tr>\n";
		echo "<td align='left' width='1%' class='forum-caption' style='white-space:nowrap'>Site &amp; Board Statistics</td>\n";
		echo "</tr></table>\n";
		echo "<table cellpadding='0' cellspacing='1' width='100%'>\n<tr>\n";
		echo "<td class='tbl2' width='1%' style='white-space: no-wrap;' rowspan='4'><center><img src='//i.animeftw.tv/forumimages/user.png' border='0' alt='Active Users' /></center></td>\n";
		echo "</tr>\n<tr>\n";
		$FinalDate3 = time()-900;
		$ou24h = mysqli_query($conn, "SELECT COUNT(ID) FROM users WHERE lastActivity>='$FinalDate3'");
		$online_users_24hours = mysqli_result($ou24h, 0);

		echo "<td class='tbl2' align='right'><span style='float: left; padding-left:5px;'>$online_users_24hours user(s) active in the past 15 minutes</span><a href='/forums/active-topics'>Today's active topics</a>&nbsp;</td>\n";
		echo "</tr>\n<tr>\n";
		echo "<td class='tbl1'>\n";
		echo $this->LatestActivity($FinalDate3);
		echo "</td>\n";
		echo "</tr>\n<tr>\n";
		echo "<td class='tbl2'><img src='//i.animeftw.tv/adminbadge.gif' alt='Admin of Animeftw' style='vertical-align:middle;' /><a href=\"/staff/\">Admin</a> | <a href=\"/staff\">Moderator</a> | <a href=\"/staff/\">Site Staff</a> | <img src='//i.animeftw.tv/advancedimages/default.gif' alt='Advanced User Title' style='vertical-align:middle;' /><a href='/advanced'>Advanced Member</a> | <a href='/user/'>Basic Member</a></td>\n";
		echo "</tr>\n<tr>\n";
		//Board Statistics
		echo "<td class='tbl2' width='1%' style='white-space: no-wrap;' rowspan='4'><center><img src='//i.animeftw.tv/forumimages/stats.png' border='0' alt='Board Stats' /></center></td>\n";
		echo "</tr>\n<tr>\n";
		echo $this->SimpleStats();
		echo "</tr>\n</table>\n";
	}

	// Function just to give teh latest activity
	private function LatestActivity($FinalDate3){
		$query19 = "SELECT ID, lastActivity FROM users WHERE lastActivity>='".$FinalDate3."' ORDER BY lastActivity DESC";
		$result19 = mysqli_query($conn, $query19) or die('Error : ' . mysqli_error());

		$count = mysqli_num_rows($result19);
		$online_users_24hours = "";
		if (count($online_users_24hours))
		{
		    $i = 0;
			while(list($ID,$lastActivity) = mysqli_fetch_array($result19))
			{
				echo $this->formatUsername($ID,'self',$lastActivity);
				if($i <= $count)
				{
					echo ",\n";
				}
				else
				{
					echo "\n";
				}

				$i++;
			}
		}
	}

	// Something simple for our stats... booya.
	private function SimpleStats(){
		$result = mysqli_query($conn, "SELECT COUNT(ID) AS UserCount, (SELECT ID FROM users WHERE active='1' ORDER BY id DESC LIMIT 0, 1) AS ChosenUser, (SELECT COUNT(pid) FROM forums_post) AS ForumPosts FROM users WHERE Active='1'");
		$row = mysqli_fetch_assoc($result);
		$TotalUsers = $row['UserCount'];
		$ChosenUser = $row['ChosenUser'];
		$TotalPosts = $row['ForumPosts'];

		echo "<td colspan='3' class='tbl1'>Our members have made a total of <b>$TotalPosts</b> posts<br />We have <b>$TotalUsers</b> registered members<br />The newest member is <b>" . $this->formatUsername($ChosenUser) . "</b></td>\n";
	}
}
?>