<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateDirectoryTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('c_directory', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('parent_id')->unsigned()->nullable();
				$table->string('type')->index('type');
				$table->string('name')->index('name');
				$table->string('value')->nullable();
				$table->timestamps();
				
				// Foreign keys
				$table->foreign('parent_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('c_directory');
		}
	}
