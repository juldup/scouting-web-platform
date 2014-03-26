@extends('base')

@section('content')
  @include('subviews.contextualHelp', array('help' => 'parameters'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Paramètres du site</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-md-12'>
      <div id="section_form" class="form-horizontal well">
        {{ Form::open(array('url' => URL::route('edit_parameters_submit', array('section_slug' => $user->currentSection->slug)))) }}
          <div class="form-group">
            <div class="col-md-12 text-right">
              <input type="submit" class="btn btn-primary" value="Enregistrer les changements"/>
            </div>
          </div>
          <legend>Prix des cotisations</legend>
          <div class="form-group">
            <div class="col-sm-3 col-lg-2 col-sm-offset-6 col-md-offset-4"><label class="control-label">Scout</label></div>
            <div class="col-sm-3"><label class="control-label">Animateur</label></div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 col-md-4"><label class="control-label">1 membre dans la famille</label></div>
            <div class="col-sm-3 col-lg-2">{{ Form::text('price_1_child', $prices['1 child'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
            <div class="col-sm-3">{{ Form::text('price_1_leader', $prices['1 leader'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 col-md-4"><label class="control-label">2 membres dans la famille</label></div>
            <div class="col-sm-3 col-lg-2">{{ Form::text('price_2_children', $prices['2 children'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
            <div class="col-sm-3">{{ Form::text('price_2_leaders', $prices['2 leaders'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 col-md-4"><label class="control-label">3 membres ou plus dans la famille</label></div>
            <div class="col-sm-3 col-lg-2">{{ Form::text('price_3_children', $prices['3 children'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
            <div class="col-sm-3">{{ Form::text('price_3_leaders', $prices['3 leaders'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
          </div>
          <legend>Inscriptions</legend>
          <div class="form-group">
            <div class="col-lg-5 col-md-6 col-sm-9 control-label">
              {{ Form::label("registration_active", "Activer les inscriptions") }}
              <span class="horiz-divider"></span>
              {{ Form::checkbox("registration_active", 1, $registration_active) }}
            </div>
          </div>
          <legend>Pages du site</legend>
          <div class="form-group">
            @foreach ($pages as $page=>$pageData)
              <div class="col-lg-5 col-md-6 col-sm-9 control-label">
                {{ Form::label($page, $pageData['description']) }}
                <span class="horiz-divider"></span>
                {{ Form::checkbox($page, 1, $pageData['active']) }}
              </div>
              <div class="col-lg-1"></div>
            @endforeach
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@stop