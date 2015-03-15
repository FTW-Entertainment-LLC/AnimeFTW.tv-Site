<?php
$PageTitle = 'AnimeFTW.tv Dev Site, Unauthorized access is strictly forbidden.';
$siteroot = 'dev.animeftw.tv';
include('includes/siteroot.php');
if (isset($_POST['username']) && isset($_POST['password'])) 
{
		function makeUrlFriendly($postUsername) {
			// Replace spaces with underscores
			$output = preg_replace("/\s/e" , "_" , $postUsername);

			// Remove non-word characters
			$output = preg_replace("/\W/e" , "" , $output);

			return strtolower($output);
			}	
$userName  = $_POST['username'];
$userName = makeUrlFriendly($userName);
	$password = $_POST['password'];
	$last_page = $_POST['last_page'];
	require_once ( 'includes/settings.php' );
	
	if ( array_key_exists ( '_submit_check', $_POST ) )
	{
		if ( $userName != '' && $password != '' )
		{
			$query = 'SELECT ID, Username, Active, Reason, Password, devaccess FROM ' . DBPREFIX . 'users WHERE Username = ' . $db->qstr ( $userName ) . ' AND Password = ' . $db->qstr ( md5 ( $password ) );

			if ( $db->RecordCount ( $query ) == 1 )
			{
				$row = $db->getRow ( $query );
				if($row->devaccess == 0)
				{
					//for now lets limit everyone but admins to the party...
					$error = 'WARNING: Developmental Access is restricted to Authorized users ONLY.';
				}
				else {
					if ( $row->Active == 1 )
					{
						include 'includes/config.php';
						include 'includes/newsOpenDb.php';
						set_login_sessions ( $row->ID, $row->Password, ( $_POST['remember'] ) ? TRUE : FALSE );
						$query = 'UPDATE users SET lastLogin=\''.time().'\' WHERE Username=\'' . $userName . '\'';
						mysql_query($query) or die('Error : ' . mysql_error());
						
						$query = "INSERT INTO `logins` (`ip`, `date`, `uid`) VALUES
	('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$row->ID."')";
						mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());	
						
						if ($last_page == '')
						{
							header ( "Location: http://".$siteroot."/user/".$userName );
						}
						else if ($last_page == 'http://www.animeftw.tv/login.php' || $last_page == 'http://www.animeftw.tv/login')
						{
							header ( "Location: http://".$siteroot."/user/".$userName );
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
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?=$sslornot;?>://<?=$siteroot;?>/style.css" type="text/css" />
	<link rel="stylesheet" href="<?=$sslornot;?>://<?=$siteroot;?>/jquery.bettertip.css" type="text/css" />
	<link rel="stylesheet" href="<?=$sslornot;?>://<?=$siteroot;?>/css.php" type="text/css" />
	<script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/ddlevelsmenu.js"></script>
	<script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/search-bar.js"></script>
	<script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/ajax.js"></script>
    <script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/ajax-poller.js"></script>
    <script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/jquery.bettertip.pack.js"></script>
	<script type="text/javascript" src="<?=$sslornot;?>://<?=$_SERVER['HTTP_HOST'];?>/scripts/comments-process.js"></script>
    <script type="text/javascript" src="<?=$sslornot;?>://<?=$_SERVER['HTTP_HOST'];?>/scripts/ajax-dynamic-content.js"></script>
    <script type="text/javascript" src="<?=$sslornot;?>://<?=$_SERVER['HTTP_HOST'];?>/scripts/global-aftw.js"></script>
    <?
	if($_SERVER['PHP_SELF'] == '/videos.php')
	{
	?>
	<script type="text/javascript" src="<?=$sslornot;?>://<?=$siteroot;?>/scripts/rating_update.js"></script>
	<?
	}
	?>
    <script type="text/javascript">
		$(function(){
			BT_setOptions({openWait:250, closeWait:0, cacheEnabled:true});
		})
	</script>
	<title><?=$PageTitle;?></title>
</head>
<body>
	<div id="content">		
		<?php 
		//include('logonav.php');
		//include('topnav.php'); 
		//include('topbox.php');
		?>	
        <br />
		<div class="left">
        
        <div class="left_articles" align="center">
		<div class="buttons"></div>
		<h2>Dev Site Login</h2>
    	<p class="description">&nbsp;</p>
		<p>

        
        <?php	if ( isset ( $error ) )	
				{ echo '<p class="error">' . $error . '</p>' . "\n";	
				}	?>
				<?php	if ( isset ( $msg ) )	
				{ echo '<p class="msg">' . $msg . '</p>' . "\n";	}
				 else {'Oooops it didnt work try again >.>';
				 }
				 if(isset($_COOKIE['__flc']))
				 {
					 $timeleft = $_COOKIE['__flc'] - time();
					 $time = round($timeleft/60);
					 echo 'ERROR: '.$time.' minute(s) left before reactivation.';
				 }
				 else {
				 ?>
                 <br />
                 <?
				/* $initialreferer = $_SERVER['HTTP_REFERER'];
				 if($initialreferer == 'http://www.animeftw.tv/')
				 {
					 $referer2 = 'https://www.animeftw.tv/';
				 }
				 else {
					 $referer2 = $_SERVER['HTTP_REFERER'];
				 }	*/
				 echo $siteroot;
				 ?>                
                 <div align="center">
            <form id="form1" action="/login" method="post">
			<input type="hidden" name="_submit_check" value="1"/>            
            <table width="340px">
            <tr>
                <td>
                <input name="last_page" type="hidden" value="<?=$referer2;?>" />
                <label for="username">Username:</label>
                </td>
                <td>
                <input name="username" id="username" type="text" style="width: 227px" /><br />
                </td>
            </tr>
            <tr>
            	<td>
                <label for="password">Password:</label>
                </td>
                <td>
                <input name="password" id="password" type="password" style="width:154px;" />
    
                <input name="submit" type="submit" class="button_2" value="Sign In" />
                </td>
            </tr>
            <tr>
            <td colspan="2">
            <div class="cb"></div>

							<div style="margin: 5px 0px 0px 70px;">
							
								<input type="checkbox" name="remember" id="remember" />Keep me logged in</div>
             	</td>
              </tr>
             </table>
             </form>
             </div>
            <?
			 }
			?> 
            </p>

			</div>          
		</div>		
		<?php
		//include('footer.php'); 
		?>
	</div>
</body>
</html>