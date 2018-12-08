<?php

# Наша задача перекинуть клиента на нужную анкету если он зашел не туда
if (!isset($user['id']))  {header("Location: /"); exit;} 
$o=checkUser(); $nheader=''; 

# Проверяем если у клиента переход на app_wizard но нет активных лидов > на главную
if ($o['h']=='app_wizard') {
	if ($user['a_lid']==0) {
		#
		$nheader='/';
	}
	
}

# Если по анализу клиента ему на другую страницу -> тогда туда
if ($o['h']!=$this_wizard_name) $nheader='/'.$o['h'];

if ($nheader!='') {
	header("Location: ".$nheader); exit; 
}

?>