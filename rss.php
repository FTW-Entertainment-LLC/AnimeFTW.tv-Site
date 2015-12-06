<?php
header('Content-Type: application/xml');
// "application/rss+xml charset=UTF-8"

include_once('includes/classes/config.class.php');
$Config = new Config();
$Config->buildUserInformation();
$profileArray = $Config->UserArray;
if(!isset($_GET['type']))
{
	// we never defined the type of RSS feed we were looking for so they get the default news..
echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <atom:link rel="self" type="application/rss+xml" href="http://www.animeftw.tv/rss/threads" />
    <title>Latest News and Updates from AnimeFTW.tv</title>
    <link>http://www.animeftw.tv/</link>
    <description>Latest News for AnimeFTW.tv</description>
    <language>en</language>
    <copyright>FTW Entertainment LLC</copyright>
    <managingEditor>support@animeftw.tv (AnimeFTW.tv Support)</managingEditor>
    <webMaster>dnsadmin@ftwentertainment.com (DNS Admin)</webMaster>
    <pubDate>' . date("r") . '</pubDate>
    <lastBuildDate>' . date("r") . '</lastBuildDate>
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <ttl>2</ttl>
    <image>
      <url>http://www.animeftw.tv/images/aa92a33a-f2fa-4ace-b5b7-5a7a11b89770.png</url>
      <link>http://www.animeftw.tv/</link>
      <title>Latest News and Updates from AnimeFTW.tv</title>
    </image>';

$query = "SELECT forums_threads.tid, forums_threads.ttitle, forums_threads.tfid, forums_threads.tdate, forums_forum.fseo, users.Username FROM forums_threads, forums_forum, users WHERE (tfid='1' OR tfid='2' OR tfid='9') AND users.ID=tpid AND fid=tfid ORDER BY tid DESC LIMIT 0, 10";
$result = mysql_query($query) or die('Error : ' . mysql_error());
while(list($tid, $ttitle, $tfid, $tdate, $fseo, $Username) = mysql_fetch_array($result)) 
{	
	$pbody = strip_tags($pbody);
	echo '<item>
      <pubDate>' . date("r",$tdate) . '</pubDate>
      <title>' . stripslashes($ttitle) . '</title>
      <description>' . stripslashes($ttitle) . '</description>
      <guid isPermaLink="true">http://www.animeftw.tv/forums/' . $fseo . '/topic-' . $tid . '/</guid>
      <link>http://www.animeftw.tv/forums/' . $fseo . '/topic-' . $tid . '/</link>
      <author>spport@animeftw.tv (' . $Username . ')</author>
    </item>';
}
echo '	</channel>
</rss>';
}
else
{
	if(isset($_GET['type']) && $_GET['type'] == 'episodes')
	{
		echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" 
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
<channel>
    <atom:link rel="self" type="application/rss+xml" href="http://www.animeftw.tv/rss/episodes" />
    <title>Latest 50 Episodes from AnimeFTW.tv</title>
    <link>http://www.animeftw.tv/anime</link>
    <description>View the latest 50 episodes from AnimeFTW.tv!</description>
    <language>en</language>
    <copyright>Copyright (C) 2015 FTW Entertainment LLC</copyright>
    <managingEditor>support@animeftw.tv (AnimeFTW.tv Support)</managingEditor>
    <webMaster>dnsadmin@ftwentertainment.com (DNS Admin)</webMaster>
    <pubDate>' . date("r") . '</pubDate>
    <lastBuildDate>' . date("r") . '</lastBuildDate>
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <ttl>2</ttl>
    <image>
      <url>http://www.animeftw.tv/images/aa92a33a-f2fa-4ace-b5b7-5a7a11b89770.png</url>
      <link>http://www.animeftw.tv/anime</link>
      <title>Latest 50 Episodes from AnimeFTW.tv</title>
    </image>';
		if($profileArray[0] == 0)
		{
			// they are not logged in, show them series that are for unregistered users..
			$query = "SELECT `episode`.`epname`, `episode`.`epnumber`, `episode`.`Movie`, `episode`.`date`, `series`.`fullSeriesName`, `series`.`seoname` FROM `series`, `episode` WHERE `series`.`aonly`= 0 AND `series`.`stillRelease` = 'yes' AND `series`.`id`=`episode`.`sid` ORDER BY `episode`.`id` DESC LIMIT 0, 50";
		}
		else
		{
			// logged in, let them see all episodes episodes..
			$query = "SELECT `episode`.`epname`, `episode`.`epnumber`, `episode`.`Movie`, `episode`.`date`, `series`.`fullSeriesName`, `series`.`seoname` FROM `series`, `episode` WHERE `series`.`id`=`episode`.`sid` ORDER BY `episode`.`id` DESC LIMIT 0, 50";
		}
		$result = mysql_query($query);
		while($row = mysql_fetch_assoc($result))
		{
			if($row['Movie'] != 0)
			{
				// its a movie..
				$videotype = 'movie-';
				$fullType = 'Movie';
			}
			else
			{
				$videotype = 'ep-';
				$fullType = 'Episode';
			}
			echo '
	<item>
		<pubDate>' . date("r",$row['date']) . '</pubDate>
		<title><![CDATA[' . $fullType . ' ' . $row['epnumber'] . ' of ' . stripslashes($row['fullSeriesName']) . ' ]]></title>
		<description><![CDATA[Episode ' . $row['epnumber'] . ' of ' . stripslashes($row['fullSeriesName']) . ' was added to the site, titled ' . stripslashes($row['epname']) . ']]></description>
		<guid isPermaLink="true">http://www.animeftw.tv/anime/' . $row['seoname'] . '/' . $videotype . $row['epnumber'] . '</guid>
		<link>http://www.animeftw.tv/anime/' . $row['seoname'] . '/' . $videotype . $row['epnumber'] . '</link>
		<author>support@animeftw.tv (AnimeFTW.tv Staff)</author>
    </item>';
		}
echo '
</channel>
</rss>';
	}
	else if(isset($_GET['type']) && $_GET['type'] == 'series')
	{
		echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <atom:link rel="self" type="application/rss+xml" href="http://www.animeftw.tv/rss/series" />
    <title>Latest 20 Series from AnimeFTW.tv</title>
    <link>http://www.animeftw.tv/anime</link>
    <description>View the latest 20 series from AnimeFTW.tv!</description>
    <language>en</language>
    <copyright>Copyright (C) 2015 FTW Entertainment LLC</copyright>
    <managingEditor>support@animeftw.tv (AnimeFTW.tv Support)</managingEditor>
    <webMaster>dnsadmin@ftwentertainment.com (DNS Admin)</webMaster>
    <pubDate>' . date("r") . '</pubDate>
    <lastBuildDate>' . date("r") . '</lastBuildDate>
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <ttl>2</ttl>
    <image>
      <url>http://www.animeftw.tv/images/aa92a33a-f2fa-4ace-b5b7-5a7a11b89770.png</url>
      <link>http://www.animeftw.tv/anime</link>
      <title>Latest 20 Series from AnimeFTW.tv</title>
    </image>';
		if($profileArray[0] == 0)
		{
			// they are not logged in, they cannot pass
			echo '
	<item>
		<pubDate>' . date("r",'2015-01-01') . '</pubDate>
		<title>RSS Feed not available to unregistered users.</title>
		<description><![CDATA[Please log in with an account to see this RSS feed.]]></description>
		<guid isPermaLink="true">http://www.animeftw.tv/register</guid>
		<link>http://www.animeftw.tv/register</link>
		<author>support@animeftw.tv (AnimeFTW.tv Staff)</author>
    </item>';
		}
		else
		{
			// logged in, let them see episodes..
			$query = "SELECT `fullSeriesName`, `seoname`, `description` FROM `series` WHERE `active` = 'yes' ORDER BY `id` DESC LIMIT 0, 20";
			$result = mysql_query($query);
			while($row = mysql_fetch_assoc($result))
			{
				echo '
	<item>
		<pubDate>' . date("r") . '</pubDate>
		<title>' . stripslashes($row['fullSeriesName']) . '</title>
		<description><![CDATA[' . htmlentities($row['description'], ENT_COMPAT | ENT_SUBSTITUTE, 'utf-8') . ']]></description>
		<guid isPermaLink="true">http://www.animeftw.tv/anime/' . $row['seoname'] . '/</guid>
		<link>http://www.animeftw.tv/anime/' . $row['seoname'] . '/</link>
		<author>support@animeftw.tv (AnimeFTW.tv Staff)</author>
    </item>';
			}
		}
echo '
</channel>
</rss>';
	}
	else
	{
		echo 'Help ive fallen and cant get up!';
	}
}
