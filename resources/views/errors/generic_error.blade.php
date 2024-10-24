@extends('base')
{{--
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
--}}
<?php
use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;
use App\Models\MemberHistory;
?>

@section('title')
  Erreur
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Désolés !</h1>
      <p class='alert alert-danger'>
        Une erreur inconnue s'est produite. Veuillez réessayer plus tard ou <a href="{{ URL::route('contacts') }}#webmaster">contacter le webmaster</a>.
      </p>
      
      <p>
        <a class="btn btn-default" href="{{ URL::previous() }}">Revenir à la page précédente</a>
      </p>
    </div>
  </div>
@stop