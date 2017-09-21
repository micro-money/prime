<?php

include($dr.'/a/tset_leads.php');	$t=$tset_leads;	# 

$t['htname']='Lead details';						#   
$t['mf']=['viewMode'=>'tabVert',];					#  
$t['tkol']=1;										#      
$t['wd']=['fd(w|n)'=>'ldid|'.$lid.'|0']; 			#     

if (empty($t['fe'])) $t['fe']=[];					#  id      
$t['fe'][]='ival_lduid';

$t['fs']=['lduid'=>['h'=>1],'ldfil','ldhow'=>['e'=>1],'ldramount'=>['e'=>1],'ldrdays'=>['e'=>1],'ldppd'=>[],'ldbank'=>['e'=>1],'ldfacc'=>['e'=>1],'ldcst'=>[],'ldnote'=>['e'=>1],'ldst','ldcl'=>[],'ldcrmid','lddv'];
#          
if (substr($selfc, 0, 12)!='/a/leadview/') unset($t['fs']['ldcl']);

if ($user['role']=='super') $t['fs']['ldppd']['e']=1;

$fdata=$t; 

function ival_lduid($w){
	$w['sas_sqlm']["user_id"]=$w['data'][0]["lduid"];
	return $w;
}