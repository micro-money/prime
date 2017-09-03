<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateCallsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('d_calls', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('person_id')->unsigned();
				$table->integer('deal_id')->unsigned();
				$table->integer('contact_id')->unsigned();
				$table->integer('user_id')->unsigned();
				$table->integer('type_id')->unsigned();
				$table->integer('status_id')->unsigned();
				$table->string('notes', 1024)->nullable();
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('contact_id')->references('id')->on('p_contacts')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('user_id')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('type_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('status_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
			});
			
			Schema::create('d_promises', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('deal_id')->unsigned();
				$table->integer('call_id')->unsigned()->nullable();
				$table->integer('person_id')->unsigned();
				$table->string('type')->nullable();
				$table->decimal('amount', 12, 2);
				$table->timestamp('date');
				$table->integer('status_id')->unsigned();
				$table->string('notes', 1024)->nullable();
				
				// Timestamps
				$table->timestamp('started_at')->nullable();
				$table->timestamp('finished_at')->nullable();
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('call_id')->references('id')->on('d_calls')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('status_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('d_promises');
			Schema::dropIfExists('d_calls');
		}
	}
