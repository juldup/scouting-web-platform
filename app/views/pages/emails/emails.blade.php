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
    XXX
  @endif
  
@stop