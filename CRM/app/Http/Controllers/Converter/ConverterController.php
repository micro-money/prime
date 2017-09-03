<?php
	
	namespace App\Http\Controllers\Coverter;
	
	use Carbon\Carbon;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Http\Request;
	
	use CitiesSeeder;
	use App\Models\Converter\zContact;
	use App\Models\Converter\zOpportunity;
	use App\Models\Converter\zLead;
	use App\Models\Person;
	use App\Models\Contact;
	use App\Models\Address;
	use App\Models\Directory;
	use App\Models\Document;
	use App\Models\Workplace;
	use App\Models\Deal;
	use App\Models\Debt;
	use App\Models\Promise;
	use App\Models\Payment;
	use App\Models\DealStatus;
	
	class ConverterController extends Controller {
		protected $chunkSize = 1000;
		protected $branchId = '7ffcfa45-b517-441c-86f0-808eaab4dd11';
		protected $statistics;
		
		// Constructor
		public function __construct() {
			$this->statistics = [
				'downloaded'  => 0,
				'created'     => 0,
				'updated'     => 0,
				'started_at'  => time(),
				'finished_at' => null,
			];
		}
	}