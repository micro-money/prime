<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Static list';
$page['desc'] = 'Static pages list';

#       
$dpel=[							# : :   -        
	'mt'=>['gf'=>'sastabV1',				# :    
		'tl'=>	[	
					'web_pages'=>[
						#        (      )
						'c'=>['action'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],
						]
					],
				],
		#   (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM web_pages wp {(order)}",
		#   (select fields) =>['e'=>1] 
		'fs'=>['wpid','wpshow'=>['e'=>1],'wps'=>['e'=>1],'wpurl','wpten','action'],	
		'filter'=>[],
		'mf'=>[
			'sort'=>['wpid','wpshow','wps'],  // 'ulogin'=>[1,' ASC'],' DESC'
			'etd_spurl'=>'$tdf="<a href=\'/".$tdv."\'>".$tdv."</a>";',
			'etd_action'=>'$tdf="<a title=\'Edit content\' class=\'btn btn-sm btn-danger\' href=\'/a/staticview/?id=".$rv["wpid"]."\'><i class=\'fa fa-edit\'> Edit</i></a>";',	
		]	

	],
];

require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				#    
require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   

/* --------------------------  ------------ */ ob_start(); ?>
	<div class="container">
		<h2>Static pages</h2>
		<?= $html['mt'] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';