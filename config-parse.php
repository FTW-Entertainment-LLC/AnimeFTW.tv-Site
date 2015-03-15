<?php
  #################################################
 #
 # >> Config System
 #    Author: Adam Treadway (Zigbigidorlu)
 #   Purpose: Provide config data to source
 # Copyright: Copyright (c) 2011/2012 AnimeFTW.tv
 #    Rights: All rights reserved.
 #     Notes: None.
 #
  #################################################

 Class PMConfig Extends Common {
	public $conf,$lang;

	public function PMConfig() {
		$this::loadConfig();
		$this::loadLang();
		return array("config"=>$this->conf,"lang"=>$this->lang);
	}

	private function loadConfig() {
		if(file_exists("config.ini")) {
			$cfgfile = file("config.ini",FILE_IGNORE_NEW_LINES);
			foreach($cfgfile as $line) {
				$line = trim($line);
				if(substr($line,0,1) == "@") {
					$temp = explode("=",$line);
					if(count($temp) > 1) {
						$varname = strtolower(trim(substr($temp[0],1)));
						array_shift($temp);
						$this->conf[$varname] = trim(implode("=",$temp));
					}
				}
			}
		} else {
			$this::term("Config.ini not found!");
		}
	}

	private function loadLang() {
		if(file_exists("lang/".$this->conf['lang'].".ini")) {
			$langfile = file("lang/".$this->conf['lang'].".ini",FILE_IGNORE_NEW_LINES);
			foreach($langfile as $line) {
				$line = trim($line);
				if(substr($line,0,1) == "@") {
					$temp = explode("=",$line);
					if(count($temp) > 1) {
						$varname = strtolower(trim(substr($temp[0],1)));
						array_shift($temp);
						$this->lang[$varname] = trim(implode("=",$temp));
					}
				}
			}
		} else {
			$this::term("Missing or invalid language (".$this->conf['LANG']."). Please check config.");
		}
	}
	public function __destruct() {}
 }
?>