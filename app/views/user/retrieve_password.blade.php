@extends('base')

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  <div class="row">
    <div class='col-lg-12'>
      <h1>Récupérer votre mot de passe</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      <p>Entrez votre adresse e-mail. Un lien pour changer votre mot de passe vous sera envoyé.</p>
      {{ Form::open() }}
        {{ Form::label('email', 'Adresse e-mail :') }}
        {{ Form::text('email', '', array('class' => 'form-control very-large')) }}
        {{ Form::submit('Envoyer', array('class' => 'btn btn-primary')) }}
      {{ Form::close() }}
    </div>
  </div>
@stop