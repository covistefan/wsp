<?php
/**
* @author s.haendler@covi.de
* @copyright (c) 2008, Common Visions Media.Agentur (COVI)
* @since 3.1
* @version 3.3.2
* @lastchange 2009-01-07
*/

// Tracking des Dateiaufrufs
// deaktiviert zugunsten der google analytics anbindung und
// auf grund der hohen datenbanklast

/*

$visitor = array();
$acttime = date("YmdHis");
$agent = split("; ",$_SERVER['HTTP_USER_AGENT']);

foreach ($agent AS $key => $value):
	if (is_array($value)===false):
		if ($browser==""):
			if (eregi("MSIE 8",$value)):
				$browser = "Internet Explorer 8.x";
			elseif (eregi("MSIE 7",$value)):
				$browser = "Internet Explorer 7.x";
			elseif (eregi("MSIE 6",$value)):
				$browser = "Internet Explorer 6.x";
			elseif (eregi("MSIE 5",$value)):
				$browser = "Internet Explorer 5.x";
			elseif (eregi("MSIE",$value)):
				$browser = "Internet Explorer";
			elseif (eregi("Camino",$value)):
				$browser = "Camino";
			elseif (eregi("iCab",$value)):
				$browser = "iCab";
			elseif (eregi("Shiira",$value)):
				$browser = "Shiira";
			elseif (eregi("Konqueror",$value)):
				$browser = "Konqueror";
			elseif (eregi("Navigator",$value)):
				$browser = "Netscape";
			elseif (eregi("Firefox/3",$value)):
				$browser = "Firefox 3.x";
			elseif (eregi("Firefox/2",$value)):
				$browser = "Firefox 2.x";
			elseif (eregi("Firefox/1",$value)):
				$browser = "Firefox 1.x";
			elseif (eregi("Firefox",$value)):
				$browser = "Firefox";
			elseif (eregi("Opera",$value)):
				$browser = "Opera";
			elseif (eregi("Safari",$value)):
				$browser = "Safari";
			elseif (eregi("Gecko/200",$value)):
				$browser = "Mozilla";
			elseif (eregi("slurp",$value) || eregi("shelob",$value) || eregi("gonzo",$value) || eregi("crawler",$value) || eregi("bot",$value)):
				$browser = "Suchmaschine";
			endif;
		endif;
		
		if ($system==""):
			if (eregi("Windows NT 6",$value)):
				$system = "Windows Vista";
			elseif (eregi("Windows NT 5.1",$value)):
				$system = "Windows XP";
			elseif (eregi("Windows NT 5.0",$value)):
				$system = "Windows 2000";
			elseif (eregi("Windows NT 4.0",$value)):
				$system = "Windows NT";
			elseif (eregi("Windows 98",$value)):
				$system = "Windows 98";
			elseif (eregi("Windows 95",$value)):
				$system = "Windows 95";
			elseif (eregi("Linux",$value)):
				$system = "Linux";
			elseif (eregi("mac",$value)):
				$system = "Mac OS";
			elseif (eregi("iphone",$value)):
				$system = "iPhone";
			elseif (eregi("slurp",$value) || eregi("shelob",$value) || eregi("gonzo",$value) || eregi("crawler",$value) || eregi("bot",$value)):
				$system = "Suchmaschine";
			endif;
		endif;
	endif;
endforeach;

if ($browser==""):
	$browser = $_SERVER['HTTP_USER_AGENT'];
endif;
if ($system==""):
	$system = $_SERVER['HTTP_USER_AGENT'];
endif;

function getRefererURL($referer) {
	$rHost = explode('://', $referer);
	$rHost = explode('/', $rHost[1]);
	return $rHost[0];
	}
	
function getRefererKeys($referer) {
	$rKey = explode('?q=', $referer);
	if (count($rKey)<2):
		$rKey = explode('&q=', $referer);
	endif;
	$rKey = explode('&', $rKey[1]);
	$rKey = explode('+', $rKey[0]);
	sort($rKey);
	return implode("+",$rKey);
	}

$visitor[$_SERVER["REMOTE_ADDR"]][$acttime]['system'] = $system;
$visitor[$_SERVER["REMOTE_ADDR"]][$acttime]['browser'] = $browser;
$visitor[$_SERVER["REMOTE_ADDR"]][$acttime]['referrer'] = getRefererURL($_SERVER["HTTP_REFERER"]);

$trackfile_sql = "SELECT * FROM `tracking_files` WHERE `file` = '".$_SERVER['PHP_SELF']."' AND `track` = '".date("Ymd")."'";
$trackfile_res = @mysql_query($trackfile_sql);
$trackfile_num = @mysql_num_rows($trackfile_res);

if ($trackfile_num == 0):
	$sql = "INSERT INTO `tracking_files` SET `file` = '".$_SERVER['PHP_SELF']."', `mid` = '".$wspvars['mid']."', `hits` = '1', `visits` = '".addslashes(serialize($visitor))."', `track` = '".date("Ymd")."'";
	@mysql_query($sql);
elseif ($system!="Suchmaschine" && $browser!="Suchmaschine"):
	$visits = unserialize(mysql_result($trackfile_res, 0, "visits"));
	$newvisit = false;
	if (!(array_key_exists($_SERVER["REMOTE_ADDR"], $visits))):
		$visits = array_merge ($visits, $visitor);
	else:
		$timekeys = count($visits[$_SERVER["REMOTE_ADDR"]]);
		foreach ($visits[$_SERVER["REMOTE_ADDR"]] AS $key => $value):
			if ($tk==($timekeys-1)):
				if ($key<(date("YmdHis", mktime()-1800))):
					$visits[$_SERVER["REMOTE_ADDR"]] = array_merge ($visits[$_SERVER["REMOTE_ADDR"]], $visitor[$_SERVER["REMOTE_ADDR"]]);
				elseif ($visits[$_SERVER["REMOTE_ADDR"]][$key]['system']!=$system):
					$visits[$_SERVER["REMOTE_ADDR"]] = array_merge ($visits[$_SERVER["REMOTE_ADDR"]], $visitor[$_SERVER["REMOTE_ADDR"]]);
				elseif ($visits[$_SERVER["REMOTE_ADDR"]][$key]['browser']!=$browser):
					$visits[$_SERVER["REMOTE_ADDR"]] = array_merge ($visits[$_SERVER["REMOTE_ADDR"]], $visitor[$_SERVER["REMOTE_ADDR"]]);
				endif;
			endif;
			$tk++;
		endforeach;
	endif;
	
	$sql = "UPDATE `tracking_files` SET `hits` = `hits`+1, `visits` = '".serialize($visits)."' WHERE `file` = '".$_SERVER['PHP_SELF']."' AND `track` = '".date("Ymd")."'";
	@mysql_query($sql);
endif;

$trackinfo_sql = "SELECT * FROM `tracking_info` WHERE `track` = '".date("Ymd")."'";
$trackinfo_res = @mysql_query($trackinfo_sql);
$trackinfo_num = @mysql_num_rows($trackinfo_res);

if ($trackinfo_num==0):
	$system_value = array($system => 1);
	$browser_value = array($browser => 1);
	if (getRefererURL($_SERVER["HTTP_REFERER"])!=""):
		$referer_value = array(getRefererURL($_SERVER["HTTP_REFERER"]) => 1);
	endif;
	if (getRefererKeys($_SERVER["HTTP_REFERER"])!=""):
		$keywords_value = array(getRefererKeys($_SERVER["HTTP_REFERER"]) => 1);
	endif;
	
	$sql = "INSERT INTO `tracking_info` SET `system` = '".serialize($system_value)."', `browser` = '".serialize($browser_value)."', `referer` = '".serialize($referer_value)."', `keywords` = '".serialize($keywords_value)."', `track` = '".date("Ymd")."'";
	@mysql_query($sql);
else:
	$system_value = unserialize(mysql_result($trackinfo_res, 0, "system"));
	$browser_value = unserialize(mysql_result($trackinfo_res, 0, "browser"));
	$referer_value = unserialize(mysql_result($trackinfo_res, 0, "referer"));
	$keywords_value = unserialize(mysql_result($trackinfo_res, 0, "keywords"));
	
	if (key_exists($system, $system_value)):
		$system_value[$system] = $system_value[$system]+1;
	else:
		$system_value[$system] = 1;
	endif;
	
	if (key_exists($browser, $browser_value)):
		$browser_value[$browser] = $browser_value[$browser]+1;
	else:
		$browser_value[$browser] = 1;
	endif;
	
	if (getRefererURL($_SERVER["HTTP_REFERER"])!=""):
		if (is_array($referer_value)):
			if (key_exists(getRefererURL($_SERVER["HTTP_REFERER"]), $referer_value)):
				$referer_value[getRefererURL($_SERVER["HTTP_REFERER"])] = $referer_value[getRefererURL($_SERVER["HTTP_REFERER"])]+1;
			else:
				$referer_value[getRefererURL($_SERVER["HTTP_REFERER"])] = 1;
			endif;
		else:
			$referer_value = array();
			$referer_value[getRefererURL($_SERVER["HTTP_REFERER"])] = 1;
		endif;
	endif;
	
	if (getRefererKeys($_SERVER["HTTP_REFERER"])!=""):
		if (is_array($keywords_value)):
			if (key_exists(getRefererKeys($_SERVER["HTTP_REFERER"]), $keywords_value)):
				$keywords_value[getRefererKeys($_SERVER["HTTP_REFERER"])] = $keywords_value[getRefererKeys($_SERVER["HTTP_REFERER"])]+1;
			else:
				$keywords_value[getRefererKeys($_SERVER["HTTP_REFERER"])] = 1;
			endif;
		else:
			$keywords_value = array();
			$keywords_value[getRefererKeys($_SERVER["HTTP_REFERER"])] = 1;
		endif;
	endif;
	
	$sql = "UPDATE `tracking_info` SET `system` = '".serialize($system_value)."', `browser` = '".serialize($browser_value)."', `referer` = '".serialize($referer_value)."', `keywords` = '".serialize($keywords_value)."' WHERE `track` = '".date("Ymd")."'";
	@mysql_query($sql);
endif;

*/

?>