<?php
	// ok only 5 banks CB, AYA, KBZ, AGD and United Amara  16/03/2017
require_once($dr.'/tool/report_func.php');
	
#require_once($dr.'/tool/app_ah_ins.php');

function acceptPost($mp){
	
	$pdata=[		# Все что мы ожидаем от клиента 
		'email'=>['email'],
		'birthdate'=>['day','month','year'],
		'Gender'=>['Gender'],
		'SecondPhone'=>['SecondPhone'],
		'City'=>['City'],
		'social'=>['social'],
		'cname'=>['cname'],
		'CompanyPhone'=>['CompanyPhone'],
		'SalaryAmount'=>['SalaryAmount'],
		'Coworker'=>['CoworkerPhone','CoworkerName'],
		'Guarantor1'=>['Guarantor1Name','Guarantor1Phone'],
		'Guarantor2'=>['Guarantor2Name','Guarantor2Phone'],
		'Guarantor3'=>['Guarantor3Name','Guarantor3Phone'],
		'Guarantor4'=>['Guarantor4Name','Guarantor4Phone'],
		'onrc'=>['onrc'],
		'payment'=>['bank_account','bank'],	#,'bank_account','domestic_remittance','payment_system'
	];

	if (!isset($mp['rq'])) $rq=['l'=>[],'u'=>[],'c'=>[],'a'=>[]]; 
	else $rq=$mp['rq']; 												# Заготовка под данные от клиента
	
	if (!isset($mp['pd'])) $pd=$_POST;
	else $pd=$mp['pd'];
	
	$user=$mp['user']; 
	
	foreach ($pdata as $par=>$v) {	# Перебираем все ожидаемые post комбинации
		$tf=0; $tw=count($v); foreach ($v as $pk=>$pn) if (isset($pd[$pn])) $tf++;
		if ($tf==$tw) {
			#echo "[$par|$pn]";
			$o=getPost(['rq'=>$rq,'par'=>$par,'pd'=>$pd]); $rq=$o['rq'];
		}	# У нас все даные есть для текущего параметра
	}
	
		if (isset($_FILES) && count($_FILES)>0) $rq=getPost(['rq'=>$rq,'par'=>'files','pd'=>$pd]);	# У нас падают сканы
		
		if (isset($rq['u']) && count($rq['u'])>0) {									# Вносим данные в таблицу users
			$upq=[]; foreach ($rq['u'] as $k=>$v) $upq[]=$k.'=\''.mysql_real_escape_string($v).'\'';	
			db_request("UPDATE `users` SET " . implode(', ', $upq) . " WHERE `id` = {$user['id']}");
		}
		if (isset($rq['l']) && count($rq['l'])>0 && $user['a_lid']>0) {				# Вносим данные в таблицу leads
			$upq=['udr=now()']; foreach ($rq['l'] as $k=>$v) $upq[]=$k.'=\''.mysql_real_escape_string($v).'\'';		
			db_request("UPDATE `leads` SET " . implode(', ', $upq) . " WHERE `id` = {$user['a_lid']}");
		}	
		if (isset($rq['c']) && count($rq['c'])>0) {									# Вносим данные в таблицу users_contacts
			
			foreach ($rq['c'] as $k=>$v) {
				$upq=["uid={$user['id']}",'dv=now()']; foreach ($v as $fn=>$fv) $upq[]=$fn.'=\''.mysql_real_escape_string($fv).'\'';		
				$q='INSERT IGNORE INTO `users_contacts` SET '.implode(',',$upq).' ON DUPLICATE KEY UPDATE it=it+1';  # .' ON DUPLICATE KEY UPDATE '.implode(',',$upq)
				$o=db_insert_ar($q);
				#  |".mysql_affected_rows()."    [".json_encode($o)."]
				if ($o['a']==2 || $o=false) aPgE("</br>[{$rq['c'][$k]['cval']}] ".l('This contact is rejected. Please input another one.').' ',true); #.json_encode($rq['c']);  # [{"cr":1,"ct":1,"cname":"Test","cval":"89027665100"}]
			}
			
		}
	
	# 09972308868
	if (isset($rq['a']) && count($rq['a'])>0) {									# Вносим данные в таблицу leads
		$upq=[];
		foreach ($rq['a'] as $k=>$v) {
			if (strlen($v)>0) {
				$dacc=onlyInList(array('o'=>'0123456789','s'=>$v));
				if (strlen($dacc)>3) {	
					$upq[]=$k.'=\''.mysql_real_escape_string($v).'\'';			# Оригинальный номер счет
					if ($k=='bacc') $upq[]='dbacc=\''.$dacc.'\'';				# Поисковая часть счета
				}
			}
		}
		if (count($upq)>0) {	# Только не пустые
			$upq[]='uid='.$user['id']; $upq[]='dv=now()'; 
			db_request("INSERT IGNORE INTO `users_accounts` SET ".implode(',',$upq));
		}
	}			
	return ['rq'=>$rq];
}	

