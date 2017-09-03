<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');
$page['title'] = 'Scan check';
$page['desc'] = 'Check customers scans';

#$vlim=['etd'=>' if (strlen($tdv)>60) $tdf=substr($tdv, 0, 60)." ...";'];	# Обрезка значения до 10 символов
/*
Алгоритм: Задача проверить все сканы документов с типом ft(1,2) в два захода.
1) В тему ли этот скан (нужный документ или нет). Если не в тему то 
2) Является ли этот скан NRC документом (он у всех одинаковый и искать его предпочтительнее чем банковские сканы)

Для реализации функционала используем кастомный файл. В котором будет кастомный запрос.
Для реализации функционала используем очередь захвата.
*/

$mel='mt';
$dpel=[							 
	$mel=>['gf'=>'sastabV1',				
		'title'=>'<h2>Scan checking</h2>',
		'tl'=>	['application_files'=>['c'=>[
				'empty'=>['p'=>'empty','q'=>' '],
				'fp'=>['p'=>'timg','etd'=>'$tdf=cuScanV1(["h"=>$rv["afh"],"v"=>$tdv,"s"=>200,"m"=>1]);'],
				]],],
		'dl'=>30, #6x4
		'qt'=>'SELECT {(select)} FROM {(from)} WHERE 1=1 {(where)} {(limit)}', 		
		'setl'=>[
			'IsDoc'=>['(w|n)'=>'empty|wt1|e'],
			'TypeOk'=>['(w|n)'=>'empty|wt2|e'],
			'TypeErr'=>['(w|n)'=>'empty|wt3|e'],
			'NotDoc'=>['(w|n)'=>'empty|wt4|e'],
			'UnCheck'=>['(w|n)'=>'empty|wt5|e'],
			],
		'wtmpl'=>[	# Группировка всегда сначала новые
				'wt1'=>['t'=>'af.ac=1 order by af.dv DESC '],	# Только nrc + банк которые еще не проверялись на документ
				'wt2'=>['t'=>'af.ac=2 order by af.dv DESC '],			# Только nrc которые после проверки документа
				'wt3'=>['t'=>'af.ac=3 order by af.dv DESC '],
				'wt4'=>['t'=>'af.ac=4 order by af.dv DESC '],
				'wt5'=>['t'=>'af.ac=0 order by af.dv DESC '],
				],
		'setd'=>'IsDoc',
		#'not show all'=>1,	# Не показывать фильтр - все . Он тут не уместен
		'filter'=>[	# Фильтр
			#'addhtml'=>'',  
			],		
		'paginator'=>'page', 
		'mf'=>['efunc'=>'tab'],
		'fs'=>['afid','timg','afi','afi','afh'],	   
	],
];
		

/*
Еще надо фиксированные размеры фото . 

$page['js'][] = $hn.$selfp.'/m.js?ver='.$jsver;						# Подключаем персональный js

# Для норм работы селектора работы - без этого не переключается расчет строк которые остались
$pset=['only'=>'w','el'=>$mel]; if (isset($_POST[$mel.'(setd)'])) $pset['setd']=$_POST[$mel.'(setd)'];
	
if ($_POST['wtype'] && isset($dpel[$mel]['setl'][$_POST['wtype']])) {	# Кастомная работа 1. Пришел тип работы и он совпадает с режимами на странице
	if ($_POST['wtype']=='Document check') {	# Если пришел пост с ac3 -> значит у нас режим проверки на документ(да/нет) -> ac3=>[ID1,ID2](Есть строки на ac3) или ac3=[] (нет строк на ac3)
		$ac1=4; $ac2=1; $ac3=0;
	}
	if ($_POST['wtype']=='NRC check') {	# Если пришел пост с ac2 -> значит у нас режим проверки совпадения типа паспорта -> ac2=>[ID1,ID2](Есть строки с несовпадающим типом) или ac2=[] (все строки совпадают по типу)
		$ac1=3; $ac2=2; $ac3=1;
	}
	$idm=[]; if (isset($_POST['idl'])) foreach ($_POST['idl'] as $rid) if (is_numeric($rid)) $idm[]=intval($rid); 
	if (count($idm)>0) db_request('update application_files set ac='.$ac1.' where id in ('.implode('',$idm).')');
	db_request('update application_files set ac='.$ac2.' where iw='.$user['id'].' and ac='.$ac3.' ');	# Помечаем все оставшие что в работе c ac=1 -> ac=2 -> т.к. они соответвуют паспортам 
}
*/
require_once($dr.'/tool/sas/stage1_settings.php');  					# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
/*
# Очередь для текущего оператора mt
$func = $dpel[$mel]['gf']; 												# Имя функции построения элемента
$w=$func(array_merge($dpel[$mel],$pset));								# Запускаем функцию с именем элемента
				
db_request('update application_files af set iw=0 where iw='.$user['id']);	# Сброс старой работы
db_request('update application_files af set iw='.$user['id'].' where 1=1 '.$w['where'].' '.$w['limit']);	# Добавляем в очередь новые согласно входящему условию

$dpel[$mel]['wtmpl']['wt1']['t']='iw='.$user['id'];						# Корректируем шаблон условия . Меням выборку работы на все что есть на текущего оператора
$kolm=db_array($w['sql_kol']);	$kol=$kolm[0]['kol']; 
$dpel[$mel]['tkol']=$kol;												# Отдаем все кол-во по текущей работе	

#$page['js_raw'].='sas_setl_'.$mel.'='.json_encode(array_keys($dpel[$mel]['setl'])).';';

$as='style="padding: 5px;margin-bottom: 5px;margin-right: 7px;"';
$nextbut='<div class="form-group" '.$as.'><button type="button" class="btn btn-warning" onclick="nextch({el:\''.$mel.'\',wtype:\''.$w['setd'].'\'});">These Checked > Open next scans</button></div>';
$dpel[$mel]['mf']['inputsetd']=$w['setd'];

if ($kol>0) $dpel[$mel]['filter']['addhtml']=$nextbut;					# Если у нас есть кол-во в работу > Добавляем кнопку
*/
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');					# Аякс работа если есть

require_once($dr.'/tool/sas/stage2_build_elements.php');				# Выполняем запросы к базе данных и строим html у динамических элементов

function CheckScansGallery($mp){
	# Пример построения галлереи иконок
	$tabf=$mp['tabf']; $tdms=$mp['tdms']; $tre=$mp['tre']; $bb=[]; 
	#$n=4; $bhtml=''; $xs=12;  $sm=12/2; $md=12/3; $lg=12/4;  # class="col-xs-'.$xs.' col-sm-'.$sm.' col-md-'.$md.' col-lg-'.$lg.'"
	foreach ($tabf as $tr=>$kr) if (is_string($kr['timg']['v'])) $bb[]='
	<div '.$tre[$tr].' style="border-top: 1px double black;float:left;min-width:230px;min-height:255px;" '.$kr['timg']['e'].'>
		<div class="checkbox">
			<label><input rowid="'.$kr['afid']['v'].'" type="checkbox" checked value="">'.$mp['inputsetd'].'</label>
		</div>
		'.$kr['timg']['v'].'
	</div>';
	$html='<div class="container"'.$tdms.'><div class="row">'.implode('',$bb).'</div></div>';	
	if (isset($mp['filter'])) $html='<div>'.$mp['filter']['v'].'</div>'.$html;
	if (isset($mp['bottom'])) $html=$html.'<div>'.$mp['bottom']['v'].'</div>';
	return ['h'=>$html,'a'=>$bl];
}

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container">
		<?= $html[$mel] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';