<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/
?>
<!doctype html>
<html lang="fr" @yield('html_parameters')>
<head>
  <link rel="SHORTCUT ICON" href="{{ URL::to('') }}/favicon.ico">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    {{{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}} - @yield('title')
  </title>
  {{ Less::to('styles') }}
  @yield('head')
  {{ Parameter::get(Parameter::$ADDITIONAL_HEAD_HTML) }}
</head>
<body>
  @yield('body_top')
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
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
  <script>window.jQuery || document.write('<script src="{{ asset('js/libs/jquery-1.11.0.min.js') }}"><\/script>')</script>
  <script src="{{ asset('js/libs/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/application.js') }}"></script>
  <script src="{{ asset('js/libs/bootstrap-switch.min.js') }}"></script>
  <script src="{{ asset('js/libs/jquery.tablesorter.js') }}"></script>
  <script>
    $('.sort-by-column').tablesorter();
  </script>
  @yield('additional_javascript')
</body>
</html>
