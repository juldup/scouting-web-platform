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
use App\Models\MemberHistory;

?>

@section('title')
  Gestion des fiches santé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('health_card') }}">
      Retour à la page de fiches santé
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-health-cards'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Gestion des fiches santé {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @if (count($members))
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <table class="table table-striped table-hover ">
          <thead>
            
            <tr>
              <th>Nom</th>
              <th>Dernière signature</th>
              <th>Destruction automatique dans...</th>
              @if ($download_all)
                <td class="text-right">
                  <p>
                    <a class="btn-sm btn-primary" href="{{ URL::route("manage_health_cards_download_all", array('section_slug' => $user->currentSection->slug)) }}">
                      Télécharger tout
                    </a>

                    <a class="btn-sm btn-primary" href="{{ URL::route("manage_health_cards_download_summary", array('section_slug' => $user->currentSection->slug)) }}">
                      Télécharger le résumé
                    </a>
                  </p>
                </td>
              @else
                <th></th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach($members as $member)
            <tr>
              <td>
                {{{ $member['member']->first_name }}} {{{ $member['member']->last_name }}}
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
              <td class="text-right">
                @if (array_key_exists('health_card', $member))
                  <a class="btn-sm btn-primary" href="{{ URL::route("health_card_download", $member['member']->id) }}">
                    Télécharger
                  </a>
                @else
                  <p class='label label-danger'>Fiche inexistante</p>
                @endif
              </td>
            @endforeach
            @if ($download_all)
              <tr>
                <td colspan="3"></td>
                <td class="text-right">
                  <p>
                    <a class="btn-sm btn-primary" href="{{ URL::route("manage_health_cards_download_all", array('section_slug' => $user->currentSection->slug)) }}">
                      Télécharger tout
                    </a>
                    &nbsp;
                    <a class="btn-sm btn-primary" href="{{ URL::route("manage_health_cards_download_summary", array('section_slug' => $user->currentSection->slug)) }}">
                      Télécharger le résumé
                    </a>
                  </p>
                </td>
              </tr>
            @endif
          </tobdy>
        </table>
      </div>
    </div>
  @else
    {{-- No members --}}
    <div class="row">
      <div class="col-md-12">
        <p>Il n'y a pas de membre dans cette section</p>
      </div>
    </div>
  @endif
  
  
@stop