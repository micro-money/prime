<?php
$ttt2=''; # "Location: /login?act=quit"

$ttt1=0;
if (isset($sas)) 			$ttt1=1;	#      sas 
if (isset($_GET['cajx'])) 	$ttt1=1;	#     cajx.php     .

if (!isset($user) || !isset($user['role']) || $user['role']=='') {
	#          >   .   .
	$ttt2='/';  # login?act=quit 
} else {
	if (!in_array($user['role'],$rolem)) {
		#      
		$atxt="You do not have access for this section. Contact your administrator.";
		if ($ttt1==0) die($atxt);
		if ($ttt1==1) {
			$outm["ef"]="showalert"; $outm["asts"]=3;  $outm["atxt"]=$atxt;  
			mysql_close(); die(json_encode($outm));
		}
	}
}
		
if ($ttt2!='') { header("Location: ".$ttt2); exit; }
