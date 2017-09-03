<?php
	
	use Illuminate\Database\Seeder;
	
	class TypesSeeder extends Seeder {
		public static $statuses = [
			'call'      => [
				'Sale'       => 0,
				'Collection' => 1,
			],
			'workplace' => [
				'Government' => null,
				'Self-employed' => null,
				'Company' => null,
			],
		];
		
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			$stats = [];
			foreach (self::$statuses as $type => $status) {
				foreach ($status as $name => $value) {
					$stats[] = App\Models\Directory::create([
						'type'  => $type . '_type',
						'name'  => $name,
						'value' => $value,
					]);
				}
			}
		}
	}
