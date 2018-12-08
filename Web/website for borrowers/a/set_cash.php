<?php

include($dr.'/a/tset_cashs.php');	$t=$tset_cashs;	# Лиды

$t['htname']='Cash details';						# Заголовок для закладки
$t['mf']=['viewMode'=>'tabVert',];					# Вертикалим таблицу
$t['tkol']=1;										# Сброс запроса на общее количество строк
$t['wd']=['fd(w|n)'=>'mid|'.$cash.'|0']; 			# Ставим условие запроса конкретного лида	
$t['fs']=$t['setd_fs']['For user']; $t['fs']['muid']=['h'=>1];

if (empty($t['fe'])) $t['fe']=[];
$t['fe'][]='ival_muid';								# Подставляем id клиента для которого будет подгружена информация

$fdata=$t; 

function ival_muid($w) {
	$w['sas_sqlm']["user_id"]=$w['data'][0]["muid"];
	return $w;
}

