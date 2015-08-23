<?php
/****************************************************************\
## FileName: errors.class.php								 
## Author: Brad Riemann								 
## Usage: Errors sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Errors extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		echo '<div  class="body-container">Right column stuff.</div>';
	}
	
}

?>