<?php
/****************************************************************\
## FileName: episode-updates.class.php	
## Type: Class								 
## Author: Brad Riemann										 
## Usage: Parses Episode updates, from image creations, video updates
## full series updates (images) to full muxing.
## Copyright 2014 FTW Entertainment LLC, All Rights Reserved
## Updated: 10/19/2014 by robotman321
## Version: 1.0.0
\****************************************************************/

require_once("/home/mainaftw/public_html/includes/classes/config.v2.class.php");

class EpisodeUpdates extends Config {

	var $CronID,$DataSet = array();
	
	public function __construct()
	{
		parent::__construct();
		
		// Set Class variables.
		$this->CronID 				= 14;
		
		// Initialize the Script
		$this->scriptInit();
	}
	
	private function scriptInit()
	{
		// Build the array with the data we need to crunch.
		if($this->bool_buildDataArray() == 1)
		{
			// there are rows to process.. PROCEED!
			$this->parseEpisodeData();
		}
		else
		{
			// it returned a zero, we need to close out the script
			$this->recordCronjob();
		}
	}
	
	private function bool_buildDataArray()
	{
		// we want to query the database to see if there is anything to do.
		$query = "SELECT * FROM `" . $this->MainDB . "`.`episode_updates` WHERE `status` = 0 AND `date_completed` = 0";
		$result = $this->mysqli->query($query);
		
		$count = mysqli_num_rows($result);
		
		if($count > 0)
		{
			$i = 0;
			while($row = $result->fetch_assoc())
			{
				$this->DataSet[$i]['id'] = $row['id'];
				$this->DataSet[$i]['eid'] = $row['eid'];
				$this->DataSet[$i]['date'] = $row['date'];
				$this->DataSet[$i]['type'] = $row['type'];
				$this->DataSet[$i]['status'] = $row['status'];
				$this->DataSet[$i]['date_completed'] = $row['date_completed'];
				$i++;
			}
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	private function parseEpisodeData()
	{
		/*
		* We will now loop through the data given to us.
		* 0 - Episode image creation
		* 1 - Episode Video Creation (480p)
		* 2 - Episode Video Creation (720p)
		* 3 - Episode Video Creation (1080p)
		* 4 - Full Series Reencode, includes all 720/1080p and must ONLY be in the /new/ folder to work..
		* 5 - Full Image updates.
		*/
	
		foreach($this->DataSet as $Entry)
		{
			if($Entry['type'] == 0)
			{
				$this->executeImageCreation($Entry);
			}
			else if($Entry['type'] == 1)
			{
				$this->execute480pCreation($Entry);
			}
			else if($Entry['type'] == 2)
			{
				$this->execute720pCreation($Entry);
			}
			else if($Entry['type'] == 3)
			{
				$this->execute1080pCreation($Entry);
			}
			else if($Entry['type'] == 4)
			{
				$this->executeFullSeriesRemux($Entry);
			}
			else if($Entry['type'] == 5)
			{
				$this->executeFullSeriesImageUpdate($Entry);
			}
			else
			{
			}
		}
	}
	
	private function executeImageCreation($Entry)
	{
		// Create the image for a video..
		$query = "SELECT `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`, `series`.`seriesname`, `series`.`videoServer` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id` = '" . $Entry['eid'] . "'";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'http://videos.animeftw.tv/fetch-pictures-v2.php?node=add-image&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		//echo $url;
		$UpdateResult = $this->remoteProcedureCall($url);
		if($UpdateResult == "Succcess")
		{
			$this->updateEntry(1,$Entry['id'],NULL);
		}
		else
		{
			$this->updateEntry(0,$Entry['id'],$UpdateResult);
		}
	}
	
	private function execute480pCreation($Entry)
	{
		// mux a 480p video
		$query = "SELECT `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`, `series`.`seriesname`, `series`.`videoServer` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id` = '" . $Entry['eid'] . "'";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'http://videos.animeftw.tv/fetch-pictures-v2.php?node=mux-480p-video&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		//echo $url;
		$UpdateResult = $this->remoteProcedureCall($url);
		if($UpdateResult == "Succcess")
		{
			$this->updateEntry(1,$Entry['id'],NULL);
		}
		else
		{
			$this->updateEntry(0,$Entry['id'],$UpdateResult);
		}
	}
	
	private function execute720pCreation($Entry)
	{
		// mux a 480p video
		$query = "SELECT `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`, `series`.`seriesname`, `series`.`videoServer` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id` = '" . $Entry['eid'] . "'";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'http://videos.animeftw.tv/fetch-pictures-v2.php?node=mux-720p-video&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		//echo $url;
		$UpdateResult = $this->remoteProcedureCall($url);
		if($UpdateResult == "Succcess")
		{
			$this->updateEntry(1,$Entry['id'],NULL);
		}
		else
		{
			$this->updateEntry(0,$Entry['id'],$UpdateResult);
		}
	}
	
	private function execute1080pCreation($Entry)
	{
		// mux a 480p video
		$query = "SELECT `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`, `series`.`seriesname`, `series`.`videoServer` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`id` = '" . $Entry['eid'] . "'";
		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();
		$url = 'http://videos.animeftw.tv/fetch-pictures-v2.php?node=mux-1080p-video&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
		//echo $url;
		$UpdateResult = $this->remoteProcedureCall($url);
		if($UpdateResult == "Succcess")
		{
			$this->updateEntry(1,$Entry['id'],NULL);
		}
		else
		{
			$this->updateEntry(0,$Entry['id'],$UpdateResult);
		}
	}
	
	private function executeFullSeriesRemux($Entry)
	{
		// mux a 480p video
		$query = "SELECT `episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`, `episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`, `series`.`seriesname`, `series`.`videoServer` FROM `episode`, `series` WHERE `series`.`id`=`episode`.`sid` AND `episode`.`sid` = '" . $Entry['eid'] . "'";
		$result = $this->mysqli->query($query);
		$UpdateResult = '';
		while($row = $result->fetch_assoc())
		{
			$url = 'http://videos.animeftw.tv/fetch-pictures-v2.php?node=mux-1080p-video&seriesName=' . $row['seriesname'] . '&epprefix=' . $row['epprefix'] . '&epnumber=' . $row['epnumber'] . '&durration=360&vidwidth=' . $row['vidwidth'] . '&vidheight=' . $row['vidheight'] . '&videotype=' . $row['videotype'] . '&movie=' . $row['Movie'];
			//echo $url;
			$UpdateResult .= $this->remoteProcedureCall($url);
		}
		
		if($UpdateResult == "Succcess")
		{
			$this->updateEntry(1,$Entry['id'],NULL);
		}
		else
		{
			$this->updateEntry(0,$Entry['id'],$UpdateResult);
		}
	}
	
	private function executeFullSeriesImageUpdate($Entry)
	{
	}
	
	private function remoteProcedureCall($url)
	{
		$file = file_get_contents($url);
		echo $file;
	}
	
	private function updateEntry($status,$id,$results)
	{
		if($status == 1)
		{
			// Success
			$status = 2;
			$addon = '';
		}
		else
		{	
			// fail
			$status = 1;
			$addon = " , `details` = '$results'";
		}
		$query = "UPDATE `" . $this->MainDB . "`.`episode_updates` SET `status` = " . $status . ", `date_completed` = " . time() . $addon . " WHERE `id` = " . $id;
		$result = $this->mysqli->query($query);
	}
	
	private function recordCronjob()
	{
		echo 'nothing to do here..';
	}
}

$EpisodeUpdates = new EpisodeUpdates();