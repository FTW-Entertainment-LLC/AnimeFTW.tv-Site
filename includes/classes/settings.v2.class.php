<?php
/****************************************************************\
## FileName: series.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Series Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Settings extends Config {

	public $Data, $UserArray, $DevArray, $permissionArray, $MessageCodes;

	public function __construct($Data = NULL,$UserArray = NULL,$DevArray = NULL,$permissionArray = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserArray = $UserArray;
		$this->DevArray = $DevArray;
		$this->permissionArray = $permissionArray;
	}

	public function array_displayAppSettings() {
		$results = array('status' => 200, 'message' => "Request Successful.");
        $results['top-categories'] = '1,6,15';
		if($this->DevArray['ads'] == 1 && $this->UserArray['Level_access'] == 3) {
			// The developer has ads, so we allow them to see ads in the request.
			$results['enabled'] = '1';
			$results['total'] = '1';			
			$results['ads'][0]['id'] = '1';
			$results['ads'][0]['unit-name'] = 'banner-0';
			$results['ads'][0]['unit-id'] = 'ca-app-pub-8589185802146818/8669312195';
			$results['ads'][0]['format'] = 'BANNER';
			$results['ads'][0]['enabled'] = '1';
		}
		else {
			$results['enabled'] = '0';
			$results['total'] = '0';
		}
		return $results;
	}
}