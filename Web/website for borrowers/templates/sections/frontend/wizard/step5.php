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
                            <label class="control-label"><?= l('LAST STEP! To get a loan you need to install a Android Application!') ?></label>
                        </div>
                    </div>
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $this_wizard_name ?>" accept-charset="UTF-8" method="post" enctype="multipart/form-data">
							
						<div class="form-group form-group-lg select" aria-required="true">
							<label class="select col-xs-12 col-md-5 control-label" for="app_inst">
								<abbr title="required">* </abbr>
								<a href="https://play.google.com/store/apps/details?id=mm.com.money">
									<img width="183px" height="39px" class="gb_6b" src="https://www.gstatic.com/android/market_images/web/play_prism_hlock_2x.png">
								</a>
							</label>
							<div class="col-xs-12 col-md-7">
								<p style="margin-top: 13px;">	<?= l('To get a loan, you need to install a <a href="https://play.google.com/store/apps/details?id=mm.com.money">Android Application</a> and make a loan there.') ?></p>
							</div>
						</div>
				
						<? require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); ?>											
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>