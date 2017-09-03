<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Debt extends Model {
		use SoftDeletes;
		
		protected $table = 'f_debts';
		protected $fillable = [
			'deal_id',
			'product_interest_id',
			'amount',
			'created_at',
		];
		protected $dates = [
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function deal() {
			return $this->belongsTo('App\Models\Deal');
		}
		
		public function type() {
			return $this->belongsTo('App\Models\ProductInterest');
		}
	}
