<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class UpdateDebtsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::table('f_debts', function (Blueprint $table) {
				$table->integer('product_interest_id')->unsigned()->nullable()->after('deal_id');
				$table->decimal('amount', 12, 2)->default(0)->after('product_interest_id');
				
				// Drops
				$table->dropColumn(['body_amount', 'interest_amount']);
				
				// Foreign keys
				$table->foreign('product_interest_id')->references('id')->on('f_product_interest')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::table('f_debts', function (Blueprint $table) {
				// Foreign keys
				$table->dropForeign('f_debts_product_interest_id_foreign');
				
				// Returning original
				$table->decimal('body_amount', 12, 2)->after('deal_id');
				$table->decimal('interest_amount', 12, 2)->after('body_amount');
				
				// Drops
				$table->dropColumn(['product_interest_id', 'amount']);
			});
		}
	}
