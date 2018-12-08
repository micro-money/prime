<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1;  
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $lo=intval($_GET['id']); } else { die('need loan id'); } 

$page['title'] = 'Loan #'.$lo.' info'; $page['desc'] = 'Loan infomation';

# Устанавливаем подробную информацию по лиду на первое место ($fdata->$fd)
require_once($dr.'/a/set_loan.php'); 

$fdata['fs'][]='lorc';	# Любой оператор может заказать перерасчет сделки

if (!empty($sas_eupd)) $sas_eupd=[];
$sas_eupd[]='ival_banupdate';		#   Доп Блокировка обновления хотя она избыточная 'e'=>1 и так и так не будет у безправного

function ival_banupdate($w){
	if ($w['tfn']=="lospd") {
		if ($w['sendm']==0) $w['up']=0;
		if ($w['fvm'][$w['n']]=="") {
			$w['fvm'][$w['n']]="null"; $w['tts']["t"]="m";
		}
	}	
	return $w;
}

#   Перезагрузка страницы при типовом обновлении даты остановки процентов
if (!empty($sas_eafterupd)) $sas_eafterupd=[];
$sas_eafterupd[]='ival_areload';

function ival_areload($w){
	
	if (in_array("lospd",$w['flm'])) {
		$w['outm']["eu"]=$w['hn']."/a/loanview/?id=".$w['lo'];  				
		$w['o']=calcDebt(["id"=>$w['lo'],"r"=>1,"m"=>5]);			
	}	
	
	return $w;
}


$sendm=0; $cashier=['super','cashier']; 
if (in_array($user['role'],$cashier)) {
	$sendm=1;
	// 'rm'
	$fdata['fs'][]='lorm'; # print_r($data); die(); 	
	$fdata['fs'][]='losm';	
	
	$fdata['fs']['lospd']['e']=1;	# Только у супер админа или кассира есть возможность сменить дату остановки процентов
	// В случае если мы меняем дату начисления процентов у нас должен срабатывать перерасчет по причине chspd
	
	//print_r($fdata); die();

	# Супер админ и кассир могут изменять разрешенный срок займа и разрешенную сумму перевода.
	$fdata['fs']['loterm']	['e']=1;
	$fdata['fs']['lowa']	['e']=1;
}

require_once($dr.'/a/loanview/cajx.php');							# Подключаем кастомную обработку аякса
$page['js'][] = $hn.'/a/loanview/m.js?ver='.$jsver;					# Подключаем персональный js
	
require_once($dr.'/a/set_userview.php');

require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

$cuname=$sas_sqlm['m']['uname'];  $MainPhone=$sas_sqlm['m']['ulogin'];

if ($sendm==1) {	# Мы зашли с правами кассира >Добавляем функции отправки и приема денег
	$shead='Sending money to '.$cuname;										# Шапка отправка денег
	$rhead='Recieve money from '.$cuname;									# Шапка прием денег
	$cacc='<span name="cacc">'.$sas_sqlm["lo"]["lobacc"].'<span>';			# Счет клиента
	$dvm=explode(' ',$sas_sqlm["lo"]["lodv"]); $dvmin=$dvm[0];				# Минимальная дата операционного дня
	
	$cashman=HtmlTable_option(['fs'=>['v'=>flibs(['n'=>'AllAdminList']),'ie'=>['a'=>'style="width:auto;" id="cashman"']],'tdv'=>4]);  
	$roacc=HtmlTable_option(['fs'=>['v'=>$libs['UsrOurWallet'],'ie'=>['a'=>'style="width:auto;" id="roacc"']],'tdv'=>'d3973abf-9b7e-4b85-8d53-00b9d0e416b6']);
	$soacc=HtmlTable_option(['fs'=>['v'=>$libs['UsrOurWallet'],'ie'=>['a'=>'style="width:auto;" id="soacc"']],'tdv'=>'d3973abf-9b7e-4b85-8d53-00b9d0e416b6']);
}

if ($sas_sqlm["lo"]["lost"]==20) $bd=1;
if (in_array($sas_sqlm["lo"]["lost"],[4,5,6,7,8]) && $user['role']=='super') $wod=1; 	# Списать долг может только супер из просрочек

