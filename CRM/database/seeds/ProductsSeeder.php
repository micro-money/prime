<?php
	
	use Illuminate\Database\Seeder;
	
	class ProductsSeeder extends Seeder {
		private $basicInterests = [
			'Principal',
			'Main interest',
			'Overdure interest',
		];
		private $products = [
			[
				'name'              => 'Myanmar',
				'description'       => 'Copied from BPM Online.',
				'min_term'          => 7,
				'max_term'          => 32,
				'prolongation_term' => 14,
				'setoff_term'       => 3,
				'close_amount'      => 7,
				'currency_id'       => 2,
				'active'            => 1,
			],
		];
		private $interests = [
			'Myanmar' => [
				[
					'interest_id'            => 1,
					'name'                   => 'Init Principal',
					'fix_amount'             => 0,
					'float_amount'           => 100,
					'charge_on_main'         => true,
					'charge_in_main_term'    => false,
					'charge_on_overdue'      => false,
					'charge_in_overdue_term' => false,
					'start_charge_day'       => 0,
					'stop_charge_day'        => 0,
					'include_in_base'        => true,
					'priority'               => 100,
					'active'                 => true,
				],
				[
					'interest_id'            => 2,
					'name'                   => 'Main Interest',
					'fix_amount'             => 0,
					'float_amount'           => 1,
					'min_repayment_amount'   => 0,
					'charge_on_main'         => false,
					'charge_in_main_term'    => true,
					'charge_on_overdue'      => false,
					'charge_in_overdue_term' => false,
					'start_charge_day'       => 0,
					'stop_charge_day'        => 180,
					'include_in_base'        => false,
					'priority'               => 70,
					'active'                 => true,
				],
				[
					'interest_id'            => 3,
					'name'                   => 'Overdue Prolongation Fee',
					'fix_amount'             => 5000,
					'float_amount'           => 0,
					'min_repayment_amount'   => 100,
					'charge_on_main'         => false,
					'charge_in_main_term'    => false,
					'charge_on_overdue'      => true,
					'charge_in_overdue_term' => false,
					'start_charge_day'       => 0,
					'stop_charge_day'        => 0,
					'include_in_base'        => false,
					'priority'               => 50,
					'active'                 => true,
				],
			],
		];
		
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			// Basic
			foreach ($this->basicInterests as $basicInterest) {
				App\Models\Interest::create([
					'name' => $basicInterest,
				]);
			}
			
			// Creating products
			foreach ($this->products as $name => $product) {
				$productObject = App\Models\Product::create($product);
				
				// Interests
				$interests = $this->interests[$product['name']];
				foreach ($interests as &$interest) {
					$interest = App\Models\ProductInterest::create($interest);
				}
				$productObject->interests()->saveMany($interests);
			}
		}
	}
