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
  Photos du jour @if ($date) ({{ Helper::dateToHuman($date) }}) @endif
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Photos du @if ($date) {{ Helper::dateToHuman($date) }} @else jour @endif</h1>
    </div>
  </div>
  <div class="row">
    @if (count($photos))
      <div class="col-md-12">
        <p>Chaque jour, deux photos sont tirées au hasard parmi celles de ces dernières années.</p>
      </div>
      @foreach ($photos as $photo)
        <div class="col-md-6 daily-photo-wrapper">
          <a class="" href="{{ $photo['albumUrl'] }}">
            <div class="image-wrapper-outer">
              <div class="image-wrapper-inner">
                <img src="{{ $photo['photoUrl'] }}" />
              </div>
            </div>
          </a>
        </div>
      @endforeach
    @else
      <div class="col-md-12">
        <p>Il n'y a pas de photos pour cette date</p>
      </div>
    @endif
  </div>
  <div class="vertical-divider"></div>
  <div class="row">
    <div class="col-md-12">
      <a class="btn btn-default" href="{{ $yesterdayUrl }}">Voir les photos @if ($date) de la veille @else d'hier @endif</a>
      @if ($date && $date != date('Y-m-d'))
        <a class="btn btn-default"href='{{ URL::route('daily_photos') }}'>
          Voir les photos d'aujourd'hui
        </a>
      @endif
    </div>
  </div>
@stop
