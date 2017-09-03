<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Static list';
$page['desc'] = 'Static pages list';

# Мы расписываем какие элементы будут на странице
$dpel=[							# ОБЯЗ: ГЛОБАЛЬНЫЙ: Базовые установки - минимально даже с ними уже может строиться 
	'mt'=>['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	[	
					'web_pages'=>[
						# Тут можно добавить или переопределить старые настройки (затереть старые можно только переопределив их новыми)
						'c'=>['action'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],
						]
					],
				],
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM web_pages wp {(order)}",
		# Поля выбора (select fields) =>['e'=>1] 
		'fs'=>['wpid','wpshow'=>['e'=>1],'wps'=>['e'=>1],'wpurl','wpten','action'],	
		'filter'=>[],
		'mf'=>[
			'sort'=>['wpid','wpshow','wps'],  // 'ulogin'=>[1,' ASC'],' DESC'
			'etd_spurl'=>'$tdf="<a href=\'/".$tdv."\'>".$tdv."</a>";',
			'etd_action'=>'$tdf="<a title=\'Edit content\' class=\'btn btn-sm btn-danger\' href=\'/a/staticview/?id=".$rv["wpid"]."\'><i class=\'fa fa-edit\'> Edit</i></a>";',	
		]	

	],
];

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container">
		<h2>Static pages</h2>
		<?= $html['mt'] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';