<?php

# Наша задача пеекинуть клиента на нужную анкету ели он зашел не туда
if (!isset($user['id']))  {header("Location: /"); exit;} 
$o=checkUser(); $nheader=''; 

# Повеяем ели у клиента пееход на app_wizard но нет активных лидов > на главную
if ($o['h']=='app_wizard') {
	if ($user['a_lid']==0) {
		#
		$nheader='/';
	}
	
}

# Ели по анализу клиента ему на дугую таницу -> тогда туда
if ($o['h']!=$this_wizard_name) $nheader='/'.$o['h'];

if ($nheader!='') {
	header("Location: ".$nheader); exit; 
}

?>