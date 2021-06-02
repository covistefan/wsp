<?php
/**
*
* @author stefan@covi.de
* @since 3.1
* @version 3.2.3
* @lastchange 2007-08-10
*/

if (ereg("Gecko" , $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'mozilla';
if (ereg("Firefox" , $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'firefox';
if (ereg("Netscape" , $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'netscape';
if (ereg("MSIE" , $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'internet explorer';
if (ereg("Opera" , $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'opera';
if (ereg("AppleWebKit" , $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'applewebkit';
if (ereg("Safari", $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'safari';
if (ereg("Avant", $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'avant';
if (ereg("Konqueror", $_SERVER["HTTP_USER_AGENT"])) $showbrowser = 'konqueror';

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')>0):
	$usedbrowser = 'ie';
else:
	$usedbrowser = 'allother';
endif;
?>