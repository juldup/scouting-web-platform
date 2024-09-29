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
  Coin des animateurs
@stop

@section('additional_javascript')
  @vite(['resources/js/leader-corner.js'])
@stop

@section('content')
  <h1>Coin des animateurs</h1>
  
  <div class="vertical-divider"></div>
  
  <div class="row">
    @foreach ($operations as $operationCategory=>$ops)
      <div class="col-lg-3 col-md-4">
        <div class="list-group clickable-list-group">
          <div class="list-group-item active">
            {{{ $operationCategory }}}
          </div>
          @foreach ($ops as $operationName=>$operationData)
            @if ($operationData['url'])
              <div class="list-group-item clickable leader-help-item" data-leader-help="{{ $operationData['help'] }}">
                <a href="{{ $operationData['url'] }}"></a>
                {!! $operationName !!}
                <a href="#{{ $operationData['help-anchor'] }}" class="help-badge"></a>
              </div>
            @else
              <div class="list-group-item leader-help-item" data-leader-help="{{ $operationData['help'] }}">
                <span class="leader-corner-disabled">{{ $operationName }}</span>
                <a href="#{{ $operationData['help-anchor'] }}" class="help-badge"></a>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
  
  <div class="well help-content leader-help-general">
    <legend>Informations générales sur la structure du site</legend>
    <p>Ce site a été conçu pour être modulable.  Toutes les informations annuelles (listing, photos, calendrier, documents, etc.) sont changeables.<p>
    <p>Il y a deux manières de visiter le site : en tant que visiteur, parent, scout, ou bien en tant qu'animateur.  Les animateurs ont le droit de modifier toutes les informations se trouvant sur le site.  Sois donc prudent avec ce que tu fais, car certaines opérations ne peuvent être annulées.</p>
    <h3>Les droits d'accès</h3>
    <p>Les accès aux pages et informations du site dépendent du statut du visiteur :</p>
    <table style='margin-left: 50px'>
      <tr><td style='vertical-align: top'><span class='important'>Non inscrit</span>&nbsp;: <td>Accès limité aux pages publiques.</tr>
      <tr><td style='vertical-align: top'><span class='important'>Visiteur</span>&nbsp;: <td>Il peut écrire dans le livre d'or, mais n'a accès à aucune information privée.</tr>
      <tr><td style='vertical-align: top'><span class='important'>Membre</span>&nbsp;: <td>Un membre (scout ou parent) peut consulter les listings limités, télécharger les documents, voir les e-mails, les photos et créer des fiches santé pour sa famille.  Un compte d'utilisateur est automatiquement membre si son adresse e-mail a été validée et fait partie de nos listings.</tr>
      <tr><td style='vertical-align: top'><span class='important'>Animateur</span>&nbsp;: <td>Un animateur peut accéder au coin des animateurs. Certains droits lui sont attribués par {{{ Parameter::adaptAnUDenomination("l'animateur d'unité") }}}.</tr>
      <tr><td style='vertical-align: top'><span class='important'>Webmaster</span>&nbsp;: <td>Il n'a aucune limitation.</tr>
    </table>
    <h3>Les onglets</h3>
    <p>Chaque section possède un onglet (voir en haut de la page).  Changer d'onglet adapte le site à la section, tant pour les visiteurs que pour les animateurs.  En particulier, les données modifiables sont limitées à celles de ta section, à moins que tu n'aies des privilèges spéciaux.</p>
  </div>
  
  @foreach ($help_sections as $help_anchor => $help)
    <div class="leader-corner-help" data-leader-help="{{ $help }}" style="display: none;">
      <a name='{{ $help_anchor }}'>&nbsp;</a>
      @include('subviews.leaderHelp', array('help' => $help))
    </div>
  @endforeach
  
@stop