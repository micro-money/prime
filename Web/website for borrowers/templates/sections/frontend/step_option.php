<div class="form-group form-group-lg select <?= $ss_cladd ?><?= $ss_rname ?>">
	<label class="select <?= $ss_cladd ?>col-xs-12 col-md-5 control-label" for="<?= $ss_rname ?>">
		<? if ($ss_req!=0) { ?><abbr title="required">* </abbr><? } ?>
		<?= l($ss_lname) ?>
	</label>
	<div class="col-xs-12 col-md-7">
		<select tabindex="<?= $ss_tabi ?>"  class="select optional form-control input-lg" name="<?= $ss_fname ?>" id="<?= $ss_rname ?>" <?= $ss_eladd ?>>
			<? foreach ($ss_oarr as $ss_okey=>$ss_ovol) { 
			$ss_astr=''; if ($ss_okey==$ss_fval) $ss_astr='selected="selected"'; ?>
			<option <?= $ss_astr ?> value="<?= $ss_okey ?>"><?= l($ss_ovol) ?></option>
			<? } ?>
		</select>
	</div>
</div>