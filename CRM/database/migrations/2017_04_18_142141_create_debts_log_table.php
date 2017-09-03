<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateDebtsLogTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('f_debts_log', function(Blueprint $table) {
				$table->increments('id');
				$table->integer('deal_id')->unsigned();
				$table->integer('product_interest_id')->unsigned()->nullable();
				$table->decimal('amount', 12, 2);
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('deal_id')->references('id')->on('d_deals')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('product_interest_id')->references('id')->on('f_product_interest')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('f_debts_log');
		}
	}
