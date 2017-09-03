@extends('layouts.wrappers.clean')

@section('content')
	{!! Form::open(['route' => 'password.reset']) !!}
	<div class="register-disclaimer white-text mt30">
		{{ trans('auth.fill-email') }}
	</div>
	@if(isset($errors))
		<div class="input-field mt30 center-align message message--error">
			@foreach($errors as $error)
				{{ $error }}<br>
			@endforeach
		</div>
	@endif
	<div class="input-field mt30">
		{{ Form::label('email', 'E-mail') }}
		{{ Form::text('email', null, ['class' => 'white-text mb0', 'autocomplete' => 'off']) }}
	</div>
	<div class="input-field center-align">
		{{ Form::submit('Restore', ['class' => 'btn']) }}
	</div>
	<div class="register-disclaimer white-text mt30">
		{{ trans('auth.restore') }}
	</div>
	{!! Form::close() !!}
@endsection