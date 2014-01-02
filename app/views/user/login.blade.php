@extends('base')

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Déjà venu ? Entrez votre adresse e-mail et votre mot de passe.</h2>
    </div>
  </div>
  @if ($error_login)
    <div class='row'>
      <div class="col-lg-12">
        <p class='alert alert-danger'>Mauvais pseudo, e-mail ou mot de passe. Réessayez.</p>
      </div>
    </div>
  @endif

  <form name="login" method="post" action="{{ URL::route('login_submit') }}" onSubmit="return checkLogin();">
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
        
        ATTENTION: cette option utilise des cookies.
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
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Nouveau sur le site ? Créez votre compte d'utilisateur.</h2>
    </div>
  </div>
  
  <!--<form name="create" method="post" action="{{ URL::route('create_user') }}">-->
  {{ Form::open(array('route' => 'create_user', 'name' => 'create')) }}
    <div class='row'>
      <div class='col-lg-2'>
        {{ Form::label('create_username', "Pseudo :") }}
      </div>
      <div class="col-lg-3">
        {{ Form::text('create_username', "", array('size' => '35')) }}
      </div>
      <div class='col-lg-7'>
        <p>Choisissez-vous un nom d'utilisateur.</p>
        @if ($errors->first('create_username'))
          <p class="alert alert-danger">{{ $errors->first('create_username') }}</p>
        @endif
      </div>
    </div>
    <div class='row'>
      <div class='col-lg-2'>
        {{ Form::label('create_email') }}
      </div>
      <div class="col-lg-3">
        {{ Form::text('create_email', "", array('size' => '35')) }}
      </div>
      <div class='col-lg-7'>
        <p>Si vous êtes membre ou parent de l'unité, veillez à utiliser l'adresse mentionnée lors de l'inscription dans l'unité.</p>
        @if ($errors->first('create_email'))
          <p class="alert alert-danger">{{ $errors->first('create_email') }}</p>
        @endif
      </div>
    </div>
    <div class='row'>
      <div class='col-lg-2'>
        {{ Form::label('create_password', "Mot de passe :") }}
      </div>
      <div class="col-lg-3">
        {{ Form::password('create_password', "", array('size' => '35')) }}
      </div>
      <div class='col-lg-7'>
        <p>Choisissez un mot de passe secret.</p>
        @if ($errors->first('create_password'))
          <p class="alert alert-danger">{{ $errors->first('create_password') }}</p>
        @endif
      </div>
    </div>
    <div class='row'>
      <div class='col-lg-2'>
        {{ Form::label('create_remember', "Mémoriser :") }}
      </div>
      <div class="col-lg-3">
        {{ Form::checkbox('create_remember', 1, false, array('class' => 'checkbox')) }}
      </div>
      <div class='col-lg-7'>
        Mémoriser votre pseudo et mot de passe sur cet ordinateur. Vous serez connecté automatiquement à votre prochaine visite.
        ATTENTION: cette option utilise des cookies.
      </div>
    </div>
    <div class="row">
      <div class="col-lg-2">
      </div>
      <div class="col-lg-4">
        <button type='submit'>Créer un compte</button>
      </div>
    </div>
  {{ Form::close() }}
  
@stop