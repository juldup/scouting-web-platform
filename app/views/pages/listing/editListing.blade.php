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
  Gestion du listing
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-members.js') }}"></script>
  <script>
    var members = new Array();
    @foreach ($members as $member)
      members[{{ $member->id }}] = @include ('subviews.memberToJavascript', array('member' => $member));
    @endforeach
    var ajaxChangeSubgroupOrRoleURL = "{{ URL::route('ajax_change_subgroup_or_role') }}";
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('listing', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour au listing
    </a>
  </p>
@stop

@section('forward_links')
  @if ($user->can(Privilege::$EDIT_LISTING_ALL, 1))
    <p>
      <a href='{{ URL::route('desk_listing') }}'>
        Listing Desk
      </a>
    </p>
  @endif
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-listing'))
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Gestion du listing {{{ $user->currentSection->de_la_section }}} ({{ $members->count() }} membres)</h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12 text-right">
      <p>
        <label>Télécharger le listing {{{ $user->currentSection->de_la_section }}} :</label>
        <a class="btn-sm btn-default" href="{{ URL::route('download_listing', array('section_slug' => $user->currentSection->slug)) }}">
          PDF
        </a>
        <a class="btn-sm btn-default" href="{{ URL::route('download_full_listing', array('section_slug' => $user->currentSection->slug, 'format' => 'excel')) }}">
          Excel
        </a>
        <a class="btn-sm btn-default" href="{{ URL::route('download_listing_options', array('section_slug' => $user->currentSection->slug)) }}">
          Plus d'options
        </a>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.editMemberForm', array('form_legend' => "Modifier un membre", 'submit_url' => URL::route('listing_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => false, 'edit_identity' => $can_edit_identity, 'edit_section' => $can_change_section, 'edit_totem' => true, 'edit_leader' => false, 'edit_photo' => true))
    </div>
  </div>
  
  @if ($members->count())
    <div class="row">
      <div class="col-lg-12">
        <table class="table table-striped table-hover sort-by-column">
          <thead>
            <th>N°</th>
            <th class="parser-false">Actions</th>
            @if ($user->currentSection->id == 1)
              <th>Section</th>
            @endif
            <th>Nom</th>
            <th>Prénom</th>
            @if ($show_totem)
              <th>Totem</th>
            @endif
            @if ($show_subgroup)
              <th>{{{ $user->currentSection->subgroup_name }}}</th>
            @endif
            @if ($show_role)
              <th>Rôle</th>
            @endif
            <th>Date de naissance</th>
            <th>Année</th>
          </thead>
          <tbody>
            <?php $counter = 1; ?>
            <?php $guestTitleShown = false; ?>
            @foreach ($members as $member)
              @if ($member->is_guest && !$guestTitleShown)
                <tr>
                  <td colspan="2"></td>
                  <th colspan="{{ 4 + ($user->currentSection->id == 1 ? 1 : 0)
                          + ($show_totem ? 1 : 0)
                          + ($show_subgroup ? 1 : 0)
                          + ($show_role ? 1 : 0) }}">
                    Invités
                  </th>
                </tr>
              @endif
              <tr>
                <td>
                  {{ $counter++ }}
                </td>
                <td>
                  <a class="btn-sm btn-primary" href="javascript:editMember({{ $member->id }})">
                    Modifier
                  </a>
                  <a class="btn-sm btn-danger warning-delete" href="{{ URL::route('manage_listing_delete', array('member_id' => $member->id)) }}">
                    Supprimer
                  </a>
                </td>
                @if ($user->currentSection->id == 1)
                  <td data-text="{{ $member->getSection()->position }}">
                    {{{ $member->getSection()->name }}}
                  </td>
                @endif
                <td>{{{ $member->last_name }}}</td>
                <td>{{{ $member->first_name }}}</td>
                @if ($show_totem)
                  <td>{{{ $member->totem }}}</td>
                @endif
                @if ($show_subgroup)
                  <td>
                    <span style="display: none;">{{{ $member->subgroup }}}</span>
                    {{ Form::select('select_subgroup', ['' => 'Aucun(e)'] + $subgroup_choices + ['select_other_subgroup' => "Créer un groupe"], $member->subgroup, ['class' => 'form-control medium', 'data-member_id' => $member->id]) }}
                  </td>
                @endif
                @if ($show_role)
                  <td>
                    <span style="display: none;">{{{ $member->role }}}</span>
                    {{ Form::select('select_role', ['' => 'Aucun'] + $role_choices + ['select_other_role' => "Créer un rôle"], $member->role, ['class' => 'form-control medium', 'data-member_id' => $member->id]) }}
                  </td>
                @endif
                <td data-text="{{ $member->birth_date }}">{{{ $member->getHumanBirthDate() }}}</td>
                <td>{{{ $member->year_in_section }}}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    
  @else
    
    <div class="row">
      <div class="col-lg-12">
        @if ($user->currentSection->id == 1)
          <p>Il n'y a auncun scout dans l'unité.</p>
        @else
          <p>Il n'y a aucun membre dans cette section.</p>
        @endif
      </div>
    </div>
    
  @endif
  
  <div id="pending-commit" style="display: none;"><span class="glyphicon glyphicon-refresh"></span></div>
@stop