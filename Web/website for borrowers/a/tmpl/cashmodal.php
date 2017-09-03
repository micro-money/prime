		<div class="modal fade" id="smoney" role="dialog">
			<div class="modal-dialog">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 class="modal-title"><?= $shead ?></h4>
				</div>  
				<div class="modal-body">
					<div class="form-horizontal">
						<div class="form-group">
						  <label class="control-label col-sm-2" for="samount">Amount to send:</label>
						  <div class="col-sm-10">
							<input type="number" class="form-control" id="samount" placeholder="Amount to send" name="samount" value="<?= $sas_sqlm["lo"]["lowa"] ?>">
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="soacc">From our account:</label>
						  <div class="col-sm-10">          
							<?= $soacc ?>
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="cacc">To account:</label>
						  <div class="col-sm-10">          
							<?= $cacc ?>
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="sopdate">Operation date:</label>
						  <div class="col-sm-10">          
							<input type="date" class="form-control" id="sopdate" placeholder="Operation Date" name="sopdate" min="<?= $dvmin ?>" max="<?= date('Y-m-d', strtotime(' +0 day')) ?>" value="<?= date('Y-m-d') ?>">  
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="sofdate">Set-off date:</label>
						  <div class="col-sm-10">          
							<input type="date" class="form-control" id="sofdate" placeholder="Set-off Date" name="sofdate" min="<?= $dvmin ?>" max="<?= date('Y-m-d', strtotime(' +0 day')) ?>" value="<?= date('Y-m-d') ?>">  
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="snote">Note:</label>
						  <div class="col-sm-10">          
							<textarea class="form-control" rows="3" id="snote"></textarea>
						  </div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" onclick="movemoney(0);">Send money</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			  </div>
			</div>
		</div>
		<div class="modal fade" id="rmoney" role="dialog">
			<div class="modal-dialog">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 class="modal-title"><?= $rhead ?></h4>
				</div>  
				<div class="modal-body">
					<div class="form-horizontal">
						<div class="form-group">
						  <label class="control-label col-sm-2" for="ramount">Amount to recieve:</label>
						  <div class="col-sm-10">
							<input type="number" class="form-control" id="ramount" placeholder="Amount to recieve" name="ramount">
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="roacc">To our account:</label>
						  <div class="col-sm-10">          
							<?= $roacc ?>
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="cashman">Cash provider:</label>
						  <div class="col-sm-10">          
							<?= $cashman ?>
						  </div>
						</div>						
						<div class="form-group">
						  <label class="control-label col-sm-2" for="ropdate">Operation date:</label>
						  <div class="col-sm-10">          
							<input type="date" class="form-control" id="ropdate" placeholder="Operation Date" name="opdate" min="<?= $dvmin ?>" max="<?= date('Y-m-d', strtotime(' +0 day')) ?>" value="<?= date('Y-m-d') ?>">  
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="rofdate">Set-off date:</label>
						  <div class="col-sm-10">          
							<input type="date" class="form-control" id="rofdate" placeholder="Set-off date" name="rofdate" min="<?= $dvmin ?>" max="<?= date('Y-m-d', strtotime(' +0 day')) ?>" value="<?= date('Y-m-d') ?>">  
						  </div>
						</div>
						<div class="form-group">
						  <label class="control-label col-sm-2" for="rnote">Note:</label>
						  <div class="col-sm-10">          
							<textarea class="form-control" rows="3" id="rnote"></textarea>
						  </div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="movemoney(1);">Recieve money</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			  </div>
			</div>
		</div>