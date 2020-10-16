<?php
	include('../includes/siteroot.php');
	include('../includes/settings.php');
	if(isset($_GET['activate'])) {
		$key = mysqli_real_escape_string($_GET['activate']);
		$query = "SELECT `id`,`firstName` FROM `users` WHERE `random_key`='$key' LIMIT 1";
		if($db->RecordCount($query) == 1) {
			$db->query("UPDATE `users` SET `active`='1' WHERE `random_key`='$key'");
			echo file_get_contents("../template/default/activated.tpl");
		} else {
			echo file_get_contents("../template/default/activatenotfound.tpl");
		}
		
	} elseif(isset($_GET['success'])) {
		echo file_get_contents("../template/default/msuccess.tpl");
	} elseif(isset($_GET['u']) && isset($_GET['p']) && isset($_GET['e']) && isset($_GET['f']) &&
			 isset($_GET['g']) && isset($_GET['b']) && isset($_GET['t']) &&
			 isset($_GET['a']) && isset($_GET['n']) && isset($_GET['h']) &&
			 isset($_GET['v']) ) {
		$u = $_GET['u']; $p = $_GET['p']; $e = $_GET['e']; $f = $_GET['f']; $g = $_GET['g'];
		$b = $_GET['b']; $t = $_GET['t']; $a = $_GET['a']; $n = $_GET['n'];
		$h = $_GET['h']; $v = $_GET['v'];

		$g = ($g == 1) ? "male" : "female";
		
		if(!checkUnique('Username',$u)) {
			sendMessage("User name is not available!");
		} elseif(!checkUnique('Email',$e)) {
			sendMessage("Email is not available!");
		} elseif($h != 2) {
			sendMessage("Please verify you are human!");
		} elseif(trim(strtolower($v)) != "five") {
			sendMessage("Incorrect humanity verification answer!");
		} else {
			if(!empty($b)) {
				$b = explode("/",$b);
				if($b[0] > 0 && $b[0] < 13 && $b[1] > 0 && $b[1] < 32 && $b[2] > 1900 && $b[2] < date("Y")) {
					$bm = $b[0]; $bd = $b[1]; $by = $b[2];
				} else {
					$bm = ""; $bd = "";	$by = "";
				}
			} else {
				$bm = ""; $bd = "";	$by = "";
			}
			
			$key = random_string('alnum',32);

			$headers = "From: no-reply@animeftw.tv\r\n".
    			"Content-type: text/html;charset=iso-8859-1\r\n".
			"X-Mailer: AnimeFTW.tv Mobile Systems";

			$message = "------------------------------------------------------------------------<br />".
				   "  This is an automated message! Please do not reply to it,<br />".
				   "  as it will cause your house to become haunted by the<br />".
				   "  Automated message spirits, who will enter your dreams<br />".
				   "  each night and remind you that your email was not recieved<br />".
				   "  by the FTW Entertainment, LLC staff!<br />".
				   "------------------------------------------------------------------------<br /><br /><br />".
				   "Hello $f!<br /><br />".
				   "Thank you for registering with AnimeFTW.tv! Below is your activation link<br />".
				   "to gain access to thousands of episodes of Anime, and access to our Android app!<br /><br />".
				   "<a href=\"http://www.animeftw.tv/m/register/activate/$key\">http://www.animeftw.tv/m/register/activate/$key</a><br /><br />".
				   "After activation, please visit <a href=\"http://www.animeftw.tv/rules\">our Rules</a> and <a href=\"http://www.animeftw.tv/faq\">our FAQ</a> to familiarize yourself with our site!<br />".
				   "Once you've familiarized yourself, feel free to take a look at our <a href=\"http://new.animeftw.tv/forums\">forums</a>, and get to know our fantastic community!<br />".
                   "Or check us out in Discord! <a href=\"https://discord.gg/JCm5b5E\">https://discord.gg/JCm5b5E</a> <br />".
				   "Happy viewings!<br />".
				   "- The AnimeFTW.tv staff and Community!";
			if(mail("$n <$e>","Your Registration with AnimeFTW.tv",$message,$headers)) {
				$query = $db->query("INSERT INTO users (`Username`,`display_name`,`Password`,`registrationDate`,`Email`,`Random_key`,`firstName`,
					`gender`,`ageDate`,`ageMonth`,`ageYear`,`staticip`,`timeZone`,`active`) VALUES (
					".$db->qstr(makeUrlFriendly("$u")).",".$db->qstr(makeUrlFriendly("$u")).",".$db->qstr(md5($p)).",'".time()."',
					".$db->qstr($e).",'".$key."','".$f."',
					'".$g."','". $bd."','".$bm."','".$by."',
					'".$_SERVER['REMOTE_ADDR']."','".$t."',0)" );
				sendMessage("ok");
			} else {
				sendMessage("Failed to send email");
			}
		}
		
	} else {
		echo file_get_contents("../template/default/mreg.tpl");
	}
	
	function sendMessage($out) {
		echo $out;
		exit;
	}
	
	function makeUrlFriendly($postUsername) {
		$output = @preg_replace("/\s/e" , "_" , $postUsername);
		$output = @preg_replace("/\W/e" , "" , $output);
		return strtolower($output);
	}
?>