@extends('base')
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

@section('title')
  Logs
@stop

@section('additional_javascript')
<!--  <script src="{{ asset('js/libs/angular-1.2.15.min.js') }}"></script>
  <script src="{{ asset('js/libs/angular-ui-0.4.0.js') }}"></script>-->
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular.js"></script>
  <script>
    var logsPerRequest = {{ $logs_per_request }};
    var loadMoreLogsURL = "{{ URL::route('ajax_load_more_logs', array('lastKownLogId' => 'LOG_ID', 'count' => $logs_per_request))}}";
  </script>
  <script src="{{ asset('js/logs-angular.js') }}"></script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'logs'))
  
  <h1>Logs des actions du site</h1>
  @include('pages.logs.logs-angular')
  
@stop
