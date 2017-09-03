<div class="form-group form-group-lg string <?= $ss_cladd ?><?= $ss_rname ?>">
	<label class="string <?= $ss_cladd ?>col-xs-12 col-md-5 control-label" for="<?= $ss_rname ?>">
		<? if ($ss_req!=0) { ?><abbr title="required">* </abbr><? } ?>
		<?= l($ss_lname) ?>
	</label>
	<div class="col-xs-12 col-md-7">
		<input tabindex="<?= $ss_tabi ?>" class="string <?= $ss_cladd ?>form-control input-lg" 
		<? if ($ss_phold!='') echo 'placeholder="'.$ss_phold.'"'; ?> 
		type="text" name="<?= $ss_fname ?>" id="<?= $ss_rname ?>" <?= $ss_eladd ?> value="<?= $ss_fval ?>">
	</div>
</div>