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
                            <label class="control-label"><?= l('Please write here phone number of your Emergency Contact Person and any Family member or Close Relative') ?></label>
                            <p><?= l('We never tell nobody about your loans. Your privacy and information are in safe.') ?></p>
                        </div>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $this_wizard_name ?>" accept-charset="UTF-8" method="post">
						
						<?
						$ss_eladd='';   		// Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет						
						$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=1; 				// Вывод HTML элемента - Обязательное поле  
												
						if (!isset($lead['c']['1-1'])) {
							$ss_lname='Emergency Contact Person Name';
							$ss_phold=l($ss_lname);
							$ss_fname='Guarantor1Name';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; #if (isset($lead['c']['1-1'])) $ss_fval=$lead['c']['1-1']['cname'];
							$ss_tabi=1;
							require(MC_ROOT . '/templates/sections/frontend/step_string.php');
							
							$ss_lname=l('Emergency Contact Phone number');
							$ss_phold=0;
							$ss_fname='Guarantor1Phone';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; #if (isset($lead['c']['1-1'])) $ss_fval=$lead['c']['1-1']['cval'];
							$ss_tabi=2;
							require(MC_ROOT . '/templates/sections/frontend/step_phone.php'); 
						}
						
						if (!isset($lead['c']['2-1'])) {
							$ss_lname='Family Contact Person Name';
							$ss_phold=l($ss_lname);
							$ss_fname='Guarantor2Name';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; #if (isset($lead['c']['2-1'])) $ss_fval=$lead['c']['2-1']['cname'];
							$ss_tabi=3;
							require(MC_ROOT . '/templates/sections/frontend/step_string.php');

							$ss_lname=l('Family Contact Phone number');
							$ss_phold=0;
							$ss_fname='Guarantor2Phone';
							$ss_rname=$ss_fname.'_id'; 
							$ss_fval=''; #if (isset($lead['c']['2-1'])) $ss_fval=$lead['c']['2-1']['cval'];
							$ss_tabi=4;
							require(MC_ROOT . '/templates/sections/frontend/step_phone.php'); 
						}
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 						
						?>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>