<!doctype html>
<html lang="en" @yield('html_parameters')>
<head>
  <meta charset="UTF-8">
	<title>
    {{{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}} - @yield('title')
  </title>
  {{ Less::to('styles') }}
  @yield('head')
  {{ Parameter::get(Parameter::$ADDITIONAL_HEAD_HTML) }}
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
  
  <script>
    var keepaliveURL = "{{ URL::route('session_keepalive'); }}";
  </script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="{{ asset('js/libs/jquery-1.11.0.min.js') }}"><\/script>')</script>
  <script src="{{ asset('js/libs/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/application.js') }}"></script>
  <script src="{{ asset('js/libs/bootstrap-switch.min.js') }}"></script>
  @yield('additional_javascript')
</body>
</html>
