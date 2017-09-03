<?php
	
	// Console
	Route::group([
		'middleware' => [
			'auth',
			'menu',
		],
		'namespace'  => 'Console',
	], function () {
		// Router
		Route::get('/', [
			'uses' => 'ConsoleController@router',
			'as'   => 'console.router',
		]);
		
		// Collection
		Route::get('/collection/start', [
			'uses' => 'CollectionController@index',
			'as'   => 'console.collection.index',
		]);
		Route::get('/collection/{activityId}', [
			'uses' => 'CollectionController@activity',
			'as'   => 'console.collection.activity',
		]);
		
		// Supervision
		Route::get('/supervision/queue', [
			'uses' => 'SupervisionController@queue',
			'as'   => 'console.supervision.queue',
		]);
		
		// Logout
		Route::get('/logout', [
			'uses' => 'ConsoleController@logout',
			'as'   => 'console.logout',
		]);
	});
	
	// Auth
	Auth::routes();
	
	// Scheduled tasks
	Route::group([
		'namespace' => 'Tasks',
		'prefix'    => 'tasks',
	], function () {
		// Fill activities
		Route::get('/activities', [
			'uses' => 'TasksController@activities',
			'as'   => 'tasks.activities',
		]);
	});