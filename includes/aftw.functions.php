<?php
#***********************************************************
#* aftw.class.php, Classes file for AnimeFTW.tv
#* Written by Brad Riemann
#* Copywrite 2011 FTW Entertainment LLC
#* Distrobution of this is stricty forbidden
#***********************************************************

include 'config.php';
include 'newsOpenDb.php';
$siteroot = 'www.animeftw.tv';

#-----------------------------------------
#* CleanFileName
#* @bool: Raw
#* cleans a specific input of all special chars, makes it seo friendly.
#-----------------------------------------

function CleanFileName($Raw){
	$Raw = trim($Raw);
	$RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" );
	$ReplaceWith = array("-", "", "-");
	return preg_replace($RemoveChars, $ReplaceWith, $Raw);
}

#-----------------------------------------
#* CleanFileName
#* @bool: Raw
#* cleans a specific input of all special chars, makes it seo friendly.
#-----------------------------------------

function makeFriendly($postUsername) {
	// Replace spaces with underscores
	$output = preg_replace("/\s/e" , "_" , $postUsername);
			
	// Remove non-word characters
	$output = preg_replace("/\W/e" , "" , $output);
			
	return strtolower($output);
}	
			
#-----------------------------------------------------------
# Function checkKanji
# take a series name and check the kanji
#-----------------------------------------------------------	

function checkKanjiV2($seriesName) {
	mysql_query("SET NAMES 'utf8'"); 
	$query = "SELECT kanji FROM series WHERE seriesName='$seriesName';";
	$result = mysql_query($query) or die('Error : ' . mysql_error());
	$row = mysql_fetch_array($result);
	$kanji = $row['kanji']; 
	return $kanji;
}	
?>