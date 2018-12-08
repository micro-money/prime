<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'APP Report';
$page['desc'] = 'Customer information from application';

if (isset($_GET['uid']) && intval($_GET['uid'])>0) $uid=intval($_GET['uid']);


# Мы расписываем какие элементы будут на странице
$dpel=[							# ОБЯЗ: ГЛОБАЛЬНЫЙ: Базовые установки - минимально даже с ними уже может строиться 
	'md'=>['gf'=>'sastabV1',				# ОБЯЗ: Имя функции конструктора элемента
		'tl'=>	[	
					'users'=>[
						# Тут можно добавить или переопределить старые настройки (затереть старые можно только переопределив их новыми)
					],
					'users_map'=>[
						'pt'=>'m',
						'c'=>[
							'dv'=>['p'=>'mdv'],
							'mdv2'=>['q'=>"DATE_FORMAT((select max(dv) from users_mapdata where user_id={(pt)}.user_id), '%d.%m.%Y %H:%i')"],
							'id'=>['p'=>'mid'],
							'user_id'=>['p'=>'muid'],
							'post'=>['p'=>'mpost'],
							],
					]
				],
		# Шаблон запроса (qwery template)  as mdv
		'qt'=>"SELECT {(select)} FROM users as u,users_map as m WHERE m.user_id=u.id {(where)} {(group)} {(having)} {(order)} {(limit)}",
		# Поля выбора (select fields)
		'dl'=>10,
		/**/
		'setl'=>[
			'For Last Mounth'=>['(w|n)'=>'mdv|wt1|2','(wt|wt1)'=>'day|-30'],
			'For Last Week'=>['(w|n)'=>'mdv|wt1|2','(wt|wt1)'=>'day|-7'],
			'For Last Day'=>['(w|n)'=>'mdv|wt1|2','(wt|wt1)'=>'day|-1'],
			],
		
		#'setd'=>'For Last Mounth',
		
		'wtmpl'=>[
				'wt1'=>['t'=>'date_add(now(), interval {(day)} day)','p'=>['day'=>'i']],
				],
		
		'filter'=>[
				'fw'=>['mid','mpost','ulogin','mpost','mdv'],
				],
		'paginator'=>'page',  # scroll  page  ,'a.LoanDays','a.create_at','a.CrmStatus','a.cst','a.HowDoYouWantToGetMoney','a.RequestAmount'
		
		'fs'=>['mid'=>'logID','muid'=>'UserID','ulogin'=>'Phone','uname','mdv'=>'Date','mpost'=>'AppData'],	
		'mf'=>[
			'sort'=>['mid','ulogin','muid','mdv'],  // 'ulogin'=>[1,' ASC'],' DESC'
			'etd_mpost'=>'$tdf=rep($tdv);',	 		# ,'viewMode'=>'tabVert'
		]	
	],
];

if (isset($uid)) {
	unset($dpel['md']['setl']); unset($dpel['md']['filter']); unset($dpel['md']['wtmpl']);
	$dpel['md']['qt']="SELECT {(select)} FROM users as u,users_map as m WHERE m.user_id=$uid and m.user_id=u.id {(where)} {(group)} {(having)} {(order)} {(limit)}";
}
// $datav['post']
function rep($tdv){
	$udata=''; 
	if ($tdv!='[]' && $tdv!='' && !is_array($tdv)) {
		$jsona=json_decode($tdv, true);
		
		//unset($jsona['push_token']); unset($jsona['mapl']);
		//$udata=''; 'IMEI:'.$jsona['IMEI'].'</br>Phones:</br>'.p($jsona['top_contacts']);
		if (isset($jsona['IMEI'])) $udata.='IMEI:'.$jsona['IMEI'];
		if (isset($jsona['top_contacts'])) $udata.='</br>Phones:</br>'.p($jsona['top_contacts']);
		
		// https://www.google.ru/maps/place/20%C2%B008'42.3%22N+94%C2%B056'27.9%22E/
		// 20.1450863,94.9410978 // https://www.google.ru/maps/place/
		if (isset($jsona['lat']) && isset($jsona['lng'])) {
			$gps=$jsona['lat'].','.$jsona['lng'];
			$udata.='gps: <a href="https://www.google.ru/maps/place/'.$gps.'" target="_blank">'.$gps.'</a></br>';
		}
		if (isset($jsona['primary_email'])) $udata.='primary_email:'.$jsona['primary_email'].'</br>';
		if (isset($jsona['accounts'])) $udata.='Accounts:</br>'.p($jsona['accounts']);
	}
	return $udata;
}

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>

<h2>Information from Application</h2>
<?= $html['md'] ?>

<?php require PHIX_CORE . '/render_view.php';