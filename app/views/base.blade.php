<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
    @yield('title')
  </title>
  {{ Less::to('styles') }}
</head>
<body>
  @include('menu.header')
	@yield('content')
  @include('menu.footer')
  
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="{{ URL::to('/') }}/js/bootstrap.min.js"></script>
  <script src="{{ URL::to('/') }}/js/application.js"></script>
</body>
</html>
