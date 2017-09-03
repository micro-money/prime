<?php
	namespace App\Observers;
	
	use App\Models\Contact;
	use App\Models\Person;
	
	use App\Logic\Validators\ContactsValidator;
	
	class ContactObserver {
		/**
		 * Listen to the Document created and updated event.
		 *
		 * @param  Contact $contact
		 *
		 * @return void
		 */
		public function creating(Contact $contact) {
			// Skip local names
			if ($contact->type == 'local_name') {
				return true;
			}
			
			// Email
			if ($contact->type == 'email') {
				$contact->value = ContactsValidator::email($contact->value);
				if ($contact->value === false) return false;
			}
			
			// Phone - only digits
			if ($contact->type == 'phone') {
				$contact->value = ContactsValidator::phone($contact->value);
				if ($contact->value === false) return false;
			}
			
			// Facebook
			if ($contact->type == 'facebook') {
				$contact->value = ContactsValidator::facebook($contact->value);
				if ($contact->value === false) return false;
			}
			
			// If it founds duplicate – attaches old contact to the new person
			if (gettype($contact->name) !== 'string') {
				$personNew = $contact->name;
				$contact->name = null;
				if ($contactOld = Contact::where('type', $contact->type)->where('value', $contact->value)->first()) {
					$personNew->contacts()->attach($contactOld);
					
					return false;
				}
			}
		}
		
		public function updating(Contact $contact) {
			return false;
		}
		
		/**
		 * Listen to the Document deleting event.
		 *
		 * @param  Contact $contact
		 *
		 * @return void
		 */
		public function deleting(Contact $contact) {
			//
		}
	}