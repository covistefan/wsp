<?php
/**
 * showpreview
 * @author stefan@covi.de
 * @since 3.3
 * @version 6.8
 * @lastchange 2019-01-22
 */

/* start session ----------------------------- */
session_start();
/* base includes ----------------------------- */
require ("./data/include/usestat.inc.php");
require ("./data/include/globalvars.inc.php");
/* first includes ---------------------------- */
require ("./data/include/wsplang.inc.php");
require ("./data/include/dbaccess.inc.php");
require ("./data/include/ftpaccess.inc.php");
require ("./data/include/funcs.inc.php");
/* checkParamVar ----------------------------- */

/* define actual system position ------------- */

// second includes --------------------------------
require ("./data/include/checkuser.inc.php");
require ("./data/include/errorhandler.inc.php");
require ("./data/include/siteinfo.inc.php");
// page specific includes, e.g. parser files ------
require ("./data/include/menuparser.inc.php");
require ("./data/include/fileparser.inc.php");
require ("./data/include/clsinterpreter.inc.php");
// head der datei

$_SESSION['parsetime'] = microtime();

if (isset($_REQUEST['previewid'])):
	$previewid = intval($_REQUEST['previewid']);
	$_SESSION['preview'] = true;
else:
	$previewid = 0;
	$_SESSION['preview'] = false;
endif;
if (isset($_REQUEST['previewlang'])):
	$previewlang = trim($_REQUEST['previewlang']);
	$_SESSION['previewlang'] = $previewlang;
else:
	$previewlang = 'de';
	$_SESSION['previewlang'] = 'de';
endif;

publishSites($previewid, 'preview', $previewlang);
if (!(@readfile($_SERVER['REQUEST_SCHEME']."://".$_SESSION['wspvars']['workspaceurl']."/".$_SESSION['wspvars']['wspbasedir']."/tmp/".$_SESSION['wspvars']['usevar']."/previewfile.php"))):
	@readfile($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/tmp/".$_SESSION['wspvars']['usevar']."/previewfile.php");
endif;

$_SESSION['preview'] = false;

echo "<div style=\"position: fixed; bottom: 0px; left: 0px; width: 99%; background: rgba(200,200,200,0.9); color: #000; font-size: 10px; padding: 5px 0.5%; font-family: 'Open Sans', sans-serif;\"><span style=\"margin: 5px;\">Parserzeit: ".(microtime()-$_SESSION['parsetime'])." Sekunden - returnInterpreterPath(): ".returnInterpreterPath($previewid, $previewlang)." ";
if (isset($_SESSION['previewforward']) && trim($_SESSION['previewforward'])!=''): echo " » ".trim($_SESSION['previewforward']); endif;
echo "</span></div>";

// EOF ?>