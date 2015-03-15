<?php

require_once("/home/mainaftw/public_html/includes/classes/config.class.php");

//$query = 'SELECT `id`, `category` FROM series';
/*$query = "SELECT `id`, `name` FROM `categories`";
$result = mysql_query($query);

$categoryArray = array();
while($row = mysql_fetch_assoc($result))
{
	$categoryArray[$row['id']] = $row['name'];
}*/
//print_r($categoryArray);

$query = "SELECT `id`, `category` FROM `series`";
$result = mysql_query($query);

$finalCats = array();
while($row = mysql_fetch_assoc($result))
{
	$cats = explode(",",$row['category']);
	$keys = '';
	$count = count($cats);
	$i = '';
	foreach($cats as $indCat)
	{
		//$key .= array_search($indCat, $categoryArray);
		$key .= $indCat;
		$i++;
		if($i < $count)
		{
			$key .= ' , ';
		}
	}
	//echo $key . '<br />';
	mysql_query("UPDATE `series` SET `category` = '" . $key . "' WHERE `id` = " . $row['id']);
	
	unset($key);
	unset($i);
}

/*
foreach($categoryArray as $Cat)
{
	$query = "INSERT INTO `categories` (`id`, `name`, `description`, `date_added`, `uid`, `date_modified`, `modifier`) VALUES (NULL, '" . $Cat . "', '', '" . time() . "', '1', '" . time() . "', '0')";
	mysql_query($query);
}*/