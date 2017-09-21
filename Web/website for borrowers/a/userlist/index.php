<?php #$PlainText=true;
if (isset($_GET['sas_init'])) $sas=$_GET['sas_init']; 
$backend_environment = TRUE; $sas_work=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
require_once($dr.'/a/access.php');
$page['title'] = 'Customers list'; $page['desc'] = 'Customers list (users exclude admins)';

#       
$dpel=[							# : :   -        
	'md'=>['gf'=>'sastabV1',				# :    
		'tl'=>	[	
					'users'=>[
						#        (      )
						'c'=>['act'=>['fn'=>'Actions','p'=>'action','q'=>'(select 1)'],]
					],
				],
		# te ->  eval , fe ->  
		'qt'=>"SELECT {(select)} FROM users u WHERE u.role='' {(where)} {(group)} {(having)} {(order)} {(limit)}",
		
		#   (select fields)
		'dl'=>10,
		'setl'=>[
			'For Last Mounth'	=>['(w|n)'=>'udv|wt1|2','(wt|wt1)'=>'day|-30'],
			'For Last Week'		=>['(w|n)'=>'udv|wt1|2','(wt|wt1)'=>'day|-7'],
			'For Last Day'		=>['(w|n)'=>'udv|wt1|2','(wt|wt1)'=>'day|-1'],
			],
			
		'wtmpl'=>[
				'wt1'=>['t'=>'date_add(now(), interval {(day)} day)','p'=>['day'=>'i']],
				],	
		'filter'=>[
				'fw'=>['uid','ufil','uallseek','umad','ua_od','ua_cd','udv'], # ,'ulogin','uname','unrc','uemail'
				],
		'paginator'=>'page',  # scroll  page  ,'a.LoanDays','a.create_at','a.CrmStatus','a.cst','a.HowDoYouWantToGetMoney','a.RequestAmount'
		
		'fs'=>['uid'=>[],'ufil'=>['h'=>1],'utitle'=>[],'udv'=>[],'ua_od'=>[],'ua_cd'=>[],'action'=>[]],	
		'mf'=>[
			'sort'=>['uid','udv'],  // 'ulogin'=>[1,' ASC'],' DESC'
			'etd_action'=>'$tdf="<a title=\'Edit customer information\' class=\'btn btn-sm btn-info\' href=\'/a/userview/?id=".$rv["uid"]."\'><i class=\'fa fa-edit\'> Edit</i></a>";',	
		]	
	],
];


$page['js'][] = $hn.$selfc.'m.js?ver='.$jsver;						#   js

require_once($dr.'/tool/sas/stage1_settings.php');  				#    (        )

require_once('cajx.php');											#    

if (isset($sas)) require_once($dr.'/tool/sas/sas_init.php');		#    
require_once($dr.'/tool/sas/stage2_build_elements.php');			#        html   
$cstl='style="padding: 5px;margin-bottom: 5px;margin-right: 7px;"';

$el='md';
/* --------------------------  ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">

		<div class="form-inline">  
			<div class="form-group" <?= $cstl ?>><h2>Customers list</h2></div>
			<div class="form-group" <?= $cstl ?>>
				<input tabindex="1" placeholder="&#xF002;" style="font-family:Arial, FontAwesome" type="text" class="form-control" id="<?= $el ?>_aios" >
			</div>
			<div class="form-group" <?= $cstl ?>>IN</div>
			<? if (isset($aioml)) { ?>
			<select class="form-control" name="szona" id="<?= $el ?>_aiom" <?= $cstl ?>>
				<? foreach ($aioml as $k=>$v) { ?>
					<option value="<?= $k ?>" <?= ($k == $aiom) ? 'selected' : '' ?>><?= $k ?></option> 
				<? } ?>
			</select>
			<? } ?>
			<div class="form-group" <?= $cstl ?>>
				<button type="button" class="btn btn-info" onclick="seekaio({el:'<?= $el ?>'});" name="button">Seek</button>
			</div>
		</div>
		
		<?= $html[$el] ?>
	</div>
<?php require PHIX_CORE . '/render_view.php';