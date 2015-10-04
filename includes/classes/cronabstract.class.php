<?php
/****************************************************************\
## FileName: cronabstract.class.php
## Author: Nikey
## Usage: An abstract class that implements ICron to be used as
## a base for classes that will report to crons_logs and crons
## database tables. Autoloads Config v2 and ICron interface.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

if (!class_exists("Config", false)) {
	// require in the v2 Config if the Config class does not exist.
	require_once "config.v2.class.php"; // Is this hack acceptable?
} else {
	// Ensure that we have the v2 Config.
	if (!method_exists("Config", "DB_Con")) {
		throw new Exception("Wrong Config Class Loaded.");
	}
}

if (!interface_exists("ICron", false)) {
	// (Is this relative pathing by our standards? It allows devsite and prodsite to work?)
	require_once "interfaces/cron.interface.php"; // Moar hacks!
}

class CronAbstract extends Config implements ICron {

	protected $id,
			$success;

	protected $startTime,
			$endTime;

	public function __construct($id = 0) {
		parent::__construct(); // Not sure what autoBuildUser does; but i presume we don't need it for Cron stuff!
		$this->id = $id;
	}

	public function run() {
		throw new Exception("Not implemented.");
	}

	public function __destruct() {

		if ($this->endTime == null) {
			$this->endTime = time();
		}

		$logSQL = "INSERT INTO `crons_log` (`cron_id`, `start_time`, `end_time`) VALUES (?, ?, ?)";
		// We assume that it works, since no real nice way to eject.
		if ($logQuery = $this->mysqli->prepare($logSQL)) {
			$logQuery->bind_param("iii", $this->id, $this->startTime, $this->endTime);
			$logQuery->execute();
		}

		// TODO: Some sort of $success check, but not sure where in the crons table that goes? Or if it is even needed.

		$cronSQL = "UPDATE `crons` SET `last_run` = ?, `status` = 0 WHERE `id` = ?";
		if ($cronQuery = $this->mysqli->prepare($cronSQL)) {
			$cronQuery->bind_param("ii", $this->endTime, $this->id);
			$cronQuery->execute();
		}

	}

}