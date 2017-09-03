<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zUsrCash extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_UsrCash';
		protected $dates = [
			'CreatedOn',
			'ModifiedOn',
			'UsrOperationDate',
		];
		
		public function contact() {
			return $this->belongsTo('App\Models\Converter\zContact', 'UsrBorrowerId', 'Id');
		}
		
		public function opportunity() {
			return $this->belongsTo('App\Models\Converter\zOpportunity', 'UsropportunityId', 'UsrOpportunityId');
		}
	}
