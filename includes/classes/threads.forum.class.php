<?php
/****************************************************************\
## FileName: threads.forum.class.php
## Author: Brad Riemann
## Usage: Thread Class
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWThreads extends Config {
	var $fid,$fseo,$reqLimit,$perms,$timezone;

	public function __construct()
	{
		parent::__construct();
	}

	public function Con($fid,$fseo,$reqLimit,$perms,$timezone){
		$this->fid = $fid;
		$this->fseo = $fseo;
		$this->reqLimit = $reqLimit;
		$this->perms = $perms;
		$this->timezone = $timezone;
	}

	public function ThreadListDisplay(){
		$this->DisplayPinnedTopics();
		// ADDED 27/03/15 by Robotman321
		$restrictHiddenThreads = "";
		if($this->UserArray[2] != 1 && $this->UserArray[2] != 2)
		{
		        $restrictHiddenThreads = " AND `hidden` = 0";
		}

		$query4  = "SELECT tid, ttitle, tpid, tfid, tclosed, `hidden`, `tviews` FROM forums_threads WHERE tfid='".$this->fid."' AND tstickied='0'" . $restrictHiddenThreads . " ORDER BY tclosed ASC, tupdated DESC LIMIT ".$this->reqLimit.", 30";
		$result4 = mysqli_query($conn, $query4);
		echo "<tr>\n";
		echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'>&nbsp;</td>\n";
		echo "<td class='tbl2 forum-cap'><strong>Forum Topics</strong></td>\n";
		echo "</tr>\n";
		while(list($tid,$ttitle,$tpid,$tfid,$tclosed,$hidden,$tviews) = mysqli_fetch_array($result4)) {
			$ttitle = stripslashes($ttitle);
			$ttitle = htmlentities($ttitle); //HTML exploit fix, Zigbigidorlu was here =D
			$subjectPreffix = '';
			if($hidden == 1)
			{
				// Hidden topic
				$thread_image = "<img src='//i.animeftw.tv/forumimages/f_closed.gif' border='0' alt='Closed Topic' title='Topic is Hidden!' />";
				$subjectPreffix = '<span style="color:gray;">Hidden:</span> ';
				echo '<tr style="background-color:gray;">'."\n";
			}
			else
			{
				echo "<tr>\n";
				if ($tclosed == 1)
				{
		    			$thread_image = "<img src='//i.animeftw.tv/forumimages/f_closed.gif' border='0' alt='Closed Topic' />";
		  		}
				else
				{
		   			$thread_image = "<img src='//i.animeftw.tv/forumimages/f_norm_no_dot.gif' border='0' alt='Open Topic' />";
		  		}

			}
			$thread_subject = $subjectPreffix . "<a id='topic-".$tid."' href='/forums/".$this->fseo."/topic-".$tid."/s-0' >".$ttitle."</a>";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$thread_image."</td>\n";
			echo "<td width='100%' class='tbl1'>".$thread_subject."</td>\n";
			$query3 = mysqli_query($conn, "SELECT COUNT(pid) FROM forums_post WHERE ptid='$tid'");
			$total_thread_posts = mysqli_result($query3, 0);
			$total_thread_posts2 = $total_thread_posts-1;
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$total_thread_posts2."</td>\n";
			echo "<td align='left' width='1%' class='tbl1' style='white-space:nowrap'>" . $this->formatUsername($tpid)."</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$tviews."</td>\n";
			//this would be a good time to make a mysql update for this topic.. for whever a person looks at it...
			$query02 = "SELECT pid, puid, pdate FROM forums_post WHERE ptid='$tid' ORDER BY pid DESC LIMIT 1";
			$result02 = mysqli_query($conn, $query02);
			$row02 = mysqli_fetch_array($result02);
			$pid = $row02['pid'];
			$puid = $row02['puid'];
			$pdate3 = $row02['pdate'];
			$pdate3 = $this->timeZoneChange($pdate3,$this->timezone);
			$pdate4 = date("M j Y, h:i A",$pdate3);
			$last_post_by = "<a href='/forums/".$this->fseo."/topic-".$tid."/showlastpost'>Last post by:</a>&nbsp;" . $this->formatUsername($puid);
			echo "<td width='1%' class='tbl1' style='white-space:nowrap'>".$pdate4."<br />".$last_post_by."</td>\n";

			if ($this->perms == 1 || $this->perms == 2 || $this->perms == 6) {
		    	$input_checkbox = "<input class='modcheck' type='checkbox' name='modcheck-".$tid."' value='".$tid."' />";
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$input_checkbox."</td>\n";
	  		}
			else {}

			echo "</tr>\n";
	  	}
		echo "</table><!--sub_forum_table-->\n";
	}

	private function DisplayPinnedTopics(){
		$query05  = mysqli_query($conn, "SELECT COUNT(tid) FROM forums_threads WHERE tfid='".$this->fid."' AND tstickied='1'");
		$total_stickies = mysqli_result($query05, 0);
		if ($total_stickies == 0){}
		else {
			if($total_stickies < $this->reqLimit){
			}
			else {
				$query04  = "SELECT tid, ttitle, tpid, tfid, tclosed, tviews FROM forums_threads WHERE tfid='".$this->fid."' AND tstickied='1' ORDER BY tclosed ASC, tupdated DESC LIMIT ".$this->reqLimit.", 30";
				mysqli_query($conn, "SET NAMES 'utf8'");
				$result04 = mysqli_query($conn, $query04);
				echo "<tr>\n";
				echo "<td class='tbl2 forum-cap' width='1%' style='white-space:nowrap'>&nbsp;</td>\n";
				echo "<td class='tbl2 forum-cap'><strong>Pinned Topics</strong></td>\n";
				echo "</tr>\n";

				while(list($tid,$ttitle,$tpid,$tfid,$tclosed,$tviews) = mysqli_fetch_array($result04)) {
					$ttitle = stripslashes($ttitle);
					$ttitle = htmlentities($ttitle);				   
					echo "<tr>\n";
					echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>\n";
					if ($tclosed == 1) {
						echo "<img src='//i.animeftw.tv/forumimages/f_closed.gif' border='0' alt='Closed Topic' />";
					} else {
						echo "<img src='//i.animeftw.tv/forumimages/f_norm_no_dot.gif' border='0' alt='Open Topic' />";
					}
					echo "</td>\n";
					echo "<td width='100%' class='tbl1'><span id='tid-span-$tid'><font color='red'>Read First: </font><a id=\"topic-$tid\" href=\"/forums/".$this->fseo."/topic-$tid/s-0\" >$ttitle</a></span></td>\n";

					echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>\n";
					$query3 = mysqli_query($conn, "SELECT COUNT(pid) FROM forums_post WHERE ptid='$tid'");
					$total_thread_posts = mysqli_result($query3, 0);
					$total_thread_posts2 = $total_thread_posts-1;
					echo $total_thread_posts2;
					echo "</td>\n";
					echo "<td align='left' width='1%' class='tbl1' style='white-space:nowrap'>".$this->formatUsername($tpid)."</td>\n";
					echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$tviews."</td>\n";

					//this would be a good time to make a mysql update for this topic.. for whever a person looks at it...
					$query02 = "SELECT pid, puid, pdate FROM forums_post WHERE ptid='$tid' ORDER BY pid DESC LIMIT 1";
					$result02 = mysqli_query($conn, $query02);
					$row02 = mysqli_fetch_array($result02);
					$pid = $row02['pid'];
					$puid = $row02['puid'];
					$pdate3 = $row02['pdate'];
					$pdate3 = $this->timeZoneChange($pdate3,$this->timezone);
					$pdate4 = date("M j Y, h:i A",$pdate3);
					$last_post_by = "<a href='/forums/".$this->fseo."/topic-".$tid."/showlastpost'>Last post by:</a>&nbsp;".$this->formatUsername($puid);

					echo "<td width='1%' class='tbl1' style='white-space:nowrap'>".$pdate4."<br />".$last_post_by."</td>\n";

					if ($this->perms == 1 || $this->perms == 2 || $this->perms == 6) {
						$input_checkbox = "<input class='modcheck' type='checkbox' name='modcheck-".$tid."' value='".$tid."' />";
						echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$input_checkbox."</td>\n";
					} else {
					}
					echo "</tr>\n";
				}
			}
		}
	}
}

?>
