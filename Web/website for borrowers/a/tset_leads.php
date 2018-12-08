<?php
$cfn='For curr country ';
$cwsetn='Leads in Call Work';

#$prev1='print_r($dpel[$el]); die();';

$cfsel=''; if ($curr_fil>-1) $cfsel=' AND ld.fil='.$curr_fil;
$tset_leads=['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	['leads'=>[
				'c'=>['act'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],],
				],],
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM leads ld WHERE 1=1 $cfsel {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'qtl'=>[
			'For user'=>"SELECT {(select)} FROM leads ld WHERE ld.uid={(user_id)} {(where)} {(group)} {(having)} {(order)} {(limit)}",
		],
		# Поля выбора (select fields)
		'dl'=>10,
		'setl'=>[
			$cwsetn=>['(w|n)'=>'ldcid','(w|s)'=>'2','(w|v)'=>'0'],
			'Fully completed leads: Ready for calling'=>['(w|n)'=>'lddv|ldst|lda_rst','(w|s)'=>'2|0|4','(w|v)'=>'wt1|2|0,2','(wt|wt1)'=>'day|-30'],
			'Fully completed leads: Wait for recall'=>['(w|n)'=>'lddv|ldst|lda_rst','(w|s)'=>'2|0|0','(w|v)'=>'wt1|2|1','(wt|wt1)'=>'day|-30'],
			'Fully completed leads: Аfter waiting for recall'=>['(w|n)'=>'lddv|ldst|lda_rst','(w|s)'=>'2|0|0','(w|v)'=>'wt1|2|2','(wt|wt1)'=>'day|-30'],
			'For Last Mounth'=>['(w|n)'=>'lddv|wt1|2','(wt|wt1)'=>'day|-30'],
			'For Last Week'=>['(w|n)'=>'lddv|wt1|2','(wt|wt1)'=>'day|-7'],
			'For Last Day'=>['(w|n)'=>'lddv|wt1|2','(wt|wt1)'=>'day|-1'],
			],
		'wtmpl'=>[
				'wt1'=>['t'=>'date_add(now(), interval {(day)} day) ','p'=>['day'=>'i']],
				],
		'filter'=>[
				'fw'=>['ldid','lduid','ldst','ldcst','ldfil','ldcrmst','lddv'], # ,'ulogin','uname','unrc','uemail'  ,'uallseek','bacc'
				],
		'paginator'=>'page',  # scroll  page  ,'a.LoanDays','a.create_at','a.CrmStatus','a.cst','a.HowDoYouWantToGetMoney','a.RequestAmount'
		# 'fs'=>['ldid','ulogin','uname','ldst','lddv','action'],	
		'fs'=>['ldid'=>[],'ldfil'=>['h'=>1],'lduname'=>[],'ldst'=>[],'ldcst'=>[],'lddv'=>[],'action'=>[]],	
		'setd_fs'=>[
			$cwsetn=>['ldid'=>[],'lduname'=>[],'ldst'=>[],'ldcst'=>[],'lducid'=>[],'lddv'=>[],'action'=>[]],
		],
		'mf'=>[
			'sort'=>['ldid','lddv'],  // 'ulogin'=>[1,' ASC'],' DESC'
			'etd_action'=>'$tdf="<a type=\'button\' title=\'View all lead details\' class=\'btn btn-info btn-sm\' href=\'/a/leadview/?id=".$rv["ldid"]."\'><span class=\'glyphicon glyphicon-edit\'></span> Details</a>";',	
		],
	]; 
	

# md(setd)