<?
  header("Content-type: image/gif");
include('../includes/siteroot.php');
if(isset($_GET['username']))
{
	$Username = $_GET['username'];
}
if(isset($_GET['background']))
{
	$Background = $_GET['background'];
}
					$query003 = "SELECT ID FROM users WHERE Username='".$Username."'";
							$result003 = mysql_query($query003) or die('Error : ' . mysql_error());
							$row003 = mysql_fetch_array($result003);
							$ID003 = $row003['ID'];
							//query1
				
			$Username = str_replace('_', ' ', $Username); 
			//$Username = underscoresToSpaces($Username);
			$row1 = trackerShowEpisode($ID003,0);
			$row2 = trackerShowEpisode($ID003,1);
			$row3 = trackerShowEpisode($ID003,2);
			$row4 = trackerShowEpisode($ID003,3);
$imgHgt = 100;
$imgWidth= 350;
$imgColorHex="487239";

$txt = array($row1,$row2,$row3,$row4,$Username);
$fntSize = array(11,11,11,11,16);
$fntHex = array('FFFFFF','FFFFFF','FFFFFF','FFFFFF','FFFFFF');
$fntShad = array(3,3,3,3,2);
$fnt = array('fonts/arial.ttf','fonts/arial.ttf','fonts/arial.ttf','fonts/arial.ttf','fonts/GeosansLight.ttf');
$font = "fonts/arial.ttf";

$background = @imagecreatefromjpeg("./tracker-backgrounds/".$Background.".jpg") or die("Cannot Initialize new gd image stream");
$background_color = imagecolorallocate($background, 0, 0, 254);

$t = count($txt);
$totY = 5;
$i = 0;
$yValue = 20;
while ($i < $t)
{

if($fntShad[$i] == 1)
{
$bbox = imageftbbox($fntSize[$i], 0, $fnt[$i], $txt[$i], array("linespacing" => 1));
$width = abs($bbox[0]) + abs($bbox[2]);
$height = abs($bbox) + abs($bbox[5]);
$xcoord = (($imgWidth - $width)) -2;
$ycoord = $height + $totY -25;
$int = hexdec("FFFFFF");
$arr = array("red" => 0xFF & ($int >> 0x10),
"green" => 0xFF & ($int >> 0x8),
"blue" => 0xFF & $int);
$tcolor = imagecolorallocate($background, $arr["red"], $arr["green"], $arr["blue"]);

imagettftext($background, $fntSize[$i], 0, $xcoord, $ycoord, $tcolor, $fnt[$i], $txt[$i]);
$totY = $ycoord + 5;
$i++;
}
else if($fntShad[$i] == 2)
{
$bbox = imageftbbox($fntSize[$i], 0, $fnt[$i], $txt[$i], array("linespacing" => 1));
$width = abs($bbox[0]) + abs($bbox[2]);
$height = abs($bbox) + abs($bbox[5]);
$xcoord = (($imgWidth - $width)) -4;
$ycoord = $height + $totY -2;
if ($Background == 'luckystar')
{
$int = hexdec("000000");
}
else {
$int = hexdec("FFFFFF");
}
$arr = array("red" => 0xFF & ($int >> 0x10),
"green" => 0xFF & ($int >> 0x8),
"blue" => 0xFF & $int);
$tcolor = imagecolorallocate($background, $arr["red"], $arr["green"], $arr["blue"]);

imagettftext($background, $fntSize[$i], 0, $xcoord, $ycoord, $tcolor, $fnt[$i], $txt[$i]);
$totY = $ycoord + 5;
$i++;
}
else 
{
$bbox = imageftbbox($fntSize[$i], 0, $fnt[$i], $txt[$i], array("linespacing" => 1));
$width = abs($bbox[0]) + abs($bbox[2]);
$height = abs($bbox) + abs($bbox[5]);
$xcoord = (($imgWidth - $width)) -2;
$ycoord = $height + $totY + 2;
if ($i <= 1)
{
$yValue = ($yValue*$i)+20;
}
else if ($i == 2)
{
$yValue = ($yValue*$i)-20;
}
else
{
$yValue = 80;
}
if ($Background == 'luckystar')
{
$int = hexdec("000000");
}
else {
$int = hexdec("FFFFFF");
}
$arr = array("red" => 0xFF & ($int >> 0x10),
"green" => 0xFF & ($int >> 0x8),
"blue" => 0xFF & $int);
$tcolor = imagecolorallocate($background, $arr["red"], $arr["green"], $arr["blue"]);

imagettftext($background, $fntSize[$i], 0, 10, $yValue, $tcolor, $fnt[$i], $txt[$i]);
$totY = $ycoord + 5;
$i++;

}
}
imagegif($background);
imagedestroy($background);


?> 