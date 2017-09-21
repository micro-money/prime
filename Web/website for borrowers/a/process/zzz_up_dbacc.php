<?php
/*
  users_accounts.dbacc     ''
*/

$baccl = db_array("SELECT id,bacc FROM `users_accounts` WHERE dbacc='' limit $lim"); 

$up=0; $del=0;

foreach ($baccl as $k=>$v) {
	$dbacc=onlyInList(array('o'=>'0123456789','s'=>$v['bacc']));
	if (strlen($dbacc)>3) {
		#      
		$up++; db_request("update users_accounts set dbacc='$dbacc' where id={$v['id']}");
	} else {
		#      
		$del++; db_request("delete from users_accounts where id={$v['id']}");
	}
}

echo "[up=$up|del=$del]";

function onlyInList($mp){
	$o=$mp['o'];   //    
	$s=$mp['s'];   //  
	/*
	onlyInList(array('o'=>$onlyEngAll.$onlyDig,'s'=>$s1));
	   ,         ,      
	*/	
	$ms = str_split($s); $mo = str_split($o); 
	$f=''; foreach ($ms as $k=>$v) if (in_array($v,$mo)) $f.=$v;
	return $f;
}
