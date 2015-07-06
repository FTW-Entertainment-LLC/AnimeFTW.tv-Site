<?php
  #################################################
 #
 # >> Message System
 #    Author: Adam Treadway (Zigbigidorlu)
 #   Purpose: Peer-to-peer private message system
 # Copyright: Copyright (c) 2011/2012 AnimeFTW.tv
 #    Rights: All rights reserved.
 #     Notes: None.
 #
  #################################################
 
	session_start();
	require_once "includes/classes/config.class.php";
 	require_once "aftw-commonf.php";
	require_once "config-parse.php";
 
	Class Messages Extends Common {
		protected $config;
		
		public function Messages() {
			$this->config = new PMConfig();
			$this->userID = $this::getID();
			$this->perPage = $this::Conf('MES_PERPAGE',$this->config);
			$this::doMessages();
		}
		
		private function doMessages() {
			$method = (isset($_GET['method'])) ? $_GET['method'] : "backend";
			if($method == "AJAX") {
				$mode = (isset($_GET['mode'])) ? $_GET['mode'] : NULL;
				switch($mode) {
					case "compose":
						$this::Compose();
						break;
					case "readInbox":
					case "readOutbox":
					case "readDrafts":
						$page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
						$this::ReadBox($mode,$page);
						break;
					case "readMessage":
						$id = isset($_GET['id']) ? $_GET['id'] : 0;
						$this::ReadMessage($id);
						break;
					case "getDraft":
						$id = isset($_GET['id']) ? $_GET['id'] : 0;
						$this::ReadDraft($id);
						break;
					case "doSend":
						$this::SendMessage();
						break;
					case "doSave":
						$this::SendMessage(true);
						break;
					case "delete":
						$id = isset($_GET['id']) ? $_GET['id'] : NULL;
						$this::delMessage($id);
						break;
					default:
						echo $this::lang("SPLASH_ATTACK",$this->config);
						break;
				}
			} else {
				echo $this::lang("SPLASH_ATTACK",$this->config);
			}
		}
		
		# Information based functions
		private function ReadBox($mode,$page) {
			$Config = new Config();
			$messages = array();			
			$id = $this->userID;
			
			if($id > 0) {
				$base = ($page-1)*$this->perPage;
				if($mode == "readInbox") {
					$messages[] = array("tpl"=>$this::readTemplate("mes_row",$this->config),
										"boxname"=>$this::lang("MESSAGE_INBOX",$this->config),
										"col_subj"=>$this::lang("MESSAGE_SUBJECT",$this->config),
										"col_fromto"=>$this::lang("MESSAGE_FROM",$this->config),
										"col_date"=>$this::lang("MESSAGE_DATE",$this->config),
										"col_del"=>$this::lang("MESSAGE_DELETE",$this->config));
				
					$query = mysql_query("SELECT `id`,`viewed`,`date`,`msgSubject`,`rid`,`sid` FROM `messages` WHERE `rid`='$id' AND `sent`='0' ORDER BY `id` DESC LIMIT $base,".$this->perPage);
					while($row = mysql_fetch_assoc($query)) {
						$row['from'] = $this::nameFromId($row['sid']);
						
						$newtime = $Config->timeZoneChange($row['date'],$this->profileArray[3]);
						$newtime = date("D M d Y, h:i a",$newtime);
						$row['time'] = $newtime;
						$row['isowner'] = true;
						$messages[] = $row;
					}
				} elseif($mode == "readOutbox") {
					$messages[] = array("tpl"=>$this::readTemplate("mes_row",$this->config),
										"boxname"=>$this::lang("MESSAGE_OUTBOX",$this->config),
										"col_subj"=>$this::lang("MESSAGE_SUBJECT",$this->config),
										"col_fromto"=>$this::lang("MESSAGE_TO",$this->config),
										"col_date"=>$this::lang("MESSAGE_DATE",$this->config),
										"col_del"=>$this::lang("MESSAGE_DELETE",$this->config));
				
					$query = mysql_query("SELECT `id`,`viewed`,`date`,`msgSubject`,`rid`,`sid` FROM `messages` WHERE `sid`='$id' AND `sent`='0' ORDER BY `id` DESC LIMIT $base,".$this->perPage);
					while($row = mysql_fetch_assoc($query)) {
						$row['from'] = $this::nameFromId($row['rid']);
						$newtime = $Config->timeZoneChange($row['date'],$this->profileArray[3]);
						$newtime = date("D M d Y, h:i a",$newtime);
						$row['time'] = $newtime;
						$row['isowner'] = false;
						$messages[] = $row;
					}
				} elseif($mode == "readDrafts") {
					$messages[] = array("tpl"=>$this::readTemplate("mes_row",$this->config),
										"boxname"=>$this::lang("MESSAGE_DRAFTS",$this->config),
										"col_subj"=>$this::lang("MESSAGE_SUBJECT",$this->config),
										"col_fromto"=>$this::lang("MESSAGE_TO",$this->config),
										"col_date"=>$this::lang("MESSAGE_DATE",$this->config),
										"col_del"=>$this::lang("MESSAGE_DELETE",$this->config));
				
					$query = mysql_query("SELECT `id`,`viewed`,`date`,`msgSubject`,`rid`,`sid` FROM `messages` WHERE `sid`='$id' AND `sent`='1' ORDER BY `id` DESC LIMIT $base,".$this->perPage);
					while($row = mysql_fetch_assoc($query)) {
						$row['from'] = $this::nameFromId($row['rid']);
						$newtime = $Config->timeZoneChange($row['date'],$this->profileArray[3]);
						$newtime = date("D M d Y, h:i a",$newtime);
						$row['time'] = $newtime;
						$row['isowner'] = false;
						$messages[] = $row;
					}
				}
				if(isset($messages[1])) {
					$parsed = json_encode($messages);
					echo $parsed;
				}
				else {
					$this::jsonError("MESSAGE_NO_MESSAGES");
				}
			} else {
				$this::jsonError("NOT_LOGGED_IN");
			}
		}
		
		private function Compose() {
			$uid = $this->userID;
			if($uid > 0) {
				$output = array("tpl"=>$this::readTemplate("mes_compose",$this->config),
								"boxname"=>$this::lang("MESSAGE_COMPOSE",$this->config));
				echo json_encode($output);
			} else {
				$this::jsonError("NOT_LOGGED_IN");
			}
		}
		
		private function ReadMessage($id) {
			$uid = $this->userID;
			if($uid > 0) {
				$tpl = $this::readTemplate("mes_view",$this->config);
				$mes_query = mysql_query("SELECT * FROM `messages` WHERE `id`='$id' AND (`rid`='$uid' OR `sid`='$uid')");
				if(mysql_num_rows($mes_query) == 1) {
					mysql_query("UPDATE `messages` SET `viewed`='0' WHERE `id`='$id' AND `rid`='$uid'");
				
					$boxname = $this::lang("MESSAGE_READMES",$this->config);
					$mes_row = mysql_fetch_assoc($mes_query);
					$newtime = $Config->timeZoneChange($mes_row['date'],$this->profileArray[3]);
					$newtime = date("D M d Y, h:i a",$newtime);
					$tpl = str_replace("%id%",$mes_row['id'],$tpl);
					$tpl = str_replace("%message%",nl2br($mes_row['msgBody']),$tpl);
					if($mes_row['rid'] == $uid) $tpl = preg_replace("/<!-- BUTTONS -->(.+?)<!-- BUTTONS -->/is","$1",$tpl);
					else $tpl = preg_replace("/<!-- BUTTONS -->(.+?)<!-- BUTTONS -->/is","",$tpl);
					echo json_encode(array("tpl"=>$tpl,"boxname"=>$boxname,
					"subject"=>$mes_row['msgSubject'],"date"=>$newtime,
					"author"=>$this::nameFromId($mes_row['sid'])));
				} else {
					$this::jsonError("MESSAGE_NOT_FOUND");
				}
			} else {
				$this::jsonError("NOT_LOGGED_IN");
			}
		}
		
		private function ReadDraft($id) {
			$uid = $this->userID;
			if($uid > 0) {
				$draft_query = mysql_query("SELECT * FROM `messages` WHERE `id`='$id' AND `sid`='$uid'");
				if(mysql_num_rows($draft_query) == 1) {
					$draft_row = mysql_fetch_assoc($draft_query);
					$draft_row['to'] = $this::nameFromId($draft_row['rid']);
					echo json_encode($draft_row);
				}
			} else {
				$this::jsonError("NOT_LOGGED_IN");
			}
		}
		
		private function SendMessage($save = false) {
			$uid = $this->userID;
			$rname = (isset($_GET['to'])) ? $_GET['to'] : NULL;
			$subject = (isset($_GET['subj'])) ? $_GET['subj'] : NULL;
			$message = (isset($_GET['mes'])) ? $_GET['mes'] : NULL;
			$date = time();
			$rid = $this::idFromName($rname);
			$ip = $_SERVER['REMOTE_ADDR'];
			
			$subject = mysql_real_escape_string($subject);
			$message = mysql_real_escape_string($message);
			
			if(!is_null($rid) && !empty($subject) && !empty($message)) {
				$message = strip_tags($message);
				$subject = strip_tags($subject);
				$sent = ($save == true) ? 1 : 0;
				if(mysql_query("INSERT INTO `messages`(`rid`,`sid`,`msgSubject`,`date`,`msgBody`,`Sent`,`ip`)
				VALUES('$rid','$uid','$subject','$date','$message',$sent,'$ip')")) {
					$return = array("response"=>"ok");
					echo json_encode($return);
				} else {
					$this::jsonError("MESSAGE_TRY_AGAIN");
				}
			} else {
				if(empty($rname)) {
					$this::jsonError("MESSAGE_EMPTY_USER");
				} elseif(is_null($rid)) {
					$this::jsonError("MESSAGE_USER_NOT_EXIST");
				} elseif(empty($subject)) {
					$this::jsonError("MESSAGE_EMPTY_SUBJECT");
				} elseif(empty($message)) {
					$this::jsonError("MESSAGE_EMPTY_MESSAGE");
				} else {
					$this::jsonError("UNKNOWN_ERROR");
				}
			}
		}
		
		private function delMessage($id) {
			$uid = $this->userID;
			if(mysql_query("DELETE FROM `messages` WHERE `id`='$id' AND (`rid`='$uid' OR (`sid`='$uid' AND `sent`='0'))")) {
				$return = array("response"=>"ok");
				echo json_encode($return);
			} else {
				$this::jsonError("UNKNOWN_ERROR");
			}
		}
		
		private function jsonError($m) {
			$error = array("hasError"=>$this::lang($m,$this->config));
			echo json_encode($error);
		}
		
		private function getID() {
			$Config = new Config();
			$Config->buildUserInformation();
			$this->profileArray = $Config->outputUserInformation();
			return $this->profileArray[1];
		}
		
		private function idFromName($name) {
			$id_query = mysql_query("SELECT `id` FROM `users` WHERE `username`='$name'");
			if(mysql_num_rows($id_query) == 1) {
				$id_row = mysql_fetch_assoc($id_query);
				return $id_row['id'];
			} else {
				return NULL;
			}
		}
		
		private function nameFromId($id) {
			$name_query = mysql_query("SELECT `username` FROM `users` WHERE `id`='$id'");
			if(mysql_num_rows($name_query) == 1) {
				$name_row = mysql_fetch_assoc($name_query);
				return $name_row['username'];
			} else {
				return FALSE;
			}
		}
		
		public function __destruct() {}
	}
	
	new Messages();
 ?>