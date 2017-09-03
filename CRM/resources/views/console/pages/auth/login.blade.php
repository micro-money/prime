@extends('console.layouts.wrappers.clean')

@section('content')
	{!! Form::open(['url' => 'login']) !!}
		@if(isset($errors) && count($errors))
			<div class="input-field mt30 center-align message message--error">
				@foreach($errors as $error)
					{{ print_r($error) }}<br>
				@endforeach
			</div>
		@endif
		<div class="input-field mt30">
			{{ Form::label('email', 'E-mail') }}
			{{ Form::text('email', null, ['class' => 'white-text mb10']) }}
		</div>
		<div class="input-field">
			{{ Form::label('password', 'Password') }}
			{{ Form::password('password', ['class' => 'white-text mb0']) }}
		</div>
		<div class="input-field center-align">
			{{ Form::submit('Log In', ['class' => 'btn']) }}
		</div>
		{{--
		<div class="input-field center-align oh login-links mt30">
			{{ link_to_route('register', 'Register', [], ['class' => 'login-register left white-text']) }}
			<span class="white-text">or</span>
			{{ link_to_route('password.reset', 'Restore password', ['token' => csrf_token()], ['class' => 'login-register right white-text']) }}
		</div>
		--}}
	{!! Form::close() !!}
@endsection