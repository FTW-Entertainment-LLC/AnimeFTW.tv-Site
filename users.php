<?php
include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
include_once('includes/classes/users.class.php');

if(isset($_GET['username'])){$uname = $_GET['username'];}
else {$uname = 0;}

$u = new AFTWUser();
$IsThereAUser = $u->array_getUserDetails($uname);
if($IsThereAUser == 1)
{
	$u->get_id($u->UserArray['ID']);
	$PageTitle = $u->UserArray['display_name'] . ' - AnimeFTW.TV';
}
else 
{
	$PageTitle = 'Unknown User - AnimeFTW.tv';
	$index_global_message = "ERROR: There is no User by that Username.";
}
if($_SERVER['SERVER_PORT'] == 443)
{
	$ImageHost = 'https://d206m0dw9i4jjv.cloudfront.net';
}
else
{
	$ImageHost = 'http://img02.animeftw.tv';
}

include_once('header.php');
include_once('header-nav.php');
echo psa($profileArray,1);
//$index_global_message = "NOTICE! The Member Pages are still under development, please be aware that things are still set statically for design purposes.";
	if($IsThereAUser == 1){
		echo "<div id='ua'>";
		
		echo "<span class='head'>".$u->UserArray['display_name']."</span><br /><span class='headend'>Last Update:".$u->Status($profileArray[1])."</span>";
		echo "</div>";
	}
	// Start Main BG
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
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	if($IsThereAUser == 1){
		//start internal table for users                        $u->nVar('')
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td valign='top' width='20%'><div class='ucleft'>";
		/*echo "<div align='center'>";
		if($u->nVar('avatarActivate') == 'yes'){echo '<img src="http://www.animeftw.tv/images/avatars/user'.$u->UserArray['ID'].'.'.$u->nVar('avatarExtension').'" alt="'.$u->nVar('Username').'\'s Avatar" style="padding-right:10px;" rel="#uploadbox" class="avatardiv" /><br />';}
		else {echo '<img src="http://www.animeftw.tv/images/avatars/default.gif" alt="" border="0" width="100px" /><br />';}
		echo "</div>";*/
		echo '<div class="feature02" align="center">
		<div id="avatar-div-wrapper">';
		if($u->UserArray['avatarActivate'] == 'yes')
		{
			echo '<img src="' . $ImageHost . '/avatars/user'.$u->UserArray['ID'].'.'.$u->UserArray['avatarExtension'].'" alt="'.$u->UserArray['display_name'].'\'s Avatar" style="padding-right:10px;" id="user-avatar" class="avatardiv" />';
		}
		else
		{
			echo '<img src="' . $ImageHost . '/avatars/default.gif" alt="" border="0" width="100px" id="user-avatar" class="avatardiv" />';
		}
		echo '</div>';
		if($profileArray[1] == $u->UserArray['ID'] || $profileArray[2] == 1 || $profileArray[2] == 2)
		{
			echo '
			<br />
			<div id="avatar-form-wrapper" style="display:none;">
				<span class="label label-default">Image Type allowed: Jpeg, Jpg, Png and Gif. | Maximum Size 150KB</span>
				<form action="/scripts.php?view=avatar-upload" method="post" enctype="multipart/form-data" id="MyUploadForm">
					<input type="hidden" name="uid" id="uid" value="' . $u->UserArray['ID'] . '" />
					<input type="hidden" name="extension" id="extension" value="' . $u->UserArray['avatarExtension'] . '" />
					<input name="image_file" id="imageInput" type="file" />
					<input type="submit" id="submit-btn" value="Upload" />
					<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
				</form>
			</div>
			<script>
			$("#user-avatar").on("click", function() {
				$(\'#avatar-form-wrapper\').toggle();
			});
			</script>';
		}
		echo "</div><br />";
		echo "<div class='linfo'><a href=\"#\" onClick=\"$('.tab-content').slideUp();$('#tabcontent1').slideDown();return false;\"  title=\"\"><img src='/images/userv2.png' alt='' /><span class=''>My Profile</span></a></div>\n";
		echo "<div class='linfo'><a href=\"#\" onClick=\"$('.tab-content').slideUp();$('#tabcontent2').slideDown();return false;\"  title=\"\"><img src='/images/usercontactv2.png' alt='' /><span class=''>Contact Details</span></a></div>\n";
		if($profileArray[0] == 1){
			echo "<div class='linfo'>";
			echo $u->showFriendProfileButton($u->UserArray['ID'],$profileArray);
			echo "</div>\n";
			echo "<div class='linfo'><a href=\"/pm/compose/".$u->UserArray['ID']."\" title=\"\"><img src='/images/pmuserv2.png' alt='' /><span>Send a Site PM</span></a></div>\n";
		}
		echo "<div class='linfo'><a href=\"#\" onClick=\"$('.tab-content').slideUp(); $('#tabcontent3').slideDown(); $('#comments1').load('/scripts.php?view=comments&id=".$u->UserArray['ID']."&zone=-6'); return false;\"  title=\"\" id=\"tablink3\"><img src='/images/usercommentsv2.png' alt='' /><span>View My Comments</span></a></div>\n";
		echo "<div class='linfo'><a href=\"#\" onClick=\"$('.tab-content').slideUp(); $('#tabcontent4').slideDown(); $('#watchlistprofile').load('/scripts.php?view=watchlist&node=profileview&id=".$u->UserArray['ID']."'); return false;\"  title=\"\" id=\"tablink4\"><img src='/images/new-icons/watchlist_new.png' width='18px' alt='' style='padding-left:8px;' /><span>View My WatchList</span></a></div>\n";		
		echo "<div class='linfo'><a href=\"#\" onClick=\"$('.tab-content').slideUp(); $('#tabcontent6').slideDown(); $('#episodetracker').load('/scripts.php?view=tracker&id=".$u->UserArray['ID']."'); return false;\"  title=\"\" id=\"tablink6\"><img src='/images/viewtrackerv1.png' alt='' /><span>View Episode Tracker</span></a></div>\n";		
		if($u->UserArray['ID'] == $profileArray[1]){
			echo "<div class='linfo'><a href=\"#\" rel=\"#profile\" onClick=\"$('.tab-content').slideUp(); $('#tabcontent7').slideDown();$('#usernotifications').load('/scripts.php?view=notifications&show=profile&id=".$u->UserArray['ID']."'); return false;\"><img src='/images/new-icons/notifications_new.png' width='21px' alt='' /><span>View Notifications</span></a></div>\n";			
			echo "<div class='linfo'><a href=\"#\" onclick=\"loadEditProfile(".$u->UserArray['ID']."); return false;\"  title=\"\" id=\"tablink5\"><img src='/images/usersetv2.png' alt='' /><span>Edit Your Settings</span></a></div>\n";			
		}
		else if($profileArray[2] == 1 || $profileArray[2] == 2){
			echo "<div class='linfo'><a href=\"#\" onclick=\"$('.tab-content').slideUp(); $('#tabcontent5').slideDown(); $('#profilesettings').load('/scripts.php?view=settings&id=".$u->UserArray['ID']."'); return false;\"  title=\"\" id=\"tablink5\"><img src='/images/usersetv2.png' alt='' /><span>Edit Their Settings</span></a></div>";
		}
		echo '<script type="text/javascript">
				$(function() {
					$("a[rel]").overlay({mask: \'#000\', effect: \'apple\'});
				});
			</script>';
		echo "<div id='subfriends'><div class='fds'>Friends</div>";
		//put the friends here.
		echo "<div class=\"fb\" id=\"fb1\">Loading Friend Bar...</div>";
		echo "</div>";
		echo "</div>";
		echo "<div id='subfriends'><div class='fds'>Profile Stats</div>";
		$u->ProfileStats($profileArray[1]);
		echo "<br />";
		echo "</div>";
		echo "</div></td>";
		echo "<td valign='top'>
			<div id=\"tabcontent1\" class=\"tab-content\">";
		$u->Profile($profileArray[1]);
		$u->About($profileArray[1]);
		$u->Interests($profileArray[1]);
		$u->Signature($profileArray[1]);
		echo"	</div>
			<div id=\"tabcontent2\" class=\"tab-content\" style=\"display:none;\"><div class='fds'>Contact Information</div><br />";
		$u->ContactInfo($profileArray[1]);
			echo "</div>
			<div id=\"tabcontent3\" class=\"tab-content\" style=\"display:none;\"><div class=\"comments\" id=\"comments1\">Loading User Comments. Please Wait...</div></div>
			<div id=\"tabcontent4\" class=\"tab-content\" style=\"display:none;\"><div class=\"comments\" id=\"watchlistprofile\">Loading WatchList...</div></div>
			<div id=\"tabcontent5\" class=\"tab-content\" style=\"display:none;\"><div class=\"comments\" id=\"profilesettings\">Loading Settings...</div></div>
			<div id=\"tabcontent6\" class=\"tab-content\" style=\"display:none;\"><div class=\"comments\" id=\"episodetracker\">Loading Tracker Data...</div></div>
			<div id=\"tabcontent7\" class=\"tab-content\" style=\"display:none;\"><div class=\"comments\" id=\"usernotifications\">Loading Notification information..</div></div>";		
		echo "</td>";
		echo "</tr></table>";
		//echo "- <a href=\"/management/manage-episodes?episode=add&amp;series=".$sa1."\" rel=\"#profile\">Add Episode</a><br />\n";
		echo '<div class="apple_overlay" id="profile">
			<h2 style="margin:0px">Profile Functions</h2>
			<div class="comments" id="profileedit">Loading. Please Wait...</div>
		</div>';
		echo "
				<script type='text/javascript'>
				$('#fb1').load('/scripts.php?view=friendbar&id=".$u->UserArray['ID']."&zone=-6');
				</script>
				<script type=\"text/javascript\" src=\"/scripts/instantedit.js\"></script>
				<script>
					setVarsForm(\"view=user&edit=profile&uid=".$u->UserArray['ID']."\"); 
				</script>";
	}
	echo "<td>";
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";	
	if($profileArray[2] == 0 || $profileArray[2] == 3){
		echo '
		<div id="ad-wrapper" style="height:100%;position:absolute;z-index:0;">
			<div id="ad-sidebar" style="width:220px;float:right;margin:0 0 0 270px;position:absolute;z-index:0;">';
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Advertisement</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<div align="center"><a href="/advanced-signup" target="blank">Get rid of Ads by becoming an Advanced Member today!</a></div>';
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
	echo "<div class='fds'>Profile Comments</div><br />";
	// dynamic one for the good st00f
	$u->ShowProfileComments($profileArray[1]); 	
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	if($profileArray[1] == $u->UserArray['ID'] || ($profileArray[2] == 1 || $profileArray[2] == 2)){
		echo "<script>
				// What is $(document).ready ? See: http://flowplayer.org/tools/documentation/basics.html#document_ready
				$(document).ready(function() {
				// select the overlay element - and \"make it an overlay\"
				$(\"img[rel]\").overlay({
					// custom top position
					top: 260,
					// some mask tweaks suitable for facebox-looking dialogs
					mask: {
						// you might also consider a \"transparent\" color for the mask
						color: '#fff',
						// load mask a little faster
						loadSpeed: 200,
						// very transparent
						opacity: 0.5
					},				
					// disable this for modal dialog-type of overlays
					closeOnClick: false,				
					// load it immediately after the construction
					load: false				
				});
				});
				</script>";
				$max_size = 256;
			echo "<div id=\"uploadbox\" style=\"display:none;\">
					<div>
						<h2>Avatar Upload</h2>
						<br />";
						echo '<div id="content">
						<form action="/includes/avatarupload2.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >
							 <p id="f1_upload_process">Loading...<br/><img src="/images/loader.gif" /><br/></p>
							 <p id="f1_upload_form" align="center" style="margin-left:-100px;"><br/><label>
							 <span style="margin-right:100px;">File:</span>  
									  <input name="myfile" type="file" size="30" />
		
								 </label>
								 <label>
									 <span style="margin-right:-100px;"><input type="submit" name="submitBtn" class="sbtn" value="Upload" /></span>
								 </label>
							 </p>
							 
							 <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
						 </form>
					<p align="center">Please note:<br /> Basic Member Avatars are limited to 100x100 and 100KB files.<br /> Advanced Members can utilize 250x400 and 300KB size files.';
					if($profileArray[2] == 3){
						echo '<br /><a href="/advanced-signup">Sign up for Advanced Membership Today!</a>';
					}
					echo '</p>
					 </div>
					</div>
				</div>';
	}
	// End Main BG
	include_once('footer.php');
	?>