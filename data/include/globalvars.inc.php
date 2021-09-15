<?php
/**
 * global WSP variables 
 * @author stefan@covi.de
 * @since 3.1
 * @version 7.0
 * @lastchange 2021-09-15
 */

@header_remove('X-Powered-By');
@header('X-Content-Type-Options: nosniff');
@header('X-Frame-Options: ALLOWALL');
@header('Referrer-Policy: strict-origin-when-cross-origin');
@header('Content-Security-Policy: default-src * \'unsafe-inline\';');
@header('Permissions-Policy: accelerometer=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');

if (phpversion()>5): date_default_timezone_set('Europe/Berlin'); endif;

if (isset($_REQUEST['night']) && $_REQUEST['night']=='off') { $_SESSION['wspvars']['daily'] = true; }

// handle fake-subdomains used (for EXAMPLE) at strato hosting services
$buildfilename = str_replace("//", "/", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/".$_SERVER['SCRIPT_NAME']));
if ($buildfilename!=$_SERVER['SCRIPT_FILENAME']) {
	$tmpwspbaseadd = str_replace($_SERVER['DOCUMENT_ROOT'], "", $_SERVER['SCRIPT_FILENAME']);
	$tmpwspbaseadd = str_replace($_SERVER['SCRIPT_NAME'], "", $tmpwspbaseadd);
	define('DOCUMENT_ROOT', str_replace("//", "/", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/".$tmpwspbaseadd."/")));
} else {
	define('DOCUMENT_ROOT', str_replace("//", "/", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/")));
}

if (is_file(str_replace("//", "/", __DIR__."/wspconf.inc.php"))) {
	include(str_replace("//", "/", __DIR__."/wspconf.inc.php"));
	// set wsp basedir if not set
	// temp solution -> detection required
	if (!(defined('WSP_DIR'))) {
		define('WSP_DIR', str_replace("//", "/", str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF'])));
	}
} else {
	// move to setup
	if (!(defined('WSP_DIR'))) {
        define('WSP_DIR', str_replace("//", "/", str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF'])));
    }
	header("location: ".str_replace("//", "/", str_replace("//", "/", "/".WSP_DIR."/wspsetup.php")));
	die();
}

// get language
if (is_file(str_replace("//", "/", __DIR__."/wsplang.inc.php"))) { 
    include(str_replace("//", "/", __DIR__."/wsplang.inc.php")); 
}
// get base informations
if (is_file(str_replace("//", "/", __DIR__."/wspbase.inc.php"))) { 
	include(str_replace("//", "/", __DIR__."/wspbase.inc.php")); 
}

// check db connection
// set host if not set
if (!(defined('DB_HOST'))) define('DB_HOST', 'localhost');
if (!(defined('DB_PORT'))) define('DB_PORT', intval(ini_get("mysqli.default_port")));
// create new mysql-connection
if (defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
	$_SESSION['wspvars']['db'] = @new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,intval(DB_PORT));
	if (@mysqli_ping($_SESSION['wspvars']['db'])==false) {
		header("location: ".str_replace("//", "/", str_replace("//", "/", "/".WSP_DIR."/wspsetup.php?error=nodatabase")));
		die();
	}
}

// check some ftp data
// set host if not set
if (!(defined('FTP_HOST'))) define('FTP_HOST', 'localhost');
// set ftp port to base port if not set
if (!(defined('FTP_PORT'))) define('FTP_PORT', 21);

// getting function file
if (is_file(str_replace("//", "/", __DIR__."/funcs.inc.php"))) {
	include_once(str_replace("//", "/", __DIR__."/funcs.inc.php"));
}

?>