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
  Agenda {{{ $user->currentSection->de_la_section }}}
@stop

@section('back_links')
  <p>
    <a href='{{ $page_url }}'>
      Retour au calendrier
    </a>
  </p>
@stop

@section('forward_links')
  @if ($can_edit)
    <p>
      <a href='{{ $edit_url }}'>
        Modifier le calendrier
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Agenda {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>

  
  <div class="row">
    <div class='col-md-12'>
      @foreach ($calendar_items as $item)
        <div class="calendar-list-event-row  @if ($item->start_date < date('Y-m-d')) calendar-list-past-event @endif">
          <div class="calendar-list-event-title">
            @if ($user->currentSection->id == 1)
              <span class="glyphicon glyphicon-certificate" @if ($item->start_date >= date('Y-m-d')) style="color: {{ $item->getSection()->color }};" @endif></span>
            @endif
            @if ($item->start_date == $item->end_date)
              {{{ $days[(date('w', strtotime($item->start_date)) + 6) % 7] }}} {{{ Helper::dateToHuman($item->start_date) }}}&nbsp;:
            @else
              Du {{{ Helper::dateToHuman($item->start_date) }}} au {{{ Helper::dateToHuman($item->end_date) }}}&nbsp;:
            @endif

            {{{ $item->event }}}
            @if ($user->currentSection->id == 1 && $item->getSection()->id != 1)
              <span @if ($item->start_date >= date('Y-m-d')) style="color: {{ $item->getSection()->color }};" @endif>
                ({{{ $item->getSection()->name }}})
              </span>
            @endif
            </div>
          <div class="calendar-list-event-description">
            {{ Helper::rawToHTML($item->description) }}
          </div>
        </div>
      @endforeach
    </div>
  </div>
  
  @include('subviews.downloadCalendar')

@stop