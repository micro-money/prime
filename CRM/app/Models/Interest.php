<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Interest extends Model {
		protected $table = 'c_interest_type';
		protected $fillable = [
			'id',
			'name',
		];
		protected $dates = [
			'created_at',
			'updated_at',
		];
	}
