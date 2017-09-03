<div class="wrapper">
    <div class="step">
		<? require_once(MC_ROOT . '/templates/sections/frontend/step_header.php'); ?>
        <div class="tabs-container wizard-container">
            <div class="tab-content">
                <div class="tab-pane active">
					<div class="row">
						<div class="col-md-12">	
                            <h2 class="step__title step__title-left padding-top-none">
                            <?= l('Dear') ?> <?= $GLOBALS['ss_cname'] ?>. <?= l('Your loan repay date is') ?> <?= $GLOBALS['ss_payday'] ?> <?= l('and amount is') ?> <?= $GLOBALS['ss_deb'] ?>.
							</h2>
                        </div>
                    </div>
					
<ul class="number_list number_list-small">
	<li>
	<span class="number_list__number">1</span>
		<p><?= l('Please pay via BANK to account (acc name M Zaw Naw)') ?></p>
		<p>KBZ 06030106005251101</p>
		<p>CB 0086600100027138</p>
		<p>AYA 0080201010147381</p>
		<p><?= l('or please pay via OKdollar to account') ?></p>
		<p>OK$-09454152526</p>
	</li>
	<li>
		<span class="number_list__number">2</span>
		<strong>
			<?= l('TO CLOSE LOAN AND AVOID PENALTIES, write in PAYMENT DETAILS YOUR LOAN NUMBER') ?> 
		</strong>
		<font style="color:red;"><?= $GLOBALS['ss_loanumber'] ?></font>
		<strong>
		<?= l('and YOUR PHONE!') ?>   
		</strong>
		<font style="color:red;"><?= $GLOBALS['ss_uphone'] ?></font>
	</li>
	<li>
		<span class="number_list__number">3</span>
		<?= l('Make a photo and attach confirmation press button below') ?>
	</li>
</ul>
					
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="<?= $GLOBALS['ss_fhref'] ?>" accept-charset="UTF-8" method="post" enctype="multipart/form-data">

						<?
						/*
Старые переводы:
you have a debt
Please pay now and attach your payment proof.
bank payment slip
mobile banking screenshot
ATM transfer screen
						

Dear {#Contact.Name},
Your loan repay date is {#UsrApprovedRepayDate} and amount is {#UsrAmountToPaid}.

<ul class="number_list number_list-small">
	<li>
	<span class="number_list__number">1</span>
		<p>Please pay via BANK to account (acc name M Zaw Naw)</p>
		<p>KBZ 06030106005251101</p>
		<p>CB 0086600100027138</p>
		<p>AYA 0080201010147381</p>
		<p>or please pay via OKdollar to account</p>
		<p>OK$-09454152526</p>
	</li>
	<li>
		<span class="number_list__number">2</span>
		<strong>
			TO CLOSE LOAN AND AVOID PENALTIES, write in PAYMENT DETAILS YOUR LOAN NUMBER {#UsrOpportunityId} and YOUR PHONE!
		</strong>
	</li>
	<li>
		<span class="number_list__number">3</span>
		Make a photo and send confirmation of your payment to Viber <a href="viber://add?number=%2B959767617781">+95 9 767 617 781</a>
	</li>
</ul>
<label class="control-label">Samples how should look your confirmation:</label>


<label class="control-label">Please be careful! Prolongation fee for OVERDUE PAYMENT is 5000mmk!</label>

You can avoid penalty and use money 15 days more - call now and pay only service fee! 
text to Viber: <a href="viber://add?number=%2B959972600591">+95 9 972 600 591</a>,  <a href="viber://add?number=%2B959452223580 ">+95 9 452 223 580 </a>
Call to hotline: <a href="tel:+959972600591">+95 9 972 600 591</a>,  <a href="tel:+959452223580">+95 9 452 223 580</a>


						*/
						
						$ss_eladd='';   		// Дополнительная вставка в элемент ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_cladd='required ';	// Дополнительная вставка в класс ОБЯЗАТЕЛЬНА БЫТЬ пустая или нет
						$ss_req=1; 				// Вывод HTML элемента - Обязательное поле
						
						$ss_lname='Photo of your payment proof document';
						$ss_fname='photo1';
						$ss_tabi=1;
						require(MC_ROOT . '/templates/sections/frontend/step_foto.php');
						?>
						
						<div class="row">	
							<a href="#0_2" class="col-xs-12 col-md-12 collapsed" style="text-align: left;font-size:20px;" data-toggle="collapse">
									<?= l('Samples how should look your confirmation:') ?>
							</a>
						</div>
						<div class="row">
							<div id="0_2" class="col-xs-12 col-md-12 collapse">
								<div class="row">	
									<div class="col-md-6">
										<img width="300px" height="240px" alt="p1" src="/images/docs/slip.jpg">
									</div>	
									<div class="col-md-6">
										<img width="300px" height="240px" alt="p1" src="/images/docs/mobile_atm.jpg">
									</div>								
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">							
								<h4><?= l('Please be careful!') ?></h4>
								<label class="control-label">
									<?= l('Prolongation penalty for OVERDUE PAYMENT is 5000mmk!') ?>
								</label>
							</div>	
						</div>

								
						<div class="row">
							<div class="col-md-12">							
								<label class="control-label">
								<?= l('You can avoid penalty and use money 15 days more - call now and pay only service fee!') ?>
								</label>
								<p>&nbsp;</p>
								<ul>
									<li>
										<p><?= l('Text to Viber') ?>:</p>
										<p><a href="viber://add?number=%2B959972600591">+95 9 972 600 591</a>,</p>  
										<p><a href="viber://add?number=%2B959452223580 ">+95 9 452 223 580 </a></p>
									</li>
									<li>
										<p><?= l('Call to hotline') ?>:</p>
										<p><a href="tel:+959972600591">+95 9 972 600 591</a>,</p>  
										<p><a href="tel:+959452223580">+95 9 452 223 580</a></p>
									</li>
								</ul>
							</div>	
						</div>
						
						<?
						require(MC_ROOT . '/templates/sections/frontend/step_nextbutton.php'); 
						?>      
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>