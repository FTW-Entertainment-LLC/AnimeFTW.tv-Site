<?php
	if(isset($_SERVER['HTTP_CF_VISITOR'])){
        $decoded = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
        if($decoded['scheme'] == 'http'){
            // http requests
            $port = 80;
        } else {
            $port = 443;
        }
    } else {
        $port = $_SERVER['SERVER_PORT'];
    }
	if(strpos($_SERVER['REQUEST_URI'], 'store') && $port == '80')
	{
		header("location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		exit;
	}
include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
include_once('includes/classes/stats.class.php');
include_once('includes/classes/toplist.class.php');
if(strpos($_SERVER['REQUEST_URI'], 'store'))
{
	$PageTitle = 'Store - AnimeFTW.TV';
}
else
{
	$PageTitle = 'AnimeFTW.TV';
}
$stats = new AFTWstats();
$stats->connectProfile($Config->UserArray);
$top = new AFTWtoplist();
include('header.php');
include('header-nav.php');
if (isset($_GET['rf'])){
	$Referal = $_GET['rf'];
	$ReferalLink = @$_SERVER['HTTP_REFERER'];
	$ReferalIp = $_SERVER['REMOTE_ADDR'];
	if($Referal == 'zen'){
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysqli_real_escape_string($conn, $ReferalLink) . "', NULL, '" . mysqli_real_escape_string($conn, $Referal) . "', '" . mysqli_real_escape_string($conn, time()) . "', '" . mysqli_real_escape_string($conn, $ReferalIp) . "')";
		mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
	}
	else if($Referal == 'af'){
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysqli_real_escape_string($conn, $ReferalLink) . "', NULL, '" . mysqli_real_escape_string($conn, $Referal) . "', '" . mysqli_real_escape_string($conn, time()) . "', '" . mysqli_real_escape_string($conn, $ReferalIp) . "')";
		mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
	}
	else if($Referal == 'at'){
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysqli_real_escape_string($conn, $ReferalLink) . "', NULL, '" . mysqli_real_escape_string($conn, $Referal) . "', '" . mysqli_real_escape_string($conn, time()) . "', '" . mysqli_real_escape_string($conn, $ReferalIp) . "')";
		mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
	}
	else if($Referal == 'logo'){}
	else {
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysqli_real_escape_string($conn, $ReferalLink) . "', NULL, '" . mysqli_real_escape_string($conn, $Referal) . "', '" . mysqli_real_escape_string($conn, time()) . "', '" . mysqli_real_escape_string($conn, $ReferalIp) . "')";
		mysqli_query($conn, $query) or die('Could not connect, way to go retard:' . mysqli_error());
	}
}
//$index_global_message = "";
	// Start Main BG
	echo psa($profileArray);
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
/*	if(!isset($Config->SettingsArray[17])){
		echo '
		<div id="ad-wrapper" style="height:100%;position:absolute;z-index:0;">
			<div id="ad-sidebar" style="width:220px;float:left;margin:-10px 0 0 -245px;position:absolute;z-index:0;">';
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Game</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<!-- Insticator API Embed Code -->
				<div id="insticator-container">
				<link rel="stylesheet" href="https://embed.insticator.com/embedstylesettings/getembedstyle?embedUUID=693d677f-f905-4a76-8223-3ed59a38842d">
				<div id="insticator-embed">';
				echo "
				<div id='insticator-api-iframe-div'><script>(function (d) {var id='693d677f-f905-4a76-8223-3ed59a38842d',cof = 1000 * 60 * 10,cbt = new Date(Math.floor(new Date().getTime() / cof) * cof).getTime(),js = 'https://embed.insticator.com/assets/javascripts/embed/insticator-api.js?cbt='+cbt, f = d.getElementById(\"insticator-api-iframe-div\").appendChild(d.createElement('iframe')),doc = f.contentWindow.document;f.setAttribute(\"id\",\"insticatorIframe\"); f.setAttribute(\"frameborder\",\"0\"); doc.open().write('<script>var insticator_embedUUID = \''+id+'\'; var insticatorAsync = true;<\/script><body onload=\"var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + js + '\'\" >');doc.close(); })(document);</script><noscript><a href=\"https://embed.insticator.com\">Please enable JavaScript.</a></noscript></div>";
				echo '
				</div>';
			echo '
			</div>
			<!-- End Insticator API Embed Code -->';
		echo "</div>\n";
		echo "</div>\n";
	}*/
	if($profileArray[2] == 0 || $profileArray[2] == 3){
	/*	echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Advertisement</div>\n";
		echo "<div class='side-body floatfix'>\n";
		if($profileArray[2] == 3){
			echo '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
		}
		echo '<!-- Insticator API Embed Code -->
				<div id="insticator-container">
					<div id="div-insticator-ad-1"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-1");</script></div>
				</div>
				<!-- End Insticator API Embed Code -->';
		echo "</div>\n";
		echo "</div>\n";*/
	}
	echo '
	</div>
	</div>';
	// End Main BG
	if(isset($index_global_message)){
    echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td class='note-message' align='center'>".$index_global_message."</td>\n";
	echo "</tr>\n</table>\n";
	echo "<br />\n<br />\n";
	}
    function news($Config)
	{
		//12 for staff, 14 for staff and ams
		if($Config->UserArray[2] == 1 || $Config->UserArray[2] == 2 || $Config->UserArray[2] == 4 || $Config->UserArray[2] == 5 || $Config->UserArray[2] == 6)
		{
			$addonquery = ' OR t.tfid=12 OR t.tfid=14';
		}
		else if($Config->UserArray[2] == 7)
		{
			$addonquery = ' OR t.tfid=14';
		}
		else
		{
			$addonquery = '';
		}
        $query = "SELECT `tid`, `ttitle`, `tpid`, `tfid`, `tdate`, `fseo` FROM `forums_threads` INNER JOIN `forums_forum` ON `forums_forum`.`fid`=`forums_threads`.`tfid` WHERE `forums_threads`.`tfid` in (" . $showForumPosts . ") ORDER BY `tid` DESC";
		$query = "SELECT t.tid, t.ttitle, t.tpid, t.tfid, t.tdate, p.pbody, f.ftitle, f.fseo FROM forums_threads as t, forums_post as p, forums_forum as f
		WHERE (t.tfid='1' OR t.tfid='2' OR t.tfid='9'" . $addonquery . ") AND p.pistopic='1' AND p.puid=t.tpid AND p.ptid=t.tid AND f.fid=t.tfid ORDER BY t.tid DESC LIMIT 0, 8";
		if($Config->UserArray[0] == 1)
		{
			echo '<!-- Query:' . $query . ' -->';
		}
		$result = mysqli_query($conn, $query);
		while(list($tid, $ttitle, $tpid, $tfid, $tdate, $pbody, $ftitle, $fseo) = mysqli_fetch_array($result))
		{
			$pbody = stripslashes($pbody);

			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'><a href='/forums/".$fseo."/topic-".$tid."/'>".$ttitle."</a></span>\n";
			echo "<br />\n";
			echo "<span class='poster'>Posted on ".date("m.d.y",$tdate)." by ".$Config->formatUsername($tpid)."</span>\n";
			echo "</div>\n";
			echo "<div class='tbl'>".$pbody."</div>\n";
			echo "<br />\n";
		}
	}
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";

	if(!isset($_GET['node']))
	{
		echo news($Config);
	}
	else if($_GET['node'] == 'stats')
	{
        if (1 == 2) {
				$FinalDate3 = time()-86400;
				//how many active in last minute
				$query22 = mysqli_query($conn, "SELECT ID FROM users WHERE lastActivity>='".(time()-86400)."'");
				$online_users_24hours = mysqli_num_rows($query22) or die("Error: ". mysqli_error(). " with query ". $query22);
				//sidebar st00fs
					$query2 = mysqli_query($conn, "SELECT id FROM episode");
					$full_total_episodes = mysqli_num_rows($query2) or die("Error: ". mysqli_error(). " with query ". $query2);

					$query3 = mysqli_query($conn, "SELECT id FROM series WHERE active='yes'");
					$total_series = mysqli_num_rows($query3) or die("Error: ". mysqli_error(). " with query ". $query3);

					$query4 = mysqli_query($conn, "SELECT id FROM page_comments");
					$total_comments = mysqli_num_rows($query4) or die("Error: ". mysqli_error(). " with query ". $query4);
				//gender queries!
					//male
					$query5 = mysqli_query($conn, "SELECT ID FROM users WHERE gender='male'");
					$total_male = mysqli_num_rows($query5) or die("Error: ". mysqli_error(). " with query ". $query5);
					//female
					$query6 = mysqli_query($conn, "SELECT ID FROM users WHERE gender='female'");
					$total_female = mysqli_num_rows($query6) or die("Error: ". mysqli_error(). " with query ". $query6);
				//avatar queries!
					// how many active?
					$query7 = mysqli_query($conn, "SELECT ID FROM users WHERE avatarActivate='yes'");
					$total_avatars = mysqli_num_rows($query7) or die("Error: ". mysqli_error(). " with query ". $query7);
					// how many gif?
					$query8 = mysqli_query($conn, "SELECT ID FROM users WHERE avatarExtension='gif'");
					$total_avatars_gif = mysqli_num_rows($query8) or die("Error: ". mysqli_error(). " with query ". $query8);
					// how many jpgs?
					$query9 = mysqli_query($conn, "SELECT ID FROM users WHERE avatarExtension='jpg'");
					$total_avatars_jpg = mysqli_num_rows($query9) or die("Error: ". mysqli_error(). " with query ". $query9);
					// how many pngs?
					$query10 = mysqli_query($conn, "SELECT ID FROM users WHERE avatarExtension='png'");
					$total_avatars_png = mysqli_num_rows($query10) or die("Error: ". mysqli_error(). " with query ". $query10);
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Site Statistics</span>\n";
		echo "<br />\n";
		echo "<span class='poster'>Some Basic Statistics about AnimeFTW.tv and her Users.</span>\n";
		echo "</div>\n";
				echo '<div align="center">There have been '.$online_users_24hours.' registered users online in the past 24 hours!<br /><br /></div>
					<div style="width:100%">';
				$query19 = "SELECT ID, lastActivity FROM users WHERE lastActivity>='".$FinalDate3."' ORDER BY lastActivity DESC";
				$result19 = mysqli_query($conn, $query19);
				$ucount = mysqli_num_rows($result19);
				$i =0;
  				while(list($ID,$lastActivity) = mysqli_fetch_array($result19))
				{
					$lastActivity = $Config->timeZoneChange($lastActivity,$profileArray[3]);

					echo $Config->formatUsername($ID,'self',$lastActivity = NULL);
					if($i <= $ucount)
					{
						echo ', ';
					}
					$i++;

				}
				echo "<br /><br />";
					//who was online~query

				echo '</div>';
				echo 'Our users have posted '.$total_comments.' Comments to date<br />';
				echo '<br />We have '.$total_series.' series up for viewing!<br />';
				echo '<br />'.$full_total_episodes.' episodes are online and in our database and ready for watching<br />';
				echo '<br />'.$total_male.' of our users have updated themselves to reflect themselves as Male<br />';
				echo '<br />'.$total_female.' of our users have updated themselves to reflect themselves as Female<br />';
				echo '<br />';
				echo '<br />'.$total_avatars.' of our users have updated their avatars<br />';
				echo '<br /> - '.$total_avatars_gif.' of those avatars are gifs<br />';
				echo '<br /> - '.$total_avatars_jpg.' of those avatars are jpgs<br />';
				echo '<br /> - '.$total_avatars_png.' of those avatars are png<br />';
				echo '</div>';
				echo '
					<h2>Latest 5 Comments</h2>
						coming soon!
						<div class="date"></div>
       		<h2>10 Newest Members!</h2>';
							$query = "SELECT Username FROM users WHERE active='1' ORDER BY id DESC LIMIT 0, 10";
							$result = mysqli_query($conn, $query);
							$self = $_SERVER['PHP_SELF'];

						while(list($Username) = mysqli_fetch_array($result, MYSQL_NUM))
						{
							echo '<a href="//'.$siteroot.'/user/'.$Username.'">'.$Username.'</a>, ';
						}

						?>
        	<div class="date"></div>
       		<h2>Latest 15 Episodes</h2>
            <br />
<?php
			if($profileArray[2] != 0 && $profileArray[2] !=3)
			{
			$query = "SELECT episode.id, episode.seriesname, episode.epname, episode.epnumber FROM episode, series WHERE episode.seriesname=series.seriesName AND series.active = 'yes' ORDER BY episode.id DESC LIMIT 0, 15";
			$result = mysqli_query($conn, $query);
			$self = $_SERVER['PHP_SELF'];

			while(list($id, $seriesname, $epname, $epnumber) = mysqli_fetch_array($result, MYSQL_NUM))
			{
			$query2 = "SELECT seriesName, fullSeriesName, seoname FROM series WHERE seriesName='".$seriesname."'";
			$result2 = mysqli_query($conn, $query2);
			list($seriesName2, $fullSeriesName, $seoname) = mysqli_fetch_array($result2, MYSQL_NUM);
			$fullSeriesName = stripslashes($fullSeriesName);
			$epname = stripslashes($epname);
			echo '<div> - Episode #' . $epnumber . ' added to series <a href="//'.$siteroot.'/anime/'.$seoname.'/">' . $fullSeriesName . '</a> titled: <span style="font-weight:bold;">' . $epname . '</span></div>
						<br />';
			}
		   echo' </div>';
		}
		else {
			$query = "SELECT id, seriesname, epname, epnumber FROM episode ORDER BY date DESC LIMIT 0, 15";
			$result = mysqli_query($conn, $query);
			$self = $_SERVER['PHP_SELF'];

			while(list($id, $seriesname, $epname, $epnumber) = mysqli_fetch_array($result, MYSQL_NUM))
			{
			$query2 = "SELECT seriesName, fullSeriesName, seoname FROM series WHERE seriesName='".$seriesname."'";
			$result2 = mysqli_query($conn, $query2);
			list($seriesName2, $fullSeriesName, $seoname) = mysqli_fetch_array($result2, MYSQL_NUM);
			$fullSeriesName = stripslashes($fullSeriesName);
			$epname = stripslashes($epname);
			echo '<div> - Episode #' . $epnumber . ' added to series <a href="//'.$siteroot.'/videos/'.$seoname.'/">' . $fullSeriesName . '</a> titled: <span style="font-weight:bold;">' . $epname . '</span></div>
						<br />';
			}
		   echo' </div>';
		}
    } else {
        echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
    }
	}
	else if($_GET['node'] == 'topseries')
	{
        if (1 == 2) {
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Series and Movies Toplist</span>\n";
		echo "</div>\n";
		  echo '<div class="mpart">';
			echo "<br /><div align=\"center\">Welcome to the Top Series Listing for AnimeFTW.tv, this list is a list of all the Series, OVA and Movies that are on the site.  The following script takes in data for each 24 hour period, then at midnight server time, it calculates the percentage of Watched episodes per series and divides it by the available max amount of episodes in the series. Giving it a flawless percentage which it updates the toplist to show you what everyone is watching!</div><br />";
		   echo '<div class="ExtraLeft"><br />';
		   $mtl = new AFTWtoplist();
		   $mtl->get_num(700);
		   $mtl->TopAnime();
	   		echo '</div>';
			echo '</div>';
        } else {
            echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
        }
	 }
	   else if($_GET['node'] == 'banned')
	   {
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>Banned users from AnimeFTW.tv</span>\n";
		echo "</div>\n";
		  echo '<div class="mpart">
			<p>When we notice that people are looking for malicious scripts we ban them, we wont take lightly to our site being contested.. so here are all the ip\'s with a reason to their banning, if you find this page and would like to be unbanned, please contact the admins</p>';

					$query1 = "SELECT ip, seenReason FROM banned
							ORDER BY id
							DESC";
				$result1 = mysqli_query($conn, $query1);
  				while(list($ip,$seenReason) = mysqli_fetch_array($result1))
				{
					echo "----------------------<br />";
					echo "<div>$ip - BANNED - Reason: $seenReason</div>";
				}
				echo '
                    <div class="date"></div>
                    </div>';
	   }
	   else if($_GET['node'] == 'contact')
	   {
           if (1 == 2) {
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Contact Form</span>\n";
		echo "</div>\n";
		if($_POST['email-submit'])
		{
			echo'<p>Comment/Contact Submitted.</p>';

			// EDIT THE 2 LINES BELOW AS REQUIRED
			$email_to = "support@animeftw.tv";
			$email_subject = "AnimeFTW.tv Contact Form";

			function died($error) {
				// your error code can go here
				echo "We are very sorry, but there were error(s) found with the form you submitted. ";
				echo "These errors appear below.<br /><br />";
				echo $error."<br /><br />";
				echo "Please go back and fix these errors.<br /><br />";
				die();
			}

			// validation expected data exists
			if(!isset($_POST['email']) ||
				!isset($_POST['sites'])) {
				died('We are sorry, but there appears to be a problem with the form you submitted.');
			}

			$name = $_POST['name']; // required
			$email_from = $_POST['email']; // required
			$comments = $_POST['sites']; // required
			$ip = $_POST['ip']; // required

			$error_message = "";
			$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
			if(strlen($comments) < 2) {
				$error_message .= 'The Comments you entered do not appear to be valid.<br />';
			}
			if(strlen($error_message) > 0) {
				died($error_message);
			}
			$email_message = "Form details below.\n\n";

			function clean_string($string) {
			  $bad = array("content-type","bcc:","to:","cc:","href");
			  return str_replace($bad,"",$string);
			}

			$email_message .= "Name: ".clean_string($name)."\n";
			$email_message .= "Email: ".clean_string($email_from)."\n";
			$email_message .= "Comments: ".clean_string($comments)."\n";
			$email_message .= "IP: ".clean_string($ip)."\n";


			// create email headers
			$headers = 'From: '.$email_from."\r\n".
			'Reply-To: '.$email_from."\r\n" .
			'X-Mailer: PHP/' . phpversion();
			@mail($email_to, $email_subject, $email_message, $headers);

		}
		if($_SERVER['HTTP_REFERER'] == 'http://'.$siteroot.'/contact-us')
		{
		}
		else {
                    echo'<p> Here you can send us a message, if you have a problem/concern/comment please send us, please note we will ban you from use if you abuse it.</p>';
					if($_SERVER['REMOTE_ADDR'] == '68.199.136.64'){
						echo 'I see you there, stop being a noob, your permissions for this form is revoked.';
					}
					else {
						echo '<form method="POST" action="">
							<input type="hidden" name="name" value="'.$profileArray[5].'" />
							<input type="hidden" name="ip" value="'.$_SERVER['REMOTE_ADDR'].'" />
							<p>If you wish for us to get back to you faster please provide an e-mail.</p>
							<p>Email: <input type="TEXT" name="email"><br /></p>
							<p>Comment: <textarea name="sites" cols="30" rows="10"></textarea></p>
							<div align="center"><input type="SUBMIT" name="email-submit" value="Send" /></div>
						</form>';
					}
		}
    } else {
        echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
    }
	}
	else if($_GET['node'] == 'birthdays'){
        if (1 == 2){
    		echo "<div class='side-body-bg'>\n";
    		echo "<span class='scapmain'>AnimeFTW.tv Birthdays!</span>\n";
    		echo "</div>\n";
    		echo "<div class='side-body' align=\"center\">\n";
    		echo "誕生日おめでとう, gelukkige Verjaardag, Joyeux anniversaire, feliz cumpleaños, <b><i>Happy Birthday</i></b>.<br />From all the Staff at AnimeFTW.tv, We wish you a Happy Birthday, however you say it!";
    		echo "</div>";
    		$stats->TodaysBirthdays();
        } else {
            echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
        }
	}
	else if($_GET['node'] == 'staff'){
		if($profileArray[2] != 0){
			include('includes/classes/applications.class.php');
			$A = new Applications($profileArray);
			$A->Output();
		}
		else {
			echo 'ERROR: Please login to do any staff functions.';
		}
	}
	else if($_GET['node'] == 'store'){
        if (1 == 2) {
    		include('includes/classes/store.class.php');
    		$S = new Store();
    		$S->connectProfile($profileArray);
    		$S->StoreInit();
        } else {
            echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
        }
	}
	else if($_GET['node'] == 'connect'){
        if (1 == 2){
    		echo "<div class='side-body-bg'>\n";
    		echo "<span class='scapmain'>Connect your Device to your AnimeFTW.tv Account!</span>\n";
    		echo "<div class='side-body' align=\"center\">\n";
            echo '<div align="center" style="font-size:16px;">Connect your device to your AnimeFTW.tv account, use the code from the Channel/App to pair the device to your account.</div>';
            if($profileArray[2] != 0){
                echo '<br />
                <div align="center" id="key-wrapper">
                    <form id="key-form">
                        <div id="key-div-wrapper">
                            <div>
                                <input type="input" name="key" id="app-key" style="height:60px;width:260px;font-size:52px;text-align:center;border:1px solid #CCC;border-radius:5px;" maxlength="6" onkeyup="countChar(this)" />
                            </div>
                            <div>
                                (The Key is case sensitive)
                            </div>
                        </div>
                        <div id="hidden-success" style="display:none;min-height:60px;font-size:26px;" align="center">
                            Your Device has been successfully registered with your account, proceed back to your device to continue with the Device Authentication process.
                        </div>
                        <div style="padding-top:20px;">
                            <input type="button" name="Submit" value="Submit" id="key-submit-button" />
                        </div>
                        <div>
                            Once submitted, your app/channel instance will be able to pull down a token from the servers, if you have issues authenticating your app, please hop in the <a href="/irc" target="_blank">chat</a> or email support@animeftw.tv.
                        </div>
                        <div id="key-failure-notice" style="display:none;">
                        </div>
                    </form>
                </div>
                <script>
                function countChar(val) {
                    var len = val.value.length;
                    if (len == 6) {
                        $("#app-key").css("border","1px solid green");
                    } else {
                        $("#app-key").css("border","1px solid #CCC");
                    }
                };
                $("#key-submit-button").on("click",function(){
                    var len = $("#app-key").val().length;
                    if (len < 6) {
                        $("#app-key").css("border","1px solid red");
                        return false;
                    } else {
                        $("#key-failure-notice").hide();
                        $.ajax({
    						type: "POST",
    						url: "/scripts.php?view=api&subview=validate-key",
    						data: $("#key-form").serialize(),
    						success: function(html)
    						{
    							if(html.indexOf("Success") >= 0)
    							{
                                    $("#key-div-wrapper").hide();
                                    $("#hidden-success").show();
    							}
    							else{
    								$("#key-failure-notice").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Error Submitting Request: " + html + "</div>");
    							}
    						}
    					});
    					return false;
                    }
                });
                </script>';
            }
            echo "</div>";
    		echo "</div>\n";
        } else {
            echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
        }
    }
	else {
        if (1 ==2) {
    		include('includes/classes/content.class.php');
    		$C = new Content();
    		$C->connectProfile($profileArray);
    		$C->Output();
        } else {
            echo '<div align="center" style="font-size:26px;">Thank you for your interest in the site,<br> sadly AnimeFTW.tv has closed down. <br>Please see <a href="https://www.animeftw.tv/forums/global-announcements/topic-5079/s-0">this topic</a> for the full details.</div>';
        }
	}
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	if($profileArray[2] == 0 || $profileArray[2] == 3){
		/*echo '
		<div id="ad-wrapper" style="height:100%;position:absolute;z-index:0;">
			<div id="ad-sidebar" style="width:220px;float:right;margin:0 0 0 270px;position:absolute;z-index:0;">';
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Advertisement</div>\n";
		echo "<div class='side-body floatfix'>\n";
		if($profileArray[2] == 3){
			echo '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
		}
		echo '<!-- Insticator API Embed Code -->
				<div id="insticator-container">
				<div id="div-insticator-ad-2"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-2");</script></div>
			</div>
			<!-- End Insticator API Embed Code -->';
		echo "</div>\n";
		echo "</div>\n";
		echo '
		</div>
		</div>';*/
	}
	$stats->get_la($profileArray[2]);
	if(strpos($_SERVER['REQUEST_URI'], 'store'))
	{
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>AFTW Store Options</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<div align="center">';
		if($profileArray[0] == 1)
		{
			echo '<a href="/scripts.php?view=cart&KeepThis=true&TB_iframe=true&height=400&width=780" title="Your Current Basket" class="thickbox"><img src="//animeftw.tv/images/storeimages/shopping_basket.png" alt="" title="View your current Basket" /></a>';
			echo '<a href="/store/account"><img src="//animeftw.tv/images/storeimages/history.png" alt="" title="View past orders" /></a>';
			if($profileArray[2] == 1)
			{
				echo '<a href="/store/admin"><img src="//animeftw.tv/images/storeimages/workflow.png" alt="" title="Manage the Store" /></a>';
			}
		}
		else
		{
			echo '<a href="/login">Please Login to add view your Cart</a>';
		}
		echo "</div></div></div>\n";
		$stats->ShowStoreCategories();
	}
	//$stats->donationBox();
	if($profileArray[2] != 0){
		echo "<!-- Start Top 10 List -->";
		$top->get_num(10);
		$top->StyleTop();
		$top->TopAnime();
		echo '<div align="right"><a href="/top-series">See the rest of the Top List &gt;&gt;</a></div>';
		$top->StyleBottom();
		echo "<!-- End Top 10 List -->";
	}
	//$stats->UsageStats();
	//$stats->TopWatchList();
	//$stats->BirthdayBox();
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>View us on..</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo '<div align="center">
        <div style="display:inline;">
            <a href="http://www.twitter.com/animeftwtv" target="_blank"><img src="' . ($port == '80' ? "http" : "https") . '://twitter-badges.s3.amazonaws.com/twitter-a.png" alt="Follow animeftwdottv on Twitter" border="0"/></a>
        </div>
        <div style="display:inline;">
            <div class="fb-like" data-href="https://www.facebook.com/AnimeFTW.tv" data-layout="button_count" data-action="like" data-size="large" data-show-faces="true" data-share="true">/div>
        </div>
    </div>';
    if($profileArray[8] == 1) {
        echo "<br /><a href='https://www.microsoft.com/en-us/store/apps/animeftwtv/9nblggh5k288' target='_blank'><img src='" . $Config->Host . "/themes/christmas/windows-phone-logo.png' alt='' width='225px' /></a><br />";
        echo "<br /><a href='//www.animeftw.tv/forums/xbmc-plugin-support/topic-4730/s-0' target='_blank'><img src='" . $Config->Host . "/themes/christmas/kodi-image.png' alt='Kodi Logo' border='0' width='225px' /></a><br />";
        echo "<br /><a href='//www.animeftw.tv/forums/roku-channel-support/' tager='_blank'><img src='" . $Config->Host . "/themes/default/roku-logo.png' alt='Roku TV logo' border='0' /></a><br />";
    } else {
        echo "<br /><a href='https://www.microsoft.com/en-us/store/apps/animeftwtv/9nblggh5k288' target='_blank'><img src='" . $Config->Host . "/themes/default/windows-phone-logo.png' alt='' width='225px' /></a><br />";
        echo "<br /><a href='//www.animeftw.tv/forums/xbmc-plugin-support/topic-4730/s-0' target='_blank'><img src='" . $Config->Host . "/themes/default/kodi-image.png' alt='Kodi Logo' border='0' width='225px' /></a><br />";
        echo "<br /><a href='//www.animeftw.tv/forums/roku-channel-support/' tager='_blank'><img src='" . $Config->Host . "/themes/default/roku-logo.png' alt='Roku TV logo' border='0' /></a><br />";
    }
	echo "<a href=\"http://www.animeftw.tv/download/AnimeFTW.tv.apk\"><img src=\"//animeftw.tv/images/android_logo.jpg\" alt=\"\" width=\"225px\" /></a><br />";
	echo "</div></div>\n";
	$stats->get_zone($profileArray[3]);
//	$stats->LatestSeries();
	//$stats->LatestEpisodes();
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG

include('footer.php');
?>
