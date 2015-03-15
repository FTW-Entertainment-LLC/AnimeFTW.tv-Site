<div align="center">
<?
if($profileArray[0] == 1){
?>
<form action="#" method="post">
    <input type="hidden" id="epid" value="<?php echo $EpisodeArray[10]; ?>"/>
    <input type="hidden" id="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"/>
    <input type="hidden" id="uid" value="<?php echo $profileArray[1]; ?>"/>
    <textarea id="comment" style="width:650px;height:60px;"></textarea><br />
    Spoiler?&nbsp;<select name="spoiler" id="spoiler">
 			<option value="0" selected="selected">No</option>
 			<option value="1">Yes</option>
            </select>&nbsp;<input type="submit" class="submitcomment" value=" Submit Comment " />
</form>
<div style="font:11px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;padding:2px;">If an episode has ANY issues, please do NOT post a comment about it. <a href="/forums/bug-reports/" target="_blank">Report all Video related issues here.</a></div>
<?
}
else {
	echo '<h3>ERROR In order post a comment you need to <a href="/login">Login</a> or <a href="/register">Register</a> an Account!</h3>';
}
?>
</div>