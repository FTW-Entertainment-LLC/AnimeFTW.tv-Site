<?php

//path to directory to scan
$directory = "./";
 
//get all image files with a .jpg extension.
$images = glob($directory . "*.avi");
 
//print each file name
foreach($images as $image)
{
	echo '<a href="'.$image.'">'.$image.'</a><br />';
}

?>