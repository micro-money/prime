<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1;  
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $lo=intval($_GET['id']); } else { die('need loan id'); } 

$page['title'] = 'Loan #'.$lo.' info'; $page['desc'] = 'Loan infomation';

#         ($fdata->$fd)
require_once($dr.'/a/set_loan.php'); 

$fdata['fs'][]='lorc';	#      

if (!empty($sas_eupd)) $sas_eupd=[];
$sas_eupd[]='ival_banupdate';		#         'e'=>1        

function ival_banupdate($w){
	if ($w['tfn']=="lospd") {
		if ($w['sendm']==0) $w['up']=0;
		if ($w['fvm'][$w['n']]=="") {
			$w['fvm'][$w['n']]="null"; $w['tts']["t"]="m";
		}
	}	
	return $w;
}

#          
if (!empty($sas_eafterupd)) $sas_eafterupd=[];
$sas_eafterupd[]='ival_areload';

function ival_areload($w){
	
	if (in_array("lospd",$w['flm'])) {
		$w['outm']["eu"]=$w['hn']."/a/loanview/?id=".$w['lo'];  				
		$w['o']=calcDebt(["id"=>$w['lo'],"r"=>1,"m"=>5]);			
	}	
	
	return $w;
}


$sendm=0; $cashier=['super','cashier']; 
if (in_array($user['role'],$cashier)) {
	$sendm=1;
	// 'rm'
	$fdata['fs'][]='lorm'; # print_r($data); die(); 	
	$fdata['fs'][]='losm';	
	
	$fdata['fs']['lospd']['e']=1;	#            
	//                chspd
	
	//print_r($fdata); die();

	#             .
	$fdata['fs']['loterm']	['e']=1;
	$fdata['fs']['lowa']	['e']=1;
}

require_once($dr.'/a/loanview/cajx.php');							#    
$page['js'][] = $hn.'/a/loanview/m.js?ver='.$jsver;					#   js
	
require_once($dr.'/a/set_userview.php');

require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				#    
require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   

$cuname=$sas_sqlm['m']['uname'];  $MainPhone=$sas_sqlm['m']['ulogin'];

if ($sendm==1) {	#      >     
	$shead='Sending money to '.$cuname;										#   
	$rhead='Recieve money from '.$cuname;									#   
	$cacc='<span name="cacc">'.$sas_sqlm["lo"]["lobacc"].'<span>';			#  
	$dvm=explode(' ',$sas_sqlm["lo"]["lodv"]); $dvmin=$dvm[0];				#    
	
	$cashman=HtmlTable_option(['fs'=>['v'=>flibs(['n'=>'AllAdminList']),'ie'=>['a'=>'style="width:auto;" id="cashman"']],'tdv'=>4]);  
	$roacc=HtmlTable_option(['fs'=>['v'=>$libs['UsrOurWallet'],'ie'=>['a'=>'style="width:auto;" id="roacc"']],'tdv'=>'d3973abf-9b7e-4b85-8d53-00b9d0e416b6']);
	$soacc=HtmlTable_option(['fs'=>['v'=>$libs['UsrOurWallet'],'ie'=>['a'=>'style="width:auto;" id="soacc"']],'tdv'=>'d3973abf-9b7e-4b85-8d53-00b9d0e416b6']);
}

if ($sas_sqlm["lo"]["lost"]==20) $bd=1;
if (in_array($sas_sqlm["lo"]["lost"],[4,5,6,7,8]) && $user['role']=='super') $wod=1; 	#       

#        
$lostm = db_array("select * from loans_sthist where loan=$lo order by dv ASC");
if (count($lostm)>0) {
	$lounits=[]; 
	$fn=[
		'c'	=>'Set-off day',
		'a'	=>'Amount',
		't'	=>'Days ago',
		'd'	=>'Debt body',
		'pf'=>'Percen final',
		'ps'=>'Percent start',
	];
	
	foreach ($lostm as $k=>$v) {
		$oj=json_decode($v['note'], true);

		$ddb=$oj['db'];		#    (     )
		$fp	=$oj['fp'];		#  
		$lt	=$oj['lt'];		#      
		$ot	=$oj['ot'];		#   
		$wt	=$oj['wt'];		#             
		$fs	=$oj['fs'];		#       
		$fr	=$oj['fr'];		#        
		$lp	=$oj['lp'];		#     
		$c 	=$oj['c'];		#  
		
		$tdata=htmlTable(['tab'=>$c,'head'=>$fn]);	#   
		
		$tdeb=($ddb+$fp); $tdebc='danger'; if ($tdeb<1) $tdebc='success';
		
		$ctit=$v['dv'].' ['.$libs['loans.m'][$v['m']].'] TotalDebt: <label class="bg-'.$tdebc.'">'.$tdeb.'</label> (Debt: '.$ddb.' Fee: '.$fp.')';		

		#     
		$stt=' Status: '.$libs['loans.st'][$v['nst']];
		if ($v['nst']!=$v['ost']) $stt=' <strong>Change</strong> Status: '.$libs['loans.st'][$v['ost']].' > '.$libs['loans.st'][$v['nst']];
		
		$ctit.=$stt;
		
		#     
		if (isset($oj['spd'])) $ctit.=' Stop Percent Date: '.$oj['spd'];
	
		$lounits[]='<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#collapse'.$k.'">
							'.$ctit.'
						</a>
					</h4>
					</div>
					<div id="collapse'.$k.'" class="panel-collapse collapse">		
					  <div class="panel-body">'.$tdata.'</div>
					</div>';		
	}
	
	$lohist='<div class="panel-group">
				<div class="panel panel-default">
					'.implode('',$lounits).'
				</div>
			</div>';
}
/* --------------------------  ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<div class="row">
			<div class="col-12">
				<?#= $topalerts ?>
				<h3>Loan #<?= $lo ?>&nbsp;&nbsp;&nbsp;<?= $cuname ?>&nbsp;&nbsp;&nbsp;<a href="sip:<?= $MainPhone ?>"><?= $MainPhone ?></a>&nbsp;&nbsp;&nbsp;
				<? if (isset($bd)) { ?>
					<button type="button" class="btn btn-danger" 	data-toggle="modal" data-target="#denied">Denied for loan</button>
				<? } ?>
				<? if (isset($wod)) { ?>
					<button type="button" class="btn btn-danger" 	data-toggle="modal" data-target="#wodm">Written-off debt</button>
				<? } ?>
				<? if ($sendm==1) { ?>
					<!--
					<button type="button" class="btn btn-danger" 	data-toggle="modal" data-target="#smoney">Send money</button>
					<button type="button" class="btn btn-success" 	data-toggle="modal" data-target="#rmoney">Receive money</button>
					-->
				<? } ?>
				</h3>
			</div>
		</div>
		<? require_once($dr.'/a/tmpl/workleads.php'); ?>
		<? if (isset($bd)) require_once($dr.'/a/tmpl/deniedmodal.php'); ?>
		<? if (isset($wod)) require_once($dr.'/a/tmpl/wodmodal.php'); ?>
		<? if ($sendm==1) require_once($dr.'/a/tmpl/cashmodal.php'); ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';