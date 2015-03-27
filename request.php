<?php
include('init.php');
$PageTitle = 'Anime Requests - AnimeFTW.TV';
include('header.php');
include('header-nav.php');
include('includes/classes/request.class.php');

$r = new AnimeRequest();

echo psa($profileArray);

// Start Main BG
echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
// End Main BG
echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
echo "</tr>\n</table>\n";
echo "<br />\n<br />\n";
// Start Mid and Right Content

$r->init();

//End
echo "</td>\n";
echo "</tr>\n</table>\n";

// Start Main BG
echo "</td>\n";
echo "</tr>\n</table>\n";
// End Main BG
	
include('footer.php');