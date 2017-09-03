<div class="wrapper">
    <div class="step">
		<? require_once(MC_ROOT . '/templates/sections/frontend/step_header.php'); ?>
        <div class="tabs-container wizard-container">
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="row">
                        <div class="col-md-7 col-md-offset-5">
                            <h2 class="step__title step__title-left padding-top-none">
								<?= l($ss_tahead) ?>
							</h2>
                            <label class="control-label"><?= l('Please fill up the following fields. We need very detailed information to proceed your loan. We won’t contact any of your contacts unless we have to. Fields indicated with * are mandatory.') ?></label>
                        </div>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $this_wizard_name ?>" accept-charset="UTF-8" method="post">						
						<?												
						#$ccid=$countrym[$app['current_country']]['f'];
						
						$ss_eladd=' role="socialStatus" ';  						
						$ss_cladd='';		// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=0; 			// Вывод HTML элемента - Обязательное поле  
						
						$ss_tabi=1;
						
						$ss_oarr=$libs['users.social'];
						
						$ss_lname='Social Status';
						$ss_phold=l('Please select');
						$ss_fname='social'; 
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						
						if ($lead['u']['social']==0)  require(MC_ROOT . '/templates/sections/frontend/step_option.php');							

						$ss_eladd='';   		// Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет						
						$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=1; 				// Вывод HTML элемента - Обязательное поле  
						
						$ss_lname='Company Name';
						$ss_phold='Micromoney';
						$ss_fname='cname';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=2;
						if (empty($lead['u']['cname'])) require(MC_ROOT . '/templates/sections/frontend/step_string.php');

						$ss_lname=l('Company`s Phone Number');
						$ss_phold='';
						$ss_fname='CompanyPhone'; 
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user['cphone'];
						$ss_tabi=3;
						if (empty($lead['u']['cphone'])) require(MC_ROOT . '/templates/sections/frontend/step_phone.php'); 

						$ss_lname=l('Monthly Gross Income').' ('.$countrym[$app['current_country']]['cur'].')';
						#$ss_phold='Kyat 300,000';
						$ss_phold=$countrym[$app['current_country']]['ds'];
						#print_r($countrym[$app['current_country']]);
						$ss_fname='SalaryAmount'; 
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user['salary'];
						$ss_tabi=4;
						if ($lead['u']['salary']==0) require(MC_ROOT . '/templates/sections/frontend/step_phone.php');
						
						$ss_req=0; $ss_cladd='';
						
						if (!isset($lead['c']['5-1'])) {
							$ss_lname=l('Your coworker phone number');
							$ss_phold='';
							$ss_fname='CoworkerPhone';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; if (isset($lead['c']['5-1'])) $ss_fval=$lead['c']['5-1']['cval'];
							$ss_tabi=5;
							require(MC_ROOT . '/templates/sections/frontend/step_phone.php');
							
							$ss_lname='coworker name';
							$ss_phold=l($ss_lname);
							$ss_fname='CoworkerName';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; if (isset($lead['c']['5-1'])) $ss_fval=$lead['c']['5-1']['cname'];
							$ss_tabi=6;
							require(MC_ROOT . '/templates/sections/frontend/step_string.php');
						}
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 
						?> 
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>