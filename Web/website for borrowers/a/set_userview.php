<?php

# ==========    (    ) ===============

if (isset($_FILES) && count($_FILES)>0 && isset($_POST['phtype']) && isset($_POST['phuid']) && intval($_POST['phuid'])>0) {
	require_once($dr.'/app_func.php');	
	$ag_flid=ah_uploadfile(['n'=>intval($_POST['phtype']),'nf'=>'photof','uid'=>intval($_POST['phuid'])]);
	$lim=100; $noecho=1; require_once($dr.'/a/process/icon.php');
	header("Location: ".$_SERVER['REQUEST_URI']); mysql_close(); exit;
}

if (isset($user_id)) {
	if (!isset($sas_sqlm)) 	$sas_sqlm=[]; 
	$sas_sqlm['user_id']=$user_id;	#        sql 
}	

include($dr.'/a/tset_userinfo.php');	#     +    
include($dr.'/a/tset_contacts.php');	#  
include($dr.'/a/tset_files.php');		#  
include($dr.'/a/tset_appdata.php');		#   
include($dr.'/a/tset_leads.php');		# 
include($dr.'/a/tset_calls.php');		#  
include($dr.'/a/tset_loans.php');		# 
include($dr.'/a/tset_cashs.php');		# 

$dpel=['pi'=>$tset_userinfo, 
	   'ci'=>$tset_contacts,
	   'ldl'=>$tset_leads,
	   'fl'=>$tset_files,
	   'ad'=>$tset_appdata,
	   'cl'=>$tset_calls,
	   'ch'=>$tset_cashs,
	   'lo'=>$tset_loans
	   ];   

#        ..      
if (isset($fdata)) $dpel=array_merge(['fd'=>$fdata],$dpel);

#         ->   empty.
$t2=array_keys($dpel); $t1=$t2[0];

if (empty($dpel[$t1]['fe'])) $dpel[$t1]['fe']=[];					#  id      
$dpel[$t1]['fe'][]='ival_empty_page';

if (!empty($sas_eupd)) $sas_eupd=[];
$sas_eupd[]='inval_chfil';

function inval_chfil($w){
	if ($w['tfn']=="ufil") {
		$w['up']=0; $s_kv=$w['s_kv'];
		$w['lk'] = db_array("select count(*) kol from loans where uid=$s_kv and st>1");
		if ($w['lk'][0]["kol"]==0) {
			#            
			$w['nfil']=$w['fvm'][$w['n']]; 
			$nf= $w['nfil']; 
			db_request("UPDATE users set fil=$nf WHERE id=$s_kv");
			db_request("UPDATE leads set fil=$nf WHERE uid=$s_kv");
			db_request("UPDATE loans set fil=$nf WHERE uid=$s_kv");
			$w['outm']["rld"]=1;
			$w['asts']=0; $w['aah']=1;
			$w['atxt']="Country replaced successfully. Reloading....";
		} else {
			#  
			$w['asts']=3;
			$w['atxt']="Customer have working loan(s). Can`t change country. Call Supervisor for help.";
		}
	}
	return $w;
}
 
#        uid=$s_kv.    st!=20 (!=Pipe)

#        
$ate=['ci','fl','ldl','cl','ad','ad','lo','ch'];
foreach ($ate as $v) {
	$t='ival_user'; if (isset($lo) && $v=='ch') $t='ival_loan';	#  :     >       ,   >  

	if (empty($dpel[$v]['te'])) $dpel[$v]['te']=[];					#  id      
	$dpel[$v]['te'][]=$t;
	
}

$dpel['ldl']['fs']=['ldid','ldramount','ldrdays','ldrdays','ldbank','ldfacc','ldst','lddv','action'];

$dpel['ad']['fs']['mduid']=['h'=>1];

$dpel['fl']['fs']['ufac']=['e'=>1];

function ival_empty_page($w){
	$w['kkv']=array_keys ($w['data'][0]); 
	if ($w['data'][0][$w['kkv'][0]]==[]) require_once($GLOBALS["dr"]."/a/empty.php");
	return $w;
}

function ival_user($w) {
	if (isset($w['sas_sqlm']['user_id']) && $w['sas_sqlm']['user_id']>0) {
		if (isset($w['mp']["qtl"]["For user"])) $w['mp']["qt"]=$w['mp']["qtl"]["For user"];  
		unset($w['mp']["filter"]);
	}
	return $w;
}

function ival_loan($w) {
	if (isset($w['sas_sqlm']["loan"]) && $w['sas_sqlm']["loan"]>0) {
		if (isset($w['mp']["qtl"]["For loan"])) $w['mp']["qt"]=$w['mp']["qtl"]["For loan"];  
		unset($w['mp']["filter"]);
	}
	return $w;
}

# ======================   ===================================
