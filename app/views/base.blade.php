<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
	<title>
    {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }} - @yield('title')
  </title>
  {{ Less::to('styles') }}
  @yield('head')
</head>
<body>
  <div id="wrap">
    @include('menu.header')
    <div class="container">
      @include('subviews.navigationLinks')
      @yield('content')
    </div>
  </div>
  @include('menu.footer')
  
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="{{ URL::to('/') }}/js/jquery-1.11.0.min.js"><\/script>')</script>
  <script src="{{ URL::to('/') }}/js/bootstrap.min.js"></script>
  <script src="{{ URL::to('/') }}/js/application.js"></script>
  <script src="{{ URL::to('/') }}/js/bootstrap-switch.min.js"></script>
  @yield('additional_javascript')
</body>
</html>
