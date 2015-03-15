<?php
header("Content-type: image/png");
$image = imagecreatefromjpeg('http://static.ftw-cdn.com/site-images/seriesimages/454.jpg');
imagepng($image);
imagedestroy($image);
?>