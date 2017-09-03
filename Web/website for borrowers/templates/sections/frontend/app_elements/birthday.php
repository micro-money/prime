<div class="form-group form-group-lg string required application_date_of_birth">
	<label class="string required col-xs-12 col-md-5 control-label" for="application_date_of_birth">
		<abbr title="required">
			*
		</abbr> <?= l('BirthDay') ?>
	</label>
	<div class="col-xs-12 col-md-7">
		<div class="row">
			<?
				$cbd='1985-01-01'; if (!empty($user['birthdate']))  $cbd=$user['birthdate'];
				$bdate_arr = explode('-', $cbd);
				$day = $bdate_arr[2];
				$month = $bdate_arr[1];
				$year = $bdate_arr[0];
				if (empty($year)) $year = 1985;
			?>
			<div class="col-xs-6">
				<select tabindex="4" name="day" class="select optional form-control input-lg">
					<? for ($d = 1; $d <= 31; $d ++) {
						if ($d == $day) $c = ' selected'; else $c = ''; 
						if ($day==0 && $d == 1) $c = ' selected';
					?>
						<option value="<?=$d?>"<?=$c?>><?=$d?></option>
					<? } ?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-6">
				<select tabindex="5" name="month" class="select optional form-control input-lg">
					<?
						$months = [1 => 'Jan.', 2 => 'Feb.', 3 => 'Mar.', 4 => 'Apr.', 5 => 'May', 6 => 'Jun.', 7 => 'Jul.', 8 => 'Aug.', 9 => 'Sep.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Dec.'];
						for ($m = 1; $m <= 12; $m ++) {
							if ($m == $month) $c = ' selected'; else $c = ''; 
							if ($month==0 && $m == 1) $c = ' selected';
					?>
						<option value="<?=$m?>"<?=$c?>><?=$months[$m]?></option>
					<?  } ?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-6">
				<select tabindex="6" name="year" class="select optional form-control input-lg">
					<? for ($y = 1950; $y <= 2007; $y ++) {
						if ($y == $year) $c = ' selected'; else $c = ''; 
						if ($year==0 && $y = 1985) $c = ' selected';
					?>
						<option value="<?=$y?>"<?=$c?>><?=$y?></option>
					<? } ?>
				</select>
			</div>
		</div>
	</div>
</div>