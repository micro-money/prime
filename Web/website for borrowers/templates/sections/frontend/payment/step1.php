<div class="wrapper">
    <div class="step">
		<? require_once(MC_ROOT . '/templates/sections/frontend/step_header.php'); ?>
        <div class="tabs-container wizard-container">
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="row">
                        <div class="col-md-12">		
                            <h2 class="step__title padding-top-none">
								<?= l('Dear') ?> <?= $GLOBALS['ss_cname'] ?> <?= l('Thanks for using Micromoney!') ?>
							</h2>
                            <p>
								<?= l('It a pleasure to work with you!') ?>
							</p>
                            <p>
								<?= l('From now, you are our trusted customer!') ?>
							</p>
                            <p>
								<?= l('That means you can always get new bigger loan in just 2 minutes!') ?>
							</p>
                            <p>
								<?= l('Please wait 5 days and take a new loan!') ?>
							</p>
                            <p>
								<?= l('Just go now and apply on our website http://money.com.mm/') ?>
							</p>
                        <?
						/*
						Старое убрал 
						We receive your payment documents scan. Please wait for a call from us.
Dear {#Contact.Name}
Thanks for using Micromoney!

It a pleasure to work with you!
From now, you are our trusted customer!
That means you can always get new bigger loan in just 2 minutes! 

Please wait 5 days and take a new loan!
just go now and apply on our website http://money.com.mm/
						*/
						?>
						
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>