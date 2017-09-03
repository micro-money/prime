		<div class="modal fade" id="denied" role="dialog">
			<div class="modal-dialog">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 class="modal-title">Deny a loan</h4>
				</div>  
				<div class="modal-body">
					<div class="form-horizontal">
						<div class="form-group">
						  <label class="control-label col-sm-2" for="dnote">Note:</label>
						  <div class="col-sm-10">          
							<textarea class="form-control" rows="3" id="dnote"></textarea>
						  </div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" onclick="changeStatus('deny');">Denied</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			  </div>
			</div>
		</div>