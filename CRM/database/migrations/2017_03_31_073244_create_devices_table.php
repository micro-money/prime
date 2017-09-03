<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateDevicesTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_devices', function (Blueprint $table) {
				$table->increments('id');
				$table->string('imei', 15);
				$table->string('brand', 40)->nullable();
				$table->string('model', 40)->nullable();
				$table->smallInteger('year')->unsigned()->nullable();
				$table->timestamps();
				$table->softDeletes();
			});
			
			Schema::create('p_person_device', function (Blueprint $table) {
				$table->integer('person_id')->unsigned();
				$table->integer('device_id')->unsigned();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('device_id')->references('id')->on('p_devices')->onUpdate('cascade')->onDelete('cascade');
			});
			
			Schema::create('p_device_contact', function (Blueprint $table) {
				$table->integer('device_id')->unsigned();
				$table->integer('contact_id')->unsigned();
				
				// Foreign keys
				$table->foreign('device_id')->references('id')->on('p_devices')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('contact_id')->references('id')->on('p_contacts')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_device_contact');
			Schema::dropIfExists('p_person_device');
			Schema::dropIfExists('p_devices');
		}
	}
