@extends('console.layouts.app')

@section('wrapper')
	<div class="wrapper">
		{{-- Sidebar --}}
		<aside class="sidebar hide-on-med-and-down">
			{{-- Logo --}}
			<a class="sidebar-logo" href="/"></a>
			
			{{-- Menu --}}
			<div class="sidebar-menu-wrapper">
				{!! $ConsoleSidebarMenu->asUl() !!}
			</div>
			
			{{-- Profile --}}
			<div class="sidebar-profile">
				<div class="sidebar-profile-links">
					<a class="btn btn-flat white-text" href="{{ route('console.logout') }}"><i class="mdi mdi-24px mdi-logout"></i>Exit</a>
				</div>
			</div>
		</aside>
		
		{{-- Content --}}
		<main class="content">
			<div class="content-inner">
				@yield('content')
			</div>
			<div class="content-copyright right-align">
				0.1.1 &copy; 2017 Techwell
			</div>
		</main>
	</div>
@stop