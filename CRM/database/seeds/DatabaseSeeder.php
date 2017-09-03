<?php
	
	use Illuminate\Database\Seeder;
	
	class DatabaseSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run() {
			// Users
			$this->call(UsersRolesTableSeeder::class);
			
			// Core
			$this->call(CitiesSeeder::class);
			$this->call(StatusesSeeder::class);
			$this->call(TypesSeeder::class);
			
			// Financial
			$this->call(CurrenciesSeeder::class);
			$this->call(AccountsSeeder::class);
			$this->call(InterestTypeSeeder::class);
			$this->call(ProductsSeeder::class);
		}
	}
