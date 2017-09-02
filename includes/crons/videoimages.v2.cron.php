<?php
/****************************************************************\
## FileName: videoimages.v2.cron.php
## Authors: Brad Riemann, nikey646
## Usage: Checks for episodes added in the last 15 minutes and
## runs through and initiates the image creation script.
## Copyright 2012-2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

$includePath = ($_SERVER['HTTP_HOST'] === "v4.aftw.ftwdevs.com" ||
                $_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com'||
				$_SERVER['HTTP_HOST'] === "phpdev") ? "../.." : "/home/mainaftw/public_html";

require_once("{$includePath}/includes/classes/cronabstract.class.php");
require_once("{$includePath}/includes/classes/email.v2.class.php");

class VideoImagesCron extends CronAbstract {

	public function run() {
		$this->startTime = time();
		$searchTime	= $this->startTime - 900;


		// Maintainable, Easy to Read, and Hard Line Wrapping, Modular SQL Construction
		// Downside: Those tabs appear in the string, this could cause some weird performance problem?

		// 3 Columns per line.
		$columns	=	"`episode`.`id` as `epid`, `episode`.`spriteId`, `series`.`seriesName`,
						`episode`.`epprefix`, `episode`.`epnumber`, `episode`.`vidwidth`,
						`episode`.`vidheight`, `episode`.`Movie`, `episode`.`videotype`,
						`episode`.`image`, `episode`.`sid`, `series`.`fullSeriesName`";
		$tables		=	"`episode`, `series`";
		// One set of Condition(s) per line.
		$conditions	=	"`episode`.`sid` = `series`.`id`
						AND ((`episode`.`date` != 0 AND `episode`.`date` >= ?)
						OR (`episode`.`updated` IS NOT NULL AND `episode`.`updated` >= ?))
						AND (`episode`.`image` = 0 OR `episode`.`spriteId` IS NULL)";
		$sql		=	"SELECT {$columns} FROM {$tables} WHERE {$conditions}";

		if ($_SERVER['HTTP_HOST'] === 'phpdev') { // Debug, didn't want to wait 2 hours for images to be made :>
			$searchTime	= 1; // Debug; Get any results
			$sql .= "LIMIT 1";
		}

		// Failure has occurred, ensure success if false, set the endTime and quit
		if (!$searchQuery = $this->mysqli->prepare($sql)) {
			$this->success = false;
			$this->endTime = time();
			return;
		}

		$searchQuery->bind_param("ii", $searchTime, $searchTime);
		if (!$searchQuery->execute()) {
			$this->success = false;
			$this->endTime = time();
			return;
		}

		// This method is only available when a certain driver is...
		// polyfill method with worse performance is provided when driver is unavailable.
		if (method_exists($searchQuery, "get_result")) {
			$results = $searchQuery->get_result();
			$rows = [];

			if ($results->num_rows <= 0) {
				$this->success = false;
				$this->endTime = time();
				return;
			}

			while ($obj = $results->fetch_object()) {
				$rows[] = $obj;
			}

			$results = $rows;
		} else {
			$results = $this->mysqliFetchObject($searchQuery);

			if (!count($results)) {
				$this->success = false;
				$this->endTime = time();
				return;
			}
		}

		$reportback = "";

		foreach ($results as $result) {

//			$url = "http://phpdev/Testing/FetchPictures/fetch-pictures.php"; // Debug purposes ;D
			$url =	"http://images.animeftw.tv/scripts/fetch-pictures.php";
			$url .= "?seriesName={$result->seriesName}&seriesId={$result->sid}";
			$url .= "&epprefix={$result->epprefix}&epnumber={$result->epnumber}";
			$url .= "&epid={$result->epid}&duration=360";
			$url .= "&vidwidth={$result->vidwidth}&vidheight={$result->vidheight}";
			$url .= "&videotype={$result->videotype}&movie={$result->Movie}";

			if ($result->image !== 0 && $result->spriteId !== null) {
				if ($result->image === 0) {
					$url .= "&mode=thumbnail";
				} else {
					$url .= "&mode=sprite";
				}
			}

			$contents = file_get_contents($url);
			$response = json_decode($contents);

			if ($response->error) {
				// TODO: Remove needless whitespace
				$reportback .= "L#110: Failed to generate image(s) for \"{$result->fullSeriesName}\", at \"{$result->seriesName}/{$result->epprefix}_{$result->epnumber}_ns.mp4\". Reason: \"{$response->reason}\"\n";
				continue; // Skip this one.
			}

			$image		= -1;
			$spriteId	= null;
			$time 		= time();

			$columns	=	"`updated` = ?";
			$tables		=	"`episode`";
			$conditions	=	"`id` = ?";

			if ($result->spriteId === null) {
				$spritesColumns	=	"(`width`, `height`, `totalWidth`,
									`rate`, `count`, `created`)
									VALUES (?, ?, ?, ?, ?, ?)";
				$spritesTables	=	"`sprites`";

				$spritesSql = "INSERT INTO {$spritesTables} {$spritesColumns}";
				if ($spritesQuery = $this->mysqli->prepare($spritesSql)) {
					$spritesQuery->bind_param("iiiiii", $response->sprite->width, $response->sprite->height,
														$response->sprite->totalWidth, $response->sprite->rate,
														$response->sprite->count, $time);
					if ($spritesQuery->execute()) {
						$spriteId = $spritesQuery->insert_id;
						$columns .= ", `spriteId` = ?";
					} else {
						$reportback .= "L#137: Failed to insert sprites data for episode {$result->epnumber} of \"{$result->fullSeriesName}\", with ID {$result->id}.\n";
					}
				} else {
					$reportback .= "L#140: Failed to prepare sprites query for episode {$result->epnumber} of \"{$result->fullSeriesName}\", with ID {$result->id}.\n";
				}
			}

			if ($result->image === 0) {
				$columns .= ", `image` = ?";
				$image = 1;
			}

			$sql = "UPDATE {$tables} SET {$columns} WHERE {$conditions}";
			if ($query = $this->mysqli->prepare($sql)) {
				if ($image === -1 && $spriteId === null) { // Neither...This shouldn't be possible.
					$query->bind_param("ii", $time, $result->epid);
				} else if ($image !== -1 && $spriteId === null) { // Only image
					$query->bind_param("iii", $time, $image,
											$result->epid);
				} else if ($image === -1 && $spriteId !== null) { // Only sprite
					$query->bind_param("iii", $time, $spriteId,
											$result->epid);
				} else { // Both Image and Sprite
					$query->bind_param("iiii", $time, $spriteId,
											$image, $result->epid);
				}

				if (!$query->execute()) {
					$reportback .= "L#165: Failed to update Episode {$result->epnumber} of \"{$result->fullSeriesName}\", with ID {$result->id}.\n";
				}
			} else {
				$reportback .= "L#168: Failed to prepare episode query for episode {$result->epnumber} of \"{$result->fullSeriesName}\", with ID {$result->epid}.\n";
			}
		}

		$reportback = trim($reportback);
		if (!empty($reportback)) {
			$reports	= "Video Image Creation Errors.\n\n{$reportback}";
			$email		= new Email("support@animeftw.tv");
			$email->send("2", $reports);
		}

		$this->success = true;
		$this->endTime = time();
	}

	// Credits http://stackoverflow.com/a/5287653/1891512 and php documentation
	// Work around to create stdClass when get_results is not available.
	private function mysqliFetchObject(mysqli_stmt $query) {
		$rows			= [];
		$result			= [];
		$resultArray	= [];

		$fields	= $query->result_metadata()->fetch_fields();

		// Reference work around due to MySQLi being...weird.
		foreach ($fields as $field) {
			$result[$field->name] = null;
			$resultArray[$field->name] = &$result[$field->name];
		}

		call_user_func_array(array($query, "bind_result"), $resultArray);

		while ($query->fetch()) {
			$obj = new stdClass();

			foreach ($resultArray as $key => $value) {
				$obj->$key = $value;
			}

			$rows[] = $obj;
		}

		return $rows;
	}

}

$cron = new VideoImagesCron(1);
$cron->run();