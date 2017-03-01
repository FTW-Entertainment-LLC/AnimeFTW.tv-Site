<?php
/****************************************************************\
## FileName: comments.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Comment Class and Functions
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
## Updated: 10/5/2014 by Robotman321
## Version: 1.0
\****************************************************************/

class Comment extends Config {

	var $PerPage, $Epid, $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes, $UserArray;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL,$Epid=0)
	{
		// include the parent config so we can do stuffs.
		parent::__construct();
		
		// Options needed when the API is being used.
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
		
		// deploy the variables used in normal operations.
		$this->PerPage				= 20;
		$this->Epid					= $Epid;		// Episode ID, always helpful.
		
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	public function showComments()
	{
		echo '
		<div id="comments-wrapper">';
		// if the user is not banned from posting comments, we let it through.
		$this->showForm();
		
		// Wrap the comments in their div here so we can add dynamic loading later..
		echo '<div class="comments-group">';
		$this->buildComments();
		echo '</div>';
		echo '
		</div>';
	}
	
	public function processComment()
	{
		if(isset($_POST['form-type']) && $_POST['form-type'] == 'ProfileComment')
		{
			$pid = $_POST['pid'];
			$ip = $_SERVER['REMOTE_ADDR'];
			$uid = $this->UserArray['ID'];
			$comment = $_POST['comment'];
			$pid = $this->mysqli->real_escape_string($pid);
			$comment = strip_tags($comment);
			$comment = nl2br($comment);
			$dated = date("Y-m-d H:j:s");
			//insert the comment
			$query = "INSERT INTO page_comments (`id`, `comments`, `isSpoiler`, `ip`, `page_id`, `dated`, `admin_comment`, `is_approved`, `uid`, `epid`, `type`) VALUES (NULL, '$comment', '0', '$ip', 'u".$pid."', '$dated', '', '1', '$uid', '0', '1') ";
			$result = $this->mysqli->query($query);
			$insertId = $this->mysqli->insert_id;
			//select the id for everything else
			$this->mysqli->query("INSERT INTO notifications (`id`, `uid`, `date`, `type`, `d1`, `d2`, `d3`) VALUES (NULL, '".$pid."', '".time()."', '2', '" . $insertId . "', NULL, NULL)");
			
			if($this->UserArray['avatarActivate'] == 'no')
			{
				$avatar = '<img src="' . $this->ImageHost . '/avatars/default.gif" alt="avatar" width="40px" style="padding:2px;" border="0" />';
			}
			else
			{
				$avatar = '<img src="' . $this->ImageHost . '/avatars/user' . $this->UserArray['ID'] . '.' . $this->UserArray['avatarExtension'] . '" alt="User avatar" width="40px" style="padding:2px;" border="0" />';
			}
			$comment = stripslashes($comment);
			$comment = nl2br($comment);
			
			//echo $query;
			echo '<div id="justposted" class="side-body floatfix" style="margin-bottom:2px;">';
			echo '<div id="dropmsg0" class="dropcontent">
			<div style="float:right;">'.$avatar.'</div>
			<div style="padding-bottom:2px;">' . $this->UserArray['FancyUsername'] . ' - <span title="Posted '.date('l, F jS, o \a\t g:i a',time()).'">'.date('M jS',time()).'</span></div>'.$comment.'</div></div>';
			
			if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv'){
				$slackData = "*New Profile Comment by " . $this->UserArray['Username'] . " on " . $this->string_fancyUsername($pid,NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . "'s profile from the Website*: \n ```" . $comment . "```";
				$slack = $this->postToSlack($slackData);
			}
		}
		else
		{
			echo 'Something went wrong somewhere..';
		}
	}
	
	private function showForm()
	{
		if($this->UserArray['postBan'] == 0)
		{
			// Build the users avatar
			if($this->UserArray['avatarActivate'] == 'no')
			{
				$avatar = '<img src="' . $this->ImageHost . '/avatars/default.gif" alt="avatar" height="80px" border="0" />';
			}
			else
			{
				$avatar = '<img src="' . $this->ImageHost . '/avatars/user'.$this->UserArray['ID'].'.'.$this->UserArray['avatarExtension'].'" alt="User avatar" height="80px" border="0" />';
			}
			echo '
			<div class="comments-form" style="margin-top:20px;">
				<div class="comments-user-avatar" style="display:inline-block;vertical-align:top;margin:0 20px 0 10px;">
					' . $avatar . '
				</div>
				<div class="comments-formarea" style="display:inline-block;vertical-align:top;" align="center">
					<form action="#" method="post" id="commentformv2">
						<input type="hidden" id="epid" name="epid" value="' . $this->Epid . '"/>
						<input type="hidden" id="ip" name="ip" value="' . $_SERVER['REMOTE_ADDR'] . '"/>
						<input type="hidden" id="uid" name="uid" value="' . $this->UserArray['ID'] .'"/>
						<input type="hidden" id="post" name="post" value="Comments" />
						<textarea id="comment" name="comment" style="width:650px;height:60px;"></textarea><br />
						Spoiler?&nbsp;<select name="spoiler" id="spoiler">
								<option value="0" selected="selected">No</option>
								<option value="1">Yes</option>
								</select>&nbsp;<input type="submit" class="comment-submit" value=" Submit Comment " />
					</form>
					<div style="font:11px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;padding:2px;">If an episode has ANY issues, please do NOT post a comment about it. <a href="/forums/bug-reports/" target="_blank">Report all Video related issues here.</a></div>
				</div>
				</div>
			</div>
			<script>
			$(".comment-submit").click(function() {
				$(".comment-submit").attr("disabled", true);				
				$.ajax({
					type: "POST",
					url: "/scripts.php?view=commentsv2&epid=' . $this->Epid . '&sub=post",
					data: $(\'#commentformv2\').serialize(),
					success: function(html) {
						if(html.indexOf("Success") >= 0){
							$("#comments1").load("/scripts.php?view=commentsv2&epid=' . $this->Epid . '&highlight=true");
						}
						else {
							alert("There was an issue processing that comment.");
						}
					}
				});
				return false;
			});
			</script>';
		}
	}
	private function buildComments()
	{
		// Set variables needed down the line
		$ajax = 0;
		// We need to see if we are on a certain page.
		if(isset($_GET['page']))
		{
			$page = $_GET['page']*$this->PerPage;
			$ajax = 1; // We need to let the script know this should be done through a ajax call
		}
		else
		{
			$page = 0;
		}
		$query = "SELECT comments.id, comments.comments, comments.isSpoiler, comments.ip, comments.dated, comments.negative, comments.positive, comments.uid, u.Username, u.avatarActivate, u.avatarExtension, (SELECT COUNT(id) FROM ratings WHERE rating_id=CONCAT('c',comments.id) AND IP = '1') AS Voted FROM page_comments AS comments, users AS u WHERE comments.epid = '" . $this->mysqli->real_escape_string($this->Epid) ."' AND comments.is_approved = '1' AND comments.type = 0 AND comments.uid=u.ID ORDER by comments.dated DESC LIMIT $page, " . $this->PerPage;
		$this->mysqli->query("SET NAMES 'utf8'");
		$result = $this->mysqli->query($query);
		$count = mysqli_num_rows($result);
		if($count == 0)
		{
			echo '<div style="font-size:20px;margin:10px;color:#d0d0d0;" align="center">There have been no comments for this video. Oh no...!</div>';
		}
		else
		{
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
				
				// Build the date in a normal format..
				$dated = str_replace(array(" ",":"),"-",$dated);
				list($year,$month,$day,$hour,$minute) = explode("-",$dated);
				// you can edit this line to display date/time in your preferred notation
				$dated = @date("M jS Y",mktime($hour,$minute,0,$month,$day,$year));
			
				
				// Build the users avatar
				if($row['avatarActivate'] == 'no')
				{
					$avatar = '<img src="' . $this->ImageHost . '/avatars/default.gif" alt="avatar" height="50px" border="0" />';
				}
				else
				{
					$avatar = '<img src="' . $this->ImageHost . '/avatars/user'.$uid.'.'.$row['avatarExtension'].'" alt="User avatar" height="60px" border="0" />';
				}
				if($isSpoiler == 1)
				{
					$comments = "<a style=\"cursor:pointer;\" onclick=\"ShowHideContent(this,'comment_".$cid."');\">****Episode Spoiler**** (click to reveal)</a>\n<span id=\"comment_".$cid."\" style=\"display:none\">".$comments."</span>\n";
				}
				// /scripts.php?view=utilities&amp;mode=comutil&amp;stage=after&amp;cid=42213&amp;username=chichi&amp;uid=14788&amp;vote=down
				$commentajax = "ajax_loadContent('ca-".$cid."','/scripts.php?view=utilities&mode=comutil&cid=".$cid."&username=".$row['Username']."&uid=".$uid."&stage=before');";
				// Action bar code
				$actionbar = '';
				if($this->UserArray['logged-in'] == 1)
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
				
				echo '
					<div class="single-comment-wrapper" style="margin:20px 0 20px 10px;">
						<div class="comment-wrapper" id="comment-' . $cid . '">
							<div class="comment-left" style="display:inline-block;width:10%;vertical-align:top;" align="center">
								<a href="/user/' . $row['Username'] . '">' . $avatar . '</a>
							</div>
							<div class="comment-right" style="display:inline-block;width:80%;vertical-align:top;margin-left:10px;">
								<div>
									<div style="display:inline-block;">
										' . $this->string_fancyUsername($row['uid']) . '
									</div>
									<div style="display:inline-block;margin-left:15px;">
										' . $dated . '
									</div>
								</div>
								<div>
									' . $comments . '
								</div>
							</div>
						</div>
					</div>';
			}
			
			if($ajax == 0 && $count == 20)
			{
				echo '</div>';
				echo '
				<script>
					var nextpage = 1;			
					jQuery(
					  function($)
					  {
						$(".comments-group").bind("scroll", function()
						{
							if($(".comments-end-dynamic-data").length)
							{
							}
							else
							{
								if($(this).scrollTop() + $(this).innerHeight()>=$(this)[0].scrollHeight)
								{
									var url = \'/scripts.php?view=commentsv2&page=\' + nextpage + \'&epid=' . $this->Epid . '\';
									$.post(url, function(data) {
										$(\'#available-episodes\').children().last().after(data);
										nextpage++;
									});
								}
							}
						})
					  }
					);
				</script>';
				
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
			}
		}
	}

	public function processSubmission()
	{
		// objective of this script is to process the form data coming in..
		
		$uid = $this->UserArray['ID'];
		$ip = $this->mysqli->real_escape_string($_POST['ip']);
		$epid = $this->mysqli->real_escape_string($_POST['epid']);
		$comment = $this->mysqli->real_escape_string($_POST['comment']);
		$spoiler = @$_POST['spoiler'];
		$spoiler = $this->mysqli->real_escape_string($spoiler);
		$dated = date("Y-m-d H:i:s");
		$is_approved = 1;
		
		
		$pid = $this->mysqli->real_escape_string($pid);
		$comment = strip_tags($comment);
		$comment = nl2br($comment);
		//insert the comment
		$query = "INSERT INTO page_comments (id, comments, isSpoiler, ip, page_id, dated, is_approved, uid, epid, type) VALUES (NULL, '$comment', '$spoiler', '$ip', '0', '$dated', '$is_approved', '$uid', '$epid', '0') ";
		$this->mysqli->query($query);
		if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv'){
			$slackData = "*New Episode Comment Posted by " . $this->string_fancyUsername($uid,NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . " from the Website*: \n ```" . $comment . "``` <https://www.animeftw.tv/manage/#comments>";
			$slack = $this->postToSlack($slackData);
		}
		echo 'Success';
	}
	
	public function bool_totalComments($id)
	{
		$query = "SELECT COUNT(id) as numrows FROM `page_comments` WHERE `epid` = " . $this->mysqli->real_escape_string($id);
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		return $row['numrows'];
	}
	
	public function array_displayComments()
	{
		// we set the count of how many comments to display, default is 20
		if(isset($this->Data['count']))
		{
			$count = $this->Data['count'];
		}
		else
		{
			$count = 20;
		}
		// get the current page, we'll need to multiply it by the count.
		if(isset($this->Data['page']))
		{
			$page = $this->Data['page']*$count;
		}
		else
		{
			$page = 0;
		}
		// we check what type of comment this is..
		if(isset($this->Data['type']) && isset($this->Data['id']))
		{
			if(strtolower($this->Data['type']) == 'profile' || $this->Data['type'] == '1')
			{
				// profile based comments
				$whereclause = "`page_id` = '" . $this->mysqli->real_escape_string($this->Data['id']) . "'";
			}
			else if(strtolower($this->Data['type']) == 'video' || $this->Data['type'] == '0')
			{
				// comments on videos.
				$whereclause = "`epid` = " . $this->mysqli->real_escape_string($this->Data['id']);
			}
			else
			{
				// this was not a valid request, we won't display data.
				$whereclause = 1;
			}
		}
		else if(!isset($this->Data['type']) && isset($this->Data['id']))
		{
			$whereclause = 2;
		}
		else if(!isset($this->Data['type']) && !isset($this->Data['id']))
		{
			$whereclause = 3;
		}
		else if(isset($this->Data['type']) && !isset($this->Data['id']))
		{
			$whereclause = 0;
		}
		else
		{
			$whereclause = 4;
		}
		// now we check to see if we should pass through content to the client or give them an error.
		if(!is_numeric($whereclause))
		{
			$query = "SELECT `id`, `comments`, `isSpoiler`, `dated`, `uid`, `negative`, `positive` FROM `page_comments` WHERE $whereclause ORDER BY `dated` LIMIT $page, $count";
			$result = $this->mysqli->query($query);
			$count = mysqli_num_rows($result);
			$returnarray = array();
			$returnarray['status'] = $this->MessageCodes["Result Codes"]["200"]["Status"];
			if($count > 0)
			{
				$i = 0;
				while($row = $result->fetch_assoc())
				{
					$returnarray['results'][$i]['id'] = $row['id'];
					$returnarray['results'][$i]['comment'] = stripslashes(nl2br($row['comments']));
					$returnarray['results'][$i]['spoiler'] = $row['isSpoiler'];
					$returnarray['results'][$i]['dated'] = $row['dated'];
					$returnarray['results'][$i]['user'] = $this->string_fancyUsername($row['uid'],NULL,NULL,NULL,NULL,NULL,1);
					$returnarray['results'][$i]['votes-negative'] = $row['negative'];
					$returnarray['results'][$i]['votes-positive'] = $row['positive'];
					$i++;
				}
				//$returnarray['total-comments'] = $this->bool_totalComments($epid);
			}
			else
			{
				$returnarray = array('status' => $this->MessageCodes["Result Codes"]["402"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["04-402"]["Message"]);
			}
		}
		else
		{
			if($whereclause == 0)
			{
				// type was not set, we need that to format data..
				$returnarray = array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["04-400"]["Message"]);
			}
			else if($whereclause == 1)
			{
				// the type was invalid, not supported so far..
				$returnarray = array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["04-401"]["Message"]);
			}
			else if($whereclause == 2)
			{
				// the type was invalid, not supported so far..
				$returnarray = array('status' => $this->MessageCodes["Result Codes"]["403"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["04-403"]["Message"]);
			}
			else if($whereclause == 3)
			{
				// the type was invalid, not supported so far..
				$returnarray = array('status' => $this->MessageCodes["Result Codes"]["404"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["04-404"]["Message"]);
			}
			else if($whereclause == 4)
			{
				// the type was invalid, not supported so far..
				$returnarray = array('status' => $this->MessageCodes["Result Codes"]["404"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["04-404"]["Message"]);
			}
			else
			{
				// nothing else..
			}
		}
		return $returnarray;
	}
	
	public function array_addComment(){
		if(!isset($this->Data['id']) || !is_numeric($this->Data['id']) || !isset($this->Data['comment']) || (isset($this->Data['spoiler']) && !is_numeric($this->Data['spoiler']))){
			// there was data missing.. let them know.
			$returnarray = array('status' => '422', 'message' => 'There is data missing in the request, please try again.');
		}
		else {
			if(!is_numeric($this->Data['type'])){
				$commentType = 0;
			}
			else {
				$commentType = $this->Data['type'];
			}
			
			if($commentType == 0){
				// episode comment
				$pageId = 0;
			}
			elseif($commentType == 1) {
				// profile comment
				$pageId = "u" . $this->Data['id'];
			}
			else {
				$pageId = 0;
			}
			if(isset($this->Data['spoiler'])){
				$spoiler = $this->Data['spoiler'];
			}
			else {
				$spoiler = 0;
			}
			$query = "INSERT INTO `page_comments` (`id`, `comments`, `isSpoiler`, `ip`, `page_id`, `dated`, `uid`, `epid`, `source`) VALUES (NULL, '" . $this->mysqli->real_escape_string($this->Data['comment']) . "', '" . $this->mysqli->real_escape_string($spoiler) . "', '" . $this->mysqli->real_escape_string($_SERVER['REMOTE_ADDR']) . "', '" . $pageId . "', NOW(), '" . $this->UserID . "', '" . $this->mysqli->real_escape_string($this->Data['id']) . "', '" . $this->DevArray['id'] . "')";
			
			$result = $this->mysqli->query($query);
			
			if(!$result){
				// failure
				$returnarray = array('status' => '500', 'message' => 'Something went wrong executing the query, please try again.');
			}
			else {
				if($_SERVER['HTTP_HOST'] == 'www.animeftw.tv'){
					$slackData = "*New Episode Comment Posted by " . $this->string_fancyUsername($this->UserID,NULL,NULL,NULL,NULL,NULL,TRUE,FALSE) . " from the " . $this->DevArray['name'] . "*: \n ```" . $this->Data['comment'] . "``` <https://www.animeftw.tv/manage/#comments>";
					$slack = $this->postToSlack($slackData);
				}
				// success
				$returnarray = array('status' => '200', 'message' => 'Commented added successfully.');
			}
		}
		return $returnarray;
	}
}
