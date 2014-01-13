@extends('base')

@section('forward_links')
  @if ($can_send_emails)
    <p>
      <a href="{{ URL::route('send_section_email') }}">
        Envoyer un e-mail
      </a>
    </p>
    <p>
      <a href="{{ URL::route('manage_emails') }}" >
        Gérer les e-mails
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>E-mails {{ $user->currentSection->de_la_section }}</h1>
    </div>
  </div>
  
@stop