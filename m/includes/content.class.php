<?php
/****************************************************************\
## FileName: content.class.php									 
## Author: Brad Riemann										 
## Usage: Content Constructor class for the mobile website.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Content extends Config {

	var $Page, $ErrorPages;

	public function __construct($Page)
	{
		$this->Page = $Page;
		parent::__construct();
		$this->buildErrorPages();
	}	
	
	public function Init()
	{
		$this->buildPage();
	}
	
	private function buildPage()
	{
		$query = "SELECT permissions, full_page_name, body FROM content WHERE node = '" . $this->mysqli->real_escape_string($this->Page) . "' LIMIT 0, 1";
		$results = $this->mysqli->query($query);
		
		if(!$results)
		{
			echo '<div>There was an error with the SQL query. Error: ' . mysqli_error() . '</div>';
			exit;
		}
		
		$count = mysqli_num_rows($results);
		
		if($count < 1)
		{
			$this->customPage();
		}
		else
		{
			$row = $results->fetch_assoc();
			if(strpos($row['permissions'],$this->UserArray['Level_access']) !== false)
			{
				echo '<div style="font-size:16px;font-weight:bold;">' . stripslashes($row['full_page_name']) . '</div>';
				echo '<div>' . stripslashes($row['body']) . '</div>';
			}
			else
			{
				echo '<div style="font-size:16px;font-weight:bold;">' . stripslashes($this->ErrorPages['403'][3]) . '</div>';
				echo '<div>' . stripslashes($this->ErrorPages['403'][4]) . '</div>' . $row['permissions'] . $this->UserArray[2];
			}
		}
		
	}
	
	private function buildErrorPages()
	{
		$query = "SELECT * FROM content WHERE node = 'error'";
		$results = $this->mysqli->query($query);
		
		if(!$results)
		{
			echo '<div>There was an error with the SQL query. Error: ' . mysqli_error() . '</div>';
			exit;
		}
		
		while($row = $results->fetch_assoc)
		{
			$this->ErrorPages[$row['sub_node']][] = $row['id'];
			$this->ErrorPages[$row['sub_node']][] = $row['permissions'];
			$this->ErrorPages[$row['sub_node']][] = $row['sub_node'];
			$this->ErrorPages[$row['sub_node']][] = $row['full_page_name'];
			$this->ErrorPages[$row['sub_node']][] = $row['body'];
		}
	}
	
	private function customPage()
	{
		if($this->Page == 'home')
		{
			echo '<div class="content-wrapper">';
			echo '	<div style="padding-left:10px;border-bottom:1px solid #878787;width:60%;font-size:18px;">AnimeFTW.tv Home</div>';
			echo '	<div class="body-content-wrapper" style="margin-top:2px;">';
			$query = "SELECT forums_threads.tid, forums_threads.ttitle, forums_threads.tpid, forums_threads.tfid, forums_threads.tdate, forums_post.pbody FROM forums_threads, forums_post WHERE forums_post.ptid=forums_threads.tid AND forums_post.puid=forums_threads.tpid AND (forums_threads.tfid='1' OR forums_threads.tfid='2' OR forums_threads.tfid='9') ORDER BY forums_threads.tid DESC LIMIT 0, 8";
			$result = $this->mysqli->query($query);
			while($row = $result->fetch_assoc())
			{
				$pbody = stripslashes($pbody);
				echo '<div class="content-paragraph">';
				echo '	<div style="font-size:16px;"><a href="#" onClick="$(\'#content\').load(\'ajax.php?page=forums&tid=' . $row['tid'] . '\'); return false;">' . $row['ttitle'] . '</a></div>';
				echo '	<div>Posted on ' . date("m.d.y",$row['tdate']) . ' by ' . $this->string_fancyUsername($row['tpid']) . '</div>';
				echo '	<div style="padding:5px;">' . preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", "", $row['pbody']) . '</div>';
				echo '</div>';
			}
			echo '	</div>';
			echo '</div>';
		}
		else if($this->Page == 'stats')
		{
		}
		else if($this->Page == 'stats1')
		{
		}
		else if($this->Page == 'stats2')
		{
		}
		else if($this->Page == 'stats3')
		{
		}
		else
		{
			echo $this->Page.'<br /><br />';
			echo $this->ErrorPages['404'][4];
		}
	}
}

?>