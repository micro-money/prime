<div class="wrapper">
    <div class="step">
        <!--
		<div class="input-group input-group-md margin50">
            <span class="input-group-addon active"><span class="arrow"></span><span class="hid-sm"><?= l('Personal information') ?></span><span class="vis-sm">1</span></span>
            <span class="input-group-addon"><span class="arrow"></span><span class="hid-sm"><?= l('Employment information') ?></span><span class="vis-sm">2</span></span>
            <span class="input-group-addon"><span class="arrow"></span><span class="hid-sm"><?= l('Additional information') ?></span><span class="vis-sm">3</span></span>
            <span class="input-group-addon"><span class="arrow"></span><span class="hid-sm"><?= l('Getting money') ?></span><span class="vis-sm">4</span></span>
            <span class="input-group-addon"><span class="arrow"></span><span class="hid-sm"><?= l('Finished') ?></span><span class="vis-sm">5</span></span>
        </div>
		-->
        <div class="tabs-container wizard-container">
            <div class="tab-content">
                
				<div class="tab-pane active">
					<!-- action="/app_wizard?step=10"  -->
                    <form class="simple_form form-horizontal" role="form" id="form-step-01" autocomplete="off" novalidate="novalidate" action="/app_wizard?step=10" accept-charset="UTF-8" method="post" enctype="multipart/form-data">
                        
						<div class="col-md-7 col-md-offset-5">
							<h2 class="step__title step__title-left padding-top-none"><?= l('Personal information') ?></h2>
						</div>
                        <div class="form-group form-group-lg string application_email">
                            <label class="string col-xs-12 col-md-5 control-label" for="application_email">
								<?= l('Email') ?>
                            </label>
                            <div class="col-xs-12 col-md-7">
                                <input tabindex="1" class="string required form-control input-lg" placeholder="mail@mail.com" type="text" name="email" id="application_email" value="<?= $user['email'] ?>">
                            </div>
                        </div>
						
                        <div class="form-group form-group-lg string required application_City">
                            <label class="string required col-xs-12 col-md-5 control-label" for="application_City">
                                <abbr title="required">
                                    *
                                </abbr> <?= l('City') ?>
                            </label>
                            <div class="col-xs-12 col-md-7">
                                <input tabindex="1" class="string required form-control input-lg" placeholder="<?= l('City') ?>" type="text" name="City" id="application_City" value="<?= $user['City'] ?>">
                            </div>
                        </div>

                        <div class="form-group form-group-lg string required application_township">
                            <label class="string required col-xs-12 col-md-5 control-label" for="application_township">
                                <abbr title="required">
                                    *
                                </abbr> <?= l('Township') ?>
                            </label>
                            <div class="col-xs-12 col-md-7">
                                <input tabindex="1" class="string required form-control input-lg" placeholder="<?= l('Township') ?>" type="text" name="township" id="application_township" value="<?= $user['township'] ?>">
                            </div>
                        </div>
						
                        <div class="form-group form-group-lg string required application_date_of_birth">
                            <label class="string required col-xs-12 col-md-5 control-label" for="application_date_of_birth">
                                <abbr title="required">
                                    *
                                </abbr> <?= l('BirthDay') ?>
                            </label>
                            <div class="col-xs-12 col-md-7">
                                <div class="row">
                                    <?
                                        $bdate_arr = explode('-', $user['BirthDate']);
                                        $day = $bdate_arr[2];
                                        $month = $bdate_arr[1];
                                        $year = $bdate_arr[0];
                                        if (empty($year)) $year = 1985;
                                    ?>
                                    <div class="col-xs-6">
                                        <select name="day" class="select optional form-control input-lg">
                                            <? for ($d = 1; $d <= 31; $d ++) {
                                                if ($d == $day) $c = ' selected'; else $c = ''; ?>
                                                <option value="<?=$d?>"<?=$c?>><?=$d?></option>
                                            <? } ?>
                                        </select>
                                    </div>
								</div>
								<div class="row">
                                    <div class="col-xs-6">
                                        <select name="month" class="select optional form-control input-lg">
                                            <?
                                                $months = [1 => 'Jan.', 2 => 'Feb.', 3 => 'Mar.', 4 => 'Apr.', 5 => 'May', 6 => 'Jun.', 7 => 'Jul.', 8 => 'Aug.', 9 => 'Sep.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Dec.'];
                                                for ($m = 1; $m <= 12; $m ++) {
                                                    if ($m == $month) $c = ' selected'; else $c = ''; ?>
                                                <option value="<?=$m?>"<?=$c?>><?=$months[$m]?></option>
                                            <?  } ?>
                                        </select>
                                    </div>
								</div>
								<div class="row">
                                    <div class="col-xs-6">
                                        <select name="year" class="select optional form-control input-lg">
                                            <? for ($y = 1950; $y <= 1999; $y ++) {
                                                if ($y == $year) $c = ' selected'; else $c = ''; ?>
                                                <option value="<?=$y?>"<?=$c?>><?=$y?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-lg radio_buttons required application_gender">
                            <label class="radio_buttons required col-md-5 control-label">
                                <abbr title="required">
                                    *
                                </abbr> <?= l('Gender') ?>
                            </label>
                            <div class="col-md-7">
                                <span class="radio">
                                    <label for="application_gender_0">
                                        <input class="radio_buttons required" type="radio" value="0" name="Gender" id="application_gender_0" <?= ($user['Gender'] === '0') ? ' checked="1"' : '' ?>>
                                        <?= l('Male') ?></label></span><span class="radio">
                                    <label for="application_gender_1">
                                        <input class="radio_buttons required" type="radio" value="1" name="Gender" id="application_gender_1" <?= ($user['Gender'] === '1') ? ' checked="1"' : '' ?>>
                                        <?= l('Female') ?></label></span>
                            </div>
                        </div>
                        <div class="form-group form-group-lg tel application_phone">
                            <label class="tel col-xs-12 col-md-5 control-label" for="application_phone">
                                <?= l('Your Viber Phone or any additional phone number?') ?>
                            </label>
                            <div class="col-xs-12 col-md-7">
                                <input tabindex="1" class="string tel form-control input-lg" placeholder="0944456789" type="tel" name="SecondPhone" id="application_phone" value="<?= $user['SecondPhone'] ?>">
                            </div>
                        </div>

				<!-- next step -->						
                    <div class="row">
                        <div class="col-md-7 col-md-offset-5">
                            <h2 class="step__title step__title-left padding-top-none"><?= l('Employment information') ?></h2>
                        </div>
                    </div>

                        <div class="form-group form-group-lg select application_social_status">
                            <label class="select col-xs-12 col-md-5 control-label" for="application_social_status_id">
                                <?= l('Social Status') ?>
                            </label>
                            <div class="col-xs-12 col-md-7">
                                <select role="socialStatus select2" class="select form-control input-lg" data-placeholder="<?= l('Please Select') ?>" placeholder="<?= l('Please Select') ?>" name="social_status_id" id="application_social_status_id"><option value=""><?= l('Please Select') ?></option>
                                    <? $s = $user['social_status_id']; ?>
                                    <option value="31"<?= ($s == 31) ? ' selected' : '' ?>><?= l('Business owner') ?></option>
                                    <option value="32"<?= ($s == 32) ? ' selected' : '' ?>><?= l('Self-employed') ?></option>
                                    <option value="33"<?= ($s == 33) ? ' selected' : '' ?>><?= l('Government Employee') ?></option>
                                    <option value="34"<?= ($s == 34) ? ' selected' : '' ?>><?= l('Housewife') ?></option>
                                    <option value="35"<?= ($s == 35) ? ' selected' : '' ?>><?= l('Police / Military employee') ?></option>
                                    <option value="36"<?= ($s == 36) ? ' selected' : '' ?>><?= l('Unemployed') ?></option>
                                    <option value="37"<?= ($s == 37) ? ' selected' : '' ?>><?= l('Attorney / Lawyer / Notary') ?></option>
                                    <option value="38"<?= ($s == 38) ? ' selected' : '' ?>><?= l('Student') ?></option>
                                    <option value="39"<?= ($s == 39) ? ' selected' : '' ?>><?= l('Pensioner') ?></option>
                                    <option value="40"<?= ($s == 40) ? ' selected' : '' ?>><?= l('Staff') ?></option>
                                    <option value="41"<?= ($s == 41) ? ' selected' : '' ?>><?= l('Manager') ?></option>
                                    <option value="42"<?= ($s == 42) ? ' selected' : '' ?>><?= l('Director') ?></option>
                                    <option value="43"<?= ($s == 43) ? ' selected' : '' ?>><?= l('Contract Employee') ?></option>
                                    <option value="44"<?= ($s == 44) ? ' selected' : '' ?>><?= l('Office worker') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-lg string required application_company_name">
                            <label class="string required col-xs-12 col-md-5 control-label" for="application_company_name">
                                <abbr title="required">* </abbr> <?= l('Company Name and Address') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input class="string required form-control input-lg" placeholder="Micromoney" type="text" name="company_name" id="application_company_name" value="<?= $user['company_name'] ?>" />
                            </div>
                        </div>
                        <div class="form-group form-group-lg tel required application_company_phone">
                            <label class="tel required col-xs-12 col-md-5 control-label" for="application_company_phone">
                                <abbr title="required">* </abbr><?= l('Company`s Phone Number') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input tabindex="1" class="string tel form-control input-lg" placeholder="0944456789" type="tel" name="CompanyPhone" id="application_phone" value="<?= $user['CompanyPhone'] ?>">
                            </div>
                        </div>
                        <div class="form-group form-group-lg currency required application_salary">
                            <label class="currency required col-xs-12 col-md-5 control-label" for="application_salary">
                                <abbr title="required">* </abbr><?= l('Monthly Gross Income (Ks)') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input class="form-control" type="tel" placeholder="Kyat 300,000" name="SalaryAmount" id="application_salary" value="<?= $user['SalaryAmount'] ?>" />
                            </div>
                        </div>
                        <div class="form-group form-group-lg tel optional application_coworker_phone">
                            <label class="tel optional col-xs-12 col-md-5 control-label" for="application_coworker_phone"><?= l('Your coworker phone number') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input role="phone" class="string tel optional form-control input-lg" placeholder="09 2345 6789" type="tel" name="CoworkerPhone" id="application_coworker_phone" value="<?= $user['CoworkerPhone'] ?>">
                            </div>
                        </div>
                        <div class="form-group form-group-lg string optional application_coworker_name">
                            <label class="string optional col-xs-12 col-md-5 control-label" for="application_coworker_name"><?= l('coworker name') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input class="string optional form-control input-lg" placeholder="<?= l('coworker name') ?>" type="text" name="CoworkerName" id="application_coworker_name" value="<?= $user['CoworkerName'] ?>">
                            </div>
                        </div>
				
				<!-- next step -->
                    <div class="row">
                        <div class="col-md-7 col-md-offset-5">
                            <h2 class="step__title step__title-left padding-top-none"><?= l('Additional information') ?></h2>
                            <label class="control-label"><?= l('Please write here phone number of your Emergency Contact Person and any Family member or Close Relative') ?></label>
                            <p><?= l('We never tell nobody about your loans. Your privacy and information are in safe.') ?></p>
                        </div>
                    </div>
                        <div class="form-group form-group-lg tel required application_phone_number">
                            <label class="tel required col-xs-12 col-md-5 control-label" for="application_phone_number">
                                <abbr title="required">* </abbr><?= l('Emergency Contact Person Name') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input class="string optional form-control input-lg" placeholder="<?= l('Emergency Contact Person Name') ?>" type="text" name="Guarantor1Name" id="application_coworker_name" value="<?= $user['Guarantor1Name'] ?>">
                            </div>
                        </div>

                        <div class="form-group form-group-lg tel optional application_coworker_phone">
                            <label class="tel optional col-xs-12 col-md-5 control-label" for="application_coworker_phone"><abbr title="required">* </abbr><?= l('Emergency Contact Phone number') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input role="phone" class="string tel optional form-control input-lg" placeholder="09 2345 6789" type="tel" name="Guarantor1Phone" id="application_coworker_phone" value="<?= $user['Guarantor1Phone'] ?>">
                            </div>
                        </div>

                        <div class="form-group form-group-lg tel required application_phone_number">
                            <label class="tel required col-xs-12 col-md-5 control-label" for="application_phone_number">
                                <abbr title="required">* </abbr><?= l('Family Contact Person Name') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input class="string optional form-control input-lg" placeholder="<?= l('Family Contact Person Name') ?>" type="text" name="Guarantor2Name" id="application_coworker_name" value="<?= $user['Guarantor2Name'] ?>">
                            </div>
                        </div>

                        <div class="form-group form-group-lg tel optional application_coworker_phone">
                            <label class="tel optional col-xs-12 col-md-5 control-label" for="application_coworker_phone"><abbr title="required">* </abbr><?= l('Family Contact Phone number') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input role="phone" class="string tel optional form-control input-lg" placeholder="09 2345 6789" type="tel" name="Guarantor2Phone" id="application_coworker_phone" value="<?= $user['Guarantor2Phone'] ?>">
                            </div>
                        </div>
				<!-- next step -->
                    <div class="row">
                        <div class="col-md-7 col-md-offset-5">
                            <h2 class="step__title step__title-left padding-top-none"><?= l('Getting money') ?></h2>
                        </div>
                    </div>
                       <div class="form-group form-group-lg select optional application_money_choose_id">
                            <label class="select optional col-xs-12 col-md-5 control-label" for="application_money_choose_id"><?= l("How do you want to get money?") ?></label>
                            <div class="col-xs-12 col-md-7">
                                <select role="moneyChoose" class="select optional form-control input-lg" data-placeholder="false" name="HowDoYouWantToGetMoney" id="application_money_choose_id" aria-invalid="false">
                                    <option selected="selected" value="1"><?= l("I don`t know") ?></option>
                                    <option value="2"><?= l('Bank account') ?></option>
                                    <option value="3"><?= l('Domestic remittance') ?></option>
                                    <option value="4"><?= l('Payment system') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="hidden" data-id="2" role="toggleInput">
                            <div class="form-group form-group-lg string optional application_bank_account">
                                <label class="string optional col-xs-12 col-md-5 control-label" for="bank_id"><?= l('Choose your bank here') ?></label>
                                <div class="col-xs-12 col-md-7">
                                    <select class="select optional form-control input-lg" data-placeholder="false" name="bank_id" id="bank_id" aria-invalid="false">
                                        <option selected="selected" value="1">Other</option>
                                        <option selected="selected" value="2">KBZ Bank</option>
                                        <option value="3">AYA Bank</option>
                                        <option value="4">CB Bank</option>
                                        <option value="5">AGD Bank</option>
                                        <option value="6">Yoma Bank</option>
                                        <option value="7">Innwa Bank</option>
                                        <option value="8">Myanmar Apex Bank</option>
                                        <option value="9">United Amara Bank</option>
                                        <option value="10">Myawaddy</option>
                                        <option value="11">Rural Development Bank</option>
                                        <option value="12">First Private bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-group-lg string optional application_bank_account">
                                <label class="string optional col-xs-12 col-md-5 control-label" for="application_bank_account"><?= l('Fill your bank account number here') ?></label>
                                <div class="col-xs-12 col-md-7">
                                    <input class="string optional form-control input-lg" placeholder="KBZ 0123456789879654" type="text" name="bank_account" id="application_bank_account" disabled="disabled" value="<?= $user['PaymentWallet'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="hidden" data-id="3" role="toggleInput">
                            <div class="form-group form-group-lg string optional application_domestic_remittance">
                                <label class="string optional col-xs-12 col-md-5 control-label" for="application_domestic_remittance"><?= l('Your bank branch name or address here') ?></label>
                                <div class="col-xs-12 col-md-7">
                                    <input class="string optional form-control input-lg" placeholder="KBZ bank, Merchant road" type="text" name="domestic_remittance" id="application_domestic_remittance" disabled="disabled" value="<?= $user['PaymentWallet'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="hidden" data-id="4" role="toggleInput">
                            <div class="form-group form-group-lg string optional application_payment_system">
                                <label class="string optional col-xs-12 col-md-5 control-label" for="application_payment_system"><?= l('Write here your OK$ account number') ?></label>
                                <div class="col-xs-12 col-md-7">
                                    <input class="string optional form-control input-lg" placeholder="OK$ 09123456789" type="text" name="payment_system" id="application_payment_system" disabled="disabled" value="<?= $user['PaymentWallet'] ?>">
                                </div>
                            </div>
                        </div>



						<!---->
                        <div class="form-group form-group-lg">
                            <label class="col-xs-12 col-md-5 control-label"><?= l('Please make a photo with your bank account number (safe account or check book)') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input type="file" name="photo1" class="form-control input-lg">
                            </div>
                        </div>
						
                        <div class="form-group form-group-lg string required application_document_number">
                            <label class="string required col-xs-12 col-md-5 control-label" for="application_document_number">
                                <abbr title="required">*</abbr> <?= l('NRC Number') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input role="documentNumber" class="string required form-control input-lg" placeholder="12/ThaKaKa(N)000000" maxlength="20" size="20" type="text" name="UsrMMPersonalID" id="application_document_number" value="<?= $user['UsrMMPersonalID'] ?>">
                            </div>
                        </div>
                        
						<div class="form-group form-group-lg">
                            <label class="col-xs-12 col-md-5 control-label"><?= l('Please make a photo of you NRC card') ?></label>
                            <div class="col-xs-12 col-md-7">
                                <input type="file" name="photo2" class="form-control input-lg">
                            </div>
                        </div>
						<!---->
                        <div class="form-group  form-group-lg boolean optional application_privacy_policy_acceptance">
                            <div class="col-xs-12 col-md-7 col-md-offset-5">
                                <div class="checkbox">
                                    <input value="0" type="hidden" name="application[privacy_policy_acceptance]">
                                    <label class="boolean optional checkbox-pretty checkbox" for="application_privacy_policy_acceptance">
                                        <input class="boolean optional" type="checkbox" value="1" checked="checked" name="application[privacy_policy_acceptance]" id="application_privacy_policy_acceptance">
                                        <?= l('AUTHORIZATION') ?><br>
                                        <?= l('I have read and accepted') ?> <a target="_blank" href="/terms-and-conditions"><?= l('Terms &amp; Conditions') ?></a>, <a target="_blank" href="https://drive.google.com/file/d/0BwjCTKtgJMwkd1c2T3ZUT0ZkeGM/view"><?= l('Loan agreement') ?></a>, <a target="_blank" href="https://drive.google.com/file/d/0BwjCTKtgJMwkWnl5YU5LVUpVNWM/view"><?= l('Service Contract') ?></a>.  
									</label>
                                </div>
                            </div>
                        </div>
				<!-- next step -->

				<!-- next step -->
				
						<div class="form-group form-group-actions">						
							<div class="col-md-7 col-md-offset-5">
								<span><?= l('* mandatory field') ?></span>
								<br><br>
								<input type="hidden" name="data" value="10" />
								<button name="button" type="submit" class="btn-hero btn-hero-loader" data-disable-with="<div class=&quot;loading btn-hero&quot;></div>"><span><?= l('Finish') ?></span></button>
							</div>									
                    </form>
                </div>

            </div>
        </div>

		</div>
    </div>
</div>