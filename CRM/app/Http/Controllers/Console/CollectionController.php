<?php
	
	namespace App\Http\Controllers\Console;
	
	use Carbon\Carbon;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Http\Request;
	
	use App\Models\Activity;
	use App\Models\Person;
	use App\Models\Contact;
	use App\Models\Document;
	use App\Models\Deal;
	
	use App\Logic\DealLogic;
	
	class CollectionController extends ConsoleController {
		/**
		 * Start page for collection
		 *
		 * @param Request $request
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
		 */
		public function index(Request $request) {
			return redirect()->route('console.collection.activity', ['activityId' => 1]);
			
			return view('console.pages.collection.index');
		}
		
		/**
		 * Activity page
		 *
		 * @param Request $request
		 * @param $activityId
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function activity(Request $request, $activityId) {
			$activity = Activity::findOrFail($activityId);
			$person = Person::with(['contacts', 'connections', 'addresses', 'documents', 'workplaces'])->withCount(['deals'])->findOrFail($activity->person_id);
			$deal = Deal::with(['promises', 'statuses.status', 'statusMoneySent', 'debts', 'payments', 'messages'])->findOrFail($activity->deal_id);
			
			//dd($deal);
			
			$deal = DealLogic::calculateTerm($deal);
			$deal = DealLogic::calculateDebts($deal);
			
			// Save opened status
			$activity->status = 'in_progress';
			if ($activity->status == 'in_queue') {
				$activity->started_at = Carbon::now();
			}
			$activity->save();
			
			return view('console.pages.collection.activity')->with(compact(['activity', 'person', 'deal']));
		}
	}
