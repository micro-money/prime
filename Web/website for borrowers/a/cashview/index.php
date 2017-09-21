<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1;  
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) { $cash=intval($_GET['id']); } else { die('need lead id'); } 

$page['title'] = 'Cash #'.$cash.' info'; $page['desc'] = 'Cash infomation';

#         ($fdata->$fd)
	
// ==========
require_once($dr.'/a/set_cash.php');
require_once($dr.'/a/set_userview.php');

#              
#       .
if ($user['role']=='super') {
	// 'moperday'
	//    ..   -      .  .
}

require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				#    
require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   

		$cuname=$sas_sqlm['m']['uname'];
		$MainPhone=$sas_sqlm['m']['ulogin'];

/* --------------------------  ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<div class="row">
			<div class="col-12">
				<?#= $topalerts ?>
				<h3>Cash #<?= $cash ?>&nbsp;&nbsp;&nbsp;<?= $cuname ?>&nbsp;&nbsp;&nbsp;<a href="sip:<?= $MainPhone ?>"><?= $MainPhone ?></a>&nbsp;&nbsp;&nbsp;</h3>
			</div>
		</div>
		<? require_once($dr.'/a/tmpl/workleads.php'); ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';