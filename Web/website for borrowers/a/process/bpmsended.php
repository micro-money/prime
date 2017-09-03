<?php

	$wjs=$cron_wd[0]['js']; $lim=$wjs['lim'];	# Снимаем старое время из настроек
	
	$fakt = db_array("select id,uid from leads where st<20 and crmst>0 and crmts>date_add(now(), interval -3 day) limit $lim");  #  Id='77891e49-0b7b-4cce-8dcc-61e3284a91bb'

	$cron_ar=count($fakt);
	
	if ($cron_ar>0) {	# У нас есть лиды не финализированые, которые были отправлеы в bpm более 3х дней назад. 
			
		# Еще раз запустим 
		if ($cron_ar==$lim) $cron_onemoretime=1;
				
		foreach ($fakt as $k=>$v) {
			
			db_request("update users set a_lid=0 where id={$v['uid']} and a_lid={$v['id']}");
			db_request("update leads set st=28 where id={$v['id']}");
			
		}

	} else {
		$cron_nolog=1;
	}
		
