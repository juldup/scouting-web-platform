@extends('base')

@section('title')
  Gestion de l'année des scouts
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/manage_registration.js"></script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('registration', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la page d'inscription
    </a>
  </p>
@stop

@section('content')
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'change_year'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Changer l'année des scouts {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
@stop