<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 	
require_once($dr.'/a/access.php');
$page['title'] = 'Scan check';
$page['desc'] = 'Check customers scans';

#$vlim=['etd'=>' if (strlen($tdv)>60) $tdf=substr($tdv, 0, 60)." ...";'];	#    10 
/*
:        ft(1,2)   .
1)      (   ).      
2)     NRC  (          )

     .     .
     .
*/

$mel='mt';
$dpel=[							 
	$mel=>['gf'=>'sastabV1',				
		'title'=>'<h2>Scan checking</h2>',
		'tl'=>	['users_files'=>['c'=>[
				'empty'=>['p'=>'empty','q'=>' '],
				'fp'=>['p'=>'timg','etd'=>'$tdf=cuScanV1(["h"=>$rv["ufh"],"v"=>$tdv,"s"=>200,"m"=>1]);'],
				]],],
		'dl'=>10, #6x4
		'qt'=>'SELECT {(select)} FROM {(from)} WHERE 1=1 {(where)} {(limit)}', 		
		'setl'=>[
			'Document check'=>['(w|n)'=>'empty|wt1|e'],
			'NRC check'=>['(w|n)'=>'empty|wt2|e'],
			],
		'wtmpl'=>[	#    
				'wt1'=>['t'=>'uf.ac=0 and uf.ft in (1,2) order by uf.dv DESC '],	#  nrc +       
				'wt2'=>['t'=>'uf.ac=1 and uf.ft=2 order by uf.dv DESC '],			#  nrc    
				],
		'setd'=>'Document check',
		'not show all'=>1,	#    -  .    
		'filter'=>[	# 
			'addhtml'=>'',  
			],		
		'mf'=>['efunc'=>'CheckScansGallery'],
		'fs'=>['ufid','timg','ufi','ufi','ufh'],	   
	],
];
		

/*
    1   +       3      
*/
$page['js'][] = $hn.$selfp.'/m.js?ver='.$jsver;						#   js

#      -        
$pset=['only'=>'w','el'=>$mel]; if (isset($_POST[$mel.'(setd)'])) $pset['setd']=$_POST[$mel.'(setd)'];
	
if (isset($_POST['wtype']) && isset($dpel[$mel]['setl'][$_POST['wtype']])) {	#   1.          
	
	if ($_POST['wtype']=='Document check') {	#     ac3 ->       (/) -> ac3=>[ID1,ID2](   ac3)  ac3=[] (   ac3)
		$ac1=4; $ac2=1; $ac3=0;
	}
	if ($_POST['wtype']=='NRC check') {	#     ac2 ->         -> ac2=>[ID1,ID2](    )  ac2=[] (    )
		$ac1=3; $ac2=2; $ac3=1;
	}
	$idm=[]; if (isset($_POST['idl'])) foreach ($_POST['idl'] as $rid) if (is_numeric($rid)) $idm[]=intval($rid); 
	#print_r($idm); die('$ac1='.$ac1.'; $ac2='.$ac2.'; $ac3='.$ac3.';');
	if (count($idm)>0) db_request('update users_files set ac='.$ac1.' where id in ('.implode(',',$idm).')');
	
	db_request('update users_files set ac='.$ac2.' where iw='.$user['id'].' and ac='.$ac3.' ');	#       c ac=1 -> ac=2 -> ..    
}

require_once($dr.'/tool/sas/stage1_settings.php');  					#    (        )

#     mt
$func = $dpel[$mel]['gf']; 												#    
$w=$func(array_merge($dpel[$mel],$pset));								#     
				
db_request('update users_files uf set iw=0 where iw='.$user['id']);	#   
db_request('update users_files uf set iw='.$user['id'].' where 1=1 '.$w['where'].' '.$w['limit']);	#       

$dpel[$mel]['wtmpl']['wt1']['t']='iw='.$user['id'];						#    .          
$kolm=db_array($w['sql_kol']);	$kol=$kolm[0]['kol']; 
$dpel[$mel]['tkol']=$kol;												#   -   	

#$page['js_raw'].='sas_setl_'.$mel.'='.json_encode(array_keys($dpel[$mel]['setl'])).';';

$nextbut='<div class="form-group" style="padding: 5px;margin-bottom: 5px;margin-right: 7px;"><button type="button" class="btn btn-warning" onclick="nextch({el:\''.$mel.'\',wtype:\''.$w['setd'].'\'});">These are checked > Open next scans</button></div>';
$dpel[$mel]['mf']['inputsetd']=$w['setd'];

if ($kol>0) $dpel[$mel]['filter']['addhtml']=$nextbut;					#     -   >  

if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');					#    

require_once($dr.'/tool/sas/stage2_build_elements.php');				#        html   

function CheckScansGallery($mp){	#    	
	$tabf=$mp['tabf']; $tdms=$mp['tdms']; $tre=$mp['tre']; $bb=[]; 
	foreach ($tabf as $tr=>$kr) if (is_string($kr['timg']['v'])) $bb[]='
	<div '.$tre[$tr].' style="border-top: 1px double black;float:left;min-width:230px;min-height:255px;" '.$kr['timg']['e'].'>
		<div class="checkbox">
			<label><input rowid="'.$kr['ufid']['v'].'" type="checkbox" checked value="">'.$mp['inputsetd'].'</label>
		</div>
		'.$kr['timg']['v'].'
	</div>';
	$html='<div class="container"'.$tdms.'><div class="row">'.implode('',$bb).'</div></div>';	
	if (isset($mp['filter'])) $html='<div>'.$mp['filter']['v'].'</div>'.$html;
	return ['h'=>$html,'a'=>'']; # ,'a'=>$bl
}

/* --------------------------  ------------ */ ob_start(); ?>
	<div class="container">
		<?= $html[$mel] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';