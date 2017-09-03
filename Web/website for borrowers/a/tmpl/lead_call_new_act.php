<div class="row" cajx_lid="<?= $lid ?>" >
				
	<div class="row">		
		<div class="col-lg-12">
			<div class="form-group">
				<button class="btn btn-success btn-block btn-lg" onclick="fastbutton(4);">Pipeline</button>
			</div>
			<div class="form-group">
				<label for="note">Call comment:</label>
				<textarea class="form-control" rows="1" id="note"></textarea>
			</div>
		</div>
	</div>
	
	<div class="row">		
		<div class="col-lg-6">
			<div class="form-group">
				<button class="btn btn-default" onclick="fastbutton(1);">NO PICK UP</button>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group">
				<button class="btn btn-info" onclick="fastbutton(2);">Does not want to borrow</button>
			</div>
		</div>
	</div>
	
	<div class="row">		
		<div class="col-lg-6">
			<div class="form-group">
				<button class="btn btn-warning" onclick="fastbutton(3);">Need Bank Acc or NRC</button>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group">
				<button class="btn btn-danger" onclick="fastbutton(5);">Bad guy</button>
			</div>
		</div>
	</div>
	<div class="row">			
		<div class="col-lg-12" >
			<div class="form-group">
				<span style="font-size: 20px;">Next Recall&nbsp;&nbsp;&nbsp;</span>
				<select onchange="chNextCall(this);" id="NextCallTime" style="width: 200px;
							height: 34px;
							padding: 6px 12px;
							font-size: 14px;
							line-height: 1.42857143;
							color: #555;
							background-color: #fff;
							background-image: none;
							border: 1px solid #ccc;
							border-radius: 4px;">
					<option value="0" selected>not need</option>
					<option value="1">after 30 min</option>
					<option value="2">after 1 hour</option>
					<option value="3">after 2 hours</option>
					<option value="4">after 3 hours</option>
					<option value="5">Tomorrow</option>
				</select>&nbsp;&nbsp;&nbsp;
				<button class="btn" id="NextCallBut" style="display:none;" onclick="fastbutton(6);">Next customer</button>
			</div>	
		</div>	
	</div>
</div>