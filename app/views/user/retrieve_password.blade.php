@extends('base')

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  <div class="row">
    <div class='col-lg-12'>
      <h1>Récupérer votre mot de passe</h1>
      @if (Session::has('success_message'))
        <p class='alert alert-success'>{{ Session::get('success_message') }}</p>
      @endif
      @if (Session::has('error_message'))
        <p class='alert alert-danger'>{{ Session::get('error_message') }}</p>
      @endif
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      <p>Entrez votre adresse e-mail. Un lien pour changer votre mot de passe vous sera envoyé.</p>
      {{ Form::open() }}
        {{ Form::label('email', 'Adresse e-mail :') }}
        {{ Form::text('email', '', array('size' => 35)) }}
        {{ Form::submit('Envoyer') }}
      {{ Form::close() }}
    </div>
  </div>
@stop