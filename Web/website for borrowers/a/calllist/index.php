<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');
$page['title'] = 'Customers list'; $page['desc'] = 'Customers list (users exclude admins)';

require_once($dr.'/a/tset_calls.php'); 

$dpel=['md'=>$tset_calls];			# Подключаем настроки списка анкет
	
/*
Нужен отчет разворот для звонков по дням.
Если нет даты то звонки показывают по датам
РЕЖИМ 1: 
Группировка по дням  :
Дата , Сколько сделано звонков. 
Группировка по дням + дата :
Дата , Чемпион, Сколько сделано звонков. 
*/

#$page['js'][] = $hn.$selfp.'/m.js?ver='.$jsver;						# Подключаем персональный js
	# 'um(g)']='id|user_id'

/*	
$mode=0; if (isset($_GET['mode'])) $mode=$_GET['mode'];
if ($mode=='daygr') {
	$on='BY DAYS GROUP'; $dpel['md']['setl']=[$on=>['(g)'=>'1']];
	$dpel['md']['setd']=$on; $dpel['md']['not show all']=1;
	$dpel['md']['fs']=['ukdvd','ukkol'];
}
*/

	// sas_custom_seek('{"sas_el": "md","md(setd)": "Show all","md(w|n)": "ukdv|wt1|e","md(wt|wt1)": "day|2017.06.18"}');
	// '(w|n)'=>'ukdv|wt1|e','(wt|wt1)'=>'day|2017.06.18'
	// {sas_el: "md", md(setd): "Show all", md(w|n): "ukdv", md(w|v): "2017-06-18T00:00()2017-06-19T00:00", md(w|s): "b"}
	// wd={sas_el: "md", md(setd): "Show all", md(w|n): "ukdv|2017-06-18T00:00()2017-06-19T00:00|b"};

if (isset($_POST['Day'])) {
	$sas_el=$_POST['sas_el']; $_POST[$sas_el.'(setd)']='Show all'; $daym=explode('.',$_POST['Day']); $dayt=$daym;  $dayt[2]=intval($dayt[2])+1;
	# $_POST[$sas_el.'(w|n)']='ukdv|wt1|e'; $_POST[$sas_el.'(wt|wt1)']='day|'.$_POST['Day'];
	$_POST[$sas_el.'(w|n)']='ukdv'; $_POST[$sas_el.'(w|v)']=implode('-',$daym).'()'.implode('-',$dayt); $_POST[$sas_el.'(w|s)']='b';
} 
	
require_once($dr.'/tool/sas/stage1_settings.php');  				# Создаем динамические элементы (включая необходимые запросы в базу и прочая нагрузочная часть)
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				# Аякс работа если есть
require_once($dr.'/tool/sas/stage2_build_elements.php');			# Выполняем запросы к базе данных и строим html у динамических элементов

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
<div class="container-fluid" style="margin-top: -20px;">
		<div class="form-inline" style="padding: 5px;">
			<div class="form-group"><span style="font-size: 30px;" >Call list</span></div>
			<!--<select class="form-control" style="margin-left: 30px;" onchange="call_chmode(this.value)">
				<option value="0" selected>Full list</option>
				<option value="1">Group by Days</option>
			</select>-->
		</div>
		<?= $html['md'] ?>
</div>
<?php require PHIX_CORE . '/render_view.php';