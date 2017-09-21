<?php
/*
 :
1)        . 
	  Id     ,    IP    >  .
2)     . 
	      
        .
*/

//   ==========
$log = ""; $user = []; # ,     

$login 		 = false; 	if (isset($_POST['login'])) 		$login = $_POST['login'];						#  OK
$pass 		 = false; 	if (isset($_POST['pass'])) 			$pass = $_POST['pass'];							#  OK
$act 		 = false; 	if (isset($_GET['act'])) 			$act = $_GET['act'];        					#    
$auth_hash 	 = false; 	if (isset($_COOKIE['auth_hash']))  	$auth_hash = $_COOKIE['auth_hash'];				#  OK
$ses_user_id = false; 	if (isset($_SESSION['user_id']))  	$ses_user_id = intval($_SESSION['user_id']);	# OK
$ses_ip 	 = false; 	if (isset($_SESSION['ip']))  		$ses_ip = $_SESSION['ip'];

$log .= "ses_user_id = $ses_user_id  , ses_ip = $ses_ip<br />";

if ($act == "quit") {
	$log .= "  (ip  )<br />";
	session_destroy();											#  
	setcookie('auth_hash', '', time() - 3600, '/', false); 		#  
} else {
	if (!empty($ses_user_id) && $ses_user_id>0) {
		$log .= "   <br />";   
		$user = db_row("SELECT * FROM `users` WHERE `id` = '$ses_user_id'");
	} else {
		if (!empty($auth_hash)) {
			$log .= " -    ' ' $auth_hash<br />";
			$user = db_row("SELECT * FROM `users` WHERE `auth_hash` = '".mysql_real_escape_string($auth_hash)."'");
			if (!empty($user['id'])) {
				$_SESSION['user_id'] = $user['id'];				#    ID  IP 
				$_SESSION['ip'] = $ip;
			}		
		}
	}
}

#           
if (!empty($login) && !empty($pass)) {
	$log .= " <br />";
    $user = db_row("SELECT * FROM `users` WHERE `login` ='".mysql_real_escape_string($login)."' AND `pass` ='".sha1($pass)."'");
    if (!empty($user['id'])) {
        $log .= " <br />";
        if (!empty($user['id'])) {
            $_SESSION['user_id'] = $user['id'];
            //      ' '
            if (isset($_POST['remember']) &&  isset($_POST['remember']) == "on") {
                if (!empty($user['auth_hash'])) $auth_hash = $user['auth_hash'];
                else $auth_hash = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));
                setcookie('auth_hash', $auth_hash, time() + $cooktime, '/', false); 
                db_request("UPDATE `users` SET `auth_hash` = '".mysql_real_escape_string($auth_hash)."' WHERE `id` ='{$user['id']}'");
            }
        }
        $_SESSION['ip'] = $ip;
    } else aPgE("<br>Unfortunately, authentication fails. Check the login and password!");
} else {
	$log .= " .";
	if ($selfc=='/login.php' && (!empty($login) || !empty($pass))) {
		aPgE("Please enter your login & password!");
	}
}


#       .       >      .
if (isset($user['id']) && $user['role']=='') {
	$ccid=$countrym[$app['current_country']]['f'];  #echo "[$ccid|{$user['fil']}|{$user['a_od']}|{$user['a_cd']}]";
	if ($ccid!=$user['fil']) {
		if (($user['a_od']+$user['a_cd'])==0) {
			#    ..  
			db_request("UPDATE users set fil=$ccid WHERE id={$user['id']}");
			if ($user['a_lid']>0) {
				db_request("UPDATE leads users set fil=$ccid,cfil=$ccid WHERE id={$user['a_lid']}");
			}
		} else {
			#           
			db_request("UPDATE leads users set cfil=$ccid WHERE id={$user['a_lid']}");
			
		}
	}
}