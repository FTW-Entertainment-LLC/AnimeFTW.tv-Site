<?php
/****************************************************************\
## FileName: episode.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Series Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Episode extends Config {

	public $Data, $UserID, $DevArray, $MessageCodes;
	
	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL,$DirectUserArray = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		// check if the data is null, we will override settings that would normally be reserved for the API.
		if($UserID == NULL)
		{
			$this->UserID = $this->UserArray['ID'];
		}
		else
		{
			$this->UserID = $UserID;
		}
		$this->DevArray = $DevArray;
		// again, for non-api driven model, we want to use the same information, per-say, so let's override this.
		if($AccessLevel == NULL)
		{
			$this->AccessLevel = $this->UserArray['Level_access'];
		}
		else
		{	
			$this->AccessLevel = $AccessLevel;
		}
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
	}
	
	public function array_displaySingleEpisode()
	{
		if(isset($this->Data['id']) && is_numeric($this->Data['id']))
		{
			$this->mysqli->query("SET NAMES 'utf8'");
			$query = "SELECT `episode`.`id`, `episode`.`sid`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `series`.`seriesname` FROM `" . $this->MainDB . "`.`episode`, `" . $this->MainDB . "`.`series` WHERE `episode`.`id` = " . $this->mysqli->real_escape_string($this->Data['id']) . " AND `series`.`id`=`episode`.`sid`";
			$result = $this->mysqli->query($query);
			
			$count = $result->num_rows;
			if($count > 0)
			{
				// include the comment information
				include_once("comments.v2.class.php");
				$Comment = new Comment(0);
				include_once("rating.v2.class.php");
				$Rating = new Rating();
				$row = $result->fetch_assoc();
				// a result was found, build the array for return.
				$results = '';
				$videotype = $row['videotype'];
				$results['status'] = $this->MessageCodes["Result Codes"]["200"]["Status"];
				$Ratings = $Rating->array_ratingsInformation($row['id'],$this->UserID);
				foreach($row AS $key => &$value)
				{
					if($key == 'image')
					{
						$results['image'] = $this->ImageHost . 'video-images/' . $row['epprefix'] . '_' . $row['epnumber'] . '_screen.jpeg';						
					}
					else if($key == 'hd')
					{
						if($value == 2)
						{
							$results['video'] = 'http://videos.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
							$results['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
							$results['video-1080p'] = 'http://videos2.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_1080p_' . $row['epnumber'] . '_ns.mp4';
						}
						else if($value == 1)
						{
							$results['video'] = 'http://videos.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
							$results['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
						}
						else
						{
							$results['video'] = 'http://videos.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $videotype;
						}
					}
					else if($key == 'seriesname' || $key == 'sid')
					{
						// we don't need this..
					}
					else
					{
						$results[$key] = $value;
					}
				}
				$results['total-comments'] = $Comment->bool_totalComments($this->Data['id']);
				$results['average-rating'] = $Ratings['average-rating']; // pass through the average rating.
				$results['user-rated'] = $Ratings['user-rated']; // put through if the user rated or not, if they did it will be the value they submitted.
				// add the episode image
				return $results;
			}
			else
			{
				return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);
			}
		}
		else
		{
			// Nothing matched the information give, send back to them.
			return array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);
		}
	}
	
	
	public function array_displayEpisodes()
	{
		// vars
		$finalresults = array();
		$where = "";
		$orderBy = "`episode`.`epnumber`";
		$columns = "`episode`.`id`, `episode`.`sid`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `series`.`seriesName`";
		// limit the query by a certain amount
		if(isset($this->Data['count']) && is_numeric($this->Data['count']))
		{
			$count = $this->Data['count'];
		}
		else
		{
			$count = 30;
		}
		// The start point
		if(isset($this->Data['start']) && is_numeric($this->Data['start']))
		{
			$startpoint = $this->Data['start'];
		}
		else
		{
			$startpoint = 0;
		}
		// we check if the ID is set, if it is we can assume this is for a specific series.
		if(isset($this->Data['id']) && is_numeric($this->Data['id']))
		{
			$where .=" AND `episode`.`sid` = " . $this->mysqli->real_escape_string($this->Data['id']);
		}
		else
		{
			// no reason to worry, we just need to filter by latest episode now.. 
		}
		// Add support for viewing the last X videos added.
		if(isset($this->Data['latest'])) {
			// latest is set, we will limit them to the latest ## episodes by default.
			// Unless they use the timeframe flag, which will allow them to specify a time frame from the current time
			// that they wish to pull down episodes from.
			if(isset($this->Data['timeframe'])) {
				// They can use m, s  or h at the end, this way we can do &timeframe=15m or timeframe=60s
				$timeType = substr($this->Data['timeframe'], -1);
				$timeFrame = substr($this->Data['timeframe'], 0, -1);
				if(strtolower($timeType) == 'm') {
					// Minutes timeframe.
					$finalTime = time()-($timeFrame*60);
				}
				elseif(strtolower($timeType) == 'h') {
					// hours
					$finalTime = time()-($timeFrame*60*60);
				}
				else {
					// seconds is the default, we will not accept anything else.
					$finalTime = time()-$timeFrame;
				}
				$where .= " AND `date` >= " . $this->mysqli->real_escape_string($finalTime);
			}
			else {
			}
			$columns = "`episode`.`id`, `episode`.`sid`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `series`.`fullSeriesName`, `series`.`seoname`, `series`.`seriesName`";
			$orderBy = "`episode`.`date` DESC";
		}
		else {
			$latest = "";
		}
		if(isset($this->Data['latest']) || isset($this->Data['id'])) {
			// Either this is a single series or the latest episodes listing, having neither is impossible.	
			// change to UTF-8 so we can use kanji and romaji
			$this->mysqli->query("SET NAMES 'utf8'");
			$query = "SELECT " . $columns . " FROM `" . $this->MainDB . "`.`episode`, `" . $this->MainDB . "`.`series` WHERE `series`.`id`=`episode`.`sid`" . $where . " ORDER BY " . $orderBy . " LIMIT $startpoint, $count";
			//execute the query
			$result = $this->mysqli->query($query);
				
			$finalresults = array();
			// add the series specific info to the output
				
			$count = $result->num_rows;
			if($count > 0)
			{
				$finalresults['status'] = $this->MessageCodes["Result Codes"]["200"]["Status"];
				$finalresults['series-id'] = $this->Data['id']; // supply the series id
				$finalresults['total-episodes'] = $this->bool_totalEpisodeAvailable($this->Data['id']); // total episodes in this series
				$finalresults['count'] = $count; // supply the count
				$finalresults['start'] = $startpoint; // supply the count
				// include the comment clas
				include_once("comments.v2.class.php");
				$Comment = new Comment(0);
				// include the rating class
				include_once("rating.v2.class.php");
				$Rating = new Rating();
				$i = 0;
				while($row = $result->fetch_assoc())
				{
					// a result was found, build the array for return.
					$videotype = $row['videotype'];
					$Ratings = $Rating->array_ratingsInformation($row['id'],$this->UserID);
					foreach($row AS $key => &$value)
					{
						if($key == 'image')
						{
							$finalresults['results'][$i]['image'] = $this->ImageHost . '/video-images/' . $row['epprefix'] . '_' . $row['epnumber'] . '_screen.jpeg';						
						}
						else if($key == 'hd')
						{
							if($value == 2)
							{
								$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
								$finalresults['results'][$i]['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
								$finalresults['results'][$i]['video-1080p'] = 'http://videos2.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_1080p_' . $row['epnumber'] . '_ns.mp4';
							}
							else if($value == 1)
							{
								$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
								$finalresults['results'][$i]['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
							}
							else
							{
								$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesname'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $videotype;
							}
						}
						else if($key == 'seriesname')
						{
							// we don't need this..
						}
						else
						{
							$finalresults['results'][$i][$key] = $value;
						}
					}
					$finalresults['results'][$i]['total-comments'] = $Comment->bool_totalComments($row['id']);
					$finalresults['results'][$i]['average-rating'] = $Ratings['average-rating']; // pass through the average rating.
					$finalresults['results'][$i]['user-rated'] = $Ratings['user-rated']; // put through if the user rated or not, if they did it will be the value they submitted.
					$i++;
				}
				return $finalresults;
			}
			else
			{
				return array('status' => $this->MessageCodes["Result Codes"]["404"]["Status"], 'message' => "No Results Found.");
			}
		}
		else {
			return array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);
		}
	}
	
	private function bool_totalEpisodeAvailable($sid = NULL)
	{
		if($sid != NULL)
		{
			$whereclause = " WHERE `sid` = $sid";
		}
		else
		{
			$whereclause = "";
		}
		$query = "SELECT COUNT(id) as count FROM episode" . $whereclause . "";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		return $row['count'];
	}
	
	public function array_recordEpisodeTime()
	{
		// check to make sure the ID is set, this would be of the episode.
		// also check for a time variable, it MUST be in seconds.
		if(isset($this->Data['id']) && is_numeric($this->Data['id']) && isset($this->Data['time']) && is_numeric($this->Data['time']) && $this->AccessLevel != 0)
		{
			$query = "SELECT `id` FROM `episode_timer` WHERE `uid` = " . $this->UserID . " AND `eid` = " . $this->mysqli->real_escape_string($this->Data['id']);
			$result = $this->mysqli->query($query);
			if(!$result)
			{
				return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["401"]["Message"]);
			}
			else
			{
				$count = mysqli_num_rows($result);
				if($count > 0)
				{
					// there are rows... lets update.
					$query = "UPDATE `episode_timer` SET `time` = " . $this->mysqli->real_escape_string($this->Data['time']) . ", `updated` = " . time() . " WHERE `uid` = " . $this->UserID . " AND `eid` = " . $this->mysqli->real_escape_string($this->Data['id']);
					$result = $this->mysqli->query($query);
					if(!$result)
					{
						return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["401"]["Message"]);
					}
					else
					{
						return array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["200"]["Message"]);
					}
				}
				else
				{
					// for those times we want to get the full length of the video to store in seconds.
					$maxdurration = "";
					$maxvalue = "";
					if(isset($this->Data['max']) && is_numeric($this->Data['max']))
					{
						$maxdurration = ", `max`";
						$maxvalue = ", " . $this->Data['max'];
					}
					// all data is present, we need to record in the episode table.
					$query = "INSERT INTO `episode_timer` (`id`, `uid`, `eid`, `time`, `updated`" . $maxdurration . ") VALUES (NULL, " . $this->UserID . ", " . $this->mysqli->real_escape_string($this->Data['id']) . ", " . $this->mysqli->real_escape_string($this->Data['time']) . ", " . time() . $maxvalue .")";
					$result = $this->mysqli->query($query);
					if(!$result)
					{
						return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["401"]["Message"]);
					}
					else
					{
						return array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["200"]["Message"]);
					}
				}
			}
		}
		else
		{
			// missing some of the data.
			return array('status' => $this->MessageCodes["Result Codes"]["402"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["402"]["Message"]);
		}
	}
}
