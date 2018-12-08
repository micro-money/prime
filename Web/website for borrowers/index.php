<?php $ShowErr=1;   #$PlainText=true;
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/app_func.php');

# Here new adds
# One more adds

/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */
$page['title'] = '';
# ticks:[25000, 50000, 75000, 100000, 125000, 150000, 175000, 200000],

$tcn=$app['current_country']; 

$ctr=$countrym[$tcn];
$fil=$countrym[$tcn]['f'];

$lam	=$ctr['la'];
$ccur	=$ctr['cur'];
$amlim	=$ctr['al'];
$bmr	=$ctr['bmr'];
$cp		=$ctr['cp'];

$lterms=[14,21,28]; $ltermsd=[];
$start_la=$lam[0];
$start_lt=28; 

$page['js_raw'] = '
    window.gon={};
    gon.locale="en";
    gon.translations={"js.slider.needAmount":"'.$ccur.'","resendCode":"translation missing: en.resend_code","js.slider.terms":"'.l('days').'"};
	
function getb(b){
	var bmr='.json_encode($bmr).';
	var b1=b; if (typeof(bmr[b])!= "undefined") b1=bmr[b];
	return b1;
}

function gets(){
	return '.json_encode($lam).';
}

function getl(){
	return '.$amlim.';
}
	
function getc(){
	return "'.$ccur.'";
}

function getdp(){
	return ('.$cp.'/100);
}

';

# 20$, 35$, 60$, 75$, 100$, 115$,  145$ ,  220$
# 1$ 50,5 	PHP  1000, 1750,      3000, 3750, 5000, 5750,  7750 ,  10000$
# 1$ 1357 	MMK  20$, 35$, 60$,   75$, 100$, 115$,  145$ ,  220$ 
# (Индонезийская рупия)
# 1$ 13388  IDR  120000  200000, 400000, 800000,   1000000, 1350000, 1500000,  2000000 ,  3000000 
# 1$ 34 	THB
# 1$ 153 	LKR  3000, 5000, 8000,   10000, 13000, 15000,  20000 ,  30000 
# 1$ 35 	LKR  800, 1500, 2100,   2700, 3500, 4000,  6000 ,  8000 
/* ---------------------- КОНТРОЛЛЕР СТРАНИЦЫ ----------------------- */

function getnewdate($format, $day_offset) { 
     return date($format, time() + $day_offset * 24 * 60 * 60); 
} 

$ah_headout='';

/*
АЛГОРИТМ РАБОТЫ:
Что будет если пользователь сначала зашел по своему телефону на одном браузере
потом на другом , а потом на третем?
Он добавиться во все три ячейки ? Или мы объединим все три браузреа в одну ячейку и все пользователи этих браухеров 
объединяться в общую кредитную ячейку на которую может лечь общий бан если кто то из нее станет bad gay.
ОТВЕТ: объединяем все три ячейки в одну.
Каждый пользователь при переходе из ячейки в ячейку объединяет 
эти ячейки между собой.
*/

