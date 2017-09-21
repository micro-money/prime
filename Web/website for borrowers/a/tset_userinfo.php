<?php
$cfsel=''; if ($curr_fil>-1) $cfsel=' AND u.fil='.$curr_fil;
#       
$t=['gf'=>'sastabV1',				# :    
		'tl'=>	['users'=>[],],
		'qt'=>"SELECT {(select)} FROM users u WHERE u.id={(user_id)} ",  #  $cfsel
		'mf'=>['viewMode'=>'tabVert',],	
		'fe'=>['ival_sas_sqlm'],
	];

#$ustat=['ulogin','umad'];														#  
$v=['e'=>1];
																																														
$upri=['ulogin'=>$v,'ufil'=>$v,'uname'=>$v,'ugender'=>$v,'ubirthdate'=>$v,'ufnrc'=>$v,'ucity'=>$v,'utownship'=>$v,'uaddress'=>$v,'usocial'=>$v,'usalary'=>$v,'ucname'=>$v,'ucphone'=>$v,'ucrmid'=>$v,'udv'=>$v];						# 		
																								
$usec=['ucity','utownship','ustr','ucname','ucphone','usocial'];				# 

$fs=$upri; 

$notedit=['ulogin','udv'];
foreach ($notedit as $kn) if (is2($fs,[$kn,'e'])) unset($fs[$kn]['e']);

$t['fs']=$fs;									#  

$tset_userinfo=$t;

function ival_sas_sqlm($w) {
	$w['sas_sqlm']["m"]=$w['data'][0];
	return $w;
}

?>