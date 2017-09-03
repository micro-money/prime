<div class="row">
	<ul class="nav nav-tabs">
		<? $sd=$sas_sqlm["data"]; ?>
		<? if (!isset($fdata)) { ?>
		<li class="active"><a data-toggle="pill" href="#d_pi">Primary info</a></li>		
		<? } else { ?>
			<li class="active"><a data-toggle="pill" href="#d_fd"><?= $dpel['fd']['htname'] ?></a></li>		
			<? if (isset($lo) && isset($lohist)) { ?>
			<li><a data-toggle="pill" href="#d_sh">Status history</a></li>		
			<? } ?>
			
			<li><a data-toggle="pill" href="#d_pi">Primary info</a></li>		
		<? } ?>		
		<li><a data-toggle="pill" href="#d_ci">Contacts <? if (isset($sd['ci'])) echo '('.count($sd['ci']).')' ?></a></li>
		<li><a data-toggle="pill" href="#d_fl">Files <? if (isset($sd['fl'])) echo '('.count($sd['fl']).')' ?></a></li>
		<? if (isset($sd['ad']) && count($sd['ad'])>0) { ?><li><a data-toggle="pill" href="#d_ad">App data</a></li><? } ?>
		<li><a data-toggle="pill" href="#d_ldl">Leads <? if (isset($sd['ldl'])) echo '('.count($sd['ldl']).')' ?></a></li>
		<? if (isset($sd['lo']) && count($sd['lo'])>0) { ?><li><a data-toggle="pill" href="#d_lo">Loans <? if (isset($sd['lo'])) echo '('.count($sd['lo']).')' ?></a></li><? } ?>
		<? if (isset($sd['cl']) && count($sd['cl'])>0) { ?><li><a data-toggle="pill" href="#d_cl">Calls <? if (isset($sd['cl'])) echo '('.count($sd['cl']).')' ?></a></li><? } ?>
		<? if (isset($sd['ch']) && count($sd['ch'])>0) { ?><li><a data-toggle="pill" href="#d_ch">Cash <? if (isset($sd['ch'])) echo '('.count($sd['ch']).')' ?></a></li><? } ?>
	</ul>
	<div class="tab-content">
		<? if (!isset($fdata)) { ?>
		<div id="d_pi" class="tab-pane fade in active"><?= $html['pi'] ?></div>	
		<? } else { ?>
			<div id="d_fd" class="tab-pane fade in active"><?= $html['fd'] ?></div>
			<div id="d_pi" class="tab-pane fade"><?= $html['pi'] ?></div>	
		<? } ?>
		
		<? if (isset($lo) && isset($lohist)) { ?><div id="d_sh" class="tab-pane fade"><?= $lohist ?></div><? } ?>	
		
		<div id="d_ci" class="tab-pane fade"><?= $html['ci'] ?></div>	
		
		<div id="d_fl" class="tab-pane fade">
			<? $cstl='style="padding: 5px;margin-bottom: 5px;margin-right: 7px;"'; ?>
			<form method="post" enctype="multipart/form-data">
				<div class="form-inline">  
					<input type="hidden" name="phuid" value="<?= $sas_sqlm['m']['uid'] ?>">
					<div class="form-group" <?= $cstl ?>>Select a new document file for upload</div>
					<div class="form-group" <?= $cstl ?>>
						<input tabindex="1" type="file" name="photof" class="form-control input-lg ">
					</div>
					<div class="form-group" <?= $cstl ?>>Choose file type</div>
					<select class="form-control" name="phtype" <?= $cstl ?>>
						<option value="3" selected="">Payment slip</option>
						<option value="2">NRC</option>
						<option value="1">Bank Account</option>
					</select>
					<div class="form-group" <?= $cstl ?>>
						<!--<button type="button" class="btn btn-info" type="submit" name="button">Upload</button>-->
						<input class="btn btn-info" type="submit" value="Upload">
					</div>
				</div>
			</form>			
			<?= $html['fl'] ?>
		</div>
		<div id="d_ldl" class="tab-pane fade"><?= $html['ldl'] ?></div>
		<? if (isset($sd['ad']) && count($sd['ad'])>0) { ?><div id="d_ad" class="tab-pane fade"><?= $html['ad'] ?></div><? } ?>		
		<? if (isset($sd['lo']) && count($sd['lo'])>0) { ?><div id="d_lo" class="tab-pane fade"><?= $html['lo'] ?></div><? } ?>
		<? if (isset($sd['cl']) && count($sd['cl'])>0) { ?><div id="d_cl" class="tab-pane fade"><?= $html['cl'] ?></div><? } ?>
		<? if (isset($sd['ch']) && count($sd['ch'])>0) { ?><div id="d_ch" class="tab-pane fade"><?= $html['ch'] ?></div><? } ?>
	</div>
</div>
