<?php
	
	use Illuminate\Database\Seeder;
	
	class AccountsSeeder extends Seeder {
		public static $accounts = [
			'KBZ' => '1',
			'AGD' => '2',
		];
		
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			foreach (self::$accounts as $account => $currency_id) {
				App\Models\Account::create([
					'name'  => $account,
				    'currency_id' => $currency_id
				]);
			}
		}
	}
