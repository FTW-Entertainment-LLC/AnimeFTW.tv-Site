<?php
$file = 'http://videos.animeftw.tv/restricted/saturday.night.live.s38e16.justin.timberlake.720p.hdtv.x264-2hd.mkv';
download($file,2000);

/*
Set Headers
Get total size of file
Then loop through the total size incrementing a chunck size
*/
function download($file,$chunks){
    set_time_limit(0);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-disposition: attachment; filename='.basename($file));
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    header('Pragma: public');
    $size = get_size($file);
    header('Content-Length: '.$size);

    $i = 0;
    while($i<=$size){
        //Output the chunk
        get_chunk($file,(($i==0)?$i:$i+1),((($i+$chunks)>$size)?$size:$i+$chunks));
        $i = ($i+$chunks);
    }

}

//Callback function for CURLOPT_WRITEFUNCTION, This is what prints the chunk
function chunk($ch, $str) {
    print($str);
    return strlen($str);
}

//Function to get a range of bytes from the remote file
function get_chunk($file,$start,$end){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $file);
	curl_setopt($ch, CURLOPT_REFERER, 'http://www.animeftw.tv');
	//curl_setopt($ch, CURLOPT_USERAGENT, 'XBMC');
    curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'chunk');
    $result = curl_exec($ch);
    curl_close($ch);
}

//Get total size of file
function get_size($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, 'http://www.animeftw.tv');
	//curl_setopt($ch, CURLOPT_USERAGENT, 'XBMC');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    return intval($size);
}
?>