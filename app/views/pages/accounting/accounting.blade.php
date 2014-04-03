@extends('base')

@section('html_parameters')
  
@stop

@section('head')
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/libs/jquery-ui-1.10.4.js"></script>
  <script src="{{ URL::to('/') }}/js/libs/angular-1.2.15.min.js"></script>
  <script src="{{ URL::to('/') }}/js/libs/angular-ui-0.4.0.js"></script>
  <script src="{{ URL::to('/') }}/js/accounting-angular.js"></script>
@stop

@section('content')
  <h1>Trésorie {{ $user->currentSection->de_la_section }}</h1>
  @include('pages.accounting.accounting-angular')
@stop
