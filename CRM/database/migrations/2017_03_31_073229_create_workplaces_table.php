<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateWorkplacesTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_workplaces', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('person_id')->unsigned();
				$table->integer('type_id')->unsigned()->nullable();
				$table->string('company')->nullable();
				$table->string('occupation')->nullable();
				$table->integer('salary')->default(0);
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('type_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_workplaces');
		}
	}
