<?php
include_once('includes/classes/config.class.php');
include_once('includes/classes/videos.class.php');
include_once('includes/classes/pages.class.php');
include_once('includes/classes/stats.class.php');
$Config = new Config();
$Config->buildUserInformation();
$v = new AFTWVideos(); //Build our videos
$p = new AFTWpage();
$stats = new AFTWstats();
$v->connectProfile($Config->outputUserInformation());
$stats->connectProfile($Config->UserArray);
if(isset($_GET['type']) && $_GET['type'] == 'anime'){
	if(isset($_GET['seo'])){$seo = $_GET['seo'];}else{$seo = '';}
	if(isset($_GET['eid'])){$eid = $_GET['eid'];}else{$eid = '';}
	if(isset($_GET['oid'])){$oid = $_GET['oid'];}else{$oid = '';}
	if(isset($_GET['mid'])){$mid = $_GET['mid'];}else{$mid = '';}
	$PageTitle = $v->PageTitle($seo,$eid,$oid,$mid,$_GET['type']);
}
else {
	$PageTitle = 'Video Library - AnimeFTW.TV';
}
if($_SERVER['REQUEST_URI'] == '/videos/' || $_SERVER['REQUEST_URI'] == '/videos')
{
	header("location: /anime");
}
include('header.php');
include('header-nav.php');

