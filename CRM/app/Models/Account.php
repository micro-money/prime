<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Account extends Model {
		use SoftDeletes;
		
		protected $table = 'c_accounts';
		protected $fillable = [
			'name',
			'currency_id',
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
	}
