<?php
	/*
$ss_banklista=[
	1=>'Other',
	2=>'KBZ Bank',
	3=>'AYA Bank',
	4=>'CB Bank',
	5=>'AGD Bank',
	9=>'United Amara Bank',
];
	
	6=>'Yoma Bank',
	7=>'Innwa Bank',
	8=>'Myanmar Apex Bank',
	10=>'Myawaddy',
	11=>'Rural Development Bank',
	12=>'First Private bank',
	*/
	
		
	/*
	switch ($step) {
		case 0:														# mail,birthday,sec phone,gender
			$rq=step0($rq); 
			break;
		case 1:														# social,salary,work data,coworker data
			$rq=step1($rq);
			break;
		case 2:														# Дополнительные контакты
			$rq=step2($rq);											
			break;
		case 3:														# Банковские реквизиты
			$rq=step3($rq);			
			break;
		case 4:														# Сканы паспорта и банка
			$rq=step4($rq);
			break;
		default: header("Location: /"); exit;
	}
	*/
		
function ss_chek_bank($rq){
	Global $page,$libs;
	
	$how = intval($_POST['how']);
	$bank_account = $_POST['bank_account'];
	$domestic_remittance = $_POST['domestic_remittance'];
	$payment_system = $_POST['payment_system'];
	$bank = intval($_POST['bank']);
	
    # Каким образом клиент хочет получить деньги 
	
	if (isset($libs['loans.how'][$how])) {
		$rq['l']['how'] = $how;
	
		switch ($how) {
			case 2:
				if (empty($bank)) $page['error_msg'] .= "<br>Unknown Bank!";
				else if (isset($libs['bank_id'][$bank])) $rq['l']['bank'] = $bank;
				if (empty($bank_account)) $page['error_msg'] .= "<br>Not specified bank account!";
				else {
					/*
					$m='@@@@ @@@@ @@@ @@@ @@@'; # @@-@@@@-@@@@
					$bank_account_f1=onlyInList(array('o'=>$GLOBALS['onlyDig'],'s'=>$bank_account)); 
					$fm=stringMaskBasic(array('m'=>$m,'r'=>'@','s'=>$bank_account_f1));
					
					// print_r($fm); die('|'.$bank_account_f1.'|'.$bank_account.'|');
					$bank_account_f2=$fm['f'];
					*/
					$oacc=$bank_account; 
				}
				break;
			case 3:
				if (empty($domestic_remittance)) $page['error_msg'] .= "<br>Transfer details not specified!";
				else {
					$oacc=$domestic_remittance; 
				}
				break;
			case 4:
				if (empty($payment_system)) $page['error_msg'] .= "<br>Payment system Missing!";
				else {
					$oacc=$payment_system; 
				}
				break;
			default:
				break;
		}
		
		if (isset($oacc))	{		# Фиксиуем оригинальное и отформатированное название счета
			$rq['l']['oacc'] = $oacc;
			$rq['l']['facc']=bankFormat(array('r'=>0,'s'=>$oacc));
		}
	}
	return $rq;	
}

function step3($rq) {
	
	$rq=ss_chek_bank($rq);				# Банковские реквизиты
	
	$enrc = $_POST['onrc'];				# Номер паспорта
	
	$ss_mp=['n'=>'origUsrMMPersonalID','v'=>$enrc,'ee'=>"<br>Missing passport information!"];
	$cv=ss_chek_string($ss_mp); 
	if ($cv) {
		$rq['u']['onrc'] = $cv;			# У нас в итоговом запросе есть id паспорта > форматируем ее в транслит
		
		$bw=getMmToEngFormatArray();	# Подготавливаем рабочий массив форматирования

		$fd=FormatMmPersId(['bw'=>$bw,'mmid'=>$enrc]);	# Форматируем  Паспотр, если скрипт сомневается тогда он верет Оригинал
		$rq['u']['fnrc'] = $fd['fin'];
	}

	return $rq;			
}


