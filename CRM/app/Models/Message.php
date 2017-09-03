<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Message extends Model {
		use SoftDeletes;
		
		protected $table = 'd_messages';
		protected $fillable = [
			'deal_id',
			'person_id',
			'is_cp',
			'contact_id',
			'user_id',
			'service',
			'message',
			'status',
			'sent_at',
			'delivered_at',
			'created_at',
			'updated_at',
		];
		protected $dates = [
			'sent_at',
			'delivered_at',
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
