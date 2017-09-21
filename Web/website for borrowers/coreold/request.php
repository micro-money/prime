<?php

/*     MySQL 

$dbpnt = @mysql_connect($db['host'], $db['username'], $db['password']);
$mysql_err = mysql_error();
if (!$dbpnt) die("     MySQL<br />" . $mysql_err);
mysql_select_db($db['base'], $dbpnt);
*/

#     
session_start();
$dbase=$db['base']; #print_r($_SESSION); die();
if (!isset($_SESSION['fil'])) $_SESSION['fil']=0; $curr_fil=$_SESSION['fil']; 
/*
if (isset($_SESSION['dbase']) && isset($db['bases'][$_SESSION['dbase']]))  {
	$dbase = $_SESSION['dbase'];
} else {
	if (!isset($_SESSION['dbase'])) {
		$sho=$_SERVER['HTTP_HOST'];
		foreach ($db['domens'] as $k=>$v) if ($sho==$k) $dbase = $v;
	} 
}
*/

if (!is_array($db['username'])) {
	$dbpnt = @mysql_connect($db['host'], $db['username'], $db['password']);		$mysql_err = mysql_error();
	if (!$dbpnt) die("     MySQL<br />" . $mysql_err);
} else {
	$x=0; while ($x<count($db['username'])) {
		$dbpnt = @mysql_connect($db['host'], $db['username'][$x], $db['password'][$x]);		$mysql_err = mysql_error();
		if ($dbpnt) { header("u1: ".$db['username'][$x]); $x=count($db['username']); }
		$x++; 
	}
	if (!$dbpnt) die("     MySQL<br />" . $mysql_err);
}

mysql_select_db($dbase, $dbpnt);

mysql_query("SET TIME_ZONE='+6:30'");
mysql_query("set character_set_results='utf8'");
mysql_query("set character_set_client='utf8'");
mysql_query("set collation_connection='utf8_general_ci'");

/*    */

//   IP   
$ip = $_SERVER['REMOTE_ADDR'];      $ip_sql = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
$br = $_SERVER['HTTP_USER_AGENT'];	$br_sql = mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
#$self = substr ($_SERVER['PHP_SELF'], 1);
$self=str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['PHP_SELF']);
$selfm=explode('/',str_replace(['/','\\'],'/',$self)); $selfn=$selfm[count($selfm)-1];
$selfc=str_replace('index.php','',$self);
$selfp=implode('/',$selfm);
#print_r($_SERVER); die();

$r_get_def_country=0; 

#          >  
if (!isset($_COOKIE['country']) && !isset($_GET['country']))  				$r_get_def_country=1;
#           >  
if (isset($_COOKIE['country']) && !isset($countrym[$_COOKIE['country']])) 	$r_get_def_country=1;
#         >  
if (isset($_GET['country']) && !isset($countrym[$_GET['country']]))  		$r_get_def_country=1;

if ($r_get_def_country==1) $_GET['country']=$app['default_country']; 		#      > 

if (isset($_GET['country'])) {	#  
	setcookie('country', $_GET['country'], time() + $cooktime);  #86400 * 365
	$_COOKIE['country'] = $_GET['country']; 
} 
$app['current_country']		= $_COOKIE['country'];

$r_get_def_language=0;

#          >  
if (!isset($_COOKIE['language']) && !isset($_GET['language']))  							$r_get_def_language=1;
#           >  
if (isset($_COOKIE['language']) && 
	!in_array( $_COOKIE['language'],$countrym[$app['current_country']]['l'])) 				$r_get_def_language=1;
#         >  
if (isset($_GET['language']) && 
	!in_array( $_GET['language'],$countrym[$app['current_country']]['l'])) 					$r_get_def_language=1;
	#    'default_language' => 'mm',  
if ($r_get_def_language==1) $_GET['language']=$countrym[$app['current_country']]['l'][0]; 	#      >      

