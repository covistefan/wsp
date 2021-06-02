<?php
/**
 * javascript parser-functions
 * @author stefan@covi.de
 * @since 6.0
 * @version 7.0
 * @lastchange 2019-09-02
 */

if (!(function_exists('publishJS'))) {
    function publishJS($jsid, $ftp = false) {
        $returnstat = false;
        if ($ftp) { 
            $js_sql = 'SELECT `id`, `file`, `scriptcode` FROM `javascript` WHERE `id` = '.intval($jsid);
            $js_res = doSQL($js_sql);
            if ($js_res['num']>0) {
                $tmppath = cleanPath(DOCUMENT_ROOT."/".WSP_DIR."/tmp/".$_SESSION['wspvars']['usevar']."/");
                $tmpfile = tempnam($tmppath, '');
                $fh = fopen($tmpfile, "r+");
                fwrite($fh, stripslashes(trim($js_res['set'][0]['scriptcode'])));
                fclose($fh);
                if (!ftp_put($ftp, FTP_BASE."/data/script/".trim($js_res['set'][0]['file']).'.js', $tmpfile, FTP_BINARY)) {
                    $returnstat = false;
                    $_SESSION['wspvars']['errormsg'] .= "<p>Kann erzeugte Datei <strong>".trim($js_res['set'][0]['file']).".js</strong> nicht hochladen. (Put)</p>";
                }
                else {
                    $returnstat = true;
                    $timestamp = time();
                    doSQL("UPDATE `javascript` SET `lastpublish` = ".$timestamp." WHERE `id` = ".intval($jsid));
                }
                unlink($tmpfile);
            }
            return $returnstat;
        }
	}	// publishJS()
}

?>