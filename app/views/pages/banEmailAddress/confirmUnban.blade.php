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
  Merci!
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Adresse réinscrite</h1>
      <div class="alert alert-success">
        <p>
          Vous recevrez à nouveau les e-mails envoyés depuis ce site à l'adresse <strong>{{{ $email }}}</strong>.
        </p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-6">
      <a class="btn btn-primary" href="{{ URL::route('home') }}">Retour au site</a>
    </div>
  </div>
@stop
