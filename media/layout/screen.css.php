<?php

/**
 * WSP Main Stylesheet
 * @author s.haendler@covi.de
 * @copyright (c) 2009, Common Visions Media.Agentur (COVI)
 * @since 3.2.3
 * @version 4.0.4
 * @lastchange 2011-05-12
 */

// $wspvars['icon']['expand_x'] = "/expands.gif";
// $wspvars['icon']['collapse_x'] = "/collapses.gif";

$wspvars['iconset']['folder'] = "";
$_SESSION['wspvars']['iconset']['folder'] = $wspvars['iconset']['folder'];

if (!$styleinclude):

header("Content-Type: text/css");

?>html {
	width: 100%;
	margin: 0px;
	}

body {
	margin: 0px;
	margin-left: 210px;
	margin-top: 30px;
	font-family: verdana, arial, sans-serif;
	font-size: 10px;
	background: #ffffff;
	}

h1 {
	font-size: 120%;
	font-weight: bold;
	margin-top: 4px;
	margin-bottom: 14px;
	color: #2f629e;
	}

h2 {
	font-size: 110%;
	font-weight: bold;
	margin-top: 8px;
	margin-bottom: 14px;
	color: #2f629e;
	}

p {
	margin-top: 4px;
	margin-bottom: 4px;
	}

a {
	color: #e99b4b;
	text-decoration: none;
	font-weight: bold;
	}

form {
	margin: 0px;
	padding: 0px;
	}

input, textarea {
	font-family: verdana, arial, sans-serif;
	font-size: 11px;
	}

/* block definitions */

#headerback {
	position: absolute; top: 0px; left: 0px; width: 100%; height: 25px; 
	line-height: 25px; background: #2F629E; text-align: right;
	}

#header {
	position: absolute; top: 0px; right: 0px; width: 100%; height: 25px; line-height: 25px; text-align: right;
	}

#headerloginname {
	position: absolute;
	margin: 0px;
	padding: 0px;
	top: 0px;
	left: 8px;
	margin-left: 0px;
	height: 25px;
	line-height: 25px;
	color: #ffffff;
	font-weight: bold;
	}

#header #bar1 {
	position: absolute; right: 0px; height: 25px; width: 11px; background: #759BC6;
	}

#header #bar2 {
	position: absolute; right: 15px; height: 25px; width: 27px; background: #97BCE8;
	}
	
#header #bar3 {
	position: absolute; right: 42px; height: 25px; width: 6px; background: #759BC6;
	}

#header #bar4 {
	position: absolute; right: 48px; height: 25px; width: 42px; background: #97BCE8;
	}

#header #bar5 {
	position: absolute;
	right: 90px;
	height: 25px;
	width: 3px;
	background: #759BC6;
	}

#header #bar6 {
	position: absolute;
	right: 93px;
	height: 25px;
	width: 47px;
	background: #97BCE8;
	}

#menuarea {
	position: absolute;
	top: 28px;
	left: 3px;
	width: 200px;
	}

#contentblock {
	position: relative;
	width: 98%;
	margin: 0px;
	margin-top: 34px;
	max-width: 800px;
	}

#contentholder {
	position: relative;
	width: 98%;
	margin: 0px;
	margin-top: 34px;
	max-width: 1000px;
	}


#copyrightinfo {
	position: relative;
	width: 98%;
	margin-top: 5px;
	max-width: 800px;
	}

/* fieldset definitions */

fieldset {
	padding: 4px;
	margin: 5px 0px 3px 0px;
	border: 1px solid #3d6698;
	}

fieldset h1, fieldset h2 {
	margin: 0px;
	}

legend {
	margin-bottom: 6px;
	font-weight: bold;
	}

#menuarea div.active {
	border: 1px solid #e99b4b;
	}

#menuarea div.menuitem {
	width: 190px;
	padding: 4px;
	margin-bottom: 3px;
	border: 1px solid #2f629e;
	color: #2f629e;
	line-height: 15px;
	}
	
#menuarea div.level1 {
	width: 180px;
	padding: 4px;
	padding-left: 14px;
	}

#menuarea ul {
	list-style-type: none;
	}

#menuarea li {
	list-style-type: none;
	width: 190px;
	padding: 4px;
	margin-bottom: 3px;
	border: 1px solid #2f629e;
	color: #2f629e;
	line-height: 15px;
	}

#menuarea li ul {
	margin: 2px 0px;
	padding: 0px;
	}
	
#menuarea li ul li {
	margin: 2px 0px;
	width: 180px;
	}

#menuarea li.active {
	list-style-type: none;
	border: 1px solid #e99b4b;
	}

.green {
	color: #0b7200;
	}

.greenfield {
	color: #0b7200;
	background: #ffffff;
	border: solid 1px #0b7200;
	padding: 2px 5px 2px 5px;
	cursor: pointer;
	}

.red {
	color: #c1000e;
	}

.redfield {
	color: #c1000e;
	background: #ffffff;
	border: solid 1px #c1000e;
	padding: 2px 5px 2px 5px;
	cursor: pointer;
	}

.orange {
	color: #EA974A;
	}

.orangefield {
	color: #EA974A;
	background: #ffffff;
	border: solid 1px #EA974A;
	padding: 2px 5px 2px 5px;
	cursor: pointer;
	}

