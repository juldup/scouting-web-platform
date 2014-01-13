@extends('base')

@section('back_links')
  <p>
    <a href="{{ URL::route('emails') }}">
      Retour aux e-mails
    </a>
  </p>
@stop

@section('forward_links')
  <p>
    <a href="{{ URL::route('send_section_email') }}">
      Envoyer un e-mail
    </a>
  </p>
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <h1>Gestion des e-mails {{ $user->currentSection->de_la_section }}</h1>
  </div>
</div>
@stop