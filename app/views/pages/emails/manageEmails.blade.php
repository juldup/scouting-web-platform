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
  
  @include('subviews.contextualHelp', array('help' => 'edit-emails'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Gestion des e-mails {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @foreach($emails as $email)
    <div class="row">
      <div class="col-md-12">
        <div class="well">
          <legend>
            <div class="row">
              <div class="col-md-9">
                {{ $email->subject }} – {{ Helper::dateToHuman($email->date) }}
              </div>
              <div class="col-md-3 text-right">
                @if ($email->canBeDeleted())
                  <a class="btn-sm btn-default" href="{{ URL::route('manage_emails_delete', array('email_id' => $email->id)) }}">
                    Supprimer
                  </a>&nbsp;
                @endif
                <a class="btn-sm btn-default" href="#">Archiver</a>
              </div>
            </div>
          </legend>
          <p>
            {{ $email->body_html }}
          </p>
          <p>&nbsp;</p>
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
          <p class="email-recipient-list">
            <strong>Destinataires :</strong>
            {{ $email->recipient_list }}
          </p>
        </div>
      </div>
    </div>
  @endforeach
  
@stop