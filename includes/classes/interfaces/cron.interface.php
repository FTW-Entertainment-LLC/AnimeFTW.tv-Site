<?php
/****************************************************************\
## FileName: cron.interface.php
## Author: Nikey
## Usage: An interface to standardize cron classes
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/


interface ICron {

	public function __construct($id);
	public function run();
	public function __destruct();

}