<?php
	$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
	if($mode == "cover") {
		header("content-type:image/jpeg");
		$nocover = "http://i.animeftw.tv/nocover.jpg";
		$file = (isset($_GET['img'])) ? $_GET['img'] : $nocover;
		$im = (fopen($file, "r")) ? imagecreatefromjpeg($file) : imagecreatefromjpeg($nocover);
		$new = imagecreatetruecolor(168,240);
		imagecopyresampled($new,$im,0,0,0,0,168,240,imagesx($im),imagesy($im));
		imagejpeg($new);
	} elseif($mode == "screen") {
		header("content-type:image/jpeg");
		$nocover = "http://i.animeftw.tv/noscreen.jpg";
		$file = (isset($_GET['img'])) ? $_GET['img'] : $nocover;
		$file = (basename($file) == "noimage.png") ? $nocover : $file;
		$im = (fopen($file, "r")) ? imagecreatefromjpeg($file) : imagecreatefromjpeg($nocover);
		$width = imagesx($im); $start = (floor($width/2)-60);
		$new = imagecreatetruecolor(120,396);

		if(imagesy($im) < 396) {
			$newWidth = floor((imagesx($im)/imagesy($im))*396);
			$im2 = imagecreatetruecolor($newWidth,396);
			imagecopyresized($im2,$im,0,0,0,0,$newWidth,396,imagesx($im),imagesy($im));
			imagecopy($new,$im2,0,0,$start,0,$newWidth,396);
		} else {
			imagecopy($new,$im,0,0,$start,0,imagesx($im),imagesy($im));
		}
		imagerectangle($new,1,1,118,394,imagecolorallocate($new,0,0,0));
		imagejpeg($new);
	} else {
	}
?>