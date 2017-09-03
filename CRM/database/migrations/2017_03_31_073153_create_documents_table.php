<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateDocumentsTable extends Migration {
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			Schema::create('p_documents', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type');
				$table->string('name')->nullable();
				$table->string('value')->index('value');
				$table->integer('file_id')->unsigned()->nullable();
				$table->timestamps();
				$table->softDeletes();
				
				// Foreign keys
				$table->foreign('file_id')->references('id')->on('c_files')->onUpdate('cascade')->onDelete('cascade');
			});
			
			Schema::create('p_person_document', function (Blueprint $table) {
				$table->integer('person_id')->unsigned();
				$table->integer('document_id')->unsigned();
				
				// Foreign keys
				$table->foreign('person_id')->references('id')->on('p_persons')->onUpdate('cascade')->onDelete('cascade');
				$table->foreign('document_id')->references('id')->on('p_documents')->onUpdate('cascade')->onDelete('cascade');
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('p_person_document');
			Schema::dropIfExists('p_documents');
		}
	}
