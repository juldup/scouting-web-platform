@extends('base')

@section('title')
  Merci!
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Vous ne serez plus importuné</h1>
      <div class="alert alert-success">
        <p>
          Vous ne recevrez plus d'e-mails envoyés depuis ce site à l'adresse <strong>{{{ $email }}}</strong>.
        </p>
      </div>
    </div>
  </div>
@stop
