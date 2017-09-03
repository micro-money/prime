<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	
	use App\Models\Converter\zPrimaryContact;
	
	use App\Models\Person;
	use App\Models\Contact;
	use App\Models\Connection;
	
	use App\Logic\Validators\ContactsValidator;
	
	class PrimaryContactTableConverterController extends ConverterController {
		public function __construct() {
			parent::__construct();
			$this->statistics['table'] = 'UsrPrimaryContact';
		}
		
		public function convert($start = 0) {
			foreach (zPrimaryContact::with('lead')->orderBy('CreatedOn', 'desc')->skip($start)->take($this->chunkSize)->get() as $primaryContact) {
				// Finds person which will be parent to this contact person
				$primaryContact->UsrPhone = ContactsValidator::phone(mb_substr(trim($primaryContact->UsrPhone), 0, 128));
				if ($primaryContact->UsrPhone && $primaryContact->UsrContactName && $primaryContact->lead->QualifiedContactId) {
					$personFrom = Person::where('bpm_contact_id', $primaryContact->lead->QualifiedContactId)->first();
					$connectionsFromIds = Connection::where('from_person_id', $personFrom->id)->get()->pluck('to_person_id');
					$connectionsFrom = Person::whereIn('id', $connectionsFromIds)->get();
					
					// Finds or creates new person with
					$personTo = $this->personFindOrCreate($primaryContact, $personFrom, $connectionsFrom);
					
					// Adding connection
					if ($personTo) {
						$personFrom->connectionsFrom()->attach($personTo, ['type' => 'Contact person']);
					}
				}
				
				// Log
				$this->statistics['downloaded']++;
			}
			
			// Log
			$this->statistics['finished_at'] = time();
			return $this->statistics;
		}
		
		private function personFindOrCreate(zPrimaryContact $primaryContact, Person $personFrom, $connectionsFrom) {
			// Prepare name
			$primaryContact->UsrContactName = !empty(trim($primaryContact->UsrContactName)) ? $primaryContact->UsrContactName : 'Unknown';
			
			// Get contacts phones
			$connectionsFromContacts = [];
			foreach ($connectionsFrom as $connectionFrom) {
				$connectionsFromContacts[] = $connectionFrom->phones()->first()->value;
			}
			
			// Check existance
			if (!count($connectionsFrom) || !$connectionsFrom->where('name', $primaryContact->UsrContactName)->first() || (in_array($primaryContact->UsrPhone, $connectionsFromContacts) === false)) {
				// Person add
				$personTo = Person::create([
					'bpm_contact_id' => null,
					'name'           => $primaryContact->UsrContactName,
					'birth_date'     => null,
					'sex'            => null,
					'bad'            => ($personFrom->bad == true) ? 1 : 0,
				]);
				
				// Phone add
				if (!($personToContact = Contact::where('type', 'phone')->where('value', $primaryContact->UsrPhone)->first())) {
					$personToContact = Contact::create([
						'type'  => 'phone',
						'value' => $primaryContact->UsrPhone,
					]);
				}
				
				// Attaching
				$personTo->contacts()->attach($personToContact);
				
				// Log
				$this->statistics['created'] += 2;
				
				return $personTo;
			} else {
				return false;
			}
		}
	}