<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $id=intval($_GET['id']); } else { die('need page id'); } 

$page['title'] = 'Staic #'.$id;
$page['desc'] = 'Edit Staic page #'.$id.' content';
	
$tset=['gf'=>'sastabV1',
		'tl'=>	['web_pages'=>[],],
		'qt'=>"SELECT {(select)} FROM web_pages wp WHERE id=".$id,
		'mf'=>[
			'viewMode'=>'tabVert','am'=>1,
			'etd_XX'=>'$a=1;',
			],
	];	
$en=['wpten'=>['e'=>1],'wpcen'=>['e'=>1]];
$th=['wptth'=>['e'=>1],'wpcth'=>['e'=>1]];
$ph=['wptph'=>['e'=>1],'wpcph'=>['e'=>1]];
$mm=['wptmm'=>['e'=>1],'wpcmm'=>['e'=>1]];
$bm=['wptbm'=>['e'=>1],'wpcbm'=>['e'=>1]];
$ind=['wptind'=>['e'=>1],'wpcind'=>['e'=>1]];
$sin=['wptsin'=>['e'=>1],'wpcsin'=>['e'=>1]];

$dpel=[];
$dpel['en']=$tset; $dpel['en']['fs']=$en;

$dpel['th']=$tset; $dpel['th']['fs']=$th;
$dpel['ph']=$tset; $dpel['ph']['fs']=$ph;

$dpel['mm']=$tset; $dpel['mm']['fs']=$mm;
$dpel['bm']=$tset; $dpel['bm']['fs']=$bm;	
$dpel['ind']=$tset; $dpel['ind']['fs']=$ind;
$dpel['sin']=$tset; $dpel['sin']['fs']=$sin;

$dpel['en']['fs']['wpurl']=['h'=>1];
				
$dpel['en']['fe']=['ival_sas_sqlm'];		# Снимаем титульное название таблицы $sas_sqlm['title']
	
function ival_sas_sqlm($w) {
	$w['sas_sqlm']["m"]=$w['data'][0];
	return $w;
}

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

# Снимаем  
#print_r($sas_sqlm); die();  
/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container">
		<h2>Content for page: <a href="/<?= $sas_sqlm['m']['wpurl'] ?>" target="_blank"><?= $sas_sqlm['m']['wpten'] ?></a></h2>
		<div class="row">
			<ul class="nav nav-tabs">
				<? foreach ($app['langn'] as $k=>$v) { ?>
					<li <?= ($k=='en') ? 'class="active"': '' ?>><a data-toggle="pill" href="#<?= $k ?>_con"><?= $app['langn'][$k]?> content</a></li>
				<? } ?>
			</ul>
			<div class="tab-content">
				<? foreach ($app['langn'] as $k=>$v) { ?>
				<div id="<?= $k ?>_con" class="tab-pane fade <?= ($k=='en') ? 'active in': '' ?>">
					<h2><?= $app['langn'][$k] ?></h2>
					<?= $html[$k] ?>
				</div>
				<? } ?>
			</div>
		</div>
	</div>
<?php require PHIX_CORE . '/render_view.php';