<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	
	use CitiesSeeder;
	use App\Models\Converter\zContact;
	use App\Models\Converter\zSync;
	
	use App\Models\Person;
	use App\Models\Contact;
	use App\Models\Address;
	use App\Models\Document;
	use App\Models\Directory;
	use App\Models\Workplace;
	
	use App\Logic\Validators\ContactsValidator;
	
	class ContactTableConverterController extends ConverterController {
		public function __construct() {
			parent::__construct();
			$this->statistics['table'] = 'Contact';
		}
		
		public function convert($start = 0) {
			foreach (zContact::skip($start)->take($this->chunkSize)->get() as $contact) {
				// Skip Cambodia contacts
				if ($contact->BranchId != $this->branchId) {
					continue;
				}
				
				// Person
				$person = $this->personFindOrCreate($contact);
				
				// Contacts
				$this->contactsFindOrCreate($contact, $person);
				
				// Documents
				$this->documentsFindOrCreate($contact, $person);
				
				// Addresses
				$this->addressesFindOrCreate($contact, $person);
				
				// Workplaces
				$this->workplacesFindOrCreate($contact, $person);
				
				// Log
				$this->statistics['downloaded']++;
			}
			
			// Log
			$this->statistics['finished_at'] = time();
			return $this->statistics;
		}
		
		/**
		 * Finds or creates new person
		 *
		 * @param zContact $contact
		 * @return Person
		 */
		private function personFindOrCreate(zContact $contact) {
			$birthDate = Carbon::parse($contact->BirthDate);
			
			// Check existance
			if (!($person = Person::where('bpm_contact_id', $contact->Id)->first())) {
				// Person add
				$person = Person::create([
					'bpm_contact_id' => $contact->Id,
					'name'           => $contact->Name,
					'birth_date'     => (($birthDate->year < 2000) && ($birthDate->age < 100) && ($birthDate->timestamp != 0)) ? $birthDate->timestamp : null,
					'sex'            => ($contact->GenderId == 'fc2483f8-65b6-df11-831a-001d60e938c6') ? 1 : 0,
					'bad'            => ($contact->UsrBadGuy == 'True') ? 1 : 0,
				]);
				
				// Log
				$this->statistics['created']++;
			}
			
			return $person;
		}
		
		/**
		 * Finds or creates contacts for contact
		 *
		 * @param zContact $contact
		 * @param Person $person
		 */
		private function contactsFindOrCreate(zContact $contact, Person $person) {
			$personContacts = [];
			if (!empty($contact->UsrLocalFullName)) {
				if (!$person->contacts()->where('type', 'local_name')->where('value', mb_substr($contact->UsrLocalFullName, 0, 128))->first()) {
					$personContacts[] = new Contact([
						'type'  => 'local_name',
						'value' => mb_substr($contact->UsrLocalFullName, 0, 128),
					]);
				}
			}
			if (!empty($contact->Email)) {
				$email = ContactsValidator::email(mb_substr(trim($contact->Email), 0, 128));
				if ($email && !$person->contacts()->where('type', 'email')->where('value', $email)->first()) {
					$personContacts[] = new Contact([
						'type'  => 'email',
						'value' => $email,
						'name'  => $person,
					]);
				}
			}
			if (!empty($contact->UsrPhones)) {
				$phones = explode(',', $contact->UsrPhones);
				foreach ($phones as $phone) {
					$phone = ContactsValidator::phone(mb_substr(trim($phone), 0, 128));
					if ($phone && !$person->contacts()->where('type', 'phone')->where('value', $phone)->first()) {
						$personContacts[] = new Contact([
							'type'  => 'phone',
							'value' => $phone,
							'name'  => $person,
						]);
					}
				}
			}
			if (!empty($contact->Facebook)) {
				$facebook = ContactsValidator::facebook(mb_substr(trim($contact->Facebook), 0, 128));
				if ($facebook && !$person->contacts()->where('type', 'facebook')->where('value', $facebook)->first()) {
					$personContacts[] = new Contact([
						'type'  => 'facebook',
						'value' => $facebook,
						'name'  => $person,
					]);
				}
			}
			if (!empty($personContacts)) {
				$person->contacts()->saveMany($personContacts);
				
				// Log
				$this->statistics['created'] += count($personContacts);
			}
		}
		
		/**
		 * Finds or creates documents for contact
		 *
		 * @param zContact $contact
		 * @param Person $person
		 */
		private function documentsFindOrCreate(zContact $contact, Person $person) {
			$personDocuments = [];
			if (!empty($contact->UsrMMPersonalID)) {
				if (!$person->documents()->where('type', 'nrc')->where('value', trim($contact->UsrMMPersonalID))->first()) {
					$personDocuments[] = new Document([
						'type'  => 'nrc',
						'value' => trim($contact->UsrMMPersonalID),
					]);
				}
			}
			if (!empty($contact->UsrPaySystemAccount)) {
				if (!Document::where('type', 'bank_account')->where('value', trim($contact->UsrPaySystemAccount))->first()) {
					$personDocuments[] = new Document([
						'type'  => 'bank_account',
						'value' => trim($contact->UsrPaySystemAccount),
					]);
				}
			}
			if (!empty($personDocuments)) {
				$person->documents()->saveMany($personDocuments);
			}
		}
		
		/**
		 * Finds or creates addresses for contact
		 *
		 * @param zContact $contact
		 * @param Person $person
		 */
		private function addressesFindOrCreate(zContact $contact, Person $person) {
			$personAddresses = [];
			if (!empty($contact->CityId)) {
				$cityName = isset(CitiesSeeder::$cities[$contact->CityId]) ? CitiesSeeder::$cities[$contact->CityId] : false;
				if ($cityName) {
					$cityId = Directory::where('name', $cityName)->first()->id;
					if ($cityId && !$person->addresses()->where('city_id', $cityId)->first()) {
						$personAddresses[] = new Address([
							'city_id' => $cityId,
						]);
					}
				}
			}
			if (!empty($personAddresses)) {
				$person->addresses()->save($personAddresses[0]);
			}
		}
		
		/**
		 * Finds or creates workplaces for contact
		 *
		 * @param zContact $contact
		 * @param Person $person
		 */
		private function workplacesFindOrCreate(zContact $contact, Person $person) {
			$personWorkplaces = [];
			if (!empty($contact->JobTitle)) {
				if (!$person->workplaces()->where('occupation', trim($contact->JobTitle))->first()) {
					$personWorkplaces[] = new Workplace([
						'occupation' => trim($contact->JobTitle),
					]);
				}
			}
			if (!empty($personWorkplaces)) {
				$person->workplaces()->saveMany($personWorkplaces);
			}
		}
	}