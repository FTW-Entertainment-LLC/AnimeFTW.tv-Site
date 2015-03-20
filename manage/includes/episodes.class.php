<?php
/****************************************************************\
## FileName: episodes.class.php								 
## Author: Brad Riemann								 
## Usage: Episodes sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Episodes extends Config {

	public function __construct()
	{
		parent::__construct();
		echo '<div  class="body-container srow">';
		$this->deployEpisodes();
		echo '</div>';
	}
	
	private function deployEpisodes()
	{
		// set the global variables to this function
		$limit = 60; //limit to 30 rows
		$link = 'ajax.php?node=episodes'; //we needed a link, cause were awesome.
		if(isset($_GET['sname']))
		{
			$link .= '&sname=' . $_GET['sname'];
		}
		if(isset($_GET['page']) && $_GET['page'] == 'edit')
		{
			if(isset($_GET['eid']))
			{
				$this->episodeForm($link,"edit");
			}
			else
			{
				echo 'Error. NO FOOD FOR YOU!@#';
			}
		}
		else if(isset($_GET['page']) && $_GET['page'] == 'add')
		{
			$this->episodeForm($link);
		}
		else if(isset($_GET['page']) && $_GET['page'] == 'delete')
		{
			$this->deleteEpisode();
		}
		else if(isset($_GET['page']) && $_GET['page'] == 'image-add')
		{
			$this->addVideoImage();
		}
		else
		{
			//if(isset($_GET['sname']))
			//{
			//	$link .= "&sname=".$_GET['sname'];
			//}
			//some small variable sets for this part of the function
			$rowcount = mysql_num_rows(mysql_query("SELECT id FROM episode")); //grab the total amount of episodes!
			if(isset($_GET['page']))
			{
				$start = $_GET['page'];
			}
			else
			{
				$start = 0;
			} //clean start points, cause everyone loves them
			if(isset($_GET['sname']))
			{
				$sname = htmlentities($_GET['sname']);
				$query = "SELECT episode.id, episode.epnumber, episode.epname, episode.image, series.fullSeriesName, series.seoname FROM episode, series WHERE series.id = '" . mysql_real_escape_string($sname) . "' AND episode.sid = '" . mysql_real_escape_string($sname) . "' ORDER BY id DESC LIMIT ".$start.", ".$limit;
			}
			else 
			{
				$query = "SELECT episode.id, episode.epnumber, episode.epname, episode.image, series.fullSeriesName, series.seoname FROM episode, series WHERE series.id=episode.sid ORDER BY id DESC LIMIT ".$start.", ".$limit;
			}
			$result = mysql_query($query);
			$count = mysql_num_rows($result); 
			echo '<div style="padding-top:5px;">';
			echo '<div style="float:right;padding-right:80px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&page=add\'); return false;"><b>Add Episode</b></a></div>';
			$this->pagingV1('right-column',$rowcount,$limit,$start,$link); //($count,$perpage,$start,$link)
			echo '</div>';
			//begin!
			echo '<div class="Results" id="Results" style="display:none;"></div>';
			echo '<div class="erow">
					<div class="eleftcol" align="center" style="display:inline-block;width:400px;">
						<b>Episode Title</b>
					</div>
					<div class="eepcol" align="center" style="display:inline-block;width:40px;">
						<b>Ep#</b>
					</div>
					<div class="eseriescol" align="center" style="display:inline-block;width:300px;">
						<b>Series</b>
					</div>
					<div style="display:inline-block;width:100px;">
						<b>Functions</b>
					</div>
				</div>';
			$i = 0;
			$style1 = 'style="background-color:#E5E5E5;padding:2px;"';
			$style2 = 'style="background-color:#B8EAFA;padding:2px;"';
			while($r = mysql_fetch_array($result))
			{
				if($i % 2)
				{
					$style = $style1;
				}
				else
				{
					$style = $style2;
				}
								
				echo '<div class="erow" ' . $style . ' id="episode-entry-' . $r['id'] . '">';
				echo '<div class="eleftcol"  style="display:inline-block;width:400px;">'.$r['epname'].'</div>';
				echo '<div class="eepcol" align="center"  style="display:inline-block;width:40px;">'.$r['epnumber'].'</div>';
				echo '<div class="eseriescol" align="center"  style="display:inline-block;width:300px;">'.$r['fullSeriesName'].'</div>';
				echo '<div class="eactioncol" align="center" style="display:inline-block;width:100px;">
					<a href="#" onClick="$(\'#right-column\').load(\''.$link.'&page=edit&eid='.$r['id'].'\'); return false;" title="Edit this Episode">
						<img src="' . $this->Host . '/management/settings-icon.png" height="11px" alt="" />
					</a>
					&nbsp; <a href="#" onClick="$(\'#Results\').load(\''.$link.'&page=image-add&epid='.$r['id'].'&point=after\'); return false;"><img src="' . $this->Host . '/management/redo-image-icon.png" title="Redo the episode Image" alt="IC" height="11px" /></a>
					&nbsp; <a href="#" onClick="return false;" class="html5-function" id="html5-' . $r['id'] . '"><img src="' . $this->Host . '/management/redo-video-icon.png" title="Redo the episode MP4" alt="IC" height="11px" /></a> 
					&nbsp; 
					<a href="#" class="episode-delete" id="episode-' . $r['id'] . '">
						<img src="' . $this->Host . '/management/delete-icon.png" alt="" title="Delete this episode" height="11px" />
					</a>
					</div>';
				echo '</div>';
				$i++;
			}
			echo '
			<script>
			$(document).ready(function(){
				$(".html5-function").on("click", function() {
					var this_id = $(this).attr("id").substring(6);
					alert("Function coming soon..");					
				});
				$(".episode-delete").on("click", function() {
					var this_id = $(this).attr("id").substring(8);
					var r=confirm("WARNING! WARNING! WARNING! This is an ACTIVE function, delete only if you NEED to.");
					if (r==true)
					{
						// Delete the row
						$.ajax({
							url: "ajax.php?node=episodes&page=delete&id=" + this_id,
							cache: false
						});
						//$(\'#uploads-global-wrapper\').load(\'ajax.php?node=uploads&subpage=home\');
						$("#episode-entry-" + this_id).css("background-color", "red").css("color","white").fadeOut();
					}
					else
					{
					}
					return false;
				});
			});
			</script>';
		}
	}
	
	private function episodeForm($link,$Type = NULL)
	{
		if($Type == 'edit')
		{
			if(!isset($_GET['eid']))
			{
				$id = ''; $sid = ''; $epnumber = ''; $seriesName = ''; $epname = ''; $vidheight = ''; $vidwidth = ''; $epprefix = ''; $subGroup = ''; $Movie = ''; $videotype = 'mp4'; $uesid = ''; $hd = 0; $html5 = '';
				$options = '';
				$FormMethod = '<input type="hidden" value="AddEpisode" name="method" />'; // Since the ID isn't valid we need give the add form
				$SubmitTXT = " Add Episode ";
			}
			else
			{
				$query  = "SELECT `id`, `sid`, `epnumber`, `seriesname`, `epname`, `vidheight`, `vidwidth`, `epprefix`, `subGroup`, `Movie`, `videotype`, `hd`, `html5` FROM `episode` WHERE id= '" . mysql_real_escape_string($_GET['eid']) . "'";
				$result = mysql_query($query) or die('Error : ' . mysql_error());
				list($id, $sid, $epnumber, $seriesName, $epname, $vidheight, $vidwidth, $epprefix, $subGroup, $Movie, $videotype, $hd, $html5) = mysql_fetch_array($result, MYSQL_NUM);
				$FormMethod = '<input type="hidden" value="EditEpisode" name="method" />';
				$options = '<input type="hidden" name="id" value="' . $id . '" />';
				$SubmitTXT = " Edit Episode ";
				$uesid = '';
			}	
			// we override the default as an edit.
		}
		else
		{
			if(isset($_GET['ueid']))
			{
				$query = mysql_query("SELECT `type`, `fansub`, `sid`, `resolution`, `prefix`, `anidbsid` FROM `uestatus` WHERE `id` = " . mysql_real_escape_string($_GET['ueid']));
				$row = mysql_fetch_assoc($query);
				$query = mysql_query("SELECT `episode`.`sid`, `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`videotype`, `episode`.`vidwidth`, `episode`.`hd`, `episode`.`html5`, series.seriesname FROM episode, series WHERE episode.sid=series.id AND series.id = " . $row['sid'] . " ORDER BY epnumber DESC LIMIT 0, 1");
				$row2 = mysql_fetch_assoc($query);
				$id = ''; 
				$epnumber = $row2['epnumber']+1; 
				
				// ADDED: 7/16/2014
				// Allows for first episodes to be populated correctly.
				if($epnumber == 1)
				{ 
					$epname = '';
					$dimmensions = explode("x",$row['resolution']);
					$vidheight = $dimmensions[1]; 
					$vidwidth = $dimmensions[0];
					$epprefix = $row['prefix']; 
				}
				else
				{
					// normal episode.. carry on.
					$epname = ''; 
					$vidheight = $row2['vidheight']; 
					$vidwidth = $row2['vidwidth'];
					$epprefix = $row2['epprefix']; 
				}
				$sid = $row2['sid']; 
				$seriesName = $row2['seriesname']; 
				$subGroup = $row['fansub']; 
				$aniDBid = $row['anidbsid']; 
				$Movie = ''; 
				$videotype = $row2['videotype'];
				$uesid = $row['sid'];
				$hd = $row2['hd'];
				$html5 = $row2['html5'];
			}
			else
			{
				$id = ''; $sid = ''; $epnumber = ''; $seriesName = ''; $epname = ''; $vidheight = ''; $vidwidth = ''; $epprefix = ''; $subGroup = ''; $Movie = ''; $videotype = 'mp4'; $uesid = ''; $hd = 0; $html5 = '';
			}
			// default to adding an episode
			$FormMethod = '<input type="hidden" value="AddEpisode" name="method" />';
			$SubmitTXT = " Add Episode(s) ";
			$options = '';
			$Type = 'add';
		}
		echo '<div class="body-message">Update: If you enter the anidb and episode fields, you won\'t have to enter anything in the "Episode #" and "Episode Name" fields. The same goes the other way around.<br>
		Keep in mind that this does not add the special episodes on AniDB.<br></div>
		<div id="form_results" class="form_results">&nbsp;</div>';
		echo '
		
		<form method="POST" action="#" id="EpisodeForm">
		' . $FormMethod . '
		' . $options . '
		<input type="hidden" name="uid" value="' . $this->UserArray[1] . '" />
		<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
		<div class="series-form-row" style="border: 1px solid #eeebea;border-bottom: none;">
			<div class="series-form-left">
				Anidb ID
			</div>
			<div class="series-form-right">
				<input name="anidbid" id="anidbidnum" type="text" size="25" value="' . $aniDBid . '" class="text-input2" />
				<label for="anidbid" id="anidbError" class="form-labels FormError">AniDB ID is Required</label>
			</div>
		</div>
		<div class="series-form-row" style="border-left: 1px solid #eeebea;border-right: 1px solid #eeebea;">
			<div class="series-form-left">
				Episodes
			</div>
			<div class="series-form-right">
				From:
				<input name="fromep" id="addfromnum" type="number" value="' . $epnumber . '" class="text-input2" style="width:73px;"/>
				<label for="fromep" id="addfromnumError" class="form-labels FormError">Starting Value is Required</label>
				To:
				<input name="toep" id="addtonum" type="number" value="' . $epnumber . '" class="text-input2" style="width:74px;"/>
				<label for="toep" id="addtonumError" class="form-labels FormError">End Value is Required</label>
			</div>
		</div>
		<div class="series-form-row" style="border-left: 1px solid #eeebea;border-right: 1px solid #eeebea;">
			<div class="series-form-left">
			</div>
			<div class="series-form-right">
				<span style="display: block;width: 224px;text-align: center;">OR</span>
			</div>
		</div>
		<div class="series-form-row" style="border-left: 1px solid #eeebea;border-right: 1px solid #eeebea;">
			<div class="series-form-left">
				Episode #
			</div>
			<div class="series-form-right">
				<input name="epnumber" id="epnumber" type="text" size="25" value="" class="text-input2" />
				<label for="epnumber" id="epnumberError" class="form-labels FormError">Episode Number is Required</label>
			</div>
		</div>
		<div class="series-form-row" style="border: 1px solid #eeebea;border-top: none">
			<div class="series-form-left">
				Episode Name
			</div>
			<div class="series-form-right">
				<input name="epname" id="epname" type="text" size="25" value="" class="text-input2" />
				<label for="epname" id="epnameError" class="form-labels FormError">An episode Name is required</label>
			</div>
		</div>
		<div class="series-form-row" >
			<div class="series-form-left">
				Video Width
			</div>
			<div class="series-form-right">
				<input name="vidwidth" id="vidwidth" type="text" size="25" value="' . $vidwidth . '" class="text-input2" />
				<label for="vidwidth" id="vidwidthError" class="form-labels FormError">A Video Width is required.</label>
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Video Height
			</div>
			<div class="series-form-right">
				<input name="vidheight" id="vidheight" type="text" size="25" value="' . $vidheight . '" class="text-input2" />
				<label for="vidheight" id="vidheightError" class="form-labels FormError">A Video Height is required.</label>
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Fansub Group
			</div>
			<div class="series-form-right">
				<input name="subGroup" type="text" size="25" value="' . $subGroup . '" class="text-input2" />
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Episode Prefix
			</div>
			<div class="series-form-right">
				<input name="epprefix" id="epprefix" type="text" size="25" value="' . $epprefix . '" class="text-input2" />
				<label for="epprefix" id="epprefixError" class="form-labels FormError">An Episode Prefix is required.</label>
				
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Series Name
			</div>
			<div class="series-form-right">
				<select name="sid" class="text-input2" id="sid" style="width: 550px;">
				<option value="0">-Choose Series-</option>';
				$this->SeriesList($sid, $uesid);
			echo '
				</select>
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Movie?
			</div>
			<div class="series-form-right">
				<select name="Movie" class="text-input2">
					<option value="0"'; if($Movie == 0){echo ' selected="selected"';} echo '>No</option>
					<option value="1"'; if($Movie == 1){echo ' selected="selected"';} echo '>Yes</option>
				</select>
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Video Format
			</div>
			<div class="series-form-right">
				<select name="videotype" class="text-input2">
					<option value="divx"'; if($videotype == 'divx'){echo ' selected="selected"';} echo'>DivX</option>
					<option value="mkv"'; if(($videotype == 'mkv')||!isset($Remember)){echo ' selected="selected"';} echo'>MKV</option>
					<option value="mp4"'; if($videotype == 'mp4'){echo ' selected="selected"';} echo'>MP4</option>
				</select>
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
				Silent Episode?
			</div>
			<div class="series-form-right">
				<select name="date" class="text-input2">';
					
					//These $addtime variables doesn't seem to exist, so they always return a underfined variable notice. Doesn't seem to be needed anyway but I'm not sure if I should remove them. /Hani
					echo '
					<option value="1"'; if($addtime == '1'){echo ' selected="selected"';} echo'>No</option>
					<option value="0"'; if($addtime == '0'){echo ' selected="selected"';} echo'>Yes</option>
				</select>
			</div>
		</div>';
		if($Type == 'edit')
		{
			echo '
			<div class="series-form-row">
				<div class="series-form-left">
					HTML5 Episode?
				</div>
				<div class="series-form-right">
					<select name="html5" class="text-input2" id="html5-select">
						<option value="0"'; if($html5 == '0'){echo ' selected="selected"';} echo'>No</option>
						<option value="1"'; if($html5 == '1'){echo ' selected="selected"';} echo'>Yes</option>
					</select>
					<label for="html5" id="html5Error" class="form-labels FormError">You MUST set the HTML5 to enabled for 720p/1080p to work, please address.</label>
				</div>
			</div>';
		}
		else
		{
			echo '<input type="hidden" name="html5" value="0" />';
		}
		echo '
		<div class="series-form-row">
			<div class="series-form-left">
				HD Episodes
			</div>
			<div class="series-form-right">
				<select name="hd" class="text-input2" id="hd-select">
					<option value="0"'; if($hd == '0'){echo ' selected="selected"';} echo'>480p Only</option>
					<option value="1"'; if($hd == '1'){echo ' selected="selected"';} echo'>480p/720p</option>
					<option value="2"'; if($hd == '2'){echo ' selected="selected"';} echo'>480p/720p/1080p</option>
				</select>
			</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
			</div>
			<div class="series-form-right">
				<div>
					<input type="checkbox" name="Remember" id="Remember" class="text-input2" />
					<label for="Remember">Remember certain fields?</label>&nbsp;&nbsp;
				</div>';
				if(isset($_GET['ueid']))
				{
					// if the upload entry is set, we need to give the option to remove the notification mark from the entry.
					echo '
					<div>
						<input type="checkbox" name="Changed" id="Changed" class="text-input2" />
						<label for="Changed">Remove the Notification in the Uploads Board?</label>&nbsp;&nbsp;
					</div>
					<input type="hidden" name="ueid" value="' . $_GET['ueid'] . '" />';
				}
			echo '</div>
		</div>
		<div class="series-form-row">
			<div class="series-form-left">
			</div>
			<div class="series-form-right">';			
				echo '<input type="submit" class="SubmitForm" id="submit" name="submit" value="' . $SubmitTXT . '">&nbsp;';
				if(isset($_GET['ueid']))
				{
					$BackLink = '$(\'#right-column\').load(\'ajax.php?node=uploads\'); return false;';
				}
				else
				{
					$BackLink = '$(\'#right-column\').load(\''.$link.'\'); return false;';
				}
				echo '<input type="button" value="Go Back" name="back" id="back" onClick="' . $BackLink. '">';
			echo '</div>
		</div>
		</form>';
		echo '<script>
			$(function() {
				$(\'.form_results\').hide();
				$(\'.text-input\')
					.css({border:"1px solid #CCC"})
					.css({color:"#5A5655"})
					.css({font:"13px Verdana,Arial,Helvetica,sans-serif"})
					.css({padding:"2px"})
				;
				$(\'.text-input\').focus(function(){
					$(this).css({border:"1px solid #0C90BB"});
				});
				$(\'.text-input\').blur(function(){
					$(this).css({border:"1px solid #CCC"});
				});
				$(".SubmitForm").click(function() {
					$(\'.form-labels\').hide();
					var epnumber = $("input#epnumber").val();
					if(epnumber==""){ //If epnumber is null, then they want to use the automatic function. so we check for errors there.
						var anidbid = $("input#anidbidnum").val();
						if (anidbid == "") {
							$("label#anidbError").show();
							$("input#anidbidnum").focus();
							return false;
						}
						var fromep = $("input#addfromnum").val();
						if (fromep == "") {
							$("label#addfromnumError").show();
							$("input#addfromnum").focus();
							return false;
						}
						var toep = $("input#addtonum").val();
						if (toep == "") {
							$("label#addtonumError").show();
							$("input#addtonum").focus();
							return false;
						}
					}
					if(anidbid == ""&&fromep == ""&&toep == ""){ //Show error for epnumber only if these 3 values are null.
					//If these 3 values are set, it means the user wants to use the automatic function. If not, they want to use the manual.
						if (epnumber == "") {
							$("label#epnumberError").show();
							$("input#epnumber").focus();
							return false;
						}
						var epname = $("input#epname").val();
						if (epname == "") {
							$("label#epnameError").show();
							$("input#epname").focus();
							return false;
						}
					}
					
					var vidwidth = $("input#vidwidth").val();
					if (vidwidth == "") {
						$("label#vidwidthError").show();
						$("input#vidwidth").focus();
						return false;
					}
					var vidheight = $("input#vidheight").val();
					if (vidheight == "") {
						$("label#vidheightError").show();
						$("input#vidheight").focus();
						return false;
					}
					var epprefix = $("input#epprefix").val();
					if (epprefix == "") {
						$("label#epprefixError").show();
						$("input#epprefix").focus();
						return false;
					}
					var sid = $("select#sid").val();
					if (sid == "0") {
						$("label#sidError").show();
						$("select#sid").focus();
						return false;
					}
					';
					if($Type == 'edit')
					{
						echo '
						var hd = $("#hd-select").val();
						var html5 = $("#html5-select").val();
						if ((hd == "1" || hd == "2") && html5 == "0")
						{
							$("label#html5Error").show();
							$("#html5-select").focus();
							return false;
						}';
					}
					echo '
					$.ajax({
						type: "POST",
						url: "ajax.php",';
						echo 'data: $(\'#EpisodeForm\').serialize(),';
						
						echo '
						success: function(html) {
							if(html.indexOf("Success") >= 0){
					';
					if($Type == 'add')
					{
						echo '
								if($(\'#Remember\').is(":checked")&&$("input#anidbidnum").val() == "")
								{
									var epnum = parseInt($("#epnumber").val());
									var epnum2 = epnum+1;
									$("#epnumber").val(epnum2);	
									$("#epname").val("");									
								}
								else if($(\'#Remember\').is(":checked")){
									
								}
								else
								{
									// reset the form
									$("#EpisodeForm")[0].reset();
								}
						';
					}
						echo '
								$(\'.form_results\').slideDown().html(html);											
								$(\'.form_results\').delay(8000).slideUp();
							}
							else{
								$(\'.form_results\').slideDown().html(html);
							}
						}
					});
					return false;
				
				});
			});
		</script>';
	}
	private function SeriesList($sid, $uesid){
		$query2 = "SELECT id, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
		$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
		while(list($id,$fullSeriesName) = mysql_fetch_array($result2, MYSQL_NUM))
		{
			$fullSeriesName = stripslashes($fullSeriesName);
			if(($id == $sid) || (isset($_GET['preseriesname']) && $_GET['preseriesname'] == $id) || ($uesid == $id))
			{ 
				echo '<option value="'.$id.'" selected="selected">'.$fullSeriesName.'</option> ';
			}
			else {
			}
			echo '<option value="'.$id.'">'.$fullSeriesName.'</option> ';
		}
	}
	private function deleteEpisode()
	{
		if(!isset($_GET['id']) || !is_numeric($_GET['id']))
		{
			echo 'Invalid';
		}
		else
		{
			// first thing first, check to make sure no one is abusing the delte functions.. We wont actually do anything.. just alert the admins to someone deleting multiples of stuff..
			$query = "SELECT * FROM `modlogs` WHERE `script` LIKE 'Delete Episode%' AND `date` >= " . (time()-(60*15));
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			
			// we need to count the rows, if there are more than 15 deletions in 15 minutes we need to send an email..
			if($count > 15)
			{
				// uh oh... Send out the email with the logs
			}
			else
			{
				// continue on..
			}
			
			$query = "DELETE FROM `episode` WHERE `id` = " . mysql_real_escape_string($_GET['id']);
			mysql_query($query);
			$this->ModRecord('Delete Episode id ' . $_GET['id']);
		}
	}
	
	private function addVideoImage()
	{
		// /scripts.php?view=management&u=1&node=episodes&edit=image-add
		if(!isset($_GET['epid']))
		{
			echo 'Invalid';
		}
		else
		{
			if(!isset($_GET['point']) || (isset($_GET['point']) && $_GET['point'] == 'before'))
			{
				$script_location = '/home/mainaftw/public_html/manage/ajax.php phpcli-auth=true node=episodes sname='.$_GET['sname'].' edit=image-add epid='.$_GET['epid'].' point=after';
				$CMD = exec("php-cgi -f " . $script_location);
				echo $script_location;
				//echo $CMD;
			}
			else
			{
				$epid = mysql_real_escape_string($_GET['epid']);
				$results = mysql_query("SELECT episode.epprefix, episode.epnumber, episode.vidwidth, episode.vidheight, episode.Movie, episode.videotype, series.seriesname, series.videoServer FROM episode, series WHERE series.id=episode.sid AND episode.id = '".$epid."'");
				$row = mysql_fetch_array($results);
				$url = 'http://' . $row['videoServer'] . '.animeftw.tv/fetch-pictures-v2.php?node=add&remote=true&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
				//echo $url;
				$createscript = $this->RemoteBuildEpImage($url);
				if($createscript == 'Success')
				{
					echo '<script>alert("There was an error Creating that Image! Error: '.$createscript.'");</script>';
				}
				else
				{
					echo '<script>alert("Image Creation for ' . $row['seriesname'] . ' episode ' . $row['epnumber'] . ' Completed!"); </script>';
				}
				//echo '<div align="center">Image Creation has been completed<br />Please verify that the image has shown up below. If not, please alert brad asap.<br /><img src="http://static.ftw-cdn.com/site-images/video-images/'.$row['epprefix'].'_'.$row['epnumber'].'_screen.jpeg" alt="" height="200px" /><br /><br /><input type="button" value="Back to Episode Listing" name="edit" id="edit" onclick="ajax_loadContent(\'manageedit\',\''.$link.'&sname='.$row['seriesname'].'\'); return false;"></div>';
				mysql_query("UPDATE episode SET image = 1, html5 = 1 WHERE id = $epid");
			}
		}
	}
	
	private function RemoteBuildEpImage($url)
	{
		$file = file_get_contents($url);
		echo $file;
	}
}