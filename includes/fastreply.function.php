<?
if($this->profileArray[0] == 1)
{
echo "<div id='QuickReply' style='display: none; position: relative;'>

<br />

<div class='borderwrapsub'>
	<div class='darkrow1' align='center'><img src='/images/forumimages/nav_m.gif' border='0'  alt='&gt;' width='8' height='8' />&nbsp;Fast Reply</div>
	<div style='padding:6px;' align='center' class='row2'>
		<div id='fast-reply-controls' align='center' style='width:100%;padding:6px; margin:0 auto 0 auto' class='rte-buttonbar'>

		<form id='postingform' action='/forums/' method='post' name='REPLIER' enctype='multipart/form-data'>
		<input type='hidden' name='fid' value='$fid' />
		<input type='hidden' name='tid' value='".$this->tid."' />
		<input type='hidden' name='submittitle' value='$ptitle' />
		<input type='hidden' name='pdate' value='".time()."' />
		<input type='hidden' name='puid' value='".$this->profileArray[1]."' />
		<input type='hidden' name='CODE' value='2' />
		<div style='display:none;'>
			<textarea name='text-description' id='text-description'></textarea>
		</div>
	   <div style='padding-bottom:8px' align='left'>";
		echo "<textarea id='submitbox' name='submitbox' cols='100' rows='15'></textarea>";
	   echo "</div>
	   <div>
		    <div style='margin-top:3px'>
				<input type='submit' id='topicreply' name='doreply' value='Post a Reply'  />&nbsp;
				<input type='button' id='topicreply' name='quickreplyclose' onclick='toggle_visibility(\"QuickReply\");' value='Close Fast Reply' class='button' />
			</div>";
echo "
		<script type=\"text/javascript\">
			$(document).ready(function() {";
			if($this->profileArray[2] != 1 && $this->profileArray[2] != 2){
				echo "var buttons = ['formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'image', 'video', 'file', 'table', 'link', '|', 'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule'];";
				echo "$(\"#submitbox\").redactor({buttons: buttons,minHeight: 150});";
			}
			else {
				echo '$("#submitbox").redactor({minHeight: 150});';
			}
		echo "
			});
		</script>
	   </div>
	</form>
	</div>
	</div>
</div><div class='catend'></div>
</div>
";
}
else {
}
?>