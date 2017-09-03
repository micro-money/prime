<?php
	
	namespace App\Models\Converter;
	
	use Illuminate\Database\Eloquent\Model;
	
	class zSync extends Model {
		protected $table = 'c_syncs';
		protected $fillable = [
			'table',
			'downloaded',
			'created',
			'updated',
			'started_at',
			'finished_at',
		];
		protected $dates = [
			'started_at',
			'finished_at',
			'created_at',
			'updated_at',
		];
	}
