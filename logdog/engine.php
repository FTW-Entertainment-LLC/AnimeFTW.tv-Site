<?php
 ###################################################
 # LogDog -> Engine 1.0
 # Author..... Adam Treadway, FTW Entertainment, LLC
 # Purpose:... LogDog return engine
 # Copyright.. Copyright (c) 2012 FTW Entertainment, LLC
 # Notes...... Do not distribute.
 ###################################################
 
 Class LogDog {
	private $keywords = array("user","developer","agent","ip","query","from","till");
	private $method = array();
	private $store = array();
	private $dataset = array();
	public function goGet($query) {
		//Lower case and strip all punctuation
		$lquery = strtolower($query);
		$cquery = preg_replace("/[^a-zA-Z0-9 \"._-]/u", "", $lquery);
		
		//Perform keyword check
		foreach($this->keywords as $k) {
			if(strpos($cquery,$k) !== false) {
			
				//Non-quoted phrasing
				preg_match_all("/$k [a-zA-Z0-9._-]+/",$cquery,$matches);
				if(count($matches[0])) {
					//Place key phrases into the store array
					foreach($matches[0] as $inv) {
						$x = explode(" ",$inv);
						if(count($x) == 2) {
							$this->method[] = $x[0];
							$this->store[] = $x[1];
						}
					}
				}
				
				//Quoted phrasing
				preg_match_all("/$k \"[a-zA-Z0-9 ._-]+\"/",$cquery,$matches);
				if(count($matches[0])) {
					//Place key phrases into the store array
					foreach($matches[0] as $inv) {
						$x = explode(" ",$inv,2);
						if(count($x) == 2) {
							$this->method[] = $x[0];
							$this->store[] = str_replace("\"","",$x[1]);
						}
					}
				}
			}
		}

		//Special Queries
		if($lquery == "help") {
			return $this->help();
		} elseif($lquery == "i like trains") {
			return $this->iLikeTrains();
		} else {
			//Method detected, manage query
			if(count($this->method)) {
				$this->constructResults();
			} else {
				//Developer search
				if(is_numeric($lquery)) {
				} else {
					//User search
					$user_query = $this->query("SELECT `id` FROM `users` WHERE `Username`='%s'",array("$lquery"));
					if($user_query) {
						$this->method = array("user");
						$this->store = array("$lquery");
						return $this->constructResults();
					} else {
						return $this->badBoy();
					}
				}
			}
		}
	}
	
	/*=================================================
	@ Return functions
	=================================================*/
	private function constructResults() {
		//This is the big thinker function.
		//Builds the result set based on the interpretation of input
		$build = array(); $safe = array(); $exhaust = array();
		if(count($this->method) == count($this->store)) {
			foreach($this->method as $method) {
				if($method == "user") {
					//Check if method has been used yet
					if(!in_array("user",$exhaust)) {
						$uid = array();
						$keys = array_keys($this->method,"user");
						foreach($keys as $key) {
							$val = $this->store[$key];
							$user_query = $this->query("SELECT `id` FROM `users` WHERE `Username`='%s' LIMIT 1",array($val));
							if($user_query) {
								$uid[] = $user_query['id'];
							} else {
								//Oops, user doesn't exist.
							}
						}
						
						//Build the query string
						$query = array();
						foreach($uid as $id) {
							$query[] = "`uid`='$id'";
						}
						$query = "(".implode(" OR ",$query).")";
						$build[] = $query;
						$exhaust[] = "user";
					}
				} elseif($method == "developer") {
					//Check if method has been used yet
					if(!in_array("developer",$exhaust)) {
						$did = array();
						$keys = array_keys($this->method,"developer");
						foreach($keys as $key) {
							$did[] = $this->store[$key];
						}
						
						//Build the query string
						$query = array();
						foreach($did as $id) {
							$query[] = "`did`='$id'";
						}
						$query = "(".implode(" OR ",$query).")";
						$build[] = $query;
						$exhaust[] = "developer";
					}
				} elseif($method == "agent") {
					//Check if method has been used yet
					if(!in_array("agent",$exhaust)) {
						$agent = array();
						$keys = array_keys($this->method,"agent");
						foreach($keys as $key) {
							$agent[] = $this->store[$key];
						}
						
						//Build the query string
						$query = array();
						foreach($agent as $a) {
							$query[] = "`agent` LIKE '%$a%'";
						}
						$query = "(".implode(" OR ",$query).")";
						$build[] = $query;
						$exhaust[] = "agent";
					}
				} elseif($method == "ip") {
					//Check if method has been used yet
					if(!in_array("ip",$exhaust)) {
						$ipaddr = array();
						$keys = array_keys($this->method,"ip");
						foreach($keys as $key) {
							$ipaddr[] = $this->store[$key];
						}
						
						//Build the query string
						$query = array();
						foreach($ipaddr as $ip) {
							$query[] = "`ip`='$ip'";
						}
						$query = "(".implode(" OR ",$query).")";
						$build[] = $query;
						$exhaust[] = "ip";
					}
				} elseif($method == "query") {
					//Check if method has been used yet
					if(!in_array("query",$exhaust)) {
						$rquery = array();
						$keys = array_keys($this->method,"query");
						foreach($keys as $key) {
							$rquery[] = $this->store[$key];
						}
						
						//Build the query string
						$query = array();
						foreach($rquery as $q) {
							$query[] = "`url` LIKE '%$q%'";
						}
						$query = "(".implode(" OR ",$query).")";
						$build[] = $query;
						$exhaust[] = "query";
					}
				} elseif($method == "from") {
					//Check if method has been used yet
					if(!in_array("from",$exhaust)) {
						$keys = array_keys($this->method,"from");
						$till = array_keys($this->method,"till");
						$time = $this->store[$keys[0]];
						if($time = strtotime($time)) {
							//If TILL is found (and valid), we build a range
							if(count($till) && $till = strtotime($this->store[$till[0]])) {
								$build[] = "(`date`>=$time AND `date`<=$till)";
							}
							//Otherwise...
							else {
								//If the exact falls on midnight, assume a day's range
								if(date("G",$time) == 0 && date("i",$time) == 0 && date("s",$time) == 0) {
									$till = $time+86400;
									$build[] = "(`date`>=$time AND `date`<=$till)";
								} else {
									$build[] = "(`date`=$time)";
								}
							}
						} else {
							//Time didn't convert properly. Abort.
						}
						$exhaust[] = "from";
					}
				} elseif($method == "till") {
					//This is automatically handled in FROM
				} else {
					//Should never happen. Only called when a method in the array doesn't exist.
					return $this->badBoy();
				}
			}
			
			//Build the data set
			$build = implode(" AND ",$build);
			$query = "SELECT * FROM `developers_logs` WHERE $build ORDER BY `id` DESC LIMIT 0,100";
			$query = $this->query($query,array(),true);
			while($row = mysql_fetch_assoc($query)) {
				//Collect the username
				$uid = $row['uid'];
				if($uid != 0) {
					$user_query = $this->query("SELECT `Username` FROM `users` WHERE `id`='$uid' LIMIT 1");
					$row['username'] = $user_query['Username'];
				} else {
					$row['username'] = "Unknown";
				}
				//Collect the real DID
				$did = $row['did'];
				if($uid != 0) {
					$dev_query = $this->query("SELECT `name`,`devkey` FROM `developers` WHERE `id`='$did' LIMIT 1");
					$row['devkey'] = $dev_query['devkey'];
					$row['devname'] = $dev_query['name'];
				} else {
					$row['devkey'] = "Unknown";
					$row['devname'] = "Unknown";
				}
				$this->dataset[] = $row;
			}
			
			//Return parsed data
			return $this->renderDataset();
		} else {
			return $this->badBoy();
		}
	}
	
	//Our render function. Produces the output for the screen.
	private function renderDataset() {
		$out = "";
		$tpl = fetch("resultset");
		foreach($this->dataset as $set) {
			$temp = $tpl;
			$temp = str_replace("%user%",$set['username'],$temp);
			$temp = str_replace("%ip%",$set['ip'],$temp);
			$temp = str_replace("%time%",date("D M dS, Y H:i a",$set['date']),$temp);
			$temp = str_replace("%did%",$set['did'],$temp);
			$temp = str_replace("%devname%",$set['devname'],$temp);
			$temp = str_replace("%devkey%",$set['devkey'],$temp);
			$temp = str_replace("%agent%",$set['agent'],$temp);
			$temp = str_replace("%url%",$set['url'],$temp);
			$out .= $temp;
		}
		return $out;
	}
	
	/*=================================================
	@ Static functions
	=================================================*/
	private function help() {
		return $this->fetch("help");
	}
	//Easter Egg
	private function iLikeTrains() {
		return $this->fetch("e2");
	}
	private function badBoy() {
		return $this->fetch("error");
	}
	
	/*=================================================
	@ Data related functions
	=================================================*/
	private function query($query,$safe = array(),$resource = false) {
		foreach($safe as $word) {
			$s = 1;
			$word = mysql_real_escape_string($word);
			$query = str_replace("%s",$word,$query,$s);
		}
		$query = mysql_query($query) or die(mysql_error());
		if($resource == true) return $query;
		else return mysql_fetch_assoc($query);
	}
	private function fetch($io,array $rpl = array()) {
		$io = file_get_contents("tpl/$io.tpl");
		foreach($rpl as $key=>$value) {
			$io = str_replace("%$key%",$value,$io);
		}
		return $io;
	}
 }
?>