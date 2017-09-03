<div class="form-group form-group-lg radio_buttons required application_gender">
	<label class="radio_buttons required col-md-5 control-label">
		<abbr title="required">
			*
		</abbr> <?= l('Gender') ?>
	</label>
	<div class="col-md-7">
		<span class="radio">
			<label for="application_gender_0">
				<input tabindex="7" class="radio_buttons required" type="radio" value="0" name="Gender" id="application_gender_0" <?= ($user['Gender'] === '0') ? ' checked="1"' : '' ?>>
				<?= l('Male') ?>
			</label>
		</span>
		<span class="radio">
			<label for="application_gender_1">
				<input tabindex="8" class="radio_buttons required" type="radio" value="1" name="Gender" id="application_gender_1" <?= ($user['Gender'] === '1') ? ' checked="1"' : '' ?>>
				<?= l('Female') ?>
			</label>
		</span>
	</div>
</div>