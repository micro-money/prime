<?php 

$cfsel=''; if ($curr_fil>-1) $cfsel=' AND lo.fil='.$curr_fil;
$tset_loans=['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	['loans'=>[
				'c'=>['empty'=>['p'=>'empty','q'=>' '],'act'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],],
				],],
		'qt'=>"SELECT {(select)} FROM loans lo WHERE 1=1 $cfsel {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'qtl'=>[
			'For user'=>'SELECT {(select)} FROM loans lo WHERE lo.uid={(user_id)} {(where)} {(group)} {(having)} {(order)} {(limit)}',
		],
		# Поля выбора (select fields)
		'dl'=>10,
		'setl'=>[
			'Pipeline loans'=>['(w|n)'=>'lost|20|0'],
			'Diff between Calc and CRM sendmoney'=>['(w|n)'=>'empty|wt1|e'],
			#$j1=>['(g)'=>'1'],
			],
		'wtmpl'=>[	# Группировка всегда сначала новые
				'wt1'=>['t'=>'(lo.UsrMoneySendedAmount!=lo.a_fmsa or lo.UsrMoneySendedDate!=lo.a_fmsd) AND lo.a_rdate>"2001-01-01"'],	# Только nrc + банк которые еще не проверялись на документ
				],
		#'setd'=>'Diff between Calc and CRM sendmoney',	
		'filter'=>[
				'fw'=>['loid','louid','lowa','lost','lospd','loot','lodv'], 
				],
		'paginator'=>'page',  # scroll  page   
		'fs'=>['loid','lofil'=>['h'=>1],'louname','lowa','loterm','lost','lodv','action'],	
		'setd_fs'=>[
			'For user'=>['lolid'=>[],'louid'=>[],'lofil'=>[],'loracc'=>[],'lora'=>[],'lowa'=>[],'lorterm'=>[],'loterm'=>[],'loppd'=>[],'lopa'=>[],'lomsa'=>[],'lomsd'=>[],'lofmsa'=>[],'lofmsd'=>[],'lodbody'=>[],'lodperc'=>[],'lototal'=>[],'loot'=>[],'loot'=>[],'lordate'=>[],'lobid'=>[],'lobacc'=>[],'lospd'=>[],'lost'=>[],'lonote'=>['e'=>1],'locrmiu'=>[],'locrmid'=>['h'=>1],'lodv'=>[]],
			],
		'sfs'=>[
			'crmmsa'=>['fn'=>'CRM Money Send Amount','q'=>'sum(lo.UsrMoneySendedAmount)','ef'=>'dec'],
			'cashmsa'=>['fn'=>'Cash Money Send Amount','q'=>'sum(lo.a_fmsa)','ef'=>'dec'],
			'maxlt'=>['fn'=>'Max Term','q'=>'max(lo.UsrTerm)'],
			'minlt'=>['fn'=>'Min Term','q'=>'min(lo.UsrTerm)'],
			],
		'mf'=>[
			'sort'=>['ldid','lodv'],  
			'etd_action'=>'$tdf="<a type=\'button\' title=\'View all loan details\' class=\'btn btn-info btn-sm\' href=\'/a/loanview/?id=".$rv["loid"]."\'><span class=\'glyphicon glyphicon-edit\'></span> Details</a>";',	
		]	
	]; 