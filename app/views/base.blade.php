<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
	<title>
    Unité scoute - @yield('title')
  </title>
  {{ Less::to('styles') }}
  @yield('head')
</head>
<body>
  <div id="wrap">
    @include('menu.header')
    <div class="container">
      @yield('content')
    </div>
  </div>
  @include('menu.footer')
  
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="{{ URL::to('/') }}/js/bootstrap.min.js"></script>
  <script src="{{ URL::to('/') }}/js/application.js"></script>
</body>
</html>
