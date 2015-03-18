<?php
/****************************************************************\
## FileName: anidb.class.php								 
## Author: Hani Mayahi							 
## Usage: Fetches various anime information from the AniDB HTML API.
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved

Function examples:
$romaji_name = getName("x-jat",$aid);
$english_name = getName("en",$aid);
$kanji_name = getName("ja",$aid);
$description = getDescription($aid);
$categories = getCategories($aid);
getEpisodeTitle($aid, $epno);



\****************************************************************/

class AniDB() extends Config{

	public function __construct()
	{
		parent::__construct();
	}
	
	private function cacheFile($aid){
		$filename = $rootdirectory.'/cache/anidbcache_xml/'.$aid.'.xml'; //check if file exists
		if ( file_exists($filename) ){ 
			//echo "XML for this anime is already cached.<br>";
			//echo "$filename<br>";
			$datetime1 = new DateTime(date('Y-m-d H:i:s', filemtime ($filename)));
			$datetime2 = new DateTime(date('Y-m-d H:i:s'));
			$dif = $datetime1->diff($datetime2);
			$difhours = (intval($dif->format("%a"))*24) + intval($dif->format("%h"));
			//echo $difhours." hours since it's been updated.";
			if($difhours > 24){
				fetchAnidbXML($filename, $aid);
			}
		} else { 
			fetchAnidbXML($filename, $aid);
		}
	}
	
	private function fetchAnidbXml($filename, $aid){
		//echo "Caching anime...<br>";
		//code from  messer00, http://anidb.net/perl-bin/animedb.pl?show=cmt&id=30158
		// Remember to change client name below. Also - put anidb's aid in $aid variable first

		
		$post = 'http://api.anidb.net:9001/httpapi?request=anime&client=animeftw&clientver=1&protover=1&aid='.$aid;

		//im using cURL, simulating http connection with browser and getting data from anidb
		  $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $post);
		

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
		'Accept-Encoding: gzip',
		'Accept-Charset: ISO-8859-1,UTF-8;q=0.7,*;q=0.7',
		'Cache-Control: no',
		'Accept-Language: de,en;q=0.7,en-us;q=0.3',
		'Referer: http://anidb.net/'));
		curl_setopt($ch, CURLOPT_HEADER, 0);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		
		

		$string = curl_exec($ch) or die(curl_error());

		curl_close($ch);

		//now we have all data in $string, lets put it in a file
		$ha = fopen($filename,"w");
		fputs($ha,$string);
		fclose($ha);
	}
	
	public function getName($lang,$aid){
		$xml = getxml($aid);
		$found = null;
		$priority = 0;
		foreach($xml->titles->title->xpath('//titles/title[@xml:lang = "'.$lang.'"]') as $i){ //Loop through the titles
			switch($i->attributes()){ //For priority, depends from anime and some don't have all.
				case "synonym":
					if($priority>1){continue 2;}
					$found = $i;
					$priority = 1;
					break;
				case "official":
					if($priority>2){continue 2;}
					$found = $i;
					$priority = 2;
					break;
				case "main":
					$found = $i;
					$priority = 3;
			}
		}
		if($found==null){
			$found = "NULL";
		}
		return $found;
	}
	public function getDescription($aid){ //Gets the description of the anime
		$xml = getxml($aid);
		foreach($xml->description as $i){
			return $i;
		}
	}
	public function getCategories($aid){ //Returns a string of the categories, seperated by a comma and a space bar.
		$xml = getxml($aid);
		$categories = ""; //Create the variable to store the categories..
		foreach($xml->categories->category as $i){  //Loop through them
			$categories = $categories.$i->name.", "; //Add them to the variable
		}
		$categories = substr($categories,0,strlen($categories)-2); //Remove the last two characters being ", "
		return $categories;
	}
	public function getEpisodeTitle($aid, $epno){ //Returns the title of the episode.
		$xml = getxml($aid);
		foreach($xml->episodes->episode as $i){ //Loop through the episodes, seeing as they're not in order..
			if($i->epno==$epno){ //If it's the one we're looking for
				return $i->title[1];
				break;
			}
		}
	}
	public function getEpisodeCount($aid){
		$xml = getxml($aid);
		return $xml->episodecount;
	}
	private function getxml($aid){
		cacheFile($aid); //Check to see if this file needs to update, and do so if it does.
		return simplexml_load_file($rootdirectory.'/cache/anidbcache_xml/'.$aid.'.xml');
	}

}

?>