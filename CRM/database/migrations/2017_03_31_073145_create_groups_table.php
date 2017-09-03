<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateGroupsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_groups', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type');
				$table->float('bad', 6, 5)->default(0)->unsigned()->index('bad');
				$table->timestamps();
				$table->softDeletes();
			});
			
			Schema::create('p_person_group', function (Blueprint $table) {
				$table->integer('person_id')->unsigned();
				$table->integer('group_id')->unsigned();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('group_id')->references('id')->on('p_groups')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_person_group');
			Schema::dropIfExists('p_groups');
		}
	}
