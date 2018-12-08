<?php
// https://money.com.mm/localrun/un_refdata.php?key=extrun

/* 
Скрипт проходит по таблице Lead от CRM и WEB и производит пополнение таблиц с перфиксом un_XXXX
un_tel
un_nrc
un_bacc
Алгоритм:
1. Снимаем данные по статусам


UPDATE `zsync_chrome` SET wd='{"ok":1}',dr=now() WHERE id=10;
delete FROM un_log;
delete FROM un_acc;
delete FROM un_nrc;
delete FROM un_tel;
delete FROM un_imei;

# Добавление проблемных счетов  UsrMMPersonalID
drop table if exists temp_userdata;
CREATE TABLE temp_userdata (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  t int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 free, 1 ok',
  user_id int(11) UNSIGNED NOT NULL COMMENT 'user_id',
  phones varchar(50) DEFAULT NULL,
  bacc varchar(30) DEFAULT NULL COMMENT 'bank account',
  nrc varchar(20) DEFAULT NULL COMMENT 'nrc',
  imei varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = MYISAM
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'temp table user data';

insert into temp_userdata (user_id,phones,bacc,nrc,imei)
SELECT u.id,concat(u.login,',',u.Phone,',',u.SecondPhone) as phones,a.origPaymentWallet as bacc,u.origUsrMMPersonalID as nrc,(select vol from users_mapdata where dt=1 and user_id=u.id) as imei 
FROM users as u left join application as a on (u.id=a.user_id and a.origPaymentWallet is not null);

delete from temp_userdata where phones is null and bacc is null and nrc is null and imei is null;
delete from temp_userdata where phones is null and bacc like '%lost_data%' and nrc is null and imei is null;
delete from temp_userdata where phones is null ;
*/

require_once($dr.'/tool/uni_func.php');
require_once($dr.'/tool/report_func.php');

$tablist=['leads','users','zsync_Lead']; # ,
$next_run=0; $cron_ar=0; $cron_wjs=[];
$wjs=$cron_wd[0]['js']; $lim=$wjs['lim']; 

foreach ($tablist as $k=>$tab) {
	
	$stat=addNewAnalizData(['tab'=>$tab,'lim'=>$lim,'wd'=>$wjs]);
	
	# Кол-во аффектных строк
	$cron_ar+=$stat['total'];
	
	# Если у нас обработано столько же сколько у нас лимит > Запускаем еще раз
	if ($stat['total']==$lim) $cron_onemoretime=1;
	
	//echo '<br>tab:'.$tab.' ok:'.$stat['ok'].'|err:'.$stat['er'].'|lt:'.$stat['lt'];
	$wjs=$stat['wd']; unset($stat['wd']);
	print_r($stat); 
	
	# ДОбавляем статистику на выход по каждому проходу
	$cron_wjs[$tab]=$stat;	
}
	# Передаем рабочие установки для сохранения
	$cron_sjs=$wjs;
	
	
	
