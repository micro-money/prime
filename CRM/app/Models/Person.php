<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	use Illuminate\Notifications\Notifiable;
	
	class Person extends Model {
		use SoftDeletes, Notifiable;
		
		protected $table = 'p_persons';
		protected $fillable = [
			'name',
			'birth_date',
			'sex',
			'bad',
			'bpm_contact_id',
		];
		protected $dates = [
			'birth_date',
			'created_at',
			'updated_at',
			'deleted_at',
		];
		
		// Relationships
		
		public function groups() {
			return $this->belongsToMany('App\Models\Group', 'p_person_group', 'person_id', 'group_id');
		}
		
		public function connections() {
			return $this->connectionsFrom();
		}
		
		public function connectionsFrom() {
			return $this->belongsToMany('App\Models\Person', 'p_person_connection', 'from_person_id', 'to_person_id')->withPivot('type')->withTimestamps();
		}
		
		public function connectionsTo() {
			return $this->belongsToMany('App\Models\Person', 'p_person_connection', 'to_person_id', 'from_person_id')->withPivot('type')->withTimestamps();
		}
		
		public function contacts($type = null) {
			$contacts = $this->belongsToMany('App\Models\Contact', 'p_person_contact', 'person_id', 'contact_id');
			
			if ($type) {
				$contacts = $contacts->where('type', $type);
			}
			
			return $contacts;
		}
		
		public function phones() {
			return $this->contacts('phone');
		}
		
		public function emails() {
			return $this->contacts('email');
		}
		
		public function addresses() {
			return $this->hasMany('App\Models\Address');
		}
		
		public function documents() {
			return $this->belongsToMany('App\Models\Document', 'p_person_document', 'person_id', 'document_id');
		}
		
		public function workplaces() {
			return $this->hasMany('App\Models\Workplace');
		}
		
		public function deals() {
			return $this->hasMany('App\Models\Deal');
		}
		
		public function messages() {
			return $this->hasMany('App\Models\Message');
		}
		
		public function activities() {
			return $this->hasMany('App\Models\Activity')->orderBy('created_at','desc');
		}
	}