if(isset($_GET['ref']))
{
	//$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('%s', NULL, '%s', '%s', '%s')";
	$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`)
	VALUES ('" . mysqli_real_escape_string($_SERVER['HTTP_REFERER']) . "', '" . mysqli_real_escape_string($_SERVER['REQUEST_URI']) . "', '" . mysqli_real_escape_string($_GET['ref']) . "', '" . time() . "', '" . mysqli_real_escape_string($_SERVER['REMOTE_ADDR']) . "')";
	mysqli_query($query) or die('Could not connect, way to go retard:' . mysqli_error());
}

echo psa($profileArray,1);
$index_global_message = NULL;
function bodyTopInfo($message,$bdybr,$profileArray,$Config){
	$bodyTop = "";
	// Start Main BG
   	$bodyTop .= "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	$bodyTop .= "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	$bodyTop .= '
	<div id="ad-wrapper" style="height:100%;position:absolute;z-index:0;">
		<div id="ad-sidebar" style="width:220px;float:left;margin:-10px 0 0 -245px;position:absolute;z-index:0;">';
	if($profileArray[2] == 0 || $profileArray[2] == 3){
	/*	$bodyTop .= "<div class='side-body-bg'>";
		$bodyTop .= "<div class='scapmain'>Advertisement</div>\n";
		$bodyTop .= "<div class='side-body floatfix'>\n";
		if($profileArray[2] == 3){
			$bodyTop .= '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
		}
		$bodyTop .= '<!-- Insticator API Embed Code -->
				<div id="insticator-container-0">
				<link rel="stylesheet" href="https://embed.insticator.com/embedstylesettings/getembedstyle?embedUUID=693d677f-f905-4a76-8223-3ed59a38842d">
					<div id="div-insticator-ad-1"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-1");</script></div>
				</div>
				<!-- End Insticator API Embed Code -->';
		$bodyTop .= "</div>\n";
		$bodyTop .= "</div>\n";*/
	}
	if(!isset($Config->SettingsArray[17])){
		/*$bodyTop .= "<div class='side-body-bg'>";
		$bodyTop .= "<div class='scapmain'>Game</div>\n";
		$bodyTop .= "<div class='side-body floatfix'>\n";
		$bodyTop .= '<!-- Insticator API Embed Code -->
				<div id="insticator-container-1">
				<div id="insticator-embed">';
				$bodyTop .= "
				<div id='insticator-api-iframe-div'><script>(function (d) {var id='693d677f-f905-4a76-8223-3ed59a38842d',cof = 1000 * 60 * 10,cbt = new Date(Math.floor(new Date().getTime() / cof) * cof).getTime(),js = 'https://embed.insticator.com/assets/javascripts/embed/insticator-api.js?cbt='+cbt, f = d.getElementById(\"insticator-api-iframe-div\").appendChild(d.createElement('iframe')),doc = f.contentWindow.document;f.setAttribute(\"id\",\"insticatorIframe\"); f.setAttribute(\"frameborder\",\"0\"); doc.open().write('<script>var insticator_embedUUID = \''+id+'\'; var insticatorAsync = true;<\/script><body onload=\"var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + js + '\'\" >');doc.close(); })(document);</script><noscript><a href=\"https://embed.insticator.com\">Please enable JavaScript.</a></noscript></div>";
				$bodyTop .= '
				</div>';
			$bodyTop .= '
			</div>
			<!-- End Insticator API Embed Code -->';
		$bodyTop .= "</div>\n";
		$bodyTop .= "</div>\n";*/
	}
	if($profileArray[2] == 0 || $profileArray[2] == 3){
	/*	$bodyTop .= "<div class='side-body-bg'>";
		$bodyTop .= "<div class='scapmain'>Advertisement</div>\n";
		$bodyTop .= "<div class='side-body floatfix'>\n";
		if($profileArray[2] == 3){
			$bodyTop .= '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
		}
		$bodyTop .= '<!-- Insticator API Embed Code -->
				<div id="insticator-container-2">
					<div id="div-insticator-ad-2"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-2");</script></div>
				</div>
				<!-- End Insticator API Embed Code -->';
		$bodyTop .= "</div>\n";
		$bodyTop .= "</div>\n";*/
	}
	$bodyTop .= '
	</div>
	</div>';
	// End Main BG
	if($message == NULL){}
	else {
    $bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	$bodyTop .= "<td class='note-message' align='center'>".$message."</td>\n";
	$bodyTop .= "</tr>\n</table>\n";
	$bodyTop .= "<br />\n<br />\n";
	}
	// Start Mid and Right Content
	$bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	$bodyTop .= "<td valign='top' class='main-mid'>\n";
	return $bodyTop;
}
		if($_GET['node'] == 'sort'){
			if($_GET['type'] == 'anime'){
				if(isset($_GET['param'])){
					echo bodyTopInfo($index_global_message,'yes',$profileArray,$Config);
					echo "<div class='side-body-bg'>\n";
					echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
					echo "<br />\n";
					echo "<span class='poster'>Sorting through Anime for the tag: <b>".$_GET['param']."</b></span>\n";
					echo "</div>\n";
                    echo $stats->donationBox(true);
					echo "<br />";
					echo "<div id=\"lister\">";
					echo '<br />'.$v->tagCloud('anime').'<br />';
					echo $v->showListing(0,$_GET['param'],$profileArray[2],0);
					echo "</div>";
				}
				else {
					if(isset($_GET['vtype'])){
						echo bodyTopInfo($index_global_message,'yes',$profileArray,$Config);
						echo "<div class='side-body-bg'>\n";
						echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
						echo "<br />\n";
						echo "<span class='poster'>Sorting through series for: <b>".$_GET['vtype']."</b></span>\n";
						echo "</div>\n";
                        echo $stats->donationBox(true);
						echo "<br />";
						echo "<div id=\"lister\">";
						echo '<br />'.$v->tagCloud('anime').'<br />';
						echo $v->showListing(0,$_GET['vtype'],$profileArray[2],1);
						echo "</div>";
					}
					else {}
				}

			}
			if($_GET['type'] == 'drama')
			{
			}
		}
		if($_GET['node'] == 'age'){
			if($_GET['type'] == 'anime'){
				if(isset($_GET['param'])){
					echo bodyTopInfo($index_global_message,'yes',$profileArray,$Config);
					echo "<div class='side-body-bg'>\n";
					echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
					echo "<br />\n";
					echo "<span class='poster'>Displaying all results that are in the: <b>".$_GET['param']."+</b> age Tag.</span>\n";
					echo "</div>\n";
                    echo $stats->donationBox(true);
					echo "<br />";
					echo "<div id=\"lister\">";
					echo '<br />'.$v->tagCloud('anime').'<br />';
					echo $v->showListing(0,$_GET['param'],$profileArray[2],2);
					echo "</div>";
				}
				else {}
			}
		}
		if($_GET['node'] == 'list')
		{
			if($_GET['type'] == 'anime')
			{
				echo bodyTopInfo($index_global_message,'yes',$profileArray,$Config);
				echo "<div class='side-body-bg'>\n";
				echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
				echo "<br />\n";
				echo "<span class='poster'>&nbsp;All of the Anime that AnimeFTW.tv supplies is listed below.<br>&nbsp;If you don't find what you're looking for, you can request anime <a href='/requests'>here</a>.</span>\n";
				echo "</div>\n";
                echo $stats->donationBox(true);
				echo "<br />";
				echo '<div align="center" ><a href="#" id="tagcloud-toggle">:: Toggle the Tag Cloud ::</a></div>';
				echo "<div id=\"tagcloud\" style=\"display:none\">";
				echo '<br />'.$v->tagCloud('anime').'<br />';
				echo "</div><br /><div id=\"lister\">";
				echo $v->showListing(0,NULL,$profileArray[2],0);
				echo "</div></div>";
			}
			if($_GET['type'] == 'drama')
			{
				echo bodyTopInfo($index_global_message,'yes',$profileArray,$Config);
				echo '<div class="left_articles_mod">
				<h2>AnimeFTW.tv\'s Drama Selection</h2>
				<p class="description">Drama Selection for AnimeFTW.tv</p>
				<div id="lister">';
				echo '<br />'.$v->tagCloud('drama').'<br />';
				echo $v->showListing(1,NULL,$profileArray[2],0);
				echo '</div></div>';
			}
			if($_GET['type'] == 'amv')
			{
				echo bodyTopInfo($index_global_message,'yes',$profileArray,$Config);
                echo $stats->donationBox(true);
				echo '<div class="left_articles_mod">
				<h2>AMV\'s Uploaded to AnimeFTW.tv\'s Servers</h2>
				<p class="description">AMVs listed Below</p>
				<div id="lister">';
				echo '<br />'.$v->tagCloud('amvs').'<br />';
				echo $v->showListing(2,NULL,$profileArray[2],0);
				echo '</div></div>';
			}
		}
		if($_GET['node'] == 'video'){
			if($_GET['type'] == 'anime'){
				echo $v->DisplaySeries($seo,$seo,$eid,$oid,$mid);
			}
		}
	//Body part..
	echo "</td>\n";
	echo "</tr>\n</table>\n";
	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
include('footer.php')
?>