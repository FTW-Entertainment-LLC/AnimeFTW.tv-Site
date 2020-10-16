<?
include('config.php');

$query = "SELECT id, page_id FROM page_comments WHERE page_id LIKE '1%' AND epid = '' ORDER BY id";
$result = mysqli_query($query) or die("Error: ". mysqli_error(). " with query ". $query); 
$i = 1;
while($row=mysqli_fetch_array($result))
{
	echo $i.". Comment ID: ".$row['id'].", Page ID: ".$row['page_id']."<br />\n";
	$i++;
}
?>