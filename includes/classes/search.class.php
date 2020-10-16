<?php
/****************************************************************\
## FileName: search.class.php
## Author: Brad Riemann
## Usage: Search Class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Search extends Config {

	var $UserArray;
	public function __construct()
	{
		parent::__construct();
		// initialize the userarray
		$this->UserArray = $this->outputUserInformation();
	}

	private function array_searchSite($input)
	{

	}


	private function array_searchSeries($input)
	{
		$input = mysqli_real_escape_string($input);
		if($this->UserArray[2] == 0)
		{
			// the user is an unregistered user, give them limited information
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews` FROM `series` WHERE `active` = 'yes' AND `aonly` = '0' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' OR `category` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC";
		}
		else if($this->UserArray[2] == 3)
		{
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews` FROM `series` WHERE `active` = 'yes' AND `aonly` <= '1' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' OR `category` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC";
		}
		else
		{
			$query = "SELECT `id`, `seriesName`, `fullSeriesName`, `seoname`, `ratingLink`, `category`, `total_reviews` FROM `series` WHERE `active` = 'yes' AND ( `fullSeriesName` LIKE '%".$input."%' OR `romaji` LIKE '%".$input."%' OR `kanji` LIKE '%".$input."%' OR `category` LIKE '%".$input."%' ) ORDER BY `seriesName` ASC";
		}
		$result  = mysqli_query($query) or die('Error : ' . mysqli_error());
		$numrows = mysqli_num_rows($result);
		$data = array();
		if($numrows > 0)
		{
			$i=0;
			while(list($id,$seriesName,$fullSeriesName,$seoname,$ratingLink,$category,$total_reviews) = mysqli_fetch_array($result))
			{
				$fullSeriesName = stripslashes($fullSeriesName);
				$data['id'] = $id;
				$data['type'] = 1;
				$data['seriesname'] = $seriesName;
				$data['fullSeriesName'] = $fullSeriesName;
				$data['seoname'] = $seoname;
				$data['ratingLink'] = $ratingLink;
				$data['category'] = $category;
				$data['totalReviews'] = $total_reviews;
				$i++;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
	}


	private function array_searchEpisodes($input)
	{
	}

	private function srray_searchUsers($input)
	{
	}

	private function array_searchComments($input)
	{
	}

	public function seriesStatistics($id) {
		mysqli_query("SET NAMES 'utf8'");
		$query = "SELECT kanji, romaji FROM series WHERE id='$id';";
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		$row = mysqli_fetch_array($result);
		$kanji = $row['kanji'];
		$romaji = $row['romaji'];

		$returnInfo = $this->limitCharacters($romaji,40).'<br />'.$this->limitCharacters($kanji,40);
		return $returnInfo;
	}

	private function limitCharacters($input,$chars)
	{
		$counted = strlen($input);
		$properamoutn = strlen($input)-$chars;
		if($counted > $chars){$information2 = '<span title="'.$input.'">'.substr($input,-$counted,$chars).'..</span>';}
		else {$information2 = $input;}
		return $information2;
	}

	public function seoCheck($seriesName)
	{
		$query = "SELECT seoname FROM series WHERE seriesName='$seriesName'";
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		$row = mysqli_fetch_array($result);
		$seoname = $row['seoname'];
		return $seoname;
	}

	public function getImageUrl($size,$uid,$type)
	{
		if($type == 'anime')
		{
			$returnUrl = '//i.animeftw.tv/resize/anime/s-small/' . $uid . '.jpg';
		}
		else {
			$query   = "SELECT avatarActivate, avatarExtension FROM users WHERE ID='".$uid."'";
			$result  = mysqli_query($query) or die('Error, query failed');
			$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
			$avatarActivate = $row['avatarActivate'];
			if($type == 'profile')
			{
				if($avatarActivate == 'no')
				{
					$returnUrl = $this->Host . '/avatars/default.gif';
				}
				else {
					$returnUrl = $this->Host . '/avatars/user'.$uid.'.'.$row['avatarExtension'];
				}
			}
			else {
				if($avatarActivate == 'no')
				{
					$returnUrl = '//i.animeftw.tv/resize/user/'.$size.'/default.gif';
				}
				else {
					$returnUrl = '//i.animeftw.tv/resize/user/'.$size.'/user'.$uid.'.'.$row['avatarExtension'];
				}
			}
		}
		return $returnUrl;
	}

	public function adv_count_words($str)
	{
		$words = 0;
		$str = eregi_replace(" +", " ", $str);
		$array = explode(" ", $str);
		for($i=0;$i < count($array);$i++)
		{
	 		if (eregi("[0-9A-Za-zÀ-ÖØ-öø-ÿ]", $array[$i]))
		 	$words++;
		}
		return $words;
	}
}