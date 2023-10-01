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
  Activation de mon compte d'utilisateur
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class="row">
    <div class='col-lg-8'>
      @if ($status == 'verified')
        <p class="alert alert-success">Merci ! Votre compte d'utilisateur est à présent actif.</p>
      @elseif ($status == 'unknown')
        <p class="alert alert-danger">Ce code d'activation n'existe pas. Avez-vous correctement recopié l'adresse ?</p>
      @elseif ($status == 'canceled')
        <p class='alert alert-success'>Ce compte d'utilisateur a été supprimé. Merci pour votre coopération.</p>
      @elseif ($status == 'already verified')
        <p class='alert alert-danger'>Ce compte d'utilisateur déjà été activé et ne peut être supprimé.</p>
      @endif
    </div>
  </div>
  
@stop