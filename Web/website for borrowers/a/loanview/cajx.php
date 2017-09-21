<?php
require_once($dr.'/a/process/percent.php');
if (!isset($libs)) require_once($dr.'/tool/sas/constants.php');					#  

if (isset($_GET['cajx'])) {
	
	$outm=[];  $cid=$user['id']; 
	
	$cajx=$_GET['cajx']; 	if (isset($_GET['mode'])) $mode=$_GET['mode']; 
	$rld=0;
	if ($cajx=='movemoney' && isset($mode) && in_array($mode,[0,1]) && $sendm==1) {
		# Loan #1104   Ko Myo Myint Htwe   09440658408  
		
		$rld=1;
		$mm=[
			'note'	=>	['snote',	'rnote'],
			'opdate'=>	['sopdate',	'ropdate'],
			'ofdate'=>	['sofdate',	'rofdate'],
			'oacc'	=>	['soacc',	'roacc'],
			'amount'=>	['samount',	'ramount'],
		];
		#print_r($_POST); die();
		$cd=0; $cn=3;
		$note	=mysql_real_escape_string($_POST[$mm['note'][$mode]]);			#    
		$oacc	=$_POST[$mm['oacc'][$mode]];									#   
		$opdate	=$_POST[$mm['opdate'][$mode]];									#    
		$ofdate	=$_POST[$mm['ofdate'][$mode]];									#     
		$amount	=floatval($_POST[$mm['amount'][$mode]]);						#  
		
		$cashman=0; if (isset($_POST['cashman'])) $cashman=intval($_POST['cashman']);	#    
		
		if (dateChek($opdate)) $cd++;											#  
		if (in_array($oacc,$libs['UsrOurWallet'])) $cd++;						#  
		if ($amount>0) $cd++;													#  
		
		#   
		$opdate	=mysql_real_escape_string($opdate);		
		$oacc	=mysql_real_escape_string($oacc);			
		
		if ($mode==0) {	#   
			$movesql="insert into money (uid,loan,operday,offday,amount,oacc,uacc,cashier,note,dv)  
			select uid,$lo,'$opdate','$ofdate',$amount,'$oacc',bacc,$cid,'$note',now() FROM loans
			where id=$lo";			
		}
		
		if ($mode==1) {	#    
			$amount=$amount*-1;
			$movesql="insert into money (uid,loan,operday,offday,amount,oacc,cashier,cashman,note,dv)  
			select uid,$lo,'$opdate','$ofdate',$amount,'$oacc',$cid,$cashman,'$note',now() FROM loans
			where id=$lo";			
		}
		
		#  
		db_request($movesql);
	
		#        mmove
		$o=calcDebt(['id'=>$lo,'m'=>3]);
		
		#          
		#        +    ->    
		#    
	}
	
	if ($cajx=='recalc') {
		$rld=1;  		#    
		$o=calcDebt(['id'=>$lo,'r'=>1,'m'=>2]);			#     	
	}
	
	$stm=[
	'deny'	=>[18,'\nDeny for loan: '],
	'wod'	=>[16,'\nWritten-off debt: '],
	];
	
	if (isset($stm[$cajx])) {
		$rld=1; $permit=1;  		#    
	
		if (isset($_POST['dnote'])) $dnote='concat(UsrNotes, "'.$stm[$cajx][1].mysql_real_escape_string($_POST['dnote']).'")';
	
		$lup=['st'=>$stm[$cajx][0]]; if (isset($dnote))  $lup['UsrNotes']=$dnote;
					
		if ($cajx=='wod' && $user['role']!='super') $permit=0;  #die("Only admin can do this.");
						
		if ($permit==1) fixLoans(['lup'=>$lup,'id'=>$lo]);
	}

	if ($rld==1) {
		$outm['eu']=$hn.'/a/loanview/?id='.$lo;  				#    
	}

	mysql_close(); die(json_encode($outm));
}

function dateChek($data){
  $regularka = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
  if ( preg_match($regularka, $data, $razdeli) ) {
    if 	( checkdate($razdeli[2],$razdeli[3],$razdeli[1]) ) return true;	/*   - MM, DD, YYYY: */    
  } 
  return false;   
}