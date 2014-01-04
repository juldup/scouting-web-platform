@extends('base')

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <div class="well">
        <form name="login" class="form-horizontal" method="post" action="{{ URL::route('login_submit') }}" onSubmit="return checkLogin();">
          <legend>Déjà venu ? Entrez votre adresse e-mail et votre mot de passe.</legend>
          @if ($error_login)
            <p class='alert alert-danger'>Mauvais pseudo, e-mail ou mot de passe. Réessayez.</p>
          @endif
          <div class="form-group">
            <label for="login_username" class="col-lg-2 control-label">
              Pseudo ou e-mail
            </label>
            <div class="col-lg-3">
              <input class="form-control" name="login_username" type="text" id="login_username" size="35" onKeyPress="checkEnter(event);" />
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">Votre adresse e-mail ou votre nom d'utilisateur.</p>
            </div>
          </div>
          <div class='form-group'>
            <label for="login_password" class='col-lg-2 control-label'>Mot de passe</label>
            <div class="col-lg-3">
              <input class="form-control" name="login_password" type="password" id="login_password" size="35" onKeyPress="checkEnter(event);" />
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">Votre mot de passe.</p>
            </div>
          </div>
          <div class='form-group'>
            <label for="login_remember" class='col-lg-2 control-label'>Mémoriser (*)</label>
            <div class="col-lg-3">
              <div class="checkbox">
                <input type="checkbox" name="login_remember" class="checkbox" onKeyPress="checkEnter(event);" />
              </div>
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">
                Mémoriser votre pseudo et mot de passe sur cet ordinateur.
                Vous serez connecté automatiquement à votre prochaine visite.
              </p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
              <a class="btn btn-primary" href="javascript:submitLogin()">Connexion</a>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2">
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
    <a name="nouvel-utilisateur"></a>
    <div class="col-lg-12">
      <div class="well">
        {{ Form::open(array('route' => 'create_user', 'name' => 'create', 'class' => 'form-horizontal')) }}
          <legend>Nouveau sur le site ? Créez votre compte d'utilisateur.</legend>
          <div class='form-group'>
            <div class="col-lg-2 control-label">
              {{ Form::label('create_username', "Pseudo") }}
            </div>
            <div class="col-lg-3">
              {{ Form::text('create_username', "", array('class' => 'form-control')) }}
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">
                Choisissez-vous un nom d'utilisateur.
              </p>
            </div>
          </div>
          <div class='form-group'>
            <div class="col-lg-offset-2 col-lg-8">
              @if ($errors->first('create_username'))
                <p class="alert alert-danger">{{ $errors->first('create_username') }}</p>
              @endif
            </div>
          </div>
          <div class='form-group'>
            <div class='col-lg-2 control-label'>
              {{ Form::label('create_email') }}
            </div>
            <div class="col-lg-3">
              {{ Form::text('create_email', "", array('class' => 'form-control')) }}
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">Si vous êtes membre ou parent de l'unité, veillez à utiliser l'adresse mentionnée lors de l'inscription dans l'unité.</p>
            </div>
          </div>
          <div class='form-group'>
            <div class="col-lg-offset-2 col-lg-8">
              @if ($errors->first('create_email'))
                <p class="alert alert-danger">{{ $errors->first('create_email') }}</p>
              @endif
            </div>
          </div>
          <div class='form-group'>
            <div class='col-lg-2 control-label'>
              {{ Form::label('create_password', "Mot de passe") }}
            </div>
            <div class="col-lg-3">
        {{ Form::password('create_password', array('class' => 'form-control')) }}
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">Choisissez un mot de passe secret.</p>
            </div>
          </div>
          <div class='form-group'>
            <div class="col-lg-offset-2 col-lg-8">
              @if ($errors->first('create_password'))
                <p class="alert alert-danger">{{ $errors->first('create_password') }}</p>
              @endif
            </div>
          </div>
          <div class='form-group'>
            <div class='col-lg-2 control-label'>
              {{ Form::label('create_remember', "Mémoriser (*)") }}
            </div>
            <div class="col-lg-3">
              <div class="checkbox">
                {{ Form::checkbox('create_remember', 1, false) }}
              </div>
            </div>
            <div class='col-lg-7'>
              <p class="form-side-note">
                Mémoriser votre pseudo et mot de passe sur cet ordinateur.
                Vous serez connecté automatiquement à votre prochaine visite.
              </p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
              <button class="btn btn-primary" type='submit'>Créer un compte</button>
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <p>(*) Cette option utilise des cookies pour enregistrer les information nécessaires sur votre ordinateur.</p>
    </div>
  </div>
  
@stop