<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Leads list';
$page['desc'] = 'Leads list';

require_once($dr.'/a/tset_leads.php'); 

$dpel=['md'=>$tset_leads];			#    
	
require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )
if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				#    
require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   

/* --------------------------  ------------ */ ob_start(); ?>

		<h2>Leads list</h2>
		<?= $html['md'] ?>

<?php require PHIX_CORE . '/render_view.php';