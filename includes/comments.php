<?php
	require_once("/home/mainaftw/public_html/includes/classes/config.v2.class.php");
	 $C = new Config();

echo '<div align="center" style="padding-right:80px;">';
echo '	<ol id="update" class="timeline">';
echo '		<div id="flash"></div>';

$epid = $_GET['epid'];
if(isset($_GET['count']))
{
	$from = $_GET['count'];
}
else
{
	$from = '';
}

//$epid = mysql_real_escape_string($epid);
$comment_limit = 15;
if(isset($_GET['comm_page']))
{
	$commpage = $_GET['comm_page'];
}
else
{
	$commpage= '';
}
$comm_page = is_numeric($commpage) ? $commpage : 1;
if($comm_page<1)
{
	$comm_page = 1;
}
//construct query
$from = $comm_page * $comment_limit - $comment_limit;
// construct page query to find out how many matches
$C->mysqli->query("SET NAMES 'utf8'");

$result = $C->mysqli->query("select id from page_comments WHERE epid='" . $C->mysqli->real_escape_string($epid) . "' AND is_approved = '1'") or die('Error : ' . $this->mysqli->error);

$count = mysqli_num_rows($result);

$total_pages = ceil($count / $comment_limit);

function niceday($dated)
{
	$dated = str_replace(array(" ",":"),"-",$dated);
	list($year,$month,$day,$hour,$minute) = explode("-",$dated);
	// you can edit this line to display date/time in your preferred notation
	$niceday = @date("g:ia \o\\n\ l, F jS, Y",mktime($hour,$minute,0,$month,$day,$year));
	return $niceday;
}

