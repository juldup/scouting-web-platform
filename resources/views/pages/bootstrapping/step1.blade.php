@extends('pages.bootstrapping.bootstrapping-base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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


@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 1 : Donner l'accès en écriture au système de fichiers</h1>
      @if ($success)
        <p class="alert alert-success">
          L'accès en écriture a été vérifié et fonctionne correctement.
        </p>
        <p>
          <a class="btn btn-primary" href="{{ URL::route('bootstrapping_step', array('step' => 2)) }}">
            Passer à l'étape 2
          </a>
        </p>
      @else
        <p>
          Le site doit avoir accès au contenu du répertoire suivant pour fonctionner correctement&nbsp;:
        </p>
        <div class="well well-sm">
          {{ $directory_path }}
        </div>
        <p>Veuillez donner l'accès à ce répertoire&nbsp;: <strong>chmod 777 -R app/storage</strong></p>
        <p>
          <a class="btn btn-primary" href="{{ URL::route('bootstrapping_step', array('step' => 1)) }}">
            Réessayer
          </a>
        </p>
      @endif
    </div>
  </div>  
@stop
