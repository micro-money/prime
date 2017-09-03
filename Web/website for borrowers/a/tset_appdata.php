<?php

$tset_appdata=['gf'=>'sastabV1',				# Базовые настройки для списка контактов 
		'tl'=>	['users_mapdata'=>[],],		
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM users_mapdata md WHERE 1=1 {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'qtl'=>[
			'For user'=>'SELECT {(select)} FROM users_mapdata md WHERE md.user_id={(user_id)} {(where)} {(group)} {(having)} {(order)} {(limit)}',
		],
		# Поля выбора (select fields)
		'dl'=>100,
		'filter'=>[
				'fw'=>['mduid','mddt','mdvol','mddv'], # ,'ulogin','uname','unrc','uemail'
				],
		'paginator'=>'page',  # scroll  page  ,'a.LoanDays','a.create_at','a.CrmStatus','a.cst','a.HowDoYouWantToGetMoney','a.RequestAmount'	
		'fs'=>['mdid','mduid','mddt','mdvol','mddesc','mddv'],	  # ,'ucct'
		'mf'=>[
			'sort'=>['mddt','ucdv'],  // 'ulogin'=>[1,' ASC'],' DESC'	
		]	
	];