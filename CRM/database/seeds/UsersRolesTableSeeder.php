<?php
	
	use Illuminate\Database\Seeder;
	
	class UsersRolesTableSeeder extends Seeder {
		private $roles = [
			'administrator' => [
				'name'         => 'administrator',
				'display_name' => 'Administrator',
				'count'        => 1,
			],
			'supervisor'    => [
				'name'         => 'supervisor',
				'display_name' => 'Supervisor',
				'count'        => 1,
			],
			'collector'     => [
				'name'         => 'collector',
				'display_name' => 'Collector',
				'count'        => 12,
			],
			'sales'         => [
				'name'         => 'sales',
				'display_name' => 'Sales',
				'count'        => 12,
			],
			'underwriter'   => [
				'name'         => 'underwriter',
				'display_name' => 'Underwriter',
				'count'        => 2,
			],
		];
		
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			$password = bcrypt('secret');
			
			// Create roles
			$obRoles = [];
			foreach ($this->roles as $role) {
				$obRoles[$role['name']] = App\Models\Role::create([
					'name'         => $role['name'],
					'display_name' => $role['display_name'],
				]);
				
				// Create users
				for ($i = 0; $i < $role['count']; $i++) {
					$email = $role['name'] . ($i ? $i : '') . '@money.com.mm';
					
					$user = App\Models\User::create([
						'name'           => $role['display_name'],
						'email'          => $email,
						'password'       => $password,
						'remember_token' => str_random(10),
					]);
					
					$user->roles()->attach($obRoles[$role['name']]);
				}
			}
		}
	}