if (isset($_GET['language'])) {	#  
	setcookie('language', $_GET['language'], time() + $cooktime);  #86400 * 365
	$_COOKIE['language'] = $_GET['language']; 
} 
$app['current_language'] 	= $_COOKIE['language'];

/*     */

//     (   )
function db_row ($query, $need_log = false) {
	$ret = mysql_query($query);
	$err = mysql_error();
	if (!empty($err) || $need_log) {
		$query=addslashes(stripslashes(trim($query)));
		$err=addslashes(stripslashes(trim($err)));
		$ret2 = mysql_query("INSERT INTO `log` SET `type`='mysql', `value`='$query\r\n$err'");
		$id = mysql_insert_id();  die("Error:Log:$id| $err");
	}	
	if (isset($ret) && !empty($ret)) return mysql_fetch_assoc($ret);
	else return false;
}

//   ,    -       
function db_array ($query, $need_log = false) {
	$ret = mysql_query($query);
	$err = mysql_error();
	$res = Array();
	if (isset($ret) && !empty($ret)) while ($row = mysql_fetch_assoc($ret)) {
		$res[] = $row;
	}
	if (!empty($err) || $need_log) {
		$query=addslashes(stripslashes(trim($query)));
		$err=addslashes(stripslashes(trim($err)));
		$ret2 = mysql_query("INSERT INTO `log` SET `type`='mysql', `value`='$query\r\n$err'");
		$id = mysql_insert_id();  die("Error:Log:$id| $query | $err");
	}
	return $res;
}

//     MySQL
function db_result ($query, $need_log = false) {
	$ret = mysql_query($query);
	$err = mysql_error();
	if (!empty($err) || $need_log) {
		$query=addslashes(stripslashes(trim($query)));
		$err=addslashes(stripslashes(trim($err)));
		$ret2 = mysql_query("INSERT INTO `log` SET `type`='mysql', `value`='$query\r\n$err'");
		$id = mysql_insert_id();  die("Error:Log:$id| $query | $err");
	}
	if (isset($ret) && !empty($ret)) {
		$ret2 = @mysql_result($ret, 0);
		return $ret2;
	}
	else return false;
}

//     MySQL,  true / false
function db_request ($query, $need_log = false) {
	$ret = mysql_query($query);
	$err = mysql_error();
	if (!empty($err) || $need_log) {
		$query=addslashes(stripslashes(trim($query)));
		$err=addslashes(stripslashes(trim($err)));
		$ret2 = mysql_query("INSERT INTO `log` SET `type`='mysql', `value`='$query\r\n$err'");
		$id = mysql_insert_id();  die("Error:Log:$id| $query | $err");
	}
	if (isset($ret) && !empty($ret)) return true;
	else return false;
}

//    ,  id  
function db_insert ($query, $need_log = false) {
	$ret = mysql_query($query);
	$err = mysql_error();
	$id = mysql_insert_id();
	if (!empty($err) || $need_log) {
		$query=addslashes(stripslashes(trim($query)));
		$err=addslashes(stripslashes(trim($err)));
		$ret2 = mysql_query("INSERT INTO `log` SET `type`='mysql', `value`='$query\r\n$err'");
		$id = mysql_insert_id();  die("Error:Log:$id| $query | $err");
	}
	if (isset($ret) && !empty($ret)) return $id;
	else return false;
}

function db_insert_ar ($query, $need_log = false) {
	$ret = mysql_query($query);
	$err = mysql_error();
	$id = mysql_insert_id();
	$ar=mysql_affected_rows();
	$o=['a'=>$ar,'i'=>$id];
	if (!empty($err) || $need_log) {
		$query=addslashes(stripslashes(trim($query)));
		$err=addslashes(stripslashes(trim($err)));
		$ret2 = mysql_query("INSERT INTO `log` SET `type`='mysql', `value`='$query\r\n$err'");
		$id = mysql_insert_id();  die("Error:Log:$id| $query | $err");
	}
	if (isset($ret) && !empty($ret)) return $o;
	else return false;
}

