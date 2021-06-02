<?php
/**
* @author COVI
* @copyright (c) 2007, Common Visions Media.Agentur (COVI)
* @since 3.1
* @version 3.2.3
* @lastchange 2007-08-10
*/

/**
* checks using an intern list, if referrer is a robot
* @return isRobot => true, if client is a robot from list
*/
function isRobot() {
	$isRobot = false;

	$aRobots = array();

	$aRobots[] = 'AbachoBOT';
	$aRobots[] = 'Acoon Robot';
	$aRobots[] = 'Scooter';
	$aRobots[] = 'Mercator';
	$aRobots[] = 'Aladin';
	$aRobots[] = 'Freecrawl';
	$aRobots[] = 'Eule';
	$aRobots[] = 'KIT-Fireball';
	$aRobots[] = 'Googlebot';
	$aRobots[] = 'BackRub';
	$aRobots[] = 'Slurp';
	$aRobots[] = 'Lycos_Spider';
	$aRobots[] = 'Tarantula';
	$aRobots[] = 'Gulliver';
	$aRobots[] = 'FAST-WebCrawler';
	$aRobots[] = 'scoutmaster';
	$aRobots[] = 'Infoseek Sidewinder';
	$aRobots[] = 'ADB_Web';
	$aRobots[] = 'msnbot';

	foreach ($aRobots as $robot) {
		if (strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, strlen($robot))) == strtolower($robot)) {
			$isRobot = true;
			break;
		}	// if
	}	// foreach

	return $isRobot;
}	// isRobot
?>