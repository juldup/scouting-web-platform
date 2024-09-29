<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

use App\Models\Parameter;

?>
<!doctype html>
<html lang="fr" @yield('html_parameters')>
<head>
  <link rel="SHORTCUT ICON" href="{{ URL::route('website_icon') }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    {{{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}} - @yield('title')
  </title>
  {{-- Less::to('styles') --}}
  @vite(['resources/css/styles.css'])
  <link media="all" type="text/css" rel="stylesheet" href="{{ URL::route('additional_css') }}">
  @yield('head')
  {{ Parameter::get(Parameter::$ADDITIONAL_HEAD_HTML) }}
  <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
  @yield('body_top')
  <div id="wrap" @if (isset($page_slug) && $page_slug) class="page-{{ $page_slug }}" @endif>
<div class="navigation-wrapper @if (Parameter::get(Parameter::$LOGO_TWO_LINES)) logo-two-lines @endif">
  <div class="navbar navbar-default navbar-static-top first-nav-bar" role='navigation'>
    <div class='container'>
      <div class="navbar-header" style="margin-left: 15px;">
        <a class="navbar-brand" href="{{ URL::route('home') }}">
          <span class="website-logo-wrapper">
            <span class="website-logo-frame">
              <img class="website-logo" src='{{ URL::route('website_logo') }}'/>
            </span>
          </span>
          <span class="website-title">
            {{{ Parameter::get(Parameter::$UNIT_LONG_NAME) }}}
          </span>
        </a>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="collapse navbar-collapse">
        
      </div>
    </div>
  </div>
  <div class="navbar navbar-collapse navbar-default navbar-static-top second-nav-bar collapse" id='bs-example-navbar-collapse-1'>
    <div class="container">
      <div class="menu-wrapper">
        
      </div>
    </div>
  </div>
</div>
    <div class="container">
      Le site est en maintenance. Veuillez réessayer plus tard.
    </div>
  </div>
  @include('menu.footer')
  
  <script>
    var keepaliveURL = "{{ URL::route('session_keepalive'); }}";
  </script>
  @vite(['resources/js/libs/jquery-1.11.0.js',
         'resources/js/libs/jquery-ui-1.10.4.js',
         'resources/js/libs/bootstrap.min.js',
         'resources/js/application.js',
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
