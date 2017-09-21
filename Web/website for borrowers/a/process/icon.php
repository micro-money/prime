<?php
/*
icon:  id>8848 and 
0 -    ->   1 (   )
2 -     -> 4 ( )       
3 -   
5 -    

:      users_files  icon=0
*/

$lim=10; if (isset($cron_pwjs) && isset($cron_pwjs['lim'])) $lim=$cron_pwjs['lim'];

require_once($dr.'/tool/uni/crop.php'); #  

$qw='icon=0';  $mode='new';
if (isset($_GET['iconid']) && is_numeric($_GET['iconid'])) $qw='id='.intval($_GET['id']); 
if (isset($_GET['iconmode'])) $mode=$_GET['iconmode']; 

if ($mode=='del') { $qw='icon=2'; $lim=1000; }					#      
if ($mode=='ref') { $qw='icon in (3,5) and h=0'; $lim=1000; }	#       3 -> 1 
if ($mode=='clean') { $qw='icon>-1'; $lim=50000; }				#       3 -> 1 

$newpic = db_array("SELECT id,fp FROM `users_files` WHERE $qw limit $lim"); 
$cron_wjs=[]; 
foreach ($newpic as $k=>$v) {
	$v['fp']=$dr.$v['fp'];
	$mp=['v'=>$v]; if (isset($noecho)) $mp['ne']=$noecho;
	if ($mode=='new') $icon=makeIcon($mp);
	if ($mode=='del') $icon=delIcon($mp);
	if ($mode=='ref') $icon=refIcon($mp);
	if ($mode=='clean') $icon=cleanIcon($mp);
	
	#     
	if (!isset($cron_wjs[$icon])) $cron_wjs[$icon]=0; 
	$cron_wjs[$icon]++; 
}

if (count($newpic)==0) $cron_nolog=1;	#         
	
$cron_ar=count($newpic); 
if ($cron_ar>0 && $cron_ar==$lim) $cron_onemoretime=1;  #         

function cleanIcon($mp) {	#    icon=2
	Global $nr; $v=$mp['v'];
	
	$paths=getPaths(['f'=>$v['fp']]);
	$file=$paths['file'];
	$dir=$paths['dir'];
	
	if (!file_exists($dir.'/300/'.$file)) {
		#db_request('update application_files SET icon=5 where id='.$v['id']);
		db_request('delete from users_files where id='.$v['id']);
		unlink($dir.'/'.$file);			#  
	}
}

function delIcon($mp) {	#    icon=2
	Global $nr; $v=$mp['v'];
	
	$paths=getPaths(['f'=>$v['fp']]);
	$file=$paths['file'];
	$dir=$paths['dir'];
	
	unlink($dir.'/'.$file);			# 
	unlink($dir.'/300/'.$file);		#  300
	unlink($dir.'/200/'.$file);		#  200
	
	#db_request('update application_files SET icon=5 where id='.$v['id']);
	db_request('delete from users_files where id='.$v['id']);
}

function refIcon($mp) {	#    icon=2
	Global $nr; $v=$mp['v'];
	
	$paths=getPaths(['f'=>$v['fp']]);
	$file=$paths['file'];
	$dir=$paths['dir'];
	
	$icon=1;
	if (!file_exists($dir.'/'.$file)) $icon=7; #  
	if (!file_exists($dir.'/300/'.$file)) $icon=8; 
	if (!file_exists($dir.'/200/'.$file)) $icon=9; 
		
	db_request('update users_files SET icon='.$icon.' where id='.$v['id']);
	#db_request('delete from application_files where id='.$v['id']);
}

function getPaths($mp){
	$paths=[]; 
	$fm=explode('/',$mp['f']); 
	$paths['file']=$fm[count($fm)-1];								#  
	unset($fm[count($fm)-1]);
	$paths['dir']=implode('/',$fm);									#    
	return $paths;
}

function makeIcon($mp) {
	Global $nr; $v=$mp['v'];
	
	$icon=5;				#   
	
	#   5 -    
	db_request('update users_files set icon=3 where id='.$v['id']);
		
	$ofile=$v['fp'];						#  () +  
	#echo $nr.'z1';
	if (!file_exists($ofile)) {
		$icon=3;			#   
		#echo $nr.'z2';
	} else {
		$icon=1;			#    ok

		$paths=getPaths(['f'=>$ofile]);
		$file=$paths['file'];
		$dir=$paths['dir'];
		
		if(!is_dir($dir.'/300/')) mkdir($dir.'/300/', 0777, true);		#    
		if(!is_dir($dir.'/200/')) mkdir($dir.'/200/', 0777, true);
		#echo $nr.'z5';
		resize($ofile, $dir.'/800-'.$file, 800, 0);					#    800
		#echo $nr.'1ok';
		unlink($ofile);														#  
		rename($dir.'/800-'.$file, $ofile);							#  800  	
		#echo $nr.'2ok';
		
		if (!file_exists($dir.'/300/'.$file)) resize($ofile, $dir.'/300/'.$file, 300, 0); //   100100     
		#echo $nr.'3ok';
		if (!file_exists($dir.'/200/'.$file)) resize($dir.'/300/'.$file, $dir.'/200/'.$file, 200, 0); //   200200     
		#echo $nr.'4ok';
			
	}
	
	db_request('update users_files set icon='.$icon.' where id='.$v['id']);			
	if (!isset($mp['ne'])) echo $nr.'['.$icon.'] '.$v['id'].'-'.$ofile;
	
	return $icon;
}

