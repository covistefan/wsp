<?php
/**
 * stylesheet parser-functions
 * @author stefan@covi.de
 * @since 6.0
 * @version 7.0
 * @lastchange 2019-03-07
 */

if (!(function_exists('publishCSS'))) {
function publishCSS($cssid, $ftp = false) {
	$returnstat = false;
	if ($ftp!==false) {
		$css_sql = 'SELECT * FROM `stylesheets` WHERE `id` = '.intval($cssid);
		$css_res = doSQL($css_sql);
		if ($css_res['num']>0) {
			$tmppath = cleanPath(DOCUMENT_ROOT."/".WSP_DIR."/tmp/".$_SESSION['wspvars']['usevar']."/");
			$tmpfile = tempnam($tmppath, '');
			$fh = fopen($tmpfile, "r+");
			fwrite($fh, stripslashes($css_res['set'][0]['stylesheet']));
			fclose($fh);
			if (!ftp_put($ftp, FTP_BASE."/media/layout/".$css_res['set'][0]['file'].'.css', $tmpfile, FTP_BINARY)) {
				$returnstat = false;
				addWSPMsg('errormsg', "Kann erzeugte Datei <strong>".$css_res['set'][0]['file'].".css</strong> nicht hochladen. (Put)</p>");
            }
            else {
                $returnstat = true;
				doSQL("UPDATE `stylesheets` SET `lastpublish` = ".time()." WHERE `id` = ".intval($cssid));
            }
			unlink($tmpfile);
		}
	}
	return $returnstat;
	}	// publishCSS()
}

?>