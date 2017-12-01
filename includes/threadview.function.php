<?php
$requestedThread = $_REQUEST['thread'];
$requestedForum = $_REQUEST['forum'];
if(isset($_GET['s'])){
	$start_point = $_GET['s'];
}
else {
	$start_point = 0;
}
$query = "SELECT pid, ptid, pfid, ptitle, puid, pdate, pip, pbody, pistopic, ptispost FROM forums_post WHERE ptid='$requestedThread' AND pistopic='1'";
$result = mysql_query($query) or die('Error : ' . mysql_error());
$row001 = mysql_fetch_array($result);
$tid = $row001['ptid'];
$pid = $row001['pid'];
$tfid = $row001['pfid'];
$ttitle = $row001['ptitle'];
$tpid = $row001['puid'];
$tdate = $row001['pdate'];
$tpip = $row001['pip'];
$tdate = timeZoneChange($tdate,$timeZone);
$tbody = $row001['pbody'];
$pistopic = $row001['pistopic'];
$ptispost = $row001['ptispost'];
$tbody = stripslashes($tbody);
$ttitle = stripslashes($ttitle);

//HTML exploit fix
//Zigbigidorlu was here =D
$ttitle = htmlentities($ttitle);

$initialPost = 1;
$query7 = "SELECT fid, ftitle, fseo	FROM forums_forum WHERE fseo='$requestedForum'";
$result7 = mysql_query($query7) or die('Error : ' . mysql_error());
$row7 = mysql_fetch_array($result7);
$fid = $row7['fid'];
$ftitle = $row7['ftitle'];
$fseo = $row7['fseo'];
// ADDED 27/03/15 by Robotman321
$restrictHiddenThreads = "";
if($profileArray[2] != 1 && $profileArray[2] != 2)
{
        $restrictHiddenThreads = " AND `hidden` = 0";
}

