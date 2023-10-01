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
  Listing {{{ $user->currentSection->de_la_section }}}
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ asset('js/listing-subgroups.js') }}"></script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('listing', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour au listing
    </a>
  </p>
@stop

@section('forward_links')
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_listing', array('section_slug' => $user->currentSection->slug)) }}'>
        Gérer le listing
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      @include('subviews.flashMessages')
    </div>
  </div>
    
  <div class="row">
    <div class="col-md-12">
      <h1>
        Listing par {{{ strtolower($section->subgroup_name) }}} {{{ $section->de_la_section }}}
        ({{ count($members) }} membres)
      </h1>
    </div>
  </div>
  
  @foreach ($subgroups as $subgroup=>$subgroupMembers)
    <div class='row'>
      <div class="col-md-12">
        <h2>{{{ $subgroup ? $subgroup : "Aucun groupe" }}} ({{{ count($subgroupMembers) }}} membres)</h2>
      </div>
    </div>
    <div class="row">
      @foreach ($subgroupMembers as $member)
        <div class="col-sm-6 col-md-4 leader-card">
          <div class="well">
            <div class="row">
              <div class="col-xs-6 col-sm-4 col-md-6">
                @if ($member->has_picture)
                  <img class="member-picture" src="{{ $member->getPictureURL() }}" />
                @else
                  <img class="member-picture" src="" alt=" Pas de photo " />
                @endif
              </div>
              <div class="col-xs-6 col-sm-8 col-md-6">
                <p class="leader-name">{{{ $member->getFullName() }}}</p>
                @if ($member->role)
                  <p>{{{ $member->role }}}</p>
                @endif
                @if ($show_totem)
                  <p><strong>Totem :</strong> {{ $member->totem ? $member->totem : "<em>aucun</em>" }}</p>
                @endif
                <p><a class="btn-sm btn-default" href="javascript:showMemberDetails({{ $member->id }})">Détails</a></p>
              </div>
              <div class="subgroup-listing-details" id="details_{{ $member->id }}" class="details_member" style="display: none;">
                <div class="subgroup-listing-details-panel">
                  <div class="close-button">
                    <span class="glyphicon glyphicon-remove"></span>
                  </div>
                  <table>
                    <tr>
                      <th>Photo :</th>
                      <td>
                        <img class="member-picture" src="{{ $member->getPictureURL() }}" alt=" Pas de photo " />
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <strong>Nom :</strong>
                      </th>
                      <td>
                        {{{ $member->getFullName() }}}
                      </td>
                    </tr>
                    @if ($member->role)
                      <tr>
                        <th>
                          <strong>Rôle :</strong> 
                        </th>
                        <td>
                          {{{ $member->role }}}
                        </td>
                      </tr>
                    @endif
                    @if ($member->totem)
                      <tr>
                        <th>
                          <strong>Totem @if ($member->quali) et quali @endif :</strong> 
                        </th>
                        <td>
                          {{{ $member->totem }}} {{{ $member->quali ? $member->quali : "" }}}
                        </td>
                      </tr>
                    @endif
                    <tr>
                      <th>
                        <strong>Adresse :</strong>
                      </th>
                      <td>
                        {{{ $member->address}}} <br> {{{ $member->postcode }}} {{{ $member->city }}}
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <strong>Téléphone :</strong>
                      </th>
                      <td>
                        {{ $member->getAllPublicPhones("<span class='horiz-divider'></span>", $user->isLeader()) }}
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <strong>Date de naissance :</strong> 
                      </th>
                      <td>
                        {{{ $member->getHumanBirthDate() }}}
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endforeach
@stop