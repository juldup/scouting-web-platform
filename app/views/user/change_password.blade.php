@extends('base')

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  <div class="row">
    <div class='col-lg-12'>
      <h1>Changer votre mot de passe</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @if ($status == 'normal')
    <div class="row">
      <div class='col-lg-12'>
        <p>Entrez votre nouveau mot de passe.</p>
        {{ Form::open() }}
          {{ Form::label('email', 'Mot de passe :') }}
          {{ Form::password('password', '', array('size' => 35)) }}
          {{ Form::submit('Envoyer') }}
        {{ Form::close() }}
        @if ($errors->first('password'))
          <p class='alert alert-danger'>{{ $errors->first('password') }}</p>
        @endif
      </div>
    </div>
  @elseif ($status == 'unknown')
    <div class="row">
      <div class='col-lg-12'>
        <p class='alert alert-danger' >Ce lien n'est plus valide.</p>
      </div>
    </div>
  @elseif ($status == 'done')
    <div class="row">
      <div class='col-lg-12'>
        <p class='alert alert-success' >Votre mot de passe a été modifié avec succès.</p>
      </div>
    </div>
  @endif
@stop