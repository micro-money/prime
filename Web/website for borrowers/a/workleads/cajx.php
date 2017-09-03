<?php

if (isset($_GET['cajx'])) {
	
	$nplim=4;	# Лимит количества перезвонов когда клиент не берет телефон
	
	$outm=[];  $cajx=$_GET['cajx']; $cid=$user['id'];
	
	if (count($wl)==0) { $outm['eu']=$hn.'/a/workleads'; die2($outm); }	# У текущего оператора нет лида в работе
	
	$uid=$wl[0]['uid']; 	# id клиента
	$clid=$wl[0]['id'];		# current lid by database
	
	# След лид
	if ($cajx=='next') {
		$outm['eu']=$hn.'/a/workleads';  # Релоад страницы после ответа
		$note=mysql_real_escape_string($_POST['note']);	# Нотис от менеджера 
		$lid=intval($_POST['lid']);						# Номер лида от фронта
		$cst=intval($_POST['cst']);						# Номер статусной кнопки
		$nct=intval($_POST['nct']);						# Время через сколько надо перезвонить
			
		if ($clid!=$lid) die2($outm);	# Если лид по базе данных не совпадает с номером лида который отдал фронт
			
		$lupq=""; $quup=[];
		$nctw=['null',
		'date_add(now(), interval 30 MINUTE)',
		'date_add(now(), interval 1 HOUR)',
		'date_add(now(), interval 2 HOUR)',
		'date_add(now(), interval 3 HOUR)',
		'date_add(now(), interval 1 DAY)'];

		if ($cst==1) {	# $cst=1 => 'NO PICK UP' +  у лида не превышен лимит перезвонов -> мы ему перезваниваем на след день.
			$npup = db_array('select count(*) as kol from users_calls where did='.$lid.' and dt=1 and cst=1');	
			if (count($npup)>0 && $npup[0]['kol']<$nplim) {
				$nct=5;		# Время след перезвона +1 DAY
			} else {
				$nct=0; 	# Время след перезвона null - не перезваниваем
				$lupq.=",note=concat(note,'\\nСustomer didn`t pick up phone $nplim times.')";  # \\n
				$cst=7;		# Отказ в лиде по причине не взятия трубки макс кол-ва раз
			}
		}
		
		$a_rst=0; if ($nct>0) {	# Делаем активный перезвон 
			$lupq.=",a_rst=1";	# leads.a_rst 		
			$a_rst=1;			# users_calls.a_rst 
		}
		
		#$lcst_cst
		$lstm=[
			1=>-1,# 7 'No pick up',		
			2=>26,#'Does not want to borrow',
			3=>19,#'Need Bank Acc or NRC',	# По банкам надо отдельный статус > будем давить чтобы сходили и завели
			4=>24,#'Pipeline',
			5=>26,#'Bad guy',
			6=>-1,# 8 'Next Recall',
			7=>26,# Denied due No pick up max times
		]; 
		/*
		'leads.st'=>[		# $libs['loans.st']
			0=>'New',		# Новая заявка еще ниразу до конца не заполнялась
			1=>'Must fix', 	# Необходимо исправить 
			2=>'Сompleted',	# Полностью заполнена			
			#...
			4=>'Pipe',		# Подготовленна менеджер согласовал к сделке > безопасность , сделка , перевод денег.	
			5=>'Old Lead',	# Старый лид , данные ожидают обработки
			6=>'Denied',	# В заявке отказано
			7=>'No pick up',	# Клиент не взял трубку
			8=>'Wait recall',	# Клиент заказал перезвон
			9=>'Need Bank',	# Нужен банк или NRC
			],
		*/
		# ================= Обязательный пакет ==================  
		# Обновляем статус лиду
		$nlst='st'; if ($lstm[$cst]!=-1) $nlst=$lstm[$cst]; # Если у нас меняется статус лида по сути а не по факту недозвона
		db_request("update leads set cid=0,cst=$cst,st=$nlst{$lupq} where id=$lid and cid=$cid");
		#die("update leads set cid=0,cst=$cst,st=$nlst where id=$lid and cid=$cid");
		
		# Снимаем время начала работы над лидом (убивать его не будем оно убьется автоматически при взятии след работы)
		$tsm = db_array("select id,DATE_FORMAT(ts,'%Y.%m.%d %H:%i:%s') as fts from leads_wtime where lid=$lid and cid=$cid limit 1"); # order by ts desc (убрал дабы зазря не грузить запрос)
		# Вносим результат звонка тут же зафиксируем время начала работы и время ее окончания
		$ws='null'; if (count($tsm)>0) $ws="'".$tsm[0]['fts']."'";
		# Время работы над лидом - это время звонка -> пишем его в этот звонок
		db_request("insert into users_calls (uid,dt,did,cst,note,recall,a_rst,cid,ws,dv) VALUES ($uid,1,$lid,$cst,'$note',{$nctw[$nct]},$a_rst,$cid,$ws,now())");
		
		if ($cst==4) {	# ================= Если статус pipeline =================
			# Создаем сделку со статусом pipeline
			db_request("insert into loans (fil,uid,lid,st,racc,UsrAmount,UsrRAmount,UsrRTerm,UsrTerm,UsrPercentPerDay,cid,dv) 
					                select fil,uid,id, 1, 0,   0,        ramount,   rdays,   0,      ppd,$cid,now() FROM leads WHERE id=$lid");
		}
		
		if ($cst==5) 			$quup[]='st=1';		# Если статус badgay 
		if (intval($nlst)>24) 	$quup[]='a_lid=0';	# Если лид паркуется сбросить users.a_lid=0
		if (count($quup)>0) 	db_request("update users set ".implode(',',$quup)." where id=$uid");
		
	}
	
	# Если у нас был заход в конкретный лид
	if (isset($_GET['id']) && intval($_GET['id'])>0) {
		if ($outm['eu']==$hn.'/a/workleads') $outm['eu']=$hn.'/a/leadview/?id='.intval($_GET['id']);  # Релоад страницы после ответа
	}	
		
	die2($outm);
}