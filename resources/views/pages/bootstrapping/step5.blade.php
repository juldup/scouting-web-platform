@extends('pages.bootstrapping.bootstrapping-base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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
  Initialisation du site - étape 5
@stop

@section('additional_javascript')
  <script>
    // Verified e-mail senders delete/add
    $().ready(function() {
      $('.safe-email-remove').click(function() {
        $(this).closest(".safe-email-row").remove();
      })
      $('.safe-email-add').click(function() {
        var newElement = $('.safe-email-row-prototype').clone(true);
        $(this).closest(".row").before(newElement);
        newElement.removeClass('safe-email-row-prototype');
        newElement.show();
      });
    });
  </script>
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 5 : Configurer l'envoi des e-mails</h1>
      @if ($error_message)
        <p class="alert alert-danger">
          {{{ $error_message }}}
        </p>
      @endif
      @if ($success_message)
        <p class="alert alert-success">
          {{{ $success_message }}}
        </p>
      @endif
      @if ($configuration)
        <p>
          Cette étape va permettre au site d'envoyer des e-mails. L'envoi d'e-mail est indispensable, car il permet aux visiteurs
          (dont les animateurs) de créer un compte d'utilisateur de manière sûre. Il permet aussi l'échange d'e-mail entre les membre
          et l'envoi d'e-mails aux membres d'une section.
        </p>
        <div class="form-horizontal well">
          {!! Form::open(array('route' => array('bootstrapping_step', 'step' => 5, 'action' => 'configuration'))) !!}
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('default_email_from_address', "Adresse e-mail du site") !!}
                <p>
                  Les e-mails partant du site (avec un expéditeur qui n'est dans la liste des adresses e-mail vérifiées ci-dessous) seront envoyé avec
                  cette adresse e-mail comme expéditeur.
                </p>
              </div>
              <div class="col-sm-5">
                {!! Form::text('default_email_from_address', Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('smtp_host', "Hôte SMTP pour l'envoi des e-mails") !!}
              </div>
              <div class="col-sm-5">
                {!! Form::text('smtp_host', Parameter::get(Parameter::$SMTP_HOST), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('smtp_port', "Port SMTP pour l'envoi des e-mails") !!}
              </div>
              <div class="col-sm-5">
                {!! Form::text('smtp_port', Parameter::get(Parameter::$SMTP_PORT), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('smtp_username', "Login SMTP pour l'envoi des e-mails") !!}
              </div>
              <div class="col-sm-5">
                {!! Form::text('smtp_username', Parameter::get(Parameter::$SMTP_USERNAME), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('smtp_password', "Mot de passe SMTP pour l'envoi des e-mails") !!}
              </div>
              <div class="col-sm-5">
                {!! Form::text('smtp_password', Parameter::get(Parameter::$SMTP_PASSWORD), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('smtp_security', "Sécurité SMTP pour l'envoi des e-mails") !!}
              </div>
              <div class="col-sm-5">
                {!! Form::text('smtp_security', Parameter::get(Parameter::$SMTP_SECURITY), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('email_safe_list[]', "Liste des adresses e-mail vérifiées") !!}
                <p>
                  Les e-mails partant du site avec un expéditeur de cette liste seront envoyés comme tels. Les autres auront pour
                  expéditeur l'adresse e-mail du site (voir ci-dessus).
                </p>
              </div>
              <div class="col-sm-5">
                @foreach ($safe_emails as $safe_email)
                  <div class="row safe-email-row">
                    <div class="col-xs-10">
                      {!! Form::text('email_safe_list[]', $safe_email, array("class" => "form-control safe-email")) !!}
                    </div>
                    <div class="col-xs-2">
                      <p class="form-side-note">
                        <span class="glyphicon glyphicon-remove safe-email-remove"></span>
                      </p>
                    </div>
                  </div>
                @endforeach
                <div class="row safe-email-row safe-email-row-prototype" style="display: none;">
                  <div class="col-xs-10">
                    {!! Form::text('email_safe_list[]', "", array("class" => "form-control safe-email")) !!}
                  </div>
                  <div class="col-xs-2">
                    <p class="form-side-note">
                      <span class="glyphicon glyphicon-remove safe-email-remove"></span>
                    </p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-10"></div>
                  <div class="col-xs-2">
                    <p class="form-side-note">
                      <span class="glyphicon glyphicon-plus safe-email-add"></span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-4">
                <input type="submit" class="btn btn-primary" value="Enregistrer">
              </div>
            </div>
          {!! Form::close() !!}
        </div>
        <p>
          Vous pouvez passer cette étape pour le moment, mais il sera indispensable d'encoder ces données pour que le site soit utilisable.
        </p>
        <a class="btn btn-default" href="{{ URL::route('bootstrapping_step', array('step' => 6)) }}">
          Passer cette étape
        </a>
      @elseif ($testing)
        @if ($success_message)
          <a class="btn btn-default" href="{{ URL::route('bootstrapping_step', array('step' => 5)) }}">
            Changer la configuration
          </a>
          <a class="btn btn-default" href="{{ URL::route('bootstrapping_step', array('step' => 5, 'action' => 'testing')) }}">
            Faire un nouveau test
          </a>
          <a class="btn btn-primary" href="{{ URL::route('bootstrapping_step', array('step' => 6)) }}">
            Passer à l'étape 6
          </a>
        @else
          <p>
            Vous pouvez vous envoyer un e-mail afin de vous assurer que l'envoi d'e-mails est fonctionnel.
          </p>
          <div class="well form-horizontal">
            {!! Form::open(array('route' => array('bootstrapping_step', 'step' => 5, 'action' => 'testing'))) !!}
              <div class="form-group">
                {!! Form::label('email', 'Votre adresse e-mail&nbsp;:', array('class' => 'control-label col-sm-4')) !!}
                <div class="col-sm-4">
                  {!! Form::text('email', '', array('class' => 'form-control')) !!}
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-4 col-sm-offset-4">
                  <input type="submit" class="btn btn-primary" value="Envoyer un e-mail de test">
                </div>
              </div>
            {!! Form::close() !!}
          </div>
          <a class="btn btn-default" href="{{ URL::route('bootstrapping_step', array('step' => 5)) }}">
            Changer la configuration
          </a>
          <a class="btn btn-default" href="{{ URL::route('bootstrapping_step', array('step' => 6)) }}">
            Passer cette étape
          </a>
        @endif
      @endif
    </div>
  </div>  
@stop