//     .   
$app['log_message_mysql_row'] = 0; // ID   

function f_log ($msg, $type = 'COMMON') {
    global $self, $app;
    $msg = mysql_real_escape_string($msg);
    if (empty($app['log_message_mysql_row']) || $type != 'COMMON') {
        $app['log_message_mysql_row'] = db_insert("INSERT INTO `log` SET
                    `type` = '$type',
                    `self` = '$self',
                    `value`='$msg'");
    } else {
        db_request("UPDATE `log` SET `value` = CONCAT(`value`, '\r\n$msg') WHERE `id` = '{$app['log_message_mysql_row']}'");
    }
}

/*   */

function l($phrase, $data = FALSE) {				// Return phrase on current language
    global $app;
    if (empty($phrase)) return '';
    $content = $phrase; 
	$phrase_sql=mysql_real_escape_string($phrase);	//   ,      
	if ($app['current_language'] != 'en') {
        //  
        $locale = db_row("SELECT * FROM `web_words` WHERE `en` = '$phrase' and `enable`=1");
        if (empty($locale['id'])) {
            db_request("INSERT IGNORE INTO `web_words` SET `mdkey` = MD5('$phrase_sql'), `en` = '$phrase_sql', `enable` = 0");
        } else {
            if (!empty($locale[$app['current_language']])) $content = $locale[$app['current_language']];
        }
    }
    /*
    if ($data !== FALSE && is_array($data)) {		//  
        // Render template tags, for example: [[name]]
        preg_match_all('/\/?\*?\[\[\s*([\w-_]+)\s*\]\]\*?\/?/iu', $content, $matches);
        if (!empty($matches[1])) {
			foreach ($matches[1] as $num => $name) $content = str_replace($matches[0][$num], $data[$name], $content);    
		}
	}
	*/
    return $content;
}

#      
function arrToUpdate($mp) {	//  update users set st=2 where id=100 > arrToUpdate(['t'=>'loans','u'=>['st'=>1],'i'=>100]);
	if (empty($mp['u']) || empty($mp['i']) || empty($mp['t'])) return false;
	$t=$mp['t'];$u=$mp['u'];$i=$mp['i'];
	if (!is_array($u)) return false;
	$idm = is_array($i) ? $i : [$i];
	$imp=[]; foreach ($u as $k=>$v)  $imp[]=$k.'='.$v;
	foreach ($idm as $id) {
		$qw="update $t SET ".implode(',',$imp)." where id=$id";
		db_request($qw);		
	}
	return true;
}

#    
$sbd=''; if (isset($db['sbd'])) $sbd=$db['sbd'].'.';	
function aPgE($v,$tr=false){ 
	aPg('error_msg',$v,$tr); 
}


function doIval($w,$p){
	foreach ($p as $k=>$v) {		
		if (function_exists($v)) $w=$v($w);
	}
	return $w;
}

/**
*     .
*      ->       
* $a->      , $v=>  , $k=>  ,   $a[$k],   $a[].
*  $a
*/
function addToArr($a,$c,$v,$k=null){
	if (empty($a[$c])) $a[$c]=[];
	if (!empty($k)) {
		$a[$c][$k]=$v;
	} else {
		$a[$c][]=$v;
	}
	return $a;
}

function aPg($k,$v,$tr=false) {
	Global $page; 
	if ($tr!=false ) {
		$d=$v;	#        ,       
	} else {
		$d=l($v); 
	}
	if (!isset($page[$k])) $page[$k]=$d;
	else $page[$k].=$d;
};

function testHash(){
	return false;
	$test_hash='7da8515ce442048b';
	if (isset($_COOKIE['auth_hash']) && $_COOKIE['auth_hash']==$test_hash) return true;
	return false;
}

function die2($o,$mp=[]) {
	mysql_close(); die(json_encode($o));
}

function is2($a,$kl){
	$v=$a; foreach ($kl as $k) if (isset($v[$k])) { $v=$v[$k];  } else {  return false; }
	return true;
}