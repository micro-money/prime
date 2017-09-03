<?php
	
	namespace App\Logic;
	
	use Carbon\Carbon;
	
	use App\Models\Deal;
	use App\Models\Promise;
	use App\Models\Directory;
	
	class ActivityLogic extends Logic {
		/**
		 * Get activities on date
		 *
		 * @param bool $dateFrom
		 * @return mixed
		 */
		public static function get($dateFrom = false) {
			// Collection stage
			$collectionStageDays = 60;
				
			// Generate dates
			if (!$dateFrom) $dateFrom = Carbon::today()->subDays($collectionStageDays);
			$dateTo = $dateFrom->copy()->addDays(1);
			
			// Getting promises
			$deals = Deal::whereHas('promises', function($query) use ($dateFrom, $dateTo) {
				$query->where('date', '>=', $dateFrom)->where('date', '<', $dateTo);
			})->with(['person.connections'])->get();
			return $deals;
		}
	}