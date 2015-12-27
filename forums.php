<?php
//include('init.php');
include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
$PageTitle = "Forums - AnimeFTW.TV";
//include('includes/forums.functions.php');
if(isset($_GET['view'])){$GETview = $_GET['view'];}else{$GETview = '';}
if ($GETview == 'lastpost')
{
	if(isset($_GET['thread'])){$topicID = $_GET['thread'];	}
		$topicID = mysql_real_escape_string($topicID);
		$query = "SELECT pid, ptid, pfid, ptispost FROM forums_post WHERE ptid='$topicID' ORDER BY ptispost DESC LIMIT 0, 1";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$pid = $row['pid'];
			$ptid = $row['ptid'];
			$ptispost = $row['ptispost'];
			$pfid = $row['pfid'];
			$rounded = floor($ptispost/20);
			$rounded = $rounded*20;
			$query1 = "SELECT fseo FROM forums_forum WHERE fid='".$pfid."'";
			$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
			$row1 = mysql_fetch_array($result1);
		header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/".$row1['fseo']."/topic-$ptid/s-$rounded#entry$pid");
}
if ($GETview == 'findpost')
{ 
	if(isset($_GET['post'])){
		if(isset($_GET['post']) && is_numeric($_GET['post'])){$postID = $_GET['post'];}
		$postID = mysql_real_escape_string($postID);
		$query = "SELECT pid, ptid, pfid, ptispost FROM forums_post WHERE pid='$postID' ORDER BY ptispost DESC LIMIT 0, 1";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$pid = $row['pid'];
			$ptid = $row['ptid'];
			$ptispost = $row['ptispost'];
			$pfid = $row['pfid'];
			$rounded = floor($ptispost/20);
			$rounded = $rounded*20;
			$query1 = "SELECT fseo FROM forums_forum WHERE fid='".$pfid."'";
			$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
			$row1 = mysql_fetch_array($result1);
		header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/".$row1['fseo']."/topic-$ptid/s-$rounded#entry$pid");
	}
	else if(isset($_GET['thread'])){
		if(isset($_GET['thread']) && is_numeric($_GET['thread'])){$threadID = $_GET['thread'];}
		$threadID = mysql_real_escape_string($threadID);
		$query = "SELECT tid, tfid FROM forums_threads WHERE tid='$threadID'";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$row = mysql_fetch_array($result);
			$tid = $row['tid'];
			$tfid = $row['tfid'];
			$query1 = "SELECT fseo FROM forums_forum WHERE fid='".$tfid."'";
			$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
			$row1 = mysql_fetch_array($result1);
		header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/".$row1['fseo']."/topic-$tid/s-0");
	}
}
if(isset($_POST['doreply']))
{
	$CODE = @$_POST['CODE']; //1=new thread. 2=reply
	$fid = @$_POST['fid']; //replying OR making a new thread (id it)
	$tid = @$_POST['tid']; //replying - get topic id
	$ptitle = @$_POST['ptitle']; //title for the replied topic..
	$puid = @$_POST['puid']; //the subission user's id
	$pid = @$_POST['pid']; //the subission user's id
	$submittitle = @$_POST['submittitle'];
	$submitbody = @$_POST['submitbox'];
	$mod_options = @$_POST['mod_options'];
	$userIp = @$_SERVER['REMOTE_ADDR'];
	$post_htmlstatus = @$_POST['post_htmlstatus'];
	$submittitle = stripslashes($submittitle);
	$date = time();
	$tupdated = time();
	if(!isset($_POST['text-description']) || (isset($_POST['text-description']) && $_POST['text-description'] != ''))
	{
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums");
		exit;
	}
	else
	{
		if ($CODE == 1)
		{
			if ($mod_options == 'nowt')
			{
				$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated) VALUES ('%s', '%s', '%s', '%s', '%s')",
					mysql_real_escape_string($submittitle),
					mysql_real_escape_string($puid),
					mysql_real_escape_string($fid),
					mysql_real_escape_string($date),
					mysql_real_escape_string($tupdated));
				mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			}
			else
			{
				if($mod_options == 'pin')
				{
					$modoption = 1;
					$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated, tstickied) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
					mysql_real_escape_string($submittitle),
					mysql_real_escape_string($puid),
					mysql_real_escape_string($fid),
					mysql_real_escape_string($date),
					mysql_real_escape_string($tupdated),
					mysql_real_escape_string($modoption));
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
				}
				else if ($mod_options == 'close')
				{
					$modoption = 1;
					$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated, tclosed) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
					mysql_real_escape_string($submittitle),
					mysql_real_escape_string($puid),
					mysql_real_escape_string($fid),
					mysql_real_escape_string($date),
					mysql_real_escape_string($tupdated),
					mysql_real_escape_string($modoption));
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
				}
				else
				{
					$modoption = 1;
					$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated, tstickied, tclosed) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					mysql_real_escape_string($submittitle),
					mysql_real_escape_string($puid),
					mysql_real_escape_string($fid),
					mysql_real_escape_string($date),
					mysql_real_escape_string($tupdated),
					mysql_real_escape_string($modoption),
					mysql_real_escape_string($modoption));
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
				}
			}
			$query006 = "SELECT tid FROM forums_threads WHERE tdate='$date'";
			$result006 = mysql_query($query006) or die('Error : ' . mysql_error());
			$row006 = mysql_fetch_array($result006);
			$ptid3 = $row006['tid'];
			$pistopic = 1;
			$query2 = sprintf("INSERT INTO forums_post (ptid, puid, pfid, ptitle, pdate, pbody, pistopic, pip) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($ptid3),
				mysql_real_escape_string($puid),
				mysql_real_escape_string($fid),
				mysql_real_escape_string($submittitle),
				mysql_real_escape_string($date),
				mysql_real_escape_string($submitbody),
				mysql_real_escape_string($pistopic),
				mysql_real_escape_string($userIp));
			mysql_query($query2) or die('Could not connect, way to go retard:' . mysql_error());
			$query005 = "SELECT tid, tfid FROM forums_threads WHERE tdate='$date'";
				$result005 = mysql_query($query005) or die('Error : ' . mysql_error());
				$row005 = mysql_fetch_array($result005);
				$tid = $row005['tid'];
				$tfid = $row005['tfid'];
			//header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$tfid&thread=$tid&s=0");
			header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/find/thread-$tid");
			exit;
		}
		else if($CODE == 2)
		{ 
			$tid = mysql_real_escape_string($tid);
			$query2 = mysql_query("SELECT pid FROM forums_post WHERE ptid='$tid'"); 
			$total_thread_posts = mysql_num_rows($query2) or die("Error: ". mysql_error(). " with query ". $query2);
			$new_post_id = $total_thread_posts+1;
			$query = sprintf("INSERT INTO forums_post (ptid, puid, pfid, ptitle, pdate, pbody, ptispost, pip) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($tid ),
				mysql_real_escape_string($puid),
				mysql_real_escape_string($fid),
				mysql_real_escape_string($submittitle),
				mysql_real_escape_string($date),
				mysql_real_escape_string($submitbody),
				mysql_real_escape_string($new_post_id),
				mysql_real_escape_string($userIp));
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			$query = 'UPDATE forums_threads SET tupdated=\'' . mysql_escape_string($tupdated) . '\'WHERE tid=' . $tid . '';
			mysql_query($query) or die('Error : ' . mysql_error());
			if($mod_options != 'nowt'){
				if($mod_options == 'pin'){$query = 'UPDATE forums_threads SET tstickied=\'1\' WHERE tid=' . $tid . '';}
				else if($mod_options == 'unpin'){$query = 'UPDATE forums_threads SET tstickied=\'0\' WHERE tid=' . $tid . '';}
				else if ($mod_options == 'close'){$query = 'UPDATE forums_threads SET tclosed=\'1\' WHERE tid=' . $tid . '';}
				else if ($mod_options == 'pin&close'){$query = 'UPDATE forums_threads SET tclosed=\'1\' AND tstickied=\'1\' WHERE tid=' . $tid . '';}
				else if ($mod_options == 'upinclose'){	$query = 'UPDATE forums_threads SET tclosed=\'1\', tstickied=\'0\' WHERE tid=' . $tid . '';}
				mysql_query($query) or die('Error : ' . mysql_error());
			}
			else {}
				$query005 = "SELECT pid, ptid, pfid FROM forums_post WHERE pdate='$date'";
				$result005 = mysql_query($query005) or die('Error : ' . mysql_error());
				$row005 = mysql_fetch_array($result005);
				$pid = $row005['pid'];
				$ptid = $row005['ptid'];
				$pfid= $row005['pfid'];
				$query = "SELECT fseo FROM forums_forum WHERE fid='$pfid'";
				$result = mysql_query($query) or die('Error : ' . mysql_error());
				$row = mysql_fetch_array($result);
				$fseo = $row005['fseo'];
			//header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$pfid&thread=$ptid&view=getlastpost");
			header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/".fseo."/topic-".$ptid."/showlastpost");
			exit;
		}
		else if($CODE == 3){
			$query = 'UPDATE forums_post SET pbody=\'' . mysql_escape_string($submitbody) . '\'WHERE pid=\'' . $pid . '\'';
			mysql_query($query) or die('Error : ' . mysql_error());
			//header ( "location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?view=findpost&p=$pid" );
			header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/find/post-$pid");
			exit;
		}
		else if($CODE == 4){
			$query = 'UPDATE forums_post SET pbody=\'' . mysql_escape_string($submitbody) . '\'WHERE pid=\'' . $pid . '\'';
			mysql_query($query) or die('Error : ' . mysql_error());
			//header ( "location: http://".$_SERVER['HTTP_HOST']."/forums/index.php?view=findpost&p=$pid" );
			header ( "Location: http://".$_SERVER['HTTP_HOST']."/forums/find/post-$pid");
			exit;
		}
		else
		{
			header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums");
			exit;
		}
	}
}
include_once('header.php');
include_once('header-nav.php');
$forum_global_message = "We want to thank everyone, these forums were redesigned from the ground up to be more user friendly and making reading topics interesting!";
	echo psa($profileArray);

	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	if($profileArray[2] == 0 || $profileArray[2] == 3){
		echo '
		<div id="ad-wrapper" style="height:100%;width:100%;position:absolute;z-index:-1;">
			<div id="ad-sidebar" style="width:220px;float:left;margin:-10px 0 0 -245px;position:absolute;z-index:0;">';
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Advertisements/Game</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<!-- Insticator API Embed Code -->
				<div id="insticator-container">
				<link rel="stylesheet" href="https://embed.insticator.com/embedstylesettings/getembedstyle?embedUUID=693d677f-f905-4a76-8223-3ed59a38842d">
				<div id="div-insticator-ad-1"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-1");</script></div>
				<div id="insticator-embed">';
				echo "
				<div id='insticator-api-iframe-div'><script>(function (d) {var id='693d677f-f905-4a76-8223-3ed59a38842d',cof = 1000 * 60 * 10,cbt = new Date(Math.floor(new Date().getTime() / cof) * cof).getTime(),js = 'https://embed.insticator.com/assets/javascripts/embed/insticator-api.js?cbt='+cbt, f = d.getElementById(\"insticator-api-iframe-div\").appendChild(d.createElement('iframe')),doc = f.contentWindow.document;f.setAttribute(\"id\",\"insticatorIframe\"); f.setAttribute(\"frameborder\",\"0\"); doc.open().write('<script>var insticator_embedUUID = \''+id+'\'; var insticatorAsync = true;<\/script><body onload=\"var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + js + '\'\" >');doc.close(); })(document);</script><noscript><a href=\"https://embed.insticator.com\">Please enable JavaScript.</a></noscript></div>";
				echo '
				</div>';
				if($profileArray[0] == 1){
				echo '
				<div id="div-insticator-ad-2"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-2");</script></div>';
				}
			echo '
			</div>
			<!-- End Insticator API Embed Code -->';
		echo "</div>\n";
		echo "</div>\n";
		echo '
		</div>
		</div>';
	}
    echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td class='note-message' align='center'>".$forum_global_message."</td>\n";
	echo "</tr>\n</table>\n";
	
	echo "<br />\n<br />\n";
	
			include_once('includes/classes/forumpaging.class.php');	
			include_once('includes/classes/forum.class.php');
			$f = new Forum();
			include_once('includes/classes/mainview.forum.class.php');
			include_once('includes/classes/forumview.forum.class.php');
			include_once('includes/classes/threads.forum.class.php');
			include_once('includes/classes/threadview.forum.class.php');
			$f->buildVars(@$_GET['action'],$profileArray);
			$f->Output();	
	// End Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
		
	echo "<br />\n<br />\n<br />\n";
	
	echo "<table align='center' cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td align='center'>".$f->Copyright()."</td>\n";
	echo "</tr>\n</table>\n";
		
include_once('footer.php'); 
?>
