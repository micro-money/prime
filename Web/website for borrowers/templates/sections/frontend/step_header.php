<div class="input-group input-group-md margin50" test="yes">
<? $ss_kol=0;
foreach ($GLOBALS['ss_harr'] as $ss_hkey=>$ss_hvol) { $ss_kol++; ?>
<? 	$ss_tastr=''; if ($ss_hkey==$step) { $ss_tastr=' active'; $ss_tahead=$ss_hvol;} ?>
	<span class="input-group-addon<?= $ss_tastr ?>">
		<span class="arrow"></span>
		<span class="hid-sm"><?= l($ss_hvol) ?></span>
		<span class="vis-sm"><?= $ss_kol ?></span>
	</span>
<? } ?>
</div>