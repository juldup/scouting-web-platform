@extends('base')

@section('back_links')
  @if ($showing_archives)
    <p>
      <a href='{{ URL::route('emails', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux e-mails de cette année
      </a>
    </p>
  @endif
@stop

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
      <h1>E-mails {{ $user->currentSection->de_la_section }} @if ($showing_archives) (archives) @endif</h1>
    </div>
  </div>
  
  @if (count($emails) == 0)
    <div class="row">
      <div class="col-lg-12">
        <p>Il n'y a aucun e-mail.</p>
      </div>
    </div>
  @endif
  
  @if ($user->isMember())
    
    @foreach($emails as $email)
      <div class="row">
        <div class="col-md-12">
          <div class="well">
            <legend>
              {{ $email->subject }} – {{ Helper::dateToHuman($email->date) }} à {{ Helper::timeToHuman($email->time) }}
            </legend>
            <p>
              {{ $email->body_html }}
            </p>
            @if ($email->hasAttachments())
              <p class="email-attachment-list">
                @if (count($email->getAttachments()) == 1)
                  <strong>Pièce jointe :</strong>
                @else
                  <strong>Pièces jointes :</strong>
                @endif
                @foreach ($email->getAttachments() as $attachment)
                  <a href="{{ URL::route('download_attachment', array('attachment_id' => $attachment->id)) }}">
                    {{ $attachment->filename }}
                  </a>
                  <span class="horiz-divider"></span>
                @endforeach
              </p>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  @else
    @include('subviews.limitedAccess')
  @endif
  
  @if ($has_archives)
    <div class="vertical-divider"></div>
    @if ($showing_archives)
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('email_archives', array('section_slug' => $user->currentSection->slug, 'page' => $next_page)) }}">Voir les e-mails plus anciens</a>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('email_archives', array('section_slug' => $user->currentSection->slug)) }}">Voir les e-mails archivés</a>
        </div>
      </div>
    @endif
  @endif
  
@stop