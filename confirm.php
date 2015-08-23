<?php
include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
include_once('includes/classes/register.class.php');
$Register = new Register();
$OutputArray = $Register->confirmAccount();
$msg = $OutputArray['msg'];
$error = $OutputArray['error'];
	
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