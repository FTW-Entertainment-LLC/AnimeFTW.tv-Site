<?php
/****************************************************************\
## FileName: anime.class.php									 
## Author: Brad Riemann										 
## Usage: Bullds the anime content for the mobile website.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Anime extends Config {
	
	var $Seo; // Seo global var
		
	public function __construct($Seo = NULL)
	{
		parent::__construct();
		$this->Seo = $Seo;
	}
	
	public function Init()
	{
		$this->DisplayAnime();
	}
	
	public function displayEpisode($epid = NULL)
	{
		echo '<div id="ad-overlay">';
		if($epid == NULL || !is_numeric($epid))
		{
			echo '<div align="center" style="padding:5px;font-weight:bold;">There was an issue with the request.</div>';
		}
		else
		{
			$this->buildEpisodeDisplay($epid);
		}
		echo '
			<script>
			$(document).keyup(function(e) {

				if (e.keyCode == 27) { $("#overlay").hide();$("#overlay-content").html(""); }   // esc
			});
			</script>
			<script>
			$(document).mouseup(function (e)
			{
				var container = $("#ad-overlay");

				if (!container.is(e.target) // if the target of the click isn\'t the container...
					&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					$("#overlay").hide();
					$("#overlay-content").html("");				
				}
			});
			</script>';
		echo '</div>';
	}
	
	private function buildEpisodeDisplay($epid)
	{
		$query = "SELECT series.id, series.fullSeriesName, episode.epnumber, episode.epprefix, episode.epname, episode.subGroup, episode.date, episode.image FROM episode, series WHERE series.seriesName=episode.seriesname AND episode.id = " . $this->mysqli->real_escape_string($epid);
		$results = $this->mysqli->query($query);
		
		if(!$results)
		{
			echo '<div align="center" style="padding:5px;font-weight:bold;">There was an issue with the request. ' . mysqli_error() . '</div>';
			exit;
		}
		$row = $results->fetch_assoc();
		echo '<div align="left" style="padding:5px;font-size:16px;">Episode ' . $row['epnumber']  . ' of ' . $row['fullSeriesName'] . ' - ' . $row['epname'] . ' </div>';
		$imagelink = "{$this->ImageHost}/video-images/{$row['id']}/{$epid}_screen.jpeg";
		if($row['image'] == 0)
		{ //If it doesn't have a image..
			$imagelink = $this->ImageHost."/video-images/noimage.png";
		}
		echo '<div><img src="' . $imagelink . '" alt="" style="width:80%;border:1px solid #000;" /></div>';
		echo '<div style="padding:5px;">
				<div style="display:inline-block;width:80%;" align="center">';
		
		// We need to make sure the user is logged in.
		if($this->UserArray[0] == 1)
		{
			// Next we check where this is coming from, the mobile site only supports the Android app presently.
			if(stristr($_SERVER['HTTP_USER_AGENT'],'tv.animeftw.android/3.0'))
			{
				// True, they are an android user, specified to be here.
				echo '		<input type="button" value=" Click to Play Episode " /></div>';
			}
			else
			{
				// They are not supported, so they need to move on.
				echo '		<div style="padding-bottom:10px;font-weight:bold;">Your Device is not Authorized to view content.<br /> Check out the Android app for more details.</div>';
			}
		}
		else
		{
			echo '<div>Please Login to Watch Videos</div>';
		}
		echo '</div>';
		echo '<div style="padding:5px;background-color:#ebebeb;">
				<div style="display:inline-block;width:20%;" align="right">Added:</div>
				<div style="display:inline-block;width:75%;" align="left">00/00/0000 00:00:00</div>
			</div>';
		echo '<div style="padding:5px;">
				<div style="display:inline-block;width:20%;" align="right">SubGroup:</div>
				<div style="display:inline-block;width:75%;" align="left">' . $row['subGroup'] . '</div>
			</div>';
		echo '<div style="padding:5px;background-color:#ebebeb;">
				<div style="display:inline-block;width:20%;" align="right">Comments:</div>
				<div style="display:inline-block;width:75%;" align="left">&nbsp;</div>
			</div>';
		echo $this->displayComments($epid);
		echo '<div style="padding:5px;background-color:#ebebeb;">
				<div style="display:inline-block;width:20%;" align="right">&nbsp;</div>
				<div style="display:inline-block;width:75%;" align="left">&nbsp;</div>
			</div>';
	}
	
	private function displayComments($epid)
	{
		$query = "SELECT users.Username, page_comments.comments, page_comments.dated, page_comments.isSpoiler FROM page_comments, users WHERE users.ID=page_comments.uid AND epid = " . $this->mysqli->real_escape_string($epid) . " ORDER BY page_comments.dated";
		$results = $this->mysqli->query($query);
		
		if(!$results)
		{
			echo '<div align="center" style="padding:5px;font-weight:bold;">There was an issue with the request. ' . mysqli_error() . '</div>';
			exit;
		}
		$numrow = mysqli_num_rows($results);
		$i = 0;
		$b = '';
		
		if($numrow > 3)
		{
			$b .= '<div style="height:200px;overflow-y:scroll;overflow-x:none;">';
		}
		while($row = $results->fetch_assoc())
		{
			if($i % 2)
			{
				$b .= '<div style="padding:5px;background-color:#ebebeb;">';
			}
			else
			{
				$b .= '<div style="padding:5px;">';
			}
			$b .= '
				<div style="display:inline-block;width:20%;word-wrap:break-word;vertical-align:top;" align="left">' . $row['Username'] . '</div>
				<div style="display:inline-block;width:75%;word-wrap:break-word;" align="left">' . stripslashes($row['comments']) . '</div>
			</div>';
			$i++;
		}
		if($numrow > 3)
		{
			$b .= '</div>';
		}
		return $b;
	}
	
	private function DisplayAnime()
	{
		$query = "SELECT id, seriesName, fullSeriesName, romaji, kanji, description, ratingLink, noteActivate, noteReason, category, prequelto, sequelto, total_reviews FROM series WHERE seoname = '" . $this->mysqli->real_escape_string($this->Seo) . "'";
		// we need to set the UTF8 format cause the database is latin by default
		$this->mysqli->query("SET NAMES 'utf8'");
		
		// get the results of the query
		$results = $this->mysqli->query($query);
		
		if(!$results)
		{
			// there are no results, due to an error somewhere, we must tell them!
			echo 'There was an error with your MySQL Query, the error was: ' . mysqli_error();
			exit;
		}
		
		// we have to count the amount of rows, if someone checks for an anime that doesnt exit (although they wont find it with our ajax), we need to let them know.
		$numrows = mysqli_num_rows($results);
		
		if($numrows < 1)
		{
			// tell them we wont take crap from no body!
			echo '<div align="center">There was no anime found by that name, please go back and try again.</div>';
		}
		else
		{
			$row = $results->fetch_assoc();
			// we had success, now we must show the love.
			echo '<div id="anime-wrapper" style="height:100%;margin:-20px;background-color:white;">
				<div id="anime-internal-wrapper">
					<div id="anime-header" style="margin-top:5px;">
						<div align="center">' . stripslashes($row['fullSeriesName']) . '</div> 
					</div>
					<div id="anime-info">
						<div id="anime-top-section">
							<div id="anime-info-img">
								<img src="' . $this->ImageHost . '/seriesimages/' . $row['id'] . '.jpg" style="width:100%;" />
							</div>
							<div id="anime-info-body">
								<div id="anime-info-wrapper"> 
									<div style="float:right;">
										<img src="//www.animeftw.tv/images/ratings/' . $row['ratingLink'] . '" alt="" />
									</div>
									<div>
										<div style="display:inline-block;vertical-align:top;font-weight:bold;">Ranked:</div>
										<div style="display:inline-block;vertical-align:top;">' . $this->FetchRanking($row['id']) . '</div>
									</div>
									<div>
										<div style="vertical-align:top;font-weight:bold;width:60px;">Kanji:</div>
										<div style="vertical-align:top;">' . $row['kanji'] . '</div>
									</div>
									<div>
										<div style="vertical-align:top;font-weight:bold;width:60px;">Romaji:</div>
										<div style="vertical-align:top;">' . $row['romaji'] . '</div>
									</div>
									<div>
										<div style="font-weight:bold;width:60px;">Genres:</div>
										<div> ' . $row['category'] . '</div>
									
									</div>
								</div>
							</div> 
							<div id="anime-description">
								<div id="description-wrapper">
									<div style="width:100%;border-bottom:1px solid #BFBFBF;">
										<div style="display:inline-block;width:49%;font-size:16px;font-weight:bold;">Synopsis:</div>
										<div style="display:inline-block;width:49%;" align="right">- minus -</div>
									</div>
									<div>' . stripslashes($row['description']) . '</div>
								</div>
							</div>
						</div>
						<div id="anime-body" style="width:100%">
							<div id="episode-wrapper">
								<div id="episode-header" style="width:100%;margin-bottom:3px;">
									<div style="font-size:16px;font-weight:bold;margin-left:20px;display:inline-block;width:100px;">Episodes:</div> 
									<div style="font-size:16px;font-weight:bold;margin-left:20px;display:inline-block;margin-top:-2px;float:right;padding-right:5px;font-size:10px;">Episode Jump: <input type="text" name="ep-search" style="width:30px;" id="episode-hotsearch" /></div>
								</div>
								<div id="episode-body">';
									return $this->DisplayEpisodes($row['seriesName'],stripslashes($row['fullSeriesName']));
								echo '
								</div>
							</div>
						</div>
					</div>
				</div>';
			
			
		}
		
	}
	
	private function DisplayEpisodes($seriesName,$fullSeriesName)
	{
		$query = "SELECT id, sid, epnumber, epname, seriesname, vidheight, vidwidth, epprefix, subGroup, Movie, doubleEp, date, uid, report, videotype, videoList, image, ova, hd FROM episode WHERE seriesname = '". $this->mysqli->real_escape_string($seriesName). "' AND Movie = 0 ORDER BY epnumber";
		$results = $this->mysqli->query($query);
		if(!$results)
		{
			// there are no results, due to an error somewhere, we must tell them!
			echo 'There was an error with your MySQL Query, the error was: ' . mysqli_error();
			exit;
		}
		$numrows = mysqli_num_rows($results);
		if($numrows < 1)
		{
			echo '<div>There was no episodes found for this series.</div>';
		}
		
		while($row = $results->fetch_assoc()) 
		{
			$imagelink = "{$this->ImageHost}/video-images/{$row['sid']}/{$row['id']}_screen.jpeg";
			if($row['image'] == 0)
			{ //If it doesn't have a image..
				$imagelink = $this->ImageHost."/video-images/noimage.png";
			}
			$videolink = 'http://videos.animeftw.tv/' . $seriesName . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $row['videotype'];
			if($row['Movie'] >= 1)
			{
				$videolink = 'http://videos.animeftw.tv/movies/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $row['videotype'];
			}
			$this->EpisodeLink($row['id'],$videolink,"Episode " . $row['epnumber'] . " of " . $fullSeriesName . " - " . $row['epname']);
			echo '
			<div class="ep-image-wrapper">
				<img src="' . $imagelink . '" alt="" style="width:100%;" />
				<div class="ep-image-description">
					<p class="ep-image-description-content">Episode #' . $row['epnumber'] . ': ' . $row['epname'] . '</p>
				</div> 
			</div>
			</a>';
		}
	}
	
	private function FetchRanking($sid)
	{
		$query = "SELECT lastPosition, currentPosition FROM site_topseries WHERE seriesId='" . $sid . "' ORDER BY currentPosition ASC ";
		$result = $this->mysqli->query($query);
		
		$numrows = mysqli_num_rows($result);
		if($numrows < 1)
		{
			$Rank = 'N/A';
		}
		else
		{		
			$row = $result->fetch_assoc();
			$lastPosition = $row['lastPosition'];
			$currentPosition = $row['currentPosition'];
			$singleRank = '';
			
			if($currentPosition < $lastPosition)
			{
				$Rank = $currentPosition.'&nbsp;<img src="' . $this->ImageHost . '/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: '.$lastPosition.'" />';
			}
			else if ($currentPosition == $lastPosition)
			{
				$Rank = $currentPosition.'&nbsp;<img src="' . $this->ImageHost . '/arrow_none.gif" title="Rank Unchanged, Previous Rank: '.$lastPosition.'" alt="" />';
			}
			else 
			{
				$Rank = $currentPosition.'&nbsp;<img src="' . $this->ImageHost . '/arrow_down.gif" alt="" title="Rank Went Down, Previous Rank: '.$lastPosition.'" />';
			}
		}		
		return $Rank;
	}
	
	private function EpisodeLink($epid,$link,$title)
	{
		if(stristr($_SERVER['HTTP_USER_AGENT'],'tv.animeftw.android/3.0') || $this->UserArray[2] == 1)
		{
			echo '<a href="#" onClick="openOverlay(\'' . $epid . '\'); return false;" style="position:relative;z-index:-1;">';
		}
		else
		{
			echo '<a href="#" onClick="openOverlay(\'' . $epid . '\'); return false;" style="position:relative;z-index:-1;">';
			//echo '<a href="#" onClick="alert(\'' . $this->UserArray[2] . '\'); return false;">';
		}
	}
}

?>