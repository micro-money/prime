<?php
	
	use Illuminate\Database\Seeder;
	
	class StatusesSeeder extends Seeder {
		public static $statuses = [
			'deal'    => [
				'Lead'                => 0,
				'Need to check'       => 40,
				'Denied'              => 60,
				'Denied on debt'      => 60,
				'Approved'            => 100,
				'Money has been sent' => 200,
				'Prolongation'        => 300,
				'Overdue – 15'        => 515,
				'Overdue – 30'        => 530,
				'Collection – 60'     => 660,
				'Collection – 90'     => 690,
				'Collection – 90+'    => 699,
				'Repaid'              => 1000,
			],
			'call'    => [
				'No answer'    => 0,
				'Turn off'     => -1,
				'Will not pay' => -2,
				'Will pay'     => 1,
				'Will push'    => 1,
				'Do not know'  => -2,
			],
			'promise' => [
				'Failed'  => 0,
				'Succeed' => 1,
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
						'type'  => $type . '_status',
						'name'  => $name,
						'value' => $value,
					]);
				}
			}
		}
	}
