<?php
	
	// Debug
	Route::group([
		'middleware' => [
			'auth',
			'role:administrator|supervisor|collector',
		],
		'namespace'  => 'Debug',
		'prefix'     => 'debug',
	], function () {
		// Clients list
		Route::get('/clients/list', [
			'uses' => 'DebugController@clientsList',
			'as'   => 'debug.clients.list',
		]);
		
		// Sending pushes
		Route::get('/push/{token}', function ($token) {
			dd(fcm()->to([$token])// $recipients must an array
			->data([
				'title' => 'Test FCM',
				'body'  => 'This is a test of FCM',
			])->send());
			//dd(Notification::send(\App\Models\Person::find(1), new \App\Notifications\TestNotification()));
		});
		
		// Clients connections
		Route::get('/clients/{id}', function($id) {
			$connectionsFrom = App\Models\Person::find($id)->connectionsFrom()->get()->pluck('name', 'id')->toArray();
			$connectionsTo = App\Models\Person::find($id)->connectionsTo()->get()->pluck('name', 'id')->toArray();
			$connections = array_merge($connectionsFrom, $connectionsTo);
			$connectionsContacts = App\Models\Person::with(['phones'])->whereIn('id', array_keys($connections))->get()->pluck('phones');
			dd($connectionsContacts);
		});
		
		// Activities list
		Route::get('/activities', [
			'uses' => 'DebugController@activitiesList',
			'as'   => 'debug.activities.list',
		]);
	});