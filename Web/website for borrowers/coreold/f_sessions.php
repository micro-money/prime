<?php
/*
Как авторизуемся:
1) Если у клиента есть сессия то по ней. 
	В сессии Id пользователя по нему загружаем , при этом сверяем IP если они поменялись > убиваем сесию.
2)Если сессиии нет то по хэшу. 
	По хэшу ищем пользователя и грузим его
Если нет ни сессиии ни хэша тогда авторизации нет.
*/

// САМЫЕ ВАЖНЫЕ ==========
$log = ""; $user = []; # Массив, содержащий инфо о текущем пользователе

$login 		 = false; 	if (isset($_POST['login'])) 		$login = $_POST['login'];						# Экран OK
$pass 		 = false; 	if (isset($_POST['pass'])) 			$pass = $_POST['pass'];							# Экран OK
$act 		 = false; 	if (isset($_GET['act'])) 			$act = $_GET['act'];        					# Не уч в запросах
$auth_hash 	 = false; 	if (isset($_COOKIE['auth_hash']))  	$auth_hash = $_COOKIE['auth_hash'];				# Экран OK
$ses_user_id = false; 	if (isset($_SESSION['user_id']))  	$ses_user_id = intval($_SESSION['user_id']);	# OK
$ses_ip 	 = false; 	if (isset($_SESSION['ip']))  		$ses_ip = $_SESSION['ip'];

$log .= "ses_user_id = $ses_user_id  , ses_ip = $ses_ip<br />";

if ($act == "quit") {
	$log .= "сессия разорвана (ip или выход)<br />";
	session_destroy();											# Убиваем куку
	setcookie('auth_hash', '', time() - 3600, '/', false); 		# Убиваем ХЭШ
} else {
	if (!empty($ses_user_id) && $ses_user_id>0) {
		$log .= "в сессии присутствует логин<br />";   
		$user = db_row("SELECT * FROM `users` WHERE `id` = '$ses_user_id'");
	} else {
		if (!empty($auth_hash)) {
			$log .= "есть кукис-запись с предыдущей авторизации 'Запомнить меня' $auth_hash<br />";
			$user = db_row("SELECT * FROM `users` WHERE `auth_hash` = '".mysql_real_escape_string($auth_hash)."'");
			if (!empty($user['id'])) {
				$_SESSION['user_id'] = $user['id'];				# Переписываем в сесиию ID и IP пользователя
				$_SESSION['ip'] = $ip;
			}		
		}
	}
}

# Если у нас режим авторизации это можно сделать только из 
if (!empty($login) && !empty($pass)) {
	$log .= "Режим авторизации<br />";
    $user = db_row("SELECT * FROM `users` WHERE `login` ='".mysql_real_escape_string($login)."' AND `pass` ='".sha1($pass)."'");
    if (!empty($user['id'])) {
        $log .= "соответствие найдено<br />";
        if (!empty($user['id'])) {
            $_SESSION['user_id'] = $user['id'];
            //Запоминание юзера в случае установленной опции 'Запомнить меня'
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
	$log .= "Страница авторизации.";
	if ($selfc=='/login.php' && (!empty($login) || !empty($pass))) {
		aPgE("Please enter your login & password!");
	}
}


# Если текущая страна не привязана к клиенту. и у клиента не было сделок > мы его переносим в другую страну.
if (isset($user['id']) && $user['role']=='') {
	$ccid=$countrym[$app['current_country']]['f'];  #echo "[$ccid|{$user['fil']}|{$user['a_od']}|{$user['a_cd']}]";
	if ($ccid!=$user['fil']) {
		if (($user['a_od']+$user['a_cd'])==0) {
			# Меняем клиенту филила т.к. он новичек
			db_request("UPDATE users set fil=$ccid WHERE id={$user['id']}");
			if ($user['a_lid']>0) {
				db_request("UPDATE leads users set fil=$ccid,cfil=$ccid WHERE id={$user['a_lid']}");
			}
		} else {
			# Обновляем только куки страну дабы дать манигеру понять что тут муть
			db_request("UPDATE leads users set cfil=$ccid WHERE id={$user['a_lid']}");
			
		}
	}
}