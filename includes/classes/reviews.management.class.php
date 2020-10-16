<?php
/****************************************************************\
## FileName: management.class.php									 
## Author: Brad Riemann										 
## Usage: Management Class and Functions
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
## Updated: 08/31/2013
## Version: 1.0.0
\****************************************************************/

class Reviews extends Config {

	public function __construct() 
	{
		parent::__construct();
	}
	
	public function displayReviews()
	{
		echo '<div id="reviews-wrapper">';
		echo '<div style="width:100%;height:450px;overflow-y:scroll;overflow-x:none;margin-top:5px;">';
		$this->buildReviews();
		echo '</div>';
		echo '</div>';
	}
	
	public function processRequest()
	{
		$modtype = $_GET['mod'];
		if($modtype == 'approve')
		{
			$query = "UPDATE reviews SET approved = '1', approvedby = '" . mysqli_real_escape_string($_GET['u']) . "', approvaldate = '" . time() . "' WHERE id = " . mysqli_real_escape_string($_GET['rid']);
			$results = mysqli_query($query);
			if(!$results)
			{
				echo 'There was an issue with the request: ' .mysqli_error();
				exit;
			}
			$query = "UPDATE series SET total_reviews=total_reviews+1 WHERE id = (SELECT sid FROM reviews WHERE id = " . mysqli_real_escape_string($_GET['rid']) . ")";
			$this->ModRecord("Approved Review for #" . $_GET['rid']);
			echo 'success';
		}
		else if($modtype == 'deny')
		{
			$query = "UPDATE reviews SET approved = '2', approvedby = '" . mysqli_real_escape_string($_GET['u']) . "', approvaldate = '" . time() . "' WHERE id = " . mysqli_real_escape_string($_GET['rid']);
			$results = mysqli_query($query);
			if(!$results)
			{
				echo 'There was an issue with the request: ' .mysqli_error();
				exit;
			}
			$this->ModRecord("Deny Review for #" . $_GET['rid']);
			echo 'success';
		}
		else if($modtype == 'delete')
		{
			echo 'denied';
		}
		else
		{
			echo 'denied';
		}
	}
	
	private function buildReviews()
	{
		$DivID 			= 'reviews-wrapper';
		$perpage 		= 20;
		$currentpage 	= isset($_GET['page'])?$_GET['page']:0;
		$link 			= '/scripts.php?view=management&u=' . $this->UserArray[1] . '&node=reviews';
		$count 			= mysqli_num_rows(mysqli_query("SELECT id FROM reviews"));
		
		$query = "SELECT reviews.id, series.fullSeriesName, series.seoname, users.Username, reviews.date, reviews.review, reviews.stars, reviews.approved, reviews.approvedby, reviews.approvaldate FROM reviews, users, series WHERE users.ID=reviews.uid AND series.id=reviews.sid ORDER BY id DESC LIMIT $currentpage, $perpage";
		$results = mysqli_query($query);
		echo '<div style="padding:5px 5px 0 5px;">Navigate:</div>';
		$this->internalPaging($DivID,$count,$perpage,$currentpage,$link);
		
		while($row = mysqli_fetch_array($results))
		{
			echo '<div class="reviews-row-wrapper" style="padding:5px;border-bottom:1px solid #ccc;">';
			echo '<div class="review-header">
				<div style="display:inline-block;width:175px;vertical-align:top;">Submitted by: <a href="/users/' . $row['Username'] . '">' . $row['Username'] . '</a></div>
				<div style="display:inline-block;width:100px;vertical-align:top;">' . date('M dS, Y',$row['date']) . '</div>
				<div style="display:inline-block;width:200px;vertical-align:top;"><a href="/videos/' . $row['seoname'] . '/" target="_blank">' . $row['fullSeriesName'] . '</a></div>
				<div style="display:inline-block;width:100px;vertical-align:top;">';
				echo '<select id="select-' . $row['id'] . '" class="review-mod-options">
						<option vlaue="0">Mod Options</option>
						<option vlaue="1"'; if($row['approved'] == 1){echo ' selected="selected"';} echo '>Approved</option>
						<option vlaue="2"'; if($row['approved'] == 2){echo ' selected="selected"';} echo '>Denied</option>
						<option vlaue="3">Delete</option>
					</select>
				</div>
			</div>';
			echo '<div>' . stripslashes(nl2br($row['review'])) . '</div>';
			echo '</div>';
		}
		
		echo '<script>
			$(document).ready(function() {
				$(".review-mod-options").change(function() {
					var review_id = $(this).attr("id").substring(7);
					var requested = $(this).val();
					var req_url = "' . $link . '&rid=" + review_id;
					if(requested == "Approved")
					{
						$.get(req_url + "&mod=approve", function(data,status){
							if(data.indexOf("success") != -1)
							{
							}
							else
							{
								alert("There was an error processing your request. Please Try again. " + data);
							}
						});
					}
					else if(requested == "Denied")
					{
						$.get(req_url + "&mod=deny", function(data,status){
							if(data.indexOf("success") != -1)
							{
							}
							else
							{
								alert("There was an error processing your request. Please Try again. " + data);
							}
						});
					}
					else
					{
						alert("no idea what happened.");
					}
				});
			});
		</script>';
	}
	
	//Paging function
	private function internalPaging($DivID,$count,$perpage,$start,$link)
	{
		$num = $count;
		$per_page = $perpage; 			// Number of items to show per page
		$showeachside = 4; 				// Number of items to show either side of selected page
		if(empty($start)){$start = 0;}  // Current start position
		else{$start = $start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		$front = "<span>$max_pages Pages</span>&nbsp;";
		if(($start-$per_page) >= 0){
			$next = $start-$per_page;
			$startpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($next>0?("&page=").$next:"") . '\'); return false;">&lt;</a>';
		}
		else {$startpage = '';}
		if($start+$per_page<$num){
			$endpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.'&page='.max(0,$start+1) . '\'); return false;">&gt;</a>';
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
				$middlepage .= '<a id="'.$class.'" href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($y>0?("&page=").$y:"") . '\'); return false;">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}
		
		echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}
}