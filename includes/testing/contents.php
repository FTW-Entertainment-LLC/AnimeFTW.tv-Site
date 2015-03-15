<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
$url = 'http://videos3.animeftw.tv/fetch-pictures-v2.php?node=add&remote=true&seriesName=sengokucollection&epprefix=sengokuc&epnumber=16&durration=360&vidwidth=854&vidheight=480&videotype=mkv';

echo file_get_contents($url);
?>