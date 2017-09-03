<div class="wrapper">
    <div class="step">
		<? require_once(MC_ROOT . '/templates/sections/frontend/step_header.php'); ?>
        <div class="tabs-container wizard-container">
            <div class="tab-content">
                <div class="tab-pane active">
					<div class="row">
                        <div class="col-md-7 col-md-offset-5">
                            <h2 class="step__title step__title-left padding-top-none">
                            <?= l('Dear') ?> <?= $GLOBALS['ss_cname'] ?>. <?= l('Take now your money') ?> - <?= $GLOBALS['ss_amount'] ?>&nbsp;mmk!
							</h2>
							<label class="control-label"><?= l('You are our trusted customer!') ?></label>
							<label class="control-label"><?= l('That means you can get loan in only 2 minutes!') ?></label>
                            <label class="control-label"><?= l('Please provide two more emergency contact person. We will immediately send you money.') ?></label>
							<label class="control-label"><?= l('It is a pleasure to work with you!') ?></label>
						</div>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step" autocomplete="off" novalidate="novalidate" action="<?= $GLOBALS['ss_fhref'] ?>" accept-charset="UTF-8" method="post">
						<? 		
						/*

Dear Phyu Sin Thant! Take now your money - (сколько чел выбрал в градуснике) 50.000mmk!
You are our trusted customer! 
That means you can get loan in only 2 minutes! 

Please provide two more emergency contact person. We will immediately send you money.

It a pleasure to work with you!
						
						*/
						
						
						$ss_eladd='';   		// Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет						
						$ss_cladd='required ';	// Дополнительная вставка в класс   
						$ss_req=1; 				// Вывод HTML элемента - Обязательное поле
						
						$ss_lname='Emergency person name 1';
						$ss_phold=l($ss_lname);
						$ss_fname='Guarantor3Name';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=1;
						require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 
				 
						$ss_lname=l('Emergency person phone number 1');
						$ss_fname='Guarantor3Phone';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=2;
						require(MC_ROOT . '/templates/sections/frontend/step_phone.php'); 
				 
						$ss_lname='Emergency person name 2';
						$ss_phold=l($ss_lname);
						$ss_fname='Guarantor4Name';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=3;
						require(MC_ROOT . '/templates/sections/frontend/step_string.php'); 
				 
						$ss_lname=l('Emergency person phone number 2');
						$ss_fname='Guarantor4Phone';
						$ss_rname=$ss_fname.'_id'; 
						$ss_fval=$user[$ss_fname];
						$ss_tabi=4;
						require(MC_ROOT . '/templates/sections/frontend/step_phone.php'); 
						
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 
						?>                   
					</form>
                </div>
            </div>
        </div>
    </div>
</div>