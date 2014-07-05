<!doctype html>
<html lang="fr">
<head>
  <link rel="SHORTCUT ICON" href="{{ URL::to('') }}/favicon.ico">
  <meta charset="UTF-8">
	<title>
    
  </title>
  {{ Less::to('styles') }}
  @yield('head')
  
</head>
<body>
  <div id="wrap">
    <div class="navbar navbar-default navbar-static-top first-nav-bar" role='navigation'>
      <div class='container'>
        <div class="navbar-header" style="margin-left: 15px;">
          <a class="navbar-brand">
            <span class="website-title">
              Configuration du site
            </span>
          </a>
        </div>
      </div>
    </div>
    <div class="container">
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
