<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zActivity extends Model {
		protected $connection = 'zsync';
		protected $table = 'zsync_Activity';
		
		protected $dates = [
			'StartDate',
		    'DueDate',
		];
	}
