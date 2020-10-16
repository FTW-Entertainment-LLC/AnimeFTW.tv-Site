<?php
 class query {
	public function __construct() {
	}
	public function query() {
		$args = func_get_args();
		$input = array_slice($args,1);
		$safe = $this->buildsafe($args[0],$input);
		$query = @mysqli_query($safe);

		if(gettype($query) == "resource") {
			$return = array();
			while($row = mysqli_fetch_assoc($query)) {
				$return[] = $row;
			}
			return $return;
		} else {
			return $query;
		}
	}
	private function buildsafe($query,$input) {
		foreach($input as $replace) {
			$safe = mysqli_real_escape_string($replace);
			$query = preg_replace("/%s/is",$safe,$query,1);
		}
		return $query;
	}
 }
?>