<?php 
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');
$page['title'] = 'Leads PriWork';  $page['desc'] = 'Leads primary work';

/*
Алгоритм:
	Надо взять в работу толко ближайший подходяший и свободный лид а перед этим сбросить старую работу если она есть
	Какой запрос на лиды . У каждой очереди он будет свой.
	Открываться будет только один конкретный лид.
	Открываться будет карточка клиента где на первом развороте будет этот лид единственный

	# Запрос на формирование первичной очереди по обработке лидов .
	# Тут только те лиды которые по анкетным контроллерам заполнены до конца
	# (сначала самые поздние потом ранние)
	# Первой волной идут повторники 
	# Второй волной идут новички
	
	select t.*,wt.wtime from (
	# 1) Работаем с теми у кого st=Сompleted  и те у кого есть закрытые займы и нет долгов   [ЗАПОЛНЕНЫЕ]
	select id,1 p,takework,dv from leads where st=2 and a_doc=2 and a_cd=1 and a_kd=2 
	union
	# 2) Работаем с теми у кого st=Сompleted  и те у кого нет займов
	select id,2,takework,dv from leads where st=2 and a_doc=2 and a_kd=1                        
	) t left join leads_wtime wt on (wt.id=t.id ) order by p ASC,dv DESC
*/
	$cfil=-1; if ($curr_fil>0) $cfil=$curr_fil;
	/* 
	Новый алгоритм:
	Первым делом проверяем есть у нас наш лид и актуален ли он до сих пор.
	Если есть > берем его в работу.
	Если нет > выбираем себе новый по порядку.
	*/
	$cid=$user['id'];
	
	$fqw="fil=$cfil and cid=$cid"; if (isset($_GET['id']) && intval($_GET['id'])>0) $fqw="cid=$cid"; 
	
	$wl = db_array("select id,uid,cid from leads where $fqw"); 
	
	require_once('cajx.php');		# Кастмная аякс обработка текушего лида
	
	if (count($wl)==0) {	# 0) Выбираем из тех кому назначенное перезвонить уже наступило
		$wl=[]; # пока пусто
		if (isset($sas)) unset($sas);	# Сбрасываем аякс если оно было т.к. лид уже не за нами.
	} 
	/*
	a_od=1 (1 нет не оплаченных сделок, 2 есть не оплаченных сделок)
	a_kd=2 (2 есть закрытые ок сделки, 1 нет закрытых ок сделок)
	a_doc=1 нету документов
	a_doc=4 тока банк
	a_doc=3 тока nrc
	a_doc=2 обе доки есть ок
	*/

	if (count($wl)==0) {	# 1) st=2 + повторник
		$wl = db_array("select id,uid,cid from leads where fil=$cfil and st=2 and a_cd>0 and cid=0 and a_rst!=1 ORDER BY dv DESC LIMIT 1"); 		
	}
	if (count($wl)==0) {	# 2) st=2 + новичек 
		$wl = db_array("select id,uid,cid from leads where fil=$cfil and st=2 and a_cd=0 and cid=0 and a_rst!=1 ORDER BY dv DESC LIMIT 1"); 		
	}
	
	/*
	if (count($wl)==0) {	# 3) st=2 + новичек + доки или нет или не все
		$wl = db_array("select id,uid,cid from leads where st=2 and a_doc!=2 and a_kd=1 and cid=0 ORDER BY dv DESC LIMIT 1"); 		
	}
	if (count($wl)==0) {	# 4) st=1 + анкету надо поправить и нет просрочек
		#$wl = db_array("select id,uid,cid from leads where st=1 and a_od=1 and cid=0 ORDER BY dv DESC LIMIT 1"); 		
	}	
	if (count($wl)==0) {	# 5) st=0 + анкета недозаполненая и нет просрочек
		#$wl = db_array("select id,uid,cid from leads where st=0 and a_od=1 and cid=0 ORDER BY dv DESC LIMIT 1"); 		
	}
	*/
	# todo: можно включить ускорение и обходить те запросы по которым последний раз показывал пусто. Например повторники кончились и можно баться за новичков слдующие 30 мин. И раз в 30 мин обновлять состояние по повторникам.
	
