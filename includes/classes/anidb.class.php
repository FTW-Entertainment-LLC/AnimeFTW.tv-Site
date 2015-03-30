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

class AniDB{
	private $rootdirectory;
	private static $calledLast; //Static so we keep this variable throughout the class.
	public function __construct()
	{
		if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com')
		{
			$this->rootdirectory = $_SERVER['DOCUMENT_ROOT'];
		}
		else
		{
			$this->rootdirectory = '/home/mainaftw/public_html';
		}
		
	}
	
	private function cacheFile($aid){
		
		$filename = $this->rootdirectory.'/cache/anidbcache_xml/'.$aid.'.xml'; //check if file exists
		if ( file_exists($filename) ){ 
			//echo "XML for this anime is already cached.<br>";
			//echo "$filename<br>";
			$datetime1 = new DateTime(date('Y-m-d H:i:s', filemtime ($filename)));
			$datetime2 = new DateTime(date('Y-m-d H:i:s'));
			$dif = $datetime1->diff($datetime2);
			$difhours = (intval($dif->format("%a"))*24) + intval($dif->format("%h"));
			//echo $difhours." hours since it's been updated.";
			if($difhours > 24){
				$this->fetchAnidbXML($filename, $aid);
			}
		} else { 
			$this->fetchAnidbXML($filename, $aid);
		}
	}
	
	private function fetchAnidbXml($filename, $aid){
		//echo "Caching anime...<br>";
		//code from  messer00, http://anidb.net/perl-bin/animedb.pl?show=cmt&id=30158
		// Remember to change client name below. Also - put anidb's aid in $aid variable first

		$this->ModRecord('Fetching '.$filename.' from AniDB.'); //Loggin so we can see exactly when we've tried to request from the API.
		$post = 'http://api.anidb.net:9001/httpapi?request=anime&client=animeftw&clientver=1&protover=1&aid='.$aid;
		
		$current = time();
		if(self::$calledLast!=null){
			while($current < self::$calledLast + 10){ //If it hasn't been more than 10 seconds since last call
				sleep(1); //We wait one second and then try again.
			}
			self::$calledLast = $current; //Everything's okay, set it to the current time and continue.
		}else{
			self::$calledLast = $current; //If this was the first time it was called, then we put in the current time and continue.
		}
		
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
		
		

		$string = curl_exec($ch) or die(curl_error($ch));

		curl_close($ch);

		//now we have all data in $string, lets put it in a file
		
		if($string == "<error>Anime not found</error>"){
			echo 'Anime not found';
			return;
		}
		
		$ha = fopen($filename,"w");
		$fputs = fputs($ha,$string);
		if($fputs==false){
			$this->ModRecord('AniDB: Couldn\'t cache '.$filename); //Logging if saving the file didn't work.
		}
		fclose($ha);
	}
	
	public function getName($lang,$aid){
		$xml = $this->getxml($aid);
		$found = null;
		$priority = 0;
		if($xml){
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
				$found = $xml->titles->title; //If we didn't get the language we were looking for, just take the first one.
			}
			return $found;
		}else{
			return null;
		}
	}
	public function getDescription($aid){ //Gets the description of the anime
		$xml = $this->getxml($aid);
		if($xml){
			foreach($xml->description as $i){
				return $i;
			}
		}else{
			return null;
		}
	}
	public function getCategories($aid){ //Returns a string of the categories, seperated by a comma and a space bar.
		$xml = $this->getxml($aid);
		if($xml){
			$categories = ""; //Create the variable to store the categories..
			foreach($xml->categories->category as $i){  //Loop through them
				$categories = $categories.$i->name.", "; //Add them to the variable
			}
			$categories = substr($categories,0,strlen($categories)-2); //Remove the last two characters being ", "
			return $categories;
		}else{
			return null;
		}
	}
	public function getEpisodeTitle($aid, $epno){ //Returns the title of the episode.
		$xml = $this->getxml($aid);
		if($xml){
			foreach($xml->episodes->episode as $i){ //Loop through the episodes, seeing as they're not in order..
				if($i->epno==$epno){ //If it's the one we're looking for
					return $i->title[1];
					break;
				}
			}
		}else{
			return null;
		}
	}
	public function getEpisodeCount($aid){
		$xml = $this->getxml($aid);
		if($xml){
			return $xml->episodecount;
		}else{
			return null;
		}
		
	}
	public function getSeriesType($aid){
		$xml = $this->getxml($aid);
		if($xml){
			return $xml->type;
		}else{
			return null;
		}
		
	}
	private function getxml($aid){
		$this->cacheFile($aid); //Check to see if this file needs to update, and do so if it does.
		$filename = $this->rootdirectory.'/cache/anidbcache_xml/'.$aid.'.xml';
		if ( file_exists($filename) ){ 
			return simplexml_load_file($filename);
		}else{
			return null;
		}
	}

}

?>