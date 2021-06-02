<?php
/**
 * db-access
 * @since 3.1
 * @version 3.3
 * @lastchange 2009-08-18
 */

/*
$conn_id = mysql_connect("localhost", "sws", "kpSb6gCr");
mysql_select_db("sws_db", $conn_id);
//printf("Benutzer Zeichensatz ist %s\n", $charset);
mysql_set_charset('utf8');
//mysql_set_charset('iso-8859-1');
//$charset = mysql_client_encoding($conn_id);
//printf("Benutzer Zeichensatz ist %s\n", $charset);
*/
    
DEFINE('DB_HOST', '*');
DEFINE('DB_NAME', '*');
DEFINE('DB_USER', '*');
DEFINE('DB_PASS', '*');

$_SESSION['wspvars']['db'] = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
$_SESSION['wspvars']['dbcon'] = @mysql_connect(DB_HOST,DB_USER,DB_PASS);
$_SESSION['wspvars']['dbaccess'] = @mysql_select_db(DB_NAME, $_SESSION['wspvars']['dbcon']);

?>