<?php
	header("content-type:image/jpeg");
	$nocover = "http://www.animeftw.tv/images/nocover.jpg";
	$file = (isset($_GET['img'])) ? $_GET['img'] : $nocover;
	$im = (fopen($file, "r")) ? imagecreatefromjpeg($file) : imagecreatefromjpeg($nocover);
	$new = imagecreatetruecolor(168,240);
	imagecopyresampled($new,$im,0,0,0,0,168,240,imagesx($im),imagesy($im));
	imagejpeg($new);
?>