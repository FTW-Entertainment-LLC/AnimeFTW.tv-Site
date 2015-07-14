<?php
/****************************************************************\
## FileName: comments.class.php								 
## Author: Brad Riemann								 
## Usage: Comments sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Comments extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		echo '<div  class="body-container">';
		$this->buildMainPage();
		echo '</div>';
	}
	
	private function buildMainPage()
	{
		if($this->ValidatePermission(10) == TRUE)
		{
			if(!isset($_GET['do']))
			{
				if(!isset($_GET['mode']) && $this->ValidatePermission(11) == TRUE)
				{
					$query = "SELECT page_comments.id, page_comments.comments, page_comments.ip, page_comments.dated, page_comments.epid, page_comments.uid, episode.epnumber, series.seriesname, series.seoname, series.fullSeriesName FROM page_comments, episode, series WHERE page_comments.type = 0 AND episode.id=page_comments.epid AND series.id=episode.sid ORDER by page_comments.id DESC LIMIT 0, 60";
					
					$result = mysql_query($query);
					$count = mysql_num_rows($result); 
					if ($count>0) 
					{
						echo '<div class="table-wrapper" style="width:100%;">';
						echo '	<div class="table-row-header" style="padding:5px;font-weight:bold;">';
						echo '		<div class="table-row-column" style="display:inline-block;width:255px;">Series</div>';
						echo '		<div class="table-row-column" style="display:inline-block;width:30px;">Ep #</div>';
						echo '		<div class="table-row-column" style="display:inline-block;width:125px;">Username</div>';
						echo '		<div class="table-row-column" style="display:inline-block;width:340px;">Comment</div>';
						echo '		<div class="table-row-column" style="display:inline-block;width:60px;">Dated</div>';
						echo '		<div class="table-row-column" style="display:inline-block;width:20px;">Actions</div>';
						echo '	</div>';
						$i = 0;
						while($row = mysql_fetch_assoc($result))
						{
							if($i % 2)
							{
								$rowstyle = '';
							}
							else
							{
								$rowstyle = 'background-color:#B8EAFA;';
							}
							
							$comment_id = $row['id']; 
							$comments = $row['comments'];
							$when = explode(" ",$row['dated']);
					
							// do you want to look - if there's an admin comment or long comment ...
							$see_me = "";
							if(strlen($myrow['comments'])>60)
							{
								$see_me = 1;
							}
							
							echo '	<div class="table-row" style="padding-top:2px;' . $rowstyle . '" id="comment-' . $comment_id . '">';
							echo '		<div class="table-row-column" style="display:inline-block;width:255px;vertical-align:top;word-wrap:break-word;"><a href="/anime/' . $row['seoname'] . '/" target="_blank">' . $row['fullSeriesName'] . '</a></div>';
							echo '		<div class="table-row-column" style="display:inline-block;width:30px;vertical-align:top;word-wrap:break-word;"><a href="/anime/' . $row['seoname'] . '/ep-' . $row['epnumber'] . '" target="_blank">' . $row['epnumber'] . '</a></div>';
							echo '		<div class="table-row-column" style="display:inline-block;width:125px;vertical-align:top;word-wrap:break-word;">'  . $this->formatUsername($row['uid'],"blank") . '</div>';
							echo '		<div class="table-row-column" style="display:inline-block;width:340px;vertical-align:top;word-wrap:break-word;">' . stripslashes($comments) . '</div>';
							echo '		<div class="table-row-column" style="display:inline-block;width:60px;vertical-align:top;"><span title="Posted at ' . $when[1] . '">' . $when[0] . '</span></div>';
							echo '		<div class="table-row-column" style="display:inline-block;width:50px;vertical-align:top;">';
							if($this->ValidatePermission(11) == TRUE)
							{
								if ($see_me == 1)
								{
									echo "<a href=\"#\"><img src='http://www.animeftw.tv/images/editor/magnify3.gif' width='16' height='15' alt='show in full' border='0'/></a>";
								}
								else
								{
									echo "&nbsp;";
								}
							}
							else 
							{
								echo '&nbsp;';
							}
							if($this->ValidatePermission(12) == TRUE)
							{
								echo " <a class=\"edit-comment\" href=\"#\" style=\"padding-left:2px;\" id=\"edit-" . $comment_id . "\">";
								echo "<img src='" . $this->Host . "/management/settings-icon.png' width='16' height='15' alt='edit' border='0' />";
								echo "</a>";
							}
							else
							{
								echo '&nbsp;';
							}
							if($this->ValidatePermission(13) == TRUE)
							{
								echo "<a class=\"delete-comment\" href=\"#\" style=\"padding-left:2px;\" id=\"delete-" . $comment_id . "\">";
								echo "<img src='" . $this->Host . "/management/delete-icon.png' width='16' height='15' alt='delete' border='0' />";
								echo "</a>";
							}
							else 
							{
								echo '&nbsp;';
							}
							echo '		</div>';
							echo '	</div>';
							$i++;
						}
						echo '
						<script>
						$(document).ready(function(){
							$(".delete-comment").on("click", function() {
								var this_id = $(this).attr("id").substring(7);
								var r=confirm("Are you sure you want to delete this comment?");
								if (r==true)
								{
									$.ajax({
										type: "GET",
										url: "ajax.php",
										data: "node=comments&do=delete&id=" + this_id,
										success: function(html) {
											if(html.indexOf("Success") >= 0)
											{
												$("#comment-" + this_id).css("background-color", "red").css("color","white").fadeOut();
											}
											else
											{
												alert("There was an error processing that request: " + html);
											}
										}
									});
									return false;
								
								
									// Delete the row
									//$.ajax({
									//	url: "ajax.php?node=comments&do=delete&id=" + this_id,
									//	cache: false
									//});
								}
								else
								{
								}
								return false;
							});
							$(".edit-comment").on("click", function() {
							});
						});
						</script>';
					}
				}
				else {
				}
			}
			else {
				if($_GET['do'] == 'edit' && $this->ValidatePermission(12) == TRUE)
				{
				}
				else if($_GET['do'] == 'delete' && $this->ValidatePermission(13) == TRUE)
				{
					if(!isset($_GET['id']) && !is_numeric($_GET['id']))
					{
						echo 'Error: Invalid ID entered.';
					}
					else
					{
						$query  = "DELETE FROM `page_comments` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'";
						mysql_query($query) or die('Error : ' . mysql_error());
						$this->ModRecord('Delete Comment');
						echo 'Success';
					}
				}
				else if($_GET['do'] == 'view' && $this->ValidatePermission(11) == TRUE){
				}
				else {
				}
			}
		}
	}
	
}

?>