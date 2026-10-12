<!doctype html>
<html lang="fr">
<head>
  <link rel="SHORTCUT ICON" href="{{ URL::to('') }}/favicon.ico">
  <meta charset="UTF-8">
	<title>
    
  </title>
  @vite(['resources/css/styles.css'])
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
      window.jQuery = window.$ = jQuery; // Fallback to the CDN version
  </script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  @vite(['resources/js/application.js',
         'resources/js/libs/jquery-ui-1.10.4.js',
         'resources/js/libs/bootstrap-switch.min.js',
         'resources/js/libs/jquery.tablesorter.js'
        ])

  <script type="module">
      $().ready(function() {
          // CSRF for ajax post requests
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          // initialize sortable tables
          $('.sort-by-column').tablesorter();
      });
  </script>
  @yield('additional_javascript')
</body>
</html>
