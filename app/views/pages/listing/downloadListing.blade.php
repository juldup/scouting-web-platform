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
  Gestion du listing
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('listing', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour au listing
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'download-listing'))
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Télécharger le listing</h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="well">
        {{ Form::open(array('url' => URL::route('download_listing_with_options'), 'class' => 'form-horizontal')) }}
        
        <legend>Sections</legend>
        <div class='form-group'>
          @foreach (Section::all() as $section)
            {{ Form::label('section_' . $section->id, $section->name, array('class' => 'col-sm-2 control-label')) }}
            <div class="col-sm-1">
              {{ Form::checkbox('section_' . $section->id, 1, $user->currentSection->id == 1 || $user->currentSection->id == $section->id) }}
            </div>
          @endforeach
        </div>
        <div class='form-group'>
          {{ Form::label('group_by_section', "Grouper par section", array('class' => 'col-sm-2 control-label')) }}
          <div class="col-sm-1">
            {{ Form::checkbox('group_by_section', 1, true) }}
          </div>
        </div>
        
        <legend>Membres à inclure</legend>
        <div class='form-group'>
          {{ Form::label('include_scouts', "Scouts", array('class' => 'col-sm-2 control-label')) }}
          <div class="col-sm-1">
            {{ Form::checkbox('include_scouts', 1, true) }}
          </div>
          {{ Form::label('include_leaders', "Animateurs", array('class' => 'col-sm-2 control-label')) }}
          <div class="col-sm-1">
            {{ Form::checkbox('include_leaders', 1, true) }}
          </div>
        </div>
        
        <legend>Format</legend>
        <div class='form-group'>
          {{ Form::label('format', "Format du fichier", array('class' => 'col-sm-3 col-md-2 control-label')) }}
          <div class="col-sm-2">
            {{ Form::select('format', array('pdf' => "PDF", 'excel' => "Excel", 'csv' => "CSV"), null, array('class' => 'form-control')) }}
          </div>
          {{ Form::label('full', "Inclure toutes les données", array('class' => 'col-sm-4 col-md-4 control-label')) }}
          <div class="col-sm-2">
            {{ Form::checkbox('full', 1, true) }}
          </div>
        </div>
        
        <legend>Télécharger</legend>
        <div class='form-group'>
          <div class='col-sm-offset-2 col-sm-4'>
            {{ Form::submit('Télécharger', array('class' => 'btn btn-primary')) }}
          </div>
        </div>
        
        {{ Form::close() }}
      </div>
    </div>
  </div>
  
@stop