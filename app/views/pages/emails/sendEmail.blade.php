@extends('base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/
?>

@section('title')
  Envoyer un e-mail
@stop

@section('back_links')
  @if (Parameter::get(Parameter::$SHOW_EMAILS))
    @if ($user->can(Privilege::$SEND_EMAILS))
      <p>
        <a href="{{ URL::route('manage_emails') }}" >
          Liste des e-mails
        </a>
      </p>
    @else
      <p>
        <a href="{{ URL::route('emails') }}" >
          Liste des e-mails
        </a>
      </p>
    @endif
  @endif
@stop

@section('forward_links')
  @if ($target == 'parents')
    <p>
      <a href="{{ URL::route('send_leader_email') }}" >
        Envoyer un e-mail aux animateurs
      </a>
    </p>
  @elseif ($user->can(Privilege::$SEND_EMAILS))
    <p>
      <a href="{{ URL::route('send_section_email') }}" >
        Envoyer un e-mail aux parents
      </a>
    </p>
  @endif
@stop

@section('additional_javascript')
  <script src="{{ asset('js/send-section-email.js') }}"></script>
  <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
  <script>
    CKEDITOR.replace('body', {
      language: 'fr',
      extraPlugins: 'divarea,mediaembed',
      height: '250px',
      filebrowserImageUploadUrl: "{{ URL::route('ajax_upload_image') }}",
    });
    var defaultSubject = "{{ Helper::sanitizeForJavascript($default_subject) }}";
  </script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'email-section'))
  
  <div class="row">
    <div class="col-md-12">
      @if ($target == 'leaders')
        <h1>Envoi d'un e-mail <strong>aux animateurs</strong> {{{ $user->currentSection->de_la_section }}}</h1>
      @else
        <h1>Envoi d'un e-mail <strong>aux parents</strong> {{{ $user->currentSection->de_la_section }}}</h1>
      @endif
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="form-horizontal well">
        {{ Form::open(array('id' => "email-form", 'files' => true,
                  'url' => URL::route('send_section_email_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('target', $target) }}
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
              <span class="no-wrap">
                {{ Form::label('sender_address', 'Adresse') }} :
                {{ Form::text('sender_address', $user->currentSection->email, array('class' => 'form-control large')) }}
              </span>
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('attachments', "Pièces jointes", array('class' => 'col-md-2 control-label')) }}
            <div class="col-md-5">
              <div class="attachment-input-wrapper" style='display: none;'>
                {{ Form::file('attachments[0]', array('class' => 'btn-sm btn-default')) }}
                <a class="remove-attachment"><span class="glyphicon glyphicon-remove"></span></a>
              </div>
              <a id="add-attachment-button" class="btn-sm btn-default">Ajouter</a>
            </div>
          </div>
          
          <legend>Choix des destinataires</legend>
          <div class="recipient-list-wrapper">
            @if (count($recipients) >= 1)
              <div class="form-group">
                <div class="col-md-8">
                  <p>
                    <a class="btn-sm btn-default recipient-check-all" href=""><span class="glyphicon glyphicon-check"></span></a>
                    <a class="btn-sm btn-default recipient-uncheck-all" href=""><span class="glyphicon glyphicon-unchecked"></span></a>
                  </p>
                </div>
              </div>
            @endif
            <div class="recipient-list">
              @foreach ($recipients as $superCategory=>$subCategory)
                <div class="recipient-list-wrapper">
                  @if ($superCategory)
                    <div class="form-group">
                      <div class="col-md-12">
                        {{ Form::label(null, $superCategory, array('class' => 'recipient-category')) }}
                        &nbsp;
                        &nbsp;
                        <a class="btn-sm btn-default recipient-check-all" href=""><span class="glyphicon glyphicon-check"></span></a>
                        <a class="btn-sm btn-default recipient-uncheck-all" href=""><span class="glyphicon glyphicon-unchecked"></span></a>
                        <span class="recipient-list-warning" style="display:none;">&nbsp;&nbsp;Les destinaires cachés restent cochés/décochés.</span>
                      </div>
                    </div>
                  @endif
                  <div class="recipient-list">
                    @foreach ($subCategory as $category=>$members)
                      <div class="recipient-list-wrapper">
                        <div class="form-group">
                          @if ($superCategory)
                            <div class="col-md-offset-1 col-md-11">
                          @else
                            <div class="col-md-12">
                          @endif
                            {{ Form::label(null, $category, array('class' => 'recipient-category')); }}
                            &nbsp;
                            &nbsp;
                            <a class="btn-sm btn-default recipient-check-all" href=""><span class="glyphicon glyphicon-check"></span></a>
                            <a class="btn-sm btn-default recipient-uncheck-all" href=""><span class="glyphicon glyphicon-unchecked"></span></a>
                            <span class="recipient-list-warning" style="display:none;">&nbsp;&nbsp;Les destinaires cachés restent cochés/décochés.</span>
                          </div>
                        </div>
                        <div class="form-group recipient-list">
                          @if ($superCategory)
                            <div class="col-md-10 col-md-offset-2">
                          @else
                            <div class="col-md-11 col-md-offset-1">
                          @endif
                            @foreach ($members as $member)
                              <div class="col-md-4">
                                <p>
                                  {{ Form::checkbox($member['type'] . "_" . $member['member']->id, 1, $preselectedRecipients ? $member['preselected'] : true, array('class' => 'recipient-checkbox')) }}
                                  &nbsp;&nbsp;
                                  {{{ $member['member']->first_name }}} {{{ $member['member']->last_name }}}
                                </p>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('extra_recipients', "Destinataires supplémentaires", array('class' => 'col-md-3')) }}
          </div>
          <div class="form-group">
            <div class="col-md-12">
              {{ Form::textarea('extra_recipients', '', array('rows' => 3, 'class' => 'form-control', 'placeholder' => "Tu peux ajouter des destinataires supplémentaires. Tape ici leurs adresses e-mail séparées par des virgules.")) }}
            </div>
          </div>
          
          <legend>Envoyer</legend>
          @if ($target != 'leaders')
            <p class="alert alert-danger">
              ATTENTION ! Les e-mails envoyés via cette page seront visibles sur le site par <strong>TOUS LES MEMBRES</strong> de l'unité.
              N'envoie pas d'e-mails à caractère personnel.
            </p>
          @else
            <p class="alert alert-danger">
              Cet e-mail sera visibles sur le site par <strong>TOUS LES ANIMATEURS</strong> de l'unité.
            </p>
          @endif
          <div class="form-group">
            <div class="col-md-8 col-md-offset-2">
              {{ Form::submit('Envoyer maintenant', array('class' => 'btn btn-primary')) }}
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@stop
