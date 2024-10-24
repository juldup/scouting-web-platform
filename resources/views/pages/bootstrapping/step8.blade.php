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
  Initialisation du site - étape 8
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 8 : Rédiger les pages du site et inscrire les membres</h1>
      <p>
        Le site est à présent entièrement configuré. Il ne vous reste plus qu'à rédiger les pages, inscrire les membres
        et vous familiariser avec l'utilisation du site au quotidien. Vous trouverez ci-dessous quelques explications qui vous permettront
        de vous familiariser avec les outils. Ces informations ont également été envoyées par e-mail à l'adresse du webmaster.
      </p>
      <p>
        <a class="btn btn-primary" target="_blank" href="{{ URL::route('home') }}">Aller vers le site</a>
      </p>
      @include('pages.bootstrapping.site-information')
    </div>
  </div>  
@stop
