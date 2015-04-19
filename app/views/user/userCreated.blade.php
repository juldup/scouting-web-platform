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
  Compte d'utilisateur créé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class="row">
    <div class='col-lg-12'>
      <h1>Vous êtes inscrit sur le site</h1>
      <div class="alert alert-success">
        <p>
          Votre compte d'utilisateur <strong>{{{ $user->username }}}</strong> a été créé.
        </p>
        <p>
          Un e-mail vous a été envoyé à l'adresse "{{{ $user->email }}}".
          <strong>Cliquez sur le lien d'activation</strong> dans l'e-mail pour activer votre compte.
        </p>
      </div>
      <p class="alert alert-danger">
        <strong>ATTENTION&nbsp;! Ceci ne constitue pas une inscription dans l'unité.</strong>
        Pour inscrire un enfant dans l'unité, suivez les instructions sur <a href='{{ URL::route('registration') }}'>cette page</a>.
      </p>
      <p>
        @if ($referrer)
          <a href='{{ $referrer }}' class='btn btn-default'>Retour à la page précédente</a>
        @else
          <a href='{{ URL::route('home') }}' class='btn btn-default'>Retour à la page d'accueil</a>
        @endif
      </p>
    </div>
  </div>
  
@stop
