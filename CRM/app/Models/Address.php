<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Address extends Model {
		use SoftDeletes;
		
		protected $table = 'p_addresses';
		protected $fillable = [
			'person_id',
			'city_id',
			'district_id',
			'address',
		];
		protected $dates = ['created_at', 'updated_at', 'deleted_at'];
		
		// Relationships
		
		public function person() {
			return $this->belongsTo('App\Models\Person');
		}
		
		public function city() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'city');
		}
		
		public function district() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'district');
		}
	}
