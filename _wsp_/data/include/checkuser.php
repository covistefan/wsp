<?php
/**
*
* @version 7.0
* @lastchange 2007-08-10
*/

include_once $_SERVER['DOCUMENT_ROOT']."/data/include/funcs.php";
include_once $_SERVER['DOCUMENT_ROOT']."/data/include/robots.php";

$isRobot = isRobot();
$isCookie = false;
if ($isRobot) {
	$sessionvar = '';
}
else if (isset($_COOKIE['sessionvar']) && ($wspvars['usecookies'])) {
	$sessionvar = $_COOKIE['sessionvar'];
	$isCookie = true;
}
else if (isset($_POST['sessionvar'])) {
	$sessionvar = $_POST['sessionvar'];
}
elseif (isset($_GET['sessionvar'])) {
	$sessionvar = $_GET['sessionvar'];
}
else {
	$sessionvar = '';
}	// if

// Ist der User bereits eingeloggt
//$user1_sql = "SELECT * FROM `usercontrol` WHERE `sessionid`='" . session_id() . "'";

$islogin = false;
if(isset($_GET['logout'])):
	mysql_query("UPDATE `usercontrol` SET `sessionid`='' WHERE `sessionid`='" . session_id() . "'");
endif;
$user1_num = mysql_num_rows(mysql_query("SELECT * FROM `usercontrol` WHERE `sessionid`='" . session_id() . "' AND `sessionid`!=''"));
if($user1_num==1):
	$islogin=true;
else:
	// Safearea Userabfrage und setzen des Login-Status
	if(trim($_POST['username'])!='' && trim($_POST['userpassword'])!=''):
		$user2_sql = "SELECT * FROM `usercontrol` WHERE `username`='" . trim($_POST['username']) . "' AND `password`='" . md5(trim($_POST['userpassword'])) . "'";
		$user2_res = mysql_query($user2_sql);
		$user2_num = mysql_num_rows($user2_res);
		if($user2_num==1):
			mysql_query("UPDATE `usercontrol` SET `sessionid`='" . session_id() . "' WHERE `id`='" . mysql_result($user2_res, 0, "id") . "'");
			$islogin=true;
			if(mysql_result($user2_res, 0, "cidvalue")!=''):
				$allowContents = unserialize(mysql_result($user2_res, 0, "cidvalue"));
			else:
				$allowContents = array();
			endif;
		endif;
	endif;
endif;

if ((!$isRobot) && ($wspvars['usesessionvar'])) {
	// Sessionvar nur bilden, wenn kein Robot zugreift !!!

	/*
	$accesstime = time();
	$sql = "SELECT COUNT(ts.`id`)
				FROM `tracking_user` tu, `tracking_site` ts
				WHERE tu.`sessionvar`='$sessionvar'
					&& ts.`accesstime`>=($accesstime-3)
					&& tu.`id`=ts.`tracking_user_id`";
	$rs = mysql_query($sql);
	if (mysql_db_name($rs, 0, 0) == 0) {
		$sql = "SELECT `id` FROM `tracking_user` WHERE `sessionvar`='$sessionvar'";
		$rs  = mysql_query($sql);
		$cnt = mysql_num_rows($rs);

		if ($cnt==1) {
			$id  = mysql_db_name($rs, 0, 'id');
			$sql = "INSERT INTO `tracking_site`
						(`tracking_user_id`, `page`, `toursets_id`, `tour_kategorie_id`, `accesstime`)
						VALUES($id, '".$_SERVER['PHP_SELF']."', ";
			if (isset($tsid) || isset($cid)) {
				if (isset($tsid)) {
					$sql .= "$tsid, ";
				}
				else {
					$sql .= "0, ";
				}	// if
				if (isset($cid)) {
					$sql .= "$cid";
				}
				else {
					$sql .= "0";
				}	// if
			}
			else {
				$sql .= "0, 0";
			}	// if
			$sql .= ", $accesstime)";
			mysql_query($sql) or die(writeMySQLError($sql));
			if (isset($_GET['lang'])) {
				$sql = "UPDATE `tracking_user`
							SET `language`='".$_GET['lang']."'
							WHERE `id`='$id'";
				mysql_query($sql) or die(writeMySQLError($sql));
			}	// if
		}
		else {
			$starttime  = time();
			$referer    = $_SERVER['HTTP_REFERER'];
			$remoteip   = $_SERVER['REMOTE_ADDR'];
			$useragent  = $_SERVER['HTTP_USER_AGENT'];
			$servername = $_SERVER['SERVER_NAME'];
			if (isset($_GET['language'])) {
				$language = $_GET['language'];
			}
			else {
				$language = "de";
			}	// if
			if (isset($_GET['aid'])) {
				$agencyid = intval($_GET['aid']);
			}
			else {
				$agencyid = 0;
			}	// if
			$sessionvar = md5($starttime.$referer.$remoteip.$useragent.$servername);

			$sql = "INSERT INTO `tracking_user`
						(`sessionvar`, `starttime`, `referer`, `remoteip`, `useragent`, `server_name`, `language`, `agency_id`)
						VALUES('$sessionvar', $starttime, '$referer', '$remoteip', '$useragent', '$servername', '$language', $agencyid)";
			mysql_query($sql) or die(writeMySQLError($sql));

	//		die(urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?sessionvar=$sessionvar&".$_SERVER['QUERY_STRING']."&lang=$language"));
			setcookie('sessionvar', $sessionvar);
//			header("Location: /data/include/checkcookie.php?sessionvar=$sessionvar&uri=".urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])."&param=".urlencode($_SERVER['QUERY_STRING']."&lang=$language"));
			header("Location: /data/include/checkcookie.php?sessionvar=$sessionvar&uri=".urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])."&param=".urlencode($_SERVER['QUERY_STRING']));
			die();
//			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?sessionvar=$sessionvar&".$_SERVER['QUERY_STRING']."&lang=$language");
		}	// if
	}	// if*/
}	// if
?>