@extends('base')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Désolés !</h1>
      <h3 class='alert alert-danger'>
        {{ $message ? $message : "Cette page n'existe pas." }}
      </h3>
      
      <p>
        <a class="btn btn-default" href="{{ URL::previous() }}">Revenir à la page précédente</a>
      </p>
    </div>
  </div>
@stop