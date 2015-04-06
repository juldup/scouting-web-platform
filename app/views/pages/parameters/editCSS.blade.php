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
  Paramètres du site
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-parameters.js') }}"></script>
@stop

@section('content')
  @include('subviews.contextualHelp', array('help' => 'style'))
  
      @if (Session::get('testing-css'))
        <div class='alert alert-danger'>
          <div class='row'>
            <div class='col-sm-9'>
              Tu es en <strong>mode test</strong>. Il n'y a que toi qui vois le site de cette manière et tu peux visiter les autres pages pour voir l'effet du nouveau style. <br />
              Pour appliquer les changements, clique sur <strong>Appliquer au site</strong> en base de la page.
            </div>
            <div class='col-sm-3 text-right'>
              <a href='{{ URL::route('edit_css_stop_testing') }}' class='btn btn-default'>Quitter le mode test</a>
            </div>
          </div>
        </div>
      @endif
  
  <div class="row">
    <div class="col-md-12">
      <h1>Style du site</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  {{ Form::open(array('route' => 'edit_css_submit', 'method' => 'post', 'files' => true, 'class' => 'form-horizontal')) }}
    <div class="form-group">
      <div class="col-md-12">
        Tu peux modifier le CSS du site ci-dessous. Celui-ci s'ajoutera au CSS de base.
        {{ Form::textarea('newCSS', $additional_CSS, array('class' => "form-control edit-css-textarea")) }}
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-9">
        Ou remplacer ce contenu par le contenu de ce ficher&nbsp;:
        {{ Form::file('cssFile', ['class' => "form-control large btn btn-default"]) }}
      </div>
      <div class="col-sm-3">
        {{ Form::button('Enregistrer', ['class' => "form-control btn " . (Session::get('testing-css') ? "btn-primary" : "btn-default"), 'name' => 'action', 'type' => 'submit', 'value' => 'save']) }}
      </div>
    </div>
    @if (!Session::get('testing-css'))
      <div class='form-group'>
        <div class='col-sm-3 col-sm-offset-9'>
          {{ Form::button('Tester ce CSS sans le publier', ['class' => "form-control btn btn-primary", 'name' => 'action', 'type' => 'submit', 'value' => 'test']) }}
        </div>
      </div>
    @endif
    <div class='form-group'>
      <div class='col-sm-3 col-sm-offset-9'>
        {{ Form::button('Appliquer au site', ['class' => "form-control btn btn-danger", 'name' => 'action', 'type' => 'submit', 'value' => 'apply']) }}
      </div>
    </div>
  {{ Form::close() }}
@stop