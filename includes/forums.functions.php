<?php
include('config.php');
include('newsOpenDb.php');
if ($_GET['view'] == 'lastpost')
{
	if(isset($_GET['thread']))
	{
		$topicID = $_GET['thread'];
	}
	
	$query = "SELECT * FROM forums_post WHERE ptid='$topicID' ORDER BY `forums_post`.`ptispost` DESC LIMIT 0, 1";
			$result = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
			$row = mysqli_fetch_array($result);
			$pid = $row['pid'];
			$ptid = $row['ptid'];
			$pfid = $row['pfid'];
			$ptispost = $row['ptispost'];
			if ($ptispost == 20){
				$ptispost = 19;
			}
			else {
			}
			$rounded = ceil($ptispost/20);
			$finalRounded = $postArray[$rounded];
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$pfid&thread=$ptid&s=$finalRounded#entry$pid");
}
if ($_GET['view'] == 'findpost')
{ 
	if(isset($_GET['p']))
	{
		$postID = $_GET['p'];
	}
	$query = "SELECT * FROM forums_post WHERE pid='$postID'";
			$result = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
			$row = mysqli_fetch_array($result);
			$pid = $row['pid'];
			$ptid = $row['ptid'];
			$pfid = $row['pfid'];
			$ptispost = $row['ptispost'];
			$rounded = ceil($ptispost/20);
			$finalRounded = $postArray[$rounded];
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$pfid&thread=$ptid&s=$finalRounded#entry$postID");
}
if ($_GET['view'] == 'delete')
{ 
	if(isset($_GET['p']))
	{
		$postID = $_GET['p'];
	}
	if(isset($_GET['f']))
	{
		$forumID = $_GET['f'];
	}
	if(isset($_GET['t']))
	{
		$threadID = $_GET['t'];
	}
	$query = "SELECT * FROM forums_post WHERE pid='$postID'";
			$result = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
			$row = mysqli_fetch_array($result);
			$pid = $row['pid'];
			$ptid = $row['ptid'];
			$pfid = $row['pfid'];
			$ptispost = $row['ptispost'];
			$rounded = floor($ptispost/20);
			$finalRounded = $postArray[$rounded];
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$pfid&thread=$ptid&s=$finalRounded#entry$postID");
}
			if ($_POST['doreply'])
	{
	 
	$CODE = $_POST['CODE']; //1=new thread. 2=reply
	$fid = $_POST['fid']; //replying OR making a new thread (id it)
	$tid = $_POST['tid']; //replying - get topic id
	$ptitle = $_POST['ptitle']; //title for the replied topic..
	$puid = $_POST['puid']; //the subission user's id
	$pid = $_POST['pid']; //the subission user's id
	$submittitle = $_POST['submittitle'];
	$submitbody = $_POST['submitbox'];
	$mod_options = $_POST['mod_options'];
	$userIp = $_SERVER['REMOTE_ADDR'];
	//$submitbody = htmlspecialchars($submitbody);
	$post_htmlstatus = $_POST['post_htmlstatus'];
	if($post_htmlstatus == 0)
	{
		$submitbody = htmlspecialchars($submitbody);
		$submitbody = code_to_html($submitbody);
	}
	else if ($post_htmlstatus == 1)
	{
		$submitbody = code_to_html($submitbody);
	}
	else {
		$submitbody = code_to_html($submitbody);
	}
	$submittitle = stripslashes($submittitle);
	$date = time();
	$tupdated = time();
	if ($CODE == 1)
	{
		if ($mod_options == 'nowt')
		{
		$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated) VALUES ('%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($conn, $submittitle, $conn),
			mysqli_real_escape_string($conn, $puid, $conn),
			mysqli_real_escape_string($conn, $fid, $conn),
			mysqli_real_escape_string($conn, $date, $conn),
			mysqli_real_escape_string($conn, $tupdated, $conn));
		mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
		}
		else {
			if($mod_options == 'pin')
			{
				$modoption = 1;
				$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated, tstickied) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
				mysqli_real_escape_string($conn, $submittitle, $conn),
				mysqli_real_escape_string($conn, $puid, $conn),
				mysqli_real_escape_string($conn, $fid, $conn),
				mysqli_real_escape_string($conn, $date, $conn),
				mysqli_real_escape_string($conn, $tupdated, $conn),
				mysqli_real_escape_string($conn, $modoption, $conn));
				mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
			}
			else if ($mod_options == 'close') {
				$modoption = 1;
				$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated, tclosed) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
				mysqli_real_escape_string($conn, $submittitle, $conn),
				mysqli_real_escape_string($conn, $puid, $conn),
				mysqli_real_escape_string($conn, $fid, $conn),
				mysqli_real_escape_string($conn, $date, $conn),
				mysqli_real_escape_string($conn, $tupdated, $conn),
				mysqli_real_escape_string($conn, $modoption, $conn));
				mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
			}
			else {
				$modoption = 1;
				$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated, tstickied, tclosed) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				mysqli_real_escape_string($conn, $submittitle, $conn),
				mysqli_real_escape_string($conn, $puid, $conn),
				mysqli_real_escape_string($conn, $fid, $conn),
				mysqli_real_escape_string($conn, $date, $conn),
				mysqli_real_escape_string($conn, $tupdated, $conn),
				mysqli_real_escape_string($conn, $modoption, $conn),
				mysqli_real_escape_string($conn, $modoption, $conn));
				mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
			}
		
		
		}
		$query006 = "SELECT * FROM forums_threads WHERE tdate='$date'";
			$result006 = mysqli_query($conn, $query006) or die('Error : ' . mysqli_error());
			$row006 = mysqli_fetch_array($result006);
			$ptid3 = $row006['tid'];
		$pistopic = 1;
		$query2 = sprintf("INSERT INTO forums_post (ptid, puid, pfid, ptitle, pdate, pbody, pistopic, pip) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($conn, $ptid3, $conn),
			mysqli_real_escape_string($conn, $puid, $conn),
			mysqli_real_escape_string($conn, $fid, $conn),
			mysqli_real_escape_string($conn, $submittitle, $conn),
			mysqli_real_escape_string($conn, $date, $conn),
			mysqli_real_escape_string($conn, $submitbody, $conn),
			mysqli_real_escape_string($conn, $pistopic, $conn),
			mysqli_real_escape_string($conn, $userIp, $conn));
		mysqli_query($conn, $query2) or die('Could not connect, way to go retard:' . mysqli_error());
		$query005 = "SELECT * FROM forums_threads WHERE tdate='$date'";
			$result005 = mysqli_query($conn, $query005) or die('Error : ' . mysqli_error());
			$row005 = mysqli_fetch_array($result005);
			$tid = $row005['tid'];
			$tfid = $row005['tfid'];
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$tfid&thread=$tid&s=0");
	}
	else if($CODE == 2)
	{ 
		$query2 = mysqli_query($conn, "SELECT * FROM forums_post WHERE ptid='$tid'"); 
		$total_thread_posts = mysqli_num_rows($query2) or die("Error: ". mysqli_error(). " with query ". $query2);
		$new_post_id = $total_thread_posts+1;
		$query = sprintf("INSERT INTO forums_post (ptid, puid, pfid, ptitle, pdate, pbody, ptispost, pip) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($conn, $tid , $conn),
			mysqli_real_escape_string($conn, $puid, $conn),
			mysqli_real_escape_string($conn, $fid, $conn),
			mysqli_real_escape_string($conn, $submittitle, $conn),
			mysqli_real_escape_string($conn, $date, $conn),
			mysqli_real_escape_string($conn, $submitbody, $conn),
			mysqli_real_escape_string($conn, $new_post_id, $conn),
			mysqli_real_escape_string($conn, $userIp, $conn));
		mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
		$query = 'UPDATE forums_threads SET tupdated=\'' . mysqli_escape_string($tupdated) . '\'WHERE tid=' . $tid . '';
  		mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
		if($mod_options != 'nowt')
		{
			if($mod_options == 'pin')
			{
				$query = 'UPDATE forums_threads SET tstickied=\'1\' WHERE tid=' . $tid . '';
			}
			else if($mod_options == 'unpin')
			{
				$query = 'UPDATE forums_threads SET tstickied=\'0\' WHERE tid=' . $tid . '';
			}
			else if ($mod_options == 'close')
			{
				$query = 'UPDATE forums_threads SET tclosed=\'1\' WHERE tid=' . $tid . '';
			}
			else if ($mod_options == 'pin&close')
			{
				$query = 'UPDATE forums_threads SET tclosed=\'1\' AND tstickied=\'1\' WHERE tid=' . $tid . '';
			}
			else if ($mod_options == 'upinclose')
			{
				$query = 'UPDATE forums_threads SET tclosed=\'1\', tstickied=\'0\' WHERE tid=' . $tid . '';
			}
  		mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
		}
		else {
		}
		$query005 = "SELECT * FROM forums_post WHERE pdate='$date'";
			$result005 = mysqli_query($conn, $query005) or die('Error : ' . mysqli_error());
			$row005 = mysqli_fetch_array($result005);
			$pid = $row005['pid'];
			$ptid = $row005['ptid'];
			$pfid= $row005['pfid'];
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?forum=$pfid&thread=$ptid&view=getlastpost");
	}
	else if($CODE == 3)
	{
		$query = 'UPDATE forums_post SET 
		pbody=\'' . mysqli_escape_string($submitbody) . '\'WHERE pid=\'' . $pid . '\'';
   		mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
		header ( "location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?view=findpost&p=$pid" );
	}
	else if($CODE == 4)
	{
		$query = 'UPDATE forums_post SET 
		pbody=\'' . mysqli_escape_string($submitbody) . '\'WHERE pid=\'' . $pid . '\'';
   		mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
		header ( "location: http://".$_SERVER['HTTP_HOST']."/forums/index.php?view=findpost&p=$pid" );
	}
	else
	{
		header ( "Location: ".$sslornot."://".$_SERVER['HTTP_HOST']."/forums/index.php?");
	}
}
if ($_POST['send'])
{

$sendTo = $_POST['sendTo'];
$sendFrom = $_POST['sendFrom'];
$msgSubject = $_POST['msgSubject'];
$msgBody = $_POST['msgBody'];
if ($msgSubject == '')
	{
		$msgSubject = '<no subject>';
	}
/*function multiple_sendTos ( $str )
{
	return ( preg_match ( ",", $str ) ) ? FALSE : TRUE;
}
if ( multiple_sendTos ( $sendTo ) == TRUE )
{
	$error = "You have tried to Insert more than one person, please click back and try again.";
}
else
{*/
	$sendTo = stripslashes($sendTo);
	$sendFrom = stripslashes($sendFrom);
	$msgSubject = stripslashes($msgSubject);
	$msgBody = stripslashes($msgBody);
	
	$MsgBody = code_to_html($msgBody);
	$MsgBody = nl2br($MsgBody);

	$cleanSendTo = htmlspecialchars($sendTo);
	$cleanMsgSubject = htmlspecialchars($msgSubject); 
	$query = sprintf("INSERT INTO messages (sendTo, sendFrom, msgSubject, msgBody, Sent) VALUES ('%s', '%s', '%s', '%s', 'yes')",
	mysqli_real_escape_string($conn, $cleanSendTo, $conn),
	mysqli_real_escape_string($conn, $sendFrom, $conn),
	mysqli_real_escape_string($conn, $cleanMsgSubject, $conn),
	mysqli_real_escape_string($conn, $MsgBody, $conn));
mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());			
header('Location: '.$sslornot.'://'.$_SERVER['HTTP_HOST'].'/messages/sent');
exit;
$message = "You have just sent the Message successfully";
	//}
}

?>