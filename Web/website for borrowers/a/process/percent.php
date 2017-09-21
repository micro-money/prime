<?php
#  
#       

function calcDebt($mp){
	$id=$mp['id'];  

	$r=0; if (isset($mp['r'])) $r=1;
	$m=1; if (isset($mp['m'])) $m=$mp['m'];
	
	if (isset($mp['lrec'])) {
		$lrec=$mp['lrec'];
	} else { 
		$lrec = db_array("select 
								lid,uid,
								UsrAmountToPaid,
								UsrPercentPerDay,
								st,
								if(UsrTerm=0,UsrRTerm,UsrTerm) UsrTerm,
								UsrStopPercentDate spd,
								(datediff(UsrStopPercentDate,now())*-1) spda 
								from loans where id=$id");
	}
	
	$lr=$lrec[0];
	#                      
	$dterm=$lr['UsrTerm'];							#   
	$lst=$lr['st'];								#    
	#     
	if (isset($lr['spd']) && $lr['spd']!='0000-00-00') $spd=$lr['spd'];	
	#      
	#if (isset($lr['spd']) && $lr['spd']!='0000-00-00') $spd=$lr['spd'];	
	
	#      
	$spda=0; if (isset($lr['spda'])) $spda=$lr['spda'];	
	
	$dayper=$lr['UsrPercentPerDay'];		#   
	$dbody=0; 									#  
	$per=0;										# 
	$lterm=0;									#  
								
	$fsend=-1;									#      
	$frecive=-1;								#       
	$lprolong=-1;								#        =0
	
	$ph=[];

	#   
	if (isset($mp['pays'])) $pays=$mp['pays'];
	else $pays = db_array("select id,ifnull(offday,operday) od,(sum(amount)-sum(charge)) amount,(datediff(ifnull(offday,operday),now())*-1) nterm from money where loan=$id group by od order by operday asc");
		
	foreach ($pays as $k=>$v) {
		
		#                
		
		$amount	=$v['amount'];					#  
		$nterm	=$v['nterm'];					#      ()
		
		#         >       
		if ($nterm<0) { fixLoans(['lup'=>['st'=>29],'id'=>$id]); return $o;}
		
		#    
		if ($amount>0) 	{
			if ($fsend==-1) 	$fsend=$nterm;
			if (!isset($fmsa)) 	$fmsa=$amount;			#    
			if (!isset($fmsd)) 	$fmsd=$v['od'];	#    
		}
		
		#     
		if ($amount<0 && $frecive==-1) 	$frecive=$nterm;
		
		#     
		if ($lterm>0) {
		
			#          ->       
			if ($nterm<$spda) $nterm=$spda;
			
			$cterm=$lterm-$nterm;				# 2     
			
			$cper=(1+(($cterm*$dayper)-100)/100);	#    
			
			#          .       . 		
			$pper=0; if ($dbody>0) $pper=$dbody*$cper;						
								
												#      .       +  
			$tper=$pper+$per;					#     
			
			if ($amount<0) {					#     ( )					
				$fam=-1*$amount;
				if ($fam>$tper) {				#          >  =0 ,    
					$dbody=$dbody+$tper-$fam;
					$per=0;
					$lprolong=$nterm;			#        
				} else {						#       >    ,  
					$per=$tper-$fam;
				}
			} else {							#     ( ).  		
				$dbody+=$amount;				#    .
				$per=$tper;					    #       
			}
			
		} else {
			#  
			$dbody+=$amount;	
		}
		
		$lterm=$nterm;			
		$rez[]=$v['od'].' turn='.$nterm.' debt='.$dbody.' percent='.$per.' amount='.$amount;
		
		$curr_ps=0; if (isset($tper)) $curr_ps=round($tper,2);
		
		$ph[]=['c'=>$v['od'],'a'=>$amount,'t'=>$v['nterm'],'d'=>round($dbody,2),'pf'=>round($per,2),'ps'=>$curr_ps];		
		
	}

	#            .      .
	if ($dbody>0) {
		$cper=(1+(($nterm*$dayper)/100));	#    
		$pper=$dbody*$cper-$dbody;
		$per+=$pper;	
	}

	/*
	      .
	0 - Pipe 				-     .            .      .
	1 - Denied 				-          Pipe.     denied  .       .
	
	2 - Money has been sent -  .        Pipe       .
						         .
	
		     :
	
	3 - Prolongation		- .                           .
					         : 
					
					 : Money has been sent,Overdue XXX,Collection XXX,Loan repaid (Closed won)
	
	4 - Overdue 15 			-   15 .                       15 .
	5 - Overdue 30 			-   30 .     >15   30 .
	
	6 - Collection 31-60 	-   31  60 
	7 - Collection 61-90 	-   61  90 
	8 - Collection 91+ 		-   90 
	
	9 - Loan repaid (Closed won)	-             .
	
	0=>'Pipe',						#    ,    .
	1=>'Denied',					#   ,    .
	2=>'Money has been sent',		#   ,     
	3=>'Prolongation',				#   ,               
	4=>'Overdue 15',				#   1  15 
	5=>'Overdue 30',				#   16  30 
	6=>'Collection 31-60',			#   31  60 
	7=>'Collection 61-90',			#   61  90 
	8=>'Collection 91+',			#   91 
	9=>'Loan repaid (Closed won)',	#     
	
	21=>'Payments before moneysend',		#     
	22=>'Payments without moneysend',		#    ,   
	23=>'Work status without moneymove',	#          
	24=>'Loan close without minpayment',	#         
	
	*/
	
	$nst=-1;	#   
	
	# $fsend=-1;						 	     
	# $frecive=-1;							      
	# $lprolong=-1;							       =0
	
	#   
	$prterm=$dterm;
	$prterm=14;

	#     -            ( ) 
	$wterm=0;								#      
	$overturn=0;							#    
	if ($dbody>1) {
		if ($lprolong!=-1) {
			$wterm=$lprolong;				#            
											#         $prterm
			if ($wterm>$prterm) $overturn=$wterm-$prterm;
		} else {
			if ($fsend!=-1) {
				$wterm=$fsend;				#          
											#            
				if ($wterm>$dterm) $overturn=$wterm-$dterm;
			}
		}
	}
	
	#               
	if ($frecive==-1 && $fsend==-1 && $lst<15 && $lst>1) $nst=27;

	if ($frecive>$fsend) {						#         
		#25=>'Payments before moneysend',		#    
		#26=>'Payments without moneysend',		#    ,  
		$nst=25; if ($fsend==-1) $nst=26;	
	}
	
	#      pipe,         
	#     !in_array($lst,[])
	if ($dbody<1 && $fsend==-1 && $frecive==-1) $nst=1;

	if ($nst==-1 && $fsend>-1) {				#       >    
		#die("[$lst|$dbody|$dterm|$fsend]");
		# 0->2 'Pipe'->'Money has been sent' 
		if ($dbody>1 && $dterm>=$fsend) {
			#    1->Pipe        . 
			#                .
			$nst=2;
		}
		
		#       'Prolongation' 
		#                   
		if ($lprolong!=-1 && $lprolong<$fsend && $lprolong<=$prterm) {
			#   
			#         
			#            
			$nst=3;	
		}
		#die("$lprolong|$fsend|$prterm");
		#        -  
		if (intval($dbody)>0 && $overturn>0) {
			#           -> 				
			$nst=4;						#   1  15 
			if ($overturn>15) $nst=5;	#   16  30 
			if ($overturn>30) $nst=6;	#   31  60 
			if ($overturn>60) $nst=7;	#   61  90 
			if ($overturn>90) $nst=8;	#   91 
		}
		
		#         -    
		if (intval($dbody)<1 && $frecive!=-1 && $fsend>$frecive) {
			#             
			$nst=19;	
		}
		
	}
	$lt=0; if (isset($v)) $lt=$v['nterm'];
	$o=[
		'db'	=>round($dbody,2),		#    (     )
		'fp'	=>round($per,2),		#  
		
		'ot'	=>$overturn,			#   
			
		'lt'	=>$lt,					#      
		'wt'	=>$wterm,				#             
		'fs'	=>$fsend,				#       
		'fr'	=>$frecive,				#        
		'lp'	=>$lprolong,			#     
		
		'c'		=>$ph,					#  
	];
	
	if (isset($fmsa)) 	$o['fmsa']=$fmsa;	#    
	if (isset($fmsd)) 	$o['fmsd']=$fmsd;	#    
	if (isset($spd)) 	$o['spd' ]=$spd;	#      >    
	
	if ($lst!=$nst) {
		$m=4;	#         >    
		
		#       Pipe   >      
		if ($lst<2 && $nst>1 && isset($lr['uid']) && isset($lr['lid'])) {
			db_request("update users set a_lid=0 where id={$lr['uid']} and a_lid={$lr['lid']}");
		}	
	}
	
	if ($m>1 || $r==1) {
		#       
		#         >          
		db_request("insert into loans_sthist (loan,ost,nst,note,m,dv) VALUES ($id,$lst,$nst,'".json_encode($o)."',$m,now())");	
	}
	
	#       
	$lup=['st'=>$nst,'a_dbody'=>$dbody,'a_dperc'=>$per,'a_fmsa'=>'0','a_ot'=>$o['ot'],'a_fmsd'=>'null','a_rdate'=>'now()'];
	
	if (isset($fmsa)) 	$lup['a_fmsa']=$fmsa;			#    
	if (isset($fmsd)) 	$lup['a_fmsd']="'$fmsd'";		#    	
		
	#die('Sorry. Current Loan Status is Not Re Calculation. Pls ask to SuperAdmin Change Status Manualy.');
	#      15 >   >   
	if ($lst<15) fixLoans(['lup'=>$lup,'id'=>$id]);

	return $o;
	
	#echo 'FINALY: [deal: '.$id.' ] Debt body:'.round($dbody,2).' Percent till today:'.round($per,2).' Total debt:'.round($dbody+$per,2).'<br>';
}



#print_r($rez);
#echo 'FINALY: Debt body:'.$dbody.' Percent till today:'.$per.' Total debt:'.round($dbody+$per,2);

