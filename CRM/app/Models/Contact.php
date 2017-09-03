<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	
	class Contact extends Model {
		use SoftDeletes;
		
		protected $table = 'p_contacts';
		protected $fillable = [
			'type',
			'name',
			'value',
			'attempts',
			'successful',
			'active',
		];
		protected $dates = [
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function persons() {
			return $this->belongsToMany('App\Models\Person', 'p_person_contact', 'contact_id', 'person_id');
		}
		
		public function calls() {
			return $this->hasMany('App\Models\Call');
		}
	}
