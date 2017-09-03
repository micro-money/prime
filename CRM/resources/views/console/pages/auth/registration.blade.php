@extends('layouts.wrappers.clean')

@section('content')
	{!! Form::open(['route' => 'register']) !!}
	<div class="register-disclaimer white-text mt30">
		{{ trans('auth.fill-all') }}
	</div>
	@if(isset($errors))
		<div class="input-field mt30 center-align message message--error">
			@foreach($errors as $error)
				{{ $error }}<br>
			@endforeach
		</div>
	@endif
	<div class="input-field mt30">
		{{ Form::label('company', 'Company') }}
		{{ Form::text('company', null, ['class' => 'white-text mb0', 'autocomplete' => 'off']) }}
	</div>
	<div class="input-field mt30">
		{{ Form::label('name', 'Your name') }}
		{{ Form::text('name', null, ['class' => 'white-text mb10', 'autocomplete' => 'off']) }}
	</div>
	<div class="input-field mt30">
		{{ Form::label('email', 'E-mail') }}
		{{ Form::text('email', null, ['class' => 'white-text mb10', 'autocomplete' => 'off']) }}
	</div>
	<div class="input-field">
		{{ Form::label('password', 'Password') }}
		{{ Form::password('password', ['class' => 'white-text mb10', 'autocomplete' => 'off']) }}
	</div>
	<div class="input-field mt0 mb30">
		{{ Form::checkbox('agree', '1', false, ['id' => 'agree', 'class' => 'filled-in']) }}
		{{ Form::label('agree', trans('auth.agree')) }}
	</div>
	<div class="input-field center-align">
		{{ Form::submit('Register', ['class' => 'btn']) }}
	</div>
	{!! Form::close() !!}
@endsection