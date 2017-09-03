<div class="form-group form-group-lg <?= $ss_cladd ?>">
	<label class="col-xs-12 col-md-5 control-label <?= $ss_cladd ?>">
		<? if ($ss_req!=0) { ?><abbr title="required">* </abbr><? } ?>
		<?= l($ss_lname) ?>
	</label>
	<div class="col-xs-12 col-md-7">
		<input tabindex="<?= $ss_tabi ?>" type="file" name="<?= $ss_fname ?>" <?= $ss_eladd ?> class="form-control input-lg <?= $ss_cladd ?>">
	</div>
</div>
