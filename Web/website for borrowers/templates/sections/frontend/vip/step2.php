<div class="wrapper">
    <div class="step">
		<? require_once(MC_ROOT . '/templates/sections/frontend/step_header.php'); ?>
        <!--<div class="tabs-container wizard-container">-->
            <div class="content__block"> <!--<div class="tab-content">-->
                <div class="content__block__body"> <!--<div class="tab-pane active">-->

		   
					<!--<div class="row">-->
                        <div class="col-12">
                            <h2 class="step__title step__title-left ">  <!--padding-top-none-->
							<?= l('Success!') ?>
							</h2>
                            <label class="control-label">
								<?= l('Your application is approved!') ?>
							</label>
          <!--We will DO OUR BEST to send you money AS SOON AS POSSIBLE!--> 							
							<p>
							<?= l('We will send you! ') ?> <?= $GLOBALS['ss_howmoney'] ?> mmk <?= l('withing 24hours') ?> <?= $GLOBALS['ss_wheremoney'] ?>!
							</p>
<div id="set_string_173" timerset="<?= $GLOBALS['ss_stimer'] ?>" test="03/10/2017 00:00" class="second-my timerhello timerhello_173">
	<div class="second-my-content"> 
		<p class="result">
			<!--<span class="result-day items">00</span>
			<span class="dot">day.&nbsp;</span>-->
			<span class="dot"><?= l('YOU WILL GET MONEY IN') ?></span>
			<span class="result-hour items">01</span>  
			<span class="dot">hour</span>
			<span class="result-minute items">03</span>
			<span class="dot">min</span>
			<span class="result-second items">36</span>
			<span class="dot">sec</span>			
		</p> 
		<div class="clearf"></div>
	</div>
</div>
	
							<p>
							<?= l('If after 24 hours you still not receive money please CONTACT US!') ?>
							</p><p>
							<?= l('Send us message to') ?> <a href="https://www.messenger.com/t/micromoneymyanmar" target="_blank"><?= l('FACEBOOK MESSENGER') ?></a>
							</p>
							<ul>
								<li>
									<p><?= l('Send message or call to Viber hotline') ?>: </p>
									<p><a href="viber://add?number=%2B959972600591">+95 9 972 600 591</a>,</p>  
									<p><a href="viber://add?number=%2B959452223580 ">+95 9 452 223 580 </a></p>
								</li>
								<li>
									<p><?= l('Send message or call to LINE hotline') ?>: </p>
									<p>micromoney.opt1</p> 
									<p>micromoney.opt2</p> 
								</li>
								<li>
									<p><?= l('Call Us on mobile hotline') ?>:</p> 
									<p><a href="tel:+959972600591">+95 9 972 600 591</a>,</p>  
									<p><a href="tel:+959452223580">+95 9 452 223 580</a></p>
								</li>
							</ul>

                        </div>
                    <!--</div>-->
                </div>
            </div>
        <!--</div>-->
    </div>
</div>

<? 
/*
Старое
We receive your loan application. Please wait for a call from us. This usually takes up to 2 days due to the queue. The money is waiting for you!
*/
?>