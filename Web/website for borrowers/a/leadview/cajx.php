<?php

#if (!isset($libs)) require_once($dr.'/tool/sas/constants.php');					# Подключаем константы

if (isset($_GET['cajx'])) {
	
	$outm=[];  $cid=$user['id'];  $cajx=$_GET['cajx']; 	#if (isset($_GET['mode'])) $mode=$_GET['mode']; 

	if ($cajx=='sleadwork') {
		# Попробуем взять лид в работу , если он свободен -> открываем его обработку, если занят уведомляем что он занят

		$wl = db_array("select id,uid,cid,fil,st,a_rst from leads where id=$lid");  $wld=$wl[0];
		
		$banfil	=[]; 			# Блокированные для обработки филиалы
		$banst	=[4,5,6]; 		# Блокированные для обработки конечные статусы
		$cblk	='';
		#  алерт если у нас запрешена обработка лидов этой стране
		if (in_array($wld['fil'],$banfil)) $cblk.="We are not working {$countryid[$wld['fil']]['t']} leads. ";	

		#  алерт если у нас лид в завершающем статусе.
		if (in_array($wld['st'],$banst)) {
			$outm["aah"]=3;
			$cblk.="This lead is parked. Please ask the superviser to change this lead status. ";
		}
		
		#  алерт если у нас лид в статусе ждет перезвона.
		if ($wld['a_rst']==1) {
			#$outm["aah"]=1;
			$rc = db_array("select recall from users_calls where dt=1 and did=$lid and now()<recall limit 1");  
			if (count($rc)>0) $cblk.="This customer ask to call him later after {$rc[0]['recall']}.";
		}	
		
		if ($wld['cid']!=0 && $wld['cid']!=$cid) {	# Если у нас лид заблокирован кем-то 
			$ak = db_array("select max(ts) mts,TIMEDIFF(now(),ts) sm from leads_wtime where lid=$lid and ts>date_add(now(), interval - 30 MINUTE)");  
			if (count($ak)==0) {
				db_request("delete from leads_wtime where lid=$lid");	# Сбрасываем все старты обработок по данному лиду
				db_request("update leads SET cid=$cid where id=$lid");  # Захватываю этот лид под себя
			} else {
				# Снимаем имя оператора
				$un = db_array("select login l from users where id={$wld['cid']}"); 
				$cblk.="Leads #$lid is already being processed by {$un[0]['l']} {$ak[0]['sm']} ago. ";	# Лид взят в работу другим оператором менее 30 мин назад
			}
		}

		if ($cblk=='') {	# Берем лид в работу если у нас нет проблем
			db_request("update leads SET cid=0 where cid=$cid and id!=$lid");  				# Сбросил все свои старые кроме нового
			db_request("update leads set cid=$cid where id=$lid and cid in (0,$cid)");   	# Беру новый при условии что в нем никого нет или я
			$outm['eu']=$hn.'/a/workleads/?id='.$lid;  										# Релоад страницы после ответа
			$outm["ef"]="showalert";
			$outm["aah"]=3; $asts=0;
			$cblk="Open worklead process. Waiting 2 sec... ";
		} else {	# Если у нас есть проблем > делаем красный алерт
			$asts=3;
		}
		
		$outm["ef"]="showalert"; $outm["asts"]=$asts;  $outm["atxt"]=$cblk;
	}
	
	mysql_close(); die(json_encode($outm));
}
