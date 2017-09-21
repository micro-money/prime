<?php

#if (!isset($libs)) require_once($dr.'/tool/sas/constants.php');					#  

if (isset($_GET['cajx'])) {
	
	$outm=[];  $cid=$user['id'];  $cajx=$_GET['cajx']; 	#if (isset($_GET['mode'])) $mode=$_GET['mode']; 

	if ($cajx=='sleadwork') {
		#      ,    ->   ,      

		$wl = db_array("select id,uid,cid,fil,st,a_rst from leads where id=$lid");  $wld=$wl[0];
		
		$banfil	=[]; 			#    
		$banst	=[4,5,6]; 		#     
		$cblk	='';
		#          
		if (in_array($wld['fil'],$banfil)) $cblk.="We are not working {$countryid[$wld['fil']]['t']} leads. ";	

		#         .
		if (in_array($wld['st'],$banst)) {
			$outm["aah"]=3;
			$cblk.="This lead is parked. Please ask the superviser to change this lead status. ";
		}
		
		#          .
		if ($wld['a_rst']==1) {
			#$outm["aah"]=1;
			$rc = db_array("select recall from users_calls where dt=1 and did=$lid and now()<recall limit 1");  
			if (count($rc)>0) $cblk.="This customer ask to call him later after {$rc[0]['recall']}.";
		}	
		
		if ($wld['cid']!=0 && $wld['cid']!=$cid) {	#      - 
			$ak = db_array("select max(ts) mts,TIMEDIFF(now(),ts) sm from leads_wtime where lid=$lid and ts>date_add(now(), interval - 30 MINUTE)");  
			if (count($ak)==0) {
				db_request("delete from leads_wtime where lid=$lid");	#       
				db_request("update leads SET cid=$cid where id=$lid");  #     
			} else {
				#   
				$un = db_array("select login l from users where id={$wld['cid']}"); 
				$cblk.="Leads #$lid is already being processed by {$un[0]['l']} {$ak[0]['sm']} ago. ";	#        30  
			}
		}

		if ($cblk=='') {	#         
			db_request("update leads SET cid=0 where cid=$cid and id!=$lid");  				#      
			db_request("update leads set cid=$cid where id=$lid and cid in (0,$cid)");   	#           
			$outm['eu']=$hn.'/a/workleads/?id='.$lid;  										#    
			$outm["ef"]="showalert";
			$outm["aah"]=3; $asts=0;
			$cblk="Open worklead process. Waiting 2 sec... ";
		} else {	#      >   
			$asts=3;
		}
		
		$outm["ef"]="showalert"; $outm["asts"]=$asts;  $outm["atxt"]=$cblk;
	}
	
	mysql_close(); die(json_encode($outm));
}
