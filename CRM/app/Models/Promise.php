<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Promise extends Model {
		use SoftDeletes;
		
		protected $table = 'd_promises';
		protected $fillable = [
			'deal_id',
			'call_id',
			'person_id',
			'type',
			'amount',
			'date',
			'status_id',
			'notes',
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
		
		public function call() {
			return $this->belongsTo('App\Models\Call');
		}
		
		public function person() {
			return $this->belongsTo('App\Models\Person');
		}
		
		public function status() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'promise_status');
		}
	}
