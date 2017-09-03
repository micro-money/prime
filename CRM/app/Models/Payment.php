<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Payment extends Model {
		use SoftDeletes;
		
		protected $table = 'f_payments';
		protected $fillable = [
			'person_id',
			'deal_id',
			'user_id',
			'amount',
			'created_at',
			'updated_at',
		];
		protected $dates = [
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function person() {
			return $this->belongsTo('App\Models\Person');
		}
		
		public function deal() {
			return $this->belongsTo('App\Models\Deal');
		}
		
		public function user() {
			return $this->belongsTo('App\Models\User');
		}
	}
