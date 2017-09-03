<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Currency extends Model {
		protected $table = 'c_currencies';
		protected $fillable = [
			'name',
			'code',
			'sign',
			'before_value',
			'active',
		];
		
		public function rates() {
			return $this->hasMany('App\Models\CurrencyRate')->orderBy('updated_at', 'desc');
		}
		
		public function rate() {
			return $this->hasMany('App\Models\CurrencyRate')->orderBy('updated_at', 'desc')->first();
		}
	}
