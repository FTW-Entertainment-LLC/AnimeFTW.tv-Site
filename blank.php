<?php
include('init.php');

include('header.php');
include('header-nav.php');
$index_global_message = "Welcome to the new index.php page!";
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// End Main BG
    echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td class='note-message' align='center'>".$index_global_message."</td>\n";
	echo "</tr>\n</table>\n";
	echo "<br />\n<br />\n";
	// Start Mid and Right Content
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td valign='top' class='main-mid'>\n";
		echo '<div class="side-body-bg"><div align="center" style="padding:4px 5px 5px 5px;">Showing the latest 10 comments for robotman321</div>
		<div class="side-body">Posted on: Saturday, September 3rd, 2011, 08:41 am<br>Posted in: <a href="/videos/no-6/">No.6</a>, Episode #<a href="/videos/no-6/ep-1">1</a><br>Comment:<br>Interesting.. I think I\'m going to like this one :D</div>
		<div class="side-body">Posted on: Friday, June 24th, 2011, 09:04 pm<br>Posted in: <a href="/videos/c/">C</a>, Episode #<a href="/videos/c/ep-10">10</a><br>Comment:<br>oh man... next episode gonna own to no end!</div>
		
		<div class="side-body">Posted on: Saturday, May 28th, 2011, 06:04 pm<br>Posted in: <a href="/videos/claymore/">Claymore</a>, Episode #<a href="/videos/claymore/ep-8">8</a><br>Comment:<br>PEOPLE.. the comments that were spoilers came BEFORE the spoiler function was around. I have hidden them as some people were just extremely disrespectful</div>
		<div class="side-body">Posted on: Saturday, May 28th, 2011, 01:58 pm<br>Posted in: <a href="/videos/deadman-wonderland/">Deadman Wonderland</a>, Episode #<a href="/videos/deadman-wonderland/ep-6">6</a><br>Comment:<br>wow. just wow.. this series is all full of Effed up-ness... holy cow..</div>
		<div class="side-body">Posted on: Saturday, April 23rd, 2011, 07:58 pm<br>Posted in: <a href="/videos/c/">C</a>, Episode #<a href="/videos/c/ep-1">1</a><br>Comment:<br>I like this series o.o, OP reminds me of Eden of the East.....</div>
		
		<div class="side-body">Posted on: Saturday, April 16th, 2011, 02:46 pm<br>Posted in: <a href="/videos/heavens-lost-property2/">Heaven\'s Lost Property 2</a>, Episode #<a href="/videos/heavens-lost-property2/ep-5">5</a><br>Comment:<br>theres no audio -.-\' i\'m redoing it now...</div>
		<div class="side-body">Posted on: Wednesday, April 13th, 2011, 09:31 pm<br>Posted in: <a href="/videos/crest-of-the-stars/">Crest of the Stars</a>, Episode #<a href="/videos/crest-of-the-stars/ep-1">1</a><br>Comment:<br>There are subs.. just not at the beginning.. notice the NOTICE XD telling you there are no subs...</div>
		<div class="side-body">Posted on: Friday, April 1st, 2011, 01:45 pm<br>Posted in: <a href="/videos/hajime-no-ippo/">Hajime no Ippo</a>, Episode #<a href="/videos/hajime-no-ippo/ep-60">60</a><br>Comment:<br>crimson.. its a spoiler... its clearly marked as a spoiler... i\'m deleting your comment for flaming, he did nothing wrong.</div>
		
		<div class="side-body">Posted on: Wednesday, February 16th, 2011, 02:43 pm<br>Posted in: <a href="/videos/fairy-tail/">Fairy Tail</a>, Episode #<a href="/videos/fairy-tail/ep-65">65</a><br>Comment:<br>this video loads fine....!</div>
		<div class="side-body">Posted on: Sunday, February 13th, 2011, 09:22 am<br>Posted in: <a href="/videos/bleach/">Bleach</a>, Episode #<a href="/videos/bleach/ep-308">308</a><br>Comment:<br>omgomgomgomg epic! EPIC!!!!</div>';
	echo "</td>\n";
	echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
	# Right Content here
	#
	echo "<div class='side-body-bg'>";
	echo "<div class='scapmain'>Panel 1 Title Here</div>\n";
	echo "<div class='side-body floatfix'>\n";
	echo "I am a random text and I will repeat. I am a random text and I will repeat. I am a random text and I will repeat. I am a random text and I will repeat.";
	echo "</div></div>\n";
	if($profileArray[2] == 0 || $profileArray[2] == 3){
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
		
include('footer.php')
?>