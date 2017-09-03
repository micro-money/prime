<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/app_func.php');
$this_wizard_name='vip_wizard';
#die("[$this_wizard_name]");
require_once 'app_init.php';

// ini_set('error_reporting', E_ALL);ini_set('display_errors', 1);ini_set('display_startup_errors', 1);

//	print_r($user);
/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */
$page['title'] = 'Vip Form';
/**/
$page['js_raw'] = <<<JS
    window.gon={};
    gon.locale="en";
    gon.translations={"js.slider.needAmount":"MMK","resendCode":"translation missing: en.resend_code","js.slider.terms":"days"};
JS;
# Сборки по номерам шагов

$step=2; $bdstep=$step;  // 0 - Обычная анкета 10 - в один шаг 

$ss_harr=[1=>'Take money now!',2=>'Approved!']; //  ,2=>'Getting money'
$ss_cname=l('Customer'); if (isset($user['Name'])) $ss_cname=$user['Name'];
#$ss_amount=$ah_appdata['RequestAmount'];

$ll=$o['ll'][count($o['ll'])-1];

if ($ll['st']<2) {
	db_request("update leads set udr=now(),st=2 where id={$user['a_lid']}"); # Устанавливаем лиду статус завершен						
	$timem = db_array("select date_add(udr, interval 24 hour) as stimer from leads where id={$user['a_lid']}"); $ll['stimer']=$timem[0]['stimer'];
}
	# Подготавливаем значения для шаблона страницы спасибо
	$ss_stimer=date("m/d/Y H:i", strtotime($ll['stimer'])); 	
	$page['js_raw'] .=ah_initTime([]);	
#die("[$ss_stimer]");
$ss_amount=$ll['ramount'];
$ss_hakey=$step+1;		
$ss_fhref='/vip_wizard'; if ($step>0) $ss_fhref.='?step='.$step;
	
/*
echo json_encode($user);
echo json_encode($ah_appdata);

die();
*/
$ss_howmoney=$ss_amount;
$ss_wheremoney=''; 	

$page['js_raw'] .=ah_initMessenger([]);
	//die('here err7='.$step);	
$tmpl='sections/frontend/vip/step' . $step;					# Шаблон страницы след шага анкеты

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ 
ob_start(); require_once($dr.'/templates/'.$tmpl.'.php');
?>
<? #e($tmpl) ?>
<?php require PHIX_CORE . '/render_view.php';