// check to see if there are any comments..
if($count == 0)
{
	echo "<li>No Comments have been provided.</li>\n";
}
else 
{
	//$post_id value comes from the POSTS table
	$query = "SELECT comments.id, comments.comments, comments.isSpoiler, comments.ip, comments.dated, comments.negative, comments.positive, comments.uid, u.Username, u.avatarActivate, u.avatarExtension, (SELECT COUNT(id) FROM ratings WHERE rating_id=CONCAT('c',comments.id) AND IP = '1') AS Voted FROM page_comments AS comments, users AS u WHERE comments.epid = '$epid' AND comments.is_approved = '1' AND comments.type = 0 AND comments.uid=u.ID ORDER by comments.dated DESC LIMIT $from, $comment_limit";
	$C->mysqli->query("SET NAMES 'utf8'");
	$result = $C->mysqli->query($query);
	$i = 1;
	
	//$query = "SELECT * FROM ratings WHERE rating_id='c".mysql_escape_string($cid)."' AND IP = '".$profileArray[1]."'";
//			$result = mysql_query($query);
	
	while($row = $result->fetch_assoc())
	{
		$uid = $row['uid'];
		$cid = $row['id'];
		$comments = $row['comments'];
		$comments = preg_replace('/\v+|\\\[rn]/','<br />',$comments);
		$comments = stripslashes($comments);
		$isSpoiler = $row['isSpoiler'];
		$ip = $row['ip'];
		$dated = $row['dated'];
		$negative = $row['negative'];
		$positive = $row['positive'];
		
		//$query1 = "SELECT Username, avatarActivate, avatarExtension FROM users WHERE ID = '$uid'";// what matches THIS page?
		//$result1 = mysql_query($query1) or die("Error: ". mysql_error(). " with query ". $query1);
		//$row1 = mysql_fetch_array($result1);
		if($row['avatarActivate'] == 'no'){$avatar = '<img src="/images/avatars/default.gif" alt="avatar" height="50px" border="0" />';}
		else {$avatar = '<img src="/images/avatars/user'.$uid.'.'.$row['avatarExtension'].'" alt="User avatar" height="50px" border="0" />';}
		if($isSpoiler == 1)
		{
			$comments = "<a style=\"cursor:pointer;\" onclick=\"ShowHideContent(this,'comment_".$cid."');\">****Episode Spoiler**** (click to reveal)</a>\n<span id=\"comment_".$cid."\" style=\"display:none\">".$comments."</span>\n";
		}
		// /scripts.php?view=utilities&amp;mode=comutil&amp;stage=after&amp;cid=42213&amp;username=chichi&amp;uid=14788&amp;vote=down
		$commentajax = "ajax_loadContent('ca-".$cid."','/scripts.php?view=utilities&mode=comutil&cid=".$cid."&username=".$row['Username']."&uid=".$uid."&stage=before');";
		// Action bar code
		$actionbar = '';
		if($C->UserArray['logged-in'] == 1)
		{
			if($row['Voted'] == 0)
			{
				// it means they have not voted yet
				$actionbar .= '<a id="cid-up-' . $cid . '" class="vote-up linkopacity" style="cursor:pointer;" title="Vote Up this Comment!"><img src="/images/tinyicons/thumb_up.png" alt="" border="0"></a>&nbsp;';
				$actionbar .= '<a id="cid-dw-' . $cid . '" class="vote-down linkopacity" style="cursor:pointer;" title="Vote Down this Comment!"><img src="/images/tinyicons/thumb_down.png" alt="" border="0"></a>&nbsp;';
			}
			else
			{
				// it means they already voted this up or down.
				$actionbar .= '<a class="linkopacity" title="You have already voted on this comment!"><img src="/images/tinyicons/thumb_up.png" alt="" border="0"></a>&nbsp;';
				$actionbar .= '<a class="linkopacity" title="You have already voted on this comment!"><img src="/images/tinyicons/thumb_down.png" alt="" border="0"></a>&nbsp;';
			}
		}
		$actionbar .= '<a href="/pm/compose/' . $uid . '" title="Will open in a new Tab" target="_blank" class="linkopacity"><img src="/images/tinyicons/email.png" alt="" border="0"></a>&nbsp;';
		$actionbar .= '<a href="javascript:void(0)" class="linkopacity" title="Report Comment" onclick="alert(\'Feature coming soon!\');"><img src="/images/tinyicons/exclamation.png" alt="" border="0"></a>&nbsp;';
		$actionbar .= '<a href="/user/' . $row['Username'] . '" class="linkopacity" title="View User\'s Profile" target="_blank"><img src="/images/tinyicons/user.png" alt="" border="0"></a>';
		
		if ($i % 2)
		{
			echo '
			<li class="box">
				<div class="comment-wrapper" id="comment-' . $cid . '">
					<div align="left">	
						<div class="comment-header" style="width:100%;padding:2px;">
							<div style="display:inline-block;width:74%;vertical-align:top;margin-left:0;" align="left">
								<span class="com_name">Posted by ' . $C->string_fancyUsername($row['uid']) . ' at ' . niceday($dated) . '</span>
							</div>
							<div style="display:inline-block;width:25%;vertical-align:top;" align="right">
								<div class="more2">' . $actionbar . '</div>
							</div>
						</div>					
						<div style="padding:2px;">
							<div style="display:inline-block;padding:5px;width:8%;vertical-align:top;">
								<a href="/user/' . $row['Username'] . '">' . $avatar . '</a>
							</div>
							<div class="comment-body" style="display:inline-block;width:90%;text-align:left;">
								<span class="comment">' . $comments . '</span>
							</div>
						</div>
					</div>
				</div>
			</li>';
		}
		else 
		{
			echo '
			<li class="box">
				<div class="comment-wrapper" id="comment-' . $cid . '">
					<div align="right">
						<div class="comment-header" style="width:100%;padding:2px;">
							<div style="display:inline-block;width:25%;vertical-align:top;" align="left">
								<div class="more2">' . $actionbar . '</div>
							</div>
							<div style="display:inline-block;width:74%;vertical-align:top;margin-right:0;" align="right">
								<span class="com_name">Posted by ' . $C->string_fancyUsername($row['uid']) . ' at ' . niceday($dated) . '</span>
							</div>
						</div>
						<div style="padding:2px;">
							<div class="comment-body" style="display:inline-block;width:90%;text-align:right;">
								<span class="comment">' . $comments . '</span>
							</div>
							<div style="display:inline-block;padding:5px;width:8%;vertical-align:top;">
								<a href="/user/' . $row['Username'] . '">' . $avatar . '</a>
							</div>
						</div>
					</div>
				</div>
			</li>';
		}
		$i++;
	}
}
// Pagination magic (of sorts)
if ($total_pages>1) {
			echo "<div style=\"background:#F7F7F7;\"><br /><span style=\"padding:10px;\"><span class=\"commentpaging\">Page:</span>&nbsp;";
			for ($z=-5; $z<6;$z++) {
				$dapage = $comm_page+$z;
				if (($dapage>0) && ($dapage<=$total_pages)) {
					if ($dapage==$comm_page) {
						echo "<span class=\"commentpagingcurrent\">&nbsp;". $dapage. "&nbsp;</span>";
					} else {
						echo "<span class=\"commentpaging\"><a class=\"comment-paging\" id=\"\" onclick=\"$('#comments1').html('<div align=\'center\' style=\'padding:10px;text-align:center;font-size:16px;\'><img src=\'/images/loading-mini.gif\' alt=\'\' />&nbsp;Loading the Next Page. Please Wait...</div>').delay(3000).load('/includes/comments.php?epid=".$epid."&amp;comm_page=". $dapage. "'); return false;\" style='cursor:pointer;'>&nbsp;". $dapage. "&nbsp;</a></span>";
					}
					echo "&nbsp;&nbsp;";
				}			
			}
			echo "</span></div>";
		echo '<script type="text/javascript">
				$(function() {
					$("a[rel]").overlay({mask: \'#000\', effect: \'apple\'});
				});
			</script>';
}
echo '
</ol>
</div>';
	echo '
	<script>
		$(document).ready(function(){
			$(".comment-wrapper").hover(function() {
				
			});
			$(".vote-up").click(function() {
				var comment_id = $(this).attr("id").substring(7);
				var request_url = "/scripts.php?view=utilities&mode=comment-votes&cid=" + comment_id + "&vote=up";
				$.get(request_url, function(){
				})
				.done(function(html) {
					if(html.indexOf("success") >= 0)
					{
						$("#cid-dw-" + comment_id).attr("title").val("You voted up this comment!");
						$("#cid-up-" + comment_id).attr("title").val("You voted up this comment!");
					}
					else
					{
						alert(html);
					}
				})
				.fail(function(html) {
					alert(html);
				});
			});
			$(".vote-down").click(function() {
				var comment_id = $(this).attr("id").substring(7);
				var request_url = "/scripts.php?view=utilities&mode=comment-votes&cid=" + comment_id + "&vote=down";
				$.get(request_url, function(){
				})
				.done(function(html) {
					if(html.indexOf("Success") >= 0)
					{
						$("#cid-dw-" + comment_id).attr("title").val("You voted up this comment!");
						$("#cid-up-" + comment_id).attr("title").val("You voted up this comment!");
					}
					else
					{
						alert(html);
					}
				})
				.fail(function(html) {
					alert(html);
				});
			});
		});
	</script>';