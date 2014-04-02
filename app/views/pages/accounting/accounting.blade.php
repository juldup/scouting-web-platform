@extends('base')

@section('html_parameters')
ng-app
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/accounting-angular.js"></script>
  <script src="{{ URL::to('/') }}/js/angular.min.js"></script>
@stop

@section('content')
  <h1>Trésorie {{ $user->currentSection->de_la_section }}</h1>
  @include('pages.accounting.accounting-angular')
@stop
