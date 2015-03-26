<?php
/****************************************************************\
## FileName: threadview.forum.class.php									 
## Author: Brad Riemann										 
## Usage: Shows the guts of the Thread
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWThreadView extends Config{
	var $start, $tid, $fseo, $paging, $profileArray;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function Con($start,$tid,$fseo,$profileArray){
		if($start == ''){$start = 0;}
		$this->start = $start;
		$this->tid = $tid;
		$this->fseo = $fseo;
		$this->paging = 20;
		$this->profileArray = $profileArray;
	}
	
	public function DisplayThread(){
		$query7 = "SELECT f.fid, f.ftitle, f.fseo, t.tid, t.tclosed, t.ttitle FROM forums_forum AS f, forums_threads AS t WHERE f.fseo = '".$this->fseo."' AND t.tid = '".$this->tid."'";
		$result7 = mysql_query($query7) or die('Error : ' . mysql_error());
		$row7 = mysql_fetch_array($result7);
		$fid = $row7['fid'];
		$ftitle = $row7['ftitle'];
		$fseo = $row7['fseo'];
		$tclosed = $row7['tclosed'];
		$ttitle = $row7['ttitle'];
		$topicid = $row7['tid'];
		if ($fseo != $this->fseo){
		echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a></div><br />";
			echo "<div class='errorwrap'>
							<h4>The error returned was:</h4>									
							<p>Sorry, the link that brought you to this page seems to be out of date or broken. E=1</p>									
			</div><br />";
		}
		else {
			$query009 = mysql_query("SELECT COUNT(fid) FROM forums_forum WHERE fid='$fid' AND fpermission LIKE '%".$this->profileArray[2]."%'"); 
			$total_forum = mysql_result($query009, 0);
			if ($total_forum == 0){
				echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a></div><br />";
				echo "<div class='errorwrap'>
						<h4>The error returned was:</h4>
						<p>Sorry, the link that brought you to this page seems to be out of date or broken. E=2</p>
					</div><br />";
			}
			else {
				echo addTopicView($topicid);
				echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;<a href='/forums/$fseo/'>$ftitle</a>&nbsp;&gt;&nbsp;$ttitle</div><br />";
				echo "<table style='width:100%' cellspacing='0'>
				<tr>
				<td style='padding-left:0px' width='40%' valign='middle' nowrap='nowrap'><div>"; 
				// get posts in this topic
				$query   = "SELECT COUNT(pid) AS numrows FROM forums_post WHERE ptid='".$this->tid."'";
				$result  = mysql_query($query) or die('Error, query failed');
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];				
				//BEGIN PAGING Class
				$pagin = new AFTWForumPageing();
				$pagin->get_icount($numrows);
				$pagin->get_start($this->start);
				$pagin->get_setlimit($this->paging);
				$pagin->get_url('/forums/'.$this->fseo.'/topic-'.$this->tid.'/');					
				echo $pagin->ShowPaging();
				echo "</div></td>
				<td class='nopad' style='padding:0px 0px 5px 0px' align='right' width='60%'>";
				echo $this->PostButtons($tclosed,$fid,'top'); //Post Buttons
				echo "</td>
						</tr>
					</table>
					<table width='100%' style='padding:0px' cellspacing='0' cellpadding='0'>";
					//begin title
				echo "<tr>
						 <td width='100%' class='darkrow1' style='word-wrap:break-word; padding: 0;' align='center'><div><img src='/images/forumimages/nav_m.gif' border='0'  alt='&gt;' width='8' height='8' />&nbsp;<span class='topictitle'>$ttitle</span></div></td>
					</tr>
					</table>
					<div class=\"borderwrap\">";	
				// The end all be-all query...
				$query = "SELECT pid, ptid, puid, pfid, ptitle, pdate, pbody, pip FROM forums_post WHERE ptid='".$this->tid."' ORDER BY pdate LIMIT ".$this->start.", ".$this->paging;
				//echo 'Query: '.$query.'<br /> Start: '.$this->start.', tid: '.$this->tid.', fseo: '.$this->fseo.', paging: '.$this->paging;
				$result001 = mysql_query($query) or die('Error : ' . mysql_error());
				$i = 0;
				if($this->start == 0){$c = 1;}else {$c = $this->start;}
				while(list($pid,$ptid,$puid,$pfid,$ptitle,$pdate,$pbody,$pip) = mysql_fetch_array($result001))
				{
					$pbody = stripslashes($pbody);
					
					
					
					//Anime request code:
					//This part could probably all be integrated to the request class. Also needs to be designed better.
					include_once('includes/classes/request.class.php');
					$Requests = new AnimeRequest();
					
					$out;
					preg_match_all("/\[animerequest\](\d+)\[\/animerequest\]/", $pbody, $out);
					
					foreach($out[0] as $i){ //Delete the found text from the body.
						$pbody = str_replace($i, " ", $pbody);
					}
					
					foreach($out[1] as $i){
						//echo $i;
						$req_query = "SELECT Username, name, status, type, episodes, anidb, user_id, date, description, details FROM user_requests WHERE id='".$i."'";
						$req_result = mysql_query($req_query) or die('Error : ' . mysql_error());
						if($req_result){
							while(list($Username, $name, $status, $type, $episodes, $anidb, $user_id, $date, $description, $details) = mysql_fetch_array($req_result)){
								if($episodes==0){
									$episodes = "?";
								}
								$pbody = $pbody.'
								Request: <a href="/requests?highlight='.$i.'">'.$name.'</a><br>
								Requested by: '.$this->formatUsername($user_id).'<br>
								AniDB: <a href="http://anidb.net/a'.$anidb.'">'.$anidb.'</a><br>
								Status: '.$Requests->getStatus($status).'<br>
								Episodes: '.$episodes.'<br>
								Requested: '.date("Y-m-d H:i:s", $date).'<br>
								Description: '.$description.'<br><br>
								User Comments:<br>'.$details.'<br><br>
								
								';
							}
						}else{
							echo "Couldn't retrieve Anime Request information.";
						}
					}
					//End of anime request code
					
					
					
					$ptitle = stripslashes($ptitle);
					$pdate = timeZoneChange($pdate,$this->profileArray[3]);
					
					//get the user who posted this...
					$query003 = "SELECT Level_access, firstName, gender, country, avatarActivate, personalMsg, avatarExtension, memberTitle, signatureActive, Signature FROM users WHERE ID='$puid'";
					$result003 = mysql_query($query003) or die('Error : ' . mysql_error());
					$row003 = mysql_fetch_array($result003);
		
					if ($row003['firstName'] == '') {
	    				$firstName003 = "Unknown";
	  				} else {
	    				$firstName003 = $row003['firstName'];
	  				}
										
					if ($row003['gender'] == '') {
						$gender003 = "<img src='/images/gender-unknown.png' alt='gender' border='0' />&nbsp;\n"; 
					} else {	
						$gender003 = "<img src='/images/".$row003['gender'].".gif' alt='gender' border='0' />&nbsp;\n";
					}
	  						
					if ($row003['country'] == '') {
						$country003 = "Unknown";
					} else {
						$country003 = $row003['country'];
					}
	    
					$avatarActivate003 = $row003['avatarActivate'];
					$personalMsg003 = $row003['personalMsg'];
					$avatarExtension003 = $row003['avatarExtension'];
					$memberTitle003 = $row003['memberTitle'];
					$signatureActive003 = $row003['signatureActive'];
					$Signature003 = $row003['Signature'];
					$Signature003 = stripslashes($Signature003);
					$Level_access003 = $row003['Level_access'];
					$advanceImage003 = $row003['advanceImage'];
					$pdate1 = date("M j Y, h:i A",$pdate);		
					if($this->profileArray[2] == 1 || $this->profileArray[2] == 2) { $IpAddress1 = "IP: <i><a href='http://ip-lookup.net?ip=".$pip."' target='_blank'>".$pip."</a></i> &bull;\n"; }
					else {$IpAddress1 = '';}
  
    				//check user post posts and count them
					$query006 = mysql_query("SELECT pid FROM forums_post WHERE puid='$puid'"); 
					$total_user_post_posts1 = mysql_num_rows($query006);
					$total_posts2 = $total_user_post_posts1;

					///START POST VIEW!
					if ($i % 2) {
														
						echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl2' style='margin-bottom:10px'>\n<tr>\n";
						
						echo "<td class='tbl2' style='width:160px'>\n<a name='entry".$pid."'></a>\n";
						echo "<div style='float: left;'>". $this->formatUsername($puid)."</div>\n";
						echo "</td>\n";
						
						echo "<td class='tbl2' style='padding-left: 30px; padding-right: 20px;'>\n";
						echo "<div style='float: left;'>\n";
					if($puid == $this->profileArray[1]) {
						echo "<a href='/forums/edit/post-".$pid."'>Edit your post</a>\n";
					  } else if ($this->profileArray[2] == 1 || $this->profileArray[2] == 2) {
						echo "<a href='/forums/edit/post-".$pid."'>Edit user post</a>\n";
					  } else { echo "&nbsp;"; }
						echo "</div>\n";
						echo "<div style='float: right;'><img src='/images/forumimages/to_post_off.gif' alt='on' border='0' style='padding-bottom:2px' /> ".$pdate1." &bull; ".$IpAddress1." Post <a href='#' onclick='link_to_post($pid); return false;' title='Show the link to this post'>#".$c."</a> &bull; <a href='javascript:scroll(0,0);'>Top</a></div>\n";
						echo "</td>\n";
						
						echo "</tr>\n<tr>\n";
						
						echo "<td valign='top' class='tbl2' style='width:160px;'>\n";
						echo "<div align='center'>\n";
					if($avatarActivate003 == 'yes') { 
						echo "<img src='/images/avatars/user".$puid.'.'.$avatarExtension003."' alt='User Avatar' /><br /><br />\n";
					  } else { 
						echo "<img src='/images/avatars/default.gif' alt='User Avatar' /><br /><br />\n";
					  }
						echo "<span class='small'>";
						if($memberTitle003 == 'none') { } 
						else {echo "<img src='/images/stafficons/".$memberTitle003.".png' alt='Member Title' border='0' /><br />\n";}
						if ($personalMsg003 != '') { echo $personalMsg003."<br />\n";} 
						echo "</span><br /><br />\n";
						echo "</div>\n";
						echo "<div align='left'>\n";
						echo "<span class='small'><strong>Posts:</strong> ".$total_posts2."</span><br />\n";
						echo "<span class='small'><strong>Member:</strong> #".$puid."</span><br />\n";
						echo "<span class='small'><strong>Gender:</strong> ".$gender003."</span><br />\n";
						echo "<span class='small'><strong>From:</strong> ".$country003."</span><br />\n";
						echo "<span class='small'><strong>Name:</strong> ".$firstName003."</span><br />\n";
						echo "</div>\n";
						echo "<br /></td>\n";
						
						echo "<td valign='top' class='tbl2' id='post-".$pid."' style='padding-left:18px;'>\n";
						echo "<div class='speech_bubble say'>\n";
						echo "<div class='top-left'></div>\n";
						echo "<div class='top-right'></div>\n";
						echo "<div class='content'>".$pbody;
					if ($signatureActive003 == 'yes' && $Level_access003 != 3) {
						echo "<br /><br />--------------------<br />\n";
						echo "<div class=\"signature\">$Signature003</div>\n";
					} else { }
						echo "</div>\n";
						echo "<div class='bottom-left'></div>\n";
						echo "<div class='bottom-right'></div>\n";
						echo "<div class='tail'></div>\n";
						echo "</div>\n";
						echo "</td>\n</tr>\n</table>\n";
					
					} else {
					
						echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl2' style='margin-bottom:10px'>\n<tr>\n";
						
						echo "<td class='tbl2' style='padding-left: 20px; padding-right: 30px;'>\n";
						echo "<div style='float: left;'><a href='javascript:scroll(0,0);'>Top</a> &bull; Post <a href='#' onclick='link_to_post($pid); return false;' title='Show the link to this post'>#".$c."</a> &bull; ".$IpAddress1." <img src='/images/forumimages/to_post_off.gif' alt='On' border='0' style='padding-bottom:2px' /> ".$pdate1."</div>\n";
						echo "<div style='float: right;'>\n";
						if($puid == $this->profileArray[1]) {
						echo "<a href='/forums/edit/post-".$pid."'>Edit your post</a>\n";
					  } else if ($this->profileArray[2] == 1 || $this->profileArray[2] == 2) {
						echo "<a href='/forums/edit/post-".$pid."'>Edit user post</a>\n";
					  } else { echo "&nbsp;"; }
						echo "</div>\n";
						echo "</td>\n";
					
						echo "<td class='tbl2' style='width:160px'>\n<a name='entry".$pid."'></a>\n";
						echo "<div style='float: right;'>" . $this->formatUsername($puid) . "</div>\n";
						echo "</td>\n";
						
						echo "</tr>\n<tr>\n";
					
						echo "<td valign='top' class='tbl2' id='post-".$pid."' style='padding-right:18px;'>\n";
						echo "<div class='speech_bubble say'>\n";
						echo "<div class='top-left'></div>\n";
						echo "<div class='top-right'></div>\n";
						echo "<div class='content'>".$pbody;
					if ($signatureActive003 == 'yes' && $Level_access003 != 3) {
						echo "<br /><br />--------------------<br />\n";
						echo "<div class=\"signature\">$Signature003</div>\n";
					} else { }
						echo "</div>\n";
						echo "<div class='bottom-left'></div>\n";
						echo "<div class='bottom-right'></div>\n";
						echo "<div class='tail rt'></div>\n";
						echo "</div>\n";
						echo "</td>\n";
					
						echo "<td valign='top' class='tbl2' style='width:160px; padding-left:10px;'>\n";
						echo "<div align='center'>\n";
					if($avatarActivate003 == 'yes') { 
						echo "<img src='/images/avatars/user".$puid.'.'.$avatarExtension003."' alt='User Avatar' /><br /><br />\n";
					  } else { 
						echo "<img src='/images/avatars/default.gif' alt='User Avatar' /><br /><br />\n";
					  }
						echo "<span class='small'>";
						if($memberTitle003 == 'none') { } 
						else {echo "<img src='/images/stafficons/".$memberTitle003.".png' alt='Member Title' border='0' /><br />\n";}
						if ($personalMsg003 != '') { echo $personalMsg003."<br />\n";} 						
						echo "</span><br /><br />\n";
						echo "</div>\n";
						echo "<div align='left'>\n";
						echo "<span class='small'><strong>Posts:</strong> ".$total_posts2."</span><br />\n";
						echo "<span class='small'><strong>Member:</strong> #".$puid."</span><br />\n";
						echo "<span class='small'><strong>Gender:</strong> ".$gender003."</span><br />\n";
						echo "<span class='small'><strong>From:</strong> ".$country003."</span><br />\n";
						echo "<span class='small'><strong>Name:</strong> ".$firstName003."</span><br />\n";
						echo "</div>\n";
						echo "</td>\n</tr>\n</table>\n";
					
					}
					
					$i++;
					$c++;
				}
				//END POSTS WHILE
				//this is the start of the bottom of the table
				echo "<table style='width:100%' cellspacing='0'>
						<tr>
					<td style='padding-left:0px' width='40%' valign='middle' nowrap='nowrap'>";
				# BEGIN PAGING NAV!
				echo $pagin->ShowPaging();
				# END PAGING
				echo "</td>
					<td style='padding:5px 5px 5px 0px' align='right' width='60%' valign='middle'>";
				echo $this->PostButtons($tclosed,$fid,'bottom');
				echo "</td>
					</tr>
					</table>";
				include('includes/fastreply.function.php');
				echo "<br /><br />";
			}
		}
	}
	
	private function PostButtons($tclosed,$fid,$loc){
		if($loc == 'bottom'){
			$fastreply = "<span><a href='javascript:toggle_visibility(\"QuickReply\");'>Fast Reply</a></span>&nbsp;&nbsp;";
		}
		else {
			$fastreply = '';
		}			
		echo "<div class=\"forum_button\">";	
		if ($this->profileArray[0] != 1){}
		else if ($tclosed == 1){
			if ($this->profileArray[2] == 1 || $this->profileArray[2] == 2){
				echo $fastreply."<span><a href=\"/forums/post/reply-".$this->tid."\">Post a Reply</a></span>&nbsp;&nbsp;";
			}
			else {
				echo "<span><a>Closed</a></span>&nbsp;&nbsp;";
			}
			$query = "SELECT fid FROM forums_forum WHERE fid='$fid' AND fpermpost LIKE '%".$this->profileArray[2]."%'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$allowedToMakeNewTopic2 = $row['fid'];
			if ($allowedToMakeNewTopic2 ==''){}
			else {
				echo "<span><a href='/forums/post/topic-$fid'>Post a Topic</a></span>&nbsp;";
			}
		}
		else {
			echo $fastreply."<a href='/forums/post/reply-".$this->tid."'>Post a Reply</a>&nbsp;&nbsp;";
			$query = "SELECT fid FROM forums_forum WHERE fid='$fid' AND fpermpost LIKE '%".$this->profileArray[2]."%'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$allowedToMakeNewTopic2 = $row['fid'];
			if ($allowedToMakeNewTopic2 == ''){}
			else {
				echo "<span><a href='/forums/post/topic-$fid'>Post a Topic</a></span>";
			}
		}
		echo "</div>";
	}
}
?>