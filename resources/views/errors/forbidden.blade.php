@extends("base")
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
  Accès privé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section("content")
  <h1>Accès privé</h1>
  @if ($user->isConnected)
    <p>Vous n'avez pas accès à cette page !</p>
  @else
    <div class="col-lg-12 alert alert-warning">
      <p><strong>Vous n'êtes pas connecté sur le site</strong></p>
      <p>Pour pouvoir accéder à cette page, vous devez <a href="{{ URL::route('login') }}">vous connecter</a>.</p>
    </div>
  @endif
@stop
