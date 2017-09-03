<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateAdressesTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_addresses', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('person_id')->unsigned();
				$table->integer('city_id')->unsigned()->nullable();
				$table->integer('district_id')->unsigned()->nullable();
				$table->string('address')->nullable();
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('city_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('district_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_addresses');
		}
	}
