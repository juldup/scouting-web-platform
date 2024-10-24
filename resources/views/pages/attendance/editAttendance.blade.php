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
  Gestion des présences
@stop

@section('back_links')
  @if ($year != Helper::thisYear())
    <a href="{{ URL::route('edit_attendance', array('section_slug' => $user->currentSection->slug)) }}" class="btn btn-default">
      Retour à cette année
    </a>
  @endif
@stop

@section('additional_javascript')
  @vite(['resources/js/libs/angular-1.2.15.min.js'])
  <script>
    window.commitAttendanceChangesURL = "{{ URL::route('upload_attendance', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
    window.canEdit = {{ $canEdit ? "true" : "false" }};
    window.members = {!! json_encode($members); !!};
    window.monitoredEvents = {!! json_encode($monitoredEvents); !!};
    window.unmonitoredEvents = {!! json_encode($unmonitoredEvents); !!};
  </script>
  @vite(['resources/js/attendance-angular.js'])
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'attendance'))
  
  <h1>Présences {{{ $user->currentSection->de_la_section }}}&nbsp;: année {{{ $year }}}</h1>
  
  @include('pages.attendance.attendance-angular')
  <div id="pending-commit" style="display: none;"><span class="glyphicon glyphicon-refresh"></span></div>
  
  <div class="vertical-divider"></div>
  <p>
    <a href="{{ URL::route('edit_attendance', array('section_slug' => $user->currentSection->slug, 'year' => $previousYear)) }}" class="btn btn-default">Voir les présences de l'année {{ $previousYear }}</a>
  </p>
  
@stop
