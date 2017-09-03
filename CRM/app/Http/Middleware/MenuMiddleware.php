<?php
	
	namespace App\Http\Middleware;
	
	use \Menu as Menu;
	
	class MenuMiddleware {
		public function handle($request, $next) {
						
			// Console
			Menu::make('ConsoleSidebarMenu', function ($menu) {
				
			});
			
			return $next($request);
		}
	}
