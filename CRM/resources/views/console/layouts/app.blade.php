<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!--   CSRF   -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!--   SEO   -->
	<title>{{ $title or config('app.name', 'Micromoney BPM') }}</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!-- # SEO # -->
	
	<!--   Open Graph   -->
	<meta property="og:url" content="">
	<meta property="og:title" content="">
	<meta property="og:description" content="">
	<meta property="og:image" content="">
	<!-- # Open Graph # -->
	
	<!--   Materialize   -->
	<link type="text/css" rel="stylesheet" href="{{ asset('/console/css/vendor/materialdesignicons/mdi.min.css') }}" media="screen, projection">
	<link type="text/css" rel="stylesheet" href="{{ asset('/console/css/vendor/materialize/materialize.css') }}" media="screen, projection">
	<!-- # Materialize # -->
	
	<!--   Styles   -->
	<link type="text/css" rel="stylesheet" href="{{ asset('/console/css/vendor/google/font.ubuntu.css') }}">
	<link type="text/css" rel="stylesheet" href="{{ asset('/console/css/app.css') }}" media="screen,projection">
	<!-- # Styles # -->
</head>
<body>
	@yield('wrapper')
	<!--   Scripts   -->
	<script type="text/javascript" src="{{ asset('/console/js/vendor/jquery/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/console/js/vendor/materialize/materialize.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/console/js/app/common.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/console/js/app/pages.js') }}"></script>
	<!-- # Scripts # -->
</body>
</html>
