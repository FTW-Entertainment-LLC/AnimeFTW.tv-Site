<?php

$startTime = time();

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

		$url = ($cdn) ? die("No CDN Url") : "http://images.animeftw.tv/video-images/{$filename}";

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

}

$lastIdQuery = mysqli_query($conn, "SELECT `value` FROM `settings` WHERE `name`='verify_image_exists_last_id'");
if (!$lastIdQuery) {
	die("Failed to get verify_image_exists_last_id");
}

$lastId = mysqli_fetch_row($lastIdQuery);
if (!$lastId) {
	die("Failed to get value from verify_image_exists_last_id");
}

//$lastId = intval($lastId); // Do we want to conform it to an Int?

$seriesQuery = mysqli_query($conn, "SELECT `id` FROM `series` WHERE `active`='yes' ORDER BY `id` LIMIT {$lastId[0]},1");
if (!$seriesQuery) {
	die("Failed to get series");
}

$series = mysqli_fetch_row($seriesQuery);
if (!$series) {
	$countCheckQuery = mysqli_query($conn, "SELECT count(id) FROM `series` WHERE `active`='yes'");

	if ($countCheckQuery) {

		$countCheck = mysqli_fetch_row($countCheckQuery);
		if (!$countCheck) {
			die("Fatal: Failed to check to see if next ID is safe or end of list");
		}

		$nextId = $lastId[0];
		if (++$nextId > $countCheck[0]) {
			$nextId = 0;
		}

		$settingsQuery = mysqli_query($conn, "UPDATE `settings` SET `value`='{$nextId}' WHERE `id` = '15'");

	} else {
		die("Fatal: Failed to check to see if next ID is safe or end of list");
	}

	die("Failed to get series value for #{$lastId[0]}");
}

$episodesQuery = mysqli_query($conn, "SELECT `id`, `spriteId` FROM `episode` WHERE `sid`='{$series[0]}'");
if (!$episodesQuery) {
	die("Failed to get episodes");
}

$imageChecker = new ImageChecker();

while ($episode = mysqli_fetch_row($episodesQuery)) {

	$exists = $imageChecker->check("{$series[0]}/{$episode[0]}_screen.jpeg");
	if (!$exists) {
		mysqli_query($conn, "UPDATE `episode` SET `image` = 0, `updated` = '" . time() . "' WHERE `id` = '{$episode[0]}'");
	}

	$exists = $imageChecker->check("{$series[0]}/{$episode[0]}_sprite.jpeg");
	if (!$exists && $episode[1] === null) {
		$spriteQuery = mysqli_query($conn, "DELETE FROM `sprites` WHERE `id` = '{$episode[1]}'");
		if ($spriteQuery) {
			mysqli_query($conn, "UPDATE `episode` SET `spriteId` = NULL, `updated` = '" . time() . "' WHERE `id` = '{$episode[0]}'");
		}
	}


}

$settingsQuery = mysqli_query($conn, "UPDATE `settings` SET `value`='" . ++$lastId[0] . "' WHERE `id` = '15'");
if (!$settingsQuery) {
	// What do...this is unrecoverable :L
}

$endTime = time();
mysqli_query($conn, "INSERT INTO `crons_log` (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '15', '{$startTime}', '{$endTime}');");
mysqli_query($conn, "UPDATE `crons` SET `last_run` = '{$endTime}', `status` = 0 WHERE `id` = 15"); // No idea why status = 0
