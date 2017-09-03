<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateActivitiesTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('d_activities', function(Blueprint $table) {
				$table->increments('id');
				$table->integer('deal_id')->unsigned()->nullable();
				$table->integer('person_id')->unsigned()->nullable();
				$table->boolean('is_cp')->default(false)->nullable();
				$table->integer('user_id')->unsigned()->nullable();
				$table->integer('priority')->default(5000)->index();
				$table->enum('status', ['in_queue','in_progress','finished','cancelled','deferred'])->default('in_queue')->index();
				$table->text('notes')->nullable();
				
				// Timestamps
				$table->timestamp('date')->nullable();
				$table->timestamp('started_at')->nullable();
				$table->timestamp('finished_at')->nullable();
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('user_id')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('d_activities');
		}
	}
