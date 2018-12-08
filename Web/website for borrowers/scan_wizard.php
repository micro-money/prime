<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/app_func.php');
$this_wizard_name='scan_wizard';

require_once 'app_init.php';

//	print_r($user);
/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */
$page['title'] = 'Scan Form';
$page['js_raw'] = <<<JS
    window.gon={};
    gon.locale="en";
    gon.translations={"js.slider.needAmount":"MMK","resendCode":"translation missing: en.resend_code","js.slider.terms":"days"};
JS;

/*
$sql_kldata="SELECT al.id,sum(al.lam) as deb,al.UsrOpportunityId,al.uphone,DATE_FORMAT(if(payday>UsrNewRepayDate,payday,UsrNewRepayDate),'%d.%m.%Y') as fpayday FROM users as u, crm_in_activeloans as al 
WHERE u.id = ".$user['id']." and u.login = al.uphone and al.os=0 and al.lam>0 "; 
*/
	
require_once(MC_ROOT.'/tool/sec_set.php');
#$bad2keys=array_keys($bad2); foreach ();
	
$sql_kldata="SELECT
  '".$user['login']."' as uphone,round(sum(UsrAmountToPaid)) as deb,UsrOpportunityId,DATE_FORMAT(min(if(UsrApprovedRepayDate>ifnull(UsrPromiseToPayDate,'2001.01.01'),UsrApprovedRepayDate,UsrPromiseToPayDate)),'%d.%m.%Y') as fpayday
FROM calc_od where Title like '%".$user['login']."%'"; 
# ,sync_id as id and StageId in ('".implode("','",array_keys($bad2))."')  09767195322

$user_edata = db_row($sql_kldata);	// Открытые сделки

$step=0;  // 0 - Обычная анкета 10 - в один шаг 

if (isset($_GET['step'])) $step = intval($_GET['step']);
$rq=['`updated_at` = now()'];

if (!empty($_POST['data'])) {
	switch ($step) {
		case 0:	
			if (isset($_FILES) && count($_FILES)>0) {	
				$ah_fn=3; $ah_ff='photo1';
				$ag_flid=ah_uploadfile(['n'=>$ah_fn,'nf'=>$ah_ff]);	
				if ($ag_flid>0) {
					# Дублируем  строчку на выгрузку 
					#db_request("INSERT INTO `crm_out_scans` (login,stype,scan,dv) select u.login,f.ft,concat('http://money.com.mm',f.fp),f.dv from application_files as f,users as u where f.id=$ag_flid and u.id=f.uid");
				} else {
					aPgE("<br>Photo not uploaded. Please try again.");
				}
			} 
			break;
		case 1:
			
			break;
		default: header("Location: /"); exit;
	}

}

$ss_harr=[0=>'Payment proof photo',1=>'Finished'];
$ss_cname=l('Customer'); if (isset($user['Name'])) $ss_cname=$user['Name'];
$ss_deb=''; if (isset($user_edata['deb'])) $ss_deb=$user_edata['deb'].' mmk';

$ss_loanumber=''; if (isset($user_edata['UsrOpportunityId']) && $user_edata['UsrOpportunityId']!='') $ss_loanumber=$user_edata['UsrOpportunityId'];
$ss_uphone=''; if (isset($user_edata['uphone']) && $user_edata['uphone']!='') $ss_uphone=$user_edata['uphone'];
$ss_payday=''; if (isset($user_edata['fpayday'])) $ss_payday=$user_edata['fpayday'];


$ss_hakey=$step+1;		
$ss_fhref='/scan_wizard'; if ($step>0) $ss_fhref.='?step='.$step;
$tmpl='sections/frontend/payment/step' . $step;					# Шаблон страницы след шага анкеты
/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ 
ob_start(); 	require_once($dr.'/templates/'.$tmpl.'.php');
?>
<? #e($tmpl) ?>
<?php require PHIX_CORE . '/render_view.php';