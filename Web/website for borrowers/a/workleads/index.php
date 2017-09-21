<?php 
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');
$page['title'] = 'Leads PriWork';  $page['desc'] = 'Leads primary work';

/*
:
	                  
	    .      .
	     .
	           

	#         .
	#           
	# (    )
	#     
	#    
	
	select t.*,wt.wtime from (
	# 1)      st=ompleted              []
	select id,1 p,takework,dv from leads where st=2 and a_doc=2 and a_cd=1 and a_kd=2 
	union
	# 2)      st=ompleted       
	select id,2,takework,dv from leads where st=2 and a_doc=2 and a_kd=1                        
	) t left join leads_wtime wt on (wt.id=t.id ) order by p ASC,dv DESC
*/
	$cfil=-1; if ($curr_fil>0) $cfil=$curr_fil;
	/* 
	 :
	              .
	  >    .
	  >     .
	*/
	$cid=$user['id'];
	
	$fqw="fil=$cfil and cid=$cid"; if (isset($_GET['id']) && intval($_GET['id'])>0) $fqw="cid=$cid"; 
	
	$wl = db_array("select id,uid,cid from leads where $fqw"); 
	
	require_once('cajx.php');		#     
	
	if (count($wl)==0) {	# 0)        
		$wl=[]; #  
		if (isset($sas)) unset($sas);	#      ..     .
	} 
	/*
	a_od=1 (1    , 2    )
	a_kd=2 (2    , 1    )
	a_doc=1  
	a_doc=4  
	a_doc=3  nrc
	a_doc=2    
	*/

	if (count($wl)==0) {	# 1) st=2 + 
		$wl = db_array("select id,uid,cid from leads where fil=$cfil and st=2 and a_cd>0 and cid=0 and a_rst!=1 ORDER BY dv DESC LIMIT 1"); 		
	}
	if (count($wl)==0) {	# 2) st=2 +  
		$wl = db_array("select id,uid,cid from leads where fil=$cfil and st=2 and a_cd=0 and cid=0 and a_rst!=1 ORDER BY dv DESC LIMIT 1"); 		
	}
	
	/*
	if (count($wl)==0) {	# 3) st=2 +  +      
		$wl = db_array("select id,uid,cid from leads where st=2 and a_doc!=2 and a_kd=1 and cid=0 ORDER BY dv DESC LIMIT 1"); 		
	}
	if (count($wl)==0) {	# 4) st=1 +      
		#$wl = db_array("select id,uid,cid from leads where st=1 and a_od=1 and cid=0 ORDER BY dv DESC LIMIT 1"); 		
	}	
	if (count($wl)==0) {	# 5) st=0 +     
		#$wl = db_array("select id,uid,cid from leads where st=0 and a_od=1 and cid=0 ORDER BY dv DESC LIMIT 1"); 		
	}
	*/
	# todo:             .          30 .    30     .
	
/*
     
select id,uid,cid,1 t,dv from leads where st=2 and a_od=1 and a_kd=2 and cid=0 
union
select id,uid,cid,2 t,dv from leads where st=2 and a_doc=2 and a_kd=1 and cid=0 
union
select id,uid,cid,3 t,dv from leads where st=2 and a_doc!=2 and a_kd=1 and cid=0 
ORDER BY t,dv DESC;
*/
	
	#          

	if (count($wl)==0) {
		$fl=[];
	} else {
		$fl=$wl;
		if ($wl[0]['cid']==0) {	#       
			$lid=$wl[0]['id']; #id   >      
			db_request("update leads set cid=$cid where id=$lid and cid=0");	
			#          >   
			$fl = db_array("select id,uid,cid from leads where cid=$cid");
			if (count($fl)>0) {		#       
				db_request("DELETE FROM leads_wtime where cid=$cid");	#           users_calls
				db_request("update leads SET cid=0 where cid=$cid and id!=$lid");	#        (         )
				db_request("insert into leads_wtime (cid,lid,ts) VALUES ($cid,$lid,now())");	
			}
		}
	}
	
	if (count($fl)>0) {
		$sas_sqlm["user_id"]=$fl[0]['uid']; 
		$lid=$fl[0]['id']; 
		require_once($dr.'/a/set_lead.php');
		/*
		      .
		 :
		         .
		         >     
		   call .
		*/

		$page['js'][] = $hn.$selfc.'/m.js?ver='.$jsver;						#   js	
		
		require_once($dr.'/a/set_userview.php');							#   
		
		#   
		# $dpel['pi']['ci']='$sas_sqlm["ci"]=$data;';
		
		require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )
		if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');				#    
		require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   
	
		$cuname=$sas_sqlm['m']['uname'];
		$MainPhone=$sas_sqlm['m']['ulogin'];
		
		#   
		ob_start();
?>
	<div class="container-fluid" style="margin-top: -20px;">
		<div class="row">
			<div class="col-12">
				<?#= $topalerts ?>
				<h3>Lead #<?= $lid ?>&nbsp;&nbsp;&nbsp;<?= $cuname ?>&nbsp;&nbsp;&nbsp;<a href="sip:<?= $MainPhone ?>"><?= $MainPhone ?></a>&nbsp;&nbsp;&nbsp;</h3>
			</div>
		</div>
		<? require_once($dr.'/a/tmpl/lead_call_new_act.php'); ?>
		<? require_once($dr.'/a/tmpl/workleads.php'); ?>
	</div>
<?		require PHIX_CORE . '/render_view.php';
	} else {
		
		
		$wfl = db_array("select fil,count(*) kol from leads where st=2 and a_rst!=1 group by 1"); 
	
		$mhead='There are not full completed leads. (<a href="" >try to refresh</a>)';
		
		if (count($wfl)>0) {
			if ($curr_fil==-1) $mhead='For start Call Work. Please select any country:';
			if ($curr_fil==0)  $mhead='We are not working myanmar leads now. Please select other country:';
		}
		ob_start();
?>
	<div class="container-fluid">
		<div class="row">
			<h2><?= $mhead ?></h2>
			<? if (count($wfl)>0) { ?>
			<ul>
				<? foreach ($wfl as $k=>$v) { ?>
					<li><a href="/a/f/chdb?fil=<?= $v['fil'] ?>&h=<?= $_SERVER['REQUEST_URI'] ?>"><?= $countryid[$v['fil']]['t']." (".$v['kol'].")"; ?></a></li>
				<? } ?>
			</ul>
			<? } ?>
		</div>
	</div>
<?php require PHIX_CORE . '/render_view.php';  }