<?php
	
	use Illuminate\Database\Seeder;
	
	class CurrenciesSeeder extends Seeder {
		private $currencies = [
			'USD' => [
				'name'         => 'US Dollar',
				'sign'         => '$',
				'before_value' => true,
				'rate'         => 1,
			],
			'MMK' => [
				'name'         => 'Myanmar Kyat',
				'sign'         => ' MMK',
				'before_value' => false,
				'rate'         => 1360,
			],
		];
		
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			foreach ($this->currencies as $code => $currency) {
				App\Models\Currency::create([
					'name'         => $currency['name'],
					'code'         => $code,
					'sign'         => $currency['sign'],
					'before_value' => $currency['before_value'],
				])->rates()->save(App\Models\CurrencyRate::create([
					'value' => $currency['rate'],
				]));
			}
		}
	}
