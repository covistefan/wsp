<?php
/**
 * @author stefan@covi.de
 * @since 6.0
 * @version 7.0
 * @lastchange 2019-08-20
 */

if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!='') {
    session_start();
    require("../data/include/globalvars.inc.php");
    require("../data/include/siteinfo.inc.php");
    require("../data/include/clsinterpreter.inc.php");

    if (isset($_POST['mid']) && intval($_POST['mid'])>0) {
        // check for selected page if mid is already in publisher
        $fp_sql = "SELECT `id` FROM `wspqueue` WHERE (`action` = 'publishcontent' OR `action` = 'publishitem' OR `action` = 'publishstructure') AND `param` = ".intval($_POST['mid'])." AND `done` = 0";
        $fp_res = doSQL($fp_sql);
        if ($fp_res['num']==0) {
            doSQL("INSERT INTO `wspqueue` SET `uid` = ".$_SESSION['wspvars']['userid'].", `set` = ".time().", `action` = 'publishitem', `param` = ".intval($_POST['mid']).", `timeout` = ".time());
            $wq_sql = "SELECT `id` FROM `wspqueue` WHERE `done` = 0"; 
            $wq_res = getNumSQL($wq_sql);
            echo $wq_res;
        } else {
            echo -1;
        }
    }
}
else {
	echo "timeout|false";
}

// EOF ?>