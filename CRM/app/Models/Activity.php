<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Activity extends Model {
		use SoftDeletes;
		
		protected $table = 'd_activities';
		protected $fillable = [
			'deal_id',
			'person_id',
			'is_cp',
			'user_id',
			'priority',
			'status',
			'notes',
			'date',
			'started_at',
			'finished_at',
		];
		protected $dates = [
			'date',
			'started_at',
			'finished_at',
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function deal() {
			return $this->belongsTo('App\Models\Deal');
		}
		
		public function person() {
			return $this->belongsTo('App\Models\Person');
		}
		
		public function contact() {
			return $this->belongsTo('App\Models\Contact');
		}
		
		public function user() {
			return $this->belongsTo('App\Models\User');
		}
	}
