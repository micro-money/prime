<?php
	namespace App\Http\Controllers\Tasks;
	
	use Carbon\Carbon;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use App\Http\Controllers\Controller;
	
	use App\Logic\ActivityLogic;
	
	use App\Models\Converter\zActivity;
	
	use App\Models\Activity;
	use App\Models\Deal;
	use App\Models\User;
	use App\Models\Role;
	
	class TasksController {
		/**
		 * Create activities
		 */
		public function activities() {
			$activities = [];
			
			// Users
			$usersIds = User::whereHas('role', function ($query) {
				$query->where('name', 'collector');
			})->orderBy('id')->pluck('id')->toArray();
			
			// Get deals which is on collection stage
			$deals = ActivityLogic::get();
			$dealsPerUser = ceil($deals->count() / count($usersIds));
			$userDealsCount = 0;
			foreach ($deals as $deal) {
				// Take user
				if (!$userDealsCount || ($userDealsCount >= $dealsPerUser)) {
					$userDealsCount = 0;
					$userId = array_shift($usersIds);
				}
				
				// Deal owner
				$activities[] = [
					'deal_id'   => $deal->id,
					'person_id' => $deal->person_id,
					'user_id'   => $userId,
					'priority'  => 5000,
				];
				
				// Contact persons
				if ($deal->person->connections) {
					foreach ($deal->person->connections as $connection) {
						$activities[] = [
							'deal_id'   => $deal->id,
							'person_id' => $connection->id,
							'user_id'   => $userId,
							'priority'  => 5000,
						];
					}
				}
				
				// Increase deals on current user
				$userDealsCount++;
			}
			
			// Saving
			if (count($activities)) {
				// Get table
				$activitiesInstance = new Activity();
				$activitiesTable = $activitiesInstance->getTable();
				
				// Mass saving
				DB::table($activitiesTable)->insert($activities);
			}
		}
	}