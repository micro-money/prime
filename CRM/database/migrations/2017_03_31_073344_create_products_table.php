<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateProductsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('f_products', function(Blueprint $table) {
				$table->increments('id');
				$table->string('name');
				$table->text('description')->nullable();
				$table->smallInteger('min_term')->default(1);
				$table->smallInteger('max_term')->default(100);
				$table->smallInteger('prolongation_term')->default(14);
				$table->smallInteger('setoff_term')->default(0);
				$table->decimal('min_amount', 12, 2)->default(0);
				$table->decimal('max_amount', 12, 2)->default(99999999);
				$table->decimal('close_amount', 12, 2)->default(0);
				$table->integer('currency_id')->unsigned()->nullable()->default(1);
				$table->boolean('active')->default(false);
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('currency_id')->references('id')->on('c_currencies')->onUpdate('cascade')->onDelete('cascade');
			});
			
			Schema::create('f_product_interest', function(Blueprint $table) {
				$table->increments('id');
				$table->integer('product_id')->unsigned()->nullable();
				$table->integer('interest_id')->unsigned()->nullable();
				$table->string('name');
				$table->decimal('fix_amount', 12, 2)->default(0)->comment('In product"s currency');
				$table->decimal('float_amount', 5, 2)->default(0)->comment('In percent');
				$table->decimal('min_repayment_amount', 12, 2)->default(0);
				
				// Charges
				$table->boolean('charge_on_main')->default(false)->comment('Charge once in send money case');
				$table->boolean('charge_in_main_term')->default(false)->comment('Charge everyday in main term');
				$table->boolean('charge_on_overdue')->default(false)->comment('Charge once in overdue case');
				$table->boolean('charge_in_overdue_term')->default(false)->comment('Charge everyday in overdue');
				$table->smallInteger('start_charge_day')->default(0);
				$table->smallInteger('stop_charge_day')->default(0);
				$table->boolean('include_in_base')->default(false)->comment('Include in base for further interests calculation');
				
				// Activity and priority
				$table->smallInteger('priority')->default(50);
				$table->boolean('active')->default(false);
				
				// Timestamps
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('product_id')->references('id')->on('f_products')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('interest_id')->references('id')->on('c_interest_type')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('f_product_interest');
			Schema::dropIfExists('f_products');
		}
	}