function step2($rq) {
	Global $page,$step,$user;
	$Guarantor1Phone = $_POST['Guarantor1Phone'];
	$Guarantor1Name = $_POST['Guarantor1Name'];
	$Guarantor2Phone = $_POST['Guarantor2Phone'];
	$Guarantor2Name = $_POST['Guarantor2Name'];
	
    # Первый поручитель Имя
	$ss_mp=['n'=>'Guarantor1Name','v'=>$Guarantor1Name,
	'ee'=>"<br>Not Specified name of the person # 1"];
	$g1n=ss_chek_string($ss_mp); 

    # Второй поручитель Имя
	$ss_mp=['n'=>'Guarantor2Name','v'=>$Guarantor2Name,
	'ee'=>"<br>Not Specified name of the person # 2"];
	$g2n=ss_chek_string($ss_mp); 
	
	# Первый поручитель Телефон
	$ss_mp=['n'=>'Guarantor1Phone','v'=>$Guarantor1Phone,'ds'=>21,
	'ed'=>"<br>You have already used before this phone of the person # 1",
	'ee'=>"<br>Not Specified phone of the person # 1 Warranty",
	'er'=>"<br>Error in the room phone warranty person # 1! You must enter 7-11 digits without spaces."];
	$g1p=ss_chek_phone($ss_mp); 
	
	# Второй поручитель Телефон
 	$ss_mp=['n'=>'Guarantor2Phone','v'=>$Guarantor2Phone,'ds'=>22,
	'ed'=>"<br>You have already used before this phone of the person # 2",
	'ee'=>"<br>Not Specified phone of the person # 2 Warranty",
	'er'=>"<br>Error in the room phone warranty person # 2! You must enter 7-11 digits without spaces."];
	$g2p=ss_chek_phone($ss_mp);  

	if ($g1n && $g1p)  $rq['c'][] = ['cr'=>1,'ct'=>1,'cname'=>$g1n,'cval'=>$g1p];
	if ($g2n && $g2p)  $rq['c'][] = ['cr'=>2,'ct'=>1,'cname'=>$g2n,'cval'=>$g2p];
	 
	#print_r($rq); die();
	 
	return $rq;
}

function step1($rq) {
	Global $page,$step,$user;
	$social = intval($_POST['social']);
	$cname = $_POST['cname'];
	$cphone = $_POST['CompanyPhone'];  
	$salary = $_POST['SalaryAmount'];
	$CoworkerPhone = $_POST['CoworkerPhone'];
	$CoworkerName = $_POST['CoworkerName'];
 
    # Соц статус (НЕОБЯЗАТЕЛЕН)
	if (isset($libs['users.social'][$social])) $rq['u']['social'] = $social;
	
    # Имя Компании
	$ss_mp=['n'=>'cname','v'=>$cname,'ee'=>"<br>Name of the Company not specified!"];	
	$cv=ss_chek_string($ss_mp); if ($cv) $rq['u']['cname'] = $cv;
	
	# Телефон Компании
	$ss_mp=[
	'n'=>'cphone','v'=>$cphone,'ds'=>11,
	'ed'=>"<br>You have already used before this Phone of the Company",
	'ee'=>"<br>Phone of the Company not specified!",
	'er'=>"<br>Error in operating room phone! You must enter 7-11 digits without spaces."	
	];
	$cv=ss_chek_phone($ss_mp); if ($cv) $rq['u']['cphone'] = $cv;
		
    # Доход
	$ss_mp=['n'=>'salary','v'=>$salary,'ee'=>"<br>Not Set salary!"];
	$cv=ss_chek_string($ss_mp); if ($cv && $cv>0) $rq['u']['salary'] = $cv;
	
	# Телефон коллеги (НЕОБЯЗАТЕЛЕН)
	$ss_mp=[
	'n'=>'CoworkerPhone','v'=>$CoworkerPhone,'ds'=>12,'nones'=>1,
	'ed'=>"<br>You have already used before this coworker phone.",
	'er'=>"<br>Error in coworker phone number! You must enter 7-11 digits without spaces."	
	];
	$cwp=ss_chek_phone($ss_mp); 
	

    # Имя коллеги (НЕОБЯЗАТЕЛЕН) 
	$ss_mp=['n'=>'CoworkerName','v'=>$CoworkerName,'nones'=>1,];
	$cwn=ss_chek_string($ss_mp); 
	
	if ($cwp && $cwn)  $rq['c'][] = ['cr'=>5,'ct'=>1,'cname'=>$cwn,'cval'=>$cwp]; 
	
	return $rq;
}

