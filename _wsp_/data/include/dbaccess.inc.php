<?php
/**
 * db-access
 * @since 3.1
 * @version 3.3
 * @lastchange 2009-08-18
 */

DEFINE('DB_HOST', '*');
DEFINE('DB_NAME', '*');
DEFINE('DB_USER', '*');
DEFINE('DB_PASS', '*');

$_SESSION['wspvars']['db'] = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
$_SESSION['wspvars']['dbcon'] = @mysql_connect(DB_HOST,DB_USER,DB_PASS);
$_SESSION['wspvars']['dbaccess'] = @mysql_select_db(DB_NAME, $_SESSION['wspvars']['dbcon']);

// EOF
