<?php
 require "aftw-commonf.php";
 require "config-parse.php";
 Class Messages extends Common {
	private $config, $UserArray;
 	public function __construct() {
		$this->config = new PMConfig();
		
		$errcodes = array(404,403,500);
		if(isset($_GET['error']) && in_array($_GET['error'],$errcodes)) {
			$PageTitle = str_replace("%siteroot%","",$this::lang("404_TITLE",$this->config));
		} else {
			$PageTitle = str_replace("%siteroot%","",$this::lang("MESSAGE_TITLE",$this->config));
		}
		require_once "includes/classes/config.class.php";
		$Config = new Config(); 
		$Config->buildUserInformation();
		$this->UserArray = $Config->outputUserInformation();
	
		include('header.php');
		include('header-nav.php');
		
		$this->userID = $this::getID();
		$this->page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		$this->perPage = $this::Conf('MES_PERPAGE',$this->config);
		
		$index_global_message = $this::lang("MESSAGE_TOP_MESSAGE",$this->config);

		if(isset($_GET['do'])) {
			$do = $_GET['do'];
			switch($do) {
				case "compose":
					$layout = $this::Compose();
					break;
				case "read":
					$id = (isset($_GET['id'])) ? $_GET['id'] : 0;
					$layout = $this::ReadMessage($id);
					break;
				case "sent":
					$layout = $this::ReadBox("readOutbox",$this->page);
					break;
				case "drafts":
					$layout = $this::ReadBox("readDrafts",$this->page);
					break;
				default:
					$layout = $this::ReadBox("readInbox",$this->page);
					break;
			}
		} else {
			$layout = $this::ReadBox("readInbox",$this->page);
		}
		
		$repl = array("%theme_width%","%index_global_message%");
		$with = array(THEME_WIDTH,"$index_global_message");
		if($profileArray[2] == 0 || $profileArray[2] == 3) {
			$layout = preg_replace("/<!-- IF A -->(.+?)<!-- IF A -->/is","$1",$layout);
		} else {
			$layout = preg_replace("/<!-- IF A -->(.+?)<!-- IF A -->/is","",$layout);
		}
		
		$layout = str_replace($repl,$with,$layout);
		echo $layout;
		include('footer.php');
	}

	private function ReadBox($mode,$page) {
		$messages = array();			
		$id = $this->userID;
		
		if($id > 0) {
			$base = ($page-1)*$this->perPage;
			$output = $this::readTemplate("mes_page",$this->config);
			$tpl = $this::readTemplate("mes_row",$this->config);
			if($mode == "readInbox") {
				$baseurl = "/pm/inbox";
				$head_from = array("boxname","col_subj","col_fromto","col_date","col_del");
				$head_to = array($this::lang("MESSAGE_INBOX",$this->config),$this::lang("MESSAGE_SUBJECT",$this->config),
								 $this::lang("MESSAGE_FROM",$this->config),$this::lang("MESSAGE_DATE",$this->config),
								 $this::lang("MESSAGE_DELETE",$this->config));
									
			
				$query = mysqli_query($conn, "SELECT SQL_CALC_FOUND_ROWS `id`,`viewed`,`date`,`msgSubject`,`rid`,`sid` FROM `messages` WHERE `rid`='$id' AND `sent`='0' ORDER BY `id` DESC LIMIT $base,".$this->perPage);
				$total = mysqli_query($conn, "SELECT FOUND_ROWS()");
				$trow = mysqli_fetch_assoc($total);
				while($row = mysqli_fetch_assoc($query)) {
					include_once('includes/classes/config.class.php');
					$Config = new Config();
					$newtime = $Config->timeZoneChange($row['date'],$this->profileArray[3]);
					$row['from'] = $this::nameFromId($row['sid']);
					$newtime = date("D M d Y, h:i a",$newtime);
					$row['time'] = $newtime;
					$row['isowner'] = true;
					
					$row_keys = array_keys($row);
					foreach($row_keys as $key=>$value) { $row_keys[$key] = "%$value%"; }
					
					$messages[] = str_replace($row_keys,$row,$tpl);
				}
				
				foreach($head_from as $key=>$value) { $head_from[$key] = "%$value%"; }
				
				$output = str_replace($head_from,$head_to,$output);
				$output = str_replace("%messages%",implode("\n",$messages),$output);
			} elseif($mode == "readOutbox") {
				$baseurl = "/pm/sent";
				$headers = array( "boxname"=>$this::lang("MESSAGE_OUTBOX",$this->config),
									"col_subj"=>$this::lang("MESSAGE_SUBJECT",$this->config),
									"col_fromto"=>$this::lang("MESSAGE_TO",$this->config),
									"col_date"=>$this::lang("MESSAGE_DATE",$this->config),
									"col_del"=>$this::lang("MESSAGE_DELETE",$this->config));
			
				$query = mysqli_query($conn, "SELECT SQL_CALC_FOUND_ROWS `id`,`viewed`,`date`,`msgSubject`,`rid`,`sid` FROM `messages` WHERE `sid`='$id' AND `sent`='0' ORDER BY `id` DESC LIMIT $base,".$this->perPage);
				$total = mysqli_query($conn, "SELECT FOUND_ROWS()");
				$trow = mysqli_fetch_assoc($total);
				while($row = mysqli_fetch_assoc($query)) {
					$row['from'] = $this::nameFromId($row['rid']);
					include_once('includes/classes/config.class.php');
					$Config = new Config();
					$newtime = $Config->timeZoneChange($row['date'],$this->profileArray[3]);
					$newtime = date("D M d Y, h:i a",$newtime);
					$row['time'] = $newtime;
					$row['isowner'] = false;
					$messages[] = $this::tpl_replace($tpl,$row);
				}
				$output = $this::tpl_replace($output,$headers);
				$output = str_replace("%messages%",implode("\n",$messages),$output);
			} elseif($mode == "readDrafts") {
				$baseurl = "/pm/drafts";
				$headers = array("tpl"=>$this::readTemplate("mes_row",$this->config),
									"boxname"=>$this::lang("MESSAGE_DRAFTS",$this->config),
									"col_subj"=>$this::lang("MESSAGE_SUBJECT",$this->config),
									"col_fromto"=>$this::lang("MESSAGE_TO",$this->config),
									"col_date"=>$this::lang("MESSAGE_DATE",$this->config),
									"col_del"=>$this::lang("MESSAGE_DELETE",$this->config));
			
				$query = mysqli_query($conn, "SELECT SQL_CALC_FOUND_ROWS `id`,`viewed`,`date`,`msgSubject`,`rid`,`sid` FROM `messages` WHERE `sid`='$id' AND `sent`='1' ORDER BY `id` DESC LIMIT $base,".$this->perPage);
				$total = mysqli_query($conn, "SELECT FOUND_ROWS()");
				$trow = mysqli_fetch_assoc($total);
				while($row = mysqli_fetch_assoc($query)) {
					$row['from'] = $this::nameFromId($row['rid']);
					include_once('includes/classes/config.class.php');
					$Config = new Config();
					$newtime = $Config->timeZoneChange($row['date'],$this->profileArray[3]);
					$newtime = date("D M d Y, h:i a",$newtime);
					$row['time'] = $newtime;
					$row['isowner'] = false;
					$messages[] = $this::tpl_replace($tpl,$row);
				}
				$output = $this::tpl_replace($output,$headers);
				$output = str_replace("%messages%",implode("\n",$messages),$output);
			}
			if(isset($output) && $output != "") {
				$total = ceil($trow['FOUND_ROWS()']/$this->perPage);
				$pagination = $this::pagination($page,$total,$baseurl);
				$output = str_replace("%pagination%",$pagination,$output);
				return $output;
			}
			else {
				return $this::lang("MESSAGE_NO_MESSAGES",$this->config);
			}
		} else {
			return $this::lang("NOT_LOGGED_IN",$this->config);
		}
	}
	
	private function Compose() {
		$uid = $this->userID;
		if($uid > 0) {
			$to = (isset($_GET['to'])) ? $this::nameFromId($_GET['to']) : "";
			
			//Subject manipulation
			if(isset($_GET['subj'])) {
				if(is_numeric($_GET['subj'])) {
					$id = $_GET['subj'];
					$uid = $this->userID;
					$q = mysqli_query($conn, "SELECT * FROM `messages` WHERE `id`='$id' AND (`rid`='$uid' OR `sid`='$uid')");
					if(mysqli_num_rows($q) == 1) {
						$r = mysqli_fetch_assoc($q);
						$sub = $r['msgSubject'];
						$pos = stripos($sub,"RE: ");
						$subject = ($pos !== false && $pos == 0) ? "RE: ".substr($sub,4) : "RE: ".$sub;
						
						$message = "[quote='".addslashes($to)."']".$r['msgBody']."[/quote]";
					} else {
					}
				} else {
					$subject = (isset($_GET['subj'])) ? "RE: ".$_GET['subj'] : "";
					$message = "";
				}
			} else {
				$subject = "";
				$message = "";
			}
			
			$tpl = $this::readTemplate("mes_compose",$this->config);
			$output = $this::readTemplate("mes_page",$this->config);
			$rpl = array("boxname"=>$this::lang("MESSAGE_COMPOSE",$this->config),
							"messages"=>$tpl,"col_subj"=>"","col_fromto"=>"","col_date"=>"",
							"to"=>"$to","subj"=>"$subject","message"=>"$message","pagination"=>"");
			return $this::tpl_replace($output,$rpl);
		} else {
			return $this::lang("NOT_LOGGED_IN",$this->config);
		}
	}
		
	private function ReadMessage($id) {
		$uid = $this->userID;
		if($uid > 0) {
			$output = $this::readTemplate("mes_page",$this->config);
			$tpl = $this::readTemplate("mes_view",$this->config);
			$mes_query = mysqli_query($conn, "SELECT * FROM `messages` WHERE `id`='$id' AND (`rid`='$uid' OR `sid`='$uid')");
			if(mysqli_num_rows($mes_query) == 1) {
				mysqli_query($conn, "UPDATE `messages` SET `viewed`='0' WHERE `id`='$id' AND `rid`='$uid'");
			
				$boxname = $this::lang("MESSAGE_READMES",$this->config);
				$mes_row = mysqli_fetch_assoc($mes_query);
				include_once('includes/classes/config.class.php');
				$Config = new Config();
				$newtime = $Config->timeZoneChange($mes_row['date'],$this->profileArray[3]);
				$newtime = date("D M d Y, h:i a",$newtime);
				
				$pos = stripos($mes_row['msgSubject'],"RE: ");
				$sp_subj = ($pos !== false && $pos == 0) ? substr($mes_row['msgSubject'],4) : $mes_row['msgSubject'];
				$tpl = str_replace("%id%",$mes_row['id'],$tpl);
				$tpl = str_replace("%sid%",$mes_row['sid'],$tpl);
				$tpl = str_replace("%ssubj%",addslashes($sp_subj),$tpl);
				$tpl = str_replace("%message%",$this::formatMessage($mes_row['msgBody']),$tpl);
				if($mes_row['rid'] == $uid) $tpl = preg_replace("/<!-- BUTTONS -->(.+?)<!-- BUTTONS -->/is","$1",$tpl);
				else $tpl = preg_replace("/<!-- BUTTONS -->(.+?)<!-- BUTTONS -->/is","",$tpl);
				$output = $this::tpl_replace($output,array("boxname"=>$boxname,
				"col_subj"=>$mes_row['msgSubject'],"col_date"=>$newtime,
				"col_fromto"=>$this::nameFromId($mes_row['sid']),"pagination"=>""));
				
				$output = str_replace("%messages%",$tpl,$output);
				return $output;
			} else {
				$output = $this::tpl_replace($output,array("boxname"=>$this::lang("MESSAGE_READMES",$this->config),
				"col_subject"=>"Message not found","col_date"=>"","col_author"=>"","pagination"=>""));
				$output = str_replace("%messages%",$this::lang("MESSAGE_NOT_FOUND",$this->config),$output);
				return $output;
			}
		} else {
			return $this::lang("NOT_LOGGED_IN",$this->config);
		}
	}
		
	private function ReadDraft($id) {
		$uid = $this->userID;
		if($uid > 0) {
			$draft_query = mysqli_query($conn, "SELECT * FROM `messages` WHERE `id`='$id' AND `sid`='$uid'");
			if(mysqli_num_rows($draft_query) == 1) {
				$draft_row = mysqli_fetch_assoc($draft_query);
				$draft_row['to'] = $this::nameFromId($draft_row['rid']);
				echo json_encode($draft_row);
			}
		} else {
			$this::jsonError("NOT_LOGGED_IN");
		}
	}
	
	private function getID() {
		$this->profileArray = $this->UserArray;
		return $this->profileArray[1];
	}
	
	private function idFromName($name) {
		$id_query = mysqli_query($conn, "SELECT `ID` FROM `users` WHERE `Username`='$name'");
		if(mysqli_num_rows($id_query) == 1) {
			$id_row = mysqli_fetch_assoc($id_query);
			return $id_row['ID'];
		} else {
			return NULL;
		}
	}
	
	private function nameFromId($id) {
		$name_query = mysqli_query($conn, "SELECT `Username` FROM `users` WHERE `ID`='$id'");
		if(mysqli_num_rows($name_query) == 1) {
			$name_row = mysqli_fetch_assoc($name_query);
			return $name_row['Username'];
		} else {
			return FALSE;
		}
	}
	
	private function tpl_replace($tpl,array $rpl) {
		foreach($rpl as $from=>$to) {
			$tpl = str_replace("%$from%",$to,$tpl);
		}
		return $tpl;
	}
	
	private function formatMessage($mes) {
		preg_match_all("/\[quote='(.+?)'\]/is",$mes,$starts);
		preg_match_all("/\[\/quote\]/is",$mes,$ends);
		$count = (count($starts[0]) > count($ends[0])) ? count($ends[0]) : count($starts[0]);
		for($i=0; $i < $count; $i++) {
			$mes = preg_replace("/\[quote='(.+?)'\]/is","<DIV class='pmquser'>$1</DIV><DIV class='pmquote'>",$mes,1);
			$mes = preg_replace("/\[\/quote\]/is","</DIV>",$mes,1);
		}
		$mes = nl2br($mes);
		return $mes;
	}
	
	private function pagination($page,$total,$baseurl) {
		$output = "Pages: ";
		if($total == 0) {
			$output = "";
		}
		if($total < 7) {
			//6 or fewer pages
			// 1 2 3 4 5 6
			for($i=1; $i < ($total+1); $i++) {
				if($page == $i)
					$output .= "<SPAN class=\"pg_this\">$i</SPAN>";
				else
					$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
			}
		}
		else {
		//More than 6 pages
			if($page == 4 || $page == ($total-3)) {
				//Pages 4 and ($total - 4) are special
				// 1 2 ... 3 4 5 ... 98 99 100
				// 1 2 3 ... 96 97 98 ... 99 100
				
				$base_round = ($page == 4) ? 3 : 4;
				$ceil_round = ($page == ($total-3)) ? ($total - 1) : ($total - 2);
				for($i=1; $i < $base_round; $i++)
					$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
				$output .= "<SPAN class=\"pg_ellipse\">...</SPAN>";
				for($i = $page-1; $i < $page+2; $i++) {
					if($page == $i)
						$output .= "<SPAN class=\"pg_this\">$i</SPAN>";
					else
						$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
				}
				$output .= "<SPAN class=\"pg_ellipse\">...</SPAN>";
				for($i=$ceil_round; $i < $total+1; $i++)
					$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
			}
			
			elseif($page < 4 || $page > ($total-3)) {
				//Pages LT 3 and GT ($total-3) and under display first and last only
				// 1 2 3 ... 98 99 100
				$base = ($page < 4) ? 5 : 4;
				$ceil = ($page > ($total-4)) ? 3 : 2;
				for($i=1; $i < $base; $i++) {
					if($page == $i)
						$output .= "<SPAN class=\"pg_this\">$i</SPAN>";
					else
						$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
				}
				$output .= "<SPAN class=\"pg_ellipse\">...</SPAN>";
				for($i=($total - $ceil); $i < $total+1; $i++) {
					if($page == $i)
						$output .= "<SPAN class=\"pg_this\">$i</SPAN>";
					else
						$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
				}
			}
			
			else {
				//Pages in the middle
				// 1 2 3 ... 49 50 51 ... 98 99 100
				for($i=1; $i < 4; $i++)
					$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
				$output .= "<SPAN class=\"pg_ellipse\">...</SPAN>";
				for($i=($page - 1); $i < $page+2; $i++) {
					if($page == $i)
						$output .= "<SPAN class=\"pg_this\">$i</SPAN>";
					else
						$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
				}
				$output .= "<SPAN class=\"pg_ellipse\">...</SPAN>";
				for($i=$total-2; $i < $total+1; $i++)
					$output .= "<SPAN class=\"pg_list\" onclick=\"location.href='$baseurl/$i'\">$i</SPAN>";
			}
		}
		return $output;
		
	}
 }
 new Messages();