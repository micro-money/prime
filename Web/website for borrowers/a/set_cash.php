<?php

include($dr.'/a/tset_cashs.php');	$t=$tset_cashs;	# 

$t['htname']='Cash details';						#   
$t['mf']=['viewMode'=>'tabVert',];					#  
$t['tkol']=1;										#      
$t['wd']=['fd(w|n)'=>'mid|'.$cash.'|0']; 			#     	
$t['fs']=$t['setd_fs']['For user']; $t['fs']['muid']=['h'=>1];

if (empty($t['fe'])) $t['fe']=[];
$t['fe'][]='ival_muid';								#  id      

$fdata=$t; 

function ival_muid($w) {
	$w['sas_sqlm']["user_id"]=$w['data'][0]["muid"];
	return $w;
}

