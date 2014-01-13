@extends('base')

@section('back_links')
  <p>
    <a href="{{ URL::route('manage_emails') }}" >
      Liste des e-mails
    </a>
  </p>
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/ckeditor/ckeditor.js"></script>
  <script>
    CKEDITOR.replace('body', {
      language: 'fr',
      extraAllowedContent: 'img[!src,width,height]',
      extraPlugins: 'divarea',
      height: '250px'
    });
  </script>
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Envoi d'un e-mail aux parents {{ $user->currentSection->de_la_section }}
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="form-horizontal well">
        {{ Form::open(array('files' => true, 'url' => URL::route('send_section_email_submit', array('section_slug' => $user->currentSection->slug)))) }}
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
            {{ Form::file('attachments[0]', array('class' => 'form-control btn btn-default')) }}
            {{ Form::file('attachments[1]', array('class' => 'form-control btn btn-default')) }}
            {{ Form::file('attachments[2]', array('class' => 'form-control btn btn-default')) }}
            {{ Form::file('attachments[3]', array('class' => 'form-control btn btn-default')) }}
          </div>
        </div>
        
        <legend>Choix des destinataires</legend>
        
        {{ Form::close() }}
      </div>
    </div>
  </div>
@stop