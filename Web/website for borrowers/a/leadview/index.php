<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1;  
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $lid=intval($_GET['id']); } else { die('need lead id'); } 

$page['title'] = 'Lead #'.$lid.' info'; $page['desc'] = 'Lead infomation';

# Устанавливаем подробную информацию по лиду на первое место ($fdata->$fd)

require_once('cajx.php');										# Подключаем кастомную обработку аякса
$page['js'][] = $hn.$selfc.'m.js?ver='.$jsver;					# Подключаем персональный js
	
// ==========
require_once($dr.'/a/set_lead.php');
require_once($dr.'/a/set_userview.php');



if ($user['role']!='supers') {
	$dpel['fd']['preval']='
		$dpel[$el]=disableEdit($dpel[$el]);
	';
}

function disableEdit($e){ # Отменяем все редактирование
	if (isset($e['fs'])) {
		foreach ($e['fs'] as $k=>$v) {
			if (isset($v['e'])) unset($e['fs']['e']);
		}
	}
	return $e;
}

require_once($dr.'/tool/sas/stage1_settings.php');  			# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');	# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');		# Выполняем запросы к базе данных и строим html у динамических элементов

$cuname=$sas_sqlm['m']['uname'];
$MainPhone=$sas_sqlm['m']['ulogin'];

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<div class="row">
			<div class="col-12">
				<?#= $topalerts ?>
				<h3>Lead #<?= $lid ?>&nbsp;&nbsp;&nbsp;<?= $cuname ?>&nbsp;&nbsp;&nbsp;<a href="sip:<?= $MainPhone ?>"><?= $MainPhone ?></a>&nbsp;&nbsp;&nbsp;</h3>
			</div>
		</div>
		<? require_once($dr.'/a/tmpl/workleads.php'); ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';