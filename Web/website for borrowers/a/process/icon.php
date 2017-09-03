<?php
/*
icon:  id>8848 and 
0 - новая большая фотка -> след этап 1 (обрезана нарезана и поменена)
2 - надо удалить за ненадобностью -> 4 (или удалено) След стадия это удаление строчки из таблицы
3 - файла не существует
5 - в работе по нарезке

Алгоритм: Берем все строчки из таблицы users_files где icon=0
*/

$lim=10; if (isset($cron_pwjs) && isset($cron_pwjs['lim'])) $lim=$cron_pwjs['lim'];

require_once($dr.'/tool/uni/crop.php'); # Нарезка файлов

$qw='icon=0';  $mode='new';
if (isset($_GET['iconid']) && is_numeric($_GET['iconid'])) $qw='id='.intval($_GET['id']); 
if (isset($_GET['iconmode'])) $mode=$_GET['iconmode']; 

if ($mode=='del') { $qw='icon=2'; $lim=1000; }					# Если удаляем то берем большой список
if ($mode=='ref') { $qw='icon in (3,5) and h=0'; $lim=1000; }	# Если проверяем наличие фото по статусу 3 -> 1 
if ($mode=='clean') { $qw='icon>-1'; $lim=50000; }				# Если проверяем наличие фото по статусу 3 -> 1 

$newpic = db_array("SELECT id,fp FROM `users_files` WHERE $qw limit $lim"); 
$cron_wjs=[]; 
foreach ($newpic as $k=>$v) {
	$v['fp']=$dr.$v['fp'];
	$mp=['v'=>$v]; if (isset($noecho)) $mp['ne']=$noecho;
	if ($mode=='new') $icon=makeIcon($mp);
	if ($mode=='del') $icon=delIcon($mp);
	if ($mode=='ref') $icon=refIcon($mp);
	if ($mode=='clean') $icon=cleanIcon($mp);
	
	# Пополняем статистический массив на выход
	if (!isset($cron_wjs[$icon])) $cron_wjs[$icon]=0; 
	$cron_wjs[$icon]++; 
}

if (count($newpic)==0) $cron_nolog=1;	# Не пишем в лог если ничего нет в работе
	
$cron_ar=count($newpic); 
if ($cron_ar>0 && $cron_ar==$lim) $cron_onemoretime=1;  # ЗАпускаем еще раз если у нас не все отработало

function cleanIcon($mp) {	# Удаление файлов с icon=2
	Global $nr; $v=$mp['v'];
	
	$paths=getPaths(['f'=>$v['fp']]);
	$file=$paths['file'];
	$dir=$paths['dir'];
	
	if (!file_exists($dir.'/300/'.$file)) {
		#db_request('update application_files SET icon=5 where id='.$v['id']);
		db_request('delete from users_files where id='.$v['id']);
		unlink($dir.'/'.$file);			# Оригинал стираем
	}
}

function delIcon($mp) {	# Удаление файлов с icon=2
	Global $nr; $v=$mp['v'];
	
	$paths=getPaths(['f'=>$v['fp']]);
	$file=$paths['file'];
	$dir=$paths['dir'];
	
	unlink($dir.'/'.$file);			# Оригинал
	unlink($dir.'/300/'.$file);		# Иконка 300
	unlink($dir.'/200/'.$file);		# Иконка 200
	
	#db_request('update application_files SET icon=5 where id='.$v['id']);
	db_request('delete from users_files where id='.$v['id']);
}

function refIcon($mp) {	# Удаление файлов с icon=2
	Global $nr; $v=$mp['v'];
	
	$paths=getPaths(['f'=>$v['fp']]);
	$file=$paths['file'];
	$dir=$paths['dir'];
	
	$icon=1;
	if (!file_exists($dir.'/'.$file)) $icon=7; # Оригинала нет
	if (!file_exists($dir.'/300/'.$file)) $icon=8; 
	if (!file_exists($dir.'/200/'.$file)) $icon=9; 
		
	db_request('update users_files SET icon='.$icon.' where id='.$v['id']);
	#db_request('delete from application_files where id='.$v['id']);
}

function getPaths($mp){
	$paths=[]; 
	$fm=explode('/',$mp['f']); 
	$paths['file']=$fm[count($fm)-1];								# Имя файла
	unset($fm[count($fm)-1]);
	$paths['dir']=implode('/',$fm);									# Дирректория где лежит файл
	return $paths;
}

function makeIcon($mp) {
	Global $nr; $v=$mp['v'];
	
	$icon=5;				# Зависла в работе
	
	# Ставим статус 5 - в работе по нарезке
	db_request('update users_files set icon=3 where id='.$v['id']);
		
	$ofile=$v['fp'];						# Полный путь(дирректория) + имя файла
	#echo $nr.'z1';
	if (!file_exists($ofile)) {
		$icon=3;			# Если не существует
		#echo $nr.'z2';
	} else {
		$icon=1;			# Если существует все ok

		$paths=getPaths(['f'=>$ofile]);
		$file=$paths['file'];
		$dir=$paths['dir'];
		
		if(!is_dir($dir.'/300/')) mkdir($dir.'/300/', 0777, true);		# Восстанавливаем подпапки для иконок
		if(!is_dir($dir.'/200/')) mkdir($dir.'/200/', 0777, true);
		#echo $nr.'z5';
		resize($ofile, $dir.'/800-'.$file, 800, 0);					# Нарезаем оригинал на 800
		#echo $nr.'1ok';
		unlink($ofile);														# Удаляем оригинал
		rename($dir.'/800-'.$file, $ofile);							# Переменовываем 800 в оригинал	
		#echo $nr.'2ok';
		
		if (!file_exists($dir.'/300/'.$file)) resize($ofile, $dir.'/300/'.$file, 300, 0); // Уменьшаем до 100х100 с сохранением прапорций если нет
		#echo $nr.'3ok';
		if (!file_exists($dir.'/200/'.$file)) resize($dir.'/300/'.$file, $dir.'/200/'.$file, 200, 0); // Уменьшаем до 200х200 с сохранением прапорций если нет
		#echo $nr.'4ok';
			
	}
	
	db_request('update users_files set icon='.$icon.' where id='.$v['id']);			
	if (!isset($mp['ne'])) echo $nr.'['.$icon.'] '.$v['id'].'-'.$ofile;
	
	return $icon;
}

