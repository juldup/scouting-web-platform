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
  Gestion des paiements
@stop

@section('back_links')
  @if ($year != Helper::thisYear())
    <a href="{{ URL::route('edit_payment', array('section_slug' => $user->currentSection->slug)) }}" class="btn btn-default">
      Retour à cette année
    </a>
  @endif
@stop

@section('additional_javascript')
  <script src="{{ asset('js/libs/angular-1.2.15.min.js') }}"></script>
  <script>
    var commitPaymentChangesURL = "{{ URL::route('upload_payment', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
    var postNewEventURL = "{{ URL::route('add_payment_event', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
    var deleteEventURL = "{{ URL::route('delete_payment_event', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
    var canEdit = {{ $canEdit ? "true" : "false" }};
    var members = {{ json_encode($members); }};
    var events = {{ json_encode($events); }};
  </script>
  <script src="{{ asset('js/payment-angular.js') }}"></script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'payment'))
  
  <h1>Paiements {{{ $user->currentSection->de_la_section }}}&nbsp;: année {{{ $year }}}</h1>
  
  @include('pages.payment.payment-angular')
  <div id="pending-commit" style="display: none;"><span class="glyphicon glyphicon-refresh"></span></div>
  
  <div class="vertical-divider"></div>
  <p>
    <a href="{{ URL::route('edit_payment', array('section_slug' => $user->currentSection->slug, 'year' => $previousYear)) }}" class="btn btn-default">Voir les paiements de l'année {{ $previousYear }}</a>
  </p>
  
@stop
