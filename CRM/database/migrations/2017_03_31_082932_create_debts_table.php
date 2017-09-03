<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateDebtsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('f_debts', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('deal_id')->unsigned();
				$table->decimal('body_amount', 12, 2);
				$table->decimal('interest_amount', 12, 2);
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
			});
			
			Schema::create('f_payments', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('person_id')->unsigned();
				$table->integer('deal_id')->unsigned();
				$table->integer('user_id')->unsigned();
				$table->decimal('amount', 12, 2)->nullable();
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('user_id')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('f_debts');
			Schema::dropIfExists('f_payments');
		}
	}
