<?php
# Мы расписываем какие элементы будут на странице
$tset_lead=['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	['leads'=>[]],	
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM leads ld WHERE ld.id=".$lid,
		# Поля выбора (select fields)
		'fs'=>['lduid'=>['h'=>1],'ldhow'=>['e'=>1],'ldramount'=>['e'=>1],'ldrdays'=>['e'=>1],'ldbank'=>['e'=>1],'ldfacc'=>['e'=>1],'ldst'=>['e'=>1],'lddv'],	
		'mf'=>['viewMode'=>'tabVert',
		],
	];