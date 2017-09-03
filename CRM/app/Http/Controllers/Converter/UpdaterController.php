<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use App\Http\Controllers\Controller;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Http\Request;
	
	use App\Models\Converter\zContact;
	use App\Models\Converter\zOpportunity;
	use App\Models\Converter\zLead;
	use App\Models\Person;
	use App\Models\Contact;
	use App\Models\Address;
	use CitiesSeeder;
	use App\Models\Directory;
	use App\Models\Document;
	use App\Models\Workplace;
	use App\Models\Deal;
	use App\Models\Converter\zSync;
	
	class UpdaterController extends Controller {
		private $branchId = '7ffcfa45-b517-441c-86f0-808eaab4dd11';
		private $chunkSize = 1000;
		
		/**
		 * Converts zsync_Contact table into Laravel's new structure
		 *
		 * @return int
		 */
		public function updateContactTable($start = 0) {
			$lastUpdateDate = $this->getLastUpdateTimestamp('lead');
			$rowsDownloaded = $rowsCreated = $rowsUpdated = 0;
			foreach (zContact::where('BranchId', $this->branchId)->where('ModifiedOn', '>', $lastUpdateDate->toDateTimeString())->with(['opportunity'])->orderBy('ModifiedOn', 'asc')->skip($start)->take($this->chunkSize)->get() as $contact) {
				// Count row
				$rowsDownloaded++;
				
				// Person
				$birthDate = Carbon::parse($contact->BirthDate);
				$person = Person::create([
					'bpm_contact_id' => $contact->Id,
					'name'           => $contact->Name,
					'birth_date'     => (($birthDate->year < 2000) && ($birthDate->age < 100) && ($birthDate->timestamp != 0)) ? $birthDate->timestamp : null,
					'sex'            => ($contact->GenderId == 'fc2483f8-65b6-df11-831a-001d60e938c6') ? 1 : 0,
					'bad'            => ($contact->UsrBadGuy == 'True') ? 1 : 0,
				]);
				
				// Contacts
				$personContacts = [];
				if (!empty($contact->UsrLocalFullName)) {
					$personContacts[] = new Contact([
						'type'  => 'local_name',
						'value' => mb_substr($contact->UsrLocalFullName, 0, 128),
					]);
				}
				if (!empty($contact->Email) && (strpos($contact->Email, '@') !== false)) {
					$personContacts[] = new Contact([
						'type'  => 'email',
						'value' => mb_substr($contact->Email, 0, 128),
						'name'  => $person,
					]);
				}
				if (!empty($contact->UsrPhones)) {
					$phones = explode(',', $contact->UsrPhones);
					foreach ($phones as $phone) {
						$personContacts[] = new Contact([
							'type'  => 'phone',
							'value' => mb_substr(trim($phone), 0, 128),
							'name'  => $person,
						]);
					}
				}
				if (!empty($contact->Facebook)) {
					$personContacts[] = new Contact([
						'type'  => 'facebook',
						'value' => substr(trim($contact->Facebook), 0, 128),
						'name'  => $person,
					]);
				}
				if (!empty($personContacts)) {
					$person->contacts()->saveMany($personContacts);
				}
				
				// Addresses
				$personAddresses = [];
				if (!empty($contact->CityId)) {
					$cityName = isset(CitiesSeeder::$cities[$contact->CityId]) ? CitiesSeeder::$cities[$contact->CityId] : false;
					if ($cityName) {
						$personAddresses[] = new Address([
							'city_id' => Directory::where('name', $cityName)->first()->id,
						]);
					}
				}
				if (!empty($personAddresses)) {
					$person->address()->save($personAddresses[0]);
				}
				
				// Documents
				$personDocuments = [];
				if (!empty($contact->UsrMMPersonalID)) {
					$personDocuments[] = new Document([
						'type'  => 'nrc',
						'value' => trim($contact->UsrMMPersonalID),
					]);
				}
				if (!empty($contact->UsrPaySystemAccount)) {
					$personDocuments[] = new Document([
						'type'  => 'bank account',
						'value' => trim($contact->UsrPaySystemAccount),
					]);
				}
				if (!empty($personDocuments)) {
					$person->documents()->saveMany($personDocuments);
				}
				
				// Workplaces
				$personWorkplaces = [];
				if (!empty($contact->JobTitle)) {
					$person->workplaces()->saveMany([
						new Workplace([
							'occupation' => trim($contact->JobTitle),
						]),
					]);
				}
				
				// Save update time
				$lastUpdateDate = $lead->ModifiedOn;
			}
			
			return [
				'downloaded' => $rowsDownloaded,
				'created'    => $rowsCreated,
				'updated'    => $rowsUpdated,
				'date'       => $lastUpdateDate,
			];
		}
		
		public function updateOpportunityTable($start = 0) {
			foreach (zOpportunity::skip($start)->take($this->chunkSize)->get() as $opportunity) {
				// Skip Cambodia contacts
				if ($opportunity->BranchId != '7ffcfa45-b517-441c-86f0-808eaab4dd11') {
					continue;
				}
				
				// Creating deals
				$bpm_contact_id = $opportunity->ContactId;
				if ($person = Person::where('bpm_contact_id', $bpm_contact_id)->first()) {
					Deal::create([
						'person_id'          => $person->id,
						'product_id'         => 1,
						'requested_term'     => $opportunity->UsrTerm < 100 ? $opportunity->UsrTerm : null,
						'approved_term'      => $opportunity->UsrApprovedTerm,
						'requested_amount'   => $opportunity->UsrAmount,
						'approved_amount'    => $opportunity->UsrMoneySendedAmount,
						'filling_time'       => null,
						'user_agent'         => null,
						'ip'                 => null,
						'bpm_opportunity_id' => $opportunity->Id,
					]);
				}
			}
		}
		
		public function updateLeadTable($start = 0) {
			$lastUpdateDate = $this->getLastUpdateTimestamp('lead');
			$rowsDownloaded = $rowsCreated = $rowsUpdated = 0;
			foreach (zLead::where('BranchId', $this->branchId)->where('ModifiedOn', '>', $lastUpdateDate->toDateTimeString())->with(['opportunity'])->orderBy('ModifiedOn', 'asc')->skip($start)->take($this->chunkSize)->get() as $lead) {
				// Count row
				$rowsDownloaded++;
				
				
				// Save update time
				$lastUpdateDate = $lead->ModifiedOn;
			}
			
			return [
				'downloaded' => $rowsDownloaded,
				'created'    => $rowsCreated,
				'updated'    => $rowsUpdated,
				'date'       => $lastUpdateDate,
			];
		}
		
		/**
		 * Get the last time of updating
		 *
		 * @return Carbon
		 */
		public function getLastUpdateTimestamp($table) {
			if ($last = zSync::where('table', $table)->orderBy('last_row_updated_at', 'desc')->first()) {
				$last = $last->last_row_updated_at;
			} else {
				$last = Carbon::now()->subHour();
			}
			
			return $last;
		}
	}