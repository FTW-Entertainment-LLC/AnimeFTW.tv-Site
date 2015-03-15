<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class SimpleImage {
   
   var $image;
   var $image_type;
 
   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   }      
}
# small - height= 50px
#
#
if($_GET['picture'] == 'small')
{ 
	if($_GET['type'] == 'jpg')
	{
		header('Content-Type: image/jpeg');
		$imagetype = "jpg";
	}
	else if ($_GET['type'] == 'png')
	{
		header('Content-Type: image/png');
		$imagetype = "png";
	}
	else if ($_GET['type'] == 'gif')
	{
		header('Content-Type: image/gif');
		$imagetype = "gif";
	}
	else if ($_GET['type'] == 'bmp')
	{
		header('Content-Type: image/bmp');
		$imagetype = "bmp";
	}
	else if ($_GET['type'] == 'jpeg')
	{
		header('Content-Type: image/jpeg');
		$imagetype = "jpeg";
	}
	$Image = $_GET['imgname'].'.'.$imagetype;
	$image = new SimpleImage();
   	$image->load($Image);
   	$image->resizeToWidth(50);
   	$image->output();
}
if($_GET['picture'] == 'ex-s')
{ 
	if($_GET['type'] == 'jpg')
	{
		header('Content-Type: image/jpeg');
		$imagetype = "jpg";
	}
	else if ($_GET['type'] == 'png')
	{
		header('Content-Type: image/png');
		$imagetype = "png";
	}
	else if ($_GET['type'] == 'gif')
	{
		header('Content-Type: image/gif');
		$imagetype = "gif";
	}
	else if ($_GET['type'] == 'bmp')
	{
		header('Content-Type: image/bmp');
		$imagetype = "bmp";
	}
	else if ($_GET['type'] == 'jpeg')
	{
		header('Content-Type: image/jpeg');
		$imagetype = "jpeg";
	}
	$Image = $_GET['imgname'].'.'.$imagetype;
	$image = new SimpleImage();
   	$image->load($Image);
   	$image->resizeToWidth(25);
   	$image->output();
}
/*if($_GET['picture'] == 'small')
{
	$Image = 'uploads/images/'.$_GET['imgname'].'.jpg';
	header('Content-Type: image/jpeg');
   	$image = new SimpleImage();
   	$image->load($Image);
   	$image->resizeToHeight(50);
   	$image->output();
}
if($_GET['picture'] == 'x-sm' && $_GET['type'] == 'jpg')
{
	$Image = 'uploads/images/'.$_GET['imgname'].'.jpg';
	header('Content-Type: image/jpeg');
   	$image = new SimpleImage();
   	$image->load($Image);
	$image->resize(85,66);
   	$image->output();
}*/

?>

