<?php
	
	namespace App\Http\Controllers\Debug;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Auth;
	
	use App\Models\Person;
	use App\Models\Contact;
	use App\Models\Document;
	
	class DebugController {
		/**
		 * Clients list
		 *
		 * @return int
		 */
		public function clientsList(Request $request) {
			$persons = Person::with(['documents', 'emails', 'phones', 'workplaces', 'addresses.city']);
			
			// Search
			$searchPhone = $request->get('phone');
			if (!empty($searchPhone)) {
				$personsId = Contact::where('value', 'like', $searchPhone.'%')->where('type', 'phone')->join('p_person_contact', 'p_contacts.id', '=', 'p_person_contact.contact_id')->pluck('person_id');
				$persons = $persons->whereIn('id', $personsId);
			}
			
			$searchNRC = $request->get('nrc');
			if (!empty($searchNRC)) {
				$personsId = Document::where('value', 'like', '%'.$searchNRC.'%')->join('p_person_document', 'p_documents.id', '=', 'p_person_document.document_id')->pluck('person_id');
				$persons = $persons->whereIn('id', $personsId);
			}
			
			// Pagination
			$persons = $persons->paginate(30);
			
			return view('console.pages.debug.clients-list')->with(compact(['persons']));
		}
		
		public function activitiesList() {
			return 'Not used';
		}
	}
