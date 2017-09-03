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
							$ss_eladd=' aria-invalid="false" role="moneyChoose" data-placeholder="false" ';  						
							$ss_cladd='';		// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
							$ss_req=0; 			// Вывод HTML элемента - Обязательное поле  
							
							$ss_tabi=1;
							
							$ss_oarr=$libs['leads.how'];
							
							$ss_lname='How do you want to get money?';
							$ss_phold=l('Please select');
							$ss_fname='how';	$how=$lead['l']['how'];
							$ss_rname='application_money_choose_id'; 	
							$ss_fval=$how;
							#die('$ss_fval='.$ss_fval);
							require(MC_ROOT . '/templates/sections/frontend/step_option.php');							
							$tcl=''; if ($how!=2) $tcl='hidden'; 
							echo '<div class="'.$tcl.'" data-id="2" role="toggleInput">';
								/*
								0=>'I don`t know',
								2=>'Bank account',
								3=>'Domestic remittance',
								4=>'Payment system',
								*/
								
							$ss_eladd=' aria-invalid="false" data-placeholder="false" ';
							$ss_tabi=2;
							
							#$ss_oarr=$libs['bank_id'];
							$ss_oarr=$libs['bankt'][$user['fil']];
							
							$ss_lname='Choose your bank here';
							$ss_phold=l('Please select');
							$ss_fname='bank';
							$ss_rname=$ss_fname.'_id1'; 
							$ss_fval=$lead['l'][$ss_fname];
							
							require(MC_ROOT . '/templates/sections/frontend/step_option.php'); 

							// Номер счета 
							
							$ss_eladd='  ';  // disabled="disabled" Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет											
							$ss_tabi=3;
							
							$ss_lname='Fill your bank account number here';
							$ss_phold='KBZ 0123456789879654';
							$ss_fname='bank_account';
							$ss_rname=$ss_fname.'_id1'; 
							$ss_fval=$lead['l']['oacc'];
							
							require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 
							$tcl=''; if ($how!=3) $tcl='hidden'; 
							echo '</div><div class="'.$tcl.'" data-id="3" role="toggleInput">';

							$ss_tabi=4;
							$ss_lname='Your bank branch name or address here';
							$ss_phold='KBZ bank, Merchant road';
							$ss_fname='domestic_remittance';
							$ss_rname=$ss_fname.'_id2'; 
							$ss_fval=$lead['l']['oacc'];

							require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 

							$tcl=''; if ($how!=4) $tcl='hidden'; 
							echo '</div><div class="'.$tcl.'" data-id="4" role="toggleInput">';

							$ss_tabi=5;
							$ss_lname='Write here your OK$ account number';
							$ss_phold='OK$ 09123456789';
							$ss_fname='payment_system';
							$ss_rname=$ss_fname.'_id3'; 
							$ss_fval=$lead['l']['oacc'];

							require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 

							echo '</div>';
							
						$ss_eladd='';   		// Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет						
						$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=1; 				// Вывод HTML элемента - Обязательное поле  
						
						$ss_lname='NRC Number';
						#$ss_phold='12/ThaKaKa(N)000000';
						$ss_phold=$countryid[$user['fil']]['dnrs'];
						$ss_fname='onrc';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=6;
						require(MC_ROOT . '/templates/sections/frontend/step_string.php');
						
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 
                        ?>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>