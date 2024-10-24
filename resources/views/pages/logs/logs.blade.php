@extends('base')
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

@section('title')
  Logs
@stop

@section('additional_javascript')
  <script src="https://unpkg.com/@angular/core@14.2.0/bundles/core.umd.min.js"></script>
  <script src="https://unpkg.com/@angular/common@14.2.0/bundles/common.umd.min.js"></script>
  <script src="https://unpkg.com/@angular/compiler@14.2.0/bundles/compiler.umd.min.js"></script>
  <script src="https://unpkg.com/@angular/platform-browser@14.2.0/bundles/platform-browser.umd.min.js"></script>
  <script src="https://unpkg.com/@angular/platform-browser-dynamic@14.2.0/bundles/platform-browser-dynamic.umd.min.js"></script>
  <script src="https://unpkg.com/@angular/forms@14.2.0/bundles/forms.umd.min.js"></script>
  <script src="https://unpkg.com/@angular/common/http@14.2.0/bundles/common-http.umd.min.js"></script>  @vite(['resources/js/libs/angular-ui-0.4.0.js'])
  <script type="module">
    import { platformBrowserDynamic } from 'https://unpkg.com/@angular/platform-browser-dynamic@14.2.0/esm2015/platform-browser-dynamic.mjs';
    import { LogsModule } from './path-to-your-compiled-js/logs-angular.js'; // Adjust path if needed

    document.addEventListener('DOMContentLoaded', () => {
      platformBrowserDynamic().bootstrapModule(LogsModule)
        .catch(err => console.error(err));
    });
  </script>
  <script>
    window.logsPerRequest = {{ $logs_per_request }};
    window.loadMoreLogsURL = "{{ URL::route('ajax_load_more_logs', ['lastKnownLogId' => 'LOG_ID', 'count' => $logs_per_request])}}";
  </script>
  @vite(['resources/js/logs-angular.ts'])
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'logs'))
  
  <h1>Logs des actions du site</h1>
  @include('pages.logs.logs-angular')
  
@stop
