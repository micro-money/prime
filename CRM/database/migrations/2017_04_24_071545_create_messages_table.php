<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateMessagesTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('d_messages', function(Blueprint $table) {
				$table->increments('id');
				$table->integer('deal_id')->unsigned()->nullable();
				$table->integer('person_id')->unsigned()->nullable();
				$table->boolean('is_cp')->default(false)->nullable();
				$table->integer('contact_id')->unsigned()->nullable();
				$table->integer('user_id')->unsigned()->nullable();
				$table->string('service')->index();
				$table->text('message');
				$table->enum('status', ['in_queue','in_progress','sent','delivered','not_delivered','seen','cancelled','deferred','error'])->default('in_queue')->index();
				$table->timestamp('sent_at')->nullable();
				$table->timestamp('delivered_at')->nullable();
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('contact_id')->references('id')->on('p_contacts')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('user_id')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('d_messages');
		}
	}
