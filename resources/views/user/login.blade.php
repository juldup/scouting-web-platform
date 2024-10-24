@extends('base')
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

use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;

?>

@section('title')
  Connexion au site
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Déjà venu ?</h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="well">
        <form name="login" class="form-horizontal" method="post" action="{{ URL::route('login_submit') }}" onSubmit="return checkLogin();">
          @csrf
          <legend>Entrez votre adresse e-mail et votre mot de passe.</legend>
          @if ($error_login)
            <p class='alert alert-danger'>Mauvais pseudo, e-mail ou mot de passe. Réessayez.</p>
          @endif
          <div class="form-group">
            <label for="login_username" class="col-md-2 control-label">
              Pseudo ou e-mail
            </label>
            <div class="col-md-3">
              <input class="form-control" name="login_username" type="text" id="login_username" size="35" onKeyPress="checkEnter(event);" />
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">Votre adresse e-mail ou votre nom d'utilisateur.</p>
            </div>
          </div>
          <div class='form-group'>
            <label for="login_password" class='col-md-2 control-label'>Mot de passe</label>
            <div class="col-md-3">
              <input class="form-control" name="login_password" type="password" id="login_password" size="35" onKeyPress="checkEnter(event);" />
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">Votre mot de passe.</p>
            </div>
          </div>
          <div class='form-group'>
            <label for="login_remember" class='col-md-2 control-label'>Mémoriser (*)</label>
            <div class="col-md-3">
              <div class="checkbox">
                <input type="checkbox" name="login_remember" class="checkbox" onKeyPress="checkEnter(event);" />
              </div>
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">
                Mémoriser votre pseudo et mot de passe sur cet ordinateur.
                Vous serez connecté automatiquement lors de votre prochaine visite.
              </p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10 col-md-offset-2">
              <a class="btn btn-primary" href="javascript:submitLogin()">Connexion</a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2">
              <p class="control-label">
                <a href="{{ URL::route('retrieve_password') }}">Mot de passe oublié ?</a>
              </p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <h1>Nouveau sur le site ?</h1>
    </div>
  </div>
  
  <div class="row">
    <a name="nouvel-utilisateur"></a>
    <div class="col-md-12">
      <div class="well">
        <form name="create" method="post" class="form-horizontal new-user-account-form obfuscated-form" data-action-url={{ URL::route('create_user') }}>
          @csrf
          <legend>Créez votre compte d'utilisateur.</legend>
          <div class='form-group'>
            <label for="create_username" class="col-md-2 control-label">Pseudo</label>
            <div class="col-md-3">
              <input type="text" name="create_username" value="" class="form-control" />
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">
                Choisissez-vous un nom d'utilisateur.
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
            <label for="create_email" class="col-md-2 control-label">Adresse e-mail</label>
            <div class="col-md-3">
              <input type="text" name="create_email" class="form-control" />
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">Si vous êtes membre ou parent de l'unité, veillez à utiliser l'adresse mentionnée lors de l'inscription dans l'unité.</p>
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
            <label for="create_password" class="col-md-2 control-label">Mot de passe</label>
            <div class="col-md-3">
              <input type="password" name="create_password" class="form-control" />
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">Choisissez un mot de passe secret.</p>
            </div>
          </div>
          @if ($errors->first('create_password'))
            <div class='form-group'>
              <div class="col-md-offset-2 col-md-8">
                <p class="alert alert-danger">{{ $errors->first('create_password') }}</p>
              </div>
            </div>
          @endif
          <div class='form-group'>
            <label for="create_remember" class="col-md-2 control-label">Mémoriser (*)</label>
            <div class="col-md-3">
              <div class="checkbox">
                <input type="checkbox" name="create_remember" value="1" />
              </div>
            </div>
            <div class='col-md-7'>
              <p class="form-side-note">
                Mémoriser votre pseudo et mot de passe sur cet ordinateur.
                Vous serez connecté automatiquement lors de votre prochaine visite.
              </p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10 col-md-offset-2">
              <input type="submit" value="Activez le javascript pour créer un compte" class="btn btn-primary" data-text="Créer un compte" disabled />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <p>(*) Cette option utilise des cookies pour enregistrer les information nécessaires sur votre ordinateur.</p>
    </div>
  </div>
  
@stop