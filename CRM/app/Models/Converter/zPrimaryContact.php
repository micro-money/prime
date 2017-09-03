<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zPrimaryContact extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_UsrPrimaryContact';
		
		// Relationships
		
		public function lead() {
			return $this->belongsTo('App\Models\Converter\zLead', 'UsrLeadId', 'Id');
		}
	}