function ah_uploadfile($mp){
	/*
	$mp=[
	'n'=>$ah_fn,			// Номер - типа файла (1: MMid, 2: BankBook, 3: PayInvoce)
	'nf'=>$ah_ff,			// Ключ файла в массиве $_FILES document.input.name (например photo1)	
	];
	*/
	
	$fdirs=['/aupload/doc/','/aupload/app/','/aupload/app/','/aupload/pay/'];

	$nfdo=$fdirs[$mp['n']].date("Ymd", time()).'/';			// Относительная Онлайн папка загружаемого файла [без имени вэбсервера]
		
	$nfdl=$_SERVER['DOCUMENT_ROOT'].$nfdo;					// Полная локальная папка загружаемого файла DR+Онлайн папка загружаемого файла
	if(!is_dir($nfdl)) mkdir($nfdl, 0777, true);
	
	Global $user;
	$uid=$user['id']; if (isset($mp['uid'])) $uid=$mp['uid'];  // pt
	$ah_fn=$mp['n']; $ah_ff=$mp['nf']; 
	if (isset($_FILES[$ah_ff])){
		if ($_FILES[$ah_ff]["error"] == 0 && substr($_FILES[$ah_ff]["type"], 0, 5) == 'image') {
			#$ah_fext=end(explode(".", $_FILES[$ah_ff]["name"]));				// Расширение файла 
			$tmp = explode('.', $_FILES[$ah_ff]["name"]);	$ah_fext = end($tmp);
			
			// Вариант рандомного 8-ми значного тела для имени файла
			$nfb = sprintf('%04x', rand(0, 65536)) . sprintf('%04x', rand(0, 65536)) ; 
			// $nfb=''.date("Ymd_His", time());
	
			$ah_fwd4=$uid.'-'.$ah_fn.'-'.$nfb.'.'.$ah_fext;
			$ah_name = $nfdl.$ah_fwd4;
			
			if (move_uploaded_file($_FILES[$ah_ff]["tmp_name"],$ah_name)) {
				//eh('(file1-ok)');
				$ah_fwd5=$nfdo.$ah_fwd4;
				$rq="INSERT INTO `users_files` (uid,ft,fp,fs,dv) VALUES ($uid,".$ah_fn.",'".$ah_fwd5."',".$_FILES[$ah_ff]["size"].",now())";
				$ag_flid=db_insert($rq);
				return $ag_flid;
			} else {
				//eh('(file1-err-moving)'); 
			}		
			
		} else {
			// попытка загрузить что то другое не image
			//eh('(file1-no image)');
		}
	}	
	return 0;
}
	
function ss_chek_string($mp){
	#Global $user;
	$ah_ph=$mp['n']; $ah_pr=''.$mp['v'];	//Обязательно конвертить в стринг
	$rq=false;	#$rq=[]; if (isset($mp['rq'])) $rq=$mp['rq'];
	
	$er_emp="<br>Field is required"; 
	
	if (isset($mp['er']))  $er_req=$mp['er']; 
	if (isset($mp['ee']))  $er_emp=$mp['ee']; 
	if (isset($mp['reg'])) $b_reg=$mp['reg']; 
	
	$empty=0; if ($ah_pr=='') $empty=1;	
	
	if ($empty==1 && !isset($mp['nones'])) {
		# Если поле пустое и отсутствует значек НЕ ТРЕБУЕТСЯ 
		# Это поле нам необходимо -> У нас ошибка пустое поле которое необходимо
		aPgE($er_emp);
		return $rq;
	} else {
		if ($empty==0) {								# Если у нас поле не пустое 
			if (isset($b_reg)) {						# Если у нас есть обяхательное условие на рег выражение
				if (!preg_match($b_reg, $ah_pr)) {		# Если не прошли проверку на рег выражение
					if (isset($er_req)) aPgE($er_req);
					return $rq;
				}
			} 
			#$ah_pf=mysql_real_escape_string($ah_pr);
			#$rq[] = "`$ah_ph` = '$ah_pf'";
			#ah_ins(['ah_fn'=>$ah_ph,'ah_val'=>$ah_pr]);
			$rq=$ah_pr;
		}		
	}	
	return $rq;
}

