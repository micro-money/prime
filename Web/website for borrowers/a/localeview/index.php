<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $id=intval($_GET['id']); } else { die('need word id'); } 

$page['title'] = 'Locale #'.$id;
$page['desc'] = 'Edit Locale #'.$id.' translation';	

/*
Как редактировать запись когда много языков ? 
Все языки списком
*/
	
$tset=['gf'=>'sastabV1',
		'tl'=>	['web_words'=>[],],
		'qt'=>"SELECT {(select)} FROM web_words ww WHERE id=".$id,
		'mf'=>[
			'viewMode'=>'tabVert','am'=>1,
			'etd_XX'=>'$a=1;',
		]
];	
	
# Получить все имена столбцов в таблице кроме указанных и перебрать их все сделать редактируемыми
# $excl=['id','mdkey','enable'];
#$bfs=['wwen','wwmm','wwbm','wwind','wwsin']; # if (!in_array($k,$excl) && isset($v['p'])) 
foreach ($app['langn'] as $k=>$v) $fs['ww'.$k]=['e'=>1];

$dpel=[];
$dpel['en']=$tset; $dpel['en']['fs']=$fs;
$dpel['en']['fe']=['ival_sas_sqlm'];

function ival_sas_sqlm($w) {
	$w['sas_sqlm']["m"]=$w['data'][0];
	return $w;
}

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

# Снимаем  

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container">
		<h2>Translate for: <span class="bg-info"><?= $sas_sqlm['m']['wwen'] ?></span></h2> 
		<?= $html['en'] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';