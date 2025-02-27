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

?>


<!doctype html>
<html lang="fr" @yield('html_parameters')>
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @vite(['resources/css/styles.css'])
  @vite(['resources/angular/payment/styles.css'])
</head>
<body>
  



 <!-- Include styles -->
</head>
<body>
    <app-root></app-root> <!-- Your Angular root component -->
        
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      window.jQuery = window.$ = jQuery; // Fallback to the CDN version
    </script>
    @vite(['resources/angular/payment/polyfills.js'])
    @vite(['resources/angular/payment/scripts.js'])
    @vite(['resources/angular/payment/main.js'])
    
    <script>
      window.commitPaymentChangesURL = "{{ URL::route('upload_payment', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
      window.postNewEventURL = "{{ URL::route('add_payment_event', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
      window.deleteEventURL = "{{ URL::route('delete_payment_event', array('section_slug' => $user->currentSection->slug, 'year' => $year)) }}";
      window.canEdit = {{ $canEdit ? "true" : "false" }};
      window.members = {!! json_encode($members); !!};
      window.events = {!! json_encode($events); !!};
  </script>
</body>
</html>