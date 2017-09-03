<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class ProductInterest extends Model {
		use SoftDeletes;
		
		protected $table = 'f_product_interest';
		protected $fillable = [
			'product_id',
			'interest_id',
			'name',
			'fix_amount',
			'float_amount',
			'min_repayment_amount',
			'charge_on_main',
			'charge_in_main_term',
			'charge_on_overdue',
			'charge_in_overdue_term',
			'start_charge_day',
			'stop_charge_day',
			'include_in_base',
			'priority',
			'active',
		];
		protected $dates = [
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function product() {
			return $this->belongsTo('App\Models\Product');
		}
		
		public function interest() {
			return $this->belongsTo('App\Models\Interest');
		}
		
		public function debts() {
			return $this->hasMany('App\Models\Debt');
		}
	}
