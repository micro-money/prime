<?php $ShowErr=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/tool/sas/constants.php');						# Подключаем константы
require_once($dr.'/app_func.php');									# Сборки по номерам шагов

/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */
$page['title'] = 'Loan Form';
$page['js_raw'] = <<<JS
    window.gon={};
    gon.locale="en";
    gon.translations={"js.slider.needAmount":"MMK","resendCode":"translation missing: en.resend_code","js.slider.terms":"days"};
JS;
$ss_harr=[
	0=>'Personal information',
	1=>'Employment information',
	2=>'Additional information',
	3=>'Getting money',
	4=>'Documents scan',
	5=>'Finished',
];
/* ---------------------- КОНТРОЛЛЕР СТРАНИЦЫ ----------------------- */

header(getMadheader());												# Заголовок для приложения
										
$this_wizard_name='app_wizard';
require_once 'app_init.php';
# Наша задача перекинуть клиента на нужную анкету если он зашел не туда

#if ($user['login']=='89027222334') print_r($_SERVER);


if (!empty($_POST['data'])) {
	
	$o=acceptPost(['user'=>$user]); 
	$rq=$o['rq']; 	
	
	#print_r($rq); die();

	if (isset($rq['u']) && isset($rq['u']['salary'])) {
		$lead=getUserData(['uid'=>$user['id']]);					# грузим все данные клиента + последней анкеты
		#print_r($lead); die();
		$ramount=0;  $sal=0; 	# Корректируем займ если он не соответвует зарплате
		if (isset($user['salary'])) $sal=$user['salary']; if (isset($rq['u']['salary'])) $sal=$rq['u']['salary'];
		if ($lead['l']['ramount']>50000) $ramount=50000;
		if ($sal<350000 && $lead['l']['ramount']>30000) $ramount=30000;
		if ($ramount>0) db_request("UPDATE `leads` SET ramount=$ramount WHERE `id` = {$user['a_lid']}");	# Корректируем сумму заявки	
	}
	# После каждого принятого поста если все ок, я перекидываю себя еще раз на себя чтобы сбросить этот пост и чтобы он не вязался за след работой нахер.
	if (empty($page['error_msg'])) { header("Location: ".$_SERVER['REQUEST_URI']); mysql_close(); exit; }
}

if (!isset($lead)) $lead=getUserData(['uid'=>$user['id']]);			# грузим все данные клиента + последней анкеты если их нет
$stepm=stepm_wizard(); $step=false;								# Сколько осталось шагов для первичной анкеты

if (count($stepm)>0) {	# Еще есть что заполнять				
	$step=$stepm[0];		
	if ($step == 3) $page['js_raw'] .= ah_initPaymentJS();			# Банковские реквизиты > подгружаем доп скрипт
	$tmpl='sections/frontend/wizard/step' . $step;					# Шаблон страницы след шага анкеты
} else {				# Все заполнено > спасибо ждем
	if ($lead['l']['st']<2) {
		db_request("update leads set udr=now(),st=2 where id={$user['a_lid']}"); # Устанавливаем лиду статус завершен						
		$timem = db_array("select date_add(udr, interval 24 hour) as stimer from leads where id={$user['a_lid']}"); $lead['l']['stimer']=$timem[0]['stimer'];
	}
	# Подготавливаем значения для шаблона страницы спасибо
	$ss_stimer=date("m/d/Y H:i", strtotime($lead['l']['stimer'])); 	
	$page['js_raw'] .=ah_initTime([]);								# высчитываем какой сейчас таймер в его скрипт 
	$ss_howmoney=$lead['l']['ramount'];								# Размер кредита 
	$ss_wheremoney=' '.l('in').' '.$libs['bank_id'][$lead['l']['bank']].' mr.'.$lead['u']['name'];  # .' login:'.$lead['u']['login']
	# Банковские реквизиты
	$tmpl='sections/frontend/step_wait';							# Шаблон страницы ожидания
	$step=count($ss_harr)-1;
}	
	
/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ 
ob_start(); 	require_once($dr.'/templates/'.$tmpl.'.php');
?>
<? #e($tmpl) ?>
<?php require PHIX_CORE . '/render_view.php';