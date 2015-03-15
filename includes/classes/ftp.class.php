<?php

class FTP {
	
	#####################
	# Public Functions	#
	#####################
	
	public function __construct($server,$port=21,$to=120){
		$this->connection = ftp_connect($server,$port,$to);
		if(!$this->connection){
			$this->throwError("Error connecting to FTP host");
		}
		$this->mode = FTP_BINARY;
	}

	public function login($usr,$pass){
		if(!ftp_login($this->connection,$usr,$pass)){
			$this->throwError("Username or Password invalid");
		}
	}

	public function put($file,$dest){
		if(!ftp_put($this->connection,$dest,$file,$this->mode)){
			$this->throwError("File upload not successful");
		}
	}

	public function kill(){
		ftp_close($this->connection);
	}
	
	#####################
	# Private Functions	#
	#####################
	
	private function setMode($mode){
		$this->mode = $mode;
	}

	private function throwError($msg){
		die($msg);
	}
}
/*
include_once( "includes/ftp.class.php" );

$ftp = new FTP("208.53.158.178");
$ftp->login("remote@images.animeftw.tv", "mui(;Qg_5T~+");
$ftp->put( "source.php", "destination.php" );
$ftp->kill();
*/
?>