@extends("base")

@section('title')
  Accès privé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section("content")
  <h1>Accès privé</h1>
  @include('subviews.limitedAccess')
@stop