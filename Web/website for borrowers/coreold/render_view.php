<?php

/* ВЫВОД ПРЕДСТАВЛЕНИЯ СТРАНИЦЫ */

$content = ob_get_contents(); // Получаем содержимое буфера вывода в переменную
ob_end_clean(); //сбрасываем и выключаем буфер
//header("Content-Type: text/html; charset=UTF-8");

// Вставка шаблона страницы
//dd($content);
e( $page['template'], $content );
//print_r($page);
mysql_close(); die();