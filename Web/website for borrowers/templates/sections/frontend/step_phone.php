<div class="form-group form-group-lg tel <?= $ss_cladd ?><?= $ss_rname ?>">
	<label class="tel <?= $ss_cladd ?>col-xs-12 col-md-5 control-label" for="<?= $ss_rname ?>">
		<? if ($ss_req!=0) { ?><abbr title="required">* </abbr><? } ?>
		<?= $ss_lname ?>
	</label>
	<div class="col-xs-12 col-md-7">
		<input tabindex="<?= $ss_tabi ?>" class="tel <?= $ss_cladd ?>form-control input-lg tel" role="phone"  
		placeholder="<? if ($ss_phold!='') { 
			echo $ss_phold;
		} else {
			echo $countrym[$app['current_country']]['phone_def'];
		}?>" 
		type="tel" name="<?= $ss_fname ?>" id="<?= $ss_rname ?>" <?= $ss_eladd ?> value="<?= $ss_fval ?>">
	</div>
</div>