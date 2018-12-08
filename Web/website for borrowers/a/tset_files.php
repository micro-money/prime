<?php

$tset_files=['gf'=>'sastabV1',				# Базовые настройки для списка контактов 
		'tl'=>	['users_files'=>[],	],
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM users_files uf WHERE 1=1 {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'qtl'=>[
			'For user'=>'SELECT {(select)} FROM users_files uf WHERE uf.uid={(user_id)} {(where)} {(group)} {(having)} {(order)} {(limit)}',
		],
		# Поля выбора (select fields)
		'dl'=>50,
		'fs'=>['ufid','ufft','uffp','ufac','ufdv','ufh'=>['h'=>1]],	  # ,'ucct'	
	];
