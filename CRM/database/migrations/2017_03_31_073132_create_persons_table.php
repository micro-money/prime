<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreatePersonsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_persons', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');
				$table->dateTime('birth_date')->nullable();
				$table->boolean('sex')->nullable()->comment('0 - Male, 1 - Female');
				$table->string('notes', 1024)->nullable();
				$table->boolean('bad')->default(false);
				$table->timestamps();
				$table->softDeletes();
				
				// Temporary
				$table->string('bpm_contact_id')->comment('Id in BPM online')->nullable()->unique();
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_persons');
		}
	}
