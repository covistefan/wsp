<?php
/**
* @author s.haendler@covi.de
* @copyright (c) 2008, Common Visions Media.Agentur (COVI)
* @since 3.1
* @version 3.4
* @lastchange 2008-08-06
*/

// Session nutzen?
$wspvars['usesessionvar'] = false;
// alternativ Cookies nutzen, wenn der Client dies zulaesst?
$wspvars['usecookies'] = false;
// Userverhalten tracken?
$wspvars['trackuser'] = false;

if (is_file($_SERVER['DOCUMENT_ROOT'].'/data/include/switch.inc.php')):
	include $_SERVER['DOCUMENT_ROOT'].'/data/include/switch.inc.php';
endif;

// DB-Verbindung initiieren
if (is_file($_SERVER['DOCUMENT_ROOT'].'/data/include/getdb.php')):
	include $_SERVER['DOCUMENT_ROOT'].'/data/include/getdb.php';
elseif (is_file($_SERVER['DOCUMENT_ROOT'].'/data/include/dbaccess.inc.php')):
	include $_SERVER['DOCUMENT_ROOT'].'/data/include/dbaccess.inc.php';
else:
	echo "<!-- Fehler beim Zugriff auf die Datenbank. Es koennen keine dynamischen Inhalte abgebildet werden. -->";
endif;
/// site visitor tracking
include $_SERVER['DOCUMENT_ROOT'].'/data/include/tracking.php';
include $_SERVER['DOCUMENT_ROOT'].'/data/include/browserdetection.php';
include $_SERVER['DOCUMENT_ROOT'].'/data/include/checksession.php';
include $_SERVER['DOCUMENT_ROOT'].'/data/include/checkuser.php';
?>