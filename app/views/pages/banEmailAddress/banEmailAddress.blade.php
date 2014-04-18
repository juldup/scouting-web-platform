@extends('base')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Désinscrire votre adresse e-mail</h1>
      <div class="alert alert-danger">
        <p>
          Souhaitez-vous supprimer l'adresse <strong>{{{ $email }}}</strong> de notre liste de destinataires&nbsp;?
        </p>
        <p>
          Vous ne recevrez plus aucun e-mail envoyé depuis ce site web.
        </p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-6">
      <a class="btn btn-primary" href="{{ URL::route('confirm_ban_email', array('ban_code' => $ban_code)) }}">Confirmer</a>
    </div>
    <div class="col-xs-6 text-right">
      <a class="btn btn-default" href="{{ URL::route('home') }}">Annuler</a>
    </div>
  </div>
@stop