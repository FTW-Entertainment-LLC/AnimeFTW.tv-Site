<?php
include('../siteroot.php');
$query = "SELECT id, fullSeriesName FROM series ORDER BY id ASC";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result, MYSQL_ASSOC))
{
	
	$file = 'http://images.animeftw.tv/seriesimages/'.$row['id'].'.jpg';
	$file_headers = @get_headers($file);
	if($file_headers[0] == 'HTTP/1.1 404 Not Found')
	{
		echo $row['fullSeriesName'].' - id '.$row['id'].'<br /><img src="http://images.animeftw.tv/seriesimages/'.$row['id'].'.jpg" alt="" /><br /><br />';
	}
}

?>