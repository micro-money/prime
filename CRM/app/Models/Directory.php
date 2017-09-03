<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Directory extends Model {
		protected $table = 'c_directory';
		protected $fillable = [
			'parent_id',
			'type',
			'name',
			'value',
		];
		
		public function parent() {
			return $this->belongsTo('App\Models\Directory', 'id', 'parent_id');
		}
		
		public function childs() {
			return $this->hasMany('App\Models\Directory', 'parent_id', 'id');
		}
	}