if (!empty($_POST['SignupForm'])) {
    
    $phone 	= $_POST['SignupForm']['phone'];		$sql_phone	=mysql_real_escape_string($phone);
    $Name 	= $_POST['Name'];						$sql_name	=mysql_real_escape_string($Name);
    $RequestAmount 	= intval($_POST['application']['amount']);
    $LoanDays 		= intval($_POST['application']['term']);
    
	// Шаг1: 	Валидация номера и имени
    if (!preg_match("|^[0-9]{7,11}$|i", $phone)) aPgE("<br>An error in the phone number! You must enter 7-11 digits without spaces.");
	if (empty($Name)) aPgE("<br>Full Name not specified!");
	
	// Шаг2: 	Авторизация
    if (empty($page['error_msg'])) {
		
		$ah_auth=0; $ouid=0; $ah_ur=[];
		// Заводим сессию авторизации
		$auth_hash = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));
		setcookie('auth_hash', $auth_hash, time() + $cooktime, '/', false);		
		
		$ah_ur[]="`auth_hash` = '$auth_hash'";				# Добавляем обновление хэша
		
		if (isset($user['id'])) {	# Браузер авторизован
			$ah_auth=1;								
			if ($user['login'] != $phone) $ouid=$user['id'];# Браузер авторизован, но введен номер отличный от авторизованного				
		} 
		
		if ($ah_auth==0 || $ouid>0) {
			
			# МИМО :  Браузер авторизован и пользователь верно ввел авторизованный телефон  
			# ТУТ А:  Браузер не авторизован
			# ТУТ Б:  Браузер авторизован, но введен номер отличный от авторизованного
			
			# 1) Ищем в базе клиента с указанным номером телефона 
			#$sr="SELECT * FROM `users` as u WHERE `login` = '$sql_phone'"; 
			
			# Ищем поданный телефон среди основных телефонов клиента
			$sr="SELECT uid FROM `users_contacts` as u WHERE cr=0 and ct=1 and `cval` = '$sql_phone'"; 
			$sUser = db_array($sr);
			
			if (count($sUser)==0) {  
				# Номер указан впервые -> Регистриуем нового пользователя
				// Генерация случайного пароля
				$user_pass = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536));  $user_pass_hash = sha1($user_pass);
		
				$rq = "INSERT INTO `users` SET `fil`=$fil,`login` = '$sql_phone',`pass` =  '$user_pass_hash',`auth_hash` = '$auth_hash',`name` = '$sql_name'";			
				$nuid = db_insert($rq);
				# Вносим основной телефон в контакты
				db_request("insert ignore into users_contacts (uid,cr,cname,cval,ct,cp) values ($nuid,0,'Primary phone','$sql_phone',1,1)");
				
				# Проверяем клиента по старым долгам если они есть
				db_request("update users U,calc_od T SET U.a_od=1 WHERE U.id=$nuid and T.Title like '%$sql_phone%';");
				db_request("update users U,calc_сd T SET U.a_cd=1 WHERE U.id=$nuid and T.Title like '%$sql_phone%';");				
				
				// Пробуем валидно поднять данные пользователя по номеру телефона если юзер новый
				$userm = db_array("SELECT * FROM `users` WHERE `id` = $nuid"); $user = $userm[0];
								
			} else {	# Номер есть в базе -> Берем его массив как рабочий
				$srm=db_array("SELECT * FROM `users` WHERE id = ".$sUser[0]['uid']);   $user = $srm[0];
				if (in_array($user['role'],$rolem)) {header("Location: /"); exit;}	// Админа выкидываем на главную страницу т.к. нельзя указав админа логи без парольно заходить
			}
			
			# Фиксируем с какого именно телефона клиент авторизовался если была смена телефона или авторизация с нуля
			db_request("INSERT INTO `users_uphone` (uid,lphone,lname,dv) VALUES ({$user['id']},'$sql_phone','$sql_name',now())");
			
		}
		
		# Фиксируем на клике Ip и браузер клиента
		db_request("INSERT INTO `users_whist` (uid,ip,br,dv) VALUES ({$user['id']},'$ip_sql','$br_sql',now())");

		# Фиксируем связь если пользователь авторизовался под новым аккаунтом когда был ранее под другим
		if ($ouid>0) db_request("INSERT INTO `users_lck` (k1,k2,dr) VALUES ($ouid,{$user['id']},now())");
						
		/*
		# Производим проверку пользователя на наличие в базе уже бравших клиентов
		# 1) на долг и если он есть -> Аллерт + Сканы об оплате
		# 2) наличие погашенного займа -> Новый займ + Вторичная анкета
		os
		Алгоритм: Нам нужены факты: брал ранее да/нет ,  долг  да/нет  
		
		Новый: У нас пользователь кликнул взять кредит. 
		Надо знать что за клиент:
			0 - Без истории и без анкет на разборе.
			1 - Без истории но есть открытая анкета на разборе.
			2 - Хороший повторник , уже брал кредиты и вернул, без анкет на разборе
			3 - В кредите (имеется один не погашенный кредит без проспрочки)
			4 - В просрочке (клиент в просрочке)
	
			Надо знать есть ли у клиента лиды в стадии разбора (это может быть только одна единственная)
				aleads=(select id,st from leads where uid=user_id and st<6;
			Надо знать есть ли у клиента активные сделки (и если они есть)
				aloans=(select id,st from loans where uid=user_id and st<6;
			
		*/

		$o=checkUser(); 	$ah_headout="Location: /".$o['h']; 	 $new_app=$o['a'];			
		#print_r($o); die();			
		if ($new_app==1) {	# ЗАВОДИМ НОВУЮ АНКЕТУ Если мы готовим новую заявку
			$application_id = db_insert("INSERT INTO `leads` SET `uid` = {$user['id']},`fil`=$fil,`ramount` = $RequestAmount,`rdays` = $LoanDays,`a_cd` = {$user['a_cd']}");
			$qw="update users set a_lid=$application_id where id={$user['id']}";
			db_request($qw);
		}
			
		# Если мы создали нового клиента у которого есть старые сделки в CRM > надо взять его информацию из CRM данных и прикрепить себе
		if (isset($nuid) && $user['a_od']>0 || $user['a_cd']>0) {
			$ppost=[]; $rq=['l'=>[],'u'=>[],'c'=>[],'a'=>[]];
			require_once($dr.'/tool/sas/constants.php');	
			$oppm=db_array("SELECT sync_id,sync_st,Id,Title,ContactId,UsrPaySystemId,UsrPayAcc,UsrMMPersonalID FROM {$sbd}zsync_Opportunity where Title like '%$sql_phone%' and sync_st=4 order by sync_id desc limit 1");  #
			if (count($oppm)>0) {
				
				$crm_cid=$oppm[0]['ContactId'];
				$ppost['onrc']	=$oppm[0]['UsrMMPersonalID'];
				
				$rq['a']['bacc']=bankFormat(array('r'=>0,'s'=>$oppm[0]['UsrPayAcc']));
				$rq['a']['bid'] = $crm_lib['banksysid'][$oppm[0]['UsrPaySystemId']];
							
				$ctkm=db_array("SELECT sync_id,sync_st,Name,GenderId,BirthDate,MobilePhone,Email,Facebook FROM {$sbd}zsync_Contact where Id='$crm_cid' and sync_st=4");
				if (count($ctkm)>0) {
					$rq['u']['birthdate'] = $ctkm[0]['BirthDate'];	
					$rq['u']['gender'] = $crm_lib['gender'][$ctkm[0]['GenderId']];						
					$crm_email=$ctkm[0]['Email']; if ($crm_email!='') $rq['c'][] = ['cr'=>0,'ct'=>2,'cname'=>'Email','cval'=>$crm_email]; 
					$crm_fb=$ctkm[0]['Facebook']; if ($crm_fb!='') $rq['c'][] = ['cr'=>0,'ct'=>3,'cname'=>'Facebook','cval'=>$crm_fb]; 					
				}
			}
			
			# Загоняем данные по клиенту  09775217760 
			if (isset($page['error_msg'])) $ope=$page['error_msg']; 
			$o=acceptPost(['user'=>$user,'rq'=>$rq,'pd'=>$ppost]); 
			if (isset($ope)) $page['error_msg']=$ope;
			
		}
			
		/*
		Что если клиент ввел отличное имя от ранее введенного?
			Надо запоминать все что клиент когда либо вводил. Как сделать?
			Рассмотрим этот случай после отдельно не сейчас.
			Сейчас мы используем старое имя клиента
		
		
		# Если клиентос был в базе -> Обновляем имя если клиент ввел другое
		if (!empty($sUser['id']) && $new_app==1) {  
			require_once($dr.'/tool/app_ah_ins.php');
			if (ah_ins(['ah_fn'=>'Name','ah_val'=>$Name])) $ah_ur[]= "`Name` = '".mysql_real_escape_string($Name)."'";
		}
		*/
		
		# Обновляем реквизиты юзера Хэш+Имя
		db_request("UPDATE `users` SET ".implode(',',$ah_ur)." WHERE `id` = {$user['id']}");	
		$_SESSION['user_id'] = $user['id'];
    }
} else {
	$phone = false; if (isset($user['login'])) $phone = $user['login'];
	$Name = false; if (isset($user['name'])) $Name = $user['name'];
}

