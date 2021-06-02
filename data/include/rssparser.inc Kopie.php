<?php
/**
 * rss parser-functions
 * @author stefan@covi.de
 * @since 6.0
 * @version 6.0
 * @lastchange 2014-03-21
 */

if (!(function_exists('publishRSS'))):
	function publishRSS($rssid) {
		$ftpAttempt = 3;
		$counterOld = $ftpAttempt;
		$ftp = false;
		$returnstat = false;
		// create ftp-connect	
		while (!$ftp && ($ftpAttempt > 0)):
			if ($counterOld != $ftpAttempt):
				$counterOld = $ftpAttempt;
				sleep(1);
			endif;
			$ftp = ftp_connect($GLOBALS['wspvars']['ftphost']);
			$ftpAttempt--;
		endwhile;
		if ($ftp === false):
			$returnstat.= "ftpconnectfalse\n";
			$_SESSION['wspvars']['errormsg'] .= "<p>Kann erzeugte Dateien nicht hochladen. (Connect)</p>";
		elseif (!ftp_login($ftp, $GLOBALS['wspvars']['ftpuser'], $GLOBALS['wspvars']['ftppass'])):
			$returnstat.= "ftploginfalse\n";
			$_SESSION['wspvars']['errormsg'] .= "<p>Kann erzeugte Datei nicht hochladen. (Login)</p>";
		else:
			$rssdata_sql = "SELECT * FROM `rssdata` WHERE `rid` = ".intval($rssid);
			$rssdata_res = mysql_query($rssdata_sql);
			$rssdata_num = mysql_num_rows($rssdata_res);
			if ($rssdata_num>0):
				$rssentry_sql = "SELECT * FROM `rssentries` WHERE `rid` = '".intval($rssid)."' ORDER BY `eupdate` DESC";
				$rssentry_res = mysql_query($rssentry_sql);
				$rssentry_num = mysql_num_rows($rssentry_res);
				
				$tmprssfile = '<'.'?'.'xml version="1.0" encoding="UTF-8" '.'?'.'>
	<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>';
				$tmprssfile.= "\n<title>".mysql_result($rssdata_res, $r, 'rsstitle')."</title>\n";
				$tmprssfile.= "<link>http://".mysql_result($rssdata_res, $r, 'rsshref')."</link>\n";
				if (mysql_result($rssdata_res,$r,'rsssubtitle')!=mysql_result($rssdata_res, $r, 'rsstitle') && mysql_result($rssdata_res, $r, 'rsssubtitle')!=""):
					$tmprssfile.= "<description>".trim(mysql_result($rssdata_res, $r, 'rsssubtitle'))."</description>\n";
				else:
					$tmprssfile.= "<description>".mysql_result($rssdata_res, $r, 'rsshref')."</description>\n";
				endif;
				$tmprssfile.= "<copyright>".mysql_result($rssdata_res, $r, 'rssauthor')."</copyright>\n";
				$tmprssfile.= "<language>de</language>\n";
				$tmprssfile.= "<pubDate>".date("r")."</pubDate>\n";
				$tmprssfile.= "<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n";
				$tmprssfile.= "<atom:link href=\"http://".mysql_result($rssdata_res,$r,'rsshref')."/media/rss/".mysql_result($rssdata_res,$r,'rssfilename').".rss\" rel=\"self\" type=\"application/rss+xml\" />\n";
					
				for ($e=0;$e<$rssentry_num;$e++):
					$tmprssfile.= "<item>\n";
					$tmprssfile.= "<title>".trim(mysql_result($rssentry_res,$e,'etitle'))."</title>\n";
					$tmprssfile.= "<description>".utf8_encode(html_entity_decode(strip_tags(trim(mysql_result($rssentry_res,$e,'esummary')))))."</description>\n";
					if (mysql_result($rssentry_res,$e,'econtype')=="url"):
						$tmprssfile.= "<link>http://".mysql_result($rssentry_res,$e,'econ')."</link>\n";
					elseif (mysql_result($rssentry_res,$e,'econtype')=="mid"):
						// verknuepfung mit dem cm-menue und bildung der
						// verweis-url aus der menue-tabelle
						$link = mysql_result($rssdata_res,$r,'rsshref').str_replace("//","/",returnPath(mysql_result($rssentry_res,$e,'econ'), '/', 2));
						$tmprssfile.= "<link>http://".$link."</link>\n";
					endif;
					$tmprssfile.= "<author>".mysql_result($rssentry_res,$e,'eauthor')."</author>\n";
					if (mysql_result($rssentry_res,$e,'econtype')=="url"):
						$tmprssfile.= "<guid>http://".mysql_result($rssentry_res,$e,'econ')."/?rssid=".mysql_result($rssentry_res, $e, 'eid')."</guid>\n";
					elseif (mysql_result($rssentry_res,$e,'econtype')=="mid"):
						// verknuepfung mit dem cm-menue und bildung der
						// verweis-url aus der menue-tabelle
						$link = mysql_result($rssdata_res,$r,'rsshref').str_replace("//","/",returnPath(mysql_result($rssentry_res,$e,'econ'), '/', 2));
						$tmprssfile.= "<guid>http://".$link."?rssid=".mysql_result($rssentry_res, $e, 'eid')."</guid>\n";
					endif;
					$entrydate = mktime(substr(mysql_result($rssentry_res,$e,'eupdate'),8,2),substr(mysql_result($rssentry_res,$e,'eupdate'),10,2),substr(mysql_result($rssentry_res,$e,'eupdate'),12,2),substr(mysql_result($rssentry_res,$e,'eupdate'),4,2),substr(mysql_result($rssentry_res,$e,'eupdate'),6,2),substr(mysql_result($rssentry_res,$e,'eupdate'),0,4));
					$tmprssfile.= "<pubDate>".date("r", $entrydate)."</pubDate>\n";
					$tmprssfile.= "</item>\n";
				endfor;
					
				$tmprssfile.= "</channel>\n";
				$tmprssfile.= "</rss>";
				
				$tmppath = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['wspvars']['wspbasedir']."/tmp/".$GLOBALS['wspvars']['usevar']."/");
				$tmpfile = tempnam($tmppath, '');
						
				if (!(is_dir($_SERVER['DOCUMENT_ROOT']."/media/rss"))):
					ftp_mkdir($ftp, str_replace("//", "/", $GLOBALS['wspvars']['ftpbasedir']."/media/rss/"));
				endif;
						
				$fh = fopen($tmpfile, "w+");
				fwrite ($fh, $tmprssfile);
				fclose ($fh);
					
				if (!ftp_put($ftp, str_replace("//", "/", $GLOBALS['wspvars']['ftpbasedir']."/media/rss/".mysql_result($rssdata_res,$r,'rssfilename').'.rss'), $tmpfile, FTP_BINARY)):
					$_SESSION['errormsg'] .= "<p>Kann erzeugte Datei nicht hochladen. (Put)</p>";
				else:
					$rsspublish = true;
					$returnstat = true;
				endif;
				unlink($tmpfile);
			
				if ($rsspublish===true):
					for ($e=0;$e<$rssentry_num;$e++):
						mysql_query("UPDATE `rssentries` SET `epublished` = 1 WHERE `eid` = '".mysql_result($rssentry_res,$e,'eid')."'");
						$returnstat = true;
					endfor;
				endif;
			endif;
		endif;
		ftp_close($ftp);
		return $returnstat;
		}
endif;
	
?>