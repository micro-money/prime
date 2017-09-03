<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class DealStatus extends Model {
		use SoftDeletes;
		
		protected $table = 'd_statuses';
		protected $fillable = [
			'deal_id',
			'user_id',
			'status_id',
			'notes',
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
		
		public function user() {
			return $this->belongsTo('App\Models\User');
		}
		
		public function status() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'deal_status');
		}
	}
