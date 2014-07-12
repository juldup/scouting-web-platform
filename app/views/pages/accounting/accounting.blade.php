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
  <script src="{{ asset('js/libs/jquery-ui-1.10.4.js') }}"></script>
  <script src="{{ asset('js/libs/angular-1.2.15.min.js') }}"></script>
  <script src="{{ asset('js/libs/angular-ui-0.4.0.js') }}"></script>
  <script>
    var commitAccountingChangesURL = "{{ URL::route('ajax-accounting-commit-changes', array('section_slug' => $user->currentSection->slug, 'lock_id' => $lock_id))}}";
    var inheritanceCash = {{ $inherit_cash }};
    var inheritanceBank = {{ $inherit_bank }};
    var previousYear = "{{{ $previous_year }}}";
    var canEdit = {{ $can_edit ? "true" : "false" }};
    var lockId = "{{ $lock_id }}";
    var extendLockURL = "{{ URL::route('ajax-accounting-extend-lock', array('lock_id' => $lock_id)) }}";
    var categories = [
    @foreach ($categories as $category_name => $category)
      {
        name: "{{ Helper::sanitizeForJavascript($category_name) }}",
        transactions: [
          @foreach ($category as $transaction)
            {
              date: "{{ Helper::dateToHuman($transaction->date) }}",
              object: "{{ Helper::sanitizeForJavascript($transaction->object) }}",
              cashin: "{{ $transaction->cashin_cents ? $transaction->cashinFormatted() : "" }}",
              cashout: "{{ $transaction->cashout_cents ? $transaction->cashoutFormatted() : "" }}",
              bankin: "{{ $transaction->bankin_cents ? $transaction->bankinFormatted() : "" }}",
              bankout: "{{ $transaction->bankout_cents ? $transaction->bankoutFormatted() : "" }}",
              comment: "{{ Helper::sanitizeForJavascript($transaction->comment) }}",
              receipt: "{{ Helper::sanitizeForJavascript($transaction->receipt) }}",
              id: {{ $transaction->id }}
            },
          @endforeach
        ]
      },
    @endforeach
    ];
  </script>
  <script src="{{ asset('js/accounting-angular.js') }}"></script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'accounting'))
  
  <h1>Trésorie {{{ $user->currentSection->de_la_section }}}&nbsp;: année {{{ $year }}}</h1>
  @if ($locked_by_user)
    <p class='alert alert-warning'>
      Ces comptes sont pour le moment modifiés par <strong>{{ $locked_by_user }}</strong>.
      Pour que tu puisses modifier ces comptes, cet utilisateur doit fermer cette page dans son navigateur.
    </p>
  @endif
  
  @include('pages.accounting.accounting-angular')
  <div id="pending-commit" style="display: none;"><span class="glyphicon glyphicon-refresh"></span></div>
@stop
