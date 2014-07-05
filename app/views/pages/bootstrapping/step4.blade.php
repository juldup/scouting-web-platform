@extends('pages.bootstrapping.bootstrapping-base')
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
  Initialisation du site - étape 4
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 4 : Créer un compte d'utilisateur pour le webmaster</h1>
      @if ($existing_webmaster)
        <p class="alert alert-success">
          Il y a déjà un compte d'utilisateur pour le webmaster sur ce site&nbsp;: <strong>{{ $existing_webmaster->username }}</strong>.
        </p>
        <p>
          <a class="btn btn-primary" href="{{ URL::route('bootstrapping_step', array('step' => 5)) }}">
            Passer à l'étape 5
          </a>
        </p>
      @else
        <p>
          Le webmaster est un utilisateur qui a tous les pouvoirs sur le site. Crée maintenant son compte d'utilisateur.
        </p>
        @if ($error_message)
          <p class="alert alert-danger">{{ $error_message }}</p>
        @endif
        <div class="well form-horizontal">
          {{ Form::open() }}
            <div class='form-group'>
              {{ Form::label('username', "Pseudo", array('class' => "col-md-2 control-label")) }}
              <div class="col-md-3">
                {{ Form::text('username', "Webmaster", array('class' => 'form-control')) }}
              </div>
              <div class='col-md-7'>
                <p class="form-side-note">
                  Nom d'utilisateur du webmaster
                </p>
              </div>
            </div>
            @if ($errors->first('create_username'))
              <div class='form-group'>
                <div class="col-md-offset-2 col-md-8">
                  <p class="alert alert-danger">{{ $errors->first('create_username') }}</p>
                </div>
              </div>
            @endif
            <div class='form-group'>
              {{ Form::label('email', "Adresse e-mail",array('class' => "col-md-2 control-label")) }}
              <div class="col-md-3">
                {{ Form::text('email', "", array('class' => 'form-control')) }}
              </div>
              <div class='col-md-7'>
                <p class="form-side-note">Adresse e-mail du webmaster. Pourra être changée par la suite.</p>
              </div>
            </div>
            @if ($errors->first('create_email'))
              <div class='form-group'>
                <div class="col-md-offset-2 col-md-8">
                  <p class="alert alert-danger">{{ $errors->first('create_email') }}</p>
                </div>
              </div>
            @endif
            <div class='form-group'>
              {{ Form::label('password', "Mot de passe", array('class' => "col-md-2 control-label")) }}
              <div class="col-md-3">
                {{ Form::password('password', array('class' => 'form-control')) }}
              </div>
              <div class='col-md-7'>
                <p class="form-side-note">
                  <span class="danger">
                    Attention&nbsp;! Choisissez un mot de passe robuset, car quelqu'un qui devinerait ce mot de passe pourrait
                    accéder à toutes les informations privées du site et des membres de l'unité.
                  </span>
                  Pourra être changé par la suite.
                </p>
              </div>
            </div>
            @if ($errors->first('create_password'))
              <div class='form-group'>
                <div class="col-md-offset-2 col-md-8">
                  <p class="alert alert-danger">{{ $errors->first('create_password') }}</p>
                </div>
              </div>
            @endif
            <div class="form-group">
              <div class="col-md-10 col-md-offset-2">
                <button class="btn btn-primary" type='submit'>Créer un compte</button>
              </div>
            </div>
          {{ Form::close() }}
        </div>
      @endif
    </div>
  </div>  
@stop
