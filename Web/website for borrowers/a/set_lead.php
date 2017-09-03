<?php

include($dr.'/a/tset_leads.php');	$t=$tset_leads;	# Лиды

$t['htname']='Lead details';						# Заголовок для закладки
$t['mf']=['viewMode'=>'tabVert',];					# Вертикалим таблицу
$t['tkol']=1;										# Сброс запроса на общее количество строк
$t['wd']=['fd(w|n)'=>'ldid|'.$lid.'|0']; 			# Ставим условие запроса конкретного лида

if (empty($t['fe'])) $t['fe']=[];					# Подставляем id клиента для которого будет подгружена информация
$t['fe'][]='ival_lduid';

$t['fs']=['lduid'=>['h'=>1],'ldfil','ldhow'=>['e'=>1],'ldramount'=>['e'=>1],'ldrdays'=>['e'=>1],'ldppd'=>[],'ldbank'=>['e'=>1],'ldfacc'=>['e'=>1],'ldcst'=>[],'ldnote'=>['e'=>1],'ldst','ldcl'=>[],'ldcrmid','lddv'];
# Убираем кнопку обработать лид для всех форм кроме просмотра лида
if (substr($selfc, 0, 12)!='/a/leadview/') unset($t['fs']['ldcl']);

if ($user['role']=='super') $t['fs']['ldppd']['e']=1;

$fdata=$t; 

function ival_lduid($w){
	$w['sas_sqlm']["user_id"]=$w['data'][0]["lduid"];
	return $w;
}