header(getMadheader());

if ($ah_headout!='') header($ah_headout);  // Перекидываем если есть куда

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>

    <h1 class="main__title text-center"><?= l('Cash Advance For Your Personal Needs') ?></h1>
<div class="wrapper">
	
	<form class="simple_form form_hero" role="form" autocomplete="off" novalidate="novalidate" id="new_application" action="/" accept-charset="UTF-8" method="post">
        <input name="utf8" type="hidden" value="✓">
        <input type="hidden" name="authenticity_token" value="jI+FEddtlzMWl7oK2p4ARcrF/sQqt9/xmLeXvtoxKfX0pcyoj3uyX47QLR4on4m+YZqo08nPQtzlRobcK8SgPA==">
        <div class="hero" id="hero" role="scrollTo">
            <div class="hero__left">
                <h3 class="hero__title"><?= l('I need') ?> <span role="needAmount"></span> <?= l('for') ?> <span role="forTerms">15</span> <?= l('days') ?></h3>
                <div id="crlim" class="credit_limitation" role="creditLimitation"><?= l('This amount is available to repeat borrowers only') ?></div>
				<div class="hero__sliders">
                    <div class="hero__slider_amount" role="sliderAmount">
                        <input role="sliderInput" type="hidden" value="<?= $start_la ?>" name="application[amount]" id="application_amount">
                        <div role="slider" data-value="<?= $start_la ?>" value="<?= $start_la ?>" style="display: none;"></div>
                    </div>
                    <div class="hero__slider_terms" role="sliderTerms">
                        <input role="sliderInput" type="hidden" value="<?= $start_lt ?>" name="application[term]" id="application_term">
                        <div role="slider" data-value="<?= $start_lt ?>" value="<?= $start_lt ?>" style="display: none;"></div>
                    </div>
                </div>
                <div class="calc">
                    <dl class="calc__item calc__item-amount">
                        <dt class="calc__item__title"><?= l('Cash Advance Amount') ?> </dt>
                        <dd class="calc__item__price">
                            <span role="calcAmount"> </span><?= l($ccur) ?></dd>
                    </dl>
                    <dl class="calc__item calc__item-total">
                        <dt class="calc__item__title"><?= l('Total Repayment amount') ?> </dt>
                        <dd class="calc__item__price">
                            <span role="calcTotal"> </span><?= l($ccur) ?></dd>
                    </dl>
                    <dl class="calc__item calc__item-interest">
                        <dt class="calc__item__title"><?= l('Repayment date') ?> </dt>
                        <dd class="calc__item__price">
                            <span id="dated" <? foreach ($lterms as $td) echo 'dated_'.$td.'="'.getnewdate("d ", $td).l(getnewdate("M", $td)).getnewdate(" Y", $td).'"'; ?> role="calcInterest"></span></dd>
                    </dl>
                </div>
                <!--<div class="calc__note">Terms and Conditions apply. Chargeable Fees will be deducted.</div>-->
            </div>
            <div class="hero__right">
			
                <div role="floatInput" class="form_hero__group email optional application_country">
                    <div class="form-group field-application_country required">
                        <label class="email optional form_hero__label" style="color: #555;" for="application_country"><?= l('Country') ?></label>
                        <!--<input type="text" id="application_country" class="form_hero__input" name="Country" placeholder="Maung Maung" value="<?#= $Name ?>">-->
						<select onchange="chc(this.value);" class="form-control" style="height:55px;font-size: 18px;" id="application_country" name="Country">
							<? foreach ($countrym as $k=>$v) { ?>
								<option value="<?= $k ?>" <?= ($k == $tcn) ? 'selected' : '' ?>><?= $v['t'] ?></option>
							<? } ?>
						</select>			
                        <p class="help-block help-block-error"></p>
                    </div>                    
				</div>
			
                <div role="floatInput" class="form_hero__group email optional application_full_name">
                    <div class="form-group field-application_full_name required">
                        <label class="email optional form_hero__label" for="application_full_name"><?= l($countrym[$app['current_country']]['idx_name']) ?></label>
                        <input type="text" id="application_full_name" class="form_hero__input" name="Name" placeholder="Maung Maung" value="<?= $Name ?>" <?= (isset($countrym[$app['current_country']]['idx_name_css'])) ? $countrym[$app['current_country']]['idx_name_css'] : '' ?> >

                        <p class="help-block help-block-error"></p>
                    </div>                    </div>
                <div role="floatInput" class="form_hero__group tel optional application_mobile_phone">
                    <div class="form-group field-application_mobile_phone required">
                        <label class="tel optional form_hero__label" for="application_mobile_phone"><?= l($countrym[$app['current_country']]['idx_phone']) ?></label>
                        <input type="tel" id="application_mobile_phone" class="string tel optional form_hero__input" name="SignupForm[phone]" placeholder="<?= $countrym[$app['current_country']]['phone_def'] ?>" value="<?= ($phone) ?: $countrym[$app['current_country']]['phone_pr'] ?>" <?= (isset($countrym[$app['current_country']]['idx_phone_css'])) ? $countrym[$app['current_country']]['idx_phone_css'] : '' ?> >

                        <p class="help-block help-block-error"></p>
                    </div>                    
				</div>
                <div class="form-group margin-none">
                    <button type="submit" class="form_hero__btn form_hero__btn-wallet" name="new-application-button"><?= l('APPLY TO GET LOAN NOW!') ?></button>                    
				</div>
            </div>
        </div>
    </form>
