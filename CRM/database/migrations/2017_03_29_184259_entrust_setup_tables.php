<?php
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	
	class EntrustSetupTables extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return  void
		 */
		public function up() {
			// Create table for storing roles
			Schema::create('c_roles', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->unique();
				$table->string('display_name')->nullable();
				$table->string('description')->nullable();
				$table->timestamps();
			});
			
			// Create table for associating roles to users (Many-to-Many)
			Schema::create('c_role_user', function (Blueprint $table) {
				$table->integer('user_id')->unsigned();
				$table->integer('role_id')->unsigned();
				
				$table->foreign('user_id')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('role_id')->references('id')->on('c_roles')->onUpdate('cascade')->onDelete('cascade');
				
				$table->primary([
					'user_id',
					'role_id',
				]);
			});
			
			// Create table for storing permissions
			Schema::create('c_permissions', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->unique();
				$table->string('display_name')->nullable();
				$table->string('description')->nullable();
				$table->timestamps();
			});
			
			// Create table for associating permissions to roles (Many-to-Many)
			Schema::create('c_permission_role', function (Blueprint $table) {
				$table->integer('permission_id')->unsigned();
				$table->integer('role_id')->unsigned();
				
				$table->foreign('permission_id')->references('id')->on('c_permissions')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('role_id')->references('id')->on('c_roles')->onUpdate('cascade')->onDelete('cascade');
				
				$table->primary([
					'permission_id',
					'role_id',
				]);
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return  void
		 */
		public function down() {
			Schema::drop('c_permission_role');
			Schema::drop('c_permissions');
			Schema::drop('c_role_user');
			Schema::drop('c_roles');
		}
	}
