<?php
include('includes/siteroot.php');
$PageTitle = 'AnimeFTW.tv Account Login | '.$siteroot.' | Your DivX Anime streaming Source!';
if (isset($_POST['username']) && isset($_POST['password'])){
$userName  = $_POST['username'];
$userName = makeUrlFriendly($userName);
	$password = $_POST['password'];
	$last_page = $_POST['last_page'];
	require_once ( 'includes/settings.php' );
	
	if ( array_key_exists ( '_submit_check', $_POST ) ){
		if ( $userName != '' && $password != '' ){
			$query = 'SELECT ID, Username, Active, Reason, Password, devaccess FROM ' . DBPREFIX . 'users WHERE Username = ' . $db->qstr ( $userName ) . ' AND Password = ' . $db->qstr ( md5 ( $password ) );
			if ( $db->RecordCount ( $query ) == 1 ){
				$row = $db->getRow ( $query );
				if ( $row->Active == 1 ){
					include 'includes/config.php';
					include 'includes/newsOpenDb.php';
					set_login_sessions ( $row->ID, $row->Password, ( $_POST['remember'] ) ? TRUE : FALSE );
					$query = 'UPDATE users SET lastLogin=\''.time().'\' WHERE Username=\'' . $userName . '\'';
					mysql_query($query) or die('Error : ' . mysql_error());
					$query = "INSERT INTO `logins` (`ip`, `date`, `uid`) VALUES
	('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$row->ID."')";
					mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());	
					if ($last_page == ''){
						header ( "Location: http://".$siteroot."/profile/".$userName );
					}
					else if ($last_page == 'http://www.animeftw.tv/login.php' || $last_page == 'http://www.animeftw.tv/login'){
						header ( "Location: http://".$siteroot."/profile/".$userName );
					}
					else {
						header ( "Location: ".$last_page );
					}
				}
				elseif ( $row->Active == 0 ) {
					$error = 'Your membership was not activated. Please open the email that we sent and click on the activation link.';
					$failed = FALSE;
				}
				elseif ( $row->Active == 2 ) {
						$error = 'You are suspended!<br /><br /> Reason: '.$row->Reason.'<br /><br />If you feel this suspension is in error, please email: support@animeftw.tv with your username and the reason given above.';
				$failed = FALSE;
				}
			}
		}
		else {		
			$query = "INSERT INTO `failed_logins` (`name`, `password`, `ip`, `date`) VALUES
('".$userName."', '".$password."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')";
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());	
			$error = 'Login failed! Password or Username is Incorrect.<br />'.checkFailedLogins($_SERVER['REMOTE_ADDR']);
		}
	}
	else {
		$error = 'Please use both your username and password to access your account';	
	}
}
include('header.php');
include('header-nav.php');
$index_global_message = "Welcome to the new index.php page!";
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	echo '<div class="side-body-bg">
		<span class="scapmain">AnimeFTW.tv Members Login</span>
		<br />
		<span class="poster">Use your AnimeFTW.tv Account to access copious amounts of Anime in the Greatest Quality found on the net.</span>
		</div>
		<div class="tbl"><br />';
	if(isset($error)){ 
		echo '<p class="error">' . $error . '</p>' . "\n";	
	}
	if(isset($msg)){
		echo '<p class="msg">' . $msg . '</p>' . "\n";	
	}
	else {'Oooops it didnt work try again >.>';}
	if(isset($_COOKIE['__flc'])){
		$timeleft = $_COOKIE['__flc'] - time();
		$time = round($timeleft/60);
		echo 'ERROR: '.$time.' minute(s) left before reactivation.';
	}
	else {echo'<br />';
	$initialreferer = $_SERVER['HTTP_REFERER'];
	if($initialreferer == 'http://www.animeftw.tv/') {
					 $referer2 = 'https://www.animeftw.tv/';
	}
	else{$referer2 = $_SERVER['HTTP_REFERER'];}	
    echo '<div align="center"><form id="form1" action="/login" method="post">
			<input type="hidden" name="_submit_check" value="1"/>
            <table width="500px">
            <tr>
                <td align="right">
                <input name="last_page" type="hidden" value="'.$referer2.'" />
                <label class="left" for="username" style="margin: 0px 0px 0px 0px;color:#555555;">Username:</label>
                </td>
                <td width="340px" align="left">
                <input name="username" id="username" type="text" class="loginForm" style="width: 227px" /><br />
                </td>
            </tr>
            <tr>
            	<td align="right">
                <label class="left" for="password" style="margin: 0px 0px 0px 0px;color:#555555;">Password:</label>
                </td>
                <td align="left">
                <input name="password" id="password" type="password" class="loginForm" style="width:154px;" />
                <input name="submit" type="submit" class="button_2" value="Sign In" />
                </td>
            </tr>
            <tr>
            <td colspan="2">
            <div class="cb"></div>
				<div style="margin: 5px 0px 0px 100px;">
						<div style="margin-left:50px;"><input type="checkbox" name="remember" id="remember" />Keep me logged in</div>
						<div style="font-size: 9px;">(Not recommended for public or shared computers)</div>								
						<div style="margin: 10px 0px 0px 50px;"><a href="/forgot-password">Forgot Password?</a></div>								
						<div style="margin: 10px 0px 0px 50px;">Don\'t have an account? <a href="/register">Register Here.</a></div>
             	</td>
              </tr>
             </table>
             </form>
			 <br /><br />
			 <i>AnimeFTW.tv Members Enjoy many perks over the average Anime Streaming site. By logging in with your AnimeFTW.tv Account, you are given access to the net\'s Largest library of on Demand Streaming Anime in DivX. <br /><br />Along with the perks that come with being a basic member, users can upgrade their account, "FTW Subscribers" are allowed to enhance their AnimeFTW.tv Account by making them Advanced Members. AMs for short, are allowed to download all our videos and have direct access to the CDN Network for the fastest download speeds anywhere in the world.</i></div>
			 </div>';
			 }
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
		
include('footer.php')
?>