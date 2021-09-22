<?php
/**
* wsp frontend functions
* @author COVI
* @copyright (c) 2019, Common Visions Media.Agentur (COVI)
* @since 3.1
* @version 7.0
* @lastchange 2021-09-22
*/

// create dbcon
if(defined('DB_HOST')&&defined('DB_USER')&&defined('DB_PASS')&&defined('DB_NAME')){$_SESSION['wspvars']['db']=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);}

// replace mysql-functions before php7
if(!(function_exists('mysql_query'))){define('MYSQL_ASSOC',true);} if(!(function_exists('mysql_connect'))){function mysql_connect($host,$user,$pass){return array('host'=>$host,'user'=>$user,'pass'=>$pass);}} if(!(function_exists('mysql_select_db'))){function mysql_select_db($db,$connect){$_SESSION['wspvars']['db']=new mysqli($connect['host'],$connect['user'],$connect['pass'],$db);return $_SESSION['wspvars']['db'];}} if(!(function_exists('mysql_connect'))){function mysql_connect($host,$user,$pass){return array('host'=>$host,'user'=>$user,'pass'=>$pass);}} if(!(function_exists('mysql_select_db'))){function mysql_select_db($db,$connect){$_SESSION['wspvars']['db']=new mysqli($connect['host'],$connect['user'],$connect['pass'],$db);return $_SESSION['wspvars']['db'];}} if(!(function_exists('mysql_query'))){function mysql_query($sql){return doSQL($sql);}} if(!(function_exists('mysql_num_rows'))){function mysql_num_rows($queryarray=array('num'=>0)){return $queryarray['num'];}} if(!(function_exists('mysql_fetch_array'))){function mysql_fetch_array($data, $datatype){if (!(isset($_SESSION['mysql_fetch_array'][md5($data['sql'])]))){$_SESSION['mysql_fetch_array'][md5($data['sql'])] = $data['set'];}if(isset($_SESSION['mysql_fetch_array'][md5($data['sql'])])){if(count($_SESSION['mysql_fetch_array'][md5($data['sql'])])>0){$subdata=array_shift($_SESSION['mysql_fetch_array'][md5($data['sql'])]);return $subdata;}else{unset($_SESSION['mysql_fetch_array'][md5($data['sql'])]);return false;}}}} 

if(!(function_exists('mysql_result'))) {
    function mysql_result($resultset,$resultpos,$resultvar=false) {
        $rnum = array();
        foreach ($resultset['set'][0] AS $rkey=>$rvalue) {
            $rnum[]=$rkey;
        }
        if ($resultvar===false) {
            return $resultset['set'][$resultpos][($rnum[0])];
        }
        else if (is_int($resultvar)) {
            return $resultset['set'][$resultpos][($rnum[intval($resultvar)])];
        }
        else {
            return $resultset['set'][$resultpos][$resultvar];
        }
    }
}
if(!(function_exists('mysql_real_escape_string'))) {
    function mysql_real_escape_string($string) {
        return escapeSQL($string);
    }
}

if(!(function_exists('mysql_db_name'))){
    function mysql_db_name($result,$row,$field=NULL){return $result['set'][$row][$field];}};

// switch solution between mysql and mysqli
// if mysqli seems not to be supported we create the doSQL-function with 
// mysql_query support so the later defined doSQL-function will not be used
if (!(function_exists('mysqli_get_client_info'))) {
    if (!(function_exists('doSQL'))) { function doSQL($statement='') { $set = array('res'=>false,'aff'=>NULL,'num'=>NULL,'inf'=>NULL,'set'=>array(),'sql'=>$statement,'err'=>''); if ($_SESSION['wspvars']['db']) { $res = mysql_query($statement); if ($res) { $set['res'] = true; $set['aff'] = mysql_affected_rows(); $set['num'] = mysql_num_rows($res); $set['inf'] = mysql_insert_id(); } $set['err'] = mysql_error(); if($set['num'] && $set['num']>0) { for($n=0;$n<$set['num'];$n++) { $set['set'][$n] = mysql_fetch_assoc($res); } mysql_free_result($res); }} return $set; }}}
