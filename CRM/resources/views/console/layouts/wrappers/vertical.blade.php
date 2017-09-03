@extends('console.layouts.app')

@section('wrapper')
	{{-- Header --}}
	<header class="header">
		<div class="wrappers">
			{{-- Logo --}}
			<a class="header-logo" href="/"></a>
			
			{{-- Profile --}}
			<div class="header-profile">
				<a class="header-profile-info left">
					<i class="mdi mdi-36px mdi-account-circle left"></i>
					<div class="header-profile-info-name">{{ auth()->user()->name }}</div>
					<div class="header-profile-info-role">{{ auth()->user()->roles()->first()->display_name }}</div>
				</a>
				<div class="header-profile-links right">
					<a class="header-profile-links-exit" href="{{ route('console.logout') }}"><i class="mdi mdi-24px mdi-logout"></i></a>
				</div>
			</div>
		</div>
	</header>
	
	<div class="wrappers">
		{{-- Content --}}
		<main class="content vertical" data-type="{{ $type or '' }}">
			<div class="content-inner">
				@yield('content')
			</div>
			<div class="content-copyright right-align">
				0.1.1 &copy; 2017 Techwell
			</div>
		</main>
	</div>
@stop