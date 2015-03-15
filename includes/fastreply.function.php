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
	   <div style='padding-bottom:8px'>";
		echo "<textarea id='submitbox' name='submitbox' class=\"ckeditor\" cols='100' rows='15'></textarea>";
	   echo "</div>
	   <div>
		    <div style='margin-top:3px'>
				<input type='submit' id='topicreply' name='doreply' value='Post a Reply'  />&nbsp;
				<input type='button' id='topicreply' name='quickreplyclose' onclick='toggle_visibility(\"QuickReply\");' value='Close Fast Reply' class='button' />
			</div>";
echo "<noscript>
			<p>
				<strong>CKEditor requires JavaScript to run</strong>. In a browser with no JavaScript
				support, like yours, you should still see the contents (HTML data) aznd you should
				be able to edit it normally, without a rich editor interface.
			</p>
		</noscript>
		<script type=\"text/javascript\">";
		if($this->profileArray[2] != 1){
			echo "CKEDITOR.replace( 'submitbox', {toolbar : [ ['Font','FontSize','TextColor','BGColor','Bold','Italic','Underline','Strike','-','RemoveFormat','-','Blockquote','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Image','Smiley'] ] });";
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
		echo "</script>
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