# Вывод смены статусов и фиксированных расчетов по долгу
$lostm = db_array("select * from loans_sthist where loan=$lo order by dv ASC");
if (count($lostm)>0) {
	$lounits=[]; 
	$fn=[
		'c'	=>'Set-off day',
		'a'	=>'Amount',
		't'	=>'Days ago',
		'd'	=>'Debt body',
		'pf'=>'Percen final',
		'ps'=>'Percent start',
	];
	
	foreach ($lostm as $k=>$v) {
		$oj=json_decode($v['note'], true);

		$ddb=$oj['db'];		# Сумма основного долга (тело долга на которое начисляются проценты)
		$fp	=$oj['fp'];		# Начисленные проценты
		$lt	=$oj['lt'];		# Последнее движение по счету Дней назад
		$ot	=$oj['ot'];		# Дней в просрочке
		$wt	=$oj['wt'];		# Рабочий срок займа с момента выдачи или с момента последней пролонгации что позже
		$fs	=$oj['fs'];		# Дней назад первая выдача займа по договору
		$fr	=$oj['fr'];		# Дней назад первая оплата от клиента по договору
		$lp	=$oj['lp'];		# Дней назад факт последней пролонгации
		$c 	=$oj['c'];		# Схема расчета
		
		$tdata=htmlTable(['tab'=>$c,'head'=>$fn]);	# Таблица с расчетами
		
		$tdeb=($ddb+$fp); $tdebc='danger'; if ($tdeb<1) $tdebc='success';
		
		$ctit=$v['dv'].' ['.$libs['loans.m'][$v['m']].'] TotalDebt: <label class="bg-'.$tdebc.'">'.$tdeb.'</label> (Debt: '.$ddb.' Fee: '.$fp.')';		

		# Если есть факт смены статусов
		$stt=' Status: '.$libs['loans.st'][$v['nst']];
		if ($v['nst']!=$v['ost']) $stt=' <strong>Change</strong> Status: '.$libs['loans.st'][$v['ost']].' > '.$libs['loans.st'][$v['nst']];
		
		$ctit.=$stt;
		
		# Если есть дата остановки процентов
		if (isset($oj['spd'])) $ctit.=' Stop Percent Date: '.$oj['spd'];
	
		$lounits[]='<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#collapse'.$k.'">
							'.$ctit.'
						</a>
					</h4>
					</div>
					<div id="collapse'.$k.'" class="panel-collapse collapse">		
					  <div class="panel-body">'.$tdata.'</div>
					</div>';		
	}
	
	$lohist='<div class="panel-group">
				<div class="panel panel-default">
					'.implode('',$lounits).'
				</div>
			</div>';
}
/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<div class="row">
			<div class="col-12">
				<?#= $topalerts ?>
				<h3>Loan #<?= $lo ?>&nbsp;&nbsp;&nbsp;<?= $cuname ?>&nbsp;&nbsp;&nbsp;<a href="sip:<?= $MainPhone ?>"><?= $MainPhone ?></a>&nbsp;&nbsp;&nbsp;
				<? if (isset($bd)) { ?>
					<button type="button" class="btn btn-danger" 	data-toggle="modal" data-target="#denied">Denied for loan</button>
				<? } ?>
				<? if (isset($wod)) { ?>
					<button type="button" class="btn btn-danger" 	data-toggle="modal" data-target="#wodm">Written-off debt</button>
				<? } ?>
				<? if ($sendm==1) { ?>
					<!--
					<button type="button" class="btn btn-danger" 	data-toggle="modal" data-target="#smoney">Send money</button>
					<button type="button" class="btn btn-success" 	data-toggle="modal" data-target="#rmoney">Receive money</button>
					-->
				<? } ?>
				</h3>
			</div>
		</div>
		<? require_once($dr.'/a/tmpl/workleads.php'); ?>
		<? if (isset($bd)) require_once($dr.'/a/tmpl/deniedmodal.php'); ?>
		<? if (isset($wod)) require_once($dr.'/a/tmpl/wodmodal.php'); ?>
		<? if ($sendm==1) require_once($dr.'/a/tmpl/cashmodal.php'); ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';