// new ONLY mysqli based functions
if(!(function_exists('mysqli_result'))){
    function mysqli_result($res,$row,$field=0){$res->data_seek($row);$datarow=$res->fetch_array();return $datarow[$field];}};if(!(function_exists('escapeSQL'))):function escapeSQL($string){if(isset($_SESSION['wspvars']['db']) && $_SESSION['wspvars']['db']):return mysqli_real_escape_string($_SESSION['wspvars']['db'], $string);else:return $string;endif;}endif;if (!(function_exists('doSQL'))): function doSQL($statement=''){$set=array('res'=>false,'aff'=>0,'num'=>0,'set'=>array(),'sql'=>$statement,'inf'=>'','err'=>'');if ($_SESSION['wspvars']['db']):$res = $_SESSION['wspvars']['db']->query($statement);if ($res===true):$set['res']=true;$set['aff']=$_SESSION['wspvars']['db']->affected_rows;elseif($res):$set['res']=true;$set['aff']=$_SESSION['wspvars']['db']->affected_rows;$set['num']=$res->num_rows;else:$set['err']=$_SESSION['wspvars']['db']->error_list;endif;if($set['res']):$set['inf']=$_SESSION['wspvars']['db']->insert_id;endif;if($set['num'] && $set['num']>0):for($n=0;$n<$set['num'];$n++):$set['set'][$n] = mysqli_fetch_assoc($res);endfor;mysqli_free_result($res);endif;endif;return $set;}endif;
// end of mysql/mysqli

if (!(function_exists("urltext"))) { 
    function urltext($txt) { $txt = strtolower(trim(utf8_decode($txt))); $replaces = array( chr(192) => "a", chr(193) => "a", chr(194) => "a", chr(195) => "ae", chr(197) => "a", chr(196) => "ae", chr(228) => "ae", chr(198) => "ae", chr(214) => "oe", chr(220) => "ue", chr(223) => "ss", chr(224) => "a", chr(225) => "a", chr(226) => "a", chr(232) => "e", chr(233) => "e", chr(234) => "e", chr(236) => "i", chr(237) => "i", chr(238) => "i", chr(242) => "o", chr(243) => "o", chr(244) => "o", chr(246) => "oe", chr(249) => "u", chr(250) => "u", chr(251) => "u", chr(252) => "ue", "\"" => "", "\'" => "", "," => "", " " => "-", "." => "", "?" => "", "!" => "", "*" => "", "#" => ""); foreach ($replaces AS $key => $value): $txt = str_replace($key, $value, trim($txt)); endforeach; $txt = preg_replace('/[^a-z0-9\-]/', "", $txt); $t = 0; while (strpos($txt, '--') || $t==20): $txt = str_replace("--", "-", $txt); $t++; endwhile; return $txt; }}

