<?php
//require_once (MC_ROOT . '/vendor/alexkonov/mailer/class/mailer.php');
/* Регистрация, логин и сессии авторизации на сайте */

// Функция проверки авторизации (возвращает роль пользователя, либо true - если роль не определена)
function auth(){
    global $user;
    if (empty($user['id'])) return false;
    return ($user['role']) ?: true;
}

// Функция поиска аккаунта, соответствующего паре [логин-пароль]
function search_account($acc_login, $acc_pass) {
    global $user, $page, $log;
    $acc_pass_hash = sha1($acc_pass);
    $log .= "search_account($acc_login, $acc_pass) sha1 -> $acc_pass_hash<br />";
    $user = db_row("SELECT * FROM `users` WHERE `login` ='$acc_login' AND `pass` ='$acc_pass_hash'");
    if (!empty($user['id'])) {
        if (empty($user['activate_code'])) return true;
        else $page['error_msg'] = "Unfortunately, your account has not been activated! Please check your email and activate your account, by clicking on the link we have sent you a letter";
    }
    $user = array();
    return false;
}

$user = [];   // Массив, содержащий инфо о текущем пользователе
$s_id = [];		# Вся инфа на импорт 

$act 	= false; 		if (isset($_GET['act'])) 			$act = $_GET['act']; 

$login 	= false; 		if (isset($_POST['login'])) 		$login = $_POST['login'];
$pass 	= false; 		if (isset($_POST['pass'])) 			$pass = $_POST['pass'];


$register 	= false; 	if (isset($_POST['register'])) 		$register = intval($_POST['register']); // флаг данных из формы регистрации
$passchange = false; 	if (isset($_POST['passchange']))  	$passchange = intval($_POST['passchange']); // флаг смены пароля

// Данные для редактирования аккаунта
$edit 		= false; 	if (isset($_POST['edit'])) 			$edit = intval($_POST['edit']); 		// флаг редактирования
$user_id 	= false; 	if (isset($_POST['user_id'])) 		$user_id = intval($_POST['user_id']);
$user_activate = false; if (isset($_POST['user_activate'])) $user_activate = $_POST['user_activate'];

if (empty($user_id) && isset($_GET['user_id'])) 			$user_id = intval($_GET['user_id']);
if (empty($user_activate) && isset($_GET['user_activate'])) $user_activate = $_GET['user_activate'];

$user_login = false; 	if (isset($_POST['user_login']))  	$user_login = $_POST['user_login'];
$user_pass 	= false; 	if (isset($_POST['user_pass'])) 	$user_pass = $_POST['user_pass'];
$user_email = false;	if (isset($_POST['user_email'])) 	$user_email = $_POST['user_email'];
$user_name 	= false; 	if (isset($_POST['user_name'])) 	$user_name = $_POST['user_name'];
$user_phone = false; 	if (isset($_POST['user_phone'])) 	$user_phone = $_POST['user_phone'];

$user_phone = preg_replace('/[^\d]+/', '', $user_phone);

// Хеш запоминания юзера (галочка Запомнить меня при авторизации
$auth_hash = false; if (isset($_COOKIE['auth_hash']))  $auth_hash = $_COOKIE['auth_hash'];

$log = "";
$ses_log = "";
$ses_pass = "";
$ses_ip = "";

// Запуск сессии
$ses_user_id = false; 	if (isset($_SESSION['user_id']))  	$ses_user_id = $_SESSION['user_id'];
$ses_ip = false; 		if (isset($_SESSION['ip']))  		$ses_ip = $_SESSION['ip'];

$log .= "ses_user_id = $ses_user_id  , ses_ip = $ses_ip<br />";

if ($act == "quit") {
	$log .= "сессия разорвана (ip или выход)<br />";
	session_destroy();	#$_SESSION=[];
	
	#setcookie('auth_hash', '', -1);
	setcookie('auth_hash', '', time() - 3600, '/', false); 
	//header("Location: login.php");	
}

