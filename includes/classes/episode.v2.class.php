<?php
/****************************************************************\
## FileName: episode.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Series Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Episode extends Config {

	public $Data, $UserID, $DevArray, $MessageCodes, $UserArray;
	
	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL,$DirectUserArray = NULL)
	{
		parent::__construct(TRUE);
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
			$query = "SELECT `episode`.`id`, `episode`.`sid`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `series`.`seriesName` FROM `" . $this->MainDB . "`.`episode`, `" . $this->MainDB . "`.`series` WHERE `episode`.`id` = " . $this->mysqli->real_escape_string($this->Data['id']) . " AND `series`.`id`=`episode`.`sid`";
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
						$results['image'] = $this->ImageHost . '/video-images/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
						$results['image-160x140'] = $this->ImageHost . '/video-images/160x140/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';	
						$results['image-320x280'] = $this->ImageHost . '/video-images/320x280/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';	
						$results['image-640x560'] = $this->ImageHost . '/video-images/640x560/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';					
					}
					else if($key == 'hd')
					{
						if($value == 2)
						{
							$results['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
							$results['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
							$results['video-1080p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_1080p_' . $row['epnumber'] . '_ns.mp4';
						}
						else if($value == 1)
						{
							$results['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
							$results['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
						}
						else
						{
							$results['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $videotype;
						}
					}
					else if($key == 'seriesName' || $key == 'sid')
					{
						// we don't need this..
					}
					else
					{
						$results[$key] = $value;
					}
				}
				$currentPositionArray = $this->findCurrentVideoLocation();
				$results['video-position'] = $currentPositionArray['position'];
				$results['last-watched'] = $currentPositionArray['last-watched'];
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
			$columns = "`episode`.`id`, `episode`.`sid`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`Movie`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`, `series`.`seriesName`, `series`.`fullSeriesName`";
			$orderBy = "`episode`.`date` DESC";
		}
		else {
			$latest = "";
			if((isset($this->DevArray['ads']) && $this->DevArray['ads'] == 0) && $this->AccessLevel == 3){
				// developer does not have ads enabled, so we will need to limit them.
				$startpoint = 0;
				$count = 2;
				$addonEpisode = '{"id":"13268","sid":"700","epname":"Chii Seeks.","epnumber":"1","vidheight":"540","vidwidth":"720","epprefix":"chisnewadress","subGroup":"Eiga","Movie":"0","videotype":"mp4","image":"http://img02.animeftw.tv/video-images/700/13268_screen.jpeg","video":"http://videos.animeftw.tv/chisnewaddress/chisnewadress_1_ns.mp4","views":"0","spriteWidth":null,"spriteHeight":null,"spriteTotalWidth":null,"spriteRate":null,"spriteCount":null,"total-comments":"0","average-rating":0,"user-rated":-1}';
				$addonEpisode = array('id' => '0','sid' => '0','epname' => 'You must be an Advanced Member to see more than 2 episodes.','epnumber' => '0','vidheight' => '0','vidwidth' => '0','epprefix' => '0','subGroup' => '0','Movie' => '0','videotype' => 'mp4','image' => '','video' => '','views' => '0','spriteWidth' => 'null','spriteHeight' => 'null','spriteTotalWidth' => 'null','spriteRate' => '0','spriteCount' => '0','total-comments' => '0','average-rating' => '0','user-rated' => '0');
			}
		}
		if(isset($this->Data['latest']) || isset($this->Data['id'])) {
			// Either this is a single series or the latest episodes listing, having neither is impossible.
			// change to UTF-8 so we can use kanji and romaji
			
			// Create the Join statement, and append the Sprite data onto the $columns variable
			// These are always needed for Episode data?
			$spritesJoin = "LEFT JOIN `{$this->MainDB}`.`sprites` ON `sprites`.`id` = `episode`.`spriteId`";
			$columns .= ", `sprites`.`width` as spriteWidth, `sprites`.`height` as spriteHeight, `sprites`.`totalWidth` as spriteTotalWidth, `sprites`.`rate` as spriteRate, `sprites`.`count` as spriteCount";
			
			$this->mysqli->query("SET NAMES 'utf8'");
			$query = "SELECT " . $columns . " FROM `" . $this->MainDB . "`.`episode`" . $spritesJoin . ", `" . $this->MainDB . "`.`series` WHERE `series`.`id`=`episode`.`sid`" . $where . " ORDER BY " . $orderBy . " LIMIT $startpoint, $count";
			
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
				if((isset($this->DevArray['ads']) && $this->DevArray['ads'] == 0) && $this->AccessLevel == 3 && !isset($this->Data['latest'])){
					$i = 1;
					$finalresults['results'][0] = $addonEpisode;
				}
				while($row = $result->fetch_assoc())
				{
					// a result was found, build the array for return.
					$videotype = $row['videotype'];
					$Ratings = $Rating->array_ratingsInformation($row['id'],$this->UserID);
					foreach($row AS $key => &$value)
					{
						if($key == 'image')
						{
							$finalresults['results'][$i]['image'] = $this->ImageHost . '/video-images/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
							$finalresults['results'][$i]['image-160x140'] = $this->ImageHost . '/video-images/160x140/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
							$finalresults['results'][$i]['image-320x280'] = $this->ImageHost . '/video-images/320x280/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
							$finalresults['results'][$i]['image-640x560'] = $this->ImageHost . '/video-images/640x560/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
						}
						else if($key == 'hd')
						{
							if($value == 2)
							{
								$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
								$finalresults['results'][$i]['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
								$finalresults['results'][$i]['video-1080p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_1080p_' . $row['epnumber'] . '_ns.mp4';
							}
							else if($value == 1)
							{
								$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
								$finalresults['results'][$i]['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
							}
							else
							{
								$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $videotype;
							}
						}
						else if($key == 'seriesName')
						{
							// we don't need this..
						}
						else
						{
							$finalresults['results'][$i][$key] = $value;
						}
					}
					$currentPositionArray = $this->findCurrentVideoLocation($row['id']);
					$finalresults['results'][$i]['video-position'] = $currentPositionArray['position'];
					$finalresults['results'][$i]['last-watched'] = $currentPositionArray['last-watched'];
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
	
	private function bool_totalMoviesAvailable(){
		$query = "SELECT COUNT(id) as count FROM episode WHERE `Movie` = 1";
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
					// for those times we want to get the full length of the video to store in seconds.
					$maxlength = "";
					if((isset($this->Data['max']) && is_numeric($this->Data['max'])) || (isset($this->Data['length']) && is_numeric($this->Data['length'])))
					{
						if(isset($this->Data['max']))
						{
							$maxlength = ", `max` = '" . $this->mysqli->real_escape_string($this->Data['max']) . "'";
						}
						else
						{
							$maxlength = ", `max` = '" . $this->mysqli->real_escape_string($this->Data['length']) . "'";
						}
					}
					// there are rows... lets update.
					$query = "UPDATE `episode_timer` SET `time` = " . $this->mysqli->real_escape_string($this->Data['time']) . ", `updated` = " . time() . $maxlength . " WHERE `uid` = " . $this->UserID . " AND `eid` = " . $this->mysqli->real_escape_string($this->Data['id']);
					$result = $this->mysqli->query($query);
					if(!$result)
					{
						return array('status' => $this->MessageCodes["Result Codes"]["401"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["401"]["Message"]);
					}
					else
					{
						return array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => "Action Successful.");
					}
				}
				else
				{
					// for those times we want to get the full length of the video to store in seconds.
					$maxdurration = "";
					$maxvalue = "";
					if((isset($this->Data['max']) && is_numeric($this->Data['max'])) || (isset($this->Data['length']) && is_numeric($this->Data['length'])))
					{
						$maxdurration = ", `max`";
						if(isset($this->Data['max']))
						{
							$maxvalue = ", " . $this->Data['max'];
						}
						else
						{
							$maxvalue = ", " . $this->Data['length'];
						}
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
						return array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => "Action Successful.");
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
	
	public function array_displayMovies(){
		// vars
		$finalresults = array();
		$where = " AND `episode`.`Movie` = 1";
		$orderBy = "`series`.`fullSeriesName` ASC, `episode`.`epnumber` DESC";
		$columns = "`episode`.`id`, `series`.`fullSeriesName`, `episode`.`sid`, `episode`.`epname`, `episode`.`epnumber`, `episode`.`vidheight`, `episode`.`vidwidth`, `episode`.`epprefix`, `episode`.`subGroup`, `episode`.`videotype`, `episode`.`image`, `episode`.`hd`, `episode`.`views`";
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
			// no reason to worry, we just need to give them the list with the alphabetical listing.
		}
		// Add support for viewing the last X movies added.
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
				$where .= " AND `episode`.`date` >= " . $this->mysqli->real_escape_string($finalTime);
			}
			else {
			}
			$orderBy = "`episode`.`date` DESC";
		}
		else {
			$latest = "";
			if((isset($this->DevArray['ads']) && $this->DevArray['ads'] == 0) && $this->AccessLevel == 3){
				// developer does not have ads enabled, so they are not allowed to see 
				$startpoint = 0;
				$count = 0;
				$addonEpisode = array('id' => '0','sid' => '0','epname' => 'Due to restrictions, you are unable to view movies individually.','epnumber' => '0','vidheight' => '0','vidwidth' => '0','epprefix' => '0','subGroup' => '0','Movie' => '0','videotype' => 'mp4','image' => '','video' => '','views' => '0','spriteWidth' => 'null','spriteHeight' => 'null','spriteTotalWidth' => 'null','spriteRate' => '0','spriteCount' => '0','total-comments' => '0','average-rating' => '0','user-rated' => '0');
			}
		}
		// Either this is a single series or the latest episodes listing, having neither is impossible.
		// change to UTF-8 so we can use kanji and romaji
			
		// Create the Join statement, and append the Sprite data onto the $columns variable
		// These are always needed for Episode data?
		$spritesJoin = "LEFT JOIN `{$this->MainDB}`.`sprites` ON `sprites`.`id` = `episode`.`spriteId`";
		$columns .= ", `sprites`.`width` as spriteWidth, `sprites`.`height` as spriteHeight, `sprites`.`totalWidth` as spriteTotalWidth, `sprites`.`rate` as spriteRate, `sprites`.`count` as spriteCount";
			
		$this->mysqli->query("SET NAMES 'utf8'");
		$query = "SELECT " . $columns . " FROM `" . $this->MainDB . "`.`episode`" . $spritesJoin . ", `" . $this->MainDB . "`.`series` WHERE `series`.`id`=`episode`.`sid`" . $where . " ORDER BY " . $orderBy . " LIMIT $startpoint, $count";
		//execute the query
		$result = $this->mysqli->query($query);
			
		$finalresults = array();
			
		// add the series specific info to the output
		$count = $result->num_rows;
		if($count > 0)
		{
			$finalresults['status'] = $this->MessageCodes["Result Codes"]["200"]["Status"];
			$finalresults['total-movies'] = $this->bool_totalMoviesAvailable(); // total episodes in this series
			$finalresults['count'] = $count; // supply the count
			$finalresults['start'] = $startpoint; // supply the count
			// include the comment clas
			include_once("comments.v2.class.php");
			$Comment = new Comment(0);
			// include the rating class
			include_once("rating.v2.class.php");
			$Rating = new Rating();
			$i = 0;
			if((isset($this->DevArray['ads']) && $this->DevArray['ads'] == 0) && $this->AccessLevel == 3 && !isset($this->Data['latest'])){
				$i = 1;
				$finalresults['results'][0] = $addonEpisode;
			}
			while($row = $result->fetch_assoc())
			{
				// a result was found, build the array for return.
				$videotype = $row['videotype'];
				$Ratings = $Rating->array_ratingsInformation($row['id'],$this->UserID);
				foreach($row AS $key => &$value)
				{
					if($key == 'image')
					{
						$finalresults['results'][$i]['image'] = $this->ImageHost . '/video-images/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
						$finalresults['results'][$i]['image-160x140'] = $this->ImageHost . '/video-images/160x140/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
						$finalresults['results'][$i]['image-320x280'] = $this->ImageHost . '/video-images/320x280/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
						$finalresults['results'][$i]['image-640x560'] = $this->ImageHost . '/video-images/640x560/' . $row['sid'] . '/' . $row['id'] . '_screen.jpeg';
					}
					else if($key == 'hd')
					{
						if($value == 2)
						{
							$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
							$finalresults['results'][$i]['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
							$finalresults['results'][$i]['video-1080p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_1080p_' . $row['epnumber'] . '_ns.mp4';
						}
						else if($value == 1)
						{
							$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.mp4';
							$finalresults['results'][$i]['video-720p'] = 'http://videos2.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_720p_' . $row['epnumber'] . '_ns.mp4';
						}
						else
						{
							$finalresults['results'][$i]['video'] = 'http://videos.animeftw.tv/' . $row['seriesName'] . '/' . $row['epprefix'] . '_' . $row['epnumber'] . '_ns.' . $videotype;
						}
					}
					else if($key == 'seriesName')
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
	
	private function findCurrentVideoLocation($eid=null) {
        if($id == null){
            // the eid is not set, so we set it for them
            $eid = $this->Data['id'];
        }
		$query = "SELECT `time`, `updated`, `max` FROM `" . $this->MainDB . "`.`episode_timer` WHERE `uid` = " . $this->UserID . " AND `eid` = '" . $this->mysqli->real_escape_string($eid) . "'";
		$result = $this->mysqli->query($query);
		
		// add the series specific info to the output
		$count = $result->num_rows;
		
		if($count > 0) {
			$row = $result->fetch_assoc();
			
			// we need to make sure that the current entry is more than 90% watched, if it is not, then we give them the current time.
			if($row['max'] == NULL) {
				return array('position' => $row['time'], 'last-watched' => $row['updated']);
			}
			else {
				$percentage = round(($row['time']/$row['max'])*100);
				if($percentage >= 90) {
					return array('position' => '0', 'last-watched' => $row['updated']);
				}
				else {
					return array('position' => $row['time'], 'last-watched' => $row['updated']);
				}
			}
		}
		else {
			return array('position' => '0', 'last-watched' => '0');
		}
	}
}