/*
Вариант запроса для всей очереди сразу
select id,uid,cid,1 t,dv from leads where st=2 and a_od=1 and a_kd=2 and cid=0 
union
select id,uid,cid,2 t,dv from leads where st=2 and a_doc=2 and a_kd=1 and cid=0 
union
select id,uid,cid,3 t,dv from leads where st=2 and a_doc!=2 and a_kd=1 and cid=0 
ORDER BY t,dv DESC;
*/
	
	# Надо определиться с номером лида к разбору и сформировать очередь

	if (count($wl)==0) {
		$fl=[];
	} else {
		$fl=$wl;
		if ($wl[0]['cid']==0) {	# Забираем себе следующий лид если он свободный
			$lid=$wl[0]['id']; #id следующего лида > Пытаемся забронировать его под себя 
			db_request("update leads set cid=$cid where id=$lid and cid=0");	
			# Теперь проверяем за нами ли он и если да > работаем  
			$fl = db_array("select id,uid,cid from leads where cid=$cid");
			if (count($fl)>0) {		# Фиксируем время старта работы с этим лидом
				db_request("DELETE FROM leads_wtime where cid=$cid");	# Убиваем время начала работы всем лидам перед переносом его в users_calls
				db_request("update leads SET cid=0 where cid=$cid and id!=$lid");	# Убиваем лиды которые мы брали кроме нового (под филиалы совместимость чтобы более одного в работу не брать)
				db_request("insert into leads_wtime (cid,lid,ts) VALUES ($cid,$lid,now())");	
			}
		}
	}
	
	if (count($fl)>0) {
		$sas_sqlm["user_id"]=$fl[0]['uid']; 
		$lid=$fl[0]['id']; 
		require_once($dr.'/a/set_lead.php');
		/*
		Расширяем возможности карточки клиента для обработки лида.
		Что делаем:
		Мы сохраняем все старые возможности карточки клиента и добавляем новые.
		Карточка клиента будет видеть если пришел номер разбираемого лида > Она модернизируется под обработку 
		этого лида менеджером call центра.
		*/

		$page['js'][] = $hn.$selfc.'/m.js?ver='.$jsver;						# Подключаем персональный js	
		
		require_once($dr.'/a/set_userview.php');							# Настроки карточки клиента
		
		# Доработка карточки клиента
		# $dpel['pi']['ci']='$sas_sqlm["ci"]=$data;';
		
		require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
		if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
		require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов
	
		$cuname=$sas_sqlm['m']['uname'];
		$MainPhone=$sas_sqlm['m']['ulogin'];
		
		# Открываем рабочий шаблон
		ob_start();
?>
	<div class="container-fluid" style="margin-top: -20px;">
		<div class="row">
			<div class="col-12">
				<?#= $topalerts ?>
				<h3>Lead #<?= $lid ?>&nbsp;&nbsp;&nbsp;<?= $cuname ?>&nbsp;&nbsp;&nbsp;<a href="sip:<?= $MainPhone ?>"><?= $MainPhone ?></a>&nbsp;&nbsp;&nbsp;</h3>
			</div>
		</div>
		<? require_once($dr.'/a/tmpl/lead_call_new_act.php'); ?>
		<? require_once($dr.'/a/tmpl/workleads.php'); ?>
	</div>
<?		require PHIX_CORE . '/render_view.php';
	} else {
		
		
		$wfl = db_array("select fil,count(*) kol from leads where st=2 and a_rst!=1 group by 1"); 
	
		$mhead='There are not full completed leads. (<a href="" >try to refresh</a>)';
		
		if (count($wfl)>0) {
			if ($curr_fil==-1) $mhead='For start Call Work. Please select any country:';
			if ($curr_fil==0)  $mhead='We are not working myanmar leads now. Please select other country:';
		}
		ob_start();
?>
	<div class="container-fluid">
		<div class="row">
			<h2><?= $mhead ?></h2>
			<? if (count($wfl)>0) { ?>
			<ul>
				<? foreach ($wfl as $k=>$v) { ?>
					<li><a href="/a/f/chdb?fil=<?= $v['fil'] ?>&h=<?= $_SERVER['REQUEST_URI'] ?>"><?= $countryid[$v['fil']]['t']." (".$v['kol'].")"; ?></a></li>
				<? } ?>
			</ul>
			<? } ?>
		</div>
	</div>
<?php require PHIX_CORE . '/render_view.php';  }