if (!empty($ses_user_id)) { // в сессии присутствует id пользователя
    $log .= "в сессии присутствует логин<br />";
    if ($act != "quit") { // ip не изменился, попытка выхода не предпринималась  $ses_ip == $ip && 
        // ищем в БД соответствующий аккаунт
        #echo "[here|$ses_user_id|$auth_hash]";
		if (!empty($ses_user_id)) $user = db_row("SELECT * FROM `users` WHERE `id` = '$ses_user_id'");
    } else {
        /*
		$log .= "сессия разорвана (ip или выход)<br />";
        session_destroy();	#$_SESSION=[];
		
        #setcookie('auth_hash', '', -1);
		setcookie('auth_hash', '', time() - 3600, '/', false); 
        //header("Location: login.php");
		*/
	}
} else if (!empty($auth_hash)) {
	$log .= "есть кукис-запись с предыдущей авторизации 'Запомнить меня' $auth_hash<br />";
    $user = db_row("SELECT * FROM `users` WHERE `auth_hash` = '$auth_hash'");
    if (!empty($user['id'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['ip'] = $ip;
    }
}

if (!empty($user_id) && !empty($user_activate)) {
    
	$user_info = db_row("SELECT * FROM `users` WHERE `id` = '$user_id'");
    if (!empty($user_info['id'])) {
        if ($user_activate == $user_info['activate_code'] && !empty($user_info['activate_code'])) {
            if (db_request("UPDATE `users` SET `activate_code` = 0 WHERE `id` = '$user_id'"))
                $page['success_msg'] = "Your personal account successfully activated!";
            else $page['error_msg'] = "An activation error";
        } else if (empty($user_info['activate_code'])) {
            $page['error_msg'] = "This account is already activated before you";
        } else $page['error_msg'] = "Error! Invalid activation code";
    } else $page['error_msg'] = "Error! Could not find user with id";
} else if ($act == "register") {
    
	$log .= "поступили данные для регистрации нового аккаунта<br />";
    /* Проверка на заполнение всех обязательных полей: ненужное закоментировано */
    if ($_POST['agree'] != 'on') $page['error_msg'] .= "<br>It is necessary to get acquainted with the site rules and tick!";
    if (empty($user_name)) $page['error_msg'] .= "<br>Not Specified name!";
    if (!empty($user_phone) && !preg_match("|^[0-9]{10}$|i", $user_phone))
        $page['error_msg'] .= "<br>An error in the phone number! You must enter 10 digits without spaces.";
    if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $user_email))
        $page['error_msg'] .= "<br>Missing or incorrectly specified E-mail";
    else {
        // проверяем, не было ли уже зарегистрировано такого e-mail:
        $user_email_search = db_result("SELECT COUNT(*) FROM `users` WHERE `email` = '$user_email'");
        if (!empty($user_email_search))
            $page['error_msg'] .= "<br>This E-mail has already been used to register on our website!"
                . "If you have forgotten your password please use the password recovery form";
    }
    //$user_login = $user_email; // Если в качестве логина выступает E-mail
    if (empty($page['error_msg'])) {
        // Генерация случайного хэша
        $user_activate = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));
        // Генерация случайного пароля
        $user_pass = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));
        $user_pass_hash = sha1($user_pass);
        // Сохраняем юзера
        $user_id = db_insert("INSERT INTO `users` SET
                                    `login` = '$user_login',
                                    `pass` = '$user_pass_hash',
                                    `email` = '$user_email',
                                    `name` = '$user_name',
                                    `phone` = '$user_phone',
                                    `activate_code` = '$user_activate'");


        /*mail(
            $user_email,
            "Регистрация {$app['name']}",
            r('emails/register.html', [
                'domain' => $app['domain'],
                'user_id' => $user_id,
                'user_activate' => $user_activate,
                'user_pass' => $user_pass
            ]),
            "From: {$app['name']}<{$app['email']}>\r\nContent-type: text/html; charset=utf-8\r\n"
        );*/

        $page['success_msg'] .= "<strong>Регистрация прошла успешно!</strong><br/>На Ваш E-mail отправлено письмо cо ссылкой для активации личного кабинета";
    }
} else if ($act == "recovery") {
    
	$log .= "режим восстановления пароля<br />";
    if (!empty($user_id)) {
        // Получили данные для смены пароля
        $user = db_row("SELECT * FROM `users` WHERE `id` = '$user_id'");
        if (empty($user['activate_code']) || empty($user_activate) || $user_activate != $user['activate_code']) {
            if (!empty($user_pass)) {
                $user_pass_hash = sha1($user_pass);
                if (db_request("UPDATE `users` SET `pass` = '$user_pass_hash', `activate` = 0
                                WHERE `id` = '{$user['user_id']}'"))
                    $page['success_msg'] .= "<strong>Пароль успешно изменен!</strong>"
                        . "Вы можете войти на сайт с новым паролем";
            } else $page['error_msg'] = "Пожалуйста, введите пароль!";
        } else $page['error_msg'] .= "<br>Ошибка кода активации!"
            . "Проверьте корректность вставки ссылки из E-mail с инструкцией по восстановлению!";
    } else if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $user_email)) {
        $page['error_msg'] .= "<br>Missing or incorrectly specified E-mail";
    } else {
        // проверяем, не было ли уже зарегистрировано такого e-mail:
        $user_email_search = db_result("SELECT COUNT(*) FROM `users` WHERE `email` = '$user_email'");
        if (empty($user_email_search)) {
            // Генерация случайного хэша
            $user_activate = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));
            if (db_result("UPDATE `users` SET `activate_code` = '$user_activate'
                            WHERE `id`='{$user['user_id']}'")) {

                /*mail(
                    $user_email,
                    "Восстановление доступа к {$app['name']}",
                    r('emails/recovery.html', [
                        'domain' => $app['domain'],
                        'user_id' => $user_id,
                        'user_activate' => $user_activate,
                        'user_pass' => $user_pass
                    ]),
                    "From: {$app['name']}<{$app['email']}>\r\nContent-type: text/html; charset=utf-8\r\n"
                );*/

                $page['success_msg'] .= "<strong>Письмо успешно отправлено на $user_email</strong><br />"
                    . "Проверьте свой E-mail и следуйте инструкциям для восстановления пароля";
            }
        } else $page['error_msg'] .= "<br>Unfortunately, we can not find any account associated with the E-mail!";
    }
} else if ($act == "edit" || $act == "add") {
   
	$log .= "Режим редактирования данных аккаунта<br />";
    if (!empty($user['id'])) {
        if (!empty($passchange)) {
            // Для смены пароля нужно ввести старый пароль. Проверяем его правильность
            //if ($pass == $user['user_pass']) {
                if (!empty($user_pass)) {
                    $user_pass_hash = sha1($user_pass);
                    if (db_request("UPDATE `users` SET `pass` = '$user_pass_hash'
                                    WHERE `id` = '{$user['id']}'"))
                        $page['success_msg'] = "Пароль успешно изменен!";
                    else $page['error_msg'] .= "<br>Произошла ошибка при сохранении нового пароля!";
                } else $page['error_msg'] .= "<br>Не был введен новый пароль!";
            //} else $page['error_msg'] .= "<br>Старый пароль введен неверно!";
        } else {
            if (empty($user_name)) $page['error_msg'] .= "<br>Not Specified name!";
            //if (empty($user_login)) $page['error_msg'] .= "<br>Не указан логин!";
            else if (!empty($user_login)) {
                $user_login_search = db_result("SELECT COUNT(*) FROM `users`
                                                WHERE `login` = '$user_login' AND `id` <> '{$user['id']}'");
                if ($user_login_search) $page['error_msg'] .= "<br>This login is already taken!<br />";
            }
            if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $user_email))
                $page['error_msg'] .= "<br>Missing or incorrectly specified E-mail<br />";
            if (!empty($user_phone) && !preg_match("|^[0-9]{10}$|i", $user_phone))
                $page['error_msg'] .= "<br>An error in the phone number! You must enter 10 digits without spaces.<br />";
            if (empty($page['error_msg'])) {
                if (db_request("UPDATE `users` SET
                                `name` = '$user_name',
                                `login` = '$user_login',
                                `email` = '$user_email',
                                `phone` = '$user_phone' 
                                WHERE `id` = '{$user['id']}'"))
                    $page['success_msg'] = "Information edited successfully!";
                else $page['error_msg'] .= "<br>Error saving information!";
            }
        }
        // Перезапрашиваем заново из базы результат
        $user = db_row("SELECT * FROM `users` WHERE `id` = '{$user['id']}'");
    } else $page['error_msg'] .= "<br>Account Editing rejected because no authorization";
} else if (!empty($login) || !empty($pass)) {
   
	$log .= "Режим авторизации<br />";
    if (empty($login) && !empty($pass)) $page['error_msg'] = "Please enter your login!";
    else if (!empty($login) && empty($pass)) $page['error_msg'] = "Please enter your password!";
    if (empty($page['error_msg']) && search_account($login, $pass)) {
        $log .= "соответствие найдено<br />";
        if (!empty($user['id'])) {
            $_SESSION['user_id'] = $user['id'];
            //Запоминание юзера в случае установленной опции 'Запомнить меня'
            if (isset($_POST['remember']) &&  isset($_POST['remember']) == "on") {
                if (!empty($user['auth_hash'])) $auth_hash = $user['auth_hash'];
                else $auth_hash = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));
                setcookie('auth_hash', $auth_hash, time() + $cooktime, '/', false); 
                db_request("UPDATE `users` SET `auth_hash` = '$auth_hash' WHERE `id` ='{$user['id']}'");
            }
        }
        $_SESSION['ip'] = $ip;
    } else if (empty($page['error_msg']))
        $page['error_msg'] .= "<br>Unfortunately, authentication fails. Check the login and password!";
} else $log .= "Сессия пуста, не предпринимается никаких действий<br />";