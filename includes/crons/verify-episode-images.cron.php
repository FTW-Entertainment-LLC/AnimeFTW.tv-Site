<?php

$base = dirname(__dir__);
include $base . "/classes/config.class.php";

class ImageChecker extends Config {

	public function __construct() {
		parent::__construct();
		return $this;
	}

	public function check($filename, $cdn = false, $localFile = false) {

		if ($localFile) {
			// Set the relative location of the image storage here
			$file = "/{$filename}";
			// Check to see if it exists, and return "true"
			if (file_exists($file)) {
				return true;
			}
			return false;
		}

		$url = ($cdn) ? die("No CDN Url") : "http://img02.animeftw.tv/video-images/{$filename}";

		// Request is external, using cURL instead
		$ch = curl_init();

		// Set the URL
		curl_setopt($ch, CURLOPT_URL, $url);
		// Return Data, Don't Print
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Return the Header Data
		curl_setopt($ch, CURLOPT_HEADER, true);
		// Make a HEAD Request
		curl_setopt($ch, CURLOPT_NOBODY, true);

		// Execute the Request
		curl_exec($ch);
		// Get Header Response
		$data = curl_getinfo($ch);
		// Check if the Image did return 404, and return false
		if ($data["http_code"] == "404") {
			return false;
		}

		return true;
	}

	public function createImage($url) {
		return file_get_contents($url);
	}

}

$startTime = time();

$lastIdQuery = mysql_query("SELECT `value` FROM settings WHERE `name`='verify_image_exists_last_id'");
if (!$lastIdQuery)
	die("Failed to get verify_image_exists_last_id.");

$lastId = mysql_fetch_row($lastIdQuery);
if (!$lastId)
	die("Failed to get value from verify_image_exists_last_id.");

//$lastId = intval($lastId); // Do we want to conform it to an Int?

$seriesQuery = mysql_query("SELECT id, videoServer FROM series ORDER BY id LIMIT 1, {$lastId[0]}");
if (!$seriesQuery)
	die("Failed to get next series.");

$series = mysql_fetch_row($seriesQuery);
if (!$series)
	die("Failed to get series value for #{$lastId}.");

$episodesQuery = mysql_query("SELECT epprefix, epnumber, id, seriesname, vidheight, vidwidth, movie, videotype, image FROM episode WHERE `sid`='{$series[0]}'");
if (!$episodesQuery)
	die("Failed to get episodes.");

$missingImages = 0;
$lyingImages = 0;
$imageChecker = new ImageChecker();

while ($episode = mysql_fetch_row($episodesQuery)) {

	$exists = $imageChecker->check("{$episode[0]}_{$episode[1]}_screen.jpeg");
	if (!$exists) {

		$url = "http://{$series[1]}.animeftw.tv/fetch-pictures-v2.php?node=add&remote=true&seriesName={$episode[3]}&epprefix={$episode[0]}&epnumber={$episode[1]}&duration=360&vidwidth={$episode[4]}&vidheight={$episode[5]}&videotype={$episode[7]}&movie={$episode[8]}";

		$createResult = $imageChecker->createImage($url);

		if ($createResult == "success") {
			if ($episode[8] != 1) {
				// TODO: update episode.image = 1
				mysql_query("UPDATE episode SET image = 1 WHERE `id`='{$episode[2]}'");
			}
		} else {
			// Log failure where?
		}

	}

}

$endTime = time();
mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '15', '{$startTime}', '{$endTime}');");
mysql_query("UPDATE crons SET last_run = '{$endTime}', status = 0 WHERE id = 15"); // No idea why status = 0