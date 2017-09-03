<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zContact extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_Contact';
		
		public function opportunities() {
			return $this->hasMany('App\Models\Converter\zOpportunity', 'ContactId', 'Id');
		}
	}
