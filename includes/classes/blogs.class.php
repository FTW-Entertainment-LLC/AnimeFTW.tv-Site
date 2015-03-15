<?php
/****************************************************************\
## FileName: blogs.class.php									 
## Author: Brad Riemann										 
## Usage: Scripts designed for the Blog system.
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWBlog{
	var $id; //User ID
	var $showLimit; //Limit var
	var $rperm; //Read permission var
	var $cperm; //comment permission var
	var $tzone; //TimeZone of the Reading user
	var $uname; //Username for the poster
	var $bid; //Username for the poster
	//get id function
	function get_vars($id,$showLimit,$rperm,$cperm,$tzone,$uname){
		$this->id = $id;
		$this->showLimit = $showLimit;
		$this->rperm = $rperm;
		$this->cperm = $cperm;
		$this->tzone = $tzone;
		$this->uname = $uname;
	}
	//grab the current blog id
	function get_bid($bid){
		$this->bid = $bid;
	}
	// grabs the latest blog entries
	function LatestBlogs(){
		$query = "SELECT id, title, content, readperm, commentperm, date, category FROM blog_content WHERE uid='".$this->id."' AND readperm LIKE '%".$this->rperm."%' ORDER BY id ASC LIMIT 0, ".$this->showLimit;
		$result = mysql_query($query);
		$total_entries = mysql_num_rows($result);
		if($total_entries == 0){
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>".$this->uname." has not made any Blog Posts.</span>\n";
			echo "</div>\n";
		}
		else {
			while(list($id,$title,$content,$data,$category) = mysql_fetch_array($result))
			{
				$subline = "Posted by ".$this->uname." on mm/dd/yyyy";
				echo "<div class='side-body-bg'>\n";
				echo "<span class='scapmain'><a href='/blogs/".$this->uname."/$id-".stripslashes(CleanFileName($title))."/'>".stripslashes($title)."</a></span>\n";
				echo "<br />\n";
				echo "<span class='poster'>$subline</span>\n";
				echo "</div>\n";
				echo "<div class='tbl'>$content</div>\n";
				echo "<br />\n";
			}
		}
	}
	// Will give the selected Blog post
	function SingleBlog(){
		$query = "SELECT id, title, content, readperm, commentperm, date, category FROM blog_content WHERE id='".$this->bid."' AND uid='".$this->id."'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$result = mysql_query($query);
		$total_entries = mysql_num_rows($result);
		if($total_entries == 0){
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>Error: No Blog postings found.</span>\n";
			echo "</div>\n";
		}
		else {
			$row = mysql_fetch_array($result);
			$subline = "Posted by ".$this->uname." on mm/dd/yyyy";
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'><a href='/blogs/".$this->uname."/".$row['id']."-".stripslashes(CleanFileName($row['title']))."/'>".stripslashes($row['title'])."</a></span>\n";
			echo "<br />\n";
			echo "<span class='poster'>$subline</span>\n";
			echo "</div>\n";
			echo "<div class='tbl'>".$row['content']."</div>\n";
			echo "<br />\n";
		}
	}
}

?>