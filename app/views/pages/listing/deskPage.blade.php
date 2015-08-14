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
  Listing Desk
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-desk-listing.js') }}"></script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('manage_listing', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la gestion du listing
    </a>
  </p>
@stop

@section('forward_links')
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'desk-listing'))
  
  <div class="row">
    <div class="col-md-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <h1>Listing Desk</h1>
      {{ Form::open(array('route' => 'desk_listing', 'files' => true, 'class' => 'form-horizontal well')) }}
        <legend>Comparer le listing du site au listing Desk</legend>
        <p>Rends-toi sur Desk et exporte le listing au format TXT.</p>
        <div class='alert alert-info'>
          <p>Pour récupérer le listing dans le bon format sur Desk&nbsp;:</p>
          <ol>
            <li>Dans le menu de gauche, sélectionne <strong>Gestion des personnes &rarr; Export des membres (Unité)</strong></li>
            <li>Au-dessus du listing qui s'est affiché, clique sur l'icône <strong>Export</strong></li>
            <li>Dans la liste des format, sélectionne le format <strong>TXT - Fichier Texte</strong></li>
          </ol>
        </div>
        <div class="form-group">
          <div class="col-sm-5 control-label">
            <label for='listingFile'>Ficher TXT&nbsp;:</label>
          </div>
          <div class="col-sm-4 upload-desk-listing" @if (isset($fileDate)) style="display: none;" @endif>
            {{ Form::file("listingFile", array('class' => 'form-control')) }}
          </div>
          <div class='col-sm-6 existing-desk-listing'>
            @if (isset($fileDate))
              Fichier uploadé le {{ $fileDate }} <span class="horiz-divider"></span>
              <button class="btn btn-default change-desk-listing-button">Uploader un autre fichier</button>
            @endif
          </div>
        </div>
        <div class="form-group">
          <div class='col-sm-5 control-label'>
            <label for='caseInsensisive'>Ignorer les différences de majuscules/minuscules&nbsp;:</label>
          </div>
          <div class="col-sm-2">
            {{ Form::checkbox('caseInsensitive', 1, $caseInsensitive) }}
          </div>
        </div>
        <div class="form-group">
          <div class='col-sm-5 control-label'>
            <label for='caseInsensisive'>Ignorer les erreurs d'accents&nbsp;:</label>
          </div>
          <div class="col-sm-2">
            {{ Form::checkbox('ignoreAccentErrors', 1, $ignoreAccentErrors) }}
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-6 col-sm-offset-5">
            {{ Form::submit("Afficher la liste de différences", array('class' => 'btn btn-primary')) }}
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
  
  @if (isset($fileDate))
    <div class='row'>
      <div class='col-md-12'>
        <div class='listing-comparison'>
          <div class='member-data legend'>
            <label>Légende&nbsp;:</label>
            <span class='added-member'>Nouveau membre</span>
            <span class='deleted-member'>Membre à supprimer</span>
            <span class='modified-member-data'>Données à modifier</span>
          </div>
        </div>
        <h2>Liste des modifications à apporter dans Desk</h2>
        
        <table class='table listing-comparison'>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Sexe</th>
              <th>DDN</th>
              <th>Téléphone</th>
              <th>E-mail</th>
              <th>Adresse</th>
              <th>Section</th>
              <th>(Handicap)</th>
              <th>Totem</th>
              <th>Quali</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($modifications as $member)
              <tr class='member-data
                @if (array_key_exists('after', $member['last_name']) && !$member['last_name']['after'])
                  deleted-member
                @elseif (array_key_exists('before', $member['last_name']) && !$member['last_name']['before'])
                  added-member
                @endif '>
                @foreach (array('last_name', 'first_name', 'gender', 'birth_date', 'phone', 'email', 'address', 'section', 'handicap', 'totem', 'quali') as $field)
                  @if (array_key_exists('after', $member[$field]))
                    <td class='modified-member-data'>
                      <del>{{ $member[$field]['before'] }}</del>
                      {{ array_key_exists('keep', $member[$field]) ? $member[$field]['keep'] : "" }}
                      <ins>{{ $member[$field]['after'] }}</ins>
                    </td>
                  @else
                    <td @if (array_key_exists('title', $member[$field])) title="{{ $member[$field]['title'] }}" @endif>
                      {{ $member[$field]['value'] }}
                    </td>
                  @endif
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
  
@stop
