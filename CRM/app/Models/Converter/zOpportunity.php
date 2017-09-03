<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zOpportunity extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_Opportunity';
		
		public function contact() {
			return $this->belongsTo('App\Models\Converter\zContact', 'ContactId', 'Id');
		}
		
		public function lead() {
			return $this->belongsTo('App\Models\Converter\zLead', 'UsrOpportunityId', 'UsrOpportunityId');
		}
	}
