<?php

	if(strpos($_SERVER['REQUEST_URI'], 'store') && $_SERVER['SERVER_PORT'] == '80')
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
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysql_real_escape_string($ReferalLink) . "', NULL, '" . mysql_real_escape_string($Referal) . "', '" . mysql_real_escape_string(time()) . "', '" . mysql_real_escape_string($ReferalIp) . "')";
		mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
	}
	else if($Referal == 'af'){
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysql_real_escape_string($ReferalLink) . "', NULL, '" . mysql_real_escape_string($Referal) . "', '" . mysql_real_escape_string(time()) . "', '" . mysql_real_escape_string($ReferalIp) . "')";
		mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
	}
	else if($Referal == 'at'){
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysql_real_escape_string($ReferalLink) . "', NULL, '" . mysql_real_escape_string($Referal) . "', '" . mysql_real_escape_string(time()) . "', '" . mysql_real_escape_string($ReferalIp) . "')";
		mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
	}
	else if($Referal == 'logo'){}
	else {
		$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('" . mysql_real_escape_string($ReferalLink) . "', NULL, '" . mysql_real_escape_string($Referal) . "', '" . mysql_real_escape_string(time()) . "', '" . mysql_real_escape_string($ReferalIp) . "')";
		mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
	}
}
//$index_global_message = "";
	// Start Main BG
	echo psa($profileArray);
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
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
	if($profileArray[2] == 0 || $profileArray[2] == 3){
		echo "<div class='side-body-bg'>";
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
		echo "</div>\n";
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
		if($profileArray[2] == 1 || $profileArray[2] == 2 || $profileArray[2] == 4 || $profileArray[2] == 5 || $profileArray[2] == 6)
		{
			$addonquery = ' OR t.tfid=12 OR t.tfid=14';
		}
		else if($profileArray[2] == 7)
		{
			$addonquery = ' OR t.tfid=14';
		}
		else
		{
			$addonquery = '';
		}
		$query = "SELECT t.tid, t.ttitle, t.tpid, t.tfid, t.tdate, p.pbody, f.ftitle, f.fseo FROM forums_threads as t, forums_post as p, forums_forum as f 
		WHERE (t.tfid='1' OR t.tfid='2' OR t.tfid='9'" . $addonquery . ") AND p.pistopic='1' AND p.puid=t.tpid AND p.ptid=t.tid AND f.fid=t.tfid ORDER BY t.tid DESC LIMIT 0, 8";
		if($profileArray[0] == 1)
		{
			echo '<!-- Query:' . $query . ' -->';
		}
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		while(list($tid, $ttitle, $tpid, $tfid, $tdate, $pbody, $ftitle, $fseo) = mysql_fetch_array($result)) 
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
				$FinalDate3 = time()-86400;
				//how many active in last minute
				$query22 = mysql_query("SELECT ID FROM users WHERE lastActivity>='".(time()-86400)."'"); 
				$online_users_24hours = mysql_num_rows($query22) or die("Error: ". mysql_error(). " with query ". $query22);
				//sidebar st00fs					
					$query2 = mysql_query("SELECT id FROM episode"); 
					$full_total_episodes = mysql_num_rows($query2) or die("Error: ". mysql_error(). " with query ". $query2);
					
					$query3 = mysql_query("SELECT id FROM series WHERE active='yes'"); 
					$total_series = mysql_num_rows($query3) or die("Error: ". mysql_error(). " with query ". $query3);
					
					$query4 = mysql_query("SELECT id FROM page_comments"); 
					$total_comments = mysql_num_rows($query4) or die("Error: ". mysql_error(). " with query ". $query4);
				//gender queries!
					//male
					$query5 = mysql_query("SELECT ID FROM users WHERE gender='male'"); 
					$total_male = mysql_num_rows($query5) or die("Error: ". mysql_error(). " with query ". $query5);
					//female
					$query6 = mysql_query("SELECT ID FROM users WHERE gender='female'"); 
					$total_female = mysql_num_rows($query6) or die("Error: ". mysql_error(). " with query ". $query6);
				//avatar queries!
					// how many active?
					$query7 = mysql_query("SELECT ID FROM users WHERE avatarActivate='yes'"); 
					$total_avatars = mysql_num_rows($query7) or die("Error: ". mysql_error(). " with query ". $query7);
					// how many gif?
					$query8 = mysql_query("SELECT ID FROM users WHERE avatarExtension='gif'"); 
					$total_avatars_gif = mysql_num_rows($query8) or die("Error: ". mysql_error(). " with query ". $query8);
					// how many jpgs?
					$query9 = mysql_query("SELECT ID FROM users WHERE avatarExtension='jpg'"); 
					$total_avatars_jpg = mysql_num_rows($query9) or die("Error: ". mysql_error(). " with query ". $query9);
					// how many pngs?
					$query10 = mysql_query("SELECT ID FROM users WHERE avatarExtension='png'"); 
					$total_avatars_png = mysql_num_rows($query10) or die("Error: ". mysql_error(). " with query ". $query10);
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Site Statistics</span>\n";
		echo "<br />\n";
		echo "<span class='poster'>Some Basic Statistics about AnimeFTW.tv and her Users.</span>\n";
		echo "</div>\n";
				echo '<div align="center">There have been '.$online_users_24hours.' registered users online in the past 24 hours!<br /><br /></div>
					<div style="width:100%">';
				$query19 = "SELECT ID, lastActivity FROM users WHERE lastActivity>='".$FinalDate3."' ORDER BY lastActivity DESC";
				$result19 = mysql_query($query19) or die('Error : ' . mysql_error());
				$ucount = mysql_num_rows($result19);
				$i =0;
  				while(list($ID,$lastActivity) = mysql_fetch_array($result19))
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
							$result = mysql_query($query) or die('Error : ' . mysql_error());
							$self = $_SERVER['PHP_SELF'];

						while(list($Username) = mysql_fetch_array($result, MYSQL_NUM))
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
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$self = $_SERVER['PHP_SELF'];
	
			while(list($id, $seriesname, $epname, $epnumber) = mysql_fetch_array($result, MYSQL_NUM))
			{
			$query2 = "SELECT seriesName, fullSeriesName, seoname FROM series WHERE seriesName='".$seriesname."'";
			$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
			list($seriesName2, $fullSeriesName, $seoname) = mysql_fetch_array($result2, MYSQL_NUM);
			$fullSeriesName = stripslashes($fullSeriesName);
			$epname = stripslashes($epname);
			echo '<div> - Episode #' . $epnumber . ' added to series <a href="//'.$siteroot.'/anime/'.$seoname.'/">' . $fullSeriesName . '</a> titled: <span style="font-weight:bold;">' . $epname . '</span></div>
						<br />';
			}
		   echo' </div>';
		}
		else {
			$query = "SELECT id, seriesname, epname, epnumber FROM episode ORDER BY date DESC LIMIT 0, 15";
			$result = mysql_query($query) or die('Error : ' . mysql_error());
			$self = $_SERVER['PHP_SELF'];
	
			while(list($id, $seriesname, $epname, $epnumber) = mysql_fetch_array($result, MYSQL_NUM))
			{
			$query2 = "SELECT seriesName, fullSeriesName, seoname FROM series WHERE seriesName='".$seriesname."'";
			$result2 = mysql_query($query2) or die('Error : ' . mysql_error());
			list($seriesName2, $fullSeriesName, $seoname) = mysql_fetch_array($result2, MYSQL_NUM);
			$fullSeriesName = stripslashes($fullSeriesName);
			$epname = stripslashes($epname);
			echo '<div> - Episode #' . $epnumber . ' added to series <a href="//'.$siteroot.'/videos/'.$seoname.'/">' . $fullSeriesName . '</a> titled: <span style="font-weight:bold;">' . $epname . '</span></div>
						<br />';
			}
		   echo' </div>';
		}
	}
	else if($_GET['node'] == 'topseries')
	{
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
	 }
	 else if($_GET['node'] == 'chat')
	 {
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Chat</span>\n";
		echo "</div>\n";
		   ?>
           <div class="mpart">
           <div align="center">You are being connected to irc.ftwirc.com, FTW Entertainment's personal IRC server.<br />To change your nick type /nick (nickhere)</div>
                    <?php
					
				if ( $Logged == 1 )
							{
								echo '<div align="center"><iframe align="center" src="http://widget.mibbit.com/?settings=ea6a5f6fddfdb032b083170d36ac4d3c&server=irc.ftwirc.com&channel=%23ftw&noServerNotices=true&noServerMotd=true&autoConnect=true&nick='.$name.'" height="450px" width="750px" frameborder="0"></iframe></div>';
							}
							else 
							{
							
				echo '<div align="center"><iframe align="center" src="http://widget.mibbit.com/?settings=ea6a5f6fddfdb032b083170d36ac4d3c&server=irc.ftwirc.com&channel=%23ftw&noServerNotices=true&noServerMotd=true&autoConnect=true" height="350px" width="650px" frameborder="0"></iframe></div>';
							}
				?>
				<div class="date"></div>
				</div>
           <?php
	   }
	   else if($_GET['node'] == 'donate')
	   {
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Donations</span>\n";
		echo "</div>\n";
		   ?><div class="mpart">
           <p>Animeftw and FTW Entertainment Strive to bring you on-demand streaming anime for free, as part of our promise to deliver the highest quality streaming anime on the web, we look to new frontiers to help deleiver the highest quality and the fastest streaming on the net.<br /><br />
                    If you are a member or you are just a community member that has not signed up for our members status, and would like to donate to help offset bandwidtha nd server costs, please click the donate link below.<br /><br />
                   </p>
                   <br />
                   <h2>NOTICE!!!! Donating DOES <i>NOT</i> give you advanced member status! You can subscribe to our site by going <a href="/advanced-signup">HERE</a> and doing that will get you Advanced Status, but NOT donating, we appreciate all donations but this needed clarification! </h2>
                    <table width="98%">
                    	<tr>
                        	<td align="center" width="122px"><span class="minortext" style="font-weight:bold;">Click to Donate</span></td>
                            <td align="center"><span class="minortext" style="font-weight:bold;">This Month's Donors</span></td>
                            <td align="center"><span class="minortext" style="font-weight:bold;">Donors To date</span></td>
                        </tr>
                        <tr>
                        	<td align="center" height="100px">
                            	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal" name="paypal">
      <input type="hidden" name="cmd" value="_donations" />
      <input type="hidden" name="business" value="donate@animeftw.tv" />
      <?php
	  if($Logged == 0)
	  {
		  echo '
      <input type="hidden" name="item_name" value="Animeftw Non-Member Donation" />
      <input type="hidden" name="item_number" value="120001" />
      <input type="hidden" name="page_style" value="PayPal" />
	  ';
	  }
	  else {
		  echo '
      <input type="hidden" name="item_name" value="Animeftw Member Donation" />
      <input type="hidden" name="item_number" value="120002" />
      <input type="hidden" name="page_style" value="PayPal" />
      <input type="hidden" name="cn" value="'.$name.'" />
	  ';
	  }
	  ?>
      <input type="hidden" name="page_style" value="PayPal" />
      <input type="hidden" name="no_shipping" value="1" />
      <input type="hidden" name="return" value="http://<?=$siteroot;?>/donation-accepted" />
      <input type="hidden" name="cancel_return" value="http://<?=$siteroot;?>/donation-cancelled" />
      <input type="hidden" name="currency_code" value="USD" />
      <input type="hidden" name="tax" value="0" />
      <input type="hidden" name="lc" value="US" />
      <input type="hidden" name="bn" value="PP-DonationsBF" />
      <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
      <img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
      </form>
                            </td>
                            <td align="center" height="100px">
                           
        <?php 
		//include('donations/index.php');
		?>
                           </td>
                            <td align="center" height="100px"> <?php
													
							 $query   = "SELECT mc_gross, first_name, reg_date
											FROM donation_paypal 
											ORDER BY reg_date";
								$result  = mysql_query($query) or die('Error : ' . mysql_error());
								
							  while(list($mc_gross, $first_name, $reg_date) = mysql_fetch_array($result))
							{
									echo "<div align='left'>$first_name - $mc_gross - $reg_date</div>";
							}
							?></td>
                        </tr>
                    </table>
                    <div class="date"></div>
                    </div>
					<?php
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
				$result1 = mysql_query($query1) or die('Error : ' . mysql_error());
  				while(list($ip,$seenReason) = mysql_fetch_array($result1))
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
	}
	else if($_GET['node'] == 'birthdays'){
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Birthdays!</span>\n";
		echo "</div>\n";
		echo "<div class='side-body' align=\"center\">\n";
		echo "誕生日おめでとう, gelukkige Verjaardag, Joyeux anniversaire, feliz cumpleaños, <b><i>Happy Birthday</i></b>.<br />From all the Staff at AnimeFTW.tv, We wish you a Happy Birthday, however you say it!";
		echo "</div>";
		$stats->TodaysBirthdays();
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
		include('includes/classes/store.class.php');
		$S = new Store();
		$S->connectProfile($profileArray);
		$S->StoreInit();
	}
	else {
		include('includes/classes/content.class.php');
		$C = new Content();
		$C->connectProfile($profileArray);
		$C->Output();
	}
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	if($profileArray[2] == 0 || $profileArray[2] == 3){
		echo '
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
		</div>';
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
			echo '<a href="/scripts.php?view=cart&KeepThis=true&TB_iframe=true&height=400&width=780" title="Your Current Basket" class="thickbox"><img src="/images/storeimages/shopping_basket.png" alt="" title="View your current Basket" /></a>';
			echo '<a href="/store/account"><img src="/images/storeimages/history.png" alt="" title="View past orders" /></a>';
			if($profileArray[2] == 1)
			{
				echo '<a href="/store/admin"><img src="/images/storeimages/workflow.png" alt="" title="Manage the Store" /></a>';
			}
		}
		else
		{
			echo '<a href="/login">Please Login to add view your Cart</a>';
		}
		echo "</div></div></div>\n";
		$stats->ShowStoreCategories();
	}
	if($profileArray[2] != 0){
		echo "<!-- Start Top 10 List -->";
		$top->get_num(10);
		$top->StyleTop();
		$top->TopAnime();
		echo '<div align="right"><a href="/top-series">See the rest of the Top List &gt;&gt;</a></div>';
		$top->StyleBottom();
		echo "<!-- End Top 10 List -->";
	}
	$stats->UsageStats();
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>Friends of AFTW</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo '<div align="center">';
	echo '<a href="http://www.cybernations.net"><img src="/images/cn-gif.gif" alt="Cybernations - A Nation Simulation Game" border="0" /></a>&nbsp;<a href="http://animetoplist.org/">' . ($_SERVER['SERVER_PORT'] == '80' ? '<img src="http://animetoplist.org/button.php?id=317" alt="Read Manga online" />' : "AnimeTopList") . '</a><br />';
	echo "</div></div></div>\n";
	$stats->TopWatchList();
	$stats->BirthdayBox();
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>View us on..</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo '<div align="center"><a href="http://www.twitter.com/animeftwtv" target="_blank"><img src="' . ($_SERVER['SERVER_PORT'] == '80' ? "http" : "https") . '://twitter-badges.s3.amazonaws.com/twitter-a.png" alt="Follow animeftwdottv on Twitter" border="0"/></a>&nbsp;&nbsp;<iframe src="' . ($_SERVER['SERVER_PORT'] == '80' ? "http" : "https") . '://www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2FAnimeFTW.tv&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></div>';
	echo "<a href='http://kodi.tv/' target='_blank'><img src='/images/xbmc-logo.png' alt='XBMC Logo' border='0' /></a><br />";
	echo "<a href=\"http://www.animeftw.tv/download/AnimeFTW.tv.apk\"><img src=\"/images/android_logo.jpg\" alt=\"\" width=\"225px\" /></a>";
	echo "</div></div>\n";
	$stats->get_zone($profileArray[3]);
	$stats->LatestSeries();
	$stats->LatestEpisodes();
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
		
include('footer.php');
?>