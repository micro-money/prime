<?php

/* ИНФОРМАЦИОННЫЕ УВЕДОМЛЕНИЯ В ВЕРХНЕЙ ЧАСТИ СТРАНИЦЫ */

if (!empty($page['success_msg'])) {$ah_m=ah_addl($page['success_msg']); e('bootstrap3/alert', ['class' => 'success', 'msg' =>$ah_m ]); }
if (!empty($page['error_msg'])) {$ah_m=ah_addl($page['error_msg']); e('bootstrap3/alert', ['class' => 'danger', 'msg' => $ah_m]); }

function ah_addl($mp){
	$os=str_replace(['<br />','<br>'],'|x|',$mp); $osmft=[]; 
	//echo '{'.$os.'}';
	$osm=explode('|x|',$os);
	if (is_array($osm)) {
		//print_r($osm);
		foreach ($osm as $k=>$v) if ($v!='') $osmft[]=$v; 
		//print_r($osmft);
		$osmt=implode('<br />',$osmft);
	} else {
		$osmt=$osm;
	}
	return $osmt;
}