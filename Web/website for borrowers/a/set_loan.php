<?php

#         ($fdata->$fd)
require_once($dr.'/a/tset_loans.php'); $t=$tset_loans;		#  

$t['htname']='Loan details';				#   
$t['mf']=['viewMode'=>'tabVert',];			#  
$t['tkol']=1;								#      
$t['wd']=['fd(w|n)'=>'loid|'.$lo.'|0']; 	#     

#unset($t['setl']); unset($t['setd']);  'ukid',
#$t['fs']=['louid'=>['h'=>1],'ukdt'=>['h'=>1],'ukcid'=>['h'=>1],'ukdid'=>['h'=>1],'ukcst','ukcname','ukdocn','uknote','ukrecall','ukws','ukdv'];	
$t['fs']=$t['setd_fs']['For user']; $t['fs']['louid']=['h'=>1]; $t['fs']['loid']=['h'=>1];

if (empty($t['fe'])) $t['fe']=[];					#  id      
$t['fe'][]='ival_lo_data';

$fdata=$t; # print_r($data); die(); 

/*
function addCass($tabf){
	$but='<button type="button" class="btn btn-danger" onclick="sendmoney();">Send money</button>';
	$but='<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#sendmodal">Send money</button>';
	$tabf[0]['']=['v'=>$but,'e'=>''];
	return $tabf;
}
*/

function ival_lo_data($w){
	
	$w['sas_sqlm']["user_id"]=$w['data'][0]["louid"]; 
	$w['sas_sqlm']["loan"]=$w['data'][0]["loid"]; 
	$w['sas_sqlm']["lo"]=$w['data'][0];
	
	return $w;
}