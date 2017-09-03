<?php
	
	namespace App\Http\Controllers\Console;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Http\Request;
	use Carbon\Carbon;
	
	use App\Logic\ActivityLogic;
	use App\Models\Activity;
	
	class SupervisionController extends ConsoleController {
		/**
		 * Start page for collection
		 *
		 * @return int
		 */
		public function queue(Request $request) {
			$date = Carbon::today();
			$deals = ActivityLogic::get($date);
			
			// Creating activities
			foreach ($deals as $deal) {
				// Loaner
				if (!Activity::where('deal_id', $deal->id)->where('date', $date)->count()) {
					$deal->activities()->save(new Activity([
						'person_id' => $deal->person_id,
						'user_id'   => 1,
						'is_cp'     => false,
						'priority'  => 5000,
						'date'      => $date,
					]));
					
					// Connections
					$connectionsActivities = [];
					foreach ($deal->person->connections as $connection) {
						$connectionsActivities[] = new Activity([
							'person_id' => $connection->id,
							'user_id'   => 1,
							'is_cp'     => true,
							'priority'  => 5000,
							'date'      => $date,
						]);
					}
					$deal->activities()->saveMany($connectionsActivities);
				}
			}
			
			$activities = Activity::with(['deal', 'person', 'deal.debts'])->where('date', $date)->get();
			
			return view('console.pages.supervision.queue')->with(compact(['activities']));
		}
	}