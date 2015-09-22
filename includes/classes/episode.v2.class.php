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
					else if($key == 'seriesname' || $key == 'html5' || $key == 'sid')
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
				return array('status' => $this->MessageCodes["Result Codes"]["03-401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["03-400"]["Message"]);
			}
		}
		else
		{
			// Nothing matched the information give, send back to them.
			return array('status' => $this->MessageCodes["Result Codes"]["03-400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["03-400"]["Message"]);
		}
	}
	
	
	public function array_displayEpisodes()
	{
		if(isset($this->Data['id']) && is_numeric($this->Data['id']))
		{
			// vars
			$finalresults = array();
			
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
			// change to UTF-8 so we can use kanji and romaji
			$this->mysqli->query("SET NAMES 'utf8'");
			$query = "SELECT `episode`.`id`, `episode`.`sid`, `episode`.`spriteId`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `series`.`seriesname`, `sprites`.`width` as spriteWidth, `sprites`.`height` as spriteHeight, `sprites`.`totalWidth` as spriteTotalWidth, `sprites`.`rate` as spriteRate, `sprites`.`count` as spriteCount FROM `" . $this->MainDB . "`.`episode`, LEFT JOIN `{$this->MainDB}`.`sprites` ON `sprites`.`id` = `episode`.`spriteId`, `" . $this->MainDB . "`.`series` WHERE `episode`.`sid` = " . $this->mysqli->real_escape_string($this->Data['id']) . " AND `series`.`id`=`episode`.`sid` ORDER BY `episode`.`epnumber` LIMIT $startpoint, $count";
			//execute the query
			$result = $this->mysqli->query($query);
			
			$finalresults = array();
			// add the series specific info to the output
			$finalresults['series-id'] = $this->Data['id']; // supply the series id
			$finalresults['total-episodes'] = $this->bool_totalEpisodeAvailable($this->Data['id']); // total episodes in this series
			$finalresults['count'] = $count; // supply the count
			$finalresults['start'] = $startpoint; // supply the count
			
			$count = $result->num_rows;
			if($count > 0)
			{
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
							$finalresults['results'][$i]['image'] = "{$this->ImageHost}/video-images/{$row['sid']}/{$row['id']}_screen.jpeg";
						}
						else if($key == "spriteId" && $value != null)
						{
							$finalresults['results'][$i]['sprite'] = [
								"image"	=> "{$this->ImageHost}/video-images/{$row['sid']}/{$row['id']}_screen.jpeg",
								"width" => $row['spriteWidth'],
								"height" => $row['spriteHeight'],
								"total-width" => $row['spriteTotalWidth'],
								"rate"	=> $row['spriteRate'],
								"count"	=> $row['spriteCount'],
							];
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
						else if($key == 'seriesname' || $key == 'html5' || $key == 'sid')
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
				return array('status' => $this->MessageCodes["Result Codes"]["03-401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["03-401"]["Message"], 'debug' => $this->mysqli->error, "sql" => $query);
			}
		}
		else
		{
			// Nothing matched the information give, send back to them.
			return array('status' => $this->MessageCodes["Result Codes"]["03-400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["03-400"]["Message"]);
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
						return array('status' => $this->MessageCodes["Result Codes"]["201"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["201"]["Message"]);
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
						return array('status' => $this->MessageCodes["Result Codes"]["201"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["201"]["Message"]);
					}
				}
			}
		}
		else
		{
			// missing some of the data.
			return array('status' => $this->MessageCodes["Result Codes"]["03-402"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["03-402"]["Message"]);
		}
	}
}