<?php
/****************************************************************\
## FileName: series.class.php
## Author: Brad Riemann
## Usage: Series sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Series extends Config {

	var $Categories;

	public function __construct()
	{
		parent::__construct(TRUE);
		if(isset($_POST['method']) && $_POST['method'] == 'AdminSeriesSearch')
		{
			$this->adminSeriesSearch();
		}
		else
		{
			echo '<div class="body-container srow">';
			$this->deploySeries();
			echo '</div>';
		}
	}

	private function deploySeries()
	{
		if($this->ValidatePermission(21) == TRUE)
		{
			$link = 'ajax.php?node=series'; // base url
			$limit = 30; //series per page
			$DivID = 'right-column';
			if(isset($_GET['page'])){$page = $_GET['page'];}
			else {$page = 0;}
			echo '<div align="center" style="padding-top:5px;">';
			echo '<a href="#" onClick="$(\'#right-column\').load(\''.$link.'\'); return false;">Home</a> ';
			if($this->ValidatePermission(22) == TRUE)
			{
				echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=addseries&step=before\'); return false;">Add Series</a> ';
			}
			if($this->ValidatePermission(24) == TRUE)
			{
				echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=search\'); return false;">Series Search</a> ';
			}
			if($this->ValidatePermission(25) == TRUE)
			{
				echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=upload\'); return false;">Upload Series Image</a> ';
			}
			if($this->ValidatePermission(72) == TRUE)
			{
				echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=mass-update\'); return false;">Mass Updates</a> ';
			}
			if($this->ValidatePermission(73) == TRUE)
			{ //announce
				echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=announce\'); return false;">Announcement Builder</a> ';
			}
			echo '</div><br />';
			if(!isset($_GET['stage']))
			{
				$this->buildCategories();
				echo '
				<div id="ContentStuff" class="ContentStuff">';
				$TotalSeries = $this->Query('series'); //count all of the series please.
				$query = "SELECT id, seriesName, fullSeriesName, seoname, romaji, kanji, videoServer, active, description, ratingLink, stillRelease, Movies, moviesOnly, OVA, noteActivate, noteReason, category FROM series ORDER BY id DESC LIMIT $page, $limit";
				mysqli_query($conn, "SET NAMES 'utf8'");
				$result = mysqli_query($conn, $query);

				echo '<div id="seriesg">';
				echo '<div style="padding:3px;">';
				$this->pagingV1($DivID,$TotalSeries,$limit,$page,$link);
				echo '</div>';
				echo '<div>';
				$i = 0;
				$fivearray = array(1 => '<br />',2 => '<br /><br />',3 => '<br /><br /><br />',4 => '<br /><br /><br /><br />',5 => '<br /><br /><br /><br /><br />',6 => '<br /><br /><br /><br /><br /><br />',7 => '<br /><br /><br /><br /><br /><br /><br />');
				echo '
				<script type="text/javascript" src="assets/jqplot.dataAxisRenderer.min.js"></script>
				<script type="text/javascript" src="assets/jqplot.barRenderer.min.js"></script>
				<script type="text/javascript" src="assets/jqplot.categoryAxisRenderer.min.js"></script>
				<script type="text/javascript" src="assets/jqplot.pointLabels.min.js"></script>';
				while(list($id, $seriesName, $fullSeriesName, $seoname, $romaji, $kanji, $videoServer, $active, $description, $ratingLink, $stillRelease, $Movies, $moviesOnly, $OVA, $noteActivate, $noteReason, $category) = mysqli_fetch_array($result, MYSQL_NUM))
				{
					$query = mysqli_query($conn, "SELECT id FROM episode WHERE seriesname='".$seriesName."' AND Movie = 0");
					$CountEpisodes = mysqli_num_rows($query);
					if($moviesOnly == 1){$moviesOnly = 'yes';}else {$moviesOnly = 'no';}
					if($noteActivate == 1){$noteActivate = 'yes';}else {$noteActivate = 'no';}
					if($active == 'no'){$active = "<span class=\"sinactive\">In-Active</span>";}else {$active = "<span class=\"sactive\">Active</span>";}
					$description = stripslashes($description);
					$fullSeriesName = stripslashes($fullSeriesName);
					$dlength = strlen($description);
					if($dlength > 800)
					{
						$gvar = ceil(($dlength-800)/55);
						$gvar = $fivearray[$gvar];
					}
					else {$gvar = '';}

					// ADDED 10/11/2014 by Robotman321
					// explodes the category string so we can look through and make it awesome.
					$category = split(" , ",$category);
					$finalizedCategories = '';
					$i = 0;
					$count = count($category);
					foreach($category as $value)
					{
						$finalizedCategories .= $this->Categories[$value]['name'];
						$i++;
						if($i < $count)
						{
							$finalizedCategories .= ', ';
						}
					}

					if($i % 2){ $srow = ' class="srow2" style="background-color:#D6D6D6;"';} else {$srow = ' class="srow"';}
					echo '<div'.$srow.'>
					<div>
						<div class="sleftcol">
							<div align="center"><a href="/anime/'.$seoname.'/" target="_blank">View Series</a>';
						if($this->ValidatePermission(23) == TRUE)
						{
							echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=edit&step=before&sid='.$id.'\'); return false;">Edit Series</a>';
						}
						echo '| <a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=episodes&sname='.$id.'\'); return false;">Episodes</a>';
						echo '</div>
						<b>ID #:</b> ' . $id . '<br />
						<b>Series Name:</b> ' . $fullSeriesName . '<br />
						<b>Series Site Active?</b> '.$active.'<br />
						<b>Kanji:</b> '.$kanji.'<br />
						<b>Romaji:</b> '.$romaji.'<br />
						<b>Video Server:</b> '.$videoServer.'<br />
						<b>Still Airing?</b> '.$stillRelease.'<br />
						<b>Movies only?</b> '.$moviesOnly.'<br />
						<b>Seres Note Active?</b> '.$noteActivate.'<br />
						<b>Total Episodes:</b> '.$CountEpisodes.'<br />
						<b>Number of Movies:</b> '.$Movies.'<br />
						<b>Genres:</b> '.$finalizedCategories.'<br />
						<b>Rating:</b><br /><img src="//i.animeftw.tv/ratings/' . $ratingLink . '" alt="" title="This series\'s rating" />
						'.$gvar.'
						</div>

						<div class="srightcol">'.nl2br($description).'</div>
						<div class="sfarrightcol"><img src="' . $this->Host . '/seriesimages/' . $id . '.jpg" alt="" style="height:250px;" /></div>
					</div>';
					$today = strtotime(date("Y-m-d"));
					$sevendaysago = $today-(7*86400);
					$query1 = "SELECT `date`, `views` FROM  `mainaftw_stats`.`series_stats` WHERE `series_id` = " . $id . " AND `date` >= " . $sevendaysago . " AND `date` <= " . $today;
					//echo $query1;
					$result1 = mysqli_query($conn, $query1);
					if(!$result1)
					{
					}
					else
					{
						$data = '';
						$count = mysqli_num_rows($result1);
						if($count < 9)
						{
						}
						else
						{
							echo '<div id="series-chart-' . $id . '" style="margin:0 0 10px 20px; width:425px; height:200px;"></div>';
							$a = 1;
							while($row = mysqli_fetch_assoc($result1))
							{
								$data .= '[\'' . date("Y-m-d",$row['date']) . ' 8:00AM\',' . $row['views'] . ']';
								if($a < $count)
								{
									$data .= ', ';
								}
								$a++;
							}
							echo "
							<script class=\"code\" type=\"text/javascript\">

								$(document).ready(function(){
								  var line1=[" . $data . "];
								  var plot2 = $.jqplot('series-chart-$id', [line1], {
									  title:'Daily Episode Views for " . stripslashes($fullSeriesName) . "',
									  gridPadding:{right:35},
									  axes:{
										xaxis:{
										  renderer:$.jqplot.DateAxisRenderer,
										  tickOptions:{formatString:'%b %#d, %y'},
										  min:'" . date("F d, Y", $sevendaysago) . "',
										  tickInterval:'1 day'
										}
									  },
									  seriesDefaults: {
										showMarker:false,
										pointLabels: { show:true }
									  }
								  });
								});

							</script>";
						}
					}
					echo '</div>';
					$i++;
				}
				echo '</div>';
				$this->pagingV1($DivID,$TotalSeries,$limit,$page,$link);
				echo '</div>';
				echo '</div>';
			}
			else
			{
				if($_GET['stage'] == 'search' && $this->ValidatePermission(24) == TRUE)
				{
					echo '<div class="tbl"><br />
						<div align="center">';
					echo '<form method="POST" name="AdminSeriesSearch" id="AdminSeriesSearch">';
					echo '<input type="hidden" id="method" class="method" value="AdminSeriesSearch" name="method" />
						<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
						<input type="hidden" name="uid" value="' . $this->UserArray[1] . '" />
						<table width="650px">
						<tr>
							<td align="left" colspan="2">
								<div align="center">
									<input name="SeriesName" id="SeriesName" type="text" class="text-input" style="width:200px;" />
									<input name="submit" type="button" class="SubmitForm" value="Search" />
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="margin: 5px 0px 0px 0px;">
									<div align="center" style="font-size: 9px;">Use the above form, to search through series on the site, use the Romaji, Kanji or Series Names and results will be returned.<br />(Results Below)</div>
								</div>
							</td>
						</tr>
						</table>
						</form>
						</div></div>';
						echo '
						<script type="text/javascript" src="assets/jqplot.dataAxisRenderer.min.js"></script>
						<script type="text/javascript" src="assets/jqplot.barRenderer.min.js"></script>
						<script type="text/javascript" src="assets/jqplot.categoryAxisRenderer.min.js"></script>
						<script type="text/javascript" src="assets/jqplot.pointLabels.min.js"></script>';
						echo '<div id="form_results" class="form_results">&nbsp;</div>';
						echo '<script>
						$(function() {
							$(\'#SeriesName\').keypress(function(event){

								if (event.keyCode == 10 || event.keyCode == 13)
								{
									event.preventDefault();
									$(\'.SubmitForm\').click();
								}
							});
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
								$.ajax({
									type: "POST",
									url: "ajax.php",
									data: $(\'#AdminSeriesSearch\').serialize(),
									success: function(html) {
										$(\'.form_results\').show().html(html);
									}
								});
								return false;
							});
							return false;
						});
						</script>';
				}
				else if($_GET['stage'] == 'upload' && $this->ValidatePermission(25) == TRUE){
					echo 'Upload feature coming soon';
				}
				else if($_GET['stage'] == 'announce' && $this->ValidatePermission(73) == TRUE){
					echo '<br /><div align="center">The announcement builder takes a given date range and returns the series and episodes added within that timeframe.<br> Episodes added are limited to airing series.</div>';
                    echo '
                    <form id="SeriesAnnouncementBuilder">
                    <input type="hidden" id="method" class="method" value="SeriesAnnouncementBuilder" name="method" />
                    <div id="date-picker-container" align="center" style="padding:10px;">
                        <div style="display:inline-block;width:15.999%;"></div>
                        <div style="vertical-align:top;width:30.9999%;display:inline-block;" align="left">
                            <div>Start Date:</div>
                            <div>
                                <input type="text" data-toggle="datepicker" class="text-input" name="start-date" value="09/07/2017">
                            </div>
                        </div>
                        <div style="vertical-align:top;width:30.9999%;display:inline-block;" align="left">
                            <div>End Date:</div>
                            <div>
                                <input type="text" data-toggle="datepicker" class="text-input" name="end-date" value="09/07/2017">
                            </div>
                        </div>
                    </div>
                    <div align="center">
                        <input type="submit" name="update-form" id="update-form" value="Update Output">
                    </div>
                    </form>
                    <script>
                        $(\'[data-toggle="datepicker"]\').datepicker();
                    </script>';
					echo '<br /><b>Output:</b><br />';
					echo '<div id="SeriesAnnouncementOutput"><textarea style="height:325px;overflow-y:scroll;overflow-x:none;border:1px solid #0C90BB;width:100%" onclick="this.select()"></textarea></div>';
						echo '
						<script>
						$(document).ready(function() {
							$(\'#update-form\').click(function() {
								$.ajax({
									type: "POST",
									url: "ajax.php",
									data: $(\'#SeriesAnnouncementBuilder\').serialize(),
									success: function(html) {
										$(\'#SeriesAnnouncementOutput\').show().html(html);
									}
								});
								return false;
							});
						});
						</script>';
				}
				else if($_GET['stage'] == 'mass-update' && $this->ValidatePermission(72) == TRUE)
				{
						if(isset($_GET['seriesname']))
						{
							$ReqSeriesName = $_GET['seriesname'];
						}
						else
						{
							$ReqSeriesName = '';
						}
						echo '<div align="center">The Mass update Tool is used to update a series` episodes from a single interface, since this is technically a series function I have placed it here instead of in the episode section as it makes it easier to just find it and run. -Brad</div>';
						echo '<div align="center" style="padding-top:5px;">';
							echo '<select name="AvailableSeries" id="AvailableSeries" style="color: #000000;">';
						$query2 = "SELECT `id`, `seriesName`, `fullSeriesName` FROM series ORDER BY fullSeriesName ASC";
						echo '<option id="0" value="0">Choose a Series</option> ';
						$result2 = mysqli_query($conn, $query2);
						while(list($id, $seriesName, $fullSeriesName) = mysqli_fetch_array($result2, MYSQL_NUM))
						{
							$fullSeriesName = stripslashes($fullSeriesName);
							echo '<option id="'.$id.'" value="'.$id.'"'; if($id == $ReqSeriesName){echo' selected';} echo '>'.$fullSeriesName.'</option> ';
						}
						echo '</select>';
						echo '</div><br />';
						echo '<div id="SeriesOptions">&nbsp;</div>';
						echo '
						<script>
						$(document).ready(function() {
							$(\'#AvailableSeries\').change(function() {
								$(\'#right-column\').load(\''.$link.'&stage=mass-update&seriesname=\' + $(\'select\').val());
							});
						});
						</script>';
					if(isset($_GET['seriesname']))
					{
						$query = "SELECT `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`videotype`, `episode`.`hd`, `series`.`fullSeriesName` FROM `episode`, `series` WHERE episode.sid = '" . mysqli_real_escape_string($conn, $_GET['seriesname']) . "' AND series.id='" . mysqli_real_escape_string($conn, $_GET['seriesname']) . "'LIMIT 0, 1";
						$results = mysqli_query($conn, $query);
						$row = mysqli_fetch_array($results);
						echo '<div id="form_results" class="form_results">&nbsp;</div>';
						echo '<form method="POST" name="MassEpisodeEdit" id="MassEpisodeEdit">';

						echo '
						<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
						<input type="hidden" id="method" class="method" value="MassEpisodeUpdate" name="method" />
						<input type="hidden" name="fullSeriesName" value="' . $row['fullSeriesName'] . '" />
						<input type="hidden" name="sid" value="' . $_GET['seriesname'] . '" />
						<input type="hidden" name="old_vidwidth" value="' . $row['vidwidth'] . '" />
						<input type="hidden" name="old_vidheight" value="' . $row['vidheight'] . '" />
						<input type="hidden" name="old_epprefix" value="' . $row['epprefix'] . '" />
						<input type="hidden" name="old_subGroup" value="' . $row['subGroup'] . '" />
						<input type="hidden" name="old_videotype" value="' . $row['videotype'] . '" />
						<input type="hidden" name="old_hd" value="' . $row['hd'] . '" />
						<input type="hidden" name="uid" value="' . $this->UserArray[1] . '" />
						<table width="620px" border="0" cellpadding="2" cellspacing="1" align="center">
						<tr>
							<td width="130px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Video Width</td>
							<td>
								<input name="vidwidth" type="text" id="vidwidth" style="width:50px;" value="' . $row['vidwidth'] . '" class="text-input" />
								<label for="vidwidth" id="vidwidthError" class="FormError">The Video Width is Required</label>
							</td>

						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Video Height</td>
							<td>
								<input name="vidheight" type="text" id="vidheight" style="width:50px;" value="' . $row['vidheight'] . '" class="text-input" />
								<label for="vidheight" id="vidheightError" class="FormError">The Video Height is Required</label>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Episode Preffix</td>
							<td>
								<input name="epprefix" type="text" class="text-input" style="width:200px;" id="epprefix" value="' . $row['epprefix'] . '" />
								<label for="epprefix" id="epprefixError" class="FormError">The Episode Prefix is Required</label>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Fansub Group</td>
							<td>
								<input name="subGroup" type="text" class="text-input" id="subGroup" style="width:150px;" value="' . $row['subGroup'] . '" />
								<label for="subGroup" id="subGroupError" class="FormError">The Fansub Group is Required</label>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Video Type</td>
							<td>
								<select name="videotype" style="color: #000000;">
								<option value="divx"'; if($row['videotype'] == 'divx'){echo ' selected="selected"';} echo'>DivX</option>
								<option value="mkv"'; if($row['videotype'] == 'mkv'){echo ' selected="selected"';} echo'>MKV</option>
								<option value="mp4"'; if($row['videotype'] == 'mp4'){echo ' selected="selected"';} echo'>MP4</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Update Type [<a href="#" onClick="return false;" title="Use with caution, this will update Episodes, Movies and BOTH if selected.">?</a>]</td>
							<td>
								<select name="UpdateType" style="color: #000000;">
								<option value="0" selected="selected">Episodes Only</option>
								<option value="1">Movies Only</option>
								<option value="2">Episodes AND Movies</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="100px" style="font:13px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;">Any HD Episodes?</td>
							<td>
								<select name="hd" class="text-input2" id="hd-select">
									<option value="0"'; if($row['hd'] == '0'){echo ' selected="selected"';} echo'>480p Only</option>
									<option value="1"'; if($row['hd'] == '1'){echo ' selected="selected"';} echo'>480p/720p</option>
									<option value="2"'; if($row['hd'] == '2'){echo ' selected="selected"';} echo'>480p/720p/1080p</option>
								</select>
							</td>
						</tr>
						</table><br />';
						echo '<input type="submit" class="SubmitForm" id="submit" name="submit" value="Update All Episodes">';
						echo '</form>';
							echo '</form>';
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
								$(\'label\').hide();
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
								var subGroup = $("input#subGroup").val();
								if (subGroup == "") {
									$("label#subGroupError").show();
									$("input#subGroup").focus();
									return false;
								}
								var hd = $("#hd-select").val();
								var c=confirm("All Checks were passed, please note, there is no going back, were all the settings correct?");
								if(c==false)
								{
									return false;
								}
								else
								{
									$.ajax({
										type: "POST",
										url: "ajax.php",
										data: $(\'#MassEpisodeEdit\').serialize(),
										success: function(html) {
											if(html.indexOf("Success") >= 0){
												//$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Episode Mass Update was Successful!</div>");
												$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
											$(\'.form_result\').delay(8000).slideUp();
											}
											else{
												$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
											}
										}
									});
									return false;
								}
							});
							return false;
						});
						</script>';
					}
					// The idea behind this feature, is that it will give a list of series, followed by all the options for  the episodes, so you can change the eppisode prefix, width, height, sub group, image status, video type, in bulk. The best way to implement will be to have the system use a GET request back to the same script, to define the seriesname, if the seriesname is defined it will then pull up the first episodes options.. this is dangerous as things down the line could have multiple dimmensions.. possibly a way to show multiples? like a group by... food for later thoughts..
				}
				else if(($_GET['stage'] == 'edit' && $this->ValidatePermission(23) == TRUE) || ($_GET['stage'] == 'addseries' && $this->ValidatePermission(22) == TRUE))
				{
					// ADDED 10/11/2014 by Robotman321
					// Builds the Category listing
					$this->buildCategories();

					if($_GET['stage'] == 'edit' && $this->ValidatePermission(23) == TRUE)
					{
						$Type = 'edit';
						if(!is_numeric($_GET['sid'])){
							echo 'That id is not valid..';
							exit;
						}
						else {
							$sid = mysqli_real_escape_string($conn, $_GET['sid']);
							$sid = htmlentities($sid);
							$query2  = "SELECT id, seriesName, fullSeriesName, romaji, kanji, synonym, seoname, videoServer, active, description, ratingLink, stillRelease, Movies, moviesOnly, OVA, noteReason, aonly, sequelto, prequelto, category, seriesType, seriesList, ueid, hd, `license` FROM series WHERE id='$sid'";
							mysqli_query($conn, "SET NAMES 'utf8'");
							$result2 = mysqli_query($conn, $query2);
							list($id, $seriesName, $fullSeriesName, $romaji, $kanji, $synonym, $seoname, $videoServer, $active, $description, $ratingLink, $stillRelease, $Movies, $moviesOnly, $OVA, $noteReason, $aonly, $sequelto, $prequelto, $category, $seriesType, $seriesList, $ueid, $hd, $license) = mysqli_fetch_array($result2, MYSQL_NUM);
							$description = str_replace("<br />", "\n", $description);

							$description = stripslashes($description);
							$noteReason = stripslashes($noteReason);
							$fullSeriesName = stripslashes($fullSeriesName);

							$HiddenInputs = '<input type="hidden" name="sid" value="' . $id . '" id="sid" />
							<input type="hidden" id="method" class="method" value="EditSeries" name="method" />';
							$SubmitTXT = 'Update Series';
						}
					}
					else if($_GET['stage'] == 'addseries' && $this->ValidatePermission(22) == TRUE)
					{
						if(isset($_GET['ueid']))
						{
							$Type = 'add';
							$HiddenInputs = '<input type="hidden" id="method" class="method" value="AddSeries" name="method" />
							<input type="hidden" id="uploaderid" name="uploaderid" value="' . $_GET['ueid'] . '" />';
							$SubmitTXT = 'Add Series';
							$query = "SELECT `series`, `prefix`, `anidbsid`, `hd`, `airing` FROM `uestatus` WHERE `id` = " . mysqli_real_escape_string($conn, $_GET['ueid']);
							$result = mysqli_query($conn, $query);
							$row = mysqli_fetch_array($result);
							$SeriesPrefix = substr($row['series'], 0, 10);
							$SeriesPrefix = strtolower($SeriesPrefix);

							// UPDATED: 7/17/2014 by Robotman321
							// Added more options to pull from the beginning of the string.
							if($SeriesPrefix == '[reencode]')
							{
								$FixedName = trim(substr($row['series'], 11), ' ');
							}
							else if(strtolower(substr($row['series'], 0, 8)) == '[winter]' || strtolower(substr($row['series'], 0, 8)) == '[spring]' || strtolower(substr($row['series'], 0, 8)) == '[summer]')
							{
								$FixedName = trim(substr($row['series'], 8), ' ');
							}
							else if(strtolower(substr($row['series'], 0, 6)) == '[fall]')
							{
								$FixedName = trim(substr($row['series'], 6), ' ');
							}
							else
							{
								$FixedName = trim($row['series'], ' ');
							}
							$seoname = strtolower($FixedName);
							$seoname = preg_replace('/[^a-z0-9 -]+/', '', $seoname);
							$seoname = str_replace(' ', '-', $seoname);
							$seoname = trim($seoname, '-');

                            $stillRelease = 'no';
                            if ($row['airing'] == 1) {
                                $stillRelease = 'yes';
                            }
							$id = ''; $seriesName = $row['prefix']; $fullSeriesName = $FixedName; $romaji = ''; $kanji = ''; $synonym = ''; $videoServer = ''; $active = 'yes'; $description = ''; $ratingLink = 'e.jpg'; $Movies = 0; $moviesOnly = ''; $OVA = ''; $noteReason = ''; $aonly = ''; $sequelto = ''; $prequelto = ''; $category = ''; $seriesType = '2'; $seriesList = '';$hd = $row['hd']; $ueid = $_GET['ueid']; $license = 0;
						}
						else
						{
							$Type = 'add';
							$HiddenInputs = '<input type="hidden" id="method" class="method" value="AddSeries" name="method" />';
							$SubmitTXT = 'Add Series';
							$id = ''; $seriesName = ''; $fullSeriesName = ''; $romaji = ''; $kanji = ''; $synonym = ''; $seoname = ''; $videoServer = ''; $active = 'yes'; $description = ''; $ratingLink = 'e.jpg'; $stillRelease = ''; $Movies = 0; $moviesOnly = ''; $OVA = ''; $noteReason = ''; $aonly = ''; $sequelto = ''; $prequelto = ''; $category = ''; $seriesType = '2'; $seriesList = ''; $ueid = ''; $hd = 0; $license = 0;
						}
					}
					else
					{
						$Type = '';
						$HiddenInputs = '';
						$SubmitTXT = '';
					}
					echo '
					<script>
						$(document).ready(function() { $("#category").select2({placeholder: "Input Categories", tokenSeparators: [",", " "]}); });
					</script>';
					echo '<div id="form_results" class="form_results">&nbsp;</div>';
					echo '<form method="POST" action="#" id="SeriesForm">';
					echo '<div>';
					echo $HiddenInputs;
					echo '
					<input type="hidden" name="hidden" value="0" id="hidden" />
					<input type="hidden" name="videoServer" value="videos" id="videoServer" />
					<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" id="Authorization" />
					<input type="hidden" name="uid" value="' . $this->UserArray[1] . '" />
					<div id="series-wrapper">
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Base Series Name</i></b><br /> <i>This should be the fullname of the series with no capitals, spaces or underscores, I.E:</i><br /> <b>airgear</b></div>
							<div class="series-form-right">
								<input name="seriesName" type="text" id="seriesName" size="25" value="'.$seriesName.'" class="text-input" />
								<label for="seriesName" id="seriesNameError" class="FormError">A Base Series Name is Required.</label>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Full Series Name</i></b><br /> <i>This should be the fullname of the series WITH proper capitilization and spaces, I.E:</i><br /> <b>Air Gear</b></div>
							<div class="series-form-right">
								<input name="fullSeriesName" type="text" class="text-input" id="fullSeriesName" size="25" value="'.$fullSeriesName.'" />
								<label for="fullSeriesName" id="fullSeriesNameError" class="FormError">A Full Series Name is Required.</label>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Romaji</i></b><br /></div>
							<div class="series-form-right">
								<input name="romaji" type="text" class="text-input" id="romaji" size="25" value="'.$romaji.'" />
								<label for="romaji" id="romajiError" class="FormError">The Romaji Name is Required.</label>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Kanji</i></b><br /></div>
							<div class="series-form-right">
								<input name="kanji" type="text" class="text-input" id="kanji" size="25" value="'.$kanji.'" />
								<label for="kanji" id="kanjiError" class="FormError">The Kanji Name is Required.</label>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Synonym</i></b><br /> <i>Sometimes a series is known by more than the official title, those go here, use commas to separate each name.</i></div>
							<div class="series-form-right"><textarea name="synonym" id="synonym" cols="55" rows="5" class="text-input">'.$synonym.'</textarea></div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>SEO Name</i></b><br /> <i>This should be the fullname of the series without Capitals, Spaces should be replaced with hyphens (-). I.E:</i><br /> <b>air-gear</b></div>
							<div class="series-form-right">
								<input name="seoname" type="text" class="text-input" id="seoname" size="25" value="'.$seoname.'" />
								<label for="seoname" id="seonameError" class="FormError">A SEO Name is Required.</label>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Site Active?</i></b><br /> <i>Is this series site active? No is the Default</i></div>
							<div class="series-form-right">
								<select name="active" style="color: #000000;" class="text-input">
									<option value="no"'; if($active == 'no'){echo ' selected="selected"';} echo '>No</option>
									<option value="yes"'; if($active == 'yes'){echo ' selected="selected"';} echo '>Yes</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Series Synopsis/description</i></b><br /> <i>Take the description from the series on <a href="http://anidb.net">AniDB.net</a> and paste it here, NO html required</i></div>
							<div class="series-form-right"><textarea name="description2" id="description2" cols="55" rows="5" class="text-input">'.$description.'</textarea></div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Series Genres</i></b><br /> <i>Take the "categories" from the series on <a href="http://anidb.net">AniDB.net</a> and paste it here, NO Special characters []&lt;&gt; just words and commas.</i></div>
							<div class="series-form-right">';

								$category = explode(" , ",$category);
								echo '
								<select name="category[]" style="width:400px;" id="category" multiple="multiple">';
								foreach($this->Categories as $CatArray)
								{
									if(in_array($CatArray['id'],$category))
									{
										$selected = ' selected';
									}
									else
									{
										$selected = '';
									}
									echo '<option value="' . $CatArray['id'] . '"' . $selected . '>' . $CatArray['name'] . '</option>'."\n";
								}
								echo '
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Series Rating</i></b><br /><i>Choose the rating that goes with the series.</i></div>
							<div class="series-form-right">
								<div align="center" style="display:inline-block;"><img src="//i.animeftw.tv/ratings/e.jpg" alt="Everyone" /><br /><input type="radio" name="ratingLink" value="e.jpg" '; if($ratingLink == 'e.jpg'){echo 'checked="checked"';} echo ' /></div>
								<div align="center" style="display:inline-block;"><img src="//i.animeftw.tv/ratings/12+.jpg" alt="12+" /><br /><input type="radio" name="ratingLink" value="12+.jpg" '; if($ratingLink == '12+.jpg'){echo 'checked="checked"';} echo '  /></div>
								<div align="center" style="display:inline-block;"><img src="//i.animeftw.tv/ratings/15+.jpg" alt="15+" /><br /><input type="radio" name="ratingLink" value="15+.jpg" '; if($ratingLink == '15+.jpg'){echo 'checked="checked"';} echo '  /></div>
								<div align="center" style="display:inline-block;"><img src="//i.animeftw.tv/ratings/18+.jpg" alt="18+" /><br /><input type="radio" name="ratingLink" value="18+.jpg" '; if($ratingLink == '18+.jpg'){echo 'checked="checked"';} echo '  /></div>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Still Releasing?</i></b><br /> <i>Is this Series still releasing? Default is No</i></div>
							<div class="series-form-right">
								<select name="stillRelease" style="color: #000000;" class="text-input">
									<option value="no" '; if($stillRelease == 'no'){echo 'selected="selected"';} echo ' >No</option>
									<option value="yes" '; if($stillRelease == 'yes'){echo 'selected="selected"';} echo ' >Yes</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Movies:</i></b><br /> <i>Does this Series Have any movies?</i></div>
							<div class="series-form-right">
								<select name="Movies" style="color: #000000;" class="text-input">
									<option value="0" '; if($Movies == '0'){echo 'selected="selected"';} echo '>No</option>
									<option value="1" '; if($Movies == '1'){echo 'selected="selected"';} echo '>Yes</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Movie Only Series?</i></b><br /> <i>Is this Series Movies only? No is Default</i></div>
							<div class="series-form-right">
								<select name="moviesOnly" style="color: #000000;" class="text-input">
									<option value="0" '; if($moviesOnly == '0'){echo 'selected="selected"';} echo '>No</option>
									<option value="1" '; if($moviesOnly == '1'){echo 'selected="selected"';} echo '>Yes</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>OVA Only Series?</i></b><br /> <i>Is this Series OVA only? No is Default</i></div>
							<div class="series-form-right">
								<select name="OVA" style="color: #000000;" class="text-input">
									<option value="0" '; if($OVA == '0'){echo 'selected="selected"';} echo '>No</option>
									<option value="1" '; if($OVA == '1'){echo 'selected="selected"';} echo '>Yes</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><i>Note Reason</i></b><br /> <i>Only if Yes is above, fill this out and the reason will be placed on the site, NO HTML is needed</i></div>
							<div class="series-form-right"><textarea name="noteReason" id="noteReason" cols="55" rows="5" class="text-input">' . $noteReason . '</textarea></div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Series Type</i></b><br /> <i>Is this series an MKV, DIVX or MP4 based series?</i></div>
							<div class="series-form-right">
								<select name="seriesType" style="color: #000000;" class="text-input">
									<option value="0" '; if($seriesType == '0'){echo 'selected="selected"';} echo '>DivX</option>
									<option value="1" '; if($seriesType == '1'){echo 'selected="selected"';} echo '>MKV</option>
									<option value="2" '; if($seriesType == '2'){echo 'selected="selected"';} echo '>MP4</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Prequel</i></b><br /> <i>What series is a Prequel to this?</i></div>
							<div class="series-form-right">
								<select name="prequelto" style="color: #000000;" class="text-input">';
								echo '<!-- '.$prequelto.' -->';
								$query2 = "SELECT id, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
								if($prequelto == 0)
								{
									echo '<option id="0" value="0">None</option> ';
								}
								else
								{
									echo '<option id="0" value="0">None</option> ';
								}
								$result2 = mysqli_query($conn, $query2);
								while(list($id2, $fullSeriesName) = mysqli_fetch_array($result2, MYSQL_NUM))
								{
									$fullSeriesName = stripslashes($fullSeriesName);
									echo '<option id="'.$id2.'" value="'.$id2.'"'; if($id2 == $prequelto){echo' selected';} echo '>'.$fullSeriesName.'</option> ';

								}
								echo '</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Sequel</i></b><br /> <i>What series is a Sequel to this?</i></div>
							<div class="series-form-right">
								<select name="sequelto" style="color: #000000;" class="text-input">';
								echo '<!-- '.$sequelto.' -->';
								$query2 = "SELECT id, fullSeriesName, active FROM series ORDER BY fullSeriesName ASC";
								if($sequelto == 0){
									echo '<option id="0" value="0">None</option> ';
								}
								else {
									echo '<option id="0" value="0">None</option> ';
								}
								$result2 = mysqli_query($conn, $query2);
								while(list($id2, $fullSeriesName) = mysqli_fetch_array($result2, MYSQL_NUM)){
									$fullSeriesName = stripslashes($fullSeriesName);
									echo '<option id="'.$id2.'" value="'.$id2.'"'; if($id2 == $sequelto){echo' selected';} echo '>'.$fullSeriesName.'</option> ';
								}
								echo '</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>HD Videos.</i></b><br /><br /></div>
							<div class="series-form-right">
								<select name="hd" style="color: #000000;" class="text-input">
									<option value="0" '; if($hd == '0'){echo 'selected="selected"';} echo '>480p Only</option>
									<option value="1" '; if($hd == '1'){echo 'selected="selected"';} echo '>480p/720p</option>
									<option value="2" '; if($hd == '2'){echo 'selected="selected"';} echo '>480p/720p/1080p</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Member Level?</i></b><br /> <i>What Level of Membership should be required to view this series?</i></div>
							<div class="series-form-right">
								<select name="aonly" style="color: #000000;" class="text-input">
									<option value="0" '; if($aonly == '0'){echo 'selected="selected"';} echo '>Unregistered +</option>
									<option value="1" '; if($aonly == '1'){echo 'selected="selected"';} echo '>Basic +</option>
									<option value="2" '; if($aonly == '2'){echo 'selected="selected"';} echo '>Advanced +</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Uploads Board Entry</i></b><br /> <i>Does this series have an entry on the uploads board?</i></div>
							<div class="series-form-right">
								' . $this->uploadsEntrySelect($ueid, null) . '
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Licensed?</i></b><br /> <i>Is this anime licensed?</i></div>
							<div class="series-form-right">
								<select name="license" style="color: #000000;" class="text-input">
									<option value="0" '; if($license == '0'){echo 'selected="selected"';} echo '>No</option>
									<option value="1" '; if($license == '1'){echo 'selected="selected"';} echo '>Yes</option>
								</select>
							</div>
						</div>
						<div class="series-form-row">
							<div class="series-form-left"><b><i>Series Type?</i></b><br /> <i>Is it Anime, is it a Drama?</i> ** do not use **</div>
							<div class="series-form-right">
								<select name="seriesList" style="color: #000000;" class="text-input">
									<option value="0" '; if($seriesList == '0'){echo 'selected="selected"';} echo '>Anime</option>
									<option value="1" '; if($seriesList == '1'){echo 'selected="selected"';} echo '>Drama</option>
								</select>
							</div>
						</div>
					</div>';
							echo '<input type="submit" class="SubmitForm" id="submit" name="submit" value="' . $SubmitTXT . '">';
							if(isset($_GET['ueid']))
							{
								echo '&nbsp;<input type="button" value="Back to the Tracker" onClick="$(\'html, body\').animate({ scrollTop: 0 }, \'slow\');$(\'#uploads-global-wrapper\').load(\'ajax.php?node=uploads\'); return false;" />';
							}
							echo '</form>';
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
							$(".SubmitForm").click(function() {';
							if($Type == 'add')
							{ //seoname, description2, category
								echo '
								$(\'label\').hide();
								var seriesName = $("input#seriesName").val();
								if (seriesName == "") {
									$("label#seriesNameError").show();
									$("input#seriesName").focus();
									return false;
								}
								var fullSeriesName = $("input#fullSeriesName").val();
								if (fullSeriesName == "") {
									$("label#fullSeriesNameError").show();
									$("input#fullSeriesName").focus();
									return false;
								}
								var romaji = $("input#romaji").val();
								if (romaji == "") {
									$("label#romajiError").show();
									$("input#romaji").focus();
									return false;
								}
								var kanji = $("input#kanji").val();
								if (kanji == "") {
									$("label#kanjiError").show();
									$("input#kanji").focus();
									return false;
								}
								var seoname = $("input#seoname").val();
								if (seoname == "") {
									$("label#seonameError").show();
									$("input#seoname").focus();
									return false;
								}
								';
							}
							else // its an edit.. duh
							{
							}
							echo '
								$.ajax({
									type: "POST",
									url: "ajax.php",
									data: $(\'#SeriesForm\').serialize(),
									success: function(html) {
										if(html.indexOf("Success") >= 0){
											$("html, body").animate({ scrollTop: 0 }, "slow");
										';
										if($Type == 'add')
										{
											echo '
											$("#SeriesForm")[0].reset();
											$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>" + fullSeriesName + " Added Successfully</div>");';
										}
										else // its an edit.. duh
										{
											echo '
											$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Update Successful</div>");';
										}
										echo '
											$(".form_results").delay(8000).slideUp();
										}
										else{
											alert(html);
											$(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
										}
									}
								});
								return false;
							});
							return false;
						});
						</script>';

				}
				else {
					echo 'WTF Were you doing, were you trying to be cool? Error S-002';
				}
			}
		}
	}

	private function adminSeriesSearch()
	{
		$this->buildCategories();
		$input = mysqli_real_escape_string($conn, $_POST['SeriesName']);
		mysqli_query($conn, "SET NAMES 'utf8'");
		$query   = "SELECT id, seriesName, fullSeriesName, seoname, romaji, kanji, videoServer, active, description, ratingLink, stillRelease, Movies, moviesOnly, OVA, noteActivate, noteReason, category, (SELECT COUNT(id) FROM episode WHERE seriesname=series.seriesName) AS numeps FROM series WHERE ( fullSeriesName LIKE '%".$input."%' OR romaji LIKE '%".$input."%' OR kanji LIKE '%".$input."%' ) ORDER BY seriesName ASC LIMIT 100";
		$result  = mysqli_query($conn, $query);
		$ts = mysqli_num_rows($result);
		if($ts < 1)
		{
			echo '<div align="center">There were no results found for: <b>' . stripslashes($input) . '</b></div>';
		}
		else
		{
			echo '<div align="center">Showing ' . $ts . ' Results for: <b>' . stripslashes($input) . '</b></div>';
			while(list($id, $seriesName, $fullSeriesName, $seoname, $romaji, $kanji, $videoServer, $active, $description, $ratingLink, $stillRelease, $Movies, $moviesOnly, $OVA, $noteActivate, $noteReason, $category, $numeps) = mysqli_fetch_array($result, MYSQL_NUM))
			{
				//$query = mysqli_query($conn, "SELECT id FROM episode WHERE seriesname='".$seriesName."' AND Movie = 0");
				$CountEpisodes = $numeps;
				if($moviesOnly == 1){$moviesOnly = 'yes';}else {$moviesOnly = 'no';}
				if($noteActivate == 1){$noteActivate = 'yes';}else {$noteActivate = 'no';}
				if($active == 'no'){$active = "<span class=\"sinactive\">In-Active</span>";}else {$active = "<span class=\"sactive\">Active</span>";}
				$description = stripslashes($description);
				$fullSeriesName = stripslashes($fullSeriesName);
				$dlength = strlen($description);
				if($dlength > 800)
				{
					$gvar = ceil(($dlength-800)/55);
					$gvar = $fivearray[$gvar];
				}
				else
				{
					$gvar = '';
				}
				if($i % 2){ $srow = ' class="srow2" style="background-color:#D6D6D6;"';} else {$srow = ' class="srow"';}

				// ADDED 10/11/2014 by Robotman321
				// explodes the category string so we can look through and make it awesome.
				$category = split(" , ",$category);
				$finalizedCategories = '';
				$i = 0;
				$count = count($category);
				foreach($category as $value)
				{
					$finalizedCategories .= $this->Categories[$value]['name'];
					$i++;
					if($i < $count)
					{
						$finalizedCategories .= ', ';
					}
				}

				echo '<div'.$srow.'>
				<div>
					<div class="sleftcol">
						<div align="center"><a href="/anime/'.$seoname.'/" target="_blank">View Series</a>';
					if($this->ValidatePermission(23) == TRUE)
					{
						echo '| <a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=series&stage=edit&step=before&sid='.$id.'\'); return false;">Edit Series</a>';
					}
					echo '| <a href="#" onClick="$(\'#right-column\').load(\'ajax.php?node=episodes&sname='.$id.'\'); return false;">Episodes</a>';
					echo '</div>
					<b>ID #:</b> ' . $id . '<br />
					<b>Series Name:</b> ' . $fullSeriesName . '<br />
					<b>Series Site Active?</b> '.$active.'<br />
					<b>Kanji:</b> '.$romaji.'<br />
					<b>Romaji:</b> '.$kanji.'<br />
					<b>Video Server:</b> '.$videoServer.'<br />
					<b>Still Airing?</b> '.$stillRelease.'<br />
					<b>Movies only?</b> '.$moviesOnly.'<br />
					<b>Seres Note Active?</b> '.$noteActivate.'<br />
					<b>Total Episodes:</b> '.$CountEpisodes.'<br />
					<b>Number of Movies:</b> '.$Movies.'<br />
					<b>Genres:</b> '.$finalizedCategories.'<br />
					<b>Rating:</b><br /><img src="//i.animeftw.tv/ratings/' . $ratingLink . '" alt="" title="This series\'s rating" />
					'.$gvar.'
					</div>

					<div class="srightcol">'.$description.'</div>
					<div class="sfarrightcol"><img src="' . $this->Host . '/seriesimages/' . $id . '.jpg" alt="" style="height:250px;" /></div>
					</div>';
					$today = strtotime(date("Y-m-d"));
					$sevendaysago = $today-(7*86400);
					$query1 = "SELECT `date`, `views` FROM  `mainaftw_stats`.`series_stats` WHERE `series_id` = " . $id . " AND `date` >= " . $sevendaysago . " AND `date` <= " . $today;
					$result1 = mysqli_query($conn, $query1);
					if(!$result1)
					{
					}
					else
					{
						$data = '';
						$count = mysqli_num_rows($result1);
						if($count < 5)
						{
						}
						else
						{
							echo '<div id="series-chart-' . $id . '" style="margin:0 0 10px 20px; width:425px; height:200px;"></div>';
							$a = 1;
							while($row = mysqli_fetch_assoc($result1))
							{
								$data .= '[\'' . date("Y-m-d",$row['date']) . ' 8:00AM\',' . $row['views'] . ']';
								if($a < $count)
								{
									$data .= ', ';
								}
								$a++;
							}
							echo "
							<script class=\"code\" type=\"text/javascript\">

								$(document).ready(function(){
								  var line1=[" . $data . "];
								  var plot2 = $.jqplot('series-chart-$id', [line1], {
									  title:\"Daily Episode Views for " . stripslashes($fullSeriesName) . "\",
									  gridPadding:{right:35},
									  axes:{
										xaxis:{
										  renderer:$.jqplot.DateAxisRenderer,
										  tickOptions:{formatString:'%b %#d, %y'},
										  min:'" . date("F d, Y", $sevendaysago) . "',
										  tickInterval:'1 day'
										}
									  },
									  seriesDefaults: {
										showMarker:false,
										pointLabels: { show:true }
									  }
								  });
								});

							</script>";
						}
					}
				echo '</div>';
				$i++;
			}
		}
	}

	private function Query($var)
	{
		if($var == 'fl')
		{
			$iquery = "SELECT COUNT(id) FROM failed_logins";
		}
		else if($var == 'l')
		{
			$iquery = "SELECT COUNT(id) FROM logins";
		}
		else if($var == 'active')
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 1";
		}
		else if($var == 'inactive')
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 0";
		}
		else if($var == 'sus')
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Active = 2";
		}
		else if($var == 'advanced')
		{
			$iquery = "SELECT COUNT(ID) FROM users WHERE Level_access = 7";
		}
		else if($var == 'episodes')
		{
			$iquery = "SELECT COUNT(id) FROM episode";
		}
		else if($var == 'series')
		{
			$iquery = "SELECT COUNT(id) FROM series";
		}
		else {}
		$query = mysqli_query($conn, $iquery);
		$total = mysqli_result($query, 0);
		return $total;
		//unset $query;
	}
}

?>
