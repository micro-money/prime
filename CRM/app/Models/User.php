<?php
	
	namespace App\Models;
	
	use Zizaco\Entrust\Traits\EntrustUserTrait;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	
	class User extends Authenticatable {
		protected $table = 'c_users';
		
		use Notifiable;
		use EntrustUserTrait;
		
		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array
		 */
		protected $fillable = [
			'name',
			'email',
			'password',
		];
		
		/**
		 * The attributes that should be hidden for arrays.
		 *
		 * @var array
		 */
		protected $hidden = [
			'password',
			'remember_token',
		];
		
		// Relationships
		
		public function messages() {
			return $this->hasMany('App\Models\Message');
		}
		
		public function activities() {
			return $this->hasMany('App\Models\Activity')->orderBy('created_at','desc');
		}
		
		public function role() {
			return $this->belongsToMany('App\Models\Role', 'c_role_user');
		}
	}
