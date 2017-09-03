@extends('console.layouts.app')

@section('wrapper')
	{{-- Content --}}
	<main class="center-block">
		<div class="clean-wrapper">
			<a class="clean-logo" href="{{ route('login') }}"></a>
			<div class="clean-content">
				@yield('content')
			</div>
		</div>
		<div class="clean-footer center-align">&copy; 2017 Techwell</div>
	</main>
@stop