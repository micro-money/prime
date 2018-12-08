<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');
$page['title'] = 'Sec';  $page['desc'] = 'Security Report';

require_once(MC_ROOT.'/tool/report_func.php');
require_once(MC_ROOT.'/tool/sec_func.php');
require_once(MC_ROOT.'/tool/uni_func.php');

//if ($user['login']!='donsergey') die ('sorry '.$user['login'].' - Report in the error correction phase. Will be available in the next hour.');

$report=''; $lh=''; $db='';

/*
	Алгоритм простая рекурсия по счету:
	Телефон или Счет или Паспорт - Все ищем через счет.

	Телефон - выводим все счета этого телефона
	Счет просто приводим.
	Паспорт - выводим все счета этого паспорта

	В ИТОГЕ ИМЕЕМ СЧЕТ(А) и понеслась рекурсия 
РЕК	 снимаем всех пользователей у которых есть такие счета по application_baccount.user_id
	 получаем список всех счетов этих пользователей сгруппированный .
	 Фиксируем кол-во счетов.
	 
РЕК	 Далее запрашиваем всех пользователей по этому списку + все счета этих пользователей и смотрим кол-во счетов 
	 если счета добавились еще раз. Если нет стоп.
	 
РЕК ...
	 
	 На каждом обороте по счету фиксируем номер обхода.
*/

//$_POST['ChPhone']='09442157396';

if (isset($_POST['u_acc']) || isset($_POST['u_nrc']) || isset($_POST['u_imei'])) {
	$u_acc_ch=''; if (isset($_POST['u_acc'])) $u_acc_ch='checked';
	$u_nrc_ch=''; if (isset($_POST['u_nrc'])) $u_nrc_ch='checked';
	$u_imei_ch=''; if (isset($_POST['u_imei'])) $u_imei_ch='checked';
} else {
	$u_acc_ch='checked'; $u_nrc_ch='checked'; $u_imei_ch='checked';
}

if (!empty($_POST['ChBankAccount']) || !empty($_POST['ChPhone'])) {
	//header("Content-type: text/plain; charset=utf-8"); header("Cache-Control: no-store, no-cache, must-revalidate");header("Cache-Control: post-check=0, pre-check=0", false); 
}

$report_table=[];
if (!empty($_POST['ChBankAccount']) && count($report_table)==0) {
	
	# Делаем проверку по банковскому номеру
	# Подготавливаем банковский номер на проверку - чистим его от букв
	$bankaccs=onlyInList(array('o'=>$onlyDig,'s'=>$_POST['ChBankAccount']));
	//die ("|".$_POST['ChBankAccount']."|");
	if (strlen($bankaccs)>6) {
		$lh.='<br>Input Bank Account : '.$bankaccs;
		# Получаем все телефоны по этому счету
		$rq="SELECT phone as id FROM un_acc as a where val='$bankaccs' group by 1";
		$phones = db_array($rq);
		if (count($phones)>0) {	
			# Ищем всех юзеров которые использовали этот счет , далее ищем всех юзеров которые использовали эти счета
			$omf=getLinks(['phones'=>$phones,'cheks'=>getPostCheck()]); 
		} else {
			$rstr=' was not found in database';
		}
	} else {
		aPgE("<br>You must enter more than 6 digits in bank account field.");
	}
	
}

if (!empty($_POST['ChPhone']) && count($report_table)==0) {

	# Делаем проверку по телефону
	# Форматируем телефон
	$fphone=onlyInList(array('o'=>$onlyDig,'s'=>$_POST['ChPhone']));
	$lh.='<br>Input Phone Number : '.$fphone;
	# Получаем все ключевые телефоны с этим номером
	$qw="SELECT phone as id FROM un_tel WHERE val='$fphone' group by 1";
	$phones = db_array($qw);

	# Ищем всех юзеров которые использовали этот счет , далее ищем всех юзеров которые использовали эти счета
	if (count($phones)>0) {
		$omf=getLinks(['phones'=>$phones,'cheks'=>getPostCheck()]);  //  ,'cheks'=>['un_acc','un_nrc','un_imei']
	} else {
		$rstr=' was not found in database';
	}
}

