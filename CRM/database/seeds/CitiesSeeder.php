<?php
	
	use Illuminate\Database\Seeder;
	
	class CitiesSeeder extends Seeder {
		public static $cities = [
			'0'                                    => 'Yangon Region',
			'c67c7ed6-1317-40ae-8863-a47d02b12eb6' => 'Yangon',
			'692527a9-7e2d-4324-a1c9-e056b0c6a50d' => 'Yangon',
			'b85a981a-b4db-4425-aa6f-e749f5d7aea6' => 'Yangon',
			'7eda58dc-032d-4c9f-a7c4-af3da670e425' => 'Yangon',
			'793ce1a5-4151-4d59-a74b-9a94074b86bf' => 'Yangon',
			'692527a9-7e2d-4324-a1c9-e056b0c6a50d' => 'Yangon',
			'c90d8dff-588d-4b77-a802-6b343eb112e8' => 'Yangon',
			'43e386e3-0cc0-46ae-a5bb-d40cfa4e437b' => 'Yangon',
			'ea4bbc4b-c746-4b8f-a021-39e648a3d6da' => 'Nay Pyi Taw',
			'50493a39-1e17-4450-b8e0-e8249251e342' => 'Nay Pyi Taw',
			'688fbf38-3b93-41e6-ac53-755da22324ca' => 'Nay Pyi Taw',
			'101556f8-807a-4031-963e-42e543479ff2' => 'Mandalay',
			'e5e0305a-379c-41e8-8545-6e04bf33d04d' => 'Mandalay',
			'71344d21-12b4-4f9e-8b2a-9c74bb87e84d' => 'Bago',
			'e5dfa4c9-abad-47df-9dee-5b2e8d6f94eb' => 'Shan Region',
			'e74289c9-c61c-4cb8-8a00-4dd4243ef20f' => 'Kayin Region',
			'5ed7d3ce-7f47-42d6-ae01-c547bdb6ca28' => 'Kachin Region',
			'9d3a03b7-3952-4453-9683-4a9a26d030d7' => 'Ayeyarwady Region',
			'52b91cec-868b-4654-acd2-25446f29463e' => 'Mon Region',
			'41238a95-31f1-40fa-a71b-c3f05fca5cf4' => 'Tanintharyi Region',
			'3eb76a29-1d10-48f1-9158-23a9f8825a23' => 'Magway Region',
		];
		
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			$uniqueCities = array_unique(self::$cities);
			foreach ($uniqueCities as $name) {
				App\Models\Directory::create([
					'type' => 'city',
					'name' => $name,
				]);
			}
		}
	}
