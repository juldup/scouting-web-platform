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

?>

@section('title')
  Recherche : {{ $search_string }}
@stop

@section('content')
  
  @if ($search_string)
    <div class="row">
      <div class="col-lg-12">
        <h1>Recherche</h1>
        Nouvelle recherche : @include('pages.search.search_box')
        <br /><br />
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-12">
        @if ($results['total'] == 0)
          <p>Aucun résultat pour « {{ $search_string }} ».</p>
        @elseif ($results['total'] == 1)
        <h2>1 résultat pour « {{ $search_string }} »</h2>
        @else
        <h2>{{ count($results['hits']) }} résultats pour « {{ $search_string }} »</h2>
        @endif
        @if (!$userIsMember)
        <p><strong>Note : en vous <a href="{{ URL::route('login') }}">connectant</a> en tant que membre de l'unité, vous pourrez voir également les résultats privés.</strong> </p>
        @endif
      </div>
    </div>
    @foreach ($results['hits'] as $hit)
      <div class="row">
        <div class="col-md-12">
          <div class="well clickable clickable-no-default">
            <a href="{{ $hit['_source']['url'] }}" target="_blank"></a>
            <legend>
              <span class="glyphicon glyphicon-certificate" style="color: {{ Section::find($hit['_source']['section_id'])->color }}"></span>
              {{ $hit['_source']['text_type_name'] }}
              {{ Section::find($hit['_source']['section_id'])->de_la_section }} :
              {{ $hit['_source']['title'] }}
            </legend>
            <div class="search-result-body">
              {{ strip_tags($hit['_source']['content']) }}
              <br /><br />
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @else
    <div class="row">
      <div class="col-lg-12">
        <h1>Recherche</h1>
        Rechercher sur le site : @include('pages.search.search_box')
      </div>
    </div>
  @endif
  
@stop
