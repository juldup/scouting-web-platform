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
  Créer un lien d'inscription temporaire
@stop

@section('additional_javascript')
  <script>
    function copyToClipboard() {
      // Get the text field
      var copyText = document.getElementById("temporary-link");
      // Select the text field
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile devices
      // Copy the text inside the text field
      document.execCommand("copy");
      // Show 'copied' message
      $("#copied-message").show();
    }
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('manage_registration', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la gestion des inscriptions
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'create-temporary-registration-link'))
  
  <h1>Créer un lien d'inscription temporaire</h1>
  
  @include('subviews.flashMessages')
  
  <br />
  
  @if (Session::has('code'))
    Le lien suivant sera valide pendant {{ Session::get('days') }} {{ Session::get('days')==1 ? "jour" : "jours" }} :
    <div class="row form-horizontal">
      <div class="col-md-8">
        <input type='text' value="{{ URL::route('temporary_registration_link', array('code' => Session::get('code'))) }}"
               id='temporary-link' class="form-control"/>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary form-control" onclick="copyToClipboard()">Copier</button>
      </div>
      <div class="col-md-2">
        <div style="display: none;" id='copied-message' class='form-side-note'>Copié</div>
      </div>
    </div>
  @else
    {!! Form::open(array('url' => URL::route('create_temporary_registration_link_post'), 'class' => 'form-horizontal')) !!}
    <div class="row">
      <div class="col-md-12">
        Créer un lien valide pendant&nbsp;
        {!! Form::text('days', 7, array('class' => 'form-control small')) !!}
        &nbsp;jours.
        <span class='horiz-divider'></span>
        {!! Form::submit('Créer maintenant', array('class' => 'btn btn-primary')) !!}
      </div>
    </div>
    {!! Form::close() !!}
  @endif
  
@stop