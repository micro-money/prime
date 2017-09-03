<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Group extends Model {
		use SoftDeletes;
		
		protected $table = 'p_documents';
		protected $fillable = [
			'type',
			'name',
			'bad',
		];
		protected $dates = ['deleted_at'];
		
		// Relationships
		
		public function persons() {
			return $this->belongsToMane('App\Models\Person', 'p_person_group', 'group_id', 'person_id');
		}
	}
