<?php
/****************************************************************\
## FileName: toplist.class.php									 
## Author: Brad Riemann										 
## Usage: Toplist classes with sub functions
## Copywrite 2011 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWtoplist extends Config {
    
	var $num; //amount of series to show
	var $sing; //1:0, for base series pages or single instances.
	var $type;
	
    public function __construct()
    {
        parent::__construct();
    }
    
	//grab all the vars
	function get_num($i){$this->num = $i;}
	function get_sing($i){$this->sing = $i;}
	function get_type($i){$this->type = $i;}
	
	#function TopAnime($num)
	#displays a given amount of toseries based on the $num param
	function TopAnime(){
		$query = "SELECT seriesId, lastPosition, currentPosition FROM site_topseries ORDER BY currentPosition ASC LIMIT 0, ".$this->num;
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		if($this->num == 10){$topTen = '<ol class="top10">'."\n";}
		else{$topTen = '<ol>'."\n";}
  		while(list($seriesId,$lastPosition,$currentPosition) = mysql_fetch_array($result)){
			$listedName = $this->ShowData2($seriesId);
			if($currentPosition < $lastPosition){
				$Rank = '&nbsp;<img src="' . $this->Host . '/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: '.$lastPosition.'" />';
			}
			else if ($currentPosition == $lastPosition){
				$Rank = '&nbsp;<img src="' . $this->Host . '/arrow_none.gif" title="Rank Unchanged, Previous Rank: '.$lastPosition.'" alt="" />';
			}
			else {
				$Rank = '&nbsp;<img src="' . $this->Host . '/arrow_down.gif" alt="" title="Rank Went Down, Previous Rank: '.$lastPosition.'" />';
			}
			if($listedName == 'na'){
				$topTen .= '';
			}
			else {
				$topTen .= '<li type="1">'.$listedName.$Rank.'</li>'."\n";
			}
		}
		$topTen .= '</ol>'."\n";
		echo $topTen;
	}
	//stylizing function
	function StyleTop(){
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Top ".$this->num." Anime</div>\n";
		echo "<div class='side-body floatfix'>\n";
	}
	function StyleBottom(){
		echo "</div></div>\n";
	}
	function ShowData2($seriesId){
		$query = "SELECT id, seoname, fullSeriesName FROM series WHERE id='$seriesId'";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$row = mysql_fetch_array($result);
		return "<a class='side tooltip-overlay' href='/anime/".$row['seoname']."/' data-node=\"/scripts.php?view=profiles&show=tooltips&id=".$row['id']."\">".stripslashes($row['fullSeriesName'])."</a>\n";
	}
}

?>