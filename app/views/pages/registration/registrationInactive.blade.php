@extends('base')

@section('title')
  Inscriptions
@stop

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_registration') }}'>
        Gérer les inscriptions
      </a>
    </p>
  @endif
@stop

@section('content')
  <div class="row page_body">
    <div class="col-md-12">
      <h1>{{ $page_title }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  <div class="row page_body">
    <div class="col-md-12 alert alert-warning">
      <p>
        Les inscriptions ne sont pas ouvertes pour l'instant.
      </p>
      <p>
        Pour toute question à ce propos, veuillez contacter le <a href="{{ URL::route('contacts') }}">staff d'unité</a>.
      </p>
    </div>
  </div>
@stop
