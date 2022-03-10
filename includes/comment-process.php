<?php
include('config.php');

//functions and stuff
function niceday($dated) {
	$dated = str_replace(array(" ",":"),"-",$dated);
	list($year,$month,$day,$hour,$minute) = explode("-",$dated);
	// you can edit this line to display date/time in your preferred notation
	$niceday = @date("g:ia \o\\n\ l, F jS, Y",mktime($hour,$minute,0,$month,$day,$year));
	echo $niceday;
}
//functions and stuff
function niceday2($dated) {
	$dated = str_replace(array(" ",":"),"-",$dated);
	list($year,$month,$day,$hour,$minute) = explode("-",$dated);
	// you can edit this line to display date/time in your preferred notation
	$niceday = @date("g:ia \o\\n\ l, F jS, Y",mktime($hour,$minute,0,$month,$day,$year));
	echo $niceday;
}


if($_POST)
{
	$uid = $_POST['uid'];
	$uid = mysqli_real_escape_string($conn, $uid);
	$ip = $_POST['ip'];
	$ip = mysqli_real_escape_string($conn, $ip);
	$epid = @$_POST['epid'];
	$epid = mysqli_real_escape_string($conn, $epid);
	$comment = $_POST['comment'];
	$comment = mysqli_real_escape_string($conn, $comment);
	$spoiler = @$_POST['spoiler'];
	$spoiler = mysqli_real_escape_string($conn, $spoiler);
	$dated = date("Y-m-d H:i:s");
	$is_approved = 1;
	if(isset($_POST['t']) && $_POST['t'] == 1){
		$pid = $_POST['pid'];
		if(!is_numeric($pid)){
			echo 'The comment was not processed.';
		}
		else {
			$pid = mysqli_real_escape_string($conn, $pid);
			$comment = strip_tags($comment);
			$comment = nl2br($comment);
			//insert the comment
			$query = "INSERT INTO page_comments (id, comments, isSpoiler, ip, page_id, dated, is_approved, uid, epid, type) VALUES (NULL, '$comment', '0', '$ip', 'u".$pid."', '$dated', '$is_approved', '$uid', '0', '1') ";
			mysqli_query($conn, $query);
			//select the id for everything else
			$result = mysqli_query($conn, "SELECT id FROM page_comments WHERE dated='".$dated."' AND uid='".$uid."' AND page_id='u".$pid."'") or die("error ". mysqli_error(). " with query ");
			$row = mysqli_fetch_array($result); //put it in an array
			//insert a notification row.. cause were bad ass like that.
			$queryi = "INSERT INTO notifications (`id`, `uid`, `date`, `type`, `d1`, `d2`, `d3`) VALUES (NULL, '".$pid."', '".time()."', '2', '".$row['id']."', NULL, NULL)";
			mysqli_query($conn, $queryi);
			$result2 = mysqli_query($conn, "SELECT Username, avatarActivate, avatarExtension FROM users WHERE ID='".$uid."'");
			$row2 = mysqli_fetch_array($result2);
			if($row2['avatarActivate'] == 'no'){$avatar = '<img src="//animeftw.tv/images/avatars/default.gif" alt="avatar" width="40px" style="padding:2px;" border="0" />';}
			else {$avatar = '<img src="//animeftw.tv/images/avatars/user'.$uid.'.'.$row2['avatarExtension'].'" alt="User avatar" width="40px" style="padding:2px;" border="0" />';}
			$comment = stripslashes($comment);
			$comment = nl2br($comment);

			//echo $query;
			echo '<div id="justposted" class="side-body floatfix">';
			echo '<div id="dropmsg0" class="dropcontent">
			<div style="float:right;">'.$avatar.'</div>
			<div style="padding-bottom:2px;"><a href="/user/'.$row2['Username'].'">'.$row2['Username'].'</a> - <span title="Posted '.date('l, F jS, o \a\t g:i a',time()).'">'.date('M jS',time()).'</span></div>'.$comment.'</div></div>';
		}
	}
	else {
		if(!is_numeric($epid)){
			echo 'The comment was not processed.';
		}
		else {
			$comment = strip_tags($comment);
			$comment = nl2br($comment);
			$query = "INSERT INTO page_comments (id, comments, isSpoiler, ip, dated, is_approved, uid, epid) VALUES (NULL, '".addslashes($comment)."', '$spoiler', '$ip', '$dated', '$is_approved', '$uid', '$epid') ";
			mysqli_query($conn, $query);
			//Select the last one you just did..
			$result = mysqli_query($conn, "SELECT id FROM page_comments WHERE dated='".$dated."' AND uid='".$uid."' AND epid='".$epid."'") or die("error ". mysqli_error(). " with query ".$query);
			$row = mysqli_fetch_array($result);
			$result2 = mysqli_query($conn, "SELECT Username, avatarActivate, avatarExtension FROM users WHERE ID='".$uid."'");
			$row2 = mysqli_fetch_array($result2);
			if($row2['avatarActivate'] == 'no'){$avatar = '<img src="//animeftw.tv/images/avatars/default.gif" alt="avatar" height="50px" border="0" />';}
			else {$avatar = '<img src="//animeftw.tv/images/avatars/user'.$uid.'.'.$row2['avatarExtension'].'" alt="User avatar" height="50px" border="0" />';}

			$commentajax = "ajax_loadContent('ca-".$row['id']."','/includes/random_scripts.php?get=utilities&mode=comutil&cid=".$row['id']."&username=".$row2['Username']."&stage=before');";
			$comment = stripslashes($comment);
			$comment = nl2br($comment);

			echo '
			<li class="box">
				<div class="more2"><span id="ca-'.$row['id'].'">loading..</span><script type="text/javascript">'.$commentajax.'</script></div>
				<div align="right">
				<div style="float:right;padding-left:5px;"><a href="/user/'.$row2['Username'].'">'.$avatar.'</a></div>
				<span class="com_name">Posted by <a href="/user/'.$row2['Username'].'">'.$row2['Username'].'</a> on '.date("l, F jS, Y",time()).'</span> <br />
				<span class="comment">'.$comment.'</span>
				</div>
			</li>';
		}
	}
}