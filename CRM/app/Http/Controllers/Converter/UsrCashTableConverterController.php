<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	use CitiesSeeder;
	
	use App\Models\Converter\zUsrCash;
	
	use App\Models\Directory;
	use App\Models\Person;
	use App\Models\Deal;
	use App\Models\Payment;
	
	class UsrCashTableConverterController extends ConverterController {
		public function __construct() {
			parent::__construct();
			$this->statistics['table'] = 'UsrCash';
		}
		
		public function convert($start = 0) {
			foreach (zUsrCash::with(['opportunity'])->skip($start)->take($this->chunkSize)->get() as $cash) {
				// Skip Cambodia contacts
				if (($cash->BranchId != $this->branchId) || !($bpmContactId = $cash->UsrBorrowerId) || !($person = Person::where('bpm_contact_id', $bpmContactId)->first())) {
					continue;
				}
				
				// Payments
				$this->paymentsFindOrCreate($cash, $person);
				
				// Log
				$this->statistics['downloaded']++;
			}
			
			// Log
			$this->statistics['finished_at'] = time();
			
			return $this->statistics;
		}
		
		/**
		 * Creates payments for each person & deal
		 *
		 * @param zLead $lead
		 * @param Person $person
		 */
		private function paymentsFindOrCreate(zUsrCash $cash, Person $person) {
			if (($cash->UsrOperationDate) && ($deal = $person->deals()->where('bpm_opportunity_id', $cash->UsropportunityId)->first())) {
				if (!($deal->payments()->where('created_at', $cash->UsrOperationDate)->first())) {
					$deal->payments()->save(new Payment([
						'person_id'  => $person->id,
						'user_id'    => 1,
						'amount'     => $cash->UsrAmount,
						'created_at' => Carbon::parse($cash->UsrOperationDate),
					]));
				}
			}
		}
	}