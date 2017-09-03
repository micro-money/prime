<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;
	use PhpParser\Comment\Doc;
	
	class Document extends Model {
		use SoftDeletes;
		
		protected $table = 'p_documents';
		protected $fillable = [
			'type',
			'name',
			'value',
			'file_id',
		];
		protected $dates = ['deleted_at'];
		
		// Relationships
		
		public function persons() {
			return $this->belongsToMany('App\Models\Person', 'p_person_document', 'document_id', 'person_id');
		}
		
		public function file() {
			//return $this->belongsTo('App\Models\File');
		}
		
		// Mutators
		
		// Formatters
		
		public static function format(Document $document) {
			if ($document->type == 'bank account') {
				if (strpos($document->value, 'CB') !== false) {
					$document->name = 'CB';
				} elseif (strpos($document->value, 'OK') !== false) {
					$document->name = 'OK$';
				} elseif (strpos($document->value, 'KBZ') !== false) {
					$document->name = 'KBZ';
				} elseif (strpos($document->value, 'AYA') !== false) {
					$document->name = 'AYA';
				}
				
				$document->value = preg_replace('/[^0-9]/', '', $document->value);
			}
			
			return $document;
		}
	}
