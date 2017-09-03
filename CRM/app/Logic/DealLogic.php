<?php
	
	namespace App\Logic;
	
	use Carbon\Carbon;
	
	use App\Models\Deal;
	
	class DealLogic extends Logic {
		/**
		 * Calculates debt for a deal
		 *
		 * @param Deal $deal
		 * @return Deal
		 */
		public static function calculateDebts(Deal $deal) {
			// Total debt - BPM
			$deal->total_debt = $deal->debts->sum('amount');
			
			// Returned - BPM
			//dd($deal->payments);
			if ($deal->payments->count() > 1) {
				$payments = $deal->payments;
				$payments->shift();
				$paymentsSum = $payments->sum('amount');
			} else {
				$paymentsSum = 0;
			}
			$deal->returned_amount = $paymentsSum;
			
			// Current debt - BPM
			$deal->current_debt = $deal->total_debt - $paymentsSum;
			
			
			return $deal;
		}
		
		/**
		 * Calculates current term for a deal
		 *
		 * @param $deal
		 * @return mixed
		 */
		public static function calculateTerm($deal) {
			$moneySentDate = $deal->statusMoneySent->first()->created_at;
			$deal->current_term = Carbon::now()->diffInDays($moneySentDate);
			
			return $deal;
		}
	}