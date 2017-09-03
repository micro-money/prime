<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Connection extends Model {
		use SoftDeletes;
		
		protected $table = 'p_person_connection';
		protected $fillable = [
			'from_person_id',
			'to_person_id',
			'type',
		];
		protected $dates = [
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function from() {
			return $this->belongsTo('App\Models\Person', 'from_person_id', 'id');
		}
		
		public function to() {
			return $this->belongsTo('App\Models\Person', 'to_person_id', 'id');
		}
	}
