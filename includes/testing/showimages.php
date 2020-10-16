<?php
if(isset($_GET['s'])){$s = $_GET['s'];}
else {$s = 0;}
include('../classes/config.class.php');
/*$query = "SELECT id, epnumber, epname, epprefix, image FROM episode ORDER BY id LIMIT ".$s.", 1000";
$result = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
while($row = mysqli_fetch_array($result, MYSQL_ASSOC))
{
	if($row['image'] == 0){$imvarb = 'http://static.ftw-cdn.com/site-images/video-images/noimage.png';}
	else {$imvarb = 'http://static.ftw-cdn.com/site-images/video-images/'.$row['epprefix'].'_'.$row['epnumber'].'_screen.jpeg';}
	echo '<div>id: '.$row['id'].', epprefix: '.$row['epprefix'].', #'.$row['epnumber'].'<img src="'.$imvarb.'" alt="" height="100px" /></div>';
}*/
$C = new Config();
$query = "SELECT `id`, `fullSeriesName` FROM series WHERE fullSeriesName LIKE 't%' OR fullSeriesName LIKE 'r%' ORDER BY id LIMIT ".$s.", 1000";
$results = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($results))
{
	// $C->Host
	echo '<div>' . $row['fullSeriesName'] . '<br /><img src="' . $C->Host . '/seriesimages/' . $row['id'] . '.jpg" alt="' . $row['fullSeriesName'] . ' series image" style="height:50px;" /></div>';
}