<?php
	
	namespace App\Providers;
	
	use App\Models\Document;
	use App\Models\Contact;
	use App\Observers\ContactObserver;
	use App\Observers\DocumentObserver;
	use Illuminate\Support\ServiceProvider;
	use Illuminate\Support\Facades\Schema;
	
	class AppServiceProvider extends ServiceProvider {
		/**
		 * Bootstrap any application services.
		 *
		 * @return void
		 */
		public function boot() {
			Schema::defaultStringLength(191);
			
			// Observers
			Document::observe(DocumentObserver::class);
			Contact::observe(ContactObserver::class);
		}
		
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register() {
			//
		}
	}
