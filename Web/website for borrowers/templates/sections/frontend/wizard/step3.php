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
<!-- Please fill up the following fields. This is the most important step as we’ll transact the money to you from this information. Please choose carefully and take clear photos. -->
							<label class="control-label"><?= l('ALMOST DONE! Please choose the bank and write your bank account number. If you not yet have bank account , you can continue this application later! Hurry to take your money!') ?></label>
                        </div>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $this_wizard_name ?>" accept-charset="UTF-8" method="post" enctype="multipart/form-data">
						<?												
						$ss_req=1; 				// Вывод HTML элемента - Обязательное поле   $lead['l']['oacc'].
						
						if ($lead['l']['racc']==0 && $lead['l']['facc']=='')  {
							#
							$ss_oarr=$libs['bankt'][$countrym[$app['current_country']]['f']];
							#echo "[{$user['fil']}]";
							$ss_eladd='  ';
							$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
							$ss_tabi=1;

							#$ss_lname='Choose your bank here';
							$ss_lname='Choose your bank or payment system here';
							$ss_phold=l('Please select');
							$ss_fname='bank';
							$ss_rname=$ss_fname.'_id1'; 
							$ss_fval=$lead['l'][$ss_fname];

							require(MC_ROOT . '/templates/sections/frontend/step_option.php'); 
							
							$ss_eladd='  ';  // disabled="disabled" Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет											
							$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
							$ss_tabi=2;
							
							$ss_lname='Fill your bank account number here';
							$ss_phold='AAB 31224256789879654';
							$ss_fname='bank_account';
							$ss_rname=$ss_fname.'_id1'; 
							$ss_fval=$lead['l']['facc'];
							
							require(MC_ROOT . '/templates/sections/frontend/step_string.php');
							
						}			
						
						$ss_eladd='';   		// Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет						
						$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_tabi=3;
						
						#$ss_lname='NRC Number';
						$ss_lname='Your ID/Passport number';
						#$ss_phold='12/ThaKaKa(N)000000';
						$ss_phold=$countryid[$user['fil']]['dnrs'];
						$ss_fname='onrc';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];   #$lead['u'][$ss_fname];  
						
						if ($lead['u']['onrc'].$lead['u']['fnrc']=='') require(MC_ROOT . '/templates/sections/frontend/step_string.php');
						
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 
                        ?>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>