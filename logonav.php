<?php
define("THEME_WIDTH", "950");
$logo = "<a href='http://www.animeftw.tv/' title='".$PageTitle."'><img src='/images/animeftw_logo.png' alt='".$PageTitle."' style='border:0px;'/></a>";
	echo "	<table cellpadding='0' cellspacing='0' width='100%'>\n	<tr>\n";
	echo "	<td class='header-bg'>\n";
	echo "		<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n	<tr>\n";
	echo "			<td class='header-logo' style='padding-top: 9px;'>\n".$logo."</td>\n";
	echo "			<td align='right'>\n";
	echo "				<div class='header-log'>";
	if($profileArray[0] == 1) {
		if($profileArray[2] == 7) {echo checkUserNameNoLink($profileArray[5])."! | Inbox(".newMessages($profileArray[1]).") | <a href='https://www.animeftw.tv/logout'>Logout?</a>\n";}
		else if($profileArray[2] == 3) {echo checkUserNameNoLink($profileArray[5])."! | Inbox(".newMessages($profileArray[1]).") | <a href='https://www.animeftw.tv/logout'>Logout?</a>\n";}
		else {echo checkUserNameNoLink($profileArray[5])."! | <a href='/uploads'>Uploads</a> | Inbox(".newMessages($profileArray[1]).") | <a href='https://www.animeftw.tv/logout'>Logout?</a>\n";}
	}
	else {echo '<a href="/login">Sign In</a> | <a href="/register">Register</a> | <a href="/email-resend">Email Resend</a> | <a href="/forgot-password">Forgot Password</a>'."\n";}
	echo "				</div>\n";
	echo "				<div class='header-sort'>Sort <a href='/sort/#'>#</a> <a href='/sort/a'>A</a> <a href='/sort/b'>B</a> <a href='/sort/c'>C</a> <a href='/sort/d'>D</a> <a href='/sort/e'>E</a> <a href='/sort/f'>F</a> <a href='/sort/g'>G</a> <a href='/sort/h'>H</a> <a href='/sort/i'>I</a> <a href='/sort/j'>J</a> <a href='/sort/k'>K</a> <a href='/sort/l'>L</a> <a href='/sort/m'>M</a> <a href='/sort/n'>N</a> <a href='/sort/o'>O</a> <a href='/sort/p'>P</a> <a href='/sort/q'>Q</a> <a href='/sort/s'>S</a> <a href='/sort/t'>T</a> <a href='/sort/u'>U</a> <a href='/sort/v'>V</a> <a href='/sort/w'>W</a> <a href='/sort/x'>X</a> <a href='/sort/y'>Y</a> <a href='/sort/z'>Z</a> <a href='/sort'>More...</a></div>\n";
	echo "				<div class='header-dir'></div>\n";
	echo "			</td>\n";
	echo "		</tr>\n		</table>\n";
	echo "	</td>\n";
	echo " 	</tr>\n";
	echo "	<tr>\n";
	echo "	<td class='header-nav'>\n";
	echo "		<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n	<tr>\n";
	echo "			<td class='header-nav-left'>\n
					<a href='/'"; if($_SERVER['PHP_SELF'] == '/index.php'){echo ' class="current"';} echo ">Home</a>
					<a href='/anime'"; if($_SERVER['PHP_SELF'] == '/videos.php'){echo ' class="current"';} echo ">Anime</a>
					<a href='/edit'"; if($_SERVER['PHP_SELF'] == '/ucp.php'){echo ' class="current"';} echo ">Members</a>
					<a href='/store'"; if($_SERVER['PHP_SELF'] == '/store.php'){echo ' class="current"';} echo ">Store</a>
					<a href='/forums'"; if($_SERVER['PHP_SELF'] == '/forums.php'){echo ' class="current"';} echo ">Forums</a>
					<a href='/forums'>Devs</a></td>\n";
	echo "			<td align='right'>\n";
	echo "			<div class='header-search' style='float:right;'>";
	echo "			<div class='header-search-nav' style='float:right;'>";
	echo "				<form method='post' action='ajax.php'>
							<span class='search'>
									<input type='text' name='SearchInput' id='SearchInput' class='search-box' value='AnimeFTW.tv Site Search' />
									<input type='image' class='search-button' src='images/search-button.png' border='0' alt='' title='Search!' />
							</span>
						</form>";
	echo "				<div class='search-text'><img src='images/latest-news.png' alt='' /><span class='search-text-pre'>News: </span><span class='search-text-post'>AnimeFTW.tv Version 4.0 Released!</span></div><div id='SearchResults'></div>";
	echo "			</div></div>";
	echo "			</td>\n";
	echo "		</tr>\n		</table>\n";
	echo "	</td>\n";
	echo "	</tr>\n		</table>\n";
// Hey Ion, i'm bored out of town so i've done some adjustments/additions... lol -Brad
/*
</p>
			
			<div id="logo">
            	<img src="images/animeftw-logo.png" alt="aftw logo" />
			</div>
		</div>*/
		
?>