$query9 = "SELECT tclosed, hidden FROM forums_threads WHERE tid='$tid'" . $restrictHiddenThreads;
$result9 = mysql_query($query9) or die('Error : ' . mysql_error());
$row9 = mysql_fetch_array($result9);
$tclosed = $row9['tclosed'];
$thidden = $row9['hidden'];
if ($fseo != $requestedForum){
echo "<div id=\"navstrip\"><img src='//i.animeftw.tv/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a></div><br />";
	echo "<div class='errorwrap'>
					<h4>The error returned was:</h4>
					<p>Sorry, the link that brought you to this page seems to be out of date or broken. E=1</p>
	</div><br />";
}
else {
	$query009 = mysql_query("SELECT COUNT(fid) FROM forums_forum WHERE fid='$tfid' AND fpermission LIKE '%".$profileArray[2]."%'");
	$total_forum = mysql_result($query009, 0);
	if ($total_forum == 0){
		echo "<div id=\"navstrip\"><img src='//i.animeftw.tv/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a></div><br />";
		echo "<div class='errorwrap'>
				<h4>The error returned was:</h4>
				<p>Sorry, the link that brought you to this page seems to be out of date or broken. E=2</p>
			</div><br />";
	}
	else {
		if(isset($_GET['view'])){}
		else {
			echo addTopicView($tid);
		}
		echo "<div id=\"navstrip\"><img src='//i.animeftw.tv/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;<a href='/forums/$fseo/'>$ftitle</a>&nbsp;&gt;&nbsp;$ttitle</div><br />";
		echo "<table style='width:100%' cellspacing='0'>
				<tr>
					<td style='padding-left:0px' width='40%' valign='middle' nowrap='nowrap'><div>";

					// get posts in this topic
					$query   = "SELECT COUNT(pid) AS numrows FROM forums_post WHERE ptid='$tid'";
					$result  = mysql_query($query) or die('Error, query failed');
					$row     = mysql_fetch_array($result, MYSQL_ASSOC);
					$numrows = $row['numrows'];
					// get start points
					if(!isset($_GET['s'])){$start = 0;}
					else{$start = $_GET['s'];}
					//BEGIN PAGING Class
					$pagin = new AFTWForumPageing();
					$pagin->get_icount($numrows);
					$pagin->get_start($start);
					$pagin->get_setlimit(20);
					$pagin->get_url("http://".$_SERVER['HTTP_HOST'].'/forums/'.$fseo.'/topic-'.$tid.'/');
					echo $pagin->ShowPaging();
					// how many rows to show per page
					if ($start_point == 0 || !isset($_GET['s']))
					{
						$rowsPerPage = 19;
						$start_point2 = 0;
					}
					else {
						$rowsPerPage = 20;
						$start_point2 = $start_point-1;
					}
							echo "</div></td>
							<td class='nopad' style='padding:0px 0px 5px 0px' align='right' width='60%'>";
							if ($Logged != 1)
							{
							}
							else if ($tclosed == 1){
								if ($profileArray[2] == 1 || $profileArray[2] == 2)
								{
								echo "<span class='forumbottons'><a href='/forums/post/reply-$tid'><img align='middle' src='//i.animeftw.tv/forumimages/closed.jpg' alt='post reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;";
								}
								else {
									echo "<span class='forumbottons'><img align='middle' src='//i.animeftw.tv/forumimages/closed.jpg' alt='post reply' style='margin-bottom:5px;' /></span>&nbsp;&nbsp;";
								}
								$query = "SELECT fid FROM forums_forum WHERE fid='$requestedForum' AND fpermpost LIKE '%$profileArray[2]%'";
								$result = mysql_query($query) or die('Error : ' . mysql_error());
								$row = mysql_fetch_array($result);
								$allowedToMakeNewTopic2 = $row['fid'];
								if ($allowedToMakeNewTopic2 ==''){}
								else
								{
								echo "<span class='forumbottons'><a href='/forums/post/topic-$fid'><img align='middle' src='//i.animeftw.tv/forumimages/posttopic.jpg' alt='topic start' style='margin-bottom:5px;' /></a></span>&nbsp;";
								}
							}
							else {
							echo "<span class='forumbottons'><a href='/forums/post/reply-$tid'><img align='middle' src='//i.animeftw.tv/forumimages/postreply.jpg' alt='post reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;";
								$query = "SELECT fid FROM forums_forum WHERE fid='$requestedForum' AND fpermpost LIKE '%$profileArray[2]%'";
								$result = mysql_query($query) or die('Error : ' . mysql_error());
								$row = mysql_fetch_array($result);
								$allowedToMakeNewTopic2 = $row['fid'];
								if ($allowedToMakeNewTopic2 ==''){}
								else
								{
								echo "<span class='forumbottons'><a href='/forums/post/topic-$fid'><img align='middle' src='//i.animeftw.tv/forumimages/posttopic.jpg' alt='topic start' style='margin-bottom:5px;' /></a></span>";
								}
							}
							echo "</td>
						</tr>
					</table>
						<table width='100%' style='padding:0px' cellspacing='0' cellpadding='0'>";
					//begin title

						echo "<tr>
						 <td width='100%' class='darkrow1' style='word-wrap:break-word; padding: 0;' align='center'><div><img src='//i.animeftw.tv/forumimages/nav_m.gif' border='0'  alt='&gt;' width='8' height='8' />&nbsp;<span class='topictitle'>$ttitle</span></div></td>

						</tr>
					  </table>
						 <div class=\"borderwrap\">";
	if($_GET['s'] < 1){
	echo "<div class='speech_bubble say'>\n";
	echo "<div class='top-left'></div>\n";
	echo "<div class='top-right'></div>\n";
	}
	echo "<div class='content'>\n";

	//Begin thread body (first post then the rest!)
	if ($start_point == 0 || $start_point == '') {
	echo "<table class='ipbtable' cellspacing=\"0\">\n<tr>\n";
	echo "<td valign=\"middle\" class=\"row2\" width=\"1%\"><a name=\"entry$pid\"></a>";

								//get the user who posted this...
								$query002 = "SELECT Username, Level_access, firstName, gender, country, avatarActivate, personalMsg, avatarExtension, memberTitle, advanceImage, signatureActive, Signature FROM users WHERE ID='$tpid'";
										$result002 = mysql_query($query002) or die('Error : ' . mysql_error());
										$row002 = mysql_fetch_array($result002);
										$Username002 = $row002['Username'];
										$firstName002 = $row002['firstName'];
										$gender002 = $row002['gender'];
										$Level_access001 = $row002['Level_access'];
										$country002 = $row002['country'];
										$avatarActivate002 = $row002['avatarActivate'];
										$personalMsg = $row002['personalMsg'];
										$avatarExtension002 = $row002['avatarExtension'];
										$memberTitle002 = $row002['memberTitle'];
										$advanceImage001 = $row002['advanceImage'];
										$signatureActive001 = $row002['signatureActive'];
										$Signature001 = $row002['Signature'];
										$Signature001 = stripslashes($Signature001);
									echo "<div id=\"post-member\" class='popmenubutton-new-out'>
										<span class=\"normalname\">";
										if ($gender002 == '')
										{
											echo "<img src='//i.animeftw.tv/gender-unknown.png' alt='' border='0' title='gender unknown' />&nbsp;";
										}
										else
										{
											echo "<img src='//i.animeftw.tv/$gender002.gif' alt='' border='0' />&nbsp;";
										}
										echo checkUserName($Username002);
						echo "</span>
									</div>
							</td>
							<td class=\"row2\" valign=\"middle\" width=\"99%\">
								<!-- POSTED DATE DIV -->
								<div style=\"float: left;\">";
								//thread date section
										$tdate1 = date("M j Y, h:i A",$tdate);
									echo "<span class=\"postdetails\"> <img src='//i.animeftw.tv/forumimages/to_post_off.gif' alt='post' border='0' style='padding-bottom:2px' /> $tdate1</span>";
									if($profileArray[2] == 1 || $profileArray[2] == 2)
									{
										$IpAddress = 'IP: <i><a href="http://ip-lookup.net?ip='.$tpip.'" target="_blank">'.$tpip.'</a></i>';
									}
									else {
									}
								echo "</div>
								<!-- REPORT / DELETE / EDIT / QUOTE DIV -->
								<div align=\"right\">
									<span class=\"postdetails\"> $IpAddress Post <a title=\"Show the link to this post\" href=\"#\" onclick=\"link_to_post($pid); return false;\">#$initialPost</a>


									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td valign=\"top\" class=\"post2\">
					<table style='padding: 0;' cellspacing='0' cellpadding='0'>
					<tr><td style='padding:0;'>&nbsp;</td>

					<td style='padding: 0;' width='100%'>&nbsp;<!--no content--></td>
					<td style='padding:0;'>&nbsp;</td></tr>
					<td style='padding: 0;'>&nbsp;<!--no contest--></td>
					<td style='padding: 0;'>
					<div class='postdetails_new' align='center'>
								<span class=\"postdetails\">

										<a href=\"/profile/$Username002\" title=\"View Member Profile\">";
										if($avatarActivate002 == 'yes')
										{
											echo '<img src="//i.animeftw.tv/avatars/user'.$tpid.'.'.$avatarExtension002.'" alt="" />';
										}
										else {
											echo '<img src="//i.animeftw.tv/avatars/default.gif" alt="" />';
										}
										echo "</a><br /><br />";
									if ($memberTitle002 != 'none')
									{
										if ($personalMsg != '')
										{
										echo $personalMsg.'<br /><br />';
										}
										else {
										}
										echo '<img src="//i.animeftw.tv/'.$memberTitle002.'.jpg" alt="" border="0" /><br />';
									}
									else
									{
									}
									//check user post posts and count them
									$query004 = mysql_query("SELECT COUNT(pid) FROM forums_post WHERE puid='$tpid'");
									$total_user_post_posts = mysql_result($query004, 0);
									$total_posts = $total_user_post_posts;
									echo "<br />
									Posts: $total_posts<br />
									<!-- Joined: 19-September 07<br /> -->
									From: $country002<br />
									Member No.: $tpid<br />
									Name: $firstName002<br />

									<br />
								</span></div>
					<!-- End Middle --></td>
							<td style='padding: 0; background:'><!-- Middle Right --></td>
					<!-- Bottom Row -->
						<tr>
							<td style='padding: 0;'>&nbsp;</td>
							<td style='padding: 0;'>&nbsp;<!-- Bottom Middle --></td>

							<td style='padding: 0;'>&nbsp;</td>
						</tr>
					</table>
					<br />
								<img src=\"http://forums.ftwentertainment.com/style_images/techheaven/spacer.gif\" alt=\"\" width=\"160\" height=\"1\" /><br />
						   </td>
						   <td width=\"100%\" valign=\"top\" class=\"post2\" id='post-main-$tid'>
								<div class=\"postcolor\" id='post-$tid'>
									".bbsmiliesToCode($tbody)."
								</div>";
								//do they have a signature? is it enabled?
								if ($signatureActive001 == yes && $Level_access001 != 3)
								{
								echo "<br /><br />--------------------<br />
					<div class=\"signature\">$Signature001</div>";
								}
								else {
								}
							echo "</td>

						</tr>
						<tr>
						<td class='formbuttonrow' nowrap='nowrap'>
			<div style='text-align:left'><a href='javascript:scroll(0,0);'>Top</a></div></td>
			<td class='formbuttonrow'>";
			if($tpid == $profileArray[1])
			{
				echo "<div align='right'><span class='forumbottons'><a href='/forums/edit/post-$pid'><img align='middle' src='//i.animeftw.tv/forumimages/useredit.jpg' alt='Edit your post' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;</div>";

			}
			else if ($profileArray[2] == 1 || $profileArray[2] == 2)
			{
			echo "<div align='right'><span class='forumbottons'><a href='/forums/edit/post-$pid'><img align='middle' src='//i.animeftw.tv/forumimages/adminedit.jpg' alt='post reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;</div>";
			}
			else {
			echo "&nbsp;";
			}
	echo "</td>\n";
	echo "</tr>\n</table>";

} else { }

	echo "<table class='ipbtable' cellspacing=\"1\"><tr>
	<td width=\"101%\" class=\"row2\"><!-- no content -->	</td>\n";
	echo "</tr></table>";
	if($_GET['s'] < 1){
	echo "</div>\n";
	echo "<div class='bottom-left'></div>\n";
	echo "<div class='bottom-right'></div>\n";
	echo "</div>\n";
	}

					//BEGIN POSTS WHILE

					// by default we show first page
					$pageNum = 1;
					// if $_GET['page'] defined, use it as page number
					if(isset($_GET['page']))
					{
						$pageNum = $_GET['page'];
					}
					// counting the offset
					$offset = $start_point2;

					$initialPost2 = 2;
					$query004  = "SELECT pid, ptid, puid, pfid, ptitle, pdate, pbody, ptispost, pip FROM forums_post WHERE ptid='$tid' AND pistopic='0' ORDER BY ptispost ASC LIMIT $start_point2, $rowsPerPage";
					$result004 = mysql_query($query004) or die('Error : ' . mysql_error());
					while(list($pid,$ptid,$puid,$pfid,$ptitle,$pdate,$pbody,$ptispost,$pip) = mysql_fetch_array($result004))
					{
						$pbody = stripslashes($pbody);
						$ptitle = stripslashes($ptitle);
						$pdate = timeZoneChange($pdate,$timeZone);

	//get the user who posted this...
		$query003 = "SELECT Username, Level_access, firstName, gender, country, avatarActivate, personalMsg, avatarExtension, memberTitle, advanceImage, signatureActive, Signature FROM users WHERE ID='$puid'";
		$result003 = mysql_query($query003) or die('Error : ' . mysql_error());
		$row003 = mysql_fetch_array($result003);
		$Username003 = $row003['Username'];

	if ($row003['firstName'] == '') {
	    $firstName003 = "Unknown";
	  } else {
	    $firstName003 = $row003['firstName'];
	  }

	if ($row003['gender'] == '') {
	    $gender003 = "<img src='//i.animeftw.tv/gender-unknown.png' alt='gender' border='0' />&nbsp;\n";
	  } else {
	    $gender003 = "<img src='//i.animeftw.tv/".$row003['gender'].".gif' alt='gender' border='0' />&nbsp;\n";
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
	if($profileArray[2] == 1 || $profileArray[2] == 2) { $IpAddress1 = "IP: <i><a href='http://ip-lookup.net?ip=".$pip."' target='_blank'>".$pip."</a></i> &bull;\n"; }

    //check user post posts and count them
		$query006 = mysql_query("SELECT pid FROM forums_post WHERE puid='$puid'");
		$total_user_post_posts1 = mysql_num_rows($query006);
		$total_posts2 = $total_user_post_posts1;

///START POST VIEW!
if ($i % 2) {

	echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl2' style='margin-bottom:10px'>\n<tr>\n";

	echo "<td class='tbl2' style='width:160px'>\n<a name='entry".$pid."'></a>\n";
	echo "<div style='float: left;'>".checkUserName($Username003)."</div>\n";
	echo "</td>\n";

	echo "<td class='tbl2' style='padding-left: 30px; padding-right: 20px;'>\n";
	echo "<div style='float: left;'>\n";
if($puid == $profileArray[1]) {
	echo "<a href='/forums/edit/post-".$pid."'>Edit your post</a>\n";
  } else if ($profileArray[2] == 1 || $profileArray[2] == 2) {
	echo "<a href='/forums/edit/post-".$pid."'>Edit user post</a>\n";
  } else { echo "&nbsp;"; }
    echo "</div>\n";
	echo "<div style='float: right;'><img src='//i.animeftw.tv/forumimages/to_post_off.gif' alt='on' border='0' style='padding-bottom:2px' /> ".$pdate1." &bull; ".$IpAddress1." Post <a href='#' onclick='link_to_post($pid); return false;' title='Show the link to this post'>#".$ptispost."</a> &bull; <a href='javascript:scroll(0,0);'>Top</a></div>\n";
	echo "</td>\n";

	echo "</tr>\n<tr>\n";

	echo "<td valign='top' class='tbl2' style='width:160px;'>\n";
	echo "<div align='center'>\n";
if($avatarActivate003 == yes) {
    echo "<img src='//i.animeftw.tv/avatars/user".$puid.'.'.$avatarExtension003."' alt='User Avatar' /><br /><br />\n";
  } else {
    echo "<img src='//i.animeftw.tv/avatars/default.gif' alt='User Avatar' /><br /><br />\n";
  }
    echo "<span class='small'>";
if ($memberTitle003 == 'none') { }
    elseif ($personalMsg003 != '') { echo $personalMsg003."<br />\n<br />"; }
	else { echo "<img src='//i.animeftw.tv/".$memberTitle003.".jpg' alt='Member Title' border='0' />\n"; }
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
	echo "<div class='content'>".bbsmiliesToCode($pbody);
if ($signatureActive003 == yes && $Level_access003 != 3) {
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
	echo "<div style='float: left;'><a href='javascript:scroll(0,0);'>Top</a> &bull; Post <a href='#' onclick='link_to_post($pid); return false;' title='Show the link to this post'>#".$ptispost."</a> &bull; ".$IpAddress1." <img src='//i.animeftw.tv/forumimages/to_post_off.gif' alt='On' border='0' style='padding-bottom:2px' /> ".$pdate1."</div>\n";
	echo "<div style='float: right;'>\n";
	if($puid == $profileArray[1]) {
	echo "<a href='/forums/edit/post-".$pid."'>Edit your post</a>\n";
  } else if ($profileArray[2] == 1 || $profileArray[2] == 2) {
	echo "<a href='/forums/edit/post-".$pid."'>Edit user post</a>\n";
  } else { echo "&nbsp;"; }
    echo "</div>\n";
	echo "</td>\n";

	echo "<td class='tbl2' style='width:160px'>\n<a name='entry".$pid."'></a>\n";
	echo "<div style='float: right;'>".checkUserName($Username003)."</div>\n";
	echo "</td>\n";

	echo "</tr>\n<tr>\n";

	echo "<td valign='top' class='tbl2' id='post-".$pid."' style='padding-right:18px;'>\n";
	echo "<div class='speech_bubble say'>\n";
	echo "<div class='top-left'></div>\n";
	echo "<div class='top-right'></div>\n";
	echo "<div class='content'>".bbsmiliesToCode($pbody);
if ($signatureActive003 == yes && $Level_access003 != 3) {
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
if($avatarActivate003 == yes) {
    echo "<img src='//i.animeftw.tv/avatars/user".$puid.'.'.$avatarExtension003."' alt='User Avatar' /><br /><br />\n";
  } else {
    echo "<img src='//i.animeftw.tv/avatars/default.gif' alt='User Avatar' /><br /><br />\n";
  }
    echo "<span class='small'>";
if ($memberTitle003 == 'none') { }
    elseif ($personalMsg003 != '') { echo $personalMsg003."<br />\n<br />"; }
	else { echo "<img src='//i.animeftw.tv/".$memberTitle003.".jpg' alt='Member Title' border='0' />\n"; }
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
$initialPost2++;

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
							if ($profileArray[0] != 1)
							{
							}
							else if ($tclosed == 1){
								if ($profileArray[2] == 1)
								{
								echo "
								<span class='forumbottons'><a href='javascript:toggle_visibility(\"QuickReply\");'><img align='middle' src='//i.animeftw.tv/forumimages/fastreply.jpg' alt='quick reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;";
								echo "<span class='forumbottons'><a href='/forums/post/reply-$tid'><img align='middle' src='//i.animeftw.tv/forumimages/closed.jpg' alt='post reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;";
								}
								else {
									echo "<span class='forumbottons'><img align='middle' src='//i.animeftw.tv/forumimages/closed.jpg' alt='post reply' style='margin-bottom:5px;' /></span>&nbsp;&nbsp;";
								}
								$query = "SELECT * FROM forums_forum WHERE fid='$requestedForum' AND fpermpost LIKE '%$PermissionLevelAdvanced%'";
								$result = mysql_query($query) or die('Error : ' . mysql_error());
								$row = mysql_fetch_array($result);
								$allowedToMakeNewTopic2 = $row['fid'];
								if ($allowedToMakeNewTopic2 =='')
								{
								}
								else
								{
								echo "<span class='forumbottons'><a href='/forums/post/topic-$fid'><img align='middle' src='//i.animeftw.tv/forumimages/posttopic.jpg' alt='topic start' style='margin-bottom:5px;' /></a></span>&nbsp;";
								}
							}
							else {
							echo"<span class='forumbottons'><a href='javascript:toggle_visibility(\"QuickReply\");'><img align='middle' src='//i.animeftw.tv/forumimages/fastreply.jpg' alt='quick reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;";
							echo "<span class='forumbottons'><a href='/forums/post/reply-$tid'><img align='middle' src='//i.animeftw.tv/forumimages/postreply.jpg' alt='post reply' style='margin-bottom:5px;' /></a></span>&nbsp;&nbsp;";
								$query = "SELECT * FROM forums_forum WHERE fid='$requestedForum' AND fpermpost LIKE '%$PermissionLevelAdvanced%'";
								$result = mysql_query($query) or die('Error : ' . mysql_error());
								$row = mysql_fetch_array($result);
								$allowedToMakeNewTopic2 = $row['fid'];
								if ($allowedToMakeNewTopic2 =='')
								{
								}
								else
								{
								echo "<span class='forumbottons'><a href='/forums/post/topic-$fid'><img align='middle' src='//i.animeftw.tv/forumimages/posttopic.jpg' alt='topic start' style='margin-bottom:5px;' /></a></span>";
								}
							}
							echo "</td>
						</tr>
					</table>";
					include('fastreply.function.php');
					echo "<br /><br />";
					}
					}
					?>
