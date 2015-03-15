<?php
include('init.php');
$v = new AFTWVideos(); //Build our videos

if(isset($_GET['remote']) && $_GET['remote'] == 'yes' && !isset($_GET['page']))
{
	header('Content-type: application/json; charset=UTF-8');
	$q = mysql_real_escape_string($_GET['q']);
	$query = "SELECT fullSeriesName FROM series WHERE  active='yes' AND aonly = 0 AND ( fullSeriesName LIKE '%".$q."%' OR romaji LIKE '%".$q."%' OR kanji LIKE '%".$q."%' ) ORDER BY fullSeriesName LIMIT 0, 8";
	$query = mysql_query($query);
	$Ct = mysql_num_rows($query);
	$result = '["'.$q.'",[';
	$i = 1;
	while(list($fullSeriesName) = mysql_fetch_array($query, MYSQL_NUM)){
		if($i < $Ct){
			$result .= '"'.$fullSeriesName.'",';
		}
		else {
			$result .= '"'.$fullSeriesName.'"';
		}
		$i++;
	}
	$result .= ']';
	echo $result;
}
else if(isset($_GET['remote']) && $_GET['remote'] == 'yes' && isset($_GET['page']))
{
	if(isset($_GET['input']))
	{
		echo $v->searchSeries2($_GET['input'],$_GET['page']);
	}
	else
	{
		echo '<div align="center" style="color:gray;padding:2px;font-size:16px;width:100%;">There was an error trying to process the data, please try again.</div>';
	}
}
else 
{
								  
$PageTitle = 'Site Search - AnimeFTW.tv';
include('header.php');
include('header-nav.php');
$index_global_message = "Welcome to the new search page!";
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// End Main BG
    echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td class='note-message' align='center'>".$index_global_message."</td>\n";
	echo "</tr>\n</table>\n";
	echo "<br />\n<br />\n";
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	if(isset($_GET['q']))
	{
		echo $v->searchSeries2($_GET['q']);				
	}
	else
	{
		echo 'Search for anything on the site in realtime.';
	}
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>Search Usage Guide</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo "The Search functions used by AnimeFTW.tv utilize the greatest and fastest algorithms. You can use the search feature on this page to search through, Friends, Series and Episodes. More functionality will come as it is needed but for now it should show what people are looking for.";
	echo "</div></div>\n";
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
		
include('footer.php');
}
?>