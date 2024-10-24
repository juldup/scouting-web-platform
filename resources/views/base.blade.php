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
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;

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
  <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.2.0/ckeditor5.css" />
  @vite(['resources/css/ckeditor/imageresize.css'])
  @vite(['resources/css/ckeditor/imagestyle.css'])
  @vite(['resources/css/ckeditor/ckeditor-fix.css'])
</head>
<body>
  @yield('body_top')
  <div id="wrap" @if (isset($page_slug) && $page_slug) class="page-{{ $page_slug }}" @endif>
    @include('menu.header')
    <div class="container">
      @include('subviews.navigationLinks')
      @yield('content')
    </div>
  </div>
  @include('menu.footer')
  
  
  <script type="importmap">
      {
          "imports": {
              "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.2.0/ckeditor5.js",
              "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.2.0/"
          }
      }
  </script>
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
