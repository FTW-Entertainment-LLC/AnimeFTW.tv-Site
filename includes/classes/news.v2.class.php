<?php
/****************************************************************\
## FileName: news.v2.class.php									 
## Author: Brad Riemann										 
## Usage: News Class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class News extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes;
	private $AdvanceRestrictions;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
	}
	
	public function array_showLatestNews()
	{
		$query = "SELECT t.tid, t.ttitle, t.tpid, t.tfid, t.tdate, p.pbody, f.ftitle, f.fseo FROM forums_threads as t, forums_post as p, forums_forum as f 
		WHERE (t.tfid='1' OR t.tfid='2' OR t.tfid='9'" . $addonquery . ") AND p.pistopic='1' AND p.puid=t.tpid AND p.ptid=t.tid AND f.fid=t.tfid ORDER BY t.tid DESC LIMIT 0, 8";
		$result = $this->mysqli->query($query);
		
		$returndata = array('status' => $this->MessageCodes["Result Codes"]["201"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["201"]["Message"]);
		$count = $result->num_rows;
		if($count > 0)
		{
			$i = 0;
			while($row = $result->fetch_assoc())
			{
				$FancyUsername = $this->string_fancyUsername($row['tpid'],NULL,NULL,NULL,NULL,NULL,TRUE,TRUE);
				$returndata['results'][$i]['poster'] = $FancyUsername[0];
				$returndata['results'][$i]['poster-avatar'] = $FancyUsername[1];
				$returndata['results'][$i]['topic-id'] = $row['tid'];
				$returndata['results'][$i]['topic-title'] = $row['ttitle'];
				$returndata['results'][$i]['forum-id'] = $row['tfid'];
				$returndata['results'][$i]['date'] = $row['tdate'];
				$returndata['results'][$i]['body'] = $row['pbody'];
				$i++;
			}
		}
		else
		{
		}
		return $returndata;
	}
}
