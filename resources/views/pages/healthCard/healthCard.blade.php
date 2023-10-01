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
  Fiche santé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('forward_links')
  @if ($can_manage)
  <p>
    <a href="{{ URL::route('manage_health_cards') }}">
      Gérer les fiches santé de la section
    </a>
  </p>
  @endif
@stop

@section('content')

  <div class="row">
    <div class="col-md-12">
      <h1>Fiche santé</h1>
      @include('subviews.flashMessages')
      <p>
        La fiche santé permet aux parents de communiquer aux animateurs de leurs enfants les
        informations confidentielles concernant la santé de celui-ci. La fiche santé sert
        également lors d'éventuelles visites chez le médecin pendant nos activités. <strong>Il est
          obligatoire de la compléter, et de la réviser en début d'année et avant chaque camp.</strong>
      </p>
    </div>
  </div>
  
  @if (count($members))
    <div class="row">
      <div class="col-md-12">
        <h2>Vos fiches santé</h2>
      </div>
    </div>
    <div class="row health-card-list">
      <div class="col-sm-12">
        @foreach($members as $member)
          <p class="health-card-row-title"><strong>{{{ $member['member']->first_name }}} {{{ $member['member']->last_name }}}</strong>
            @if (array_key_exists('health_card', $member))
              <a class="btn-sm btn-default" href="{{ URL::route("health_card_edit", $member['member']->id) }}">
                Mettre à jour
              </a>
            @else
              <a class="btn-sm btn-primary" href="{{ URL::route("health_card_edit", $member['member']->id) }}">
                Créer
              </a>
            @endif
            @if (array_key_exists('health_card', $member))
              <a class="btn-sm btn-primary" href="{{ URL::route("health_card_download", $member['member']->id) }}">
                Télécharger
              </a>
            @endif
          </p>
          @if (array_key_exists('health_card', $member))
            <p>
              Dernière signature&nbsp;: {{ Helper::dateToHuman($member['health_card']->signature_date) }}
            </p>
            <p>
              Expire dans&nbsp;: 
              @if ($member['health_card']->daysBeforeDeletion() > 100)
                {{ $member['health_card']->daysBeforeDeletion() }} jours</span>
              @elseif ($member['health_card']->daysBeforeDeletion() > 1)
                <span class="danger">{{ $member['health_card']->daysBeforeDeletion() }} jours</span>
              @else
                <span class="danger">Quelques heures</span>
              @endif
            </p>
          @endif
        @endforeach
        @if ($download_all)
          <p>&nbsp;</p>
          <p>
            <a class="btn-sm btn-primary" href="{{ URL::route("health_card_download_all") }}">
              Télécharger tout
            </a>
          </p>
        @endif
      </div>
    </div>
    <div class="row health-card-table">
      <div class="col-md-10 col-md-offset-1">
        <table class="table table-striped table-hover ">
          <thead>
            <tr>
              <th></th>
              <th>Nom</th>
              <th>Dernière signature</th>
              <th>Destruction automatique dans...</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($members as $member)
            <tr>
              <td>
                @if (array_key_exists('health_card', $member))
                  <a class="btn-sm btn-default" href="{{ URL::route("health_card_edit", $member['member']->id) }}">
                    Mettre à jour
                  </a>
                @else
                  <a class="btn-sm btn-primary" href="{{ URL::route("health_card_edit", $member['member']->id) }}">
                    Créer
                  </a>
                @endif
              </td>
              <td>
                <strong>{{{ $member['member']->first_name }}} {{{ $member['member']->last_name }}}</strong>
              </td>
              <td>
                @if (array_key_exists('health_card', $member))
                  {{ Helper::dateToHuman($member['health_card']->signature_date) }}
                @else
                  -
                @endif
              </td>
              <td>
                @if (array_key_exists('health_card', $member))
                  @if ($member['health_card']->daysBeforeDeletion() > 100)
                    {{ $member['health_card']->daysBeforeDeletion() }} jours</span>
                  @elseif ($member['health_card']->daysBeforeDeletion() > 1)
                    <span class="danger">{{ $member['health_card']->daysBeforeDeletion() }} jours</span>
                  @else
                    <span class="danger">Quelques heures</span>
                  @endif
                @else
                  -
                @endif
              </td>
              <td>
                @if (array_key_exists('health_card', $member))
                  <a class="btn-sm btn-primary" href="{{ URL::route("health_card_download", $member['member']->id) }}">
                    Télécharger
                  </a>
                @endif
              </td>
            @endforeach
            @if ($download_all)
              <tr>
                <td colspan="4"></td>
                <td>
                  <a class="btn-sm btn-primary" href="{{ URL::route("health_card_download_all") }}">
                    Télécharger tout
                  </a>
                </td>
              </tr>
            @endif
          </tobdy>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h2>Confidentialité</h2>
        <p>
          Les informations que vous compléterez ci-dessous resteront confidentielles. Elles ne seront visibles
          que par vous, par toute personne partageant une des adresses e-mail indiquées lors de l'inscription
          et par les animateurs de vos enfants.
        </p>
        <p>
          Les animateurs à qui ces informations sont confiées sont tenus de respecter la loi du 8 décembre 1992
          relative à la protection de la vie privée ainsi qu'à la loi du 19 juillet 2006 modifiant celle du
          3 juillet 2005 relative aux droits des volontaires (notion de secret professionnel stipulée dans
          l'article 458 du Code pénal). Les informations communiquées ici ne peuvent donc être divulguées
          si ce n'est au médecin ou tout autre personnel soignant consulté. Conformément à la loi sur le traitement
          des données personnelles, vous pouvez les consulter et les modifier à tout moment.
          Ces données seront détruites un an après leur dernière validation si aucun dossier n'est ouvert.
        </p>
        <p>
          Bien que tout ait été mis en œuvre pour garantir la sécurité et la confidentialité des données,
          <strong>les créateurs du site et les animateurs de l'unité déclinent toute responsabilité</strong> en cas de perte ou
          divulgation de vos données. Il vous est toujours possible de compléter manuellement la fiche
          santé du scout et de la donner aux animateurs. Vous devrez alors la recompléter en chaque début
          d'année et avant chaque camp.
        </p>
      </div>
    </div>
  @else
    {{-- No members --}}
    @include('subviews.limitedAccess')
  @endif
  
  
@stop