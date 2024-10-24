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
use App\Http\Controllers\PersonalEmailController;
use App\Models\MemberHistory;

?>

@section('title')
  Listing {{{ $user->currentSection->de_la_section }}}
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  @vite(['resources/js/edit-members.js'])
  <script>
    var members = new Array();
    @foreach ($editable_members as $member)
      members[{{ $member->id }}] = @include ('subviews.memberToJavascript', array('member' => $member));
    @endforeach
  </script>
@stop

@section('forward_links')
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_listing', array('section_slug' => $user->currentSection->slug)) }}'>
        Gérer le listing
      </a>
    </p>
  @endif
  @if (count($sections) == 1)
    @if ($sections[0]['show_subgroup'])
      <p>
        <a href='{{ URL::route('listing_view_subgroups', array('section_slug' => $user->currentSection->slug)) }}'>
          Listing par {{{ strtolower($sections[0]['section_data']->subgroup_name) }}}
        </a>
      </p>
    @endif
  @endif
  <p>
    <a href="{{ URL::route('listing_view_pictures', array('section_slug' => $user->currentSection->slug)) }}">
      Photos des membres
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @include('subviews.editMemberForm', array('form_legend' => "Modifier un membre", 'submit_url' => URL::route('listing_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => false, 'edit_identity' => true, 'edit_section' => $can_change_section, 'edit_totem' => $can_manage,'edit_leader' => false, 'edit_photo' => $can_manage))
    </div>
  </div>

  @if ($user->currentSection->id == 1)
    <div class="row">
      <div class="col-md-12">
        <h1>
          Listing {{{ $user->currentSection->de_la_section }}} ({{ $total_member_count }} membres)
        </h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-right">
        <p>
          <a class="btn-sm btn-default" href="{{ URL::route('download_listing', array('section_slug' => $user->currentSection->slug)) }}">
            Télécharger le listing de toute l'unité
          </a>
        </p>
      </div>
    </div>
  @endif
  
  @foreach ($sections as $sct)
  
    <div class="row">
      <div class="col-md-12">
        <h2>
          @if (count($sections) > 1)
            <span class="glyphicon glyphicon-certificate" style="color: {{ $sct['section_data']->color }}"></span>
          @endif
          Listing {{{ $sct['section_data']->de_la_section }}}
          @if ($sct['members']->count() > 1) ({{ $sct['members']->count() }} membres) @endif
        </h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-right">
        <p>
          <a class="btn-sm btn-default" href="{{ URL::route('download_listing', array('section_slug' => $sct['section_data']->slug)) }}">
            Télécharger le listing {{{ $sct['section_data']->de_la_section }}}
          </a>
        </p>
      </div>
    </div>
  
    @if ($sct['members']->count())
    
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped table-hover sort-by-column">
            <thead>
              <th>N°</th>
              <th class="parser-false"></th>
              <th>Nom</th>
              <th>Prénom</th>
              @if ($sct['show_totem'])
                <th>Totem</th>
              @endif
              @if ($sct['show_subgroup'])
                <th>{{{ $sct['section_data']->subgroup_name }}}</th>
              @endif
              @if ($sct['show_role'])
                <th>Rôle</th>
              @endif
              <th>Téléphone</th>
              <th class="parser-false">E-mail</th>
            </thead>
            <tbody>
              <?php $counter = 1; ?>
              @foreach ($sct['members'] as $member)
                <tr>
                  <td>
                    {{ $counter++ }}
                  </td>
                  <td>
                    <a class="btn-sm btn-default" href="javascript:showMemberDetails({{ $member->id }})">Détails</a>
                    @if (Parameter::get(Parameter::$SHOW_MEMBER_HISTORY))
                      <a class="btn-sm btn-default" href="javascript:showMemberHistory({{ $member->id }})">Historique</a>
                    @endif
                    @if ($user->isOwnerOfMember($member))
                      <a class="btn-sm btn-primary" href="javascript:editMember({{ $member->id }})">Modifier</a>
                    @endif
                  </td>
                  <td>{{{ $member->last_name }}}</td>
                  <td>{{{ $member->first_name }}}</td>
                    @if ($sct['show_totem'])
                      <td>{{{ $member->totem }}}</td>
                    @endif
                    @if ($sct['show_subgroup'])
                      <td>{{{ $member->subgroup }}}</td>
                    @endif
                    @if ($sct['show_role'])
                      <td>{{{ $member->role }}}</td>
                    @endif
                  <td>{{{ $member->getPublicPhone() }}}</td>
                  <td>
                    <a class="btn-sm btn-default" href="{{ URL::route('personal_email', array("contact_type" => PersonalEmailController::$CONTACT_TYPE_PARENTS, "member_id" => $member->id)) }}">
                      Envoyer&nbsp;un&nbsp;e&#8209;mail
                    </a>
                  </td>
                </tr>
                <tr id="details_{{ $member->id }}" class="details_member tablesorter-childRow" style="display: none;">
                  <td colspan="2" class="listing-details-picture">
                    {{ $member->has_picture ? "<img src='" . $member->getPictureURL() . "' alt='not found'>" : "" }}
                  </td>
                  <td colspan="{{ 3 + ($sct['show_totem'] ? 1 : 0) + ($sct['show_subgroup'] ? 1 : 0) + ($sct['show_role'] ? 1 : 0) }}">
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Adresse :
                      </div>
                      <div class="col-md-9">
                        {{{ $member->address}}} <br /> {{{ $member->postcode }}} {{{ $member->city }}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Téléphone :
                      </div>
                      <div class="col-md-9">
                        {{ $member->getAllPublicPhones("<span class='horiz-divider'></span>", $user->isLeader()) }}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Sexe :
                      </div>
                      <div class="col-md-9">
                        {{{ $member->gender == 'M' ? "Garçon" : "Fille" }}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Date de naissance :
                      </div>
                      <div class="col-md-9">
                        {{{ $member->getHumanBirthDate() }}}
                      </div>
                    </div>
                    @if ($member->quali)
                      <div class="row">
                        <div class="col-md-3 member-detail-label">
                          Totem et quali :
                        </div>
                        <div class="col-md-9">
                          {{{ $member->totem }}} {{{ $member->quali }}}
                        </div>
                      </div>
                    @endif
                    @if ($user->isLeader())
                      <div class="row">
                        <div class="col-md-3 member-detail-label">
                          Adresse e-mail :
                        </div>
                        <div class="col-md-9">
                          {{ $member->getAllEmailAddresses("<span class='horiz-divider'></span>") }}
                        </div>
                      </div>
                    @endif
                  </td>
                </tr>
                @if (Parameter::get(Parameter::$SHOW_MEMBER_HISTORY))
                  <tr id="history_{{ $member->id }}" class="history_member tablesorter-childRow" style="display: none;">
                    <td colspan="1"></td>
                    <td colspan="{{ 4 + ($sct['show_totem'] ? 1 : 0) + ($sct['show_subgroup'] ? 1 : 0) + ($sct['show_role'] ? 1 : 0) }}">
                      <div class="row">
                        @if (count(MemberHistory::getForMember($member->id)))
                          @foreach (MemberHistory::getForMember($member->id) as $history_entry)
                            <div class="col-md-12">
                              @if ($history_entry->section_id)
                                <span class="glyphicon glyphicon-certificate" style="color: {{ $history_entry->getSection()->color }}"></span>
                                En {{{ $history_entry->year }}} :
                                {{{ $history_entry->getSection()->name }}}
                              @else
                                <span class="glyphicon glyphicon-certificate"></span>
                                En {{{ $history_entry->year }}} :
                                {{{ $history_entry->section_name_backup }}}
                              @endif
                              @if ($history_entry->subgroup && $history_entry->role)
                                ({{{ $history_entry->getSubgroupForDisplay() }}} ; role :
                                {{{ $history_entry->role }}})
                              @elseif ($history_entry->subgroup)
                                ({{{ $history_entry->getSubgroupForDisplay() }}})
                              @elseif ($history_entry->role)
                                (Role : {{{ $history_entry->role }}})
                              @endif
                            </div>
                          @endforeach
                        @else
                          Ce membre n'a pas d'historique dans l'unité.
                        @endif
                      </div>
                    </td>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    
    @else
      
      <div class="row">
        <div class="col-md-12">
          <p>Il n'y a aucun membre dans cette section.</p>
        </div>
      </div>
      
    @endif
  @endforeach
  
@stop