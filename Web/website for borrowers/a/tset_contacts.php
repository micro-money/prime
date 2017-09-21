<?php

$tset_contacts=['gf'=>'sastabV1',				#      
		'tl'=>	['users_contacts'=>[]],	
		#   (qwery template)  as mdv   users u, uc.uid=u.id 
		'qt'=>"SELECT {(select)} FROM users_contacts uc WHERE 1=1 {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'qtl'=>[
			'For user'=>'SELECT {(select)} FROM users_contacts uc WHERE uc.uid={(user_id)} {(where)} {(group)} {(having)} {(order)} {(limit)}',
		],
		#   (select fields)
		'dl'=>10,
		'filter'=>[
				'fw'=>['ucuid','uccval','ucdv'], 
				],
		'paginator'=>'page',  # scroll  page  
		'fs'=>['uccr','uccp','uccname','uccval','uccs','ucdv'],	  
		'mf'=>[
			'sort'=>['uccr','ucdv'],  
		]	
	];
	