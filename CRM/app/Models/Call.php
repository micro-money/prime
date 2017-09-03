<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Call extends Model {
		use SoftDeletes;
		
		protected $table = 'd_calls';
		protected $fillable = [
			'person_id',
			'deal_id',
			'contact_id',
			'user_id',
			'type_id',
			'status_id',
			'notes',
			'started_at',
			'finished_at',
		];
		protected $dates = [
			'started_at',
			'finished_at',
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
		
		public function contact() {
			return $this->belongsTo('App\Models\Contact');
		}
		
		public function user() {
			return $this->belongsTo('App\Models\User');
		}
		
		public function type() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'call_type');
		}
		
		public function status() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'call_status');
		}
		
		public function promises() {
			return $this->hasMany('App\Models\Promise');
		}
	}
