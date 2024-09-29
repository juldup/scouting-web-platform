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
  @vite(['resources/js/libs/angular-1.2.15.min.js'])
  @vite(['resources/js/libs/angular-ui-0.4.0.js'])
  <script>
    var logsPerRequest = {{ $logs_per_request }};
    var loadMoreLogsURL = "{{ URL::route('ajax_load_more_logs', ['lastKnownLogId' => 'LOG_ID', 'count' => $logs_per_request])}}";
  </script>
  @vite(['resources/js/logs-angular.js'])
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'logs'))
  
  <h1>Logs des actions du site</h1>
  @include('pages.logs.logs-angular')
  
@stop
