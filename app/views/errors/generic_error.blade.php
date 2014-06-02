@extends('base')

@section('title')
  Erreur
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Désolés !</h1>
      <p class='alert alert-danger'>
        Une erreur inconnue s'est produite. Veuillez réessayer plus tard ou <a href="{{ URL::route('contacts') }}#webmaster">contacter le webmaster</a>.
      </p>
      
      <p>
        <a class="btn btn-default" href="{{ URL::previous() }}">Revenir à la page précédente</a>
      </p>
    </div>
  </div>
@stop