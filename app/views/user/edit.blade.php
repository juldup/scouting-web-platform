@extends('base')

@section('content')
  
  <div class="row">
    <div class='col-lg-12'>
      <h1>Vos données personnelles</h1>
      @if (Session::has('success_message'))
        <p class='alert alert-success'>{{ Session::get('success_message') }}</p>
      @endif
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-2'>
      <p>Nom d'utilisateur</p>
    </div>
    <div class='col-lg-4'>
      <p>{{ $user->username }}</p>
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-2'>
      <p>Adresse e-mail</p>
    </div>
    <div class='col-lg-4'>
      <p>{{ $user->email }}</p>
    </div>
    <div class="col-lg-6">
      <p>
        <a href="{{ URL::route('edit_user_email') }}">Changer mon adresse e-mail</a>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-2'>
      <p>Mot de passe</p>
    </div>
    <div class='col-lg-4'>
      <p>******</p>
    </div>
    <div class="col-lg-6">
      <p>
        <a href="{{ URL::route('edit_user_password') }}">Changer mon mot de passe</a>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-2'>
      <p>Section par défaut</p>
    </div>
    <div class='col-lg-4'>
      <p>{{ $user->getDefaultSection()->name }}</p>
    </div>
    <div class="col-lg-6">
      <p>
        <a href="{{ URL::route('edit_user_section') }}">Changer mon section par défaut</a>
      </p>
    </div>
  </div>
  
  @if ($action)
    
    <div class="row">
      <div class='col-lg-12'>
        <h1>Modification</h1>
      </div>
    </div>
    
    {{ Form::open() }}
      @if ($action != 'section')
        <div class="row">
          <div class='col-lg-2'>
            {{ Form::label('old_password', "Mot de passe actuel") }}
          </div>
          <div class='col-lg-3'>
            {{ Form::password('old_password') }}
          </div>
          <div class='col-lg-7'>
            @if ($errors->first('old_password'))
              <p class="alert alert-danger">{{ $errors->first('old_password') }}</p>
            @endif
          </div>
        </div>
      @endif
      @if ($action == 'email')
        <div class="row">
          <div class='col-lg-2'>
            {{ Form::label('email', "Nouvelle adresse e-mail") }}
          </div>
          <div class='col-lg-4'>
            {{ Form::text('email') }}
          </div>
          <div class='col-lg-6'>
            @if ($errors->first('email'))
              <p class="alert alert-danger">{{ $errors->first('email') }}</p>
            @endif
          </div>
        </div>
      @elseif ($action == 'password')
        <div class="row">
          <div class='col-lg-2'>
            {{ Form::label('password', "Nouveau mot de passe") }}
          </div>
          <div class='col-lg-3'>
            {{ Form::password('password') }}
          </div>
          <div class='col-lg-6'>
            @if ($errors->first('password'))
              <p class="alert alert-danger">{{ $errors->first('password') }}</p>
            @endif
          </div>
        </div>
      @elseif ($action == 'section')
        <div class="row">
          <div class='col-lg-2'>
            {{ Form::label('default_section', "Section par défaut") }}
          </div>
          <div class='col-lg-3'>
            {{ Form::select('default_section', $sections, $user->default_section) }}
          </div>
        </div>
      @endif
      <div class="row">
        <div class='col-lg-2'>
        </div>
        <div class='col-lg-3'>
          {{ Form::submit('Valider') }}
        </div>
      </div>
    {{ Form::close() }}
  @endif
  
@stop