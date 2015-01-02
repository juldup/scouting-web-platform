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
  Désinscrire votre adresse e-mail
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Vous êtes membre de l'unité</h1>
      <div class="alert alert-danger">
        <p>
          L'adresse e-mail <strong>{{{ $email }}}</strong> fait partie de notre listing et ne peut être supprimée de la liste des destinataires.
        </p>
        <p>
          Si vous ne souhaitez plus recevoir d'e-mails à cette adresse, veuillez <a href="{{ URL::route('contacts') }}">contacter l'animateur d'unité</a>.
        </p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-6">
      <a class="btn btn-default" href="{{ URL::route('home') }}">Retour au site</a>
    </div>
  </div>
@stop
