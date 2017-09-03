<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Deal extends Model {
		use SoftDeletes;
		
		protected $table = 'd_deals';
		protected $fillable = [
			'person_id',
			'product_id',
			'requested_term',
			'approved_term',
			'requested_amount',
			'approved_amount',
			'filling_time',
			'user_agent',
			'ip',
			'bpm_opportunity_id',
			'created_at',
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
		
		public function product() {
			return $this->belongsTo('App\Models\Product');
		}
		
		public function debts() {
			return $this->hasMany('App\Models\Debt');
		}
		
		public function calls() {
			return $this->hasMany('App\Models\Call');
		}
		
		public function promises($limit = 5) {
			return $this->hasMany('App\Models\Promise')->orderBy('id', 'desc')->limit($limit);
		}
		
		public function promise() {
			return $this->hasMany('App\Models\Promise')->orderBy('id', 'desc')->limit(1);
		}
		
		public function statuses() {
			return $this->hasMany('App\Models\DealStatus')->orderBy('id', 'desc')->limit(20);
		}
		
		public function statusMoneySent() {
			$sentStatusId = Directory::where('type', 'deal_status')->where('value', 200)->limit(1)->first()->id;
			return $this->hasMany('App\Models\DealStatus')->where('status_id', $sentStatusId)->limit(1);
		}
		
		public function status() {
			return $this->hasMany('App\Models\DealStatus')->orderBy('id', 'desc')->limit(1);
		}
		
		public function payments() {
			return $this->hasMany('App\Models\Payment');
		}
		
		public function messages() {
			return $this->hasMany('App\Models\Message');
		}
		
		public function activities() {
			return $this->hasMany('App\Models\Activity');
		}
	}
