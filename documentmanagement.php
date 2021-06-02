<?php
/**
 * Verwaltung von Dokumenten
 * @author stefan@covi.de
 * @since 3.1
 * @version 6.8
 * @lastchange 2019-01-20
 */

/* start session ----------------------------- */
session_start();
/* base includes ----------------------------- */
require ("data/include/usestat.inc.php");
require ("data/include/globalvars.inc.php");
/* first includes ---------------------------- */
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/wsplang.inc.php");
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/dbaccess.inc.php");
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/ftpaccess.inc.php");
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/funcs.inc.php");
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/filesystemfuncs.inc.php");
/* checkParamVar ----------------------------- */
$op = checkParamVar('op', '');
$extern = checkParamVar('extern', 0);
/* define actual system position ------------- */
$_SESSION['wspvars']['mgroup'] = 6;
$_SESSION['wspvars']['lockstat'] = 'documents';
$_SESSION['wspvars']['fpos'] = $_SERVER['PHP_SELF'];
$_SESSION['wspvars']['fposcheck'] = false;
$_SESSION['wspvars']['menuposition'] = 'download'; // ?? is dieser Eintrag richtig?
/* second includes --------------------------- */
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/checkuser.inc.php");
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/errorhandler.inc.php");
require ($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/siteinfo.inc.php");
/* define page specific vars ----------------- */
$mediafolder = "download";
$mediadesc = "Dateien";
// define upload based vars
$_SESSION['wspvars']['upload']['basetarget'] = 'download';
$_SESSION['wspvars']['upload']['extensions'] = ''; // leer, da jegliche Dateien hochgeladen werden können
$_SESSION['wspvars']['upload']['scale'] = false;
$_SESSION['wspvars']['upload']['thumbs'] = true;
$_SESSION['wspvars']['upload']['preview'] = true;

// define required folders to handle that page
$requiredstructure = array("/media","/media/download","/media/download/preview","/media/download/thumbs");

include ("filemanagement.php");

// EOF ?>