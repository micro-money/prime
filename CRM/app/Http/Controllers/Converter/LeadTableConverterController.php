<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	use CitiesSeeder;
	
	use App\Models\Converter\zSync;
	use App\Models\Converter\zLead;
	
	use App\Models\Directory;
	use App\Models\Person;
	use App\Models\Deal;
	
	class LeadTableConverterController extends ConverterController {
		public function __construct() {
			parent::__construct();
			$this->statistics['table'] = 'Lead';
		}
		
		public function convert($start = 0) {
			foreach (zLead::with(['opportunity'])->skip($start)->take($this->chunkSize)->get() as $lead) {
				// Skip Cambodia contacts
				if (($lead->BranchId != $this->branchId) || !($bpmContactId = $lead->QualifiedContactId) || !($person = Person::where('bpm_contact_id', $bpmContactId)->first())) {
					continue;
				}
				
				// Address
				$this->updateAddresses($lead, $person);
				
				// Deal
				$this->updateDeals($lead);
				
				// Log
				$this->statistics['downloaded']++;
			}
			
			// Log
			$this->statistics['finished_at'] = time();
			return $this->statistics;
		}
		
		/**
		 * Sets address for each person
		 *
		 * @param zLead $lead
		 * @param Person $person
		 */
		private function updateAddresses(zLead $lead, Person $person) {
			if (!empty($lead->CityId) && !empty($lead->UsrStreet) && isset(CitiesSeeder::$cities[$lead->CityId])) {
				$city = Directory::where('type', 'city')->where('name', CitiesSeeder::$cities[$lead->CityId])->first();
				if ($city) {
					$existentAddresses = $person->addresses()->where('city_id', $city->id)->whereNull('address');
					if ($existentAddressesCount = $existentAddresses->count()) {
						$existentAddresses->update(['address' => $lead->UsrStreet]);
					}
					
					// Log
					$this->statistics['updated'] += $existentAddressesCount;
				}
			}
		}
		
		private function updateDeals(zLead $lead) {
			if (!empty($lead->UsrBrowser) && ($opportunity = $lead->opportunity)) {
				$deal = Deal::where('bpm_opportunity_id', $opportunity->Id)->first();
				if ($deal) {
					$deal->filling_time = !empty($lead->UsrRegistrationDurationSec) && ($lead->UsrRegistrationDurationSec < 3600) ? $lead->UsrRegistrationDurationSec : null;
					$deal->user_agent = mb_substr($lead->UsrBrowser, 0, 191);
					$deal->ip = !empty($lead->UsrIp) ? $lead->UsrIp : null;
					$deal->save();
					
					// Log
					$this->statistics['updated']++;
				}
			}
		}
	}