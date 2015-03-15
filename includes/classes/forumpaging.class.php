<?php
/****************************************************************\
## FileName: forumpaging.class.php									 
## Author: Brad Riemann										 
## Usage: Paging for the Forums.
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWForumPageing {
	var $rate;
	var $current;
	var $icount;
	var $start;
	var $url;
	var $setlimit;
	
	//get the rate to show.
	function get_rate($rate){$this->rate = $rate;}
	function get_current($current){$this->current = $current;}
	function get_icount($icount){$this->icount = $icount;}
	function get_start($start){$this->start = $start;}
	function get_url($url){$this->url = $url;}
	function get_setlimit($setlimit){$this->setlimit = $setlimit;}
	
	function ShowPaging(){
		$thispage = $this->url;
		$num = $this->icount;
		$per_page = $this->setlimit; // Number of items to show per page
		$showeachside = 4; //  Number of items to show either side of selected page
		if(empty($this->start)){$start = 0;}  // Current start position
		else{$start = $this->start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		$front = "<span>$max_pages Pages</span>&nbsp;";
		if(($start-$per_page) >= 0)
		{
			$next = $start-$per_page;
			$startpage = '<a href="'.$thispage.($next>0?("s-").$next:"").'">&lt;</a>';
		}
		else {$startpage = '';}
		if($start+$per_page<$num)
		{
			$endpage = '<a href="'. $thispage.'s-'.max(0,$start+$per_page).'">&gt;</a>';
		}
		else {
			$endpage = '';
		}
		$eitherside = ($showeachside * $per_page);
		if($start+1 > $eitherside){
			$frontdots = " ...";
		}
		else {$frontdots = '';}
		$pg = 1;
		$middlepage = '';
		for($y=0;$y<$num;$y+=$per_page)
		{
			$class=($y==$start)?"pageselected":"";
			if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
			{
				$middlepage .= '<a id="'.$class.'" href="'.$thispage.($y>0?("s-").$y:"").'">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}
		
		return '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}
}
?>