<?php
/**
* @author s.haendler@covi.de
* @copyright (c) 2019, Common Visions Media.Agentur (COVI)
* @since 3.1
* @version 7.0
* @lastchange 2019-03-04
*/

if (!function_exists('apache_response_headers')) {
    function apache_response_headers () {
        $arh = array();
        flush();
        $headers = headers_list();
        foreach ($headers as $header) {
            $header = explode(":", $header);
            $arh[array_shift($header)] = trim(implode(":", $header));
        }
        return $arh;
    }
}
// manipulate headers
@header_remove('X-Powered-By');
if ($_SERVER['HTTPS']=='on' && !array_key_exists('X-Content-Type-Options', apache_response_headers ())) { @header('Strict-Transport-Security: max-age=31536000; includeSubDomains'); }
if (!array_key_exists('X-Content-Type-Options', apache_response_headers ())) { @header('X-Content-Type-Options: nosniff'); }
if (!array_key_exists('X-XSS-Protection', apache_response_headers ())) { @header('X-XSS-Protection: 1; mode=block'); }
if (!array_key_exists('X-Frame-Options', apache_response_headers ())) { @header('X-Frame-Options: ALLOWALL'); }
if (!array_key_exists('Referrer-Policy', apache_response_headers ())) { @header('Referrer-Policy: strict-origin-when-cross-origin'); }
if (!array_key_exists('Content-Security-Policy', apache_response_headers ())) { @header('Content-Security-Policy: default-src * \'unsafe-inline\';'); }
if (!array_key_exists('Permissions-Policy', apache_response_headers ())) { @header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'); }

if (phpversion()>5): date_default_timezone_set('Europe/Berlin'); endif;
// get information about root directory and setup param
$sysroot = str_replace("//","/",str_replace("//","/",str_replace("//","/",str_replace("/data/include/", "/", "/".__DIR__."/"))));
$docroot = str_replace("//","/",str_replace("//","/",str_replace("//","/","/".$_SERVER['DOCUMENT_ROOT']."/")));
if ($sysroot!=$docroot) { define('DOCUMENT_ROOT', $sysroot); } else { define('DOCUMENT_ROOT', $docroot); }
// include page funcs
if (is_file(DOCUMENT_ROOT.'/data/include/funcs.inc.php')): include DOCUMENT_ROOT.'/data/include/funcs.inc.php'; endif;
// initiate db-connect
if (is_file(DOCUMENT_ROOT.'/data/include/dbaccess.inc.php')): include DOCUMENT_ROOT.'/data/include/dbaccess.inc.php'; endif;
// include switch page
if (is_file(DOCUMENT_ROOT.'/data/include/switch.inc.php')): include DOCUMENT_ROOT.'/data/include/switch.inc.php'; endif;
// browser detection
if (is_file(DOCUMENT_ROOT.'/data/include/checksession.inc.php')) { include DOCUMENT_ROOT.'/data/include/checksession.inc.php'; }
// visitor control
if (is_file(DOCUMENT_ROOT.'/data/include/checkuser.inc.php')) { include DOCUMENT_ROOT.'/data/include/checkuser.inc.php'; }
// tracking
if (is_file(DOCUMENT_ROOT.'/data/include/wsptracking.inc.php')) { include DOCUMENT_ROOT.'/data/include/wsptracking.inc.php'; }

// EOF ?>