function addNewAnalizData($mp) {
	Global $nr,$sbd;
	$tab=$mp['tab']; $lim=$mp['lim']; $wd=$mp['wd']; 
	$onlyDig='0123456789';
	
	/*
	$ss = db_array("SELECT id,descr,sw,wd,send,resp,dr FROM `zsync_chrome` WHERE id=10 and act=1"); $ss=$ss[0];
	$wd=json_decode($ss['wd'], true);			// рабочие реквизиты в json

	
	#$tab='zsync_Lead';	# Имя внешней таблицы
	# Устанавливаем время последней синхронизации -> Если его нет то с 2000 года все
	$lt='2000.01.01'; 
	if (isset($wd[$tab]) && isset($wd[$tab]['lt'])) {
		$lt=$wd[$tab]['lt'];
	} else {
		if (!isset($wd[$tab])) $wd[$tab]=[];
		$wd[$tab]['lt']=$lt;
	}	
	*/
	
	/*
	$jid=3; $lt='2000.01.01'; 
	$ss = db_array("SELECT id,ifnull(jsonset,'{}') wd FROM zsync_async_settings WHERE id=$jid"); $ss=$ss[0];
	$wd=json_decode($ss['wd'], true);			// рабочие реквизиты в json
	if (isset($wd[$tab]) && isset($wd[$tab]['lt'])) {
		$lt=$wd[$tab]['lt'];
	} else {
		if (!isset($wd[$tab])) $wd[$tab]=[];
		$wd[$tab]['lt']=$lt;
	}
	*/
	$lt='2000.01.01'; 
	if (isset($wd[$tab]) && isset($wd[$tab]['lt'])) {
		$lt=$wd[$tab]['lt'];
	} else {
		if (!isset($wd[$tab])) $wd[$tab]=[];
		$wd[$tab]['lt']=$lt;
	}	
	
	$qlist=[
	'zsync_Lead'=>"SELECT 
					Id,0 as t,DATE_FORMAT(CreatedOn, '%Y.%m.%d %H:%i:%s') as ct,
					UsrPaySystemAccount as bacc,
					UsrMMPersonalID as nrc,
					UsrPhones as phones,
					UsrOpportunityId as eId 
					FROM {$sbd}zsync_Lead WHERE CreatedOn>'$lt' limit $lim",
	
	'leads'=>"SELECT 
					a.id as Id,1 as t,DATE_FORMAT(a.dv, '%Y.%m.%d %H:%i:%s') as ct,
					a.oacc as bacc,
					(select convert(GROUP_CONCAT(cval SEPARATOR ','),char) FROM users_contacts where uid=a.uid and cr=0 and ct=1  ) as phones,
					a.id as eId 
					FROM leads as a WHERE a.dv>'$lt' and a.oacc!='' limit $lim",
	/*
	'leads'=>"SELECT 
					a.id as Id,1 as t,DATE_FORMAT(a.create_at, '%Y.%m.%d %H:%i:%s') as ct,
					a.origPaymentWallet as bacc,
					concat(u.login,ifnull(concat(',',u.Phone),''),ifnull(concat(',',u.SecondPhone),'')) as phones,
					a.id as eId 
					FROM mm.application as a,mm.users as u WHERE u.id=a.user_id and a.create_at>'$lt' and a.origPaymentWallet IS NOT NULL HAVING phones IS NOT NULL limit $lim",
	*/
	# group_concat(u.login,ifnull(concat(',',u.Phone),''),ifnull(concat(',',u.SecondPhone),'')) as phones,
	# concat(u.login,ifnull(concat(',',u.Phone),''),ifnull(concat(',',u.SecondPhone),'')) as phones,
	'users'=>"SELECT 
					u.id as Id,2 as t,DATE_FORMAT(u.dv, '%Y.%m.%d %H:%i:%s') as ct,
					u.onrc as nrc,			
					(select convert(GROUP_CONCAT(cval SEPARATOR ','),char) FROM users_contacts where uid=u.id and cr=0 and ct=1) as phones,
					(select vol FROM users_mapdata where user_id=u.id and dt=1 limit 1) as imei,
					u.id as eId
					FROM users u WHERE u.dv>'$lt' limit $lim"
	
	];
	
	#Снимаем все новые данные для обработки
	$nlist = db_array($qlist[$tab]);  #  Id='77891e49-0b7b-4cce-8dcc-61e3284a91bb'
	if ($lim<11) echo $nr.$qlist[$tab];
	$stat=['tab'=>$tab,'ok'=>0,'err'=>0,'total'=>count($nlist),'wd'=>$wd];
	
	if (count($nlist)>0) {
		# Перебираем все новые строчки
		foreach ($nlist as $key=>$vol) {
			$eId=$vol['eId']; $t=$vol['t']; 

			#Форматируем телефоны (их максимум два, реже три)
			$phonem=explode(',',$vol['phones']); $phonefm=[];
			foreach ($phonem as $k=>$v) {
				
				$tp=onlyInList(array('o'=>$onlyDig,'s'=>$v));
				#echo "[$onlyDig|$v|$tp]";
				if (strlen($tp)>5) $phonefm[]=$tp;
			}

			if (!isset($phonefm[0])) {
				$stat['err']++; 
				#print_r($vol);print_r($vol);
				# Если нет главного номера -> это критическая ошибка ее надо пофиксить для детального анализа
				db_request("insert into `un_log` (tab,dt,descr,dv) VALUES ('$tab',$t,'No main phone for Id:$eId',now())");
			} else {
				$stat['ok']++;
				$mphone=$phonefm[0];
			
				#Банковский счет под формат только цифры если их не менее 6
				$ta=''; 
				if (isset($vol['bacc']) && strlen($vol['bacc'])>5) $ta=onlyInList(array('o'=>$onlyDig,'s'=>strtolower($vol['bacc'])));
				
				/*
				.'o'
				if (strposV2(['s'=>$ta,'i'=>'o'])!=-1) {
					$ta=str_replace('o', '0',$ta);
					# Если У нас в номере есть o - соседствующая с цифрами
					db_request("insert into `un_log` (tab,dt,descr,dv) VALUES ('$tab',1,'O in NRC for Id:".$vol['Id']."',now())");
				}
				*/
				
				# Пасспорт Nrc только цифры с права если их не менее 6
				$tn='';  $fnrc='';
				if (isset($vol['nrc']) && strlen($vol['nrc'])>5) {
					
					$bw=getMmToEngFormatArray();

					# Форматируем ID , если да то ФОРМАТ, если скрипт сомневается тогда -> Оригинал
					$fd=FormatMmPersId(['bw'=>$bw,'mmid'=>$vol['nrc']]);
					$fnrc=$fd['fin'];
					
					# Тут нам надо найти номер входа первой цифры после нецифры
					$ms = str_split($fnrc); $mo = str_split($onlyDig); $cs=0;  
					$msr = array_reverse($ms);	# Разворачиваем массив и перебираем с конца
					foreach ($msr as $k=>$v) {
						if ($cs==0 && !in_array($v,$mo)) $cs=1;	# Мы уперлись в первую не цифру -> все остальное пропускаем
						if ($cs!=1) $tn=$v.$tn;		
					}	
				}
				
				#Фиксируем Телефоны 
				foreach ($phonefm as $k=>$v) {
					db_request('insert ignore into `un_tel` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$v.'","'.$eId.'")');
				}
				
				#Фиксируем Счет 
				if (strlen($ta)>5) db_request('insert ignore into `un_acc` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$ta.'","'.$eId.'")');
				
				#Фиксируем Паспорт цифры
				if (strlen($tn)>5) db_request('insert ignore into `un_nrc` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$tn.'","'.$eId.'")');	
				#Фиксируем Паспорт формат
				if (strlen($fnrc)>5) db_request('insert ignore into `un_nrcf` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$fnrc.'","'.$eId.'")');	
							
				#Фиксируем imei 
				if (isset($vol['imei'])) db_request('insert ignore into `un_imei` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$vol['imei'].'","'.$eId.'")');	
			
				if ($lim<11) {
					#print_r($vol);
					$fstr=$key.' | '.$mphone.'('.implode(',',$phonefm).') ';
					if (isset($vol['nrc']))  $fstr.='| ('.$tn.')('.$fnrc.')('.$vol['nrc'].')';
					if (isset($vol['bacc']))  $fstr.='| ('.$ta.')('.$vol['bacc'].')';

					echo $nr.$fstr;
				}
			}	
		}
		
		# После того как перебрали фиксим дату на которой закончили в настройках , чтобы в след раз продолжить с нее
		$lt=$nlist[count($nlist)-1]['ct'];
		
		$wd[$tab]['lt']=$lt; $stat['lt']=$lt; $stat['wd']=$wd;
		
		/*
		$wds=mysql_real_escape_string(json_encode ($wd));
		$sq="UPDATE zsync_async_settings SET jsonset='$wds' WHERE id=$jid";
		db_request($sq);
		*/
	}
	return $stat;
}
	


?>