<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateContactsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_contacts', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type')->index();
				$table->string('name')->nullable();
				$table->string('value')->index('value');
				$table->smallInteger('attempts')->default(0)->unsigned();
				$table->smallInteger('successful')->default(0)->unsigned();
				$table->boolean('active')->default(true);
				$table->timestamps();
				$table->softDeletes();
			});
			
			Schema::create('p_person_contact', function (Blueprint $table) {
				$table->integer('person_id')->unsigned();
				$table->integer('contact_id')->unsigned();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('contact_id')->references('id')->on('p_contacts')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_person_contact');
			Schema::dropIfExists('p_contacts');
		}
	}
