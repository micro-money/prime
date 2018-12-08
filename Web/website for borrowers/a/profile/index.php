<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE;  $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
if (isset($_GET['id']) && is_numeric($_GET['id'])) $user_id=intval($_GET['id']); 

if (!isset($user_id)) $user_id=$user['id']; 
$sas_sqlm=['user_id'=>$user_id];

$page['title'] = 'Profile #'.$user_id;
$page['desc'] = 'Edit admin profile';

include($dr.'/a/tset_userinfo.php');	# Данные клиента по типам + шаблон для карточки клиента
$dpel=['pi'=>$tset_userinfo]; $v=['e'=>1];

$dpel['pi']['fs']=['ulogin'=>$v,'uname'=>$v,'ugender'=>$v,'upass'=>$v,'urole'=>[]];

if ($user['role']=='super') $dpel['pi']['fs']['urole']= $v;

if (!empty($sas_eupd)) $sas_eupd=[];
$sas_eupd[]='ival_chpass';		#   Доп Блокировка обновления хотя она избыточная 'e'=>1 и так и так не будет у безправного

function ival_chpass($w){
	if ($w['tfn']=="upass") {
		$w['upass']=sha1($w['fvm'][$w['n']]); $w['id']=$w['user_id'];
		db_request("UPDATE users set pass='{$w['upass']}' WHERE id={$w['id']}");
		$w['asts']=0; $w['up']=0;
		$w['atxt']="Password was changed to [{$w['fvm'][$w['n']]}].";
	}	
	return $w;
}

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');		# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

$cuname=$sas_sqlm['m']['uname'].' ['.$sas_sqlm['m']['ulogin'].']';
#print_r($sas_eupd); die();
/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<h2>Profile for customer # <?= $user_id.' '.$cuname ?></h2>
<div class="row">
	<div class="tab-content">
	<?= $html['pi'] ?>
	</div>	
</div>
	</div>
<?php require PHIX_CORE . '/render_view.php';