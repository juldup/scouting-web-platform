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
  Gestion des absences
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-absences.js') }}"></script>
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('absences') }}">
      Retour à la page d'absences
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'manage-absences'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Absences {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
      <p>Clique sur un événement pour voir la liste d'absents.</p>
    </div>
  </div>
  
  @if (count($events))
  <div class="row">
    <div class='col-md-12'>
      @foreach($events as $eventId=>$absences)
        <div class="absence-table-wrapper">
          @if ($eventId == 0)
            <div class="absence-event-name">
              <span class="absence-unfold"></span>
              <span class="absence-fold" style="display: none;"></span>
              Absences à d'autres événements
              ({{ count($absences) == 0 ? "aucun absent" : (count($absences) == 1 ? "<strong>1 absent</strong>" : ("<strong>" . count($absences) . " absents</strong>")) }})
            </div>
            <table class="table table-striped table-hover absence-list" style="display: none;">
              <tbody>
                @foreach($absences as $absence)
                  <tr>
                    <td class="absence-list-left-column-large">
                      <strong>{{{ $absence->getMember()->getFullName() }}}</strong>
                      à 
                      "{{{ $absence->other_event }}}"
                    </td>
                    <td>
                      {{{ $absence->explanation }}}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            @if (count($absences) == 0)
              <div class="absence-event-name-empty">
                <span class="absence-nofold"></span>
                {{ CalendarItem::find($eventId)->stringRepresentation() }}
                (aucun absent)
              </div>
            @else
              <div class="absence-event-name">
                <span class="absence-unfold"></span>
                <span class="absence-fold" style="display: none;"></span>
                {{ CalendarItem::find($eventId)->stringRepresentation() }}
                ({{ count($absences) == 1 ? "<strong>1 absent</strong>" : ("<strong>" . count($absences) . " absents</strong>") }})
              </div>
              <table class="table table-striped table-hover absence-list" style="display: none;">
                <tbody>
                  @foreach($absences as $absence)
                    <tr>
                      <td class="absence-list-left-column">
                        <strong>{{{ $absence->getMember()->getFullName() }}}</strong>
                      </td>
                      <td>
                        {{{ $absence->explanation }}}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @endif
          @endif
        </div>
      @endforeach
    </div>
  </div>
  @else
    {{-- No members --}}
    <div class="row">
      <div class="col-md-12">
        <p>Il n'y a aucune absence signalée pour cette section</p>
      </div>
    </div>
  @endif
  
  
@stop