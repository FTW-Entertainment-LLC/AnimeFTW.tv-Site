<?php
include('init.php');
	include ( 'includes/settings.php' );

	if ( $_GET['ID'] != '' && numeric ( $_GET['ID'] ) == TRUE && strlen ( $_GET['key'] ) == 32 && alpha_numeric ( $_GET['key'] ) == TRUE ) {
		$query = "SELECT ID, Password, Random_key, Active FROM " . DBPREFIX . "users WHERE ID = " . $db->qstr ( $_GET['ID'] );
		
		if ( $db->RecordCount ( $query ) == 1 ) {
			$row = $db->getRow ( $query );
			if ( $row->Active == 1 ) {
				$error = 'This member is already active !';
			}
			elseif ( $row->Random_key != $_GET['key'] ) {
				$error = 'The confirmation key that was generated for this member does not match with the one entered !';
			}
			else {
				$update = $db->query ( "UPDATE " . DBPREFIX . "users SET Active = 1 WHERE ID=" . $db->qstr ( $row->ID ) );
				if ( REDIRECT_AFTER_CONFIRMATION ) {
					//don't echo put anything before this line
					set_login_sessions ( $row->ID, $row->Password, FALSE );
					header ( "Location: https://www.animeftw.tv/login");
				}
				else {
					$msg = 'Congratulations !  You just confirmed your membership !<br /><br /> Now go Visit your <a href="http://animeftw.tv/edit">member profile</a> and update it accordingly :D !';
				}
			}
		}
		else {		
			$error = 'User not found !';		
		}
	}
	else {
		$error = 'Invalid data provided !';
	}
$PageTitle = 'Confirm Registration - AnimeFTW.tv';

include('header.php');
include('header-nav.php');
$index_global_message = "Welcome to the new index.php page!";
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// Start Mid and Right Content
	echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Registration Confirmation</span>\n";
		echo "<br />\n";
		echo "<span class='poster'>Some Basic Statistics about AnimeFTW.tv and her Users.</span>\n";
		echo "</div>\n";
		if ( isset( $error ) ) { echo '			<div>' . $error . '</div>' . "\n";}
		if ( isset( $msg ) ) { echo '			<div>' . $msg . '</div>' . "\n";}
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
		
include('footer.php')
?>