</div>
<div class="wrapper">
    <div class="visible-lg-block visible-md-block">
        <div class="main__subtitle">
            <h2><?= l('4 Easy Steps to get a Cash Loan') ?></h2>
        </div>
    </div>
    <ul class="main__steps row">
        <li class="main__steps__item">
							<span class="main__steps__title">
								<span class="main__steps__num">1</span>
								<span><?= l('Apply For Loan') ?> </span>
							</span>
            <div class="main__steps__body">
                <i class="fa fa-file-text-o"></i>
                <span class="main__steps__body__text"><?= l('Complete the 5-minute application form') ?> </span>
            </div>
        </li>
        <li class="main__steps__item">
							<span class="main__steps__title">
								<span class="main__steps__num">2</span>
								<span><?= l('Get Approval') ?> </span>
							</span>
            <div class="main__steps__body">
                <i class="fa fa-check-square-o"></i>
                <span class="main__steps__body__text"><?= l('Get status of your loan approval within 24 hours') ?> </span>
            </div>
        </li>
        <li class="main__steps__item">
							<span class="main__steps__title">
								<span class="main__steps__num">3</span>
								<span><?= l('Sign a Contract') ?> </span>
							</span>
            <div class="main__steps__body">
                <i class="fa fa-clock-o"></i>
                <span class="main__steps__body__text"><?= l('Check your registered email for the loan contract') ?> </span>
            </div>
        </li>
        <li class="main__steps__item">
							<span class="main__steps__title">
								<span class="main__steps__num">4</span>
								<span><?= l('Receive money') ?> </span>
							</span>
            <div class="main__steps__body">
                <i class="fa fa-money"></i>
                <span class="main__steps__body__text"><?= l('Receive cash via bank domestic remittance or via payment system') ?> </span>
            </div>
        </li>
    </ul>
    <div class="visible-lg-block visible-md-block">
        <div class="main__subtitle">
            <h2><?= l('Why choose Micro-money') ?></h2>
        </div>
        <ul class="main__icons row">
            <li class="col-md-2 main__icons__item">
                <img height="70" alt="<?= l('Transparent costs') ?>" src="images/eye.png">
                <span class="main__icons__title"><?= l('Transparent costs') ?></span>
            </li>
            <li class="col-md-2 main__icons__item">
                <img height="70" alt="<?= l('Simple procedures') ?>" src="images/document.png">
                <span class="main__icons__title"><?= l('Simple procedures') ?></span>
            </li>
            <li class="col-md-2 main__icons__item">
                <img height="70" alt="<?= l('Money will be sent immediately after approval') ?>" src="images/rocket.png">
                <span class="main__icons__title"><?= l('Money will be sent immediately after approval') ?></span>
            </li>
            <li class="col-md-2 main__icons__item">
                <img height="70" alt="<?= l('Always there for you – any time, any place') ?>" src="images/clock.png">
                <span class="main__icons__title"><?= l('Always there for you – any time, any place') ?></span>
            </li>
            <li class="col-md-2 main__icons__item">
                <img height="70" alt="<?= l('Flexible disbursement and repayment term') ?>" src="images/calendar.png">
                <span class="main__icons__title"><?= l('Flexible disbursement and repayment term') ?></span>
            </li>
            <li class="col-md-2 main__icons__item">
                <img height="70" alt="<?= l('Making life easier') ?>" src="images/hands.png">
                <span class="main__icons__title"><?= l('Making life easier') ?></span>
            </li>
        </ul>
    </div>
</div>
<?php require PHIX_CORE . '/render_view.php';