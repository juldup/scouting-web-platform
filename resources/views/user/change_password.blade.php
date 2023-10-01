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
  Changer mon mot de passe
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  <div class="row">
    <div class='col-lg-12'>
      <h1>Changer votre mot de passe</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @if ($status == 'normal')
    {{ Form::open() }}
      <div class="form-group">
        <div class='col-lg-12'>
          <p>Entrez votre nouveau mot de passe.</p>
          {{ Form::label('email', 'Mot de passe :') }}
          {{ Form::password('password', array('class' => 'form-control large')) }}
          {{ Form::submit('Changer', array('class' => 'btn btn-primary')) }}
        </div>
      </div>
      <div class="form-group">
        <div class='col-lg-12'>
          @if ($errors->first('password'))
            <p class='alert alert-danger'>{{ $errors->first('password') }}</p>
          @endif
        </div>
      </div>
          {{ Form::close() }}
  @elseif ($status == 'unknown')
    <div class="row">
      <div class='col-lg-12'>
        <p class='alert alert-danger' >Ce lien n'est plus valide.</p>
      </div>
    </div>
  @elseif ($status == 'done')
    <div class="row">
      <div class='col-lg-12'>
        <p class='alert alert-success' >Votre mot de passe a été modifié avec succès.</p>
      </div>
    </div>
  @endif
@stop