<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateScriptAnswerTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('c_script_answer', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('parent_script_id')->unsigned();
				$table->integer('child_script_id')->unsigned();
				$table->string('action');
				$table->string('type');
				$table->string('value');
				$table->timestamps();
				
				// Foreign keys
				$table->foreign('parent_script_id')->references('id')->on('c_scripts')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('child_script_id')->references('id')->on('c_scripts')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('c_script_answer');
		}
	}
