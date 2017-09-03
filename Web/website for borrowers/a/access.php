<?php
$ttt2=''; # "Location: /login?act=quit"

$ttt1=0;
if (isset($sas)) 			$ttt1=1;	# Если мы обращаемся к типичным sas аяксам
if (isset($_GET['cajx'])) 	$ttt1=1;	# Если мы зашли как cajx.php или как любой другой скрипт.

if (!isset($user) || !isset($user['role']) || $user['role']=='') {
	# Если клиент не авторизован вообще или это не админ > всегда на титул. Неважно как зашел.
	$ttt2='/';  # login?act=quit 
} else {
	if (!in_array($user['role'],$rolem)) {
		# Если у нас какой то админ
		$atxt="You do not have access for this section. Contact your administrator.";
		if ($ttt1==0) die($atxt);
		if ($ttt1==1) {
			$outm["ef"]="showalert"; $outm["asts"]=3;  $outm["atxt"]=$atxt;  
			mysql_close(); die(json_encode($outm));
		}
	}
}
		
if ($ttt2!='') { header("Location: ".$ttt2); exit; }
