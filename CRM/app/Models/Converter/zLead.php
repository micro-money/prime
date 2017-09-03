<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zLead extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_Lead';
		protected $dates = [
			'CreatedOn',
			'ModifiedOn',
		];
		
		public function contact() {
			return $this->belongsTo('App\Models\Converter\zContact', 'QualifiedContactId', 'Id');
		}
		
		public function opportunity() {
			return $this->belongsTo('App\Models\Converter\zOpportunity', 'UsrOpportunityId', 'UsrOpportunityId');
		}
	}
