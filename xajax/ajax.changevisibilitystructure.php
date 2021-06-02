<?php
/**
 * @description changing general menupoint visibility
 * @author s.haendler@covi.de
 * @copyright (c) 2019, Common Visions Media.Agentur (COVI)
 * @since 6.0
 * @version 6.8.1
 * @lastchange 2019-01-24
 */
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=''):
session_start();
require $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir'].'/data/include/globalvars.inc.php';
require $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir'].'/data/include/wsplang.inc.php';
require $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/dbaccess.inc.php";
require $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/ftpaccess.inc.php";
require $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/funcs.inc.php";
require $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/filesystemfuncs.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/errorhandler.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/".$_SESSION['wspvars']['wspbasediradd']."/".$_SESSION['wspvars']['wspbasedir']."/data/include/siteinfo.inc.php";

function withoutactlang($langs, $actlang) {
    $newarray = array();
    foreach($langs AS $l):
        if($l!=$actlang):
            $newarray[] = $l;
        endif;
    endforeach;
    return $newarray;
    }

if (isset($_REQUEST['mid'])) {
    $mid = intval($_REQUEST['mid']);
    $act_lang = $_REQUEST['language'];
    $vis_sql = "SELECT `visibility`, `denylang` FROM `menu` WHERE `mid` = ".$mid;
    $vis_res = doSQL($vis_sql);
    if ($vis_res['num']>0) {
        if(count($_SESSION['wspvars']['lang'])>1):
            if (intval($vis_res['set'][0]["visibility"])==0):
                $all_langs = unserializeBroken($_SESSION['wspvars']['sitelanguages']);
                $denylang = withoutactlang($all_langs['languages']['shortcut'],$act_lang);
                $newdeny = serialize($denylang);
                $sql = "UPDATE `menu` SET `visibility` = 1, `denylang` = '" . escapeSQL($newdeny) . "', `contentchanged` = 4 WHERE `mid` = ".$mid;
            else:
                $denylang = unserializeBroken($vis_res['set'][0]["denylang"]);	
                if(count($denylang)>0):
                    if(in_array($act_lang,$denylang)):
                        $denylang = withoutactlang($denylang,$act_lang);
                        $newdeny = serialize($denylang);
                        if(count($denylang)>0):
                            $sql = "UPDATE `menu` SET `denylang` = '" . escapeSQL($newdeny) . "', `contentchanged` = 4 WHERE `mid` = ".$mid;
                        else:
                            $sql = "UPDATE `menu` SET `denylang` = '', `contentchanged` = 4 WHERE `mid` = ".$mid;
                        endif;
                    else:
                        $denylang[] = $act_lang;
                        if(count($denylang)<count($_SESSION['wspvars']['lang'])):
                            $newdeny = serialize($denylang);
                            $sql = "UPDATE `menu` SET `denylang` = '" . escapeSQL($newdeny) . "', `contentchanged` = 4 WHERE `mid` = ".$mid;
                        else:
                            $sql = "UPDATE `menu` SET `visibility` = 0, `denylang` = '', `contentchanged` = 4 WHERE `mid` = ".$mid;
                        endif;

                    endif;
                else:
                    $denylang[] = $act_lang;
                    $newdeny = serialize($denylang);
                    $sql = "UPDATE `menu` SET `denylang` = '" . escapeSQL($newdeny) . "', `contentchanged` = 4 WHERE `mid` = ".$mid;

                endif;		
            endif;
        else:
            if(intval($vis_res['set'][0]["visibility"])==0):
                $sql = "UPDATE `menu` SET `visibility` = 1, `contentchanged` = 4 WHERE `mid` = ".$mid;
            else:
                $sql = "UPDATE `menu` SET `visibility` = 0, `contentchanged` = 4 WHERE `mid` = ".$mid;
            endif;				
        endif;
        $res = doSQL($sql);
        if ($res['res']) {
            echo $mid;
        }
    }
}

endif;

// EOF ?>