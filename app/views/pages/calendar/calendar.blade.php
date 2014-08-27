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
  @if ($editing)
    Gestion du calendrier
  @else
    Calendrier {{{ $user->currentSection->de_la_section }}}
  @endif
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-calendar.js') }}"></script>
  <script>
    var currentMonth = {{{ $month }}};
    var currentYear = {{{ $year }}};
    var currentSection = {{ $user->currentSection->id }};
    var events = new Array();
    @foreach ($calendar_items as $item)
      events[{{ $item->id }}] = {
        'start_day': {{ $item->getStartDay() }},
        'start_month': {{ $item->getStartMonth() }},
        'start_year': {{ $item->getStartYear() }},
        'duration': {{ $item->getDuration() }},
        'event_name': "{{ Helper::sanitizeForJavascript($item->event) }}",
        'description': "{{ Helper::sanitizeForJavascript($item->description) }}",
        'type': "{{ $item->type }}",
        'section': {{ $item->section_id }},
        'delete_url': "{{ URL::route('manage_calendar_delete', array('event_id' => $item->id, 'year' => $year, 'month' => $month, 'section_slug' => $user->currentSection->slug)) }}"
      };
    @endforeach
  </script>
@stop

@section('back_links')
  @if ($editing)
    <p>
      <a href='{{ $page_url }}'>
        Retour au calendrier
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if ($can_edit && !$editing)
    <p>
      <a href='{{ $edit_url }}'>
        Modifier le calendrier
      </a>
    </p>
  @endif
@stop

