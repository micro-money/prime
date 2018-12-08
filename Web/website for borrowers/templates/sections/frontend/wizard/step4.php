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
<!-- Please choose carefully and take clear photos. -->
                            <label class="control-label"><?= l('CONGRATULATIONS! We are ready to send you money! Please take clear photos of your documents. If you not yet have bank account , you can continue this application later!') ?></label>
                        </div>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $this_wizard_name ?>" accept-charset="UTF-8" method="post" enctype="multipart/form-data">
						<?
						$ss_eladd='';   // Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						// Вывод HTML элемента - Обязательное поле
						// Please make a photo of your bank account number (safe account or check book)
						if (!isset($lead['s'][1])) {
							$ss_lname='Photo of your bank account number.';
							$ss_fname='photo1';
							$ss_tabi=1; $ss_cladd=''; $ss_req=0;
							require(MC_ROOT . '/templates/sections/frontend/step_foto.php');
						
						?>
						<div class="row">	
							<a href="#0_2" class="col-xs-12 col-md-5 collapsed" style="text-align: right;font-size:30px;" data-toggle="collapse"><label class="control-label "><?= l('Examples') ?></label></a> 
						</div>
						<div class="row">
							<div id="0_2" class="col-xs-12 col-md-12 collapse">
								<div class="row">	
									<div class="col-md-4">
										<img width="150px" height="200px" alt="p1" src="/images/docs/bank.jpg">
									</div>	
									<div class="col-md-4">
										<img width="150px" height="200px" alt="p1" src="/images/docs/card.jpg">
									</div>	
									<div class="col-md-4">
										<img width="150px" height="200px" alt="p1" src="/images/docs/mob.jpg">
									</div>								
								</div>
							</div>
						</div>
						<?
						// Please make a photo of your NRC
						}
						if (!isset($lead['s'][2])) {
							#$ss_lname='Photo of your NRC';
							$ss_lname='Photo of your ID/Passport';
							
							$ss_fname='photo2';
							$ss_tabi=2; $ss_cladd='required '; $ss_req=1; 
							require(MC_ROOT . '/templates/sections/frontend/step_foto.php');		
						?>
							
						<p>	<?= l('Only NRC card. Applications with selfies will be denied.	See example below') ?></p>
						<div class="row">	
						<? 	$phar=$countrym[$app['current_country']]['idf'];
							if (!is_array($phar)) $phar=[$phar];
							$phkol=count($phar); if ($phkol>3) $phkol=3;
							$phmdm=[0,12,6,4];
							for ($i=0; $i<$phkol; $i++) {
						?>
							<div class="col-md-<?= $phmdm[$phkol] ?>">
								<img width="240px" height="150px" alt="p1" src="<?= $phar[$i] ?>">
							</div>															
						<? } ?>
						</div>
						<? } ?>
						<div class="form-group required form-group-lg boolean optional application_privacy_policy_acceptance">
                            <div class="col-xs-12 col-md-7 col-md-offset-5">
                                <div class="checkbox">
                                    <input value="0" type="hidden" name="application[privacy_policy_acceptance]">
                                    <label class="boolean required checkbox-pretty checkbox" for="application_privacy_policy_acceptance">
                                        <input class="boolean required" type="checkbox" value="1" checked="checked" name="application[privacy_policy_acceptance]" id="application_privacy_policy_acceptance">
                                        <?= l('AUTHORIZATION') ?><br>
                                        <?= l('I have read and accepted') ?> <a target="_blank" href="/terms-and-conditions"><?= l('Terms &amp; Conditions') ?></a>, <a target="_blank" href="https://drive.google.com/open?id=0BwjCTKtgJMwkWk0wSW5sZC0wT2M"><?= l('Loan agreement') ?></a>, <a target="_blank" href="https://drive.google.com/open?id=0BwjCTKtgJMwkYXdWd09MQ1dSckE"><?= l('Service Contract') ?></a>, <a href="/privacypolicy"><?= l('Privacy Policy') ?></a>.  
									</label>
                                </div>
                            </div>
                        </div>					
						<? require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); ?>											
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>