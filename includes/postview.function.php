<?php
if ($this->profileArray[0] != 1){
	echo 'Warning you are not logged in, you are not allowed to post unless you are logged in!';
}
else {
	//array($Logged,$globalnonid,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme)
	$treply = @$_GET['treply'];
	$pedit = @$_GET['pedit'];
	$tstart = @$_GET['tstart'];
	/*$tid1 = $_POST['tid'];
	$fid1 = $_POST['fid'];
	$CODE = $_POST['CODE'];
	$pid2 = $_POST['pid'];
	$fid1 = $_POST['fid'];*/
		//codes? 1=new topic, 2=reply, 3=post edit, 4=topic edit
	if (is_numeric($tstart))
	{
		$CODE = 1;
		$query9 = "SELECT fid, fseo, ftitle FROM forums_forum WHERE fid='$tstart'";
		$result9 = mysql_query($query9) or die('Error : ' . mysql_error());
		$row9 = mysql_fetch_array($result9);
		$fid = $row9['fid'];
		$tfid = $fid;
		$fseo = $row9['fseo'];
		$ftitle = $row9['ftitle'];
	echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;<a href='/forums/".$fseo."/'>$ftitle</a>&nbsp;";
	}
	if (is_numeric($treply))
	{
		$CODE = 2;
		$query005 = "SELECT tid, tfid, ttitle, tpid, tdate FROM forums_threads WHERE tid='$treply'";
		$result005 = mysql_query($query005) or die('Error : ' . mysql_error());
		$row005 = mysql_fetch_array($result005);
		$tid = $row005['tid'];
		$tfid = $row005['tfid'];
		$ttitle = $row005['ttitle'];
		$tpid = $row005['tpid'];
		$tdate = $row005['tdate'];
		$ttitle = stripslashes($ttitle);
		$query9 = "SELECT fid, fseo, ftitle FROM forums_forum WHERE fid='$tfid'";
		$result9 = mysql_query($query9) or die('Error : ' . mysql_error());
		$row9 = mysql_fetch_array($result9);
		$fid = $row9['fid'];
		$fseo = $row9['fseo'];
		$ftitle = $row9['ftitle'];
	echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;<a href='/forums/".$fseo."/'>$ftitle</a>&nbsp;";
	echo "&gt;&nbsp;Replying to <a href='/forums/".$fseo."/topic-$treply/s-0'>$ttitle</a></div>";
	}
	else if (is_numeric($pedit))
	{
		$CODE = 3;
		$query00 = "SELECT puid, pfid, ptitle, ptid, puid, pbody FROM forums_post WHERE pid='$pedit'";
		$result00 = mysql_query($query00) or die('Error : ' . mysql_error());
		$row00 = mysql_fetch_array($result00);
		$puid00 = $row00['puid'];
		$tfid = $row00['pfid'];
		$ptitle = $row00['ptitle'];
		$tid = $row00['ptid'];
		$puid = $row00['puid'];
		$pid2 = $pedit;
		$pbody = $row00['pbody'];
		$pbody = stripslashes($pbody);
		$query9 = "SELECT fid, fseo, ftitle FROM forums_forum WHERE fid='$tfid'";
		$result9 = mysql_query($query9) or die('Error : ' . mysql_error());
		$row9 = mysql_fetch_array($result9);
		$fid = $row9['fid'];
		$fseo = $row9['fseo'];
		$ftitle = $row9['ftitle'];
	echo "<div id=\"navstrip\"><img src='/images/forumimages/nav.gif' border='0'  alt='&gt;' />&nbsp;<a href='/forums'>AnimeFTW.TV Forums</a>&nbsp;&gt;&nbsp;<a href='/forums/".$fseo."/'>$ftitle</a>&nbsp;";
	echo "&gt;&nbsp;Editing post from <a href='/forums/find/post-$pid2'>$ptitle</a></div>";
	
	}
	else if ($CODE == 4)
	{
	echo "&gt;&nbsp;Editing thread <a href='/forums/index.php?forum=$fid&amp;thread=$tid1&amp;s=0'>$ttitle</a></div>";
	$query00 = "SELECT pbody FROM forums_post WHERE pid='$pid2'";
		$result00 = mysql_query($query00) or die('Error : ' . mysql_error());
		$row00 = mysql_fetch_array($result00);
		$tbody = $row00['pbody'];
		$tbody = stripslashes($tbody);
	}
	else {
	}
	echo "
		<div id='ipbwrapper'>";
		echo "<form id='postingform' action='/forums?' method='post' name='REPLIER' enctype='multipart/form-data'>	
		<input type='hidden' name='fid' value='$tfid' />
		<input type='hidden' name='tid' value='".@$tid."' />
		<input type='hidden' name='pid' value='".@$pid2."' />
		<input type='hidden' name='submittitle' value='".@$ttitle."' />
		<input type='hidden' name='pdate' value='".time()."' />
		<input type='hidden' name='puid' value='".$this->profileArray[1]."' />\n";
		echo "<input type='hidden' name='CODE' value='".$CODE."' /><br />		
		<div style='display:none;'>
			<textarea name='text-description' id='text-description'></textarea>
		</div>
		<table border='0' width='100%' cellspacing='0' cellpadding='0'>
		  
		</table><div class='borderwrap'>
			
			<div style='padding:0px 1px 0px 1px' class='bg1'>
		
		<table cellspacing='0'  width='100%' style='padding:3px'>
		<tr>
		 <td colspan='2' align='center' class='bg1'>
		 <div align='center' class='borderwrap'>
		 <table cellpadding='0' cellspacing='0' width='100%' class='darkrow3' style='padding:5px;'>
		  <tr>
		   <td align='center' valign='top' width='100%'>";
		   if (is_numeric($tstart))
		   {
			   $CODE = 1;
		   echo "Thread Title:<br />
		   <input id='submittitle' name='submittitle' type='text' style='background-color:#0C90BB;color:#CCCCCC;' size='50' />";
		   }
		   else {
		   }
		   if ($CODE == 3) { 
		   		if($puid == $this->profileArray[1] || $this->profileArray[2] == 1 || $this->profileArray[2] == 2){
		  			echo "<textarea id='submitbox' name='submitbox'  cols='100' rows='15' class=\"ckeditor\">" . $pbody . "</textarea>";
				}
				else {
				echo "<div class='errorwrap'>
							<h4>The error returned was:</h4>
							<p>Sorry, the link that brought you to this page seems to be out of date or broken.</p>
					</div><br />";
				}
			}
			else if ($CODE == 4){ 
				echo "<textarea id='submitbox' name='submitbox' class=\"ckeditor\" cols='100' rows='15'>" . $tbody . "</textarea>";
			}
			else {
				echo "<textarea id='submitbox' name='submitbox' class=\"ckeditor\" cols='100' rows='15'></textarea>";
			}
		   echo "</td>
		  </tr>
		 </table>
		 </div>
		 </td>
		</tr>
		</table>";
		if ($this->profileArray[2] == 1 || $this->profileArray[2] == 2 || $this->profileArray[2] == 6)
		{
			echo "<table width='100%'>
			<tr>
			<td align=\"right\" width=\"11%\">
 <b>Post Options</b>
</td>
<td align=\"left\" width=\"89%\">
	&nbsp;
<select name=\"post_htmlstatus\" class=\"dropdown\">
<option value=\"0\" selected=\"selected\">HTML Off</option>
<option value=\"1\">HTML On</option>
<option value=\"2\">HTML On - Auto Linebreak Mode</option>
			</select>
			</td>
			</tr>
			<tr>
<td colspan='2'>&nbsp;</td>
			</tr>
			";
			if ($CODE == 1 || $CODE == 2)
			{
			echo "
			<tr>
			<td width='11%' align='right'>
			<b>Post Options</b>
			</td>
			<td width='89%' align='left'>
			<select id=\"forminput\" name=\"mod_options\" class=\"forminput\">
			<option value=\"nowt\">( Do Nothing )</option>
			<option value=\"pin\">Pin this topic</option>
			<option value=\"unpin\">Unpin this topic</option>
			<option value=\"close\">Close this topic</option>
			<option value=\"pin&close\">Pin &amp; Close this topic</option>
			<option value=\"upinclose\">Unpin &amp; Close this topic</option>
			<!--<option value=\"move\">Move this topic</option>-->
			</select>
			</td>
			</tr>";
			}
			echo "</table>";
		}
		else {
			echo "<input type='hidden' name='mod_options' value='nowt' />";
		}
		echo "</div>
<div align='center'>";
	if($CODE == 1){echo "<input type='submit' name='doreply' value='Add Topic' id='topicreply' class='SubmitForm' />&nbsp;";}
	else if($CODE == 2){echo "<input type='submit' name='doreply' value='Add Reply' id='topicreply' />&nbsp;";}
	else if($CODE == 3){echo "<input type='submit' name='doreply' value='Edit Post' id='topicreply' class='SubmitForm' />&nbsp;";}
	else if($CODE == 4){echo "<input type='submit' name='doreply' value='Edit Topic' id='topicreply' class='SubmitForm' />&nbsp;";}
	echo "<input type='submit' name='preview' value='Preview Post' id='topicreply' disabled />
</div>";
		echo "</div>";
		echo "</form><br /><br />";
	echo '<script>
		$(document).ready(function() {
			$(".SubmitForm").click(function() {
				if($("#submittitle").val().length == 0)
				{
					alert("A topic title is required before you can submit a topic.");
					return false;
				}
			});
		});
	</script>';
		if($CODE == 2){
			echo "
		<b>Last 10 Posts</b>
		<div style=\"height:300px;overflow-y:scroll;overflow-x:none;\">";
				$query = "SELECT puid, pdate, pbody FROM forums_post WHERE ptid='".$tid."' ORDER BY pdate DESC LIMIT 0, 10";
				//echo 'Query: '.$query.'<br /> Start: '.$this->start.', tid: '.$this->tid.', fseo: '.$this->fseo.', paging: '.$this->paging;
				$result001 = mysql_query($query) or die('Error : ' . mysql_error());
				$i = 0;
				echo "<table>";
				while(list($puid,$pdate,$pbody) = mysql_fetch_array($result001)){
					$pbody = stripslashes($pbody);
					$pdate = $this->timeZoneChange($pdate,$this->profileArray[3]);
					$pdate = date("M j Y, h:i A",$pdate);	
					if($i % 2){$class = " class=\"oddrow\"";}
					else {$class = " class=\"evenrow\"";}
					echo "<tr".$class.">";
					echo "<td>
					<div style=\"padding:5px;\">
					<div style=\"float:left;vertical-align:top;padding-bottom:5px;\">".$this->formatUsername($puid)."<br /><span style=\"font-size:9px;\">Posted:<br /><i> $pdate</i></span></div>
					<div style=\"float:right;width:800px;padding-left:10px;padding-bottom:5px;vertical-align:top;\">$pbody</div>
					</div>
					</td>";
					//echo "<td width=\"15%\" valign=\"top\">".checkUserNameNumber($puid)."<br /><span style=\"font-size:9px;\">Posted:<br /><i> $pdate</i></span></td>";
					//echo "<td>$pbody</td>";
					echo "</tr>";
					$i++;
				}
				echo "</table>";
			}
		echo "</div>";
		echo "</div>";
		echo "<noscript>
			<p>
				<strong>CKEditor requires JavaScript to run</strong>. In a browser with no JavaScript
				support, like yours, you should still see the contents (HTML data) aznd you should
				be able to edit it normally, without a rich editor interface.
			</p>
		</noscript>
		<script type=\"text/javascript\">";
		if($this->profileArray[2] != 1 && $this->profileArray[2] != 2){
			echo "CKEDITOR.replace( 'submitbox', {toolbar : [ ['Link','Font','FontSize','TextColor','BGColor','Bold','Italic','Underline','Strike','-','RemoveFormat','-','Blockquote','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Image','Smiley'] ] });";
		}
		else {
			echo "CKEDITOR.replace( 'submitbox', {toolbar : 
			{ name: 'document',		items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
			{ name: 'clipboard',	items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
			{ name: 'editing',		items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
			{ name: 'forms',		items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
			'/',
			{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
			{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
			{ name: 'links',		items : [ 'Link','Unlink','Anchor' ] },
			{ name: 'insert',		items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
			'/',
			{ name: 'styles',		items : [ 'Styles','Format','Font','FontSize' ] },
			{ name: 'colors',		items : [ 'TextColor','BGColor' ] },
			{ name: 'tools',		items : [ 'Maximize', 'ShowBlocks','-','About' ] }
		}";
		}
		echo "</script>";
		}
	