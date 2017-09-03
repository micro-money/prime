<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Loans list';
$page['desc'] = 'Loans list';

require_once($dr.'/a/tset_loans.php'); 

$dpel=['md'=>$tset_loans];			# Подключаем настроки списка анкет
	
require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>

<h2>Loans list</h2>
<?= $html['md'] ?>

<?php require PHIX_CORE . '/render_view.php';