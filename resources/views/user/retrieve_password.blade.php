@extends('base')
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

use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;

?>

@section('title')
  Récupérer mon mot de passe
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  <div class="row">
    <div class='col-lg-12'>
      <h1>Récupérer votre mot de passe</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      <p>Entrez votre adresse e-mail. Un lien pour changer votre mot de passe vous sera envoyé.</p>
      {!! Form::open(['route' => 'retrieve_password']) !!}
        {!! Form::label('email', 'Adresse e-mail :') !!}
        {!! Form::text('email', '', array('class' => 'form-control very-large')) !!}
        {!! Form::submit('Envoyer', array('class' => 'btn btn-primary')) !!}
      {!! Form::close() !!}
    </div>
  </div>
@stop