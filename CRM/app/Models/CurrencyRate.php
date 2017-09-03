<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class CurrencyRate extends Model {
		protected $table = 'c_currency_rate';
		protected $fillable = [
			'currency_id',
			'value',
		];
		
		public function currency() {
			return $this->belongsTo('App\Models\Currency');
		}
	}