.buttonform {
	font-size: 9px;
	}

.errormsg {
	background: #cc0000;
	color: #ffffff;
	/* margin-bottom: 12px; */
	}

.noticemsg {
	background: #0b7200;
	color: #ffffff;
	/* margin-bottom: 12px; */
	}

.noticemsgdisabled {
	background: #e2e7ef;
	/* margin-bottom: 12px; */
	}

.options {
	border-color: #e8994b;
	background: #f8e1cb;
	}

.text {
	border: 1px solid #cccccc;
	background: #ffffff;
	}

.emptyrow {
	font-size: 2px;
	height: 3px;
	}

.tooltip {
	opacity: 0.2;
	color: #000000;
	font-size: x-small;
	}

.tooltip:hover, .tooltipover {
	opacity: 1;
	color: #3d6698;
	font-size: x-small;
	}

.showcase {
	float: left;
	width: 150px;
	border: 1px solid #000000;
	padding: 5px;
	margin-right: 5px;
	margin-bottom: 5px;
	text-align: center;
	overflow: hidden;
	}

.publishrequired {
	margin-left: -2px;
	padding: 1px 2px 1px 3px;
	color: #ff0000;
	}

.publish, .publishrequiredpublish {
	margin-left: -2px;
	padding: 1px 2px 1px 3px;
	color: #ffffff;
	background: #3d6698;
	}

.nopublish {
	margin-left: -2px;
	padding: 1px 2px 1px 3px;
	}

.trselected {
	background-color: #dadada;
	}

#wspinfos {
	position: fixed;
	bottom: 10px;
	right: 10px;
	border: 1px solid #000000;
	background: #ffffff;
	padding: 3px;
	z-index: 10;
	}

.trhighligt {
	background-color: #eeeeee;
	}

ul.editorlist {
	margin: 0px;
	padding: 0px;
	width: 100%;
	list-style-type: none;
	}
	
ul.editorlist li {
	list-style-type: none;
	margin: 2px;
	}

ul.fieldlist {
	margin: 0px;
	margin-left: 18px;
	padding: 0px;
	list-style-type: none;
	margin-bottom: 1px;
	}
	
ul.dragable, ul.contentable {
	margin: 0px;
	padding-left: 19px;
	}

ul.dragable li, ul.contentable li {
	margin-left: 0px;
	padding: 0px;
	}

ul.dragable #newdrop {
	padding: 3px;
	opacity: 0.5;
	}

ul.secondcol li {
	border-top: 1px solid white;
	}

ul.fieldlist li.firstcol {
	line-height: 16px;
	background-color: #ffffff;
	padding: 3px;
	margin-bottom: 1px;
	line-height: 16px;
	}

ul.fieldlist li.secondcol {
	line-height: 16px;
	padding: 3px;
	margin-bottom: 1px;
	line-height: 16px;
	}

#display_sitestructure ul {
	list-style-type: none;
	}

#display_sitestructure li {
	min-height: 18px;
	border: 1px solid #fff;
	}

#display_sitestructure .over {
	opacity: 20;
	}

div.dropmarker {
	position: relative;
	height: 3px;
	width: 11px;
	background: #000000 url(media/screen/dropzone.png);
	margin-top: -3px;
	margin-left: 5px;
	z-index:1000;
	overflow: hidden;
	}

#display_sitestructure ul li {
	list-style-type: none;
	background: #E1E7EF;
	}
	
#display_sitestructure ul ul li {
	list-style-type: none;
	background: #ffffff;
	}

#display_sitestructure ul ul ul li {
	list-style-type: none;
	background: #E1E7EF;
	}
	
#display_sitestructure ul ul ul ul li {
	list-style-type: none;
	background: #ffffff;
	}
	
#display_sitestructure ul ul ul ul ul li {
	list-style-type: none;
	background: #E1E7EF;
	}
	
#display_sitestructure ul ul ul ul ul ul li {
	list-style-type: none;
	background: #ffffff;
	}

#display_sitestructure ul ul ul ul ul ul ul li {
	list-style-type: none;
	background: #E1E7EF;
	}
					
#display_sitestructure ul ul ul ul ul ul ul ul li {
	list-style-type: none;
	background: #ffffff;
	}
					
#display_sitestructure ul ul ul ul ul ul ul ul ul li {
	list-style-type: none;
	background: #E1E7EF;
	}
					
#display_sitestructure ul ul ul ul ul ul ul ul ul ul li {
	list-style-type: none;
	background: #ffffff;
	}

#display_sitestructure ul.contentable li {
	background: none;
	}

table.contenttable {
	width: 100%;
	border-collapse: collapse;
	}

tr.tablehead {
	line-height: 16px;
	background-color: #2a639e;
	color: #ffffff;
	}

tr.activecol {
	line-height: 16px;
	background-color: #F9E1CB;
	}

tr.firstcol {
	line-height: 16px;
	background-color: #ffffff;
	}

tr.firstcol a {
	color: #000;
	}

tr.secondcol {
	line-height: 16px;
	background-color: #e2e7ef;
	}

tr.secondcol a {
	color: #000;
	}

<?php endif; ?>