function step0($rq){
	Global $page,$libs;
	//$Name = $_POST['Name'];
	$email = trim($_POST['email']);
	$day = intval($_POST['day']);
	$month = intval($_POST['month']);
	$year = intval($_POST['year']);
	$Gender = intval($_POST['Gender']);
	$SecondPhone = $_POST['SecondPhone'];
	$City = $_POST['City'];
	//$township = $_POST['township'];

    /*
	# Почта  (НЕОБЯЗАТЕЛЕН)
    $email = str_replace(' ', '', $email);  
	$ss_mp=[
	'n'=>'email','v'=>$email,
	'nones'=>1,'reg'=>"|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i",
	'er'=>"<br>Missing or incorrectly specified E-mail"
	];
	$rq=ss_chek_string($ss_mp);
	*/
	
	# Если почта введена
	if (strlen($email)>4) $rq['c'][] = ['cr'=>0,'ct'=>2,'cname'=>'Email','cval'=>$email]; 
	
	// Валидация ДР
	$BirthDate = sprintf("%02d.%02d.%04d", $day, $month, $year);
	if (empty($BirthDate)) $page['error_msg'] .= "<br>BirthDay not specified!";
	else {
		$BirthDate_ = explode('.', $BirthDate);
		$BirthDate = sprintf("%04d-%02d-%02d", $BirthDate_[2], $BirthDate_[1], $BirthDate_[0]);
		if ($BirthDate_[2]=='0000' || $BirthDate_[1]=='00' || $BirthDate_[0]=='00') $BirthDate='2000-01-01';
		$rq['u']['birthdate'] = $BirthDate;
	}
	
    # Пол 
	if (isset($libs['users.gender'][$Gender])) $rq['u']['gender'] = $Gender;
	
    # Второй телефон  (НЕОБЯЗАТЕЛЕН)
	$ss_mp=[
	'n'=>'SecondPhone','v'=>$SecondPhone,
	'ds'=>0,'nones'=>1,
	'ed'=>"<br>You have already wrote before same phone number.<br>Please provide another phone number (if you have) or leave this field empty.",
	'er'=>"<br>An error in the phone number! You must enter 7-11 digits without spaces."
	];
	$cv=ss_chek_phone($ss_mp); 
	if ($cv) $rq['c'][] = ['cr'=>0,'ct'=>1,'cname'=>'Sec phone','cval'=>$cv]; 

    # Город
	$ss_mp=['n'=>'City','v'=>$City,'ee'=>"<br>City not specified!"];
	$cv=ss_chek_string($ss_mp); if ($cv) $rq['u']['city'] = $cv;
	
	return $rq;
}


function addTwoContacts($rq) {
	Global $page,$user;
	$Guarantor3Phone = $_POST['Guarantor3Phone'];
	$Guarantor3Name = $_POST['Guarantor3Name'];
	$Guarantor4Phone = $_POST['Guarantor4Phone'];
	$Guarantor4Name = $_POST['Guarantor4Name'];

	$ss_mp=['n'=>'Guarantor3Name','v'=>$Guarantor3Name,'ee'=>"<br>Not Specified name of the person # 1"];
	$g3n=ss_chek_string($ss_mp); 		# Первый поручитель Имя
	
	$ss_mp=['n'=>'Guarantor4Name','v'=>$Guarantor4Name,'ee'=>"<br>Not Specified name of the person # 2"];
	$g4n=ss_chek_string($ss_mp); 		# Второй поручитель Имя

	$ss_mp=[
	'n'=>'Guarantor3Phone','v'=>$Guarantor3Phone,'ds'=>31,
	'ed'=>"<br>You have already used before this phone of the person # 1",
	'ee'=>"<br>Not Specified phone of the person # 1 Warranty",
	'er'=>"<br>Error in the phone person # 1! You must enter 7-11 digits without spaces."	
	];
	$g3p=ss_chek_phone($ss_mp); 		# Первый поручитель Телефон

 	$ss_mp=[
	'n'=>'Guarantor4Phone','v'=>$Guarantor4Phone,'ds'=>32,	
	'ed'=>"<br>You have already used before this phone of the person # 2",
	'ee'=>"<br>Not Specified phone of the person # 2 Warranty",
	'er'=>"<br>Error in the phone person # 2! You must enter 7-11 digits without spaces."	
	];
	$g4p=ss_chek_phone($ss_mp); 		# Второй поручитель Телефон

	if ($g3n && $g3p)  $rq['c'][] = ['cr'=>3,'ct'=>1,'cname'=>$g3n,'cval'=>$g3p];
	if ($g4n && $g4p)  $rq['c'][] = ['cr'=>4,'ct'=>1,'cname'=>$g4n,'cval'=>$g4p];	
	  
	return $rq;
}

function step4($rq) {

	Global $page,$step,$user;
	
	if (isset($_FILES) && count($_FILES)>0) {

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
	
	return $rq;			
}

#function ah_getApp(){ return db_row("SELECT * FROM `application` WHERE `id` = '{$_SESSION['application_id']}'"); }	
 
#function ah_initPaymentWallet(){ $appdata=ah_getApp(); return $appdata['PaymentWallet']; }	
	

