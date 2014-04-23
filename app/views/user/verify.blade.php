@extends('base')

@section('title')
  Activation de mon compte d'utilisateur
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class="row">
    <div class='col-lg-8'>
      @if ($status == 'verified')
        <p class="alert alert-success">Merci ! Votre compte d'utilisateur est à présent actif.</p>
      @elseif ($status == 'unknown')
        <p class="alert alert-danger">Ce code d'activation n'existe pas. Avez-vous correctement recopié l'adresse ?</p>
      @elseif ($status == 'canceled')
        <p class='alert alert-success'>Ce compte d'utilisateur a été supprimé. Merci pour votre coopération.</p>
      @elseif ($status == 'already verified')
        <p class='alert alert-danger'>Ce compte d'utilisateur déjà été activé et ne peut être supprimé.</p>
      @endif
    </div>
  </div>
  
@stop