function ss_chek_phone($mp){ // Обработка по типу телефон
	#Global $user;
	$ah_ph=$mp['n']; $ah_pr=''.$mp['v'];	//Обязательно конвертить в стринг
	$rq=false; #$rq=[]; if (isset($mp['rq'])) $rq=$mp['rq'];
	
	$er_dub="<br>You have already used before this phone";  
	$er_emp="<br>Not Specified phone number"; 
	$er_req="<br>Error in the phone! You must enter 7-11 digits without spaces."; 
	$b_reg="|^[0-9]{7,11}$|i"; 
	
	if (isset($mp['er']))  $er_req=$mp['er']; 
	if (isset($mp['ee']))  $er_emp=$mp['ee']; 
	if (isset($mp['ed']))  $er_dub=$mp['ed'];
	if (isset($mp['reg'])) $b_reg=$mp['reg']; 
	
	$empty=0; if ($ah_pr=='') $empty=1;
	
	if ($empty==1 && !isset($mp['nones'])) {
		# Если поле пустое и отсутствует значек НЕ ТРЕБУЕТСЯ 
		# Это поле нам необходимо -> У нас ошибка пустое поле которое необходимо
		aPgE($er_emp);
		return $rq;
	} else {
		if ($empty==0) {
			# Если у нас поле не пустое 
			if (isset($b_reg)) {
				if (!preg_match($b_reg, $ah_pr)) {
					# Если не прошли проверку на рег выражение
					if (isset($er_req)) aPgE($er_req);
					return $rq;
				}				
			}
			# Поле не пустое и оно удовлетворяет рег выражению
			if (dublphone(['s'=>$mp['ds'],'n'=>$ah_ph,'v'=>$ah_pr])==0) {
				#$ah_pf=mysql_real_escape_string($ah_pr);
				#$rq[] = "`$ah_ph` = '$ah_pf'";
				#ah_ins(['ah_fn'=>$ah_ph,'ah_val'=>$ah_pr]);
				#$user[$ah_ph]=$ah_pr;
				$rq=$ah_pr;
			} else aPgE($er_dub);			
		}
	}
	return $rq;
}

function dublphone($mp){	// Тест телефона на уже ранее введенный $mp=['n'=>'Имя ключ телефона:SecondPhone','v'=>'Номер телефона цифры:1234']
	return 0;  # от 10.03.2017 по просьбе Антона на 10 дней отключаем проверку телефонов
	if ($mp['v']=='') return 0;		// Если нет данных вылетаем
	
	Global $user;
	$phn=[]; $pha=[]; 
	
	$phq=['Phone','SecondPhone','CompanyPhone','CoworkerPhone','Guarantor1Phone','Guarantor2Phone','Guarantor3Phone','Guarantor4Phone'];
	
	if ($mp['s']==0)  $ur=1;
	if ($mp['s']==11) $ur=2; 
	if ($mp['s']==12) $ur=3;
	if ($mp['s']==21) $ur=4; 
	if ($mp['s']==22) $ur=5;
	if ($mp['s']==31) $ur=6;
	if ($mp['s']==32) $ur=7;
	
	for ($x=0; $x<$ur+1; $x++) $phn[]=$phq[$x];
	
	
	if ($mp['s']==10) $phn[]=$phq;		// Таблица все в одном
	
	foreach ($phn as $k=>$v) if ($mp['n']!=$v && isset($user[$v])) $pha[]=$user[$v]; 
	foreach ($phn as $k=>$v) if ($mp['n']!=$v && in_array($mp['v'],$pha)) return 1;		// Если такой телефон уже есть в рабочем массиве
	//print_r($pha); echo '['.$mp['v'].'|'.$mp['s'].']';
	return 0;
}

