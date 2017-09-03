<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateCurrenciesTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('c_currencies', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');
				$table->string('code');
				$table->string('sign');
				$table->boolean('before_value')->default(false);
				$table->boolean('active')->default(true);
				
				// Timestamps
				$table->timestamps();
			});
			
			Schema::create('c_currency_rate', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('currency_id')->unsigned()->nullable();
				$table->decimal('value', 6, 2);
				
				// Timestamps
				$table->timestamps();
				
				// Foreign keys
				$table->foreign('currency_id')->references('id')->on('c_currencies')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('c_currency_rate');
			Schema::dropIfExists('c_currencies');
		}
	}
