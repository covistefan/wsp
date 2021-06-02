<?php
/**
 * logout
 * @author stefan@covi.de
 * @since 3.1
 * @version 7.0
 * @lastchange 2019-03-08
 */

session_start();
require ("./data/include/usestat.inc.php");
require ("./data/include/globalvars.inc.php");
if (isset($_SESSION['wspvars']['usevar']) && trim($_SESSION['wspvars']['usevar'])!=''):
	doSQL("DELETE FROM `security` WHERE `usevar` = '".escapeSQL($_SESSION['wspvars']['usevar'])."'");
endif;
// destroy session and redirect
session_destroy();
session_regenerate_id(false);
header("location: ./login.php?logout");
?>