if ((!empty($_POST['ChNrc']) || !empty($_POST['ChNrcD'])) && count($report_table)==0) {
	# Делаем проверку по номеру паспорта
	# Форматируем паспорт
	# Подготавливаем рабочий массив форматирования
	 
	if (!empty($_POST['ChNrcD']) && empty($_POST['ChNrc'])) {
		$nrcmode=1;
		$nrcp='ChNrcD';
	} else {
		$nrcmode=0;
		$nrcp='ChNrc';
	}
	
	$bw=getMmToEngFormatArray();
	$nrcorig=mysql_real_escape_string($_POST[$nrcp]);
	# Форматируем ID , если да то ФОРМАТ, если скрипт сомневается тогда -> Оригинал
	$fm=FormatMmPersId(['bw'=>$bw,'mmid'=>$_POST[$nrcp]]);
	#if ($nrcmode==1) $fm['fin']=onlyInList(array('o'=>$onlyDig,'s'=>$fm['fin']));
	$_POST[$nrcp]=$fm['fin'];
	$fnrc=mysql_real_escape_string($fm['fin']);
	# Получаем все счета за этим паспортом
	
	/*
	$nrcqvery="LCASE(u.UsrMMPersonalID) in (LCASE('$fnrc'),LCASE('$nrcorig'))";
	if ($nrcmode==1) {
		$ms = str_split($fnrc); 
		$nrcqvery="u.UsrMMPersonalID like '%".implode("%",$ms)."%'";
	}
	
	$q1="SELECT a.bacc as id FROM application_baccount as a,users as u where $nrcqvery and a.user_id=u.id group by 1";
	$bankacc = db_array($q1);
	*/
	
	$rq="SELECT phone as id FROM un_nrcf where val='$fnrc' group by 1";
	$phones = db_array($rq);
	
	# Ищем всех юзеров которые использовали этот счет , далее ищем всех юзеров которые использовали эти счета
	if (count($phones)>0) {
		$omf=getLinks(['phones'=>$phones,'cheks'=>getPostCheck()]);
	} else {
		$rstr=' was not found in database';
	}
}

function getPostCheck() {
	$cheks=['un_acc','un_nrcf','un_imei'];
	if (isset($_POST['u_acc']) || isset($_POST['u_nrc']) || isset($_POST['u_imei'])) {
		$cheks=[];
		if (isset($_POST['u_acc'])) $cheks[]='un_acc';
		if (isset($_POST['u_nrc'])) $cheks[]='un_nrcf';
		if (isset($_POST['u_imei'])) $cheks[]='un_imei';
	} 
	return $cheks;
}

