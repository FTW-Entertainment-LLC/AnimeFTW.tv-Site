<?php
//////////////////////////////////////////////
// PHP Countdown v1.5
// (C) 2006 Nathan Bolender
// www.nathanbolender.com
//////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
//////////////////// DO NOT EDIT BELOW THIS LINE //////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////

$version = '1.5';

define("YEAR", date('Y'));
function countdown($month, $day, $year=YEAR, $hour=0, $minute=0, $second=0) {
///////////////////////////////////////////
// usage:
// countdown(int month, int day [, int year [, int hour [, int minute [, int second]]]])
// year is optional and defaults to the current year
// hour, minute, and second are optional and default to 0
/////
// Returns:
// array(
// 		int years
// 		int months
// 		int days
// 		int hours
// 		int minutes
// 		int seconds
// 		str now
// 		str then
//		int passed
//		)
///////////////////////////////////////////
	$now = time();
	$then = mktime($hour,$minute,$second,$month,$day,$year);
	
		$till = $then-$now; // seconds until $then
		if ($till < 0) {
			$passed = 1;
			$till = abs($till);
		} else {
			$passed = 0;
		}
		$years = floor($till/31556926); // 31556926 seconds in a year
		$months = floor(($till%31556926)/2629744); // remainder of years into months - 2629744 seconds in month
		$days = floor((($till%31556926)%2629744)/86400); // remainder of months into days - 86400 seconds in a day
		$hours = floor(((($till%31556926)%2629744)%86400)/3600); // remainder of days into hours - 3600 seconds in an hour
		$minutes = floor((((($till%31556926)%2629744)%86400)%3600)/60); // remainder of hours into minutes - 60 seconds in a minute
		$seconds = floor((((($till%31556926)%2629744)%86400)%3600)%60); // remainder of minutes, already in seconds so no need to divide
	
	$fnow = date("H:i:s n/j/y", $now); // now in format: hour:minute:second month/day/year
	if (date("Y", $then) != date("Y")) {
		$fthen = date("H:i:s n/j/y", $then); // then in same format
	} else {
		$fthen = date("H:i:s n/j", $then); // then in same format	
	}
	$return = array(
					'years'=>$years,
					'months'=>$months,
					'days'=>$days,
					'hours'=>$hours,
					'minutes'=>$minutes,
					'seconds'=>$seconds,
					'now'=>$fnow,
					'then'=>$fthen,
					'passed'=>$passed
					);
	return $return;
}

function printcountdown($return, $mode=0, $name=0) {
////////////
// Modes:
// 0 (default): Time until My Birthday! - 06:33:00 11/25/05: 6 hours, 28 minutes, and 36 seconds.
// 1: My Birthday! - 06:33:00 11/25/05: 6 hours, 28 minutes, and 36 seconds.
// 2: 6 hours, 28 minutes, and 36 seconds.
// 3: 6 hours, 28 minutes, and 36 seconds
	$togo = '';
	if ($mode==0) {
		$togo .= 'Time ';
		if ($return['passed'] == 0) {
			$togo .= 'until ';
		} else {
			$togo .= 'since ';
		}
	}
	if (($mode!=2)&&($mode!=3)) {
	$togo .= '<strong>';
	if ($name !== 0) $togo .= $name.' - ';
	if (substr($return['then'], 0, 9)=='00:00:00 ') {
		$togo .= substr($return['then'], 9);
	} else {
		$togo .= $return['then'];
	}
	$togo .= '</strong>: ';
	}
	
	if (($return['then']-$return['now'])==0) {
		$timeleft = 0;
		$togo .= 'Now!';
		$todo = $togo;
	} else {
		if ($return['years'] > 0) {
			$togo .= $return['years'].' year';
			if ($return['years'] > 1) $togo .= 's';
			if ($return['months'] > 0) {
				$togo .= ',';
			}
			if (($return['seconds']!=0)||($return['minutes']!=0)||($return['hours']!=0)||($return['months']!=0)) $togo .= ' ';
		}
		
		if ($return['months'] > 0) {
			$togo .= $return['months'].' month';
			if ($return['months'] > 1) $togo .= 's';
			if ($return['days'] > 0) {
				$togo .= ',';
			}
			if (($return['seconds']!=0)||($return['minutes']!=0)||($return['hours']!=0)||($return['days']!=0)) $togo .= ' ';
		}
		
		if ($return['days'] > 0) {
			$togo .= $return['days'].' day';
			if ($return['days'] > 1) $togo .= 's';
			if ($return['hours'] > 0) {
				$togo .= ',';
			}
			if (($return['seconds']!=0)||($return['minutes']!=0)||($return['hours']!=0)) $togo .= ' ';
		}
		
		if ($return['hours'] > 0) {
			$togo .= $return['hours'].' hour';
			if ($return['hours'] > 1) $togo .= 's';
			if ($return['minutes'] > 0) {
				$togo .= ',';
			}
			if (($return['seconds']!=0)||($return['minutes']!=0)) $togo .= ' ';
		}
		
		if ($return['minutes'] > 0) {
			$togo .= $return['minutes'].' minute';
			if ($return['minutes'] > 1) $togo .= 's';
			if ($return['seconds'] > 0) {
				$togo .= ',';
				
			}
			if ($return['seconds']!=0) $togo .= ' ';
		}
		
		if ($return['seconds'] > 0) {
			$togo .= $return['seconds'].' second';
			if ($return['seconds'] > 1) $togo .= 's';
		}
		
		$expld = explode(', ', $togo);
		# EXAMPLE:
		# 0 => 5 years
		# 1 => 5 months
		# 2 => 5 days
		# 3 => 5 hours
		# 4 => 5 minutes
		# 5 => 5 seconds
		$exlast = count($expld)-1;
		$todo = '';
		foreach ($expld as $num => $value) {
			$todo .= $value;
			if (($num!=$exlast) && (count($expld)!=2)) $todo .= ', ';
			if ((count($expld)==2) && ($num!=$exlast)) $todo .= ' ';
			if ($num==($exlast-1)) $todo .= 'and ';
			if (($num==$exlast) && ($return['passed'] == 1)) $todo .= ' ago';
			if (($num==$exlast) && ($mode!=3)) $todo .= '.';
		}
	}
	//if ($return['passed']==1) $todo .= 'It\'s passed!';
	return $todo;
}
?>