<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	
	use App\Models\Converter\zSync;
	use App\Models\Converter\zOpportunity;
	
	use App\Models\Person;
	use App\Models\Deal;
	use App\Models\Debt;
	use App\Models\Promise;
	use App\Models\DealStatus;
	
	class OpportunityTableConverterController extends ConverterController {
		private $stagesToStatuses;
		
		public function __construct() {
			parent::__construct();
			$this->statistics['table'] = 'Opportunity';
			
			$this->stagesToStatuses = [
				'01e5a907-9f6b-4f98-9dd9-167a88f89b67' => 13,
				'2169fa9d-1c60-44a8-be69-dbfa499ccc3f' => 14,
				'736f54fd-e240-46f8-8c7c-9066c30aff59' => 15,
				'25673a3d-fc75-47c6-ad3a-92f40ade17c6' => 16,
				'e3021947-1ab2-40ea-a306-54b1dabec09e' => 17,
				'a0aa51e6-b380-4097-b8d7-125fd2292e8b' => 18,
				'f829fd6f-acd1-4b1a-81a0-6b63904f7008' => 19,
				'dc67661b-28bb-4d53-bb60-d2a9ae2dc842' => 20,
				'449e34f7-b97e-4edd-b690-48de8e028651' => 21,
				'd689d0d3-6d4c-4e41-be57-283aab94adb8' => 22,
				'a9b307fc-04a4-4094-92d9-d9b23132bbb0' => 23,
				'4fde2374-ddc8-469a-95e8-1a91048b9ae5' => 24,
				'60d5310c-5be6-df11-971b-001d60e938c6' => 25,
			];
		}
		
		public function convert($start = 0) {
			foreach (zOpportunity::skip($start)->take($this->chunkSize)->get() as $opportunity) {
				// Skip Cambodia contacts and with null money sent date
				if (($opportunity->BranchId != $this->branchId) || (!$opportunity->UsrMoneySendedDate) || (strpos($opportunity->UsrMoneySendedDate, '20') === false) || (strpos($opportunity->UsrCollectionDate, '20') === false) || !($person = Person::with('deals.statuses')->where('bpm_contact_id', $opportunity->ContactId)->first())) {
					continue;
				}
				
				// Preparing common info
				$opportunity->UsrMoneySendedDate = Carbon::parse($opportunity->UsrMoneySendedDate);
				
				// Deal
				$deal = $this->dealsFindOrCreate($opportunity, $person);
				
				// Debts
				$this->debtsFindOrCreate($opportunity, $person, $deal);
				
				// Promises
				$this->promisesFindOrCreate($opportunity, $person, $deal);
				
				// Statuses
				$this->statusesFindOrCreate($opportunity, $person, $deal);
				
				// Log
				$this->statistics['downloaded']++;
			}
			
			// Log
			$this->statistics['finished_at'] = time();
			return $this->statistics;
		}
		
		/**
		 * Finds or creates deals for a person
		 *
		 * @param zOpportunity $opportunity
		 * @param Person $person
		 */
		private function dealsFindOrCreate(zOpportunity $opportunity, Person $person) {
			if (!($deal = $person->deals()->where('bpm_opportunity_id', $opportunity->Id)->first())) {
				$deal = Deal::create([
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
					'updated_at'         => $opportunity->UsrMoneySendedDate,
				]);
				
				// Log
				$this->statistics['created']++;
			}
			
			return $deal;
		}
		
		/**
		 * Finds and updates or creates a new debts
		 *
		 * @param zOpportunity $opportunity
		 * @param Person $person
		 * @param Deal $deal
		 */
		private function debtsFindOrCreate(zOpportunity $opportunity, Person $person, Deal $deal) {
			$newDebts = [];
			$existentDebts = Debt::where('deal_id', $deal->id)->limit(3)->get();
			
			// Principal
			if (!($debtPrincipal = $existentDebts->where('product_interest_id', 1)->first())) {
				$newDebts[] = new Debt([
					'product_interest_id' => 1,
					'amount'              => $opportunity->UsrPrincipal,
					'created_at'          => $opportunity->UsrMoneySendedDate,
				]);
			} else {
				if (floatval($debtPrincipal->amount) != floatval($opportunity->UsrPrincipal)) {
					// Update
					$debtPrincipal->amount = $opportunity->UsrPrincipal;
					$debtPrincipal->save();
					
					// Log
					$this->statistics['updated']++;
				}
			}
			
			// Main interest
			if (!($debtMainInterest = $existentDebts->where('product_interest_id', 2)->first())) {
				$newDebts[] = new Debt([
					'product_interest_id' => 2,
					'amount'              => $opportunity->UsrMainInterest,
				]);
			} else {
				if (floatval($debtMainInterest->amount) != floatval($opportunity->UsrMainInterest)) {
					// Update
					$debtMainInterest->amount = $opportunity->UsrMainInterest;
					$debtMainInterest->save();
					
					// Log
					$this->statistics['updated']++;
				}
			}
			
			// Overdue interest
			if (!($debtOverdueInterest = $existentDebts->where('product_interest_id', 3)->first())) {
				$newDebts[] = new Debt([
					'product_interest_id' => 3,
					'amount'              => $opportunity->UsrOverdueInterest,
				]);
			} else {
				if (floatval($debtOverdueInterest->amount) != floatval($opportunity->UsrOverdueInterest)) {
					// Update
					$debtOverdueInterest->amount = $opportunity->UsrOverdueInterest;
					$debtOverdueInterest->save();
					
					// Log
					$this->statistics['updated']++;
				}
			}
			
			// Save
			$deal->debts()->saveMany($newDebts);
			
			// Log
			$this->statistics['created'] += count($newDebts);
		}
		
		/**
		 * Finds or creates initial promise for each deal
		 *
		 * @param zOpportunity $opportunity
		 * @param Person $person
		 * @param Deal $deal
		 */
		private function promisesFindOrCreate(zOpportunity $opportunity, Person $person, Deal $deal) {
			$newPromises = [];
			$existentPromises = Promise::where('deal_id', $deal->id)->get();
			
			if (!$existentPromises->where('type', 'initial_promise')->first()) {
				$newPromises[] = new Promise([
					'deal_id'   => $deal->id,
					'person_id' => $person->id,
					'type'      => 'initial_promise',
					'amount'    => $opportunity->UsrMoneySendedAmount,
					'date'      => Carbon::parse($opportunity->UsrCollectionDate),
					'status_id' => 32,	// Failed ???
				]);
			}
			
			// Save
			$deal->promises()->saveMany($newPromises);
			
			// Log
			$this->statistics['created'] += count($newPromises);
		}
		
		/**
		 * Finds or creates statuses
		 *
		 * @param zOpportunity $opportunity
		 * @param Person $person
		 * @param Deal $deal
		 */
		private function statusesFindOrCreate(zOpportunity $opportunity, Person $person, Deal $deal) {
			$newStatuses = [];
			$existentStatuses = DealStatus::where('deal_id', $deal->id)->get();
			
			// Lead
			if (!$existentStatuses->where('status_id', 13)->first()) {
				$newStatuses[] = new DealStatus([
					'user_id'   => 1,
					'status_id' => 13,
				]);
			}
			
			// Money sent
			if (!$existentStatuses->where('status_id', 18)->first()) {
				$newStatuses[] = new DealStatus([
					'user_id'    => 1,
					'status_id'  => 18,
					'created_at' => $opportunity->UsrMoneySendedDate,
				]);
			}
			
			// Final status
			$statusId = $this->stagesToStatuses[$opportunity->StageId];
			if (!$existentStatuses->where('status_id', $statusId)->first()) {
				$newStatuses[] = new DealStatus([
					'user_id'    => 1,
					'status_id'  => $statusId,
				]);
			}
			
			// Save
			$deal->statuses()->saveMany($newStatuses);
			
			// Log
			$this->statistics['created'] += count($newStatuses);
		}
	}