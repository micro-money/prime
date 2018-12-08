<?php

/* ФУНКЦИИ ЗАЩИТЫ ОТ XSS И ШИФРОВАНИЕ */

// Генерация текущего токена подписи запросов (для форм и пр.)

$rnd_hash_for_token = sprintf( '%03x%03x%03x', mt_rand(0, 4096), mt_rand(0, 4096), mt_rand(0, 4096) );
$page['security_token'] = $rnd_hash_for_token . md5($rnd_hash_for_token . intval(time()/60) . $app['key']);


// Проверка валидности входящего токена согласно его сроку жизни и одноразовой возможности использования
function check_token($check_token) {
    global $app;
    $rnd_hash = substr($check_token, 0, 9);
    //echo "\$rnd_hash = $rnd_hash, \$app['token_lifetime'] = {$app['token_lifetime']}<br />";
    // Перебор вариантов валидации токена по временным меткам согласно сроку жизни токена
    for ($i = 0; $i < $app['token_lifetime']; $i ++) {
        $token_calc = $rnd_hash . md5($rnd_hash . (intval(time()/60) - $i) . $app['key']);
        //echo "Вариант токена $i: $token_calc<br />";
        if ($check_token === $token_calc) {
            // Если присутствует сессия, то включаем систему защиты от использования токена 2 раза подряд
            if(session_name()) {
                // Нельзя использовать два раза подряд один и тот же токен.
                if ($_SESSION['last_token'] === $token_calc) return -1;
                $_SESSION['last_token'] = $token_calc;
            }
            return true;
        }
    }
    return false;
}


// Фильтрация входящих данных $_SESSION
if (isset($_SESSION)) foreach ($_SESSION as $k => $v) {
    if ( is_array($v) ) foreach ($v as $k2 => $v2) {
        $_SESSION[$k][$k2] = mysql_real_escape_string( htmlspecialchars( trim( $v2 ) ) );
    } else $_SESSION[$k] = mysql_real_escape_string( htmlspecialchars( trim( $v ) ) );
}

if (isset($_COOKIE)) foreach ($_COOKIE as $k => $v) {
    if ( is_array($v) ) foreach ($v as $k2 => $v2) {
        $_COOKIE[$k][$k2] = mysql_real_escape_string( htmlspecialchars( trim( $v2 ) ) );
    } else $_COOKIE[$k] = mysql_real_escape_string( htmlspecialchars( trim( $v ) ) );
}
if (!isset($sas_work)) {
	// Вложенные массивы тока в один уровень проверяет а все что ниже херачит
	if (isset($_POST) && empty($_POST['frompage'])) foreach ($_POST as $k => $v) {
		if ( is_array($v) ) foreach ($v as $k2 => $v2) {
			$_POST[$k][$k2] = mysql_real_escape_string( htmlspecialchars( trim( $v2 ) ) );
		} else $_POST[$k] = mysql_real_escape_string( htmlspecialchars( trim( $v ) ) );
	}

	if (isset($_GET) && empty($_POST['frompage'])) foreach ($_GET as $k => $v) {
		if ( is_array($v) ) foreach ($v as $k2 => $v2) {
			$_GET[$k][$k2] = mysql_real_escape_string( htmlspecialchars( trim( $v2 ) ) );
		} else $_GET[$k] = mysql_real_escape_string( htmlspecialchars( trim( $v ) ) );
	}
}
