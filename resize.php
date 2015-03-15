<?php 

		if($_SERVER['SERVER_PORT'] == 443)
		{
			$Host = 'https://d206m0dw9i4jjv.cloudfront.net';
		}
		else
		{
			$Host = 'http://img02.animeftw.tv';
		}
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
	if( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) {
		$current_transparent = imagecolortransparent($this->image);
		if($current_transparent != -1) {
			$transparent_color = imagecolorsforindex($this->image, $current_transparent);
			$current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
			imagefill($new_image, 0, 0, $current_transparent);
			imagecolortransparent($new_image, $current_transparent);
		} elseif( $this->image_type == IMAGETYPE_PNG) {
			imagealphablending($new_image, false);
			$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
			imagefill($new_image, 0, 0, $color);
			imagesavealpha($new_image, true);
		}
	}
	imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
	$this->image = $new_image;	
	}    
}
if($_GET['type'] == 'jpg')
{
	$newExtention = 'jpg';
}
else if($_GET['type'] == 'jpeg')
{
	$newExtention = 'jpeg';
}
else if($_GET['type'] == 'png')
{
	$newExtention = 'png';
}
else if($_GET['type'] == 'gif')
{
	$newExtention = 'gif';
}
else if($_GET['type'] == 'bmp')
{
	$newExtention = 'bmp';
}
if($_GET['picture'] == 'big')
{
	$Image = 'images/avatars/'.$_GET['imgname'].'.'.$newExtention;
	header('Content-Type: image/'.$newExtention);
   	$image = new SimpleImage();
   	$image->load($Image);
   	$image->resizeToHeight(360);
   	$image->output();
}
if($_GET['picture'] == 'small')
{
	$Image = 'images/avatars/'.$_GET['imgname'].'.'.$newExtention;
	header('Content-Type: image/'.$newExtention);
   	$image = new SimpleImage();
   	$image->load($Image);
   	$image->resizeToHeight(100);
   	$image->output();
}
if($_GET['picture'] == 'x-sm')
{
	$Image = 'http://www.animeftw.tv/images/avatars/'.$_GET['imgname'].'.'.$newExtention;
	header('Content-Type: image/'.$newExtention);
   	$image = new SimpleImage();
   	$image->load($Image);
	$image->resize(50,50);
   	$image->output();
}

if($_GET['picture'] == 's-small')
{
	$Image = $Host . '/seriesimages/'.$_GET['imgname'].'.jpg';
	header('Content-Type: image/'.$newExtention);
   	$image = new SimpleImage();
   	$image->load($Image);
	$image->resize(50,50);
   	$image->output();
}
if($_GET['picture'] == 'medium')
{
	$Image = $Host . '/seriesimages/'.$_GET['imgname'].'.jpg';
	header('Content-Type: image/'.$newExtention);
   	$image = new SimpleImage();
   	$image->load($Image);
	$image->resize(100,100);
   	$image->output();
}

if($_GET['picture'] == 'large')
{
	$Image = $Host . '/seriesimages/'.$_GET['imgname'].'.jpg';
	header('Content-Type: image/'.$newExtention);
   	$image = new SimpleImage();
   	$image->load($Image);
   	$image->resizeToHeight(200);
   	$image->output();
}
?>

