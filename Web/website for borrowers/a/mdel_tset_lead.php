<?php
# ы апиываем какие элементы будут на танице
$tset_lead=['gf'=>'sastabV1',				# : мя функции контуктоа элемента
		'tl'=>	['leads'=>[]],	
		# аблон запоа (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM leads ld WHERE ld.id=".$lid,
		# оля выбоа (select fields)
		'fs'=>['lduid'=>['h'=>1],'ldhow'=>['e'=>1],'ldramount'=>['e'=>1],'ldrdays'=>['e'=>1],'ldbank'=>['e'=>1],'ldfacc'=>['e'=>1],'ldst'=>['e'=>1],'lddv'],	
		'mf'=>['viewMode'=>'tabVert',
		],
	];