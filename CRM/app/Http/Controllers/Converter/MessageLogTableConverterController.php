<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	
	use App\Models\Converter\zMessageLog;
	
	use App\Models\Directory;
	use App\Models\Person;
	use App\Models\Deal;
	use App\Models\Payment;
	use App\Models\Contact;
	use App\Models\Message;
	
	class MessageLogTableConverterController extends ConverterController {
		public function __construct() {
			parent::__construct();
			$this->statistics['table'] = 'MessageLog';
		}
		
		public function convert($start = 0) {
			foreach (zMessageLog::skip($start)->take($this->chunkSize)->get() as $message) {
				// Skip Cambodia contacts
				if (!($bpmContactId = $message->ContactId) || !($person = Person::where('bpm_contact_id', $bpmContactId)->first())) {
					continue;
				}
				
				// Payments
				$this->messagesFindOrCreate($message, $person);
				
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
		private function messagesFindOrCreate(zMessageLog $message, Person $person) {
			if (($deal = $person->deals()->where('bpm_opportunity_id', $message->OpportunityId)->first()) && ($contact = Contact::where('value', $message->Address)->first())) {
				if (!($deal->messages()->where('sent_at', $message->SendDate)->where('status', 'sent')->first())) {
					$deal->messages()->save(new Message([
						'person_id'  => $person->id,
						'is_cp'      => false,
						'contact_id' => $contact->id,
						'user_id'    => 1,
						'service'    => '',
						'message'    => $message->Text,
						'status'     => 'sent',
						'sent_at' => Carbon::parse($message->SendDate),
						'updated_at' => Carbon::parse($message->SendDate),
					]));
				}
			}
		}
	}