if (isset($omf)) if (count($omf['rt'])>0 || count($omf['lt'])>0 || isset($rstr) || count($omf['ulist'])>0) {

	//$eva='if ($tdv>0) $tdv="<a href=\"/admin/report_app.php?uid=".$rv["UserId"]."\">app</a>";'; 'etd_mad'=>$eva
	foreach ($omf['m'] as $k=>$v) if (count($v['k'])>0) $lh.='<br>['.$k.'] : '.implode(',',$v['k']);
	//$eva='$tdr=" rrr ";';	
	if (count($omf['rt'])>0 || count($omf['lt'])>0) {
		$nmp=['tab'=>$omf['rt']];
		#if (count($omf['bad'])>0 || count($omf['good'])>0) {
			if (count($omf['bad'])>0) {
				$rstr='<font color="red">in debt '.count($omf['bad']).' times. See table for details</font>';
			} else {
				if (count($omf['good'])>0) {
					$rstr='<font color="green">very good</font>';
				} else {
					$rstr=' just new one';
				}
			}
			$eva='if (in_array($rk,$mp["bad"])) $tdr="class=\"alert-danger\""; if (in_array($rk,$mp["good"])) $tdr="class=\"alert-success\"";';	
			$nmp['etr']=$eva;
			$nmp['bad']=$omf['bad'];
			$nmp['good']=$omf['good'];
		#}
		#print_r($omf); die();
	    $rdata='<h3>Crm Opportunity list:</h3>'.htmlTable($nmp);	
		$ldata=htmlTable(['tab'=>$omf['lt']]);
		$eva='if (strlen($tdv)>2) $tdv="<a href=\"/a/appdata/?uid=".$rv["UserId"]."\">".$rv["Imei"]."</a>";'; #
		$wdata=htmlTable(['tab'=>$omf['wt'],'etd_Imei'=>$eva]);
		$report='<br><h3>User '.$rstr.'</h3>'.$rdata.'<br><h3>Crm Leads list</h3>'.$ldata.'<br><h3>Web Leads list</h3>'.$wdata;			
	} 			
}

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
    <div class="container-fluid">
	<div class="row">
        <h2 class="alert-danger">Beta version in testing</h2>
		<!--<h2>Click <a href="/admin/report1.php?ban=2&alltime=1" target="_blank">here to open</a> the list where the badguy try to take another loan</h2>-->
		<h2>Security cheking <strong>(pls use each field in turn to check)</strong></h2>
		<h4>first > Phone , second > Bank Account, third > Nrc </strong></h4>
		<div class="col-xs-12 mtop-20">
			<form method="post" enctype="multipart/form-data">
				<table class="table">
					<thead>
						<tr>
							<th>1) Phone</th>
							<th>2) Bank Account</th>
							<!--<th>3) NRC (Test full)</th>
							<th>4) NRC (Test Only dig)</th>-->
						</tr>						
					</thead>				
					<tbody>
						<tr>
							<td width="200px"><input type="tel" name="ChPhone" class="form-control" id="ChPhone" value="<?= pstd('ChPhone') ?>"></td>
							<td><input type="tel" name="ChBankAccount" class="form-control" id="ChBankAccount" value="<?= pstd('ChBankAccount') ?>"></td>
							<? /*<td width="200px"><input type="text" name="ChNrc" class="form-control" id="ChNrc" value="<?= pstd('ChNrc') ?>"></td>
							<td width="200px"><input type="text" name="ChNrcD" class="form-control" id="ChNrcD" value="<?= pstd('ChNrcD') ?>"></td> */ ?>
						</tr>
					</tbody>
				</table>
				Unions parameters used in searching: &nbsp;&nbsp;&nbsp;&nbsp; 
				<label class="checkbox-inline"><input <?= $u_acc_ch ?> name="u_acc" type="checkbox" value="">Bank Acc</label>
				<label class="checkbox-inline"><input <?= $u_nrc_ch ?> name="u_nrc" type="checkbox" value="">NRC 6dig</label>
				<label class="checkbox-inline"><input <?= $u_imei_ch ?> name="u_imei" type="checkbox" value="">IMEI</label>
				<br>
				<input type="submit" class="btn btn-success" value="Chek This">
			</form>
        </div>
    </div>
	<div class="row">
			<h2>Security result report </h2>
			<ul class="nav nav-pills">
			<li class="active"><a data-toggle="pill" href="#report">Report</a></li>
			<li><a data-toggle="pill" href="#linkhistory">Link History</a></li>
			<li><a data-toggle="pill" href="#debug">Debug</a></li>
		</ul>

		<div class="tab-content">
			<div id="report" class="tab-pane fade in active">
				<?= $report ?>
			</div>
			<div id="linkhistory" class="tab-pane fade">
				<?= $lh ?>
			</div>
			<div id="debug" class="tab-pane fade">
				<?= $db ?>			
			</div>
		</div>
	</div>
</div>
<?php require PHIX_CORE . '/render_view.php';