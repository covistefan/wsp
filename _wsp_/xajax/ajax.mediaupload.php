<?php
/**
 * ...
 * @author stefan@covi.de
 * @since 6.0
 * @version 7.0
 * @lastchange 2020-06-04
 */
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!='') {
   
    session_start();
    require("../data/include/globalvars.inc.php");
    require("../data/include/errorhandler.inc.php");
    require("../data/include/siteinfo.inc.php");
    
    if (is_array($_FILES["file"])) {
        if ($_FILES["file"]['tmp_name']!='' && $_FILES["file"]['error']==0) {
            // file was uploaded to tmp
            $sysfilename = basename($_FILES["file"]['name']);
            $sysfolder = base64_decode($_POST['fldr']);
            $tmppath = cleanPath(DOCUMENT_ROOT.DIRECTORY_SEPARATOR.WSP_DIR.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$_SESSION['wspvars']['usevar'].DIRECTORY_SEPARATOR.$sysfilename);
            // copy file to tmp folder
            $localcopy = copyFile($_FILES["file"]['tmp_name'], cleanPath(DIRECTORY_SEPARATOR.$sysfolder.$sysfilename));
            if ($localcopy===false) {
                addWSPMsg('errormsg', returnIntLang('media could not copy uploaded file to tmp'));
                $error = true;
            } else {
                $error = false;
            }
        }
    }

    // Remember to process the uploads!

    $f = $_FILES["file"];
    $file = $f["name"];

    $chunk  = isset($_POST["chunk"]) ? intval($_POST["chunk"]) : 1 ;
    $chunks = isset($_POST["chunks"]) ? intval($_POST["chunks"]) : 1;

    if ($error) {
        die("Error: " . $error);
    } else {
        die("Chunk: " . ($chunk + 1) . " of " . $chunks);
    }

}
else {
    echo "<pre>No direct access allowed</pre>";
}

?>