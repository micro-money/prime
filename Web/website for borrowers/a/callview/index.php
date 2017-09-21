<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $call_id=intval($_GET['id']); } else { die('need call id'); } 
$page['title'] = 'Call #'.$call_id.' info'; $page['desc'] = 'Call full details';

include($dr.'/a/tset_calls.php');	$t=$tset_calls;		#  

$t['htname']='Call details';					#   
$t['mf']=['viewMode'=>'tabVert',];				#  
$t['tkol']=1;									#      
$t['wd']=['fd(w|n)'=>'ukid|'.$call_id.'|0']; 	#     

if (empty($t['fe'])) $t['fe']=[];
$t['fe'][]='ival_ukuid';

#unset($t['setl']); unset($t['setd']);  'ukid',
$t['fs']=['ukuid'=>['h'=>1],'ukdt'=>['h'=>1],'ukcid'=>['h'=>1],'ukdid'=>['h'=>1],'ukcst','ukcname','ukdocn','uknote','ukrecall','ukws','ukdv'];	
$fdata=$t; # print_r($data); die(); 

function ival_ukuid($w){
	$w['sas_sqlm']["user_id"]=$w['data'][0]["ukuid"];
	return $w;
}

require_once($dr.'/a/set_userview.php');
	
require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				#    
require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   

$cuname=$sas_sqlm['m']['uname'].' ['.$sas_sqlm['m']['ulogin'].']';

/* --------------------------  ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<h2>Call #<?= $call_id ?> details to the customer <?= $cuname ?></h2>
<? require_once($dr.'/a/tmpl/workleads.php'); ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';