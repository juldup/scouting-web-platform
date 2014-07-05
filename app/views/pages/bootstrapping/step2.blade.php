@extends('pages.bootstrapping.bootstrapping-base')
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
  Initialisation du site - étape 1
@stop

@section('additional_javascript')
  <script>
    // Disable other fields for sqlite database driver
    function updateFields() {
      $('input[type="text"]').prop('disabled', $("select[name='driver']").val() === 'sqlite');
    }
    $("select[name='driver']").change(function() {
      updateFields();
    });
    $().ready(updateFields);
  </script>
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 2 : Configuration de la base de données</h1>
      @if ($database_exists)
        <p class="alert alert-success">
          La base de données est correctement configurée.
        </p>
        <p>
          <a class='btn btn-default' href="{{ URL::route('bootstrapping-step', array('step' => 2, 'reset' => true)) }}">
            Reconfigurer la base de données
          </a>
        </p>
        <p>
          <a class="btn btn-primary" href="{{ URL::route('bootstrapping-step', array('step' => 3)) }}">
            Passer à l'étape 3
          </a>
        </p>
      @else
        @if ($database_config_error)
          <p class="alert alert-danger">
            Il y a une erreur dans la configuration de la base de données.
          </p>
        @endif
        <p>
          Pour que le site soit fonctionnel, il doit être connecté à une base de données. Votre hébergeur doit vous permettre de créer une base de
          données et vous fournir les données ci-dessous.
        </p>
        <p>
          Si vous choisissez <em>sqlite</em> comme type de base de données, une base de données
          sera créée automatiquement pour vous dans le système de fichiers.
        </p>
        <div class="well form-horizontal">
          {{ Form::open() }}
          <legend>Entrez les paramètres de connexion à votre base de données</legend>
            <div class="form-group">
              {{ Form::label('driver', "Type de base de données", array('class' => 'control-label text-right col-sm-4')) }}
              <div class='col-sm-4'>
                {{ Form::select('driver', array('sqlite' => 'sqlite', 'mysql' => 'mysql', 'pgsql' => 'pgsql', 'sqlsrv' => 'sqlsrv'), $driver, array('class' => 'form-control')) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('host', "Hôte", array('class' => 'control-label text-right col-sm-4')) }}
              <div class='col-sm-4'>
                {{ Form::text('host', $host, array('class' => 'form-control')) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('database', "Base de données", array('class' => 'control-label text-right col-sm-4')) }}
              <div class='col-sm-4'>
                {{ Form::text('database', $database, array('class' => 'form-control')) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('username', "Utilisateur", array('class' => 'control-label text-right col-sm-4')) }}
              <div class='col-sm-4'>
                {{ Form::text('username', $username, array('class' => 'form-control')) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('password', "Mot de passe", array('class' => 'control-label text-right col-sm-4')) }}
              <div class='col-sm-4'>
                {{ Form::text('password', $password, array('class' => 'form-control')) }}
              </div>
            </div>
          <div class="form-group">
            <div class="col-sm-4 col-sm-offset-4">
              <input type="submit" class="btn btn-primary">
            </div>
          </div>
          {{ Form::close() }}
        </div>
      @endif
    </div>
  </div>  
@stop
