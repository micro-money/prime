<?php 

$tset_cashs=['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	['money'=>[
				'c'=>['act'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],],
				],],
		'qt'=>"SELECT {(select)} FROM money m WHERE 1=1 {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'qtl'=>[
			'For user'=>'SELECT {(select)} FROM money m WHERE m.uid={(user_id)} {(where)} {(group)} {(having)} order by m.operday {(limit)}',
			'For loan'=>'SELECT {(select)} FROM money m WHERE m.loan={(loan)} {(where)} {(group)} {(having)} order by m.operday {(limit)}',
		],
		# Поля выбора (select fields)
		'dl'=>10,
		'filter'=>[
				'fw'=>['mid','mloan','moperday','mdv'], 
				],
		'paginator'=>'page',  # scroll  page  
		'fs'=>['mid','muname','mamount','mdv','moperday','action'],	
		'setd_fs'=>[
			'For user'=>['mid','muname','mloan','moperday','mamount','moacc','muacc','mnote','mcrmid','mdv'],
			#'For list'=>['lolid','louid','loracc','lowa','lopa','loterm','loppd','lomsa','lomsd','lost','lonote','locrmid','lodv'],
			],
		'mf'=>[
			'sort'=>['mid','mdv'],  
			'etd_action'=>'$tdf="<a type=\'button\' title=\'View cash all details\' class=\'btn btn-info btn-sm\' href=\'/a/cashview/?id=".$rv["mid"]."\'><span class=\'glyphicon glyphicon-edit\'></span> Details</a>";',	
		]	
	]; 
/*
			'id'=>					['p'=>'mid',		'fn'=>'Cash Id'],
			'uid'=>					['p'=>'muid',		'fn'=>'User Id'],
			'uname'=>				['p'=>'muname','fn'=>'Customer','q'=>'(select concat(ifnull(name,"")," ",login) from users where id=m.uid)'],
			'loan'=>				['p'=>'mloan',	'fn'=>'Loan Id'],
			'operday'=>				['p'=>'moperday',	'fn'=>'Operation day'],
			'amount'=>				['p'=>'mamount',	'fn'=>'Amount'],
			'oacc'=>				['p'=>'moacc',		'fn'=>'Our account','v'=>$libs['UsrOurWallet']],
			'uacc'=>				['p'=>'muacc',		'fn'=>'Customer account'],
			'cashier'=>				['p'=>'mcashier',	'fn'=>'Cashier Id'],
			'note'=>				['p'=>'mnote',		'fn'=>'Cash note'],
			'st'=>					['p'=>'mst',		'fn'=>'Cash status'],			
			'crmid'=>				['p'=>'mcrmid',		'fn'=>'Cash CrmId',
									'etd'=>'$tdf="<a title=\'Link to Cash in CRM\' href=\''.$crmd.'0/Nui/ViewModule.aspx#CardModuleV2/UsrCashPage/edit/".$tdv."\'>".$tdv."</a>";'
									],
			'dv'=>					['p'=>'mdv',		'fn'=>'Create Time'],
*/