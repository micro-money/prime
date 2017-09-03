<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Locales';
$page['desc'] = 'Locale translations list';

$vlim=['etd'=>' if (strlen($tdv)>60) $tdf=substr($tdv, 0, 60)." ...";'];	# Обрезка значения до 10 символов

# Мы расписываем какие элементы будут на странице
$dpel=[							# ОБЯЗ: ГЛОБАЛЬНЫЙ: Базовые установки - минимально даже с ними уже может строиться 
	'mt'=>['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	[	
					'web_words'=>[
						# Тут можно добавить или переопределить старые настройки (затереть старые можно только переопределив их новыми)
						'c'=>[
							'action'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],
						]
					],
				],
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM web_words ww WHERE 1=1 {(where)} {(group)} {(having)} {(order)} {(limit)}",
		'dl'=>10,
		'setl'=>[
			'Without Burmese'=>['(w|n)'=>'wwmm|wwbm','(w|v)'=>'|','(w|s)'=>'1.0.3.0|2.4.0'],
			'Without Indonesia'=>['(w|n)'=>'wwind||0'],
			'Without Sinhalese'=>['(w|n)'=>'wwsin||0'],
			'Without Thai'=>['(w|n)'=>'wwth||0'],
			'Without Phil'=>['(w|n)'=>'wwph||0'],
			], 
		'fs'=>['wwid','wws'=>['e'=>1],'wwen'=>$vlim,'action'],	 #  ,'ltmm'=>$vlim,'ltbm'=>$vlim
		'filter'=>[
			'fw'=>['wwid','wwen','wws'],
		],
		'paginator'=>'page',
		'mf'=>[
			'sort'=>['wwid','wwen'],  // 'ulogin'=>[1,' ASC'],' DESC'
			'etd_action'=>'$tdf="<a title=\'Edit content\' class=\'btn btn-sm btn-danger\' href=\'/a/localeview/?id=".$rv["wwid"]."\'><i class=\'fa fa-edit\'> Edit</i></a>";',	
		]	

	],
];

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container">
		<h2>Locale translations</h2>
		<?= $html['mt'] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';