<?php
include_once('includes/classes/config.class.php');
include_once('includes/classes/search.class.php');
$Config = new Config();
$Config->buildUserInformation(TRUE);
$Search = new Search();
$profileArray = $Config->outputUserInformation();

if($profileArray[2] == 0)
{
	$aonly = "AND aonly='0'";
}
else if ($profileArray[2] == 3)
{
	$aonly = "AND aonly<='1'";
}
else
{
	$aonly = '';
}
function strpos_arr($haystack, $needle) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $what) {
        if(($pos = strpos($haystack, $what))!==false) return $pos;
    }
    return false;
}
if(isset($_POST['q']) || isset($_GET['q']))
{
	if(isset($_GET['q']))
	{
		$q = $_GET['q'];
	}
	else
	{
		$q = $_POST['q'];
	}
	if($profileArray[0] == 0)
	{
		echo '<li><div align="center" style="color:black;padding:2px;">Due to DMCA Abuse, this feature has been removed for unregistered members.</div></li>';
	}
	else
	{
		$SearchInput = strtolower($q);
		$SearchInput = mysqli_real_escape_string($SearchInput);
		//$episodeSearch = strpos($SearchInput,'episode');
		$episodeSearch = strpos_arr($SearchInput,array('episode','ep','movie','ova'));

		mysqli_query("SET NAMES 'utf8'"); 
		if($episodeSearch === false)
		{
			// episode string NOT found
			if($profileArray[2] == 1)
			{
				$Searched = mysqli_query("Select V1, V2, V3, V4 FROM ((SELECT Username as V1, Active as V2, Level_access as V3, ID as V4 FROM users WHERE Username LIKE '%".$SearchInput."%') UNION ALL (Select fullSeriesName as V1, active as V2, seoname as V3, id as V4 FROM series WHERE active='yes' ".$aonly." AND ( fullSeriesName LIKE '%".$SearchInput."%' OR romaji LIKE '%".$SearchInput."%' OR kanji LIKE '%".$SearchInput."%' ))) AS temp_table ORDER BY V1 ASC LIMIT 8");
			}
			else
			{
				$Searched = mysqli_query("Select V1, V2, V3, V4 FROM ((SELECT Username as V1, Active as V2, Level_access as V3, ID as V4 FROM users WHERE Username LIKE '%".$SearchInput."%' AND ID IN (SELECT reqFriend FROM friends WHERE Asker='".$globalnonid."')) UNION ALL (Select fullSeriesName as V1, active as V2, seoname as V3, id as V4 FROM series WHERE active='yes' ".$aonly." AND ( fullSeriesName LIKE '%".$SearchInput."%' OR romaji LIKE '%".$SearchInput."%' OR kanji LIKE '%".$SearchInput."%' ))) AS temp_table ORDER BY V1 ASC LIMIT 8");
			}
		}
		else
		{
			$WordCount = $Search->adv_count_words($SearchInput);
			$SearchExplode = explode(" ", $SearchInput);
			if(strpos($SearchInput,'ep') !== false || strpos($SearchInput,'episode') !== false)
			{
				$movvar = "AND Movie = '0'";
				$movoep = 'ep';
			}
			else if(strpos($SearchInput,'movie') !== false)
			{
				$movvar = "AND Movie = '1'";
				$movoep = 'movie';
			}
			else if(strpos($SearchInput,'ova') !== false)
			{
				$movvar = "AND ova = '1'";
				$movoep = 'ova';
			}
			$s = 0;
			$i2 = $WordCount-3;
			while ($s <= $i2)
			{
				@$SeriesNameFull .= $SearchExplode[$s];
				if($s == $i2){$SeriesNameFull .= "";}
				else if($s >= 0){$SeriesNameFull .= " ";}
				$s++;
			}
			$ie = $s+1;
			@$SeriesNameFull = reverseCheckSeries($SeriesNameFull);
			$Searched = mysqli_query("SELECT epnumber as V1, videotype as V2, epname as V3, epprefix as V4, id, sid FROM episode WHERE seriesname='".@$SeriesNameFull."' AND epnumber = '".$SearchExplode[$ie]."' ".$movvar);
		}
		
		$total_queries = mysqli_num_rows($Searched);
		if($total_queries < 1)
		{
			$StatusMessage = '<li><div align="center" style="color:black;padding:2px;">Zero results found.</div></li>';
		}
		else
		{
			$i = '';
			if($total_queries <= 7)
			{
				$StatusMessage = '';
			}
			else
			{
				if($SearchInput == '%' || $SearchInput == '')
				{
					$i = 9;
					$StatusMessage = '';
				}
				else {
					$StatusMessage = '<li><a href="/search?sid='.md5(time()).'&q='.$SearchInput.'"><div align="center">Displaying first 8 results for '.$SearchInput.'<br />Hit Enter for more.</div></a></li>';
				}
			}
			while(($City = mysqli_fetch_assoc($Searched)) && ($i <= 8))
			{
				$Name = utf8_encode(stripslashes($City['V1']));
				if($City['V2'] == '1'){
					$ModifiedV1 = '';
					$V1 = $Name;
					$query   = mysqli_query("SELECT id FROM friends WHERE reqFriend='".$City['V4']."' AND Asker='".$profileArray[1]."'");
					$tm = mysqli_num_rows($query);
					if($tm == 1){$V2 = '- <span style="color:#006600;">Friend</span>';}else{$V2 = '- <span style="color:#CC0000;">User</span>';}
					$V3 = '/user/'.$City['V1'];
					$V4 = '';
					//$sphoto = '<img src="' . $Search->getImageUrl('x-sm',$City['V4'],'user') . '" class="sphoto" alt="" border="0" align="left" style="padding-right:5px;padding-top:3px;" />';
					$sphoto = $Config->formatAvatar($City['V4'],'blank',FALSE,TRUE);
				}
				else if ($City['V2'] == 'divx' || $City['V2'] == 'mkv' || $City['V2'] == 'mp4')
				{ 
					$V1 = stripslashes($City['V3']);
					$V2 = '';
					$V3 = '/anime/' . $Search->seoCheck($SeriesNameFull) . '/' . $movoep . '-' . $City['V1'];
					$V4 = '';
					$sphoto = "<img src=\"{$Config->Host}/video-images/{$City['sid']}/{$City['id']}_screen.jpeg\" class=\"sphoto\" alt=\"\" border=\"0\" align=\"left\" style=\"padding-right:5px;padding-top:3px;\" height=\"50px\" />";
				}
				else{	
					$V1 = $Name;
					$ModifiedV1 = $V1;
					$V2 = '- <span style="color:#0066CC;">Anime</span>';
					$V3 = '/anime/'.$City['V3'].'/';
					$V4 = $Search->seriesStatistics($City['V4']);
					$sphoto = '<img src="' . $Search->getImageUrl('x-sm',$City['V4'],'anime') . '" class="sphoto" alt="" border="0" align="left" style="padding-right:5px;padding-top:3px;" />';
				} //32 chars long..
				
				$Name = str_replace($SearchInput, '<span class="highlight">'.$SearchInput.'</span>', $Name);
				$Name = str_replace(ucfirst($SearchInput), '<span class="highlight">'.ucfirst($SearchInput).'</span>', $Name);
				$Name = $Name;
				
				echo '<li style="vertical-align:top;"><a href="'.$V3.'" style="height:57px;">'.$sphoto.'<b>'.$V1.'</b>&nbsp;'.$V2.'<br />'.$V4.'<br /></a></li>';
				$i++;
			}
		}
		echo $StatusMessage;
	}
}

?>