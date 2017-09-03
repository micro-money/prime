<?php
	namespace App\Observers;
	
	use App\Models\Document;
	
	class DocumentObserver {
		/**
		 * Listen to the Document created and updated event.
		 *
		 * @param  Document $document
		 *
		 * @return void
		 */
		public function creating(Document $document) {
			$document = Document::format($document);
		}
		public function updating(Document $document) {
			$document = Document::format($document);
		}
		
		/**
		 * Listen to the Document deleting event.
		 *
		 * @param  Document $document
		 *
		 * @return void
		 */
		public function deleting(Document $document) {
			//
		}
	}