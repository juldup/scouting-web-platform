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
  Trésorerie
@stop

@section('head')
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
@stop

@section('back_links')
  <p>
    @if ($this_year < $year)
      <a href='{{ URL::route('accounting_by_year', array('section_slug' => $user->currentSection->slug, 'year' => $this_year)) }}'>
        &nbsp;<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;
        Année {{{ $this_year }}}
      </a>
    @endif
    @if ($this_year != $previous_year)
      <a href='{{ URL::route('accounting_by_year', array('section_slug' => $user->currentSection->slug, 'year' => $previous_year)) }}'>
        &nbsp;<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;
        Année {{{ $previous_year }}}
      </a>
    @endif
  </p>
@stop

@section('forward_links')
  <p>
    @if ($this_year > $year)
      <a href='{{ URL::route('accounting_by_year', array('section_slug' => $user->currentSection->slug, 'year' => $this_year)) }}'>
        Année {{{ $this_year }}}
        &nbsp;<span class="glyphicon glyphicon-arrow-right"></span>&nbsp;
      </a>
    @endif
    @if ($this_year != $next_year)
      <a href='{{ URL::route('accounting_by_year', array('section_slug' => $user->currentSection->slug, 'year' => $next_year)) }}'>
        Année {{{ $next_year }}}
        &nbsp;<span class="glyphicon glyphicon-arrow-right"></span>&nbsp;
      </a>
    @endif
  </p>
@stop

@section('additional_javascript')
  @vite(['resources/js/libs/jquery-ui-1.10.4.js'])
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'accounting'))
  
  <h1>Trésorie {{{ $user->currentSection->de_la_section }}}&nbsp;: année {{{ $year }}}</h1>
  
  <iframe src="{{ route('angular_accounting', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}"width="100%" height="600"></iframe>
  
@stop
