<?php

$aio_el=''; 				if (isset($_POST['aio_el']) && isset($dpel[$_POST['aio_el']])) 	$aio_el=$_POST['aio_el'];
$aios=''; 					if (isset($_POST['aios']) && strlen($_POST['aios'])>0) 			$aios=$_POST['aios'];

$aioml=[
	'Self Contact'		=>['sql'=>"SELECT uc.uid id FROM users_contacts uc,users u WHERE uc.cr=0 and uc.cval like '{(aio)}' and u.id=uc.uid and u.role=''"],
	'All+App Contacts'	=>['sql'=>"SELECT tf.uid id FROM users u,
							(
							   select * FROM 
							   (
								   SELECT uc.uid  FROM users_contacts uc WHERE uc.cval like '{(aio)}'  
								   UNION
								   SELECT m.user_id uid FROM users_map m WHERE m.post like '{(aio_encode)}' 
							   ) t group by 1
							) tf 
							WHERE tf.uid=u.id and u.role=''"],
	'Name Login NRC'	=>['wn'=>'uallseek','ws'=>6,'wv'=>'%'.$aios.'%',],
	'Bank Account'		=>['sql'=>"SELECT uid id FROM users_accounts ua WHERE ua.dbacc like '{(aio_like)}'"],
	#'Nrc,Name'			=>"SELECT uid FROM users u WHERE u.role=''",
];

# $aiom='All+App Contacts'  По умолчанию выбранный режим
$aiom='All+App Contacts'; 	if (isset($_POST['aiom']) && isset($aioml[$_POST['aiom']])) 	$aiom=$_POST['aiom'];
	
if (isset($_GET['cajx'])) {
	#print_r($_POST); die("[$aiom]");
	$outm=[];  $cajx=$_GET['cajx']; 	

	if ($cajx=='seekaio' && $aios!='' && isset($_POST['aiom']) && isset($aioml[$_POST['aiom']])) {
		# Выполняем предварительный запрос который вернет нам список ID , который мы подсунем основной таблице как параметр

		$outm=[
			"ef"=>"addAioPar",
			"wf"=>'search '.htmlentities($aios).' in '.$aiom,
			"aio_el"=>$aio_el,
		];
		
		$fd=$aioml[$aiom];

		if (isset($fd['wn'])) {			# Работаем уже подготовленный массив с параметром
			$outm = array_merge ($fd, $outm);
		} else {
			if (isset($fd['sql'])) {	# Работаем список ID через запрос
				
				$aios_sql=mysql_real_escape_string($aios); 
				
				# Энкодируем строчку
				$aio_en1=json_encode($aios);  $aio_en2=substr($aio_en1, 1, strlen($aio_en1)-2); 
				$aio_encode=mysql_real_escape_string(addslashes($aio_en2));
				
				# Убираем пробелы и Берем только цифры и ищем только их, если цифр менее 5 то берем ориг строку
				# Перед поиском разбиваем строку на символы и вставляем между ними % для большего покрытия
				$aios_dig=onlyInList(array('o'=>'0123456789%','s'=>$aios));
				$aio_like=$aios_sql; if (strlen($aios_dig)>5) $aio_like=$aios_dig;

				$o=['aio'=>$aios_sql,'aio_encode'=>$aio_encode,'aio_like'=>$aio_like];
				
				# Если во входящем запросе уже есть % то мы тогда не оборачиваем, если нет -> оборачиваем
				$pch=str_replace('%','',$aios); if (strlen($pch)==strlen($aios)) foreach ($o as $k=>$v) $o[$k]='%'.$v.'%'; #$aios='%'.$aios.'%';
				foreach ($o as $k=>$v) $o[$k]=repStrReplace('%%','%',$o[$k]); 

				$sql=doTmpl(['s'=>$fd['sql'],'o'=>$o]); 
				#die("[$sql]");
				$getid = db_array($sql); 
				$idm=[]; 
				if (count($getid)>0) {
					foreach ($getid as $k=>$v) $idm[]=$v['id']; $idms=implode(',',$idm);
					$outm["wv"]=$idms; $outm["ws"]=4; $outm["wn"]='uid';	#$outm["sql"]=$sql;
				} else {
					$outm=[
						"ef"=>"showalert","aah"=>1,"asts"=>3,					# Красный алерт что ничего не найдено
						"atxt"=>"Sorry. Nothing found, try another ... ",
					];
				}					
			} else $outm=[];			# Ни список ни запрос
		}

	}
	mysql_close(); die(json_encode($outm));
}

