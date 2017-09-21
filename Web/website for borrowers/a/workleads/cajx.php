<?php

if (isset($_GET['cajx'])) {
	
	$nplim=4;	#        
	
	$outm=[];  $cajx=$_GET['cajx']; $cid=$user['id'];
	
	if (count($wl)==0) { $outm['eu']=$hn.'/a/workleads'; die2($outm); }	#       
	
	$uid=$wl[0]['uid']; 	# id 
	$clid=$wl[0]['id'];		# current lid by database
	
	#  
	if ($cajx=='next') {
		$outm['eu']=$hn.'/a/workleads';  #    
		$note=mysql_real_escape_string($_POST['note']);	#    
		$lid=intval($_POST['lid']);						#    
		$cst=intval($_POST['cst']);						#   
		$nct=intval($_POST['nct']);						#     
			
		if ($clid!=$lid) die2($outm);	#             
			
		$lupq=""; $quup=[];
		$nctw=['null',
		'date_add(now(), interval 30 MINUTE)',
		'date_add(now(), interval 1 HOUR)',
		'date_add(now(), interval 2 HOUR)',
		'date_add(now(), interval 3 HOUR)',
		'date_add(now(), interval 1 DAY)'];

		if ($cst==1) {	# $cst=1 => 'NO PICK UP' +        ->      .
			$npup = db_array('select count(*) as kol from users_calls where did='.$lid.' and dt=1 and cst=1');	
			if (count($npup)>0 && $npup[0]['kol']<$nplim) {
				$nct=5;		#    +1 DAY
			} else {
				$nct=0; 	#    null -  
				$lupq.=",note=concat(note,'\\nustomer didn`t pick up phone $nplim times.')";  # \\n
				$cst=7;		#          - 
			}
		}
		
		$a_rst=0; if ($nct>0) {	#    
			$lupq.=",a_rst=1";	# leads.a_rst 		
			$a_rst=1;			# users_calls.a_rst 
		}
		
		#$lcst_cst
		$lstm=[
			1=>-1,# 7 'No pick up',		
			2=>26,#'Does not want to borrow',
			3=>19,#'Need Bank Acc or NRC',	#      >      
			4=>24,#'Pipeline',
			5=>26,#'Bad guy',
			6=>-1,# 8 'Next Recall',
			7=>26,# Denied due No pick up max times
		]; 
		/*
		'leads.st'=>[		# $libs['loans.st']
			0=>'New',		#        
			1=>'Must fix', 	#   
			2=>'ompleted',	#  			
			#...
			4=>'Pipe',		#      >  ,  ,  .	
			5=>'Old Lead',	#   ,   
			6=>'Denied',	#   
			7=>'No pick up',	#    
			8=>'Wait recall',	#   
			9=>'Need Bank',	#    NRC
			],
		*/
		# =================   ==================  
		#   
		$nlst='st'; if ($lstm[$cst]!=-1) $nlst=$lstm[$cst]; #             
		db_request("update leads set cid=0,cst=$cst,st=$nlst{$lupq} where id=$lid and cid=$cid");
		#die("update leads set cid=0,cst=$cst,st=$nlst where id=$lid and cid=$cid");
		
		#       (          )
		$tsm = db_array("select id,DATE_FORMAT(ts,'%Y.%m.%d %H:%i:%s') as fts from leads_wtime where lid=$lid and cid=$cid limit 1"); # order by ts desc (     )
		#             
		$ws='null'; if (count($tsm)>0) $ws="'".$tsm[0]['fts']."'";
		#     -    ->     
		db_request("insert into users_calls (uid,dt,did,cst,note,recall,a_rst,cid,ws,dv) VALUES ($uid,1,$lid,$cst,'$note',{$nctw[$nct]},$a_rst,$cid,$ws,now())");
		
		if ($cst==4) {	# =================   pipeline =================
			#     pipeline
			db_request("insert into loans (fil,uid,lid,st,racc,UsrAmount,UsrRAmount,UsrRTerm,UsrTerm,UsrPercentPerDay,cid,dv) 
					                select fil,uid,id, 1, 0,   0,        ramount,   rdays,   0,      ppd,$cid,now() FROM leads WHERE id=$lid");
		}
		
		if ($cst==5) 			$quup[]='st=1';		#   badgay 
		if (intval($nlst)>24) 	$quup[]='a_lid=0';	#     users.a_lid=0
		if (count($quup)>0) 	db_request("update users set ".implode(',',$quup)." where id=$uid");
		
	}
	
	#        
	if (isset($_GET['id']) && intval($_GET['id'])>0) {
		if ($outm['eu']==$hn.'/a/workleads') $outm['eu']=$hn.'/a/leadview/?id='.intval($_GET['id']);  #    
	}	
		
	die2($outm);
}