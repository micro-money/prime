<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Product extends Model {
		use SoftDeletes;
		
		protected $table = 'f_products';
		protected $fillable = [
			'name',
			'description',
			'min_term',
			'max_term',
			'min_amount',
			'max_amount',
			'currency_id',
			'active',
		];
		protected $dates = [
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function currency() {
			return $this->belongsTo('App\Models\Currency');
		}
		
		public function interests() {
			return $this->hasMany('App\Models\ProductInterest');
		}
		
		public function deals() {
			return $this->hasMany('App\Models\Deal');
		}
	}
