<div class="wrapper">
    <div class="step">
		<? require_once(MC_ROOT . '/templates/sections/frontend/step_header.php'); ?>
        <div class="tabs-container wizard-container">
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="col-md-7 col-md-offset-5">
                        <h2 class="step__title step__title-left padding-top-none">
                            <?= l($ss_tahead) ?>
                        </h2>
                        <label class="control-label"><?= l('Please fill up the following fields. It is easy. We will appreciate if you fill in English. Fields indicated with * are mandatory.') ?></label>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $this_wizard_name ?>" accept-charset="UTF-8" method="post">
 						
						<?
						
						$ss_eladd='';   // Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_cladd='';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=0; 		// Вывод HTML элемента - Обязательное поле
						
						if (!isset($lead['c']['0-2'])) {
							$ss_lname='Your email';  // Email
							$ss_phold='yourmail@gmail.com';
							$ss_fname='email';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; #$ss_fval=$lead['c']['0-2'][0]['cval'];
							$ss_tabi=1;
							require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 
						}
						if (empty($lead['u']['city'])) {
							$ss_req=1; $ss_cladd='required ';  
							$ss_lname='City'; 
							$ss_phold=l($ss_lname);
							$ss_fname='City';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=$user['city'];
							$ss_tabi=2;
							require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 
						}
						/*
						$ss_lname='Township';
						$ss_phold=l($ss_lname);
						$ss_fname='township';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=3;
						require(MC_ROOT . '/templates/sections/frontend/step_string.php');
						*/
						
						if (empty($lead['u']['birthdate'])) require(MC_ROOT . '/templates/sections/frontend/app_elements/birthday.php'); 
						
						#print_r($stepm);  die('['.$tmpl.']');	
						$ss_eladd='';  						
						$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=1; 			// Вывод HTML элемента - Обязательное поле  
						
						$ss_tabi=7;
						
						$ss_oarr=$libs['users.gender'];
												
						$ss_lname=l('Gender');
						$ss_fname='Gender';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user['gender'];
						
						if ($lead['u']['gender']==2)  require(MC_ROOT . '/templates/sections/frontend/step_option.php');
						?>

						
						<?
						#$ss_lname='Your Viber Phone or any additional phone number?';	
						$ss_lname=l('Your Line or Whatsapp or Viber Phone number?');	
						$ss_phold=0;
						$ss_fname='SecondPhone';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=''; if (isset($lead['c']['0-4'])) $ss_fval=$lead['c']['0-1'][0]['cval'];	# && count($lead['c']['0-4'])>1
						$ss_tabi=9;
						$ss_req=0; $ss_cladd='';   // Не требуется
						if (!isset($lead['c']['0-4'])) require(MC_ROOT . '/templates/sections/frontend/step_phone.php');
						
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 
						?> 
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>