if (!(function_exists("prepareTextField"))): function prepareTextField($givenstring) { $string = str_replace("\"","&#34;",$givenstring); return $string; } endif;
// prepare text for utf8 output
if (!(function_exists("setUTF8"))): function setUTF8($givenstring) { $stringtype = mb_detect_encoding($givenstring); if (trim($stringtype)!=""): if (mb_check_encoding($givenstring, $stringtype)): if ($stringtype=='UTF-8'): return $givenstring; else: return utf8_encode($givenstring); endif; else: if ($stringtype=='UTF-8'): return utf8_encode($givenstring); else: return $givenstring; endif; endif; else: return utf8_encode($givenstring); endif; } endif;
// clean path from double slashes
if (!(function_exists('cleanPath'))){function cleanPath($pathstring){while(substr($pathstring,0,1)=='.'){$pathstring=substr($pathstring,1);}while(preg_match("/\.\./", $pathstring)){$pathstring = preg_replace("/\.\./", ".", $pathstring);} while (preg_match("/\.\//", $pathstring)){$pathstring = preg_replace("/\.\//", "/", $pathstring);} while (preg_match("/\/\//", $pathstring)){$pathstring = preg_replace("/\/\//", "/", $pathstring);}return trim($pathstring);}}
// check serialized arrays for broken contents and repair them
// thx to martin dordel for developing this function
if (!(function_exists('unserializeBroken'))){function unserializeBroken($value){if (is_array($value)){return $value;}else if(trim($value)!=''){$check=@unserialize($value);if(is_array($check)){return $check;}else{$tmpserialized='';while(strlen($value)>0){$substring=substr($value,0,2);if(strstr($substring,'a:')){$posSemikolon=strpos($value,'{');$substring2=substr($value,0,$posSemikolon+1);$tmpserialized=$tmpserialized.$substring2;$value=substr($value,$posSemikolon+1,strlen($value));}
                    else if (strstr($substring, 'i:')) {
                        $posSemikolon = strpos($value, ';');
                        $substring2 = substr($value, 0, $posSemikolon+1);
                        $tmpserialized = $tmpserialized.$substring2;
                        $value = substr($value, $posSemikolon+1, strlen($value));
                    }
                    else if (strstr($substring, 's:')) {
                        $int = preg_match('/";[adis]:/', $value, $treffer, PREG_OFFSET_CAPTURE);
                        if($int == 1) {
                            $substring2 = substr($value, 0, $treffer[0][1]+2);
                            $a = strpos($substring2, ':"');
                            $substring3 = substr($substring2, $a+2, ($treffer[0][1]));
                            $substring3 = substr($substring3, 0, strlen($substring3)-2);
                            $strlaenge = strlen($substring3);
                            $tmpserialized = $tmpserialized."s:".$strlaenge.":".'"'.$substring3.'";';
                            $value = substr($value, $treffer[0][1]+2, strlen($value));
                        }
                        else {
                           preg_match('/";}/', $value, $treffer, PREG_OFFSET_CAPTURE);
                           $substring2 = substr($value, 0, $treffer[0][1]+2);
                           $a = strpos($substring2, ':"');
                           $substring3 = substr($substring2, $a+2, ($treffer[0][1]));
                           $substring3 = substr($substring3, 0, strlen($substring3)-2);
                           $strlaenge = strlen($substring3);
                           $tmpserialized = $tmpserialized."s:".$strlaenge.":".'"'.$substring3.'";';
                           $value = substr($value, $treffer[0][1]+2, strlen($value));
                        }
                    }
                    else if (strstr($substring, 'd:')) {
                        $posSemikolon = strpos($value, ';');
                        $substring2 = substr($value, 0, $posSemikolon+1);
                        $tmpserialized = $tmpserialized.$substring2;
                        $value = substr($value, $posSemikolon+1, strlen($value));
                    }
                    else if (strstr($substring, '}')) {
                        $tmpserialized = $tmpserialized."}";
                        $value = substr($value, 1, strlen($value));
                    }
                    else {
                        $tmpserialized = $tmpserialized.substr($value, 0, 1);
                        $value = substr($value, 1, strlen($value));
                    }
                }
                return @unserialize($tmpserialized);
            }
	   }
	}
}
// return path to given mid
if (!(function_exists('returnPath'))): function returnPath($mid, $depth = 0, $basepath = '', $baselang = 'de') { $path_sql = "SELECT `connected`, `filename`, `isindex`, `level` FROM `menu` WHERE `mid` = ".intval($mid); $path_res = mysql_query($path_sql); $path_num = 0; if ($path_res): $path_num = mysql_num_rows($path_res); endif; if ($path_num>0): $parent = mysql_result($path_res, 0, "connected"); $fullpath = array(); $fullfile = array(); $p = 0; while (true): $fullpath[$p] = mysql_result($path_res, 0, "filename"); if (mysql_result($path_res, 0, "isindex")==1 && mysql_result($path_res, 0, "level")>1):  $fullfile[$p] = 'index'; else: $fullfile[$p] = mysql_result($path_res, 0, "filename"); endif; if (intval($parent)==0): break; endif; $path_sql = "SELECT `connected`, `filename`, `isindex`, `level` FROM `menu` WHERE `mid` = ".$parent; $path_res = mysql_query($path_sql); if ($path_res): $path_num = mysql_num_rows($path_res); endif; if ($path_num>0): $parent = mysql_result($path_res, 0, 'connected'); else: $parent = 0; break; endif; $p++; endwhile; $fullpath = array_reverse($fullpath); $givebackpath = ''; if ($depth==0): $throwdir = array_pop($fullpath); $givebackpath = str_replace("//", "/", str_replace("//", "/", $basepath."/".implode("/", $fullpath)."/")); elseif ($depth==1): $givebackpath = str_replace("//", "/", str_replace("//", "/", $basepath."/".implode("/", $fullpath)."/")); elseif ($depth==2): $throwdir = array_pop($fullpath); $givebackpath = str_replace("//", "/", str_replace("//", "/", $basepath."/".implode("/", $fullpath)."/".array_shift($fullfile).".php")); else: $givebackpath = str_replace("//", "/", str_replace("//", "/", $basepath."/")); endif; else: $givebackpath = str_replace("//", "/", str_replace("//", "/", $basepath."/")); endif; if ($baselang!='de'): $givebackpath = str_replace("//", "/", str_replace("//", "/", "/".$baselang."/".$givebackpath)); endif; return str_replace("//", "/", str_replace("//", "/", $givebackpath)); } endif;

