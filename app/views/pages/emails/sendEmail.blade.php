@extends('base')

@section('back_links')
  <p>
    <a href="{{ URL::route('manage_emails') }}" >
      Liste des e-mails
    </a>
  </p>
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/sendSectionEmail.js"></script>
  <script src="{{ URL::to('/') }}/ckeditor/ckeditor.js"></script>
  <script>
    CKEDITOR.replace('body', {
      language: 'fr',
      extraAllowedContent: 'img[!src,width,height]',
      extraPlugins: 'divarea',
      height: '250px'
    });
    var defaultSubject = "{{ Helper::sanitizeForJavascript($default_subject) }}";
  </script>
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Envoi d'un e-mail aux parents {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="form-horizontal well">
        {{ Form::open(array('id' => "email-form", 'files' => true, 'url' => URL::route('send_section_email_submit', array('section_slug' => $user->currentSection->slug)))) }}
        <legend>E-mail</legend>
        <div class="form-group">
          {{ Form::label('subject', "Sujet", array('class' => 'col-md-2 control-label')) }}
          <div class="col-md-5">
            {{ Form::text('subject', $default_subject, array('class' => "form-control")) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label('body', "Message", array('class' => 'col-md-2 control-label')) }}
          <div class="col-md-10">
            {{ Form::textarea('body', '', array('class' => 'form-control')) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label('sender_address', "Expéditeur", array('class' => 'col-md-2 control-label')) }}
          <div class="col-md-10">
            {{ Form::label('sender_name', 'Nom') }} :
            {{ Form::text('sender_name', $user->currentSection->name, array('class' => 'form-control large')) }}
            <span class="horiz-divider"></span>
            {{ Form::label('sender_address', 'Adresse') }} :
            {{ Form::text('sender_address', $user->currentSection->email, array('class' => 'form-control very-large')) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label('attachments', "Pièces jointes", array('class' => 'col-md-2 control-label')) }}
          <div class="col-md-5">
            <div class="attachment-input-wrapper" style='display: none;'>
              {{ Form::file('attachments[0]', array('class' => 'btn btn-default')) }}
              <a class="remove-attachment btn btn-default">Supprimer</a>
            </div>
            <a id="add-attachment-button" class="btn btn-default">Ajouter</a>
          </div>
        </div>
        
        <legend>Choix des destinataires</legend>
        <div class="recipient-list">
          @if (count($recipients) >= 1)
            <div class="form-group">
              <div class="col-md-8 col-md-offset-4">
                <p>
                  <a class="btn-sm btn-default recipient-check-all" href="">Sélectionner tous les destinataires</a>
                  <a class="btn-sm btn-default recipient-uncheck-all" href="">Désélectionner tous les destinataires</a>
                </p>
              </div>
            </div>
          @endif
          @foreach ($recipients as $category=>$members)
          <div class="form-group">
            {{ Form::label(null, $category, array('class' => 'control-label col-md-4')); }}
            <div class="col-md-8 recipient-list">
              <p>
                <a class="btn-sm btn-default recipient-check-all" href="">Sélectionner tout</a>
                <a class="btn-sm btn-default recipient-uncheck-all" href="">Désélectionner tout</a>
              </p>
              @foreach ($members as $member)
                <p>
                  {{ Form::checkbox($member['type'] . "_" . $member['member']->id, 1, true, array('class' => 'recipient-checkbox')) }}
                  <span class="horiz-divider"></span>
                  {{ $member['member']->first_name }} {{ $member['member']->last_name }}
                </p>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
        <div class="form-group">
          {{ Form::label('extra_recipients', "Destinataires supplémentaires", array('class' => 'control-label col-md-4')) }}
          <div class="col-md-6">
            {{ Form::textarea('extra_recipients', '', array('rows' => 3, 'class' => 'form-control', 'placeholder' => "Tu peux ajouter des destinataires supplémentaires. Entre leurs adresses e-mail séparées par des virgules.")) }}
          </div>
        </div>
        
        <legend>Envoyer</legend>
        <div class="form-group">
          <div class="col-md-8 col-md-offset-4">
            {{ Form::submit('Envoyer', array('class' => 'btn btn-primary')) }}
          </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@stop