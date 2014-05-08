@extends("base")

@section('title')
  Accès privé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section("content")
  <h1>Accès privé</h1>
  @if ($user->isConnected)
    <p>Vous n'avez pas accès à cette page !</p>
  @else
    <div class="col-lg-12 alert alert-warning">
      <p><strong>Vous n'êtes pas connecté sur le site</strong></p>
      <p>Pour pouvoir accéder à cette page, vous devez <a href="{{ URL::route('login') }}">vous connecter</a>.</p>
    </div>
  @endif
@stop