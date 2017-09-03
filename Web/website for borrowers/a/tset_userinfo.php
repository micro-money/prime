<?php
$cfsel=''; if ($curr_fil>-1) $cfsel=' AND u.fil='.$curr_fil;
# Мы расписываем какие элементы будут на странице
$t=['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	['users'=>[],],
		'qt'=>"SELECT {(select)} FROM users u WHERE u.id={(user_id)} ",  #  $cfsel
		'mf'=>['viewMode'=>'tabVert',],	
		'fe'=>['ival_sas_sqlm'],
	];

#$ustat=['ulogin','umad'];														# Не редактируемые
$v=['e'=>1];
																																														
$upri=['ulogin'=>$v,'ufil'=>$v,'uname'=>$v,'ugender'=>$v,'ubirthdate'=>$v,'ufnrc'=>$v,'ucity'=>$v,'utownship'=>$v,'uaddress'=>$v,'usocial'=>$v,'usalary'=>$v,'ucname'=>$v,'ucphone'=>$v,'ucrmid'=>$v,'udv'=>$v];						# Основные		
																								
$usec=['ucity','utownship','ustr','ucname','ucphone','usocial'];				# Дополнительные

$fs=$upri; 

$notedit=['ulogin','udv'];
foreach ($notedit as $kn) if (is2($fs,[$kn,'e'])) unset($fs[$kn]['e']);

$t['fs']=$fs;									# С редактированием

$tset_userinfo=$t;

function ival_sas_sqlm($w) {
	$w['sas_sqlm']["m"]=$w['data'][0];
	return $w;
}

?>