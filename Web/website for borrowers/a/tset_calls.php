<?php

$t=['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	['users_calls'=>[],],
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM users_calls uk WHERE 1=1 {(where)} {(group)} {(having)} {(order)} {(limit)}",
		# Поля выбора (select fields)
		'qtl'=>[
			'For user'=>'SELECT {(select)} FROM users_calls uk WHERE uk.uid={(user_id)} {(where)} {(group)} {(having)} {(order)} {(limit)}',
		],
		'dl'=>15,
		'setl'=>[
			'My calls'=>['(w|n)'=>'ukcid|'.$user['id'].'|0'],
			'GROUP BY DAYS'=>['(g)'=>'1'],
			],
		// uk.d between '{(day)}' date_add('{(day)}', INTERVAL 24 hour)
		// '(w|n)'=>'ukdv|wt1|e','(wt|wt1)'=>'day|2017.06.18'
		// uc.dv>'{(day)}' and uc.dv<date_add('{(day)}', INTERVAL 24 hour)
		'wtmpl'=>[
				'wt1'=>['t'=>" between {(day)} AND date_add({(day)}, INTERVAL 24 hour)",'p'=>['day'=>'s']],
				],			
		'setd_fs'=>[
			'GROUP BY DAYS'=>['ukdvd'=>['q'=>'DATE_FORMAT(uk.dv,\'%Y.%m.%d\')'],'ukkol'=>['q'=>'count(*)'],],
		],
		'setd'=>'My calls',
		'paginator'=>'page',  # cname  ,'ukuid'=>['h'=>1]
		'fs'=>['ukid','ukuid'=>['h'=>1],'ukuname','ukdt'=>['h'=>1],'ukcid'=>['h'=>1],'ukdid'=>['h'=>1],'ukcst','ukcname','ukdv','ukdocn'],	
		'filter'=>[
				'fw'=>['ukcst','ukdt','ukdv'], # ,'ulogin','uname','unrc','uemail'
				],
		'mf'=>[
			'sort'=>['ukdv','ukkol','ukdvd'],  
			#'etd_ukdocn'=>'$href="/a/userview/?id=".$rv["ukuid"]; if ($rv["ukdt"]==1) $href="/a/leadview/?id=".$rv["ukdid"]; if ($rv["ukdt"]==2) $href="/a/loanview/?id=".$rv["ukdid"]; $tdf="<a type=\'button\' title=\'View more details\' class=\'btn btn-info btn-sm\' href=\'".$href."\'>".$tdf."</a>";',	 #href=\'/a/callview/?id=".$rv["ukid"]."\'
			'etd_ukdocn'=>'$href="/a/callview/?id=".$rv["ukid"]; $tdf="<a type=\'button\' title=\'View more details\' class=\'btn btn-info btn-sm\' href=\'".$href."\'>".$tdf."</a>";',	 #href=\'/a/callview/?id=".$rv["ukid"]."\'

			'etd_ukkol'=>'$tdf="<a type=\'button\' title=\'View more details\' class=\'btn btn-info btn-sm\' onclick=\'sas_custom_seek(".genJson(["sas_el"=>$sas_el,"Day"=>$rv["ukdvd"]]).");\'>".$tdf."</a>";',
			#'etd_ukkol'=>'$tdf="<a type=\'button\' title=\'View more details\' class=\'btn btn-info btn-sm\' href=\'/a/callview/?id=".$rv["ukdvd"]." \'>".$tdf."</a>";',	
		]	
	];

/*
if (isset($id)) {	# Если есть id клиета
	$on='For the current customer'; $t['setl'][$on]=['(w|n)'=>'ukuid|'.$id.'|0'];
}

if (isset($lid)) {	# Если есть номер лида 
	$on='For the current lead'; $t['setl'][$on]=['(w|n)'=>'ukdt|ukdid','(w|v)'=>'1|'.$lid,'(w|s)'=>'0|0'];
}

if (isset($did)) {	# Если есть номер сделки 
	$on='For current Loan'; $t['setl'][$on]=['(w|n)'=>'ukdt|ukdid','(w|v)'=>'2|'.$did,'(w|s)'=>'0|0'];
}

if (isset($on)) $t['setd']=$on;
*/

$tset_calls=$t;