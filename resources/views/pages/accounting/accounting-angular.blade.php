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


<!doctype html>
<html lang="fr" @yield('html_parameters')>
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @vite(['resources/css/styles.css'])
  @vite(['resources/angular/accounting/styles.css'])
</head>
<body>
  



 <!-- Include styles -->
</head>
<body>
  @if ($locked_by_user)
    <p class='alert alert-warning'>
      Ces comptes sont pour le moment modifiés par <strong>{{ $locked_by_user }}</strong>.
      Pour que tu puisses modifier ces comptes, cet utilisateur doit fermer cette page dans son navigateur.
    </p>
  @else
    <app-root></app-root> <!-- Your Angular root component -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      window.jQuery = window.$ = jQuery; // Fallback to the CDN version
    </script>
    @vite(['resources/angular/accounting/polyfills.js'])
    @vite(['resources/angular/accounting/scripts.js'])
    @vite(['resources/angular/accounting/main.js'])
    
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
          name: "{!! Helper::sanitizeForJavascript($category_name) !!}",
          transactions: [
            @foreach ($category as $transaction)
              {
                date: "{!! Helper::dateToHuman($transaction->date) !!}",
                object: "{!! Helper::sanitizeForJavascript($transaction->object) !!}",
                cashin: "{{ $transaction->cashin_cents ? $transaction->cashinFormatted() : "" }}",
                cashout: "{{ $transaction->cashout_cents ? $transaction->cashoutFormatted() : "" }}",
                bankin: "{{ $transaction->bankin_cents ? $transaction->bankinFormatted() : "" }}",
                bankout: "{{ $transaction->bankout_cents ? $transaction->bankoutFormatted() : "" }}",
                comment: "{!! Helper::sanitizeForJavascript($transaction->comment) !!}",
                receipt: "{!! Helper::sanitizeForJavascript($transaction->receipt) !!}",
                id: {{ $transaction->id }}
              },
            @endforeach
          ]
        },
      @endforeach
      ];
    </script>
  @endif
</body>
</html>