function getPost($mp){	#  $rq=getPost(['rq'=>$rq,'par'=>$par]); $_POST
	Global $libs;
	$rq=$mp['rq']; $par=$mp['par'];
	$pd=$mp['pd']; 
	
	$val=false; if (isset($pd[$par])) $val=$pd[$par];
	
	if ($par=='email') if (strlen($val)>4) $rq['c'][] = ['cr'=>0,'ct'=>2,'cname'=>'Email','cval'=>$val]; 
	
	if ($par=='birthdate') {			# День рождения pe
		$day= intval($pd['day']); $month= intval($pd['month']); $year= intval($pd['year']);
		$BirthDate = sprintf("%02d.%02d.%04d", $day, $month, $year);
		if (empty($BirthDate)) aPgE("<br>BirthDay not specified!");
		else {
			$BirthDate_ = explode('.', $BirthDate);
			$BirthDate = sprintf("%04d-%02d-%02d", $BirthDate_[2], $BirthDate_[1], $BirthDate_[0]);
			if ($BirthDate_[2]=='0000' || $BirthDate_[1]=='00' || $BirthDate_[0]=='00') $BirthDate='2000-01-01';
			$rq['u']['birthdate'] = $BirthDate;
		}
	}
	
	if ($par=='Gender') if (isset($libs['users.gender'][$val])) $rq['u']['gender'] = $val;	

    if ($par=='SecondPhone') {			# Второй телефон  (НЕОБЯЗАТЕЛЕН)
		$ss_mp=[	
		'n'=>'SecondPhone','v'=>$val,
		'ds'=>0,'nones'=>1,
		'ed'=>"<br>You have already wrote before same phone number.<br>Please provide another phone number (if you have) or leave this field empty.",
		'er'=>"<br>An error in the phone number! You must enter 7-11 digits without spaces."
		];
		$cv=ss_chek_phone($ss_mp); 
		if ($cv) $rq['c'][] = ['cr'=>0,'ct'=>1,'cname'=>'Sec phone','cval'=>$cv];
	}
	
    if ($par=='City') {					# Город
		$ss_mp=['n'=>'City','v'=>$val,'ee'=>"<br>City not specified!"];
		$cv=ss_chek_string($ss_mp); if ($cv) $rq['u']['city'] = $cv;
	}
	
	if ($par=='social') {				# Город
		if (isset($libs['users.social'][$val])) $rq['u']['social'] = $val;	
	}

	
	if ($par=='cname') {				# Город
		$ss_mp=['n'=>'cname','v'=>$val,'ee'=>"<br>Name of the Company not specified!"];	
		$cv=ss_chek_string($ss_mp); if ($cv) $rq['u']['cname'] = $cv;	
	}
	
	if ($par=='CompanyPhone') {			# Телефон Компании
		$ss_mp=[
		'n'=>'cphone','v'=>$val,'ds'=>11,
		'ed'=>"<br>You have already used before this Phone of the Company",
		'ee'=>"<br>Phone of the Company not specified!",
		'er'=>"<br>Error in operating room phone! You must enter 7-11 digits without spaces."	
		];
		$cv=ss_chek_phone($ss_mp); if ($cv) $rq['u']['cphone'] = $cv;	
	}
	
	if ($par=='SalaryAmount') {			# Город
		$ss_mp=['n'=>'salary','v'=>$val,'ee'=>"<br>Not Set salary!"];
		$cv=ss_chek_string($ss_mp); if ($cv && $cv>0) $rq['u']['salary'] = $cv;	
	}
	
	if ($par=='Coworker') {		# Телефон коллеги (НЕОБЯЗАТЕЛЕН)
		# Телефон коллеги (НЕОБЯЗАТЕЛЕН)
		$ss_mp=[
		'n'=>'CoworkerPhone','v'=>$pd['CoworkerPhone'],'ds'=>12,'nones'=>1,
		'ed'=>"<br>You have already used before this coworker phone.",
		'er'=>"<br>Error in coworker phone number! You must enter 7-11 digits without spaces."	
		];
		$cwp=ss_chek_phone($ss_mp); 

		# Имя коллеги (НЕОБЯЗАТЕЛЕН) 
		$ss_mp=['n'=>'CoworkerName','v'=>$pd['CoworkerName'],'nones'=>1,];
		$cwn=ss_chek_string($ss_mp); 
		
		if ($cwp && $cwn)  $rq['c'][] = ['cr'=>5,'ct'=>1,'cname'=>$cwn,'cval'=>$cwp];	
	}
	
	if ($par=='Guarantor1') $rq=doGuarantor(['rq'=>$rq,'n'=>1,'nv'=>$pd['Guarantor1Name'],'pv'=>$pd['Guarantor1Phone']]);
	if ($par=='Guarantor2') $rq=doGuarantor(['rq'=>$rq,'n'=>2,'nv'=>$pd['Guarantor2Name'],'pv'=>$pd['Guarantor2Phone']]);
	if ($par=='Guarantor3') $rq=doGuarantor(['rq'=>$rq,'n'=>3,'nv'=>$pd['Guarantor3Name'],'pv'=>$pd['Guarantor3Phone']]);
	if ($par=='Guarantor4') $rq=doGuarantor(['rq'=>$rq,'n'=>4,'nv'=>$pd['Guarantor4Name'],'pv'=>$pd['Guarantor4Phone']]);
		
	if ($par=='onrc') {						# Номер паспорта		
		$ss_mp=['n'=>'origUsrMMPersonalID','v'=>$val,'ee'=>"<br>Missing passport information!"];
		$cv=ss_chek_string($ss_mp); 
		if ($cv) {
			$rq['u']['onrc'] = $cv;			# У нас в итоговом запросе есть id паспорта > форматируем ее в трансли
			$bw=getMmToEngFormatArray();	# Подготавливаем рабочий массив форматирования
			$fd=FormatMmPersId(['bw'=>$bw,'mmid'=>$cv]);	# Форматируем  Паспотр, если скрипт сомневается тогда он верет Оригинал
			$rq['u']['fnrc'] = $fd['fin'];
		}
	}
	
	if ($par=='payment') {					# Каким образом клиент хочет получить деньги 
		
		#$how 	= intval($pd['how']);  
		
		$bank 	= intval($pd['bank']);
		if (isset($pd['bank_account'])) $oacc= $pd['bank_account'];
		
		
		#if (isset($pd['domestic_remittance'])) 	$domest = $pd['domestic_remittance'];
		#if (isset($pd['payment_system'])) 		$paysys = $pd['payment_system'];
		
		if (isset($oacc)) {
			$rq['l']['oacc'] = $oacc;
			$rq['l']['facc'] = bankFormat(array('r'=>0,'s'=>$oacc));
			$rq['a']['bacc'] = $rq['l']['facc'];
		}
		
		/*
		if (isset($libs['leads.how'][$how])) {
			$rq['l']['how'] = $how;
			switch ($how) {
				case 2:
					if (empty($bank)) aPgE("<br>Unknown Bank!");
					else if (isset($libs['bank_id'][$bank])) { $rq['l']['bank'] = $bank; $rq['a']['bid'] = $bank; }
					if (!isset($bacc)) aPgE("<br>Not specified bank account!");
					else $oacc=$bacc; 
					break;
				case 3:
					if (!isset($domest)) aPgE("<br>Transfer details not specified!");
					else $oacc=$domest; 
					break;
				case 4:
					if (!isset($paysys)) aPgE("<br>Payment system Missing!");
					else $oacc=$paysys; 
					break;
				default:
					break;
			}
			
			if (isset($oacc))	{		# Фиксиуем оригинальное и отформатированное название счета
				$rq['l']['oacc'] = $oacc;
				$rq['l']['facc']=bankFormat(array('r'=>0,'s'=>$oacc));
				$rq['a']['bacc']=$rq['l']['facc'];
			}
		}	
		*/
	}
	if ($par=='files') {					# Сканы от клиента pe
		// [name] => Фото0025.jpg [type] => image/jpeg [tmp_name] => /tmp/phpI1VMQz [error] => 0 [size] => 903549
		// Фото1
		$ah_fn=1; $ah_ff='photo1';
		$ag_flid=ah_uploadfile(['n'=>$ah_fn,'nf'=>$ah_ff]);	
		#if ($ag_flid>0) $rq[] = "`f$ah_fn` = $ag_flid";

		// Фото2
		$ah_fn=2; $ah_ff='photo2';
		$ag_flid=ah_uploadfile(['n'=>$ah_fn,'nf'=>$ah_ff]);	
		#if ($ag_flid>0) $rq[] = "`f$ah_fn` = $ag_flid";	 
	}
	return ['rq'=>$rq];
}

