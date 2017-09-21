<?php
// https://money.com.mm/localrun/un_refdata.php?key=extrun

/* 
    Lead  CRM  WEB       un_XXXX
un_tel
un_nrc
un_bacc
:
1.    


UPDATE `zsync_chrome` SET wd='{"ok":1}',dr=now() WHERE id=10;
delete FROM un_log;
delete FROM un_acc;
delete FROM un_nrc;
delete FROM un_tel;
delete FROM un_imei;

#     UsrMMPersonalID
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
	
	# -  
	$cron_ar+=$stat['total'];
	
	#           >   
	if ($stat['total']==$lim) $cron_onemoretime=1;
	
	//echo '<br>tab:'.$tab.' ok:'.$stat['ok'].'|err:'.$stat['er'].'|lt:'.$stat['lt'];
	$wjs=$stat['wd']; unset($stat['wd']);
	print_r($stat); 
	
	#       
	$cron_wjs[$tab]=$stat;	
}
	#     
	$cron_sjs=$wjs;
	
	
	
function addNewAnalizData($mp) {
	Global $nr,$sbd;
	$tab=$mp['tab']; $lim=$mp['lim']; $wd=$mp['wd']; 
	$onlyDig='0123456789';
	
	/*
	$ss = db_array("SELECT id,descr,sw,wd,send,resp,dr FROM `zsync_chrome` WHERE id=10 and act=1"); $ss=$ss[0];
	$wd=json_decode($ss['wd'], true);			//    json

	
	#$tab='zsync_Lead';	#   
	#     ->      2000  
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
	$wd=json_decode($ss['wd'], true);			//    json
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
	
	#     
	$nlist = db_array($qlist[$tab]);  #  Id='77891e49-0b7b-4cce-8dcc-61e3284a91bb'
	if ($lim<11) echo $nr.$qlist[$tab];
	$stat=['tab'=>$tab,'ok'=>0,'err'=>0,'total'=>count($nlist),'wd'=>$wd];
	
	if (count($nlist)>0) {
		#    
		foreach ($nlist as $key=>$vol) {
			$eId=$vol['eId']; $t=$vol['t']; 

			#  (  ,  )
			$phonem=explode(',',$vol['phones']); $phonefm=[];
			foreach ($phonem as $k=>$v) {
				
				$tp=onlyInList(array('o'=>$onlyDig,'s'=>$v));
				#echo "[$onlyDig|$v|$tp]";
				if (strlen($tp)>5) $phonefm[]=$tp;
			}

			if (!isset($phonefm[0])) {
				$stat['err']++; 
				#print_r($vol);print_r($vol);
				#     ->         
				db_request("insert into `un_log` (tab,dt,descr,dv) VALUES ('$tab',$t,'No main phone for Id:$eId',now())");
			} else {
				$stat['ok']++;
				$mphone=$phonefm[0];
			
				#          6
				$ta=''; 
				if (isset($vol['bacc']) && strlen($vol['bacc'])>5) $ta=onlyInList(array('o'=>$onlyDig,'s'=>strtolower($vol['bacc'])));
				
				/*
				.'o'
				if (strposV2(['s'=>$ta,'i'=>'o'])!=-1) {
					$ta=str_replace('o', '0',$ta);
					#       o -   
					db_request("insert into `un_log` (tab,dt,descr,dv) VALUES ('$tab',1,'O in NRC for Id:".$vol['Id']."',now())");
				}
				*/
				
				#  Nrc         6
				$tn='';  $fnrc='';
				if (isset($vol['nrc']) && strlen($vol['nrc'])>5) {
					
					$bw=getMmToEngFormatArray();

					#  ID ,    ,     -> 
					$fd=FormatMmPersId(['bw'=>$bw,'mmid'=>$vol['nrc']]);
					$fnrc=$fd['fin'];
					
					#          
					$ms = str_split($fnrc); $mo = str_split($onlyDig); $cs=0;  
					$msr = array_reverse($ms);	#      
					foreach ($msr as $k=>$v) {
						if ($cs==0 && !in_array($v,$mo)) $cs=1;	#       ->   
						if ($cs!=1) $tn=$v.$tn;		
					}	
				}
				
				#  
				foreach ($phonefm as $k=>$v) {
					db_request('insert ignore into `un_tel` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$v.'","'.$eId.'")');
				}
				
				#  
				if (strlen($ta)>5) db_request('insert ignore into `un_acc` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$ta.'","'.$eId.'")');
				
				#  
				if (strlen($tn)>5) db_request('insert ignore into `un_nrc` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$tn.'","'.$eId.'")');	
				#  
				if (strlen($fnrc)>5) db_request('insert ignore into `un_nrcf` (t,phone,val,eId) VALUES ('.$t.',"'.$mphone.'","'.$fnrc.'","'.$eId.'")');	
							
				# imei 
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
		
		#            ,       
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