@section('content')
  
  @if ($editing)
    @include('subviews.contextualHelp', array('help' => 'edit-calendar'))
  @endif
  
  <div class="row">
    <div class="col-md-12">
      @if ($editing)
        <h1>Gestion du calendrier {{{ $user->currentSection->de_la_section }}}</h1>
      @else
        <h1>Calendrier {{{ $user->currentSection->de_la_section }}}</h1>
      @endif
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-md-10 col-md-offset-1'>
      @if ($editing)
        <div id="calendar_event_form" class="form-horizontal well"
             @if (!Session::has('_old_input')) style="display: none;" @endif
             >
          {{ Form::open(array('url' => URL::route('manage_calendar_submit', array('year' => $year, 'month' => $month, 'section_slug' => $user->currentSection->slug)))) }}
            {{ Form::hidden('event_id', 0) }}
            <legend>Modifier un événement</legend>
            <div class='form-group'>
              {{ Form::label('start_date_day', "Date de début", array('class' => 'col-md-4 control-label')) }}
              <div class='col-md-6'>
                {{ Form::text('start_date_day', '', array('class' => 'form-control small', 'placeholder' => 'Jour')) }} /
                {{ Form::text('start_date_month', '', array('class' => 'form-control small', 'placeholder' => 'Mois')) }} /
                {{ Form::text('start_date_year', '', array('class' => 'form-control small', 'placeholder' => 'Année')) }}
              </div>
            </div>
            <div class='form-group'>
              {{ Form::label('duration_in_days', "Durée (jours)", array('class' => 'col-md-4 control-label')) }}
              <div class='col-md-1'>
                {{ Form::text('duration_in_days', '', array('class' => 'form-control small')) }}
              </div>
              <div class='col-md-6'>
                <p class="form-side-note">Compte le premier et le dernier jour de l'activité.</p>
              </div>
            </div>
            <div class='form-group'>
              {{ Form::label('event_name', "Activité", array('class' => 'col-md-4 control-label')) }}
              <div class='col-md-6'>
                {{ Form::text('event_name', '', array('class' => 'form-control', 'placeholder' => "Nom de l'activité")) }}
              </div>
            </div>
            <div class='form-group'>
              {{ Form::label('description', "Description", array('class' => 'col-md-4 control-label')) }}
              <div class='col-md-6'>
                {{ Form::textarea('description', '', array('class' => 'form-control', 'rows' => 3, 'placeholder' => "Description, horaire, infos pratiques")) }}
              </div>
            </div>
            <div class='form-group'>
              {{ Form::label('event_type', "Type d'événement", array('class' => 'col-md-4 control-label')) }}
              <div class='col-md-6'>
                {{ Form::select('event_type', $event_types, '', array('class' => 'form-control')) }}
              </div>
            </div>
            <div class='form-group'>
              {{ Form::label('section', "Section", array('class' => 'col-md-4 control-label')) }}
              <div class='col-md-6'>
                {{ Form::select('section', $sections, '', array('class' => 'form-control')) }}
              </div>
            </div>
            <div class='form-group'>
              <div class='col-md-5 col-md-offset-3'>
                {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
                <a class="btn btn-danger" id='delete_link' style="display: none;" href="">Supprimer</a>
                <a class="btn btn-default" href="javascript:dismissEvent()">Fermer</a>
              </div>
            </div>
          {{ Form::close() }}
        </div>
      @endif
    </div>
  </div>
      
  <div class="row">
    <div class='col-md-12'>
      @if ($year != $today_year || $month != $today_month)
        <p class="text-right">
          <a class="btn-sm btn-primary" href="{{ URL::route($route_month, array('month' => $today_month, 'year' => $today_year, "section_slug" => $user->currentSection->slug)) }}">
            Retour à aujourd'hui
          </a>
        </p>
      @endif
    </div>
  </div>
  
  {{-- Large screens --}}
  <div class="row @if (!$editing) calendar-large-screens @endif">
    <div class='col-md-12'>
      <div id="calendar" class="calendar">
        <div>
          {{-- Month header --}}
          <div class='month'>
            {{-- Links to the previous 6 months --}}
            @for ($i = 6; $i >= 1; $i--)
              <span class='otherMonth'>
                <a href="{{ URL::route($route_month, array('month' => (($month - $i + 11) % 12 + 1), 'year' => ($month - $i <= 0 ? $year - 1 : $year))) }}">
                  {{{ $months_short[($month - $i + 11) % 12] }}} &larr; <span class='horiz-divider'></span>
                </a>
              </span>
            @endfor
            <span class='month-name'>{{{ $months[$month-1] }}} {{{ $year }}}</span>
            {{-- Links to the next 6 months --}}
            @for ($i = 1; $i <= 6; $i++)
              <span class='otherMonth'>
                <a href="{{ URL::route($route_month, array('month' => (($month + $i + 11) % 12 + 1), 'year' => ($month + $i >= 13 ? $year + 1 : $year))) }}">
                  <span class='horiz-divider'></span> &rarr; {{{ $months_short[($month + $i - 1) % 12] }}}
                </a>
              </span>
            @endfor
          </div>
        </div>
        <div>
          {{-- Names of the days --}}
          @for ($x = 0; $x <= 6; $x++)
          <div class="day-name">{{{ $days[$x] }}}</div>
          @endfor
        </div>
        <div class="week">
          {{-- Blank days at the beginning of the month --}}
          @for ($x = 0; $x < $blank_days_before; $x++)
            <div></div>
          @endfor
          {{-- Days of the month --}}
          @for ($day = 1; $day <= $days_in_month; $day++)
            {{-- Next row at the end of the week --}}
            @if (($day + $blank_days_before - 1) % 7 == 0)
              </div>
              <div class="week">
            @endif
            {{-- Day --}}
            <div class='day {{ $editing ? "clickable" : "" }}'>
              {{-- Number of the day --}}
              @if ($editing)
                <a class="day-number" href='javascript:addEvent({{{ $day }}})'>{{{ $day }}}</a>
              @else
                <p class="day-number">{{{ $day }}}</p>
              @endif
              {{-- Events of the day --}}
              @foreach ($events[$day] as $event)
              @if ($editing && ($event->section_id != 1 || $can_edit_unit)) <a href="javascript:editEvent({{ $event->id }})"> @endif
              <div class="calendar-event-wrapper {{ !$editing ? "clickable" : "" }}">
                <p class="calendar-event" title="{{{ $event->description }}}" style="color: {{$event->getSection()->color}};">
                  <img src="{{ $event->getIcon() }}" class='calendar-event-icon' />
                  {{{ $event->event }}}
                </p>
                @if (!$editing)
                  <div class="calendar-event-details" style="display: none;">
                    @if ($event->description)
                      {{{ $event->description }}}
                    @else
                      Pas de description
                    @endif
                  </div>
                @endif
              </div>
              @if ($editing) </a> @endif
              @endforeach
            </div>
          @endfor
          @for ($x = 0; $x < $blank_days_after; $x++)
            <div></div>
          @endfor
        </div>
      </div>
    </div>
  </div>
  
  {{-- Small screens --}}
  @if (!$editing)
    <div class="row calendar-small-screens">
      <div class="col-md-12">
        {{-- Month title --}}
        <div class="text-center month-title">
          {{-- Link to previous 2 months --}}
          @for ($i = 2; $i >= 1; $i--)
            <span class='otherMonth'>
              <a href="{{ URL::route($route_month, array('month' => (($month - $i + 11) % 12 + 1), 'year' => ($month - $i <= 0 ? $year - 1 : $year))) }}">
                {{{ $months_short[($month - $i + 11) % 12] }}} &larr; <span class='horiz-divider'></span>
              </a>
            </span>
          @endfor
          <span class='month-name'>{{{ $months[$month-1] }}} {{{ $year }}}</span>
          {{-- Links to the next 2 months --}}
          @for ($i = 1; $i <= 2; $i++)
            <span class='otherMonth'>
              <a href="{{ URL::route($route_month, array('month' => (($month + $i + 11) % 12 + 1), 'year' => ($month + $i >= 13 ? $year + 1 : $year))) }}">
                <span class='horiz-divider'></span> &rarr; {{{ $months_short[($month + $i - 1) % 12] }}}
              </a>
            </span>
          @endfor
        </div>
        {{-- Events of the month --}}
        @foreach ($calendar_items as $item)
          <div class="calendar-event-row">
            <div class="calendar-event-name" style="color: {{ $item->getSection()->color }};">
              @if ($item->start_date == $item->end_date)
                {{{ $days[(date('w', strtotime($item->start_date)) + 6) % 7] }}} {{{ Helper::dateToHuman($item->start_date) }}}&nbsp;:
              @else
                Du {{{ Helper::dateToHuman($item->start_date) }}} au {{{ Helper::dateToHuman($item->end_date) }}}&nbsp;:
              @endif

              {{{ $item->event }}}
              @if ($user->currentSection->id == 1)
                ({{{ $item->getSection()->name }}})
              @endif
              </div>
            <div class="calendar-event-description">
              {{{ $item->description }}}
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
  
  @if (!$editing && Parameter::get(Parameter::$CALENDAR_DOWNLOADABLE) == "true")
    <div class="row">
      <div class="col-md-12">
        <a id="download-calendar-button" class="btn-sm btn-default">Télécharger les éphémérides</a>
        <div class="form-horizontal" id="download-calendar-form" style="display: none;">
          <h3>Télécharger les éphémérides</h3>
          {{ Form::open(array('url' => URL::route('download_calendar'))) }}
            <div class="form-group">
              <div class="col-md-12">
                {{ Form::label(null, 'Inclure les éphémérides de :') }}
              </div>
            </div>
            <div class="form-group">
              @foreach ($sectionList as $section)
                <div class="col-xs-7 col-sm-4 col-md-3 text-right">
                  {{ Form::label('section_' . $section->id, $section->name) }}
                  {{ Form::checkbox('section_' . $section->id, 1, $user->currentSection->id == 1 || $user->currentSection->id == $section->id) }}
                </div>
              @endforeach
            </div>
            <div class="form-group">
              <div class="col-md-12">
                {{ Form::label(null, 'Inclure les événenements du :') }}
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-7 col-sm-4 col-md-3 text-right">
                {{ Form::label('semester_1', "Premier semestre") }}
                {{ Form::checkbox('semester_1', 1, true) }}
              </div>
              <div class="col-xs-7 col-sm-4 col-md-3 text-right">
                {{ Form::label('semester_2', "Second semestre") }}
                {{ Form::checkbox('semester_2', 1, $include_second_semester_by_default) }}
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-7 col-sm-8 col-md-9 text-right">
                {{ Form::submit('Télécharger', array('class' => 'btn btn-primary')) }}
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  @endif

@stop