<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateDealsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('d_deals', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('person_id')->unsigned();
				$table->integer('product_id')->nullable()->unsigned();
				$table->smallInteger('requested_term')->nullable()->unsigned();
				$table->smallInteger('approved_term')->nullable()->unsigned();
				$table->decimal('requested_amount', 12, 2)->nullable()->unsigned();
				$table->decimal('approved_amount', 12, 2)->nullable()->unsigned();
				
				// Statistics
				$table->smallInteger('filling_time')->default(0)->unsigned()->nullable();
				$table->string('user_agent')->nullable();
				$table->ipAddress('ip')->nullable();
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Temporary
				$table->string('bpm_opportunity_id')->nullable()->index();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('product_id')->references('id')->on('f_products')->onUpdate('cascade')->onDelete('cascade');
			});
			
			Schema::create('d_statuses', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('deal_id')->unsigned();
				$table->integer('user_id')->unsigned();
				$table->integer('status_id')->unsigned();
				$table->string('notes', 1024)->nullable();
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('user_id')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('status_id')->references('id')->on('c_directory')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('d_statuses');
			Schema::dropIfExists('d_deals');
		}
	}
