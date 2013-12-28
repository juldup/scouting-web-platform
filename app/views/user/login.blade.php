@extends('base')

@section('content')
  
  @if (!$error_create)
  
    <div class="row">
      <div class="col-lg-12">
        <h2>Déjà venu ? Entrez votre adresse e-mail et votre mot de passe.</h2>
      </div>
    </div>
    @if ($error_login)
      <div class='row'>
        <div class="col-lg-12">
          <p>Mauvais pseudo, e-mail ou mot de passe. Réessayez.</p>
        </div>
      </div>
    @endif

    <form name="login" method="post" action="{{ URL::route('login') }}" onSubmit="return checkLogin();">
      <div class='row'>
        <div class='col-lg-2'>
          Pseudo ou e-mail : 
        </div>
        <div class="col-lg-3">
          <input name="login_username" type="text" id="login_username" size="35" onKeyPress="checkEnter(event);" />
        </div>
        <div class='col-lg-7'>
          Votre adresse e-mail ou votre nom d'utilisateur.
        </div>
      </div>
      <div class='row'>
        <div class='col-lg-2'>
          Mot de passe : 
        </div>
        <div class="col-lg-3">
          <input name="login_password" type="password" id="login_password" size="35" onKeyPress="checkEnter(event);" />
        </div>
        <div class='col-lg-7'>
          Votre mot de passe.
        </div>
      </div>
      <div class='row'>
        <div class='col-lg-2'>
          Mémoriser :
        </div>
        <div class="col-lg-3">
          <input type="checkbox" name="login_remember" class="checkbox" onKeyPress="checkEnter(event);" />
        </div>
        <div class='col-lg-7'>
          Mémoriser votre pseudo et mot de passe sur cet ordinateur. Vous serez connecté automatiquement à votre prochaine visite.
        </div>
      </div>
      <div class="row">
        <div class="col-lg-2">
        </div>
        <div class="col-lg-3">
          <a href="javascript:submitLogin()">Connexion</a>
        </div>
        <div class='col-lg-7'>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <a href="{{ URL::route('retrieve_password') }}">Mot de passe oublié ?</a>
        </div>
      </div>
    </form>
    
  @endif
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Nouveau sur le site ? Créez votre compte d'utilisateur.</h2>
    </div>
  </div>
  
  <form name="create" method="post" action="{{ URL::route('create_user') }}">
    <div class='row'>
      <div class='col-lg-2'>
        Pseudo : 
      </div>
      <div class="col-lg-3">
        <input name="create_username" type="text" id="create_username" size="35" onKeyPress="checkEnter(event);" />
      </div>
      <div class='col-lg-7'>
        Choisissez-vous un nom d'utilisateur.
      </div>
    </div>
    <div class='row'>
      <div class='col-lg-2'>
        Adresse e-mail :
      </div>
      <div class="col-lg-3">
        <input name="create_email" type="text" id="create_email" size="35" onKeyPress="checkEnter(event);" />
      </div>
      <div class='col-lg-7'>
        Si vous êtes membre ou parent de l'unité, veillez à utiliser l'adresse mentionnée lors de l'inscription dans l'unité.
      </div>
    </div>
    <div class='row'>
      <div class='col-lg-2'>
        Mot de passe :
      </div>
      <div class="col-lg-3">
        <input name="create_password" type="password" id="create_password" size="35" onKeyPress="checkEnter(event);" />
      </div>
      <div class='col-lg-7'>
        Choisissez un mot de passe secret.
      </div>
    </div>
    <div class='row'>
      <div class='col-lg-2'>
        Mémoriser :
      </div>
      <div class="col-lg-3">
        <input type="checkbox" name="login_remember" class="checkbox" onKeyPress="checkEnter(event);" />
      </div>
      <div class='col-lg-7'>
        Mémoriser votre pseudo et mot de passe sur cet ordinateur. Vous serez connecté automatiquement à votre prochaine visite.
      </div>
    </div>
    <div class="row">
      <div class="col-lg-2">
      </div>
      <div class="col-lg-4">
        <button type='submit'>Créer un compte</button>
      </div>
    </div>
  </form>
  
@stop