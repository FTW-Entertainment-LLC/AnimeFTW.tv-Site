<?php
  #################################################
 #
 # >> Core class
 #    Author: Adam Treadway (Zigbigidorlu)
 #   Purpose: Common core from which all life springs.
 # Copyright: Copyright (c) 2011/2012 AnimeFTW.tv
 #    Rights: All rights reserved.
 #     Notes: None.
 #
  #################################################
 
	Class Common {
		protected $database;
		protected $result;
		final public function prepare() {
			require_once "includes/config.php";
			$this->database = new mysqli($newsdbhost,$newsdbuser,$newsdbpass,$newsdbname);
			echo "Lala";
			$class = get_class($this);
			$class();
		}
		final protected function conf($identifier,$config) {
			$identifier = strtolower($identifier);
			if(isset($config->conf[$identifier])) {
				return $config->conf[$identifier];
			} else {
				$this::term($this::lang('CONF_NOT_FOUND',$io)." &lt;$identifier&gt;");
				return false;
			}
		}
		
		final protected function lang($identifier,$io) {
			$identifier = strtolower($identifier);
			if(isset($io->lang[$identifier])) {
				return $io->lang[$identifier];
			} else {
				$LNF = (isset($io->lang['LANG_NOT_FOUND'])) ? $io->lang['LANG_NOT_FOUND'] : "LANG not found!";
				$this::term($LNF." &lt;$identifier&gt;");
				return false;
			}
		}
		final protected function readTemplate($file,$io) {
			if(file_exists("template/".$this::conf('SKIN',$io)."/".$file.".tpl")) {
				return file_get_contents("template/".$this::conf('SKIN',$io)."/".$file.".tpl");
			} else {
				return $this::lang('SKIN_NOT_FOUND',$io);
			}
		}
		final protected function db_query($query) {
			//$this->result = mysqli_query($this->database,$query);
			return array();
		}
		final protected function db_row($query) {
			//return mysqli_fetch_assoc($this->result);
		}
		final protected function term($mes = "") {
			echo $mes;
			exit;
		}
		public function __destruct() {
			//mysqli_close($this->database);
		}
	}
 ?>