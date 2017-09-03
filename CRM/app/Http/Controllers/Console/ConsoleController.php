<?php
	
	namespace App\Http\Controllers\Console;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Http\Request;
	
	class ConsoleController extends Controller {
		/**
		 * Choosing the page after login depends on user role
		 * 
		 * @return \Illuminate\Http\RedirectResponse
		 */
		public function router() {
			// Collector's start page
			if (auth()->user()->hasRole('collector')) {
				return redirect()->route('console.collection.index');
			}
		}
		
		/**
		 * Logging out by simple GET
		 *
		 * @return \Illuminate\Http\RedirectResponse
		 */
		public function logout() {
			auth()->logout();
			
			return redirect()->route('login');
		}
	}
