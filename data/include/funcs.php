<?php
/**
*
* @author stefan@covi.de
* @since 3.1
* @version 7.0
* @lastchange 2007-08-30
*/

if (!(__FUNCS__ === true)) {
	define(__FUNCS__, true);

	/**
	 * pruefung, ob variable als POST oder GET uebergeben
	 * wurde, ansonsten belegung mit standardwert
	 */
	function checkParamVar($var, $standard, $checkcookie = false) {
		if ($checkcookie && isset($_COOKIE[$var])) {
			$param = $_COOKIE[$var];
		}
		else if (isset($_POST[$var])) {
			$param = $_POST[$var];
		}
		elseif (isset($_GET[$var])) {
			$param = $_GET[$var];
		}
		else {
			$param = $standard;
		}	// if

		return $param;
	}	// checkParamVar()

	/**
	* Unlaute, Sonderzeichen, Leerzeichen aus Dateinamen entfernen
	*/
	function removeSpecialChar($filename) {
		$filename = str_replace(chr(228), 'ae', $filename);
		$filename = str_replace(chr(196), 'ae', $filename);
		$filename = str_replace(chr(246), 'oe', $filename);
		$filename = str_replace(chr(214), 'oe', $filename);
		$filename = str_replace(chr(252), 'ue', $filename);
		$filename = str_replace(chr(220), 'ue', $filename);
		$filename = str_replace(chr(223), 'ss', $filename);
		$filename = str_replace(' ', '_', $filename);
		$filename = str_replace('\\', '', $filename);
		$filename = str_replace('/', '', $filename);
		$filename = str_replace('(', '_', $filename);
		$filename = str_replace(')', '_', $filename);

		$allowed_in_file = "[^a-zA-Z0-9_.]";
		$filename = preg_replace("/$allowed_in_file/", "_", $filename);

		return strtolower($filename);
	}	// removeSpecialChar()

	/**
	* MySQL-Fehler ausgeben
	*/
	function writeMySQLError($sql="") {
		$errstr = "<br /><span style=\"color:#ff0000; font-weight:bold;\">MySQL-Fehler ".mysql_errno().":</span> ";
		$errstr .= "<span style=\"color:#ff0000;\">".mysql_error()."<br />$sql</span><br /><br />\n";
		$backtrace = debug_backtrace();
		$errstr .= "<span style=\"color:#ff0000;\">\n";
		foreach ($backtrace as $trace) {
			$errstr .= "Fehler in Zeile ".$trace['line']." in Datei ".$trace['file']."<br />\n";
		}	// foreach
		$errstr .= "</span>\n";
		return $errstr;
	}	// writeMySQLError()

	/**
	* Pfad f�r ver�ffentlichte Seite erstellen
	*/
	function createPath($mid) {
		$path = '';

		$sql = "SELECT `connected` FROM `menu` WHERE `mid`=$mid";
		$rs = mysql_query($sql) or die(writeMySQLError($sql));

		$mid = mysql_db_name($rs, 0, 'connected');
		$sql = "SELECT `filename`, `connected` FROM `menu` WHERE `mid`=".$mid;
		$rs = mysql_query($sql) or die(writeMySQLError($sql));

		if (mysql_num_rows($rs) > 0) {
			$path = '/' . mysql_db_name($rs, 0, 'filename');
			$path = createPath($mid).$path;
		}	// if

		return $path;
	}	// createPath()

	/**
	* GET/POST-String f�r Sessionvar zur�chgeben, wenn Sessionvar genutzt wird
	* und Cookie nicht gesetzt wurde
	*/
	function getSessionVar($get = true) {
		global $sessionvar;

		$svar = '';

		if ($get) {
			$svar = "sessionvar=$sessionvar";
		}
		else {
			$svar = $sessionvar;
		}	// if

		return $svar;
	}	// getSessionVar()

	/**
	* ID-Liste zum vorherigen BreakTree ermitteln
	*/
	function getIDBTList($mid) {
		$aIDs = array();

		$sql = "SELECT `connected`, `breaktree` FROM `end_menu` WHERE `mid`=$mid";
		$rsBT = mysql_query($sql) or die(writeMySQLError($sql));
		if (mysql_num_rows($rsBT) > 0) {
			$breaktree = mysql_db_name($rsBT, 0, 'breaktree');
			if (($breaktree != 1) && ($mid > 0)) {
				$aIDs = getIDBTList(mysql_db_name($rsBT, 0, 'connected'));
			}	// if
		}	// if
		array_unshift($aIDs, $mid);

		return $aIDs;
	}	// getBreakTreeID()

	/**
	 * entsprechend den �bergebenen Parameter Stylesheet schreiben oder ignorieren
	 * @param $css	String	css-File
	 * @param $media	String	Medien-Type
	 * @param $browser	String	Browser
	 */
	function getStylesheet($css, $media, $browser) {
		if (($browser=='all') || ($browser==$GLOBALS['usedbrowser'])) {
			$buf = '<link rel="stylesheet" ';
			if ($media=='print') {
				$buf .= 'media="print" ';
			}
			else if ($media=='handheld') {
				$buf .= 'media="handheld" ';
			}	// if
			else if ($media!='all') {
				$buf .= 'media="screen" ';
			}	// if
			$buf .= 'href="'.$css.'" type="text/css" />';
			$buf .= "\n";
		}	// if

		return $buf;
	}	// getStylesheet()

	/**
	 * pfad zu gegebener mid ausgeben
	 */
	function returnPath($mid, $basepath, $depth) {
		//
		// depth 0 => rueckgabe des pfades bis hin zum hoeheren verzeichnis
		// depth 1 => rueckgabe des pfades bis hin zum verzeichnis
		// depth 2 => rueckgabe des pfades bis hin zur datei
		//
		$path_sql = "SELECT `connected`, `filename` FROM `menu` WHERE `mid` = ".$mid;
		$path_res = mysql_query($path_sql) or die(writeMySQLError($path_sql));
		if (mysql_num_rows($path_res)!=0):
			$parent = mysql_result($path_res, 0, "connected");
			$fullpath = "";

			while (true):
				$fullpath = mysql_result($path_res, 0, "filename")."/".$fullpath;
				if ($parent==0):
					break;
				endif;
				$path_sql = "SELECT `connected`, `filename` FROM `menu` WHERE `mid` = ".$parent;
				$path_res = mysql_query($path_sql) or die(writeMySQLError($path_sql));
				$parent = mysql_result($path_res, 0, 'connected');
			endwhile;

			if ($depth==1):
				$givebackpath = $basepath."/".$fullpath;
			elseif ($depth==2):
				$fullpath = substr($fullpath, 0, strlen($fullpath)-1);
				$givebackpath = $basepath."/".$fullpath.".php";
			elseif ($depth==0):
				$fullpath = substr($fullpath, 0, strlen($fullpath)-1);
				$fullpath = substr($fullpath, 0, strrpos($fullpath, "/"));
				$givebackpath = $basepath."/".$fullpath;
			endif;
		else:
			$givebackpath = "/";
		endif;

		return $givebackpath;
	}	// returnPath()
}	// if
?>