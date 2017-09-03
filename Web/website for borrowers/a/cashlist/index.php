<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Cash list';
$page['desc'] = 'Cash list';

require_once($dr.'/a/tset_cashs.php'); 

$dpel=['md'=>$tset_cashs];			# Подключаем настроки списка анкет
	
require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>

		<h2>Cash list</h2>
		<?= $html['md'] ?>

<?php require PHIX_CORE . '/render_view.php';