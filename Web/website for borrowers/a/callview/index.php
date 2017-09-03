<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $call_id=intval($_GET['id']); } else { die('need call id'); } 
$page['title'] = 'Call #'.$call_id.' info'; $page['desc'] = 'Call full details';

include($dr.'/a/tset_calls.php');	$t=$tset_calls;		# Звонки клиенту

$t['htname']='Call details';					# Заголовок для закладки
$t['mf']=['viewMode'=>'tabVert',];				# Вертикалим таблицу
$t['tkol']=1;									# Сброс запроса на общее количество строк
$t['wd']=['fd(w|n)'=>'ukid|'.$call_id.'|0']; 	# Ставим условие запроса конкретного звонка

if (empty($t['fe'])) $t['fe']=[];
$t['fe'][]='ival_ukuid';

#unset($t['setl']); unset($t['setd']);  'ukid',
$t['fs']=['ukuid'=>['h'=>1],'ukdt'=>['h'=>1],'ukcid'=>['h'=>1],'ukdid'=>['h'=>1],'ukcst','ukcname','ukdocn','uknote','ukrecall','ukws','ukdv'];	
$fdata=$t; # print_r($data); die(); 

function ival_ukuid($w){
	$w['sas_sqlm']["user_id"]=$w['data'][0]["ukuid"];
	return $w;
}

require_once($dr.'/a/set_userview.php');
	
require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

$cuname=$sas_sqlm['m']['uname'].' ['.$sas_sqlm['m']['ulogin'].']';

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<h2>Call #<?= $call_id ?> details to the customer <?= $cuname ?></h2>
<? require_once($dr.'/a/tmpl/workleads.php'); ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';