// returns path from Interpreter to given mid
if (!(function_exists('returnInterpreterPath'))): function returnInterpreterPath($mid, $baselang = 'de') { $mid_sql = "SELECT `filename` FROM `menu` WHERE `mid` = ".intval($mid); $mid_res = mysql_query($mid_sql); $mid_num = 0; if ($mid_res): $mid_num = mysql_num_rows($mid_res); endif; if ($mid_num>0): if (intval($_SESSION['wsppage']['pagelinks'])==1 || intval($GLOBALS['wsppage']['pagelinks'])==1): $givebackpath = returnPath($mid, 1, '', $baselang); else: $givebackpath = returnPath($mid, 2, '', $baselang); endif; else: $givebackpath = "/"; endif; return $givebackpath; } endif;
// returns linked text
if (!(function_exists('returnLinkedText'))): function returnLinkedText($text) { preg_match_all('/intern:#[0-9]*;[a-z][a-z]/', $text, $pattern); if (is_array($pattern[0])): foreach ($pattern[0] as $key => $value): $linkID = substr($value, strpos($value, '#')+1); $linkIDarray = explode(";", $linkID); $langTarget = ""; if ($linkIDarray[1]!="" && $linkIDarray[1]!="de"): $langTarget = $linkIDarray[1]; endif; $link = returnInterpreterPath(intval($linkIDarray[0]), $langTarget); $text = str_replace("intern:#".$linkIDarray[0], $link, $text); $text = str_replace($link.";", $link, $text); endforeach; endif; preg_match_all('/intern:#[0-9]*/', $text, $pattern); if (is_array($pattern[0])): foreach ($pattern[0] as $key => $value): $linkID = substr($value, strpos($value, '#')+1); $sql = "SELECT `filename` FROM `menu` WHERE `mid` = ".intval($linkID); $res = mysql_query($sql); $link = returnInterpreterPath(intval($linkID)); $link = str_replace(";", "", $link); $text = str_replace("intern:#".$linkID, $link, $text); $text = str_replace($link.";", $link, $text); endforeach; endif; $findmail = "/[_a-zA-Z0-9-\.]*@[_a-z0-9-]*(.[a-z]{2,6})/"; preg_match_all($findmail , $text, $output); if (is_array($output[0])): $matches = array_unique($output[0]); foreach ($matches AS $emailvalue): $replacevalue = ''; foreach (str_split("mailto:".$emailvalue) as $obj): $replacevalue .= '&#' . ord($obj) . ';'; endforeach; $text = str_replace("mailto:".$emailvalue, $replacevalue, $text); $replacevalue = ''; foreach (str_split($emailvalue) as $obj): $replacevalue .= '&#' . ord($obj) . ';'; endforeach; $text = str_replace($emailvalue, $replacevalue, $text); endforeach; endif; $pattern = array(); preg_match_all('/\[\%PAGE:[0-9]*\%\]/', $text, $pattern); if (is_array($pattern[0])): foreach ($pattern[0] as $key => $value): $linkID = intval(str_replace("%]", "", str_replace("[%PAGE:", "", $value))); $langTarget = ""; $link = returnInterpreterPath($linkID, $langTarget); $text = str_replace("[%PAGE:".$linkID."%]", $link, $text); endforeach; endif; $pattern = array(); preg_match_all('/\[(%DOC:)(\S*)%\]/', $text, $pattern); if (is_array($pattern[0])): foreach ($pattern[0] as $key => $value): $doclink = str_replace('[%DOC:', '/media/', $value); $doclink = str_replace('%]', '', $doclink); $doclink = str_replace('//', '/', $doclink); $text = str_replace($value, $doclink, $text); endforeach; endif; return $text; } endif;

if (!(function_exists('include_once_check'))){function include_once_check($path){if(file_exists($path)&&is_file($path)){include_once($path);}}}
if (!(function_exists('include_check'))){function include_check($path){if(file_exists($path)&&is_file($path)){include($path);}}}
if (!(function_exists('require_once_check'))){function require_once_check($path){if(file_exists($path)&&is_file($path)){require_once($path);}}}
if (!(function_exists('require_check'))){function require_check($path){if(file_exists($path)&&is_file($path)){require($path);}}}

// EOF ?>