<?php

/**
 * @description Aqua
 * @author s.haendler@covi.de
 * @copyright (c) 2009, Common Visions Media.Agentur (COVI)
 * @since 3.2.3
 * @version 3.4.2
 * @lastchange 2009-07-07
 */

// image informations

$wspvars['iconset']['folder'] = "aqua/";
$_SESSION['wspvars']['iconset']['folder'] = $wspvars['iconset']['folder'];

if (!$styleinclude):

header("Content-Type: text/css"); 
?>html {
	width: 100%;
	margin: 0px;
	}

body {
	margin: 3px;
	font-family: verdana, arial, sans-serif;
	font-size: 10px;
	background: #ffffff;
	}

#menuarea {
	position: relative;
	width: 99%;
	}

#contentblock {
	clear: both;
	position: relative;
	width: 98%;
	top: 34px;
	max-width: 98%;
	}

#copyrightinfo {
	position: relative;
	width: 98%;
	top: 34px;
	max-width: 800px;
	}

#menuarea div {
	background: #ffffff;
	}

#menuarea div.menuitem {
	position: relative;
	float: left;
	margin-right: 3px;
	width: auto;
	overflow: hidden;
	}

#menuarea div.level1 {
	position: relative;
	clear: both;
	width: auto;
	padding: 4px;
	border-color: #5F85B2;
	}

<?php 
endif;
?>