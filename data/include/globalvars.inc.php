<?php
/**
 * global WSP variables 
 * @author stefan@covi.de
 * @since 3.1
 * @version 7.0
 * @lastchange 2019-03-08
 */

if (phpversion()>5): date_default_timezone_set('Europe/Berlin'); endif;

if (isset($_REQUEST['night']) && $_REQUEST['night']=='off') { $_SESSION['wspvars']['daily'] = true; }

if (isset($_SESSION) && array_key_exists('usevar', $_SESSION) && trim($_SESSION['usevar'])!=''):
	$usevar = trim($_SESSION['usevar']);
elseif (isset($_COOKIE) && array_key_exists('usevar', $_COOKIE) && trim($_COOKIE['usevar'])!=''):
	$usevar = trim($_COOKIE['usevar']);
elseif (isset($_POST) && array_key_exists('usevar', $_POST) && trim($_POST['usevar'])!=''):
	$usevar = trim($_POST['usevar']);
elseif (isset($_GET) && array_key_exists('usevar', $_GET) && trim($_GET['usevar'])!=''):
	$usevar = trim($_GET['usevar']);
else:
	$usevar = '';
endif;

/* handle fake-subdomains used (for EXAMPLE) at strato hosting services */
$buildfilename = str_replace("//", "/", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/".$_SERVER['SCRIPT_NAME']));
if ($buildfilename!=$_SERVER['SCRIPT_FILENAME']):
	$tmpwspbaseadd = str_replace($_SERVER['DOCUMENT_ROOT'], "", $_SERVER['SCRIPT_FILENAME']);
	$tmpwspbaseadd = str_replace($_SERVER['SCRIPT_NAME'], "", $tmpwspbaseadd);
	define('DOCUMENT_ROOT', str_replace("//", "/", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/".$tmpwspbaseadd."/")));
else:
	define('DOCUMENT_ROOT', str_replace("//", "/", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/")));
endif;

if (is_file(str_replace("//", "/", __DIR__."/wspconf.inc.php"))): 
	include(str_replace("//", "/", __DIR__."/wspconf.inc.php"));
	// set wsp basedir if not set
	// temp solution -> detection required
	if (!(defined('WSP_DIR'))): define('WSP_DIR', $_SERVER['SCRIPT_URL']); endif;
else:
	if (!(defined('WSP_DIR'))): 
        define('WSP_DIR', $_SERVER['SCRIPT_URL']);
    endif;
endif;
if (is_file(str_replace("//", "/", __DIR__."/wsplang.inc.php"))): 
    include(str_replace("//", "/", __DIR__."/wsplang.inc.php")); 
endif;
if (is_file(str_replace("//", "/", __DIR__."/wspbase.inc.php"))): 
	include(str_replace("//", "/", __DIR__."/wspbase.inc.php")); 
endif;

// check db connection
// set host if not set
if (!(defined('DB_HOST'))): define('DB_HOST', 'localhost'); endif;
// create new mysql-connection
if (defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    $_SESSION['wspvars']['db'] = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
}
// check ftp connection
// set host if not set
if (!(defined('FTP_HOST'))): define('FTP_HOST', 'localhost'); endif;
// set ftp port if not set
if (!(defined('FTP_PORT'))): define('FTP_PORT', 21); endif;

// getting function file
if (is_file(str_replace("//", "/", __DIR__."/funcs.inc.php"))): 
	include_once(str_replace("//", "/", __DIR__."/funcs.inc.php"));
endif;

?>