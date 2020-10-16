<?php

include '../config_site.php';
include '../newsOpenDb.php';

if(!isset($_GET['limit']))
{
	$limit = 0;
}
else
{
	$limit = $_GET['limit'];
}

$query = "SELECT epnumber, epprefix, seriesname, videotype FROM episode WHERE html5 = 1 ORDER BY id LIMIT $limit, 5000";
$result = mysqli_query($query);
if(!$result)
{
	echo 'There were no results to display';
	exit;
}
$i = 0;
while(list($epnumber,$epprefix,$seriesname,$videotype) = mysqli_fetch_array($result)){
	$base = "http://videos.animeftw.tv/" . $seriesname ."/" . $epprefix ."_" . $epnumber ."_ns.mp4";
	
	$file_headers = @get_headers($base);
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		echo "Episode " . $epnumber . " of " . $seriesname . " did not have an HTML5 file!<br />\n";
		$i++;
	}
}
if($i == 0){
	echo 'No issues found.';
}

?>