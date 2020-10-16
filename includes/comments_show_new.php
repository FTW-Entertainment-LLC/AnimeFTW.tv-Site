<?php
include 'global_functions.php';
$db_table = "page_comments";
include("comments-config.php"); // configuration parameters package
include("comments-lang.php"); // language/words package

$comm_page = is_numeric($_GET['comm_page']) ? $_GET['comm_page'] : 1;
if ($comm_page<1) {
	$comm_page = 1;
}
if(!$_GET['epid'])
{
}
else {
$epid = $_GET['epid'];
}

// Figure out the limit for the query based on the current page number. 
$from = $comm_page * $comment_limit - $comment_limit;

//include("includes/db_conn.php"); // connect to host and select db
$conn = mysqli_connect($newsdbhost, $newsdbuser, $newsdbpass, $newsdbname);
# mysqli_select_db($newsdbname);

// construct page query to find out how many matches
$result=mysqli_query($conn, , "select count(*) from $db_table WHERE epid='$epid' AND is_approved = '1'");
$count=$result->num_rows;
$total_pages = ceil($count / $comment_limit);

// and the average rating is ...
$query = "SELECT AVG(rating) from $db_table WHERE epid='$epid' AND is_approved = '1' AND rating>'0'";
$result = mysqli_query($conn, , $query) or die("error ". mysqli_error(). " with query ".$query);
$row = mysqli_fetch_array($result);
$av_rating = number_format($row['AVG(rating)'],2);

// construct page query to find out how many matches
$query = "SELECT * from $db_table WHERE epid = '$epid' AND is_approved = '1' ORDER by dated DESC LIMIT $from, $comment_limit";// what matches THIS page?
$result = mysqli_query($conn, , $query) or die("Error: ". mysqli_error(). " with query ". $query); 

// skip output if no comments exist
if (!$count) {
	echo "<br /><div style='". $num_style. "'>". $no_comments. "</div><br />\n";
	echo '<span name="myspan" id="myspan"></span>';
} else {
	echo "<br /><div style='". $num_style. "'>". $comments_to_date. $count. $this_is_page. $comm_page. $page_of_page. $total_pages. ". ";
	if (($av_rating>0) && ($art_rating==1)) {
	   $stars = 5 * round($av_rating/0.5);
	   echo $average_rating. "<img src='http://www.animeftw.tv/images/stars/stars_". $stars. ".gif' alt=''/>";
	}
	echo "</div><br />";
	// output comments
	echo '<span name="myspan" id="myspan"></span>';
	echo "<table cellpadding='0' cellspacing='0' width='100%' border='0' align='center'>";
	while ($myrow = mysqli_fetch_array($result)) // loop through all results
	{ 
		$style = $style == $ro1 ? $ro2 : $ro1; 
		echo "<tr bgcolor='". $style. "'>";
		echo "<td><p style='". $comm_style. "'>
		<strong>";
		/*$query2 = "SELECT Level_access, advanceActive, advanceImage, advancePreffix FROM users WHERE Username='".$myrow['name']."'";
			$result2 = mysqli_query($conn, , $query2) or die("error ". mysqli_error(). " with query ".$query2);
			$row2 = mysqli_fetch_array($result2);
			$Level_access = $row2['Level_access'];
			$advanceActive = $row2['advanceActive'];
			$advanceImage = $row2['advanceImage'];
			$advancePreffix = $row2['advancePreffix'];*/
			
		if (!$myrow['name']) 
		{
			echo $unknown_poster;
		}
		else {
			echo checkUserName($myrow['name']);
		}
			
		echo "&nbsp;&nbsp;&nbsp;";
		echo "</strong>0&nbsp;<a href='javascript:void(0)'><img src='/images/negative.png' style='padding-top:2px;' title='report this comment as in-appropriate' alt='' /></a>&nbsp;&nbsp;<a href='javascript:void(0)'><img src='/images/positive.png' style='padding-top:2px;' title='report this comment as appropriate' alt='' /></a>&nbsp;0</p></td>";
		echo "<td align='right'><div style='". $comm_style. "'>";
		niceday($myrow['dated']);
		if (($art_rating==1) && ($myrow['rating']>0) && ($visitor_rating==1)) {
		   $star_img = "http://www.animeftw.tv/images/stars/stars_". 10*$myrow['rating']. ".gif";
		   $size = getimagesize($star_img);
		   echo "&nbsp;<img src='". $star_img. "' ". $size[3]. " alt=''/>";
		}

		echo "</div></td></tr>";
		echo "<tr bgcolor='". $style. "'>";
		echo "<td colspan='2' style='border-bottom:1px dotted ". $space_color. ";'><p style='". $comm_style. "'>";
		$comments = stripslashes($myrow['comments']);
		$comments = nl2br($comments);
		if($myrow['isSpoiler'] == 1)
		{
			$comments = "<a style=\"cursor:pointer;\"onclick=\"ShowHideContent(this,'comment_".$myrow['id']."');\">****Episode Spoiler**** (click to reveal)</a>\n<span id=\"comment_".$myrow['id']."\" style=\"display:none\">".$comments."</span>\n";
		}
		echo $comments;
		echo "</p></td></tr>\n";
	}
	// loop done
	echo "</table>\n";
}
if(isset($_GET['ep']))
{
	$POST = $_GET['ep'];
}
// Pagination magic (of sorts)
if ($total_pages>1) {
			echo "<div><br/>Page:&nbsp;";
			for ($z=-5; $z<6;$z++) {
				$dapage = $comm_page+$z;
				if (($dapage>0) && ($dapage<=$total_pages)) {
					if ($dapage==$comm_page) {
						echo "-". $dapage. "-";
					} else {
						echo "<a onclick=\"ajax_loadContent('comments1','http://".$_SERVER['HTTP_HOST']."/includes/comments_show_new.php?epid=".$epid."&amp;comm_page=". $dapage. "');return false\" style='cursor:pointer;'>&nbsp;". $dapage. "&nbsp;</a>";
						//echo "<a class='pagelink' href='".$_SERVER['REQUEST_URI']."&amp;comm_page=". $dapage. "'>&nbsp;". $dapage. "&nbsp;</a>";
					}
					echo "&nbsp;&nbsp;";
				}			
			}
			echo "</div>";
}
echo "<br/>";
?>
