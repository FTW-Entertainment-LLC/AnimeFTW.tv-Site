<?php
include('includes/siteroot.php');
if($_GET['error'] == '404'){$PageTitle = 'Error 404, Document Not Found - AnimeFTW.tv';}
if($_GET['error'] == '403'){$PageTitle = 'Error 403, Denied - AnimeFTW.tv';}
if($_GET['error'] == '500'){$PageTitle = 'Error 500, Unknown - AnimeFTW.tv';}

include('header.php');
/*include('header-nav.php');
	echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
	echo "<div class='side-body-bg'>\n";*/
	if($_GET['error'] == '404'){
		/*echo "<span class='scapmain'>Error 404, Document not Found.</span>\n";
		echo "<br />\n";
		echo "</div>\n";*/
		echo "<br /><div align=\"center\"><h3>Were Sorry.. That page doesn't exist.. yet...</h3><br /><img src=\"/images/404.png\" alt=\"Error 404 Image\" title=\"Error 404!\" /><br /><h3>....While you wait, can we interest you in some <a href=\"/anime\">Anime</a>?</h3></div>";
	}
	else if($_GET['error'] == '403'){
		echo "<span class='scapmain'>Error 403, Access is Denied.</span>\n";		
		echo "<br />\n";
		echo "</div>\n";
	}
	else if($_GET['error'] == '500'){
		echo "<span class='scapmain'>Error 500, The server got confused...</span>\n";
		echo "<br />\n";
		echo "</div>\n";
	}
	else {}
	/*echo "<div class='tbl'></div>\n";
	echo "<br />\n";
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	if($profileArray[0] == 0){
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>Advertisement</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo "<!-- Begin BidVertiser code --><SCRIPT LANGUAGE=\"JavaScript1.1\" SRC=\"http://bdv.bidvertiser.com/BidVertiser.dbm?pid=341006&bid=842663\" type=\"text/javascript\"></SCRIPT><noscript><a href=\"http://www.bidvertiser.com\">internet marketing</a></noscript><!-- End BidVertiser code --> ";
	echo "</div></div>\n";
	}	
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
					
					*/
include('footer.php')
?>