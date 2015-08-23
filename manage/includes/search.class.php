<?php
/****************************************************************\
## FileName: search.class.php								 
## Author: Brad Riemann								 
## Usage: Search sub class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Search extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		$this->managementSearch();
	}
	
	private function managementSearch()
	{
		$query = "SELECT ";
	}
	/*
	Select 
	V1, V2, V3, V4, V5
FROM 
	(
		(
		SELECT 
			ID as V1, Username as V2, Active as V3, Level_access as V4, 'user' as V5
		FROM 
			users 
		WHERE 
			Username LIKE '%robot%' OR display_name LIKE '%robot%'
		) 
		UNION
		(
		Select 
			id as V1, fullSeriesName as V2, active as V3, seoname as V4, 'series' as V5
		FROM 
			series 
		WHERE 
			fullSeriesName LIKE '%robot%' OR romaji LIKE '%robot%' OR kanji LIKE '%robot%'
		)
		UNION
		(
		Select 
			id as V1, seriesname as V2, epname as V3, epprefix as V4, 'episode' as V5
		FROM 
			episode 
		WHERE 
			epname LIKE '%robot%' OR subGroup LIKE '%robot%'
		)
	) 
AS 
	temp_table 
ORDER BY 
	V1 ASC
LIMIT 0 ,10
*/
}