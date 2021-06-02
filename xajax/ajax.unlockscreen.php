<?php
/**
 * ...
 * @author stefan@covi.de
 * @since 7.0
 * @version 7.0
 * @lastchange 2019-03-10
 */

session_start();
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!='') {

    require("../data/include/globalvars.inc.php");
    require("../data/include/errorhandler.inc.php");
    
    if (isset($_POST['repass']) && trim($_POST['repass'])!='' && isset($_POST['reuser']) && trim($_POST['reuser'])!='') {
        $relogin_sql = "SELECT `userid` FROM `security` WHERE `usevar` = '".escapeSQL(trim($_POST['reuser']))."'";
        $relogin_res = doResultSQL($relogin_sql);
        if (intval($relogin_res)>0) {
            $_SESSION['wspvars']['userid'] = intval($relogin_res);
        }
        if (isset($_SESSION['wspvars']['userid']) && intval($_SESSION['wspvars']['userid'])>0) {
            $login_sql = "SELECT * FROM `restrictions` WHERE `rid` = ".intval($_SESSION['wspvars']['userid'])." AND `pass` = '".md5($_POST['repass'])."' AND `usertype` != '0'";
            $login_res = doSQL($login_sql);
            if ($login_res['num']==1) {
                // login allowed
                if (isset($_SESSION['wspvars']['lockedvar'])) {
                    $_SESSION['wspvars']['usevar'] = $_SESSION['wspvars']['lockedvar'];
                    if (!(isset($_SESSION['wspvars']['ftp'])) || $_SESSION['wspvars']['ftp']!==true) {
                        $_SESSION['wspvars']['ftp_host'] = trim(FTP_HOST);
                        $_SESSION['wspvars']['ftp_user'] = trim(FTP_USER);
                        $_SESSION['wspvars']['ftp_pass'] = trim(FTP_PASS);
                        $_SESSION['wspvars']['ftp_base'] = trim(FTP_BASE);
                        $_SESSION['wspvars']['ftp_port'] = (defined('FTP_PORT')?intval(FTP_PORT):21);
                        $_SESSION['wspvars']['ftp_ssl'] = (defined('FTP_SSL')?FTP_SSL:false);
                        $_SESSION['wspvars']['ftp_pasv'] = (defined('FTP_PASV')?FTP_PASV:false);
                        $_SESSION['wspvars']['ftp'] = true;
                    }
                    if ($_SESSION['wspvars']['ftp']===true) {
                        // normal ftp-connection OR temporary ftp-connection are true
                        // check, if basedir is set correct by checking, if wsp-basefolder is found     
                        $ftp = doFTP();
                        // create user directory with normal ftp-connection
                        $mkdir = @ftp_mkdir($ftp, $_SESSION['wspvars']['ftp_base'].'/'.WSP_DIR.'/tmp/'.$_SESSION['wspvars']['usevar']);
                        if ($mkdir):
                            $chmod = @ftp_chmod($ftp, 0777, $mkdir);
                        endif;
                        @ftp_close($ftp);
                        unset($_SESSION['wspvars']['lockedvar']);
                        doSQL("UPDATE `security` SET `timevar` = ".time()." WHERE `usevar` = '".$_SESSION['wspvars']['usevar']."'");
                        $_SESSION['wspvars']['lockscreen'] = false;
                        echo 'true';
                    } else {
                        echo 'false';
                    }
                }
                else {
                    $_SESSION['wspvars']['usevar'] = trim($_POST['reuser']);
                    if (!(isset($_SESSION['wspvars']['ftp'])) || $_SESSION['wspvars']['ftp']!==true) {
                        $_SESSION['wspvars']['ftp_host'] = trim(FTP_HOST);
                        $_SESSION['wspvars']['ftp_user'] = trim(FTP_USER);
                        $_SESSION['wspvars']['ftp_pass'] = trim(FTP_PASS);
                        $_SESSION['wspvars']['ftp_base'] = trim(FTP_BASE);
                        $_SESSION['wspvars']['ftp_port'] = (defined('FTP_PORT')?intval(FTP_PORT):21);
                        $_SESSION['wspvars']['ftp_ssl'] = (defined('FTP_SSL')?FTP_SSL:false);
                        $_SESSION['wspvars']['ftp_pasv'] = (defined('FTP_PASV')?FTP_PASV:false);
                        $_SESSION['wspvars']['ftp'] = true;
                    }
                    if ($_SESSION['wspvars']['ftp']===true) {
                        // normal ftp-connection OR temporary ftp-connection are true
                        // check, if basedir is set correct by checking, if wsp-basefolder is found     
                        $ftp = doFTP();
                        // create user directory with normal ftp-connection
                        $mkdir = @ftp_mkdir($ftp, $_SESSION['wspvars']['ftp_base'].'/'.WSP_DIR.'/tmp/'.$_SESSION['wspvars']['usevar']);
                        if ($mkdir):
                            $chmod = @ftp_chmod($ftp, 0777, $mkdir);
                        endif;
                        @ftp_close($ftp);
                        unset($_SESSION['wspvars']['lockedvar']);
                        doSQL("UPDATE `security` SET `timevar` = ".time()." WHERE `usevar` = '".$_SESSION['wspvars']['usevar']."'");
                        $_SESSION['wspvars']['lockscreen'] = false;
                        echo 'true';
                    } else {
                        echo 'false';
                    }
                }
            }
            else {
                // login not allowed
                echo (defined('WSP_DEV')) ? 'login_num != 1' : 'false';
            }
        }
        else {
            echo (defined('WSP_DEV')) ? 'session_wspvars_userid not set or empty' : 'false';
        }
    }
    else {
        echo (defined('WSP_DEV')) ? 'post_repass ('.trim($_POST['repass']).') or post_reuser ('.trim($_POST['reuser']).') not set or empty' : 'false';
    }
}
else {
    echo "<pre>No direct access allowed</pre>";
}

// EOF ?>