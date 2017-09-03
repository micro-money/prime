<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zMessageLog extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_MessageLog';
		protected $dates = [
			'CreatedOn',
			'ModifiedOn',
			'SendDate',
		];
		
		public function contact() {
			return $this->belongsTo('App\Models\Converter\zContact', 'ContactId', 'Id');
		}
		
		public function opportunity() {
			return $this->belongsTo('App\Models\Converter\zOpportunity', 'OpportunityId', 'Id');
		}
	}
