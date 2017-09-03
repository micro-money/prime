<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Workplace extends Model {
		use SoftDeletes;
		
		protected $table = 'p_workplaces';
		protected $fillable = [
			'person_id',
			'type_id',
			'company',
			'occupation',
			'salary',
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
		
		public function type() {
			return $this->belongsTo('App\Models\Directory')->where('type', 'workplace_type');
		}
	}