function doGuarantor($mp){	# doGuarantor(['rq'=>$rq,'n'=>$n,'nv'=>$nv,'pv'=>$pv]);
	$rq=$mp['rq']; $n=$mp['n']; $nv=$mp['nv']; $pv=$mp['pv'];
	$dsm=[1=>21,2=>22,3=>23,4=>24];
	# Поручитель Имя
	$ss_mp=['n'=>'Guarantor'.$n.'Name','v'=>$nv,'ee'=>"<br>Not Specified name of the person # $n"];
	$gn=ss_chek_string($ss_mp);
	# Поручитель Телефон
	$ss_mp=['n'=>'Guarantor'.$n.'Phone','v'=>$pv,'ds'=>$dsm[$n],
	'ed'=>"<br>You have already used before this phone of the person # $n",
	'ee'=>"<br>Not Specified phone of the person # $n Warranty",
	'er'=>"<br>Error in the room phone warranty person # $n! You must enter 7-11 digits without spaces."];
	$gp=ss_chek_phone($ss_mp);  
	if ($gn && $gp)  $rq['c'][] = ['cr'=>$n,'ct'=>1,'cname'=>$gn,'cval'=>$gp];	
	return $rq;
}

function ah_initPaymentJS() {
	return <<<JS
    moneyChoose = $("#application_money_choose_id");
    moneyChoose.on("change",function(b){
        var c;
        $("@toggleInput").addClass("hidden");
        $("@toggleInput").find(".form-control").attr("disabled","disabled");
        c = $("@toggleInput[data-id="+$(this).val()+"]");
        b = c.find(".form-control");
        c.removeClass("hidden");
        return b.attr("disabled", !1);
    });
JS;
}
function ah_initMessenger($mp) {
	return <<<JS
(function(){ var widget_id = '5AnErLKIGR';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
JS;
/*
<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
</script>
<!-- {/literal} END JIVOSITE CODE -->
*/
}
function ah_initTime($mp) {
	return <<<JS
	function get_timer_173(string_173) {
		var date_new_173 = string_173; 
		var date_t_173 = new Date(date_new_173);
		var date_173 = new Date();
		var timer_173 = date_t_173 - date_173;
		if(date_t_173 > date_173) {
			var day_173 = parseInt(timer_173/(60*60*1000*24));
			if(day_173 < 10) {
				day_173 = "0" + day_173;
			}
			day_173 = day_173.toString();
			var hour_173 = parseInt(timer_173/(60*60*1000))%24;
			if(hour_173 < 10) {
				hour_173 = "0" + hour_173;
			}
			hour_173 = hour_173.toString();
			var min_173 = parseInt(timer_173/(1000*60))%60;
			if(min_173 < 10) {
				min_173 = "0" + min_173;
			}min_173 = min_173.toString();
			var sec_173 = parseInt(timer_173/1000)%60;
			if(sec_173 < 10) {sec_173 = "0" + sec_173;}
			sec_173 = sec_173.toString(); 
			timethis_173 =  hour_173 + " : " + min_173 + " : " + sec_173;
			/* day_173 + " : " + */
			/* $(".timerhello_173 p.result .result-day").text(day_173); */
			$(".timerhello_173 p.result .result-hour").text(hour_173);
			$(".timerhello_173 p.result .result-minute").text(min_173);
			$(".timerhello_173 p.result .result-second").text(sec_173);
			}else {
				/* $(".timerhello_173 p.result .result-day").text("00"); */
				$(".timerhello_173 p.result .result-hour").text("00");
				$(".timerhello_173 p.result .result-minute").text("00");
				$(".timerhello_173 p.result .result-second").text("00");
			} 
	}
	function getfrominputs_173(){
		string_173 = $("#set_string_173").attr("timerset"); /* "03/09/2017 00:00";  */
		get_timer_173(string_173);
		setInterval(function(){get_timer_173(string_173);},1000);
	}
	$(document).ready(function(){ getfrominputs_173();});
JS;
}

# Webset старый =============================

# Возвращает заголовок для приложения 
function getMadheader(){
	Global $user;
	if (empty($user)) {
		return "Mapl: 0";
	} else {
		# Проверяем есть ли дата от приложения для текущего клиента 
		$user['mad']=[];  $mad = db_array("SELECT id,dt,vol,`desc`,dv FROM `users_mapdata` WHERE `user_id` = {$user['id']}");
		if (count($mad)>0) {
			$user['mad']=$mad; return "TeslaX: ".$user['id'];
		} else {
			return "Mapl: ".$user['id'];
		}
	}	
}
# Строка запроса на установку таймера 
function setTimerSql($user){ return "UPDATE `leads` SET `stimer` = DATE_ADD(now(), interval 1435 MINUTE) WHERE `id` = {$user['a_lid']}"; }

function getUserData($mp){	# Возвращает всю информацию по клиенту , либо с активной анкеты либо с указанно либо без нее.
	Global $user;
	
	$uid=$mp['uid']; $lid=0; if (isset($mp['lid'])) $lid=$mp['lid'];
	$uo=db_array("SELECT * FROM users WHERE id=$uid");  $s=[]; $l=[]; $c=[]; $u=[]; 
	if (count($uo)>0) {	$u=$uo[0];	$user=array_merge($user,$u);	 if ($lid==0) $lid=$u['a_lid'];
		$co=db_array("SELECT id,cr,ct,cname,cval,cs FROM users_contacts WHERE uid=$uid and cs!=5 order by ct,cp DESC,dv DESC");
		foreach ($co as $k=>$v) {
			$ck=$v['cr'].'-'.$v['ct'];	if (!isset($c[$ck])) $c[$ck]=[];
			$c[$ck][]=$v;
		}
		$so=db_array("SELECT id,ft,fp,h FROM users_files WHERE uid=$uid and ft in (1,2) and ac!=4");
		if (count($so)>0) foreach ($so as $k=>$v) $s[$v['ft']]=[$v];

		if ($lid>0) {	# Если у нас есть лид к запросу
			$lm=db_array("SELECT id,uid,st,ramount,rdays,racc,how,bank,facc,oacc,cst,date_add(udr, interval 24 hour) stimer FROM `leads` WHERE id={$u['a_lid']}");  $l=$lm[0];	
		}
	}
	return ['c'=>$c,'s'=>$s,'l'=>$l,'u'=>$u]; 
}

function checkUser($euid = false) {	# Тип анкеты для клиента
	Global $user; 
	
	if (!$euid) $us=$user; 		
	else $us = db_array("select * from users where id=$euid");

	$uid=$us['id'];
	
	$aleads = db_array("select id,st,cst,oacc,bank,how,racc,ramount,date_add(udr, interval 24 hour) stimer from leads where uid=$uid and st<25 order by id desc");
	$aloans = db_array("select id,st,lid from loans where uid=$uid order by id desc"); 	
	
	$lim=[]; foreach ($aleads as $k=>$v) $lim[$v['id']]=$v; 
	$lom=[]; foreach ($aloans as $k=>$v) $lom[$v['id']]=$v; 
	
	# Долги [2,3,4,5,6,7,8,16]
	# ОК закрыта [19]
	# pipe [20]
	# проблемы [25,26,27,28,29]
	$waits=[]; $opens=[]; $closes=[];
	foreach ($lom as $k=>$v) {					# Форматируем сделки в асоц массив чтобы знать какие активные сделки есть за клиентом 
		$lst=$v['st'];	
		if ($lst==19) $closes[]=$v['id'];			# Если сделка погашена
		if (in_array($lst,[2,3,4,5,6,7,8,16,25,26,27,28,29])) $opens[]=$v['id'];			# Если сделка в работе
		if ($lst<2) $waits[]=$v['id'];				# Если сделка в ожидании
	}
	
	$wait=count($waits); $open=count($opens); $closed=count($closes);
	#print_r($waits); echo "[here2 / $wait]";	
	# Переписываем кол-во открытых и закрытых сделок их предрасчетных значений
	# $open=$us['a_od'];
	# $closed=$us['a_cd'];

	# По умолчанию первичную + старая анкета + сводка по сделкам
	$o=['h'=>'app_wizard','a'=>0,'o'=>$open,'w'=>$wait,'c'=>$closed,'ll'=>$aleads];	
	if ($open>0) {											# ($open+$wait)>0 Если у клиента есть активная сделка(ки) > никаких новых заявок	
		$o['h']="scan_wizard";								# Если у клиента есть не закрытая сделка(ки) > скан на оплату
	} else {
		
		/*
		Ситуация: 
			У нас есть более одного займа в ожидании на клиента > Оставляем самый последний - остальные авто отказ с комментарием.
			У нас есть активные сделки.
				1. Если у нас есть займ в ожидании 
					> берем номер его заявки и восстанавливаем users.a_lid все остальные заявки гасим.
				2. Займа в ожидании нет.
					> Гасим лищние активные сделки если они есть. Восстанавливаем users.a_lid если необходимо.
		*/
			
		$us_up=[];
			
		if ($wait>0) {								# Если есть займы на рассмотрении
			
			$fa_lid=$lom[$waits[0]]['lid']; 				# Если у нас есть займ на рассмотрении > у нас активная заявка должна быть только от него.
			if ($wait>1) {									# У нас есть более одного займа на рассмотрении 	
				# Оставляем самый последний - остальные авто отказ с комментарием.			
				$lup=['st'=>18,'UsrNotes'=>'concat(UsrNotes, "\nAuto Deny: More than one active at a time")']; 
				$park_lo=$waits; unset($park_lo[0]); # Исключаем первый займ > Он остается в ожидании		
				foreach ($park_lo as $lo) fixLoans(['lup'=>$lup,'id'=>$lo]);	# Принудительно паркуем оставшиеся в ожидании сделки
			}
		}
		
		if (count($aleads)>0) {								# Если есть активные заявки
			
			if (!isset($fa_lid)) $fa_lid=$aleads[0]['id'];	# Фактическая активная заявка (либо единственная если все ок, либо самая последняя - если ошибка)
			if (count($aleads)>1 || $fa_lid!=$aleads[0]['id']) {
				# Если у клиента более одной активной заявки 
				# Или основной активная заявка не равна первой активной заявке по списку.
				# Надо принудительно закрыть все лишние кроме основной.
				db_request("update leads set st=27 where uid=$uid and st<25 and id!=$fa_lid");
			}
			
		} else {
			if (!isset($fa_lid)) $o['a']=1; 				# Если у клиента нет активной заявки > Создаем ее
		}

		# Если у нас есть номер активной заявки
		# Если у клиента номер последней сделки не равен номеру активной заявки > надо этот номер восстановить
		if (isset($fa_lid) && $us['a_lid']!=$fa_lid) $us_up['a_lid']=$fa_lid;
		
		# Если у нас есть что исправить в таблице users
		if (count($us_up)>0) arrToUpdate(['t'=>'users','u'=>$us_up,'i'=>$uid]);
			
		if ($closed>0) $o['h']="vip_wizard"; 				# Вип анкета если у клиента до этого уже есть закрытые сделки 
	}
	
	if (!$euid) $user=$us;	# Глобально переписываем user если мы работаем с глобальным
	
	return $o;
}

function stepm_wizard(){	# Расчет оставшихся шагов для первичной анкеты
	Global $lead,$user; $stepm=[];
	$stepm=[]; 	# Проверяем обязательные параметры на наличие city
	if (empty($user['birthdate']) ||  $user['gender']==2 ||  empty($user['city'])) $stepm[]=0;	# Если нет дня рождения или не указан пол
	
	# Если нет ЗП или данных по работе
	if (empty($user['salary']) || empty($user['cphone']) ||  empty($user['cname'])) $stepm[]=1;	
	
	# 5й шаг это обязательное требование установить приложение 
	if ($user['mad']==0 ) $stepm[]=5;	
	
	# Если нет телефонов у Поручителей 1 и 2
	if ($user['mad']==0 && (!isset($lead['c']['1-1']) ||  !isset($lead['c']['2-1']))) $stepm[]=2;		
	
	# Если банковских реквизитов нет или номера паспорта $lead['l']['oacc'].
	if (
		$lead['u']['onrc'].$lead['u']['fnrc']=='' || ($lead['l']['racc']==0 && $lead['l']['facc']=='') 
		) $stepm[]=3;			
	
	# Если нет двух сканов
	#print_r($lead); die();
	#if (count($lead['s'])!=2) $stepm[]=4;
	if (!isset($lead['s'][2])) $stepm[]=4;
	
